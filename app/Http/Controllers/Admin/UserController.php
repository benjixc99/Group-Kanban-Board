<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GeneralException;
use App\Http\Requests\Accounts\UpdateAvatarRequest;
use App\Http\Requests\Accounts\UpdateUserRequest;
use App\Library\Tool;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;

class UserController extends AdminBaseController
{
    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('modify_user_roles');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Users')],
            ['name' => __('locale.menu.Users')],
        ];


        return view('admin.user.index', compact('breadcrumbs'));
    }


    /**
     * view all users
     *
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('modify_user_roles');

        $columns = [
            0 => 'responsive_id',
            2 => 'uid',
            3 => 'name',
            4 => 'role',
            5 => 'status',
            6 => 'actions',
        ];

        $totalData = User::nonAdmin()->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $users = User::nonAdmin()->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            } else {
                $search = $request->input('search.value');
                
                $users = User::nonAdmin()->whereLike(['uid', 'name', 'email'], $search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
                
                $totalFiltered = User::nonAdmin()->whereLike(['uid', 'name', 'email'], $search)->count();
                
            }
            // exit(json_encode($users));

        $data = [];
        if (!empty($users)) {
            foreach ($users as $user) {
                $show        = route('admin.users.show', $user->uid);

                $edit              = __('locale.buttons.edit');
                $delete            = __('locale.buttons.delete');

                $super_user = true;
                if ($user->id != 1) {
                    $super_user = false;
                }

                if ($user->role->name == 'customer') {
                    $role = '<span class="badge badge-light-secondary text-uppercase">' . __('locale.labels.customer') . '</span>';
                } elseif ($user->role->name == 'project_manager') {
                    $role = '<span class="badge badge-light-success text-uppercase">' . __('locale.labels.project_manager') . '</span>';
                } else {
                    $role = '<span class="badge badge-light-info text-uppercase">' . __('locale.labels.developer') . '</span>';
                }

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $user->uid;
                $nestedData['avatar']        = route('admin.users.avatar', $user->uid);
                $nestedData['email']         = $user->email;
                $nestedData['name']          = $user->name;
                $nestedData['role'] = $role;
                $nestedData['created_at']    = __('locale.labels.created_at') . ': ' . Tool::formatDate($user->created_at);
                $nestedData['show']              = $show;
                $nestedData['show_label']        = $edit;
                $nestedData['delete']            = $user->uid;
                $nestedData['delete_label']      = $delete;
                $nestedData['super_user']        = $super_user;

                $data[] = $nestedData;
            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();
    }

    /**
     * View user for edit
     *
     * @param  User  $user
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(User $user)
    {
        $this->authorize('change_user_type');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/users"), 'name' => __('locale.menu.Users')],
            ['name' => $user->displayName()],
        ];

        $roles = Role::where('name', '!=', 'administrator')->get();

        return view('admin.user.show', compact('breadcrumbs', 'user', 'roles'));
    }

    /**
     * get user avatar
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
     * update avatar
     *
     * @param  User  $user
     * @param  UpdateAvatarRequest  $request
     *
     * @return RedirectResponse
     */
    public function updateAvatar(User $user, UpdateAvatarRequest $request): RedirectResponse
    {
        try {
            // Upload and save image
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {

                    // Remove old images
                    $user->removeImage();
                    $user->image = $user->uploadImage($request->file('image'));
                    $user->save();

                    return redirect()->route('admin.users.show', $user->uid)->with([
                        'status'  => 'success',
                        'message' => __('locale.user.avatar_update_successful'),
                    ]);
                }

                return redirect()->route('admin.users.show', $user->uid)->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.invalid_image'),
                ]);
            }

            return redirect()->route('admin.users.show', $user->uid)->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_image'),
            ]);
        } catch (Exception $exception) {
            return redirect()->route('admin.users.show', $user->uid)->with([
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }


    /**
     * remove avatar
     *
     * @param  User  $user
     *
     * @return JsonResponse
     */
    public function removeAvatar(User $user): JsonResponse
    {
        // Remove old images
        $user->removeImage();
        $user->image = null;
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => __('locale.user.avatar_remove_successful'),
        ]);
    }


    /**
     * update user basic account information
     *
     * @param  User  $user
     * @param  UpdateUserRequest  $request
     *
     * @return RedirectResponse
     */

    public function update(User $user, UpdateUserRequest $request): RedirectResponse
    {
        $this->user->update($user, $request->input());

        return redirect()->route('admin.users.show', $user->uid)->withInput(['tab' => 'account'])->with([
            'status'  => 'success',
            'message' => __('locale.user.users_successfully_updated'),
        ]);
    }

    /**
     * change users status
     *
     * @param  User  $users
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws GeneralException
     */
    public function activeToggle(User $users): JsonResponse
    {
        try {
            $this->authorize('edit users');

            if ($users->update(['status' => !$users->status])) {
                return response()->json([
                    'status'  => 'success',
                    'message' => __('locale.user.users_successfully_change'),
                ]);
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status'  => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * destroy user
     *
     * @param  User  $user
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws Exception
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('change_user_type');

        /* PhoneNumbers::where('user_id', $user->id)->update([
            'status' => 'available',
        ]);
        Notifications::where('user_id', $user->id)->delete(); */



        if (!$user->delete()) {
            return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => __('locale.user.users_successfully_deleted'),
        ]);
    }
}
