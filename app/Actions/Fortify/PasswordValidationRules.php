<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::min(8)
            ->mixedCase()     // huruf besar dan kecil
            ->letters()       // huruf wajib ada
            ->numbers()       // angka wajib ada
            ->symbols()       // karakter spesial
            ->uncompromised(), 'confirmed'];
    }
}
