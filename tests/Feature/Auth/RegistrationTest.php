<?php

use App\Models\PreRegistrationVerification;
use App\Models\OrganizationalUnit;
use Illuminate\Support\Facades\Notification;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    Notification::fake();

    $organizationalUnit = OrganizationalUnit::query()->create([
        'unit_name' => 'Test Unit',
        'unit_code' => 'TEST-UNIT',
        'unit_type' => 'School',
        'district' => 'District Test',
        'address' => 'Test Address',
    ]);

    $response = $this->post('/register', [
        'deped_id' => 'DEPED-12345678',
        'first_name' => 'Test',
        'middle_name' => 'S',
        'last_name' => 'User',
        'suffix' => null,
        'email' => 'test@example.com',
        'position_title' => 'Teacher I',
        'contact_number' => '09123456789',
        'organizational_unit_id' => $organizationalUnit->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('register', absolute: false));
    $response->assertSessionHas('registration_verification_sent', true);

    expect(PreRegistrationVerification::query()->where('email', 'test@example.com')->exists())->toBeTrue();
});

test('registration requires a one-letter middle initial', function () {
    $organizationalUnit = OrganizationalUnit::query()->create([
        'unit_name' => 'Test Unit',
        'unit_code' => 'TEST-UNIT',
        'unit_type' => 'School',
        'district' => 'District Test',
        'address' => 'Test Address',
    ]);

    $response = $this->from('/register')->post('/register', [
        'deped_id' => 'DEPED-12345678',
        'first_name' => 'Test',
        'middle_name' => 'Sample',
        'last_name' => 'User',
        'suffix' => null,
        'email' => 'test@example.com',
        'position_title' => 'Teacher I',
        'contact_number' => '09123456789',
        'organizational_unit_id' => $organizationalUnit->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors('middle_name');
});
