<?php

$customersPermissions = [
    'view_kanban_board' => [
        'display_name' => 'View Kanban Board',
        'category' => 'Kanban Board',
    ],
    'view_task_details' => [
        'display_name' => 'View Task Details',
        'category' => 'Task Management',
    ],
    'request_task_escalation' => [
        'display_name' => 'Request Task Escalation',
        'category' => 'Task Management',
    ],
    'change_own_password' => [
        'display_name' => 'Change Own Password',
        'category' => 'User Management',
    ],
];

$developersPermissions = array_merge($customersPermissions, [
    'change_task_status' => [
        'display_name' => 'Change Task Status',
        'category' => 'Task Management',
    ],
    'log_task_progress' => [
        'display_name' => 'Log Task Progress',
        'category' => 'Task Management',
    ],
    'update_task_remaining_work' => [
        'display_name' => 'Update Task Remaining Work',
        'category' => 'Task Management',
    ],
    'manage_own_comments' => [
        'display_name' => 'Manage Own Comments',
        'category' => 'Task Management',
    ],
]);

$projectManagerPermissions = array_merge($developersPermissions, [
    'create_new_task' => [
        'display_name' => 'Create New Task',
        'category' => 'Task Management',
    ],
    'edit_task_details' => [
        'display_name' => 'Edit Task Details',
        'category' => 'Task Management',
    ],
    'assign_task' => [
        'display_name' => 'Assign Task',
        'category' => 'Task Management',
    ],
]);

$adminPermissions = array_merge($projectManagerPermissions, [
    'create_new_project' => [
        'display_name' => 'Create New Project',
        'category' => 'Project Management',
    ],
    'change_user_type' => [
        'display_name' => 'Change User Type',
        'category' => 'User Management',
    ],
    'modify_user_roles' => [
        'display_name' => 'Modify User Roles',
        'category' => 'User Management',
    ],
    'edit_any_task' => [
        'display_name' => 'Edit Any Task',
        'category' => 'Task Management',
    ],
    'assign_any_task' => [
        'display_name' => 'Assign Any Task',
        'category' => 'Task Management',
    ],
    'view_all_tasks' => [
        'display_name' => 'View All Tasks',
        'category' => 'Task Management',
    ],
    'delete_any_task' => [
        'display_name' => 'Delete Any Task',
        'category' => 'Task Management',
    ],
    'edit_any_task_priority' => [
        'display_name' => 'Edit Any Task Priority',
        'category' => 'Task Management',
    ],
    'edit_any_task_status' => [
        'display_name' => 'Edit Any Task Status',
        'category' => 'Task Management',
    ],
    'manage_any_comments' => [
        'display_name' => 'Manage Any Comments',
        'category' => 'Task Management',
    ],
    'manage_security' => [
        'display_name' => 'Manage Security',
        'category' => 'System Management',
    ],
]);

return [
    'customer' => $customersPermissions,
    'developer' => $developersPermissions,
    'project_manager' => $projectManagerPermissions,
    'administrator' => $adminPermissions,
];
