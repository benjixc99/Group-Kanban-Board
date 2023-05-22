<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Role;
use App\Models\Status;
use App\Models\Task;
use App\Models\TaskEscalation;
use App\Models\TaskProgress;
use App\Models\User;
use App\Repositories\Contracts\ProjectsRepository;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends AdminBaseController
{
    /**
     * @var ProjectsRepository
     */
    protected $projects;

    /**
     * Create a new controller instance.
     *
     * @param  ProjectsRepository  $customers
     */
    public function __construct(ProjectsRepository $customers)
    {
        $this->projects = $customers;
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
    public function create(Project $project)
    {
        $this->authorize('create_new_task');

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/projects"), 'name' => __('locale.menu.Projects')],
            ['link' => url(config('app.admin_path') . '/projects/' . $project->uid . '/show'), 'name' => $project->name],
            ['name' => __("locale.menu.Add Task")],
        ];

        $statuses = Status::all('name', 'id');
        $priorities = Priority::all('name', 'id');
        $projectUsers = $project->users()->whereHas('role', function ($query) {
            $query->where('name', 'Developer');
        })->get();


        return view('admin.projects.tasks.create', compact('breadcrumbs', 'project', 'statuses', 'priorities', 'projectUsers'));
    }

    /**
     *
     * add new customer
     *
     * @param  Project  $project
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function store(Project $project, Request $request)
    {
        $this->authorize('create_new_task');

        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'status_id' => ['required', 'exists:statuses,id'],
            'estimate' => 'required|date_format:Y-m-d H:i|after:now',
            'assignee_id' => 'required|exists:users,id',
        ]);

        $estimate = Carbon::parse($request->input('estimate'))->diffInHours(now());

        $task = $project->createTask([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'estimate' => $estimate,
            'priority_id' => $request->input('priority_id'),
            'status_id' => $request->input('status_id'),
            'assignee_id' => $request->input('assignee_id'),
        ]);

        if ($task) {
            return redirect()->route('admin.projects.show', $project)->with([
                'status' => 'success',
                'message' => __('locale.task.task_successfully_created')
            ]);
        } else {
            return redirect()->route('admin.tasks.show')->with([
                'status' => 'error',
                'message' => __('locale.task.task_create_failed')
            ]);
        }
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

    public function show(Project $project, Task $task, Request $request)
    {
        $this->authorize('view_task_details');

        $projectTask = $project->tasks->where('uid', $task->uid);

        if ($projectTask->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task not found.',
            ]);
        }

        $task = $projectTask->first();

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/projects"), 'name' => __('locale.menu.Projects')],
            ['link' => url(config('app.admin_path') . '/projects/' . $project->uid . '/show'), 'name' => $project->name],
            ['name' => $task->name],
        ];

        $statuses = Status::all('name', 'id');
        $priorities = Priority::all('name', 'id');
        $roles = Role::where('name', '!=', 'administrator')->get();


        return view('admin.projects.tasks.show', compact('breadcrumbs', 'project', 'task', 'roles', 'statuses', 'priorities'));
    }

    /**
     * Update task priority
     * 
     * @param  Project  $project
     * @param  Task  $task
     * @return JsonResponse
     */
    public function updatePriority(Project $project, Task $task, Request $request)
    {
        $this->authorize('edit_task_details');

        $request->validate([
            'status_id' => 'required|exists:statuses,id',
        ]);

        $newStatusId = $request->input('status_id');

        $task->status_id = $newStatusId;
        $task->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Task priority updated successfully'
        ]);
    }

    /**
     * Log task progress
     * 
     * @param  Project  $project
     * @param  Task  $task
     * 
     * @return Application|Factory|View
     */
    public function logProgress(Project $project, Task $task, Request $request)
    {
        $request->validate([
            'hours_spent' => ['required', 'integer'],
            'description' => ['required', 'string'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'status_id' => ['required', 'exists:statuses,id'],
        ]);

        $hours_spent = $request->input('hours_spent');
        $description = $request->input('description');
        $statusId = $request->input('status_id');
        $priorityId = $request->input('priority_id');

        $projectTask = $project->tasks->where('uid', $task->uid);

        if ($projectTask->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task not found.',
            ]);
        }

        $task = $projectTask->first();

        if ($task->status->name == 'Done') {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task is already completed.',
            ]);
        }

        $estimatedRemainingWork = $task->estimate - $task->progress;
        $newProgress = min($hours_spent, $estimatedRemainingWork);

        $task->progress += $newProgress;
        $task->priority_id = $priorityId;
        $task->status_id = $statusId;
        $task->save();

        $taskProgress = new TaskProgress();
        $taskProgress->task_id = $task->id;
        $taskProgress->user_id = auth()->user()->id;
        $taskProgress->hours_spent += $newProgress;
        $taskProgress->description = $description;
        $taskProgress->save();

        if ($task->progress >= $task->estimate) {
            $task->status_id = Status::where('name', 'Done')->first()->id;
            $task->save();
        }

        return redirect()->back()->with([
            'status' => 'success',
            'message' => __('locale.task.task_progress_logged_successfully'),
        ]);
    }


    /**
     * Request task escalation
     * 
     * @param  Project  $project
     * @param  Task  $task
     * 
     * @return Application|Factory|View
     */
    public function escalateTask(Project $project, Task $task, Request $request)
    {
        $request->validate([
            'reason' => ['required', 'string'],
            'comment' => ['sometimes', 'string'],
        ]);

        $projectTask = $project->tasks->where('uid', $task->uid);

        if ($projectTask->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task not found.',
            ]);
        }

        $task = $projectTask->first();

        $taskEscalation = new TaskEscalation();
        $taskEscalation->task_id = $task->id;
        $taskEscalation->user_id = auth()->user()->id;
        $taskEscalation->reason = $request->input('reason');
        $taskEscalation->comment = $request->input('comment');
        $taskEscalation->save();

        return redirect()->back()->with([
            'status' => 'success',
            'message' => __('locale.task.task_escalation_request_sent'),
        ]);
    }

    /**
     * Add new comment to task
     */
    public function addComment(Project $project, Task $task, Request $request)
    {
        $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $projectTask = $project->tasks->where('uid', $task->uid);

        if ($projectTask->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task not found.',
            ]);
        }

        $task = $projectTask->first();

        $taskComment = new Comment();
        $taskComment->task_id = $task->id;
        $taskComment->user_id = auth()->user()->id;
        $taskComment->comment = $request->input('comment');
        $taskComment->save();

        return redirect()->back()->with([
            'status' => 'success',
            'message' => __('locale.task.task_comment_added'),
        ]);
    }

    /**
     * Delete comment from task
     * 
     * @param  Project  $project
     * @param  Task  $task
     * @param  Comment  $comment
     * 
     * @return Application|Factory|View
     */
    public function deleteComment(Project $project, Task $task, Comment $comment)
    {
        $projectTask = $project->tasks->where('uid', $task->uid);

        if ($projectTask->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task not found.',
            ]);
        }

        $task = $projectTask->first();

        $taskComment = $task->comments->where('id', $comment->id);

        if ($taskComment->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Comment not found.',
            ]);
        }
        $comment = $taskComment->first();
        $comment->delete();

        return redirect()->back()->with([
            'status' => 'success',
            'message' => __('locale.task.task_comment_deleted'),
        ]);
    }

    /**
     * Edit comment from task
     * 
     * @param  Project  $project
     * @param  Task  $task
     * @param  Comment  $comment
     * @param  Request  $request
     * 
     * @return Application|Factory|View
     */
    public function editComment(Project $project, Task $task, Comment $comment, Request $request)
    {
        $projectTask = $project->tasks->where('uid', $task->uid);

        if ($projectTask->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task not found.',
            ]);
        }

        $task = $projectTask->first();

        $taskComment = $task->comments->where('id', $comment->id);

        if ($taskComment->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Comment not found.',
            ]);
        }
        $comment = $taskComment->first();

        $comment->comment = $request->input('comment');
        $comment->save();

        return redirect()->back()->with([
            'status' => 'success',
            'message' => __('locale.task.task_comment_updated'),
        ]);
    }

    /**
     * Show update form
     */
    public function edit(Project $project, Task $task, Request $request)
    {
        $this->authorize('edit_task_details');

        $projectTask = $project->tasks->where('uid', $task->uid);

        if ($projectTask->isEmpty()) {
            return redirect()->back()->with([
                'status' => 'error',
                'message' => 'Task not found.',
            ]);
        }

        $task = $projectTask->first();

        $breadcrumbs = [
            ['link' => url(config('app.admin_path') . "/dashboard"), 'name' => __('locale.menu.Dashboard')],
            ['link' => url(config('app.admin_path') . "/projects"), 'name' => __('locale.menu.Projects')],
            ['link' => url(config('app.admin_path') . '/projects/' . $project->uid . '/show'), 'name' => $project->name],
            ['name' => $task->name],
        ];

        $statuses = Status::all('name', 'id');
        $priorities = Priority::all('name', 'id');
        $projectUsers = $project->users()->whereHas('role', function ($query) {
            $query->where('name', 'Developer');
        })->get();
        

        return view('admin.projects.tasks.update', compact('breadcrumbs', 'project', 'task', 'statuses', 'priorities', 'projectUsers'));
    }


    /**
     * Update task
     * 
     * @param  Project  $project
     * @param  Task  $task
     * @param  Request  $request
     * 
     * @return Application|Factory|View
     */
    public function update(Project $project, Task $task, Request $request)
    {
        $this->authorize('edit_task_details');

        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'priority_id' => ['required', 'exists:priorities,id'],
            'status_id' => ['required', 'exists:statuses,id'],
            'estimate' => ['required', 'integer', 'min:' . $task->estimate],
            'assignee_id' => 'required|exists:users,id',
        ]);

        $task->name = $request->input('name');
        $task->description = $request->input('description');
        $task->estimate = $request->input('estimate');
        $task->priority_id = $request->input('priority_id');
        $task->status_id = $request->input('status_id');
        $task->assignee_id = $request->input('assignee_id');
        $task->save();

        return redirect()->route('admin.tasks.show', ['project' => $project->uid, 'task' => $task->uid])->with([
            'status' => 'success',
            'message' => __('locale.task.task_successfully_updated'),
        ]);
    }
}
