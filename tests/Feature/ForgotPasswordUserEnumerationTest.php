<?php

use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\Mpp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

const UNIFORM_RESET_MESSAGE = 'Jika email terdaftar, tautan reset kata sandi akan dikirim.';

it('returns the same forgot-password response for unknown email', function () {
    Notification::fake();

    $response = $this->post('/forgot-password', ['email' => 'unknown@example.com'], ['HTTP_REFERER' => '/forgot-password']);

    $response->assertRedirect('/forgot-password');
    $response->assertSessionHasNoErrors(['email']);
    $response->assertSessionHas('status', UNIFORM_RESET_MESSAGE);
});

it('returns the same forgot-password response for existing email', function () {
    Notification::fake();

    $mpp = Mpp::create(['name' => 'MPP Test']);
    $group = InstitutionGroup::create(['name' => 'Group Test']);
    $institution = Institution::create([
        'name' => 'Institution Test',
        'mpp_id' => $mpp->id,
        'institution_group_id' => $group->id,
    ]);

    $user = User::factory()->create([
        'email' => 'known@example.com',
        'institution_id' => $institution->id,
    ]);

    $response = $this->post('/forgot-password', ['email' => $user->email], ['HTTP_REFERER' => '/forgot-password']);

    $response->assertRedirect('/forgot-password');
    $response->assertSessionHasNoErrors(['email']);
    $response->assertSessionHas('status', UNIFORM_RESET_MESSAGE);
});
