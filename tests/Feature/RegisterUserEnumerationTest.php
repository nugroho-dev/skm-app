<?php

use App\Models\Institution;
use App\Models\InstitutionGroup;
use App\Models\Mpp;
use App\Models\User;
use App\Http\Middleware\VerifyRecaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

const UNIFORM_REGISTER_MESSAGE = 'Jika data pendaftaran valid, akun akan diproses. Silakan cek email Anda untuk langkah berikutnya.';

function buildInstitutionFixture(): Institution
{
    $mpp = Mpp::create(['name' => 'MPP Test']);
    $group = InstitutionGroup::create(['name' => 'Group Test']);

    return Institution::create([
        'name' => 'Institution Test',
        'mpp_id' => $mpp->id,
        'institution_group_id' => $group->id,
    ]);
}

it('returns the same registration response for existing email', function () {
    $this->withoutMiddleware(VerifyRecaptcha::class);

    Role::create(['name' => 'admin_instansi', 'guard_name' => 'web']);

    $institution = buildInstitutionFixture();

    User::factory()->create([
        'email' => 'registered@example.com',
        'institution_id' => $institution->id,
    ]);

    $response = $this->post('/register', [
        'name' => 'Example User',
        'email' => 'registered@example.com',
        'institution_slug' => $institution->slug,
        'password' => 'Str0ng!Pass123',
        'password_confirmation' => 'Str0ng!Pass123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status', UNIFORM_REGISTER_MESSAGE);
    $this->assertGuest();

    expect(User::query()->where('email', 'registered@example.com')->count())->toBe(1);
});

it('returns the same registration response for new email and does not auto-login', function () {
    $this->withoutMiddleware(VerifyRecaptcha::class);

    Role::create(['name' => 'admin_instansi', 'guard_name' => 'web']);

    $institution = buildInstitutionFixture();

    $response = $this->post('/register', [
        'name' => 'New User',
        'email' => 'new-user@example.com',
        'institution_slug' => $institution->slug,
        'password' => 'Str0ng!Pass123',
        'password_confirmation' => 'Str0ng!Pass123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status', UNIFORM_REGISTER_MESSAGE);
    $this->assertGuest();

    expect(User::query()->where('email', 'new-user@example.com')->exists())->toBeTrue();
});
