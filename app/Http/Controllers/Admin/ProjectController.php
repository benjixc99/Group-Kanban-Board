<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreProjectRequest;
use App\Kanban\ProjectKanban;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\ProjectsRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends AdminBaseController
{
    /**
     * @var ProjectsRepository
     */
    protected $projects;

    /**
     * Create a new controller instance.
     *
     * @param  ProjectsRepository  $projects
     */
    public function __construct(ProjectsRepository $projects)
    {
        $this->projects = $projects;
    }


    /**
     * @return Application|Factory|View
     * @throws AuthorizationException
     */

    public function index()
    {

        $this->authorize('view_kanban_board');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Projects')],
            ['name' => __('locale.menu.Projects')],
        ];


        return view('admin.projects.index', compact('breadcrumbs'));
    }


    /**
     * create new customer
     *
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create_new_project');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/projects"), 'name' => __('locale.menu.Projects')],
            ['name' => __('locale.project.create_new_project')],
        ];

        return view('admin.projects.create', compact('breadcrumbs'));
    }

    /**
     * @param  Request  $request
     *
     * @return void
     * @throws AuthorizationException
     */
    public function search(Request $request)
    {

        $this->authorize('view_kanban_board');

        $columns = [
            0 => 'responsive_id',
            1 => 'uid',
            2 => 'name',
            3 => 'tasks',
            4 => 'action',
        ];

        $totalData = $request->user()->projects()->count();
        $projectQuery = $request->user()->projects();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $projects = $projectQuery->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $projects = $projectQuery->whereLike(['uid', 'name', 'description'], $search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = $projectQuery->whereLike(['uid', 'name', 'description'], $search)->count();
        }


        $data = [];
        if (!empty($projects)) {
            foreach ($projects as $project) {
                $show = route('admin.projects.show', $project->uid);
                $totalTasks = '<span class="badge badge-light-info text-uppercase">' . $project->tasks()->count() . ' ' . __('locale.labels.tasks') . '</span>';

                $nestedData['responsive_id'] = '';
                $nestedData['uid']           = $project->uid;
                $nestedData['name']          = $project->name;
                $nestedData['tasks']         = $totalTasks;
                $nestedData['view']          = $show;

                $data[] = $nestedData;
            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    /**
     * Add new customer
     *
     * @param  StoreProjectRequest  $request
     * @return RedirectResponse
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorize('create_new_project');

        $input = $request->input();

        $project = $this->projects->store($input);

        $user = $request->user();
        $project->users()->attach($user->id, ['role_id' => $user->role->id]);

        return redirect()->route('admin.projects.show', $project->uid)->with([
            'status' => 'success',
            'message' => __('locale.customer.customer_successfully_added'),
        ]);
    }


    /**
     * View customer for edit
     *
     * @param  User  $customer
     *
     * @return Application|Factory|View
     *
     * @throws AuthorizationException
     */

    public function show(Project $project)
    {
        $this->authorize('view_kanban_board');

        $tasks = $project->tasks()->get();
        $projectUsers = $project->users()->whereDoesntHave('role', function ($query) {
            $query->where('name', 'administrator');
        })->get();

        $user = Auth::user();

        // Check if the authenticated user is a member of the project
        if (!$user->isAdministrator() && !$project->users->contains(Auth::user())) {
            abort(403, 'Unauthorized access.');
        }

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/projects"), 'name' => __('locale.menu.Projects')],
            ['name' => $project->name],
        ];

        $roles = Role::where('name', '!=', 'administrator')->get();
        $kanban = new ProjectKanban($project);
        $users = User::nonAdmin()->get();

        return $kanban->render('admin.projects.show', compact('breadcrumbs', 'projectUsers', 'project', 'user', 'roles', 'users'));
    }

    /**
     * update users in project
     *
     * @param  Project  $project
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function updateUsers(Project $project, Request $request): RedirectResponse
    {
        $this->authorize('create_new_project');

        $request->validate([
            'user_ids' => 'required|array',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        $usersData = $users->mapWithKeys(function ($user) {
            return [$user->id => ['role_id' => $user->role_id]];
        });

        // Exclude the superadmin user from detachment
        $superAdmin = User::first();
        $project->users()->where('users.id', '!=', $superAdmin->id)->detach();

        if ($users->count() > 0) {
            $project->users()->syncWithoutDetaching($usersData);
        }

        return redirect()->route('admin.projects.show', $project->uid)->with([
            'status'  => 'success',
            'message' => __('locale.customer.project_successfully_updated'),
        ]);
    }


    /**
     * update user role in project
     *
     * @param  Project  $project
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function updateUserRole(Project $project, Request $request): RedirectResponse
    {
        $this->authorize('modify_user_roles');

        $request->validate([
            'user_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        $user = User::find($request->user_id);

        if ($user) {
            $project->users()->updateExistingPivot($user->id, ['role_id' => $request->role_id]);
        }

        return redirect()->route('admin.projects.show', $project->uid)->with([
            'status'  => 'success',
            'message' => __('locale.customer.project_successfully_updated'),
        ]);
    }
}
