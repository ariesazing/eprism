<?php

use App\Models\OrganizationalUnit;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
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

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
