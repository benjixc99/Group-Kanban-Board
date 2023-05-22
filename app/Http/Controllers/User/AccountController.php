<?php

namespace App\Http\Controllers\User;


use App\Http\Requests\Accounts\ChangePasswordRequest;
use App\Http\Requests\Accounts\UpdateUserRequest;
use Exception;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use RuntimeException;

class AccountController extends Controller
{
        /**
         * @var AccountRepository
         */
        protected $account;


        /**
         * RegisterController constructor.
         *
         * @param  AccountRepository  $account
         */
        public function __construct(AccountRepository $account)
        {
                $this->account = $account;
        }


        /**
         * show profile page
         *
         * @return Application|Factory|View
         */
        public function index()
        {
                $breadcrumbs = [
                        ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                        ['name' => Auth::user()->displayName()],
                ];


                $user = Auth::user();

                return view('auth.profile.index', compact('breadcrumbs', 'user'));
        }

        /**
         * get avatar
         *
         * @return mixed
         */
        public function avatar()
        {
                if (!empty(Auth::user()->imagePath())) {

                        try {
                                $image = Image::make(Auth::user()->imagePath());
                        } catch (NotReadableException $exception) {
                                Auth::user()->image = null;
                                Auth::user()->save();

                                $image = Image::make(public_path('images/profile/profile.jpg'));
                        }
                } else {
                        $image = Image::make(public_path('images/profile/profile.jpg'));
                }

                return $image->response();
        }

        /**
         * update avatar
         *
         * @param  Request  $request
         *
         * @return RedirectResponse
         */
        public function updateAvatar(Request $request): RedirectResponse
        {
                $user = Auth::user();

                try {
                        // Upload and save image
                        if ($request->hasFile('image') && $request->file('image')->isValid()) {
                                // Remove old images
                                $user->removeImage();
                                $user->image = $user->uploadImage($request->file('image'));
                                $user->save();

                                return redirect()->route('user.account')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.customer.avatar_update_successful'),
                                ]);
                        }

                        return redirect()->route('user.account')->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.invalid_image'),
                        ]);
                } catch (Exception $exception) {
                        return redirect()->route('user.account')->with([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                }
        }

        /**
         * remove avatar
         *
         * @return JsonResponse
         */
        public function removeAvatar(): JsonResponse
        {

                $user = Auth::user();
                // Remove old images
                $user->removeImage();
                $user->image = null;
                $user->save();

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.customer.avatar_remove_successful'),
                ]);
        }

        /**
         * profile update
         *
         * @param  UpdateUserRequest  $request
         *
         * @return RedirectResponse
         */
        public function update(UpdateUserRequest $request): RedirectResponse
        {
                $input = $request->all();

                $data = $this->account->update($input);

                if (isset($data->getData()->status)) {
                        return redirect()->route('user.account')->withInput(['tab' => 'account'])->with([
                                'status'  => $data->getData()->status,
                                'message' => $data->getData()->message,
                        ]);
                }

                return redirect()->route('user.account')->withInput(['tab' => 'account'])->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);
        }


        public function changePassword(ChangePasswordRequest $request)
        {
                Auth::user()->update([
                        'password' => Hash::make($request->password),
                ]);

                Auth::logout();

                $request->session()->invalidate();

                return redirect('/login')->with([
                        'status'  => 'success',
                        'message' => 'Password was successfully changed',
                ]);
        }

        /**
         * @param  Request  $request
         *
         * @return mixed
         * @throws RuntimeException
         *
         */
        public function delete(Request $request)
        {
                $this->account->delete();


                auth()->logout();
                $request->session()->flush();
                $request->session()->regenerate();

                return redirect()->route('user.home');
        }
}
