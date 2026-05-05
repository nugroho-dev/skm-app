<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse as FailedPasswordResetLinkRequestResponseContract;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as SuccessfulPasswordResetLinkRequestResponseContract;

class UniformPasswordResetLinkResponse implements FailedPasswordResetLinkRequestResponseContract, SuccessfulPasswordResetLinkRequestResponseContract
{
    /**
     * Create a new response instance.
     */
    public function __construct(protected string $status = '')
    {
        // Status is intentionally ignored to avoid account enumeration via response differences.
    }

    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request)
    {
        $message = 'Jika email terdaftar, tautan reset kata sandi akan dikirim.';

        if ($request->wantsJson()) {
            return new JsonResponse(['message' => $message], 200);
        }

        return back()
            ->withInput($request->only('email'))
            ->with('status', $message);
    }
}
