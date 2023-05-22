<?php

use Illuminate\Support\Facades\Route;

Route::group(
    ['namespace' => 'Auth'],
    function () {
        // Registration Routes...
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register');

        // Authentication Routes...
        Route::get('login', 'LoginController@showLoginForm')->name('login');
        Route::post('login', 'LoginController@login');
        Route::post('logout', 'LoginController@logout')->name('logout');

        Route::get('login/{provider}', 'LoginController@redirectToProvider')->name('social.login');
        Route::get('login/{provider}/callback', 'LoginController@handleProviderCallback')->name('social.callback');

        // Password Reset Routes...
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
        Route::post('password/reset', 'ResetPasswordController@reset')->name('password.update');
    }
);

Route::group(
    [
        'namespace'  => 'User',
        'as'         => 'user.',
        'middleware' => ['auth', 'verified'],
    ],
    function () {
        /*
        * User Dashboard Specific
        */
        Route::get('/dashboard', 'UserController@index')->name('home');

        /*
        * User Account Specific
        */
        Route::get('account', 'AccountController@index')->name('account');
        Route::get('avatar', 'AccountController@avatar')->name('avatar');
        Route::post('avatar', 'AccountController@updateAvatar');
        Route::post('remove-avatar', 'AccountController@removeAvatar')->name('remove_avatar');

        /*
        * User Profile Update
        */
        Route::patch('account/update', 'AccountController@update')->name('account.update');
        Route::post('account/change-password', 'AccountController@changePassword')->name('account.change.password');
    }
);
