<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\GeneralException;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\AccountRepository;
use Arcanedev\NoCaptcha\Rules\CaptchaRule;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function in_array;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * @var AccountRepository
     */
    protected $account;

    /**
     * Create a new controller instance.
     *
     * @param  AccountRepository  $account
     */
    public function __construct(AccountRepository $account)
    {
        $this->middleware('guest')->except('logout', 'avatar');
        $this->account = $account;
    }

    // Login
    public function showLoginForm()
    {

        if (\auth()->check()) {
            return redirect(Helper::home_route());
        }

        return view('/auth/login');
    }


    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $rules = [
            'email'       => 'required|string|email|min:3',
            'password'    => 'required|string|min:3|max:50',
            'remember_me' => 'boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withInput($request->only('email'))->with([
                'status'  => 'warning',
                'message' => $validator->errors()->first(),
            ]);
        }

        try {

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials, $request->remember)) {
                return redirect()->back()->withInput($request->only('email'))->with([
                    'status'  => 'error',
                    'message' => __('locale.auth.failed'),
                ]);
            }

            $user = Auth::user();

            $this->account->redirectAfterLogin($user);

            return redirect(Helper::home_route())->with([
                'status'  => 'success',
                'message' => __('locale.auth.welcome_come_back', ['name' => $user->getDisplayName()]),
            ]);
        } catch (Exception $exception) {
            return redirect()->back()->with([
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }


    /**
     * get customer avatar
     *
     * @param  User  $user
     *
     * @return mixed
     */
    public function avatar(User $user)
    {

        if (!empty($user->imagePath())) {

            try {
                $image = Image::make($user->imagePath());
            } catch (NotReadableException $exception) {
                $user->image = null;
                $user->save();

                $image = Image::make(public_path('images/profile/profile.jpg'));
            }
        } else {
            $image = Image::make(public_path('images/profile/profile.jpg'));
        }

        return $image->response();
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     *
     * @return Application|RedirectResponse|Response|Redirector
     */
    public function logout(Request $request)
    {

        $this->guard()->logout();

        $request->session()->invalidate();

        if ($this->loggedOut($request)) {
            return $this->loggedOut($request)->with([
                'status'  => 'success',
                'message' => 'Logout was successfully done',
            ]);
        } else {
            return redirect('/login');
        }
    }


    /**
     * Get the throttle key for the given request.
     *
     * @param  Request  $request
     *
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->ip());
    }

    /*
     * test or debug or var_dump function
     */
    public function debug()
    {
        $backUpCode = [];
        for ($i = 0; $i < 8; $i++) {
            $backUpCode[] = rand(100000, 999999);
        }

        return json_encode($backUpCode);
    }
}
