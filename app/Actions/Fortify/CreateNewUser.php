<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Models\Institution;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'institution_slug' => ['required', 'exists:institutions,slug'],
            'password' => $this->passwordRules(),
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Data pendaftaran tidak valid.',
            'institution_slug.required' => 'Instansi wajib dipilih.',
            'institution_slug.exists' => 'Instansi tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.string' => 'Kata sandi tidak valid.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'password.min' => 'Kata sandi tidak memenuhi kebijakan keamanan.',
            'password.letters' => 'Kata sandi tidak memenuhi kebijakan keamanan.',
            'password.mixed' => 'Kata sandi tidak memenuhi kebijakan keamanan.',
            'password.numbers' => 'Kata sandi tidak memenuhi kebijakan keamanan.',
            'password.symbols' => 'Kata sandi tidak memenuhi kebijakan keamanan.',
            'password.uncompromised' => 'Kata sandi tidak memenuhi kebijakan keamanan.',
        ])->validate();

        $institution = Institution::where('slug',  $input['institution_slug'])->firstOrFail();
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'institution_id' => $institution->id,
        ]);
        $user->assignRole('admin_instansi');
        return $user;
    }
}
