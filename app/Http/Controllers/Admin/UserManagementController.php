<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\OrganizationalUnit;
use App\Models\Role;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function pending(): View
    {
        $pendingUsers = User::query()
            ->with(['role:id,role_name', 'organizationalUnit:id,unit_name,unit_code', 'status:id,status_name'])
            ->whereHas('status', fn ($query) => $query->where('status_name', 'Pending Approval'))
            ->whereNull('approved_by')
            ->latest()
            ->paginate(15);

        return view('admin.users.pending', [
            'pendingUsers' => $pendingUsers,
        ]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $activeStatusId = UserStatus::query()->where('status_name', 'Active')->value('id');

        if (! $activeStatusId) {
            return back()->withErrors(['status' => 'Active status is not configured.']);
        }

        $user->update([
            'status_id' => $activeStatusId,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
            'rejected_by' => null,
            'rejected_at' => null,
        ]);

        return back()->with('status', 'User approved successfully.');
    }

    public function reject(RejectUserRequest $request, User $user): RedirectResponse
    {
        $inactiveStatusId = UserStatus::query()->where('status_name', 'Inactive')->value('id');

        if (! $inactiveStatusId) {
            return back()->withErrors(['status' => 'Inactive status is not configured.']);
        }

        $user->update([
            'status_id' => $inactiveStatusId,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => $request->string('rejection_reason')->toString(),
            'rejected_by' => $request->user()->id,
            'rejected_at' => now(),
        ]);

        return back()->with('status', 'User request rejected.');
    }

    public function index(): View
    {
        $users = User::query()
            ->with(['role:id,role_name', 'organizationalUnit:id,unit_name,unit_code', 'status:id,status_name'])
            ->whereHas('status', fn ($query) => $query->where('status_name', 'Active'))
            ->latest()
            ->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::query()->whereIn('role_name', ['Administrator', 'Reviewer'])->orderBy('role_name')->get(['id', 'role_name']),
            'organizationalUnits' => OrganizationalUnit::query()->orderBy('unit_name')->get(['id', 'unit_name', 'unit_code']),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $activeStatusId = UserStatus::query()->where('status_name', 'Active')->value('id');

        if (! $activeStatusId) {
            return back()->withErrors(['status' => 'Active status is not configured.'])->withInput();
        }

        User::query()->create([
            'role_id' => (int) $request->integer('role_id'),
            'organizational_unit_id' => (int) $request->integer('organizational_unit_id'),
            'deped_id' => $request->input('deped_id'),
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'suffix' => $request->input('suffix'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'position_title' => $request->input('position_title'),
            'contact_number' => $request->input('contact_number'),
            'status_id' => $activeStatusId,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function edit(Request $request, User $user): View
    {
        return view('admin.users.edit', [
            'userRecord' => $user,
            'roles' => Role::query()->orderBy('role_name')->get(['id', 'role_name']),
            'statuses' => UserStatus::query()->orderBy('status_name')->get(['id', 'status_name']),
            'organizationalUnits' => OrganizationalUnit::query()->orderBy('unit_name')->get(['id', 'unit_name', 'unit_code']),
            'returnTo' => $request->query('return_to', route('admin.users.index')),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $activeStatusId = UserStatus::query()->where('status_name', 'Active')->value('id');

        $user->fill([
            'role_id' => (int) $validated['role_id'],
            'organizational_unit_id' => (int) $validated['organizational_unit_id'],
            'status_id' => (int) $validated['status_id'],
            'deped_id' => $validated['deped_id'] ?? null,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'suffix' => $validated['suffix'] ?? null,
            'email' => $validated['email'],
            'position_title' => $validated['position_title'],
            'contact_number' => $validated['contact_number'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($activeStatusId && (int) $validated['status_id'] === (int) $activeStatusId) {
            $user->approved_by = $request->user()->id;
            $user->approved_at = now();
            $user->rejection_reason = null;
            $user->rejected_by = null;
            $user->rejected_at = null;
        }

        $user->save();

        $redirectTarget = $this->safeReturnTarget($request->input('return_to'));

        return redirect($redirectTarget)->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.']);
        }

        $inactiveStatusId = UserStatus::query()->where('status_name', 'Inactive')->value('id');

        if ($inactiveStatusId) {
            $user->update([
                'status_id' => $inactiveStatusId,
            ]);
        }

        $user->delete();

        return back()->with('status', 'User deactivated successfully.');
    }

    private function safeReturnTarget(?string $returnTo): string
    {
        if (! $returnTo) {
            return route('admin.users.index');
        }

        if (Str::startsWith($returnTo, '/admin/users')) {
            return $returnTo;
        }

        return route('admin.users.index');
    }
}
