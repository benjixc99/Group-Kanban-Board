<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use App\Exceptions\GeneralException;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Repositories\Contracts\AccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Throwable;


/**
 * Class EloquentAccountRepository.
 */
class EloquentAccountRepository extends EloquentBaseRepository implements AccountRepository
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * EloquentUserRepository constructor.
     *
     * @param  User  $user
     * @param  UserRepository  $users
     *
     * @internal param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(User $user, UserRepository $users)
    {
        parent::__construct($user);
        $this->users = $users;
    }

    /**
     * @param  array  $input
     *
     * @return User
     * @throws Exception
     *
     * @throws Throwable
     */
    public function register(array $input): User
    {
        $user = $this->users->store([
            'name'  => $input['name'],
            'email'       => $input['email'],
            'password'    => $input['password'],
        ], true);

        if (config('account.verify_account')) {
            $user->sendEmailVerificationNotification();
        }

        Auth::login($user, true);

        return $user;
    }

    /**
     * @param  Authenticatable  $user
     * @param $name
     *
     * @return bool
     */
    public function hasPermission(Authenticatable $user, $name): bool
    {

        /** @var User $user */
        // First user is always super admin and cannot be deleted
        if ($user->id === 1) {
            return true;
        }

        $permissions = Session::get('permissions');

        if ($permissions == null) {
            $permissions = collect(json_decode($user->getPermissions(), true));
        }

        if (empty($permissions)) {
            return false;
        }
        return $permissions->contains($name);
    }

    /**
     * @param  array  $input
     *
     * @return JsonResponse
     *
     */
    public function update(array $input): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $user->fill(Arr::only($input, ['name', 'email', 'password']));
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => __('locale.customer.profile_was_successfully_updated'),
        ]);
    }

    /**
     * @return mixed
     * @throws GeneralException|Exception
     *
     */
    public function delete(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->is_super_admin) {
            throw new GeneralException(__('exceptions.backend.users.first_user_cannot_be_destroyed'));
        }

        if (!$user->delete()) {
            throw new GeneralException(__('exceptions.frontend.user.delete_account'));
        }

        return true;
    }

    /**
     * @param  Authenticatable  $user
     *
     * @return Authenticatable
     * @throws GeneralException
     */

    public function redirectAfterLogin(Authenticatable $user): Authenticatable
    {
        if (config('app.two_factor') === false || $user->two_factor == 0 || Session::get('two-factor-login-success') == 'success' || config('app.env') == 'demo') {
            $user->last_access_at = Carbon::now();
            if ($user->is_admin === true) {
                session(['permissions' => $user->getPermission]);
            } else {
                $permissions         = collect(json_decode($user->getPermissions, true));
                session(['permissions' => $permissions]);
            }

            if (!$user->save()) {
                throw new GeneralException('Something went wrong. Please try again.');
            }

            return $user;
        }

        return $user;
    }
}
