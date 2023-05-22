<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', 'AdminBaseController@index')->name('home');
Route::post('/projects/search', 'ProjectController@search')->name('projects.search');
Route::post('/project/{project}/updateUsers', 'ProjectController@updateUsers')->name('projects.updateUsers');
Route::post('/project/{project}/updateUserRole', 'ProjectController@updateUserRole')->name('projects.updateUserRole');
Route::get('/projects/{project}/show', 'ProjectController@show')->name('projects.show');

Route::resource('/projects', 'ProjectController', [
    'only' => ['index', 'create', 'store', 'update', 'destroy'],
]);


Route::get('/project/{project}/tasks/{task}/show', 'TaskController@show')->name('tasks.show');
Route::get('/project/{project}/tasks/{task}/edit', 'TaskController@edit')->name('tasks.edit');
Route::put('/project/{project}/tasks/{task}/updatePriority', 'TaskController@updatePriority')->name('tasks.updatePriority');
Route::post('/project/{project}/tasks/{task}/logProgress', 'TaskController@logProgress')->name('tasks.logProgress');
Route::post('/project/{project}/tasks/{task}/escalateTask', 'TaskController@escalateTask')->name('tasks.escalateTask');

Route::post('/project/{project}/tasks/{task}/comment', 'TaskController@addComment')->name('comment.add');
Route::get('/project/{project}/tasks/{task}/comment/{comment}/edit', 'TaskController@editComment')->name('comment.edit');
Route::get('/project/{project}/tasks/{task}/comment/{comment}/delete', 'TaskController@deleteComment')->name('comment.delete');

Route::resource('/project/{project}/tasks', 'TaskController', [
    'only' => ['index', 'create', 'store', 'update', 'destroy'],
]);

Route::post('/users/search', 'UserController@search')->name('users.search');
Route::get('/users/{user}/show', 'UserController@show')->name('users.show');
Route::get('/users/{user}/avatar', 'UserController@avatar')->name('users.avatar');
Route::post('/users/{user}/avatar', 'UserController@updateAvatar');
Route::post('/users/{user}/remove-avatar', 'UserController@removeAvatar');


Route::resource('/users', 'UserController', [
    'only' => ['index', 'create', 'store', 'update', 'destroy'],
]);
