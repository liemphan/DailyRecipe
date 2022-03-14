<?php

namespace DailyRecipe\Http\Controllers\Auth;

use DailyRecipe\Auth\Access\LoginService;
use DailyRecipe\Auth\Access\RegistrationService;
use DailyRecipe\Auth\Access\SocialAuthService;
use DailyRecipe\Auth\User;
use DailyRecipe\Exceptions\StoppedAuthenticationException;
use DailyRecipe\Exceptions\UserRegistrationException;
use DailyRecipe\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    protected $socialAuthService;
    protected $registrationService;
    protected $loginService;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $redirectPath = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct(
        SocialAuthService   $socialAuthService,
        RegistrationService $registrationService,
        LoginService        $loginService
    )
    {
        $this->middleware('guest');
        $this->middleware('guard:standard');

        $this->socialAuthService = $socialAuthService;
        $this->registrationService = $registrationService;
        $this->loginService = $loginService;

        $this->redirectTo = url('/');
        $this->redirectPath = url('/');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:8'],
            'password-confirm' => ['required', 'same:password']
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @throws UserRegistrationException
     */
    public function getRegister()
    {
        $this->registrationService->ensureRegistrationAllowed();
        $socialDrivers = $this->socialAuthService->getActiveDrivers();

        return view('auth.register', [
            'socialDrivers' => $socialDrivers,
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @throws UserRegistrationException
     * @throws StoppedAuthenticationException
     */
    public function postRegister(Request $request)
    {
        $this->registrationService->ensureRegistrationAllowed();
        $this->validator($request->all())->validate();
        $userData = $request->all();

        try {
            $user = $this->registrationService->registerUser($userData);
            $this->loginService->login($user, auth()->getDefaultDriver());
        } catch (UserRegistrationException $exception) {
            if ($exception->getMessage()) {
                $this->showErrorNotification($exception->getMessage());
            }

            return redirect($exception->redirectLocation);
        }

        $this->showSuccessNotification(trans('auth.register_success'));

        return redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
