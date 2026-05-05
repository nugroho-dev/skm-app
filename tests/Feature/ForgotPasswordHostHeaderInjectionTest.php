<?php

use App\Models\User;
use App\Models\Mpp;
use App\Models\Institution;
use App\Models\InstitutionGroup;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('uses APP_URL for reset link even when host header is spoofed', function () {
    config(['app.url' => 'https://app.sisukma.go.id']);

    Notification::fake();

    $mpp = Mpp::create(['name' => 'MPP Test']);
    $group = InstitutionGroup::create(['name' => 'Group Test']);
    $institution = Institution::create([
        'name' => 'Institution Test',
        'mpp_id' => $mpp->id,
        'institution_group_id' => $group->id,
    ]);

    $user = User::factory()->create([
        'institution_id' => $institution->id,
    ]);

    $response = $this
        ->withHeader('Host', 'app.evil.go.id')
        ->post('/forgot-password', ['email' => $user->email]);

    $response->assertStatus(302);

    Notification::assertSentTo(
        $user,
        ResetPasswordNotification::class,
        function (ResetPasswordNotification $notification, array $channels) use ($user) {
            $mailMessage = $notification->toMail($user);
            $expectedHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $actualHost = parse_url($mailMessage->actionUrl, PHP_URL_HOST);

            return $actualHost === $expectedHost
                && ! str_contains($mailMessage->actionUrl, 'app.evil.go.id');
        }
    );
});
