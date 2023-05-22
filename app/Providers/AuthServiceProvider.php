<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Repositories\Contracts\AccountRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->registerPolicies();

        $accountRepository = $this->app->make(AccountRepository::class);

        foreach (config('permissions') as $roleKey => $permissions) {
            foreach ($permissions as $permissionKey => $permission) {
                Gate::define($permissionKey, function (User $user) use ($accountRepository, $roleKey, $permissionKey) {
                    return $accountRepository->hasPermission($user, $permissionKey);
                });
            }
        }
    }
}
