<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OrganizationalUnit;
use App\Models\Role;
use App\Models\UserStatus;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $request->validate([
            'deped_id' => ['nullable', 'string', 'max:30', 'unique:users,deped_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'position_title' => ['required', 'string', 'max:150'],
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

        $user = User::create([
            'role_id' => $defaultRole->id,
            'organizational_unit_id' => (int) $request->organizational_unit_id,
            'deped_id' => $request->deped_id,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'suffix' => $request->suffix,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'position_title' => $request->position_title,
            'contact_number' => $request->contact_number,
            'status_id' => $defaultStatus->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
