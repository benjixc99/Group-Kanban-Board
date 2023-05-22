<?php

namespace App\Kanban;

use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use JinoAntony\Kanban\KBoard;
use JinoAntony\Kanban\KItem;
use JinoAntony\Kanban\Kanban;

class ProjectKanban extends Kanban
{
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }


    /**
     * Get the list of boards
     *
     * @return KBoard[]
     */
    public function getBoards()
    {
        $statuses = Status::all();
        $boards = [];

        foreach ($statuses as $status) {
            $board = KBoard::make(strval($status->id))
                ->setTitle($status->name);

            foreach ($statuses as $draggableStatus) {
                if ($draggableStatus->id !== $status->id) {
                    $board->canDragTo(strval($draggableStatus->id));
                }
            }

            $boards[] = $board;
        }


        return $boards;
    }


    /**
     * Get the data for each board
     *
     * @return array
     */
    public function data()
    {
        $statuses = Status::all();
        $data = [];

        foreach ($statuses as $status) {
            $tasks = Task::where('project_id', $this->project->id)
                ->where('status_id', $status->id)
                ->withCount('comments')
                ->get()
                ->map(function ($task) {
                    // Build the HTML content for the task item
                    $content = '<div class="card m-0">';
                    $content .= '<div class="card-body">';
                    $content .= '<h5 class="card-title line-clamp-2">' . $task->name . '</h5>';
                    $content .= '<p class="card-text line-clamp-3">' . $task->description . '</p>';
                    $content .= '</div>';
                    $content .= '<div class="card-footer">';
                    $content .= '<i data-feather="message-square"></i>';
                    $content .= '<span class="task-comment-count"> ' . $task->comments_count . '</span>';
                    $content .= '&nbsp;&nbsp;<span class="badge ' . $this->getPriorityBadgeColor($task->priority->name) . ' text-uppercase">' . $task->priority->name . '</span>';
                    $content .= '</div>';
                    $content .= '</div>';

                    return KItem::make(strval($task->id))
                        ->setContent($content)
                        ->withCustomProps(['task-id' => $task->uid]);
                })
                ->toArray();

            $data[strval($status->id)] = $tasks;
        }

        return $data;
    }

    protected function getPriorityBadgeColor($priorityName)
    {
        $priorities = [
            ['name' => 'Highest', 'color' => 'badge-light-danger'],
            ['name' => 'High', 'color' => 'badge-light-warning'],
            ['name' => 'Medium', 'color' => 'badge-light-primary'],
            ['name' => 'Low', 'color' => 'badge-light-info'],
            ['name' => 'Lowest', 'color' => 'badge-light-secondary'],
        ];

        foreach ($priorities as $priority) {
            if ($priority['name'] == $priorityName) {
                return $priority['color'];
            }
        }
        // Default to badge-light if no matching priority is found
        return 'badge-light-secondary';
    }


    public function build()
    {
        return $this->element('.kanban-board')
            ->margin('10px')
            ->width('365px')
            ->dragItems(true)
            ->dragBoards(false)
            ->objectName('kanbanObject');
    }
}
