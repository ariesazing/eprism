<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailBlacklist;
use App\Models\OrganizationalUnit;
use App\Models\PreRegistrationVerification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use App\Notifications\PreRegistrationVerificationNotification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'positionTitles' => User::positionTitles(),
            'organizationalUnits' => OrganizationalUnit::query()
                ->orderBy('unit_name')
                ->get(['id', 'unit_name', 'unit_code']),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'middle_name' => Str::upper(trim((string) $request->input('middle_name', ''))),
        ]);

        $submittedEmail = strtolower((string) $request->input('email'));

        if ($submittedEmail !== '' && EmailBlacklist::query()->where('email', $submittedEmail)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'This email is permanently blocked due to policy violation or fraud concerns.',
            ]);
        }

        $reRegistrationCandidate = $submittedEmail === ''
            ? null
            : User::query()
                ->withTrashed()
                ->where('email', $submittedEmail)
                ->whereNotNull('rejected_at')
                ->whereHas('status', fn ($query) => $query->where('status_name', 'Inactive'))
                ->first();

        $request->validate([
            'deped_id' => ['nullable', 'string', 'max:30', Rule::unique('users', 'deped_id')->ignore($reRegistrationCandidate?->id)],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['required', 'string', 'size:1', 'regex:/^[A-Z]$/'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($reRegistrationCandidate?->id)],
            'position_title' => ['required', Rule::in(User::positionTitles())],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);

        PreRegistrationVerification::query()->updateOrCreate(
            ['email' => $submittedEmail],
            [
                'token_hash' => $tokenHash,
                'registration_payload' => [
                    'deped_id' => $request->deped_id,
                    'first_name' => $request->first_name,
                    'middle_name' => Str::upper($request->string('middle_name')->toString()),
                    'last_name' => $request->last_name,
                    'suffix' => $request->suffix,
                    'email' => $submittedEmail,
                    'position_title' => $request->position_title,
                    'contact_number' => $request->contact_number,
                    'organizational_unit_id' => (int) $request->organizational_unit_id,
                    'password' => Hash::make($request->password),
                ],
                'expires_at' => now()->addMinutes(60),
            ]
        );

        $verificationUrl = route('register.pre-verify', [
            'token' => $token,
            'email' => $submittedEmail,
        ]);

        Notification::route('mail', $submittedEmail)
            ->notify(new PreRegistrationVerificationNotification($verificationUrl));

        return redirect()->route('register')->with([
            'registration_verification_sent' => true,
            'registration_verification_message' => 'We sent a verification link to your email. Confirm it to continue your registration request.',
        ]);
    }

    public function verifyPreRegistration(Request $request, string $token): RedirectResponse
    {
        $submittedEmail = strtolower((string) $request->query('email', ''));

        if ($submittedEmail === '') {
            return redirect()->route('register')->withErrors([
                'email' => 'Invalid verification link. Please register again.',
            ]);
        }

        $verification = PreRegistrationVerification::query()
            ->where('email', $submittedEmail)
            ->first();

        if (! $verification || ! hash_equals($verification->token_hash, hash('sha256', $token))) {
            return redirect()->route('register')->withErrors([
                'email' => 'Invalid verification link. Please register again.',
            ]);
        }

        if ($verification->expires_at->isPast()) {
            $verification->delete();

            return redirect()->route('register')->withErrors([
                'email' => 'Verification link expired. Please register again.',
            ]);
        }

        if (EmailBlacklist::query()->where('email', $submittedEmail)->exists()) {
            $verification->delete();

            return redirect()->route('register')->withErrors([
                'email' => 'This email is permanently blocked due to policy violation or fraud concerns.',
            ]);
        }

        $payload = $verification->registration_payload;

        if (! is_array($payload)) {
            $verification->delete();

            return redirect()->route('register')->withErrors([
                'email' => 'Invalid registration payload. Please register again.',
            ]);
        }

        DB::transaction(function () use ($verification, $submittedEmail, $payload): void {
            $defaultRole = Role::query()->firstOrCreate(
                ['role_name' => 'Proponent'],
                ['description' => 'Research proponent']
            );

            $defaultStatusId = (int) DB::table('user_statuses')->where('status_name', 'Pending Approval')->value('id');

            if ($defaultStatusId === 0) {
                $defaultStatusId = (int) DB::table('user_statuses')->insertGetId([
                    'status_name' => 'Pending Approval',
                    'description' => 'Awaiting administrator approval',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $reRegistrationCandidate = User::query()
                ->withTrashed()
                ->where('email', $submittedEmail)
                ->whereNotNull('rejected_at')
                ->whereHas('status', fn ($query) => $query->where('status_name', 'Inactive'))
                ->first();

            $userPayload = [
                'role_id' => $defaultRole->id,
                'organizational_unit_id' => (int) ($payload['organizational_unit_id'] ?? 0),
                'deped_id' => $payload['deped_id'] ?? null,
                'first_name' => (string) ($payload['first_name'] ?? ''),
                'middle_name' => $payload['middle_name'] ?? null,
                'last_name' => (string) ($payload['last_name'] ?? ''),
                'suffix' => $payload['suffix'] ?? null,
                'email' => $submittedEmail,
                'password' => (string) ($payload['password'] ?? ''),
                'position_title' => (string) ($payload['position_title'] ?? ''),
                'contact_number' => $payload['contact_number'] ?? null,
                'status_id' => $defaultStatusId,
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
                'rejected_by' => null,
                'rejected_at' => null,
                'email_verified_at' => now(),
            ];

            if ($reRegistrationCandidate) {
                if ($reRegistrationCandidate->trashed()) {
                    $reRegistrationCandidate->restore();
                }

                $reRegistrationCandidate->fill($userPayload);
                $reRegistrationCandidate->save();

                $user = $reRegistrationCandidate;
            } else {
                $existingUser = User::query()->withTrashed()->where('email', $submittedEmail)->first();

                if ($existingUser) {
                    $verification->delete();

                    throw ValidationException::withMessages([
                        'email' => 'An account with this email already exists.',
                    ]);
                }

                $user = User::query()->create($userPayload);
            }

            $verification->delete();

            Auth::login($user);
        });

        return redirect()->route('account.pending')->with('status', 'Email verified. Your account is now waiting for administrator approval.');
    }
}
