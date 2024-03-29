<?php

namespace DailyRecipe\Http\Controllers\Auth;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('guard:standard');
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param Request $request
     * @param string $response
     *
     * @return Response
     */
    protected function sendResetResponse(Request $request, $response)
    {
        $message = trans('auth.reset_password_success');
        $this->showSuccessNotification($message);
        $this->logActivity(ActivityType::AUTH_PASSWORD_RESET_UPDATE, user());

        return redirect($this->redirectPath())
            ->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param Request $request
     * @param string $response
     *
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        // We show invalid users as invalid tokens as to not leak what
        // users may exist in the system.
        if ($response === Password::INVALID_USER) {
            $response = Password::INVALID_TOKEN;
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
