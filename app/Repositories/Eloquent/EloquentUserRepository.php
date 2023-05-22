<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Helpers\Helper;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use App\Repositories\Contracts\RoleRepository;
use App\Repositories\Contracts\UserRepository;
use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Throwable;


class EloquentUserRepository extends EloquentBaseRepository implements UserRepository
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var RoleRepository
     */
    protected $roles;

    /**
     * EloquentUserRepository constructor.
     *
     * @param  User  $user
     * @param  RoleRepository  $roles
     * @param  Repository  $config
     */
    public function __construct(
        User $user,
        RoleRepository $roles,
        Repository $config
    ) {
        parent::__construct($user);
        $this->roles  = $roles;
        $this->config = $config;
    }

    /**
     * @param  array  $input
     * @param  bool  $confirmed
     *
     * @return User
     * @throws GeneralException
     * @throws Exception
     *
     */
    public function store(array $input, $confirmed = false): User
    {
        /** @var User $user */
        $user = $this->make(Arr::only($input, ['name', 'email']));

        if (!$this->save($user, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }


        //  event(new UserCreated($user));

        return $user;
    }

    /**
     * @param  User  $user
     * @param  array  $input
     *
     * @return User
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function update(User $user, array $input): User
    {
        $user->fill(Arr::except($input, 'password'));

        if ($user->isAdministrator()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        if (!$this->save($user, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        //  event(new UserUpdated($user));

        return $user;
    }

    /**
     * @param  User  $user
     * @param  array  $input
     *
     * @return bool
     * @throws GeneralException
     *
     */
    private function save(User $user, array $input): bool
    {
        if (isset($input['password']) && !empty($input['password'])) {
            $user->password = Hash::make($input['password']);
        }

        if (isset($input['role_id']) && !empty($input['role_id'])) {
            $user->role_id = $input['role_id'];
        } else {
            $customer_role = Role::where('name', 'customer')->first();
            $user->role_id = $customer_role->id;
        }


        if (!$user->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  User  $user
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(User $user)
    {
        if (!$user->can_delete) {
            throw new GeneralException(__('locale.exceptions.unauthorized'));
        }

        if (!$user->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        //        event(new UserDeleted($user));

        return true;
    }

    /**
     * @param  User  $user
     *
     * @return RedirectResponse
     * @throws Exception
     *
     */
    public function impersonate(User $user): RedirectResponse
    {
        if ($user->is_super_admin) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        $authenticatedUser = auth()->user();

        if (
            $authenticatedUser->id === $user->id
            || Session::get('admin_user_id') === $user->id
        ) {
            return redirect()->route('admin.home');
        }

        if (!Session::get('admin_user_id')) {
            session(['admin_user_id' => $authenticatedUser->id]);
            session(['admin_user_name' => $authenticatedUser->name]);
            session(['temp_user_id' => $user->id]);
        }

        //Login user
        auth()->loginUsingId($user->id);

        return redirect(Helper::home_route());
    }
}
