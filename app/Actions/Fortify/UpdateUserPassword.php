<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.string' => 'Kata sandi saat ini tidak valid.',
            'current_password.current_password' => 'Kata sandi saat ini tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.string' => 'Kata sandi baru tidak valid.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.min' => 'Kata sandi baru tidak memenuhi kebijakan keamanan.',
            'password.letters' => 'Kata sandi baru tidak memenuhi kebijakan keamanan.',
            'password.mixed' => 'Kata sandi baru tidak memenuhi kebijakan keamanan.',
            'password.numbers' => 'Kata sandi baru tidak memenuhi kebijakan keamanan.',
            'password.symbols' => 'Kata sandi baru tidak memenuhi kebijakan keamanan.',
            'password.uncompromised' => 'Kata sandi baru tidak memenuhi kebijakan keamanan.',
        ])->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
