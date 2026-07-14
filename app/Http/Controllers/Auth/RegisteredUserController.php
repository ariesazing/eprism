<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailBlacklist;
use App\Models\OrganizationalUnit;
use App\Models\Role;
use App\Models\UserStatus;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($reRegistrationCandidate?->id)],
            'position_title' => ['required', Rule::in(User::positionTitles())],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'organizational_unit_id' => ['required', 'integer', 'exists:organizational_units,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $defaultRole = Role::query()->firstOrCreate(
            ['role_name' => 'Proponent'],
            ['description' => 'Research proponent']
        );

        $defaultStatus = UserStatus::query()->firstOrCreate(
            ['status_name' => 'Pending Approval'],
            ['description' => 'Awaiting administrator approval']
        );

        $payload = [
            'role_id' => $defaultRole->id,
            'organizational_unit_id' => (int) $request->organizational_unit_id,
            'deped_id' => $request->deped_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'suffix' => $request->suffix,
            'email' => $submittedEmail,
            'password' => Hash::make($request->password),
            'position_title' => $request->position_title,
            'contact_number' => $request->contact_number,
            'status_id' => $defaultStatus->id,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'email_verified_at' => null,
        ];

        if ($reRegistrationCandidate) {
            if ($reRegistrationCandidate->trashed()) {
                $reRegistrationCandidate->restore();
            }

            $reRegistrationCandidate->fill($payload);
            $reRegistrationCandidate->email_verified_at = null;
            $reRegistrationCandidate->save();

            $user = $reRegistrationCandidate;
        } else {
            $user = User::create($payload);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('account.pending', absolute: false));
    }
}
