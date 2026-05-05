<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class SafeRegisteredUserController extends Controller
{
    use PasswordValidationRules;

    /**
     * Handle registration without revealing whether an email already exists.
     */
    public function store(Request $request, CreateNewUser $creator): RedirectResponse
    {
        if (config('fortify.lowercase_usernames') && $request->has(Fortify::username())) {
            $request->merge([
                Fortify::username() => Str::lower((string) $request->{Fortify::username()}),
            ]);
        }

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'institution_slug' => ['required', 'exists:institutions,slug'],
            'password' => $this->passwordRules(),
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'institution_slug.required' => 'Instansi wajib dipilih.',
            'institution_slug.exists' => 'Instansi tidak valid.',
        ])->validate();

        $email = (string) $request->input('email');

        if (! User::query()->where('email', $email)->exists()) {
            try {
                $creator->create($request->all());
            } catch (ValidationException $exception) {
                // A concurrent registration may win the unique race. Keep response uniform.
                if (! $this->hasOnlyDuplicateEmailError($exception)) {
                    throw $exception;
                }
            } catch (QueryException $exception) {
                // Swallow database-level duplicate key races to avoid account enumeration.
                if (! $this->isDuplicateKeyException($exception)) {
                    throw $exception;
                }
            }
        }

        return back()->with('status', 'Jika data pendaftaran valid, akun akan diproses. Silakan cek email Anda untuk langkah berikutnya.');
    }

    private function hasOnlyDuplicateEmailError(ValidationException $exception): bool
    {
        $errors = $exception->errors();

        return count($errors) === 1 && array_key_exists('email', $errors);
    }

    private function isDuplicateKeyException(QueryException $exception): bool
    {
        return $exception->getCode() === '23000';
    }
}
