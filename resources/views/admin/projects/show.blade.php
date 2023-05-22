@extends('layouts/contentLayoutMaster')

@section('title', 'Project #' . $project->uid)

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/jkanban/jkanban.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/app-kanban.css')) }}">
@endsection

@section('content')
    <section class="">
        <div class="row">
            <div class="col-12 mb-3 mt-2">
                <h1>{{ $project->name }}</h1>
                <p>{{ $project->description }}</p>
                @can('create_new_task')
                    <div class="btn-group">
                        <a href="{{ route('admin.tasks.create', $project->uid) }}"
                            class="btn btn-success waves-light waves-effect fw-bold mx-1">
                            {{ __('locale.buttons.add_new_task') }} <i data-feather="plus-circle"></i></a>
                    </div>
                @endcan
                {{-- Add new user opens modal --}}
                @can('create_new_project')
                    @include('admin.projects._add_new_user')
                @endcan
                <div class="overflow-scroll mt-3 kanban-application">
                    <div class="kanban-wrapper">
                        <div class="kanban-board"></div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/jkanban/jkanban.min.js')) }}"></script>

    {!! $kanban->scripts() !!}
    <script>
        $(document).ready(function() {
            "use strict"

            kanbanObject.options.click = function(el) {
                var taskId = el.getAttribute("data-task-id");

                var url =
                    "{{ route('admin.tasks.show', ['project' => $project->uid, 'task' => ':taskId']) }}";
                url = url.replace(':taskId', taskId);

                window.location.href = url;
            };

            kanbanObject.options.dropEl = function(el, target, source, sibling) {
                kanbanObject.options.draggable = false;

                var taskId = $(el).attr("data-task-id");

                var newStatusId = $(target).parent().attr("data-id");

                var apiUrl =
                    "{{ route('admin.tasks.updatePriority', ['task' => ':taskId', 'project' => $project->uid]) }}";
                apiUrl = apiUrl.replace(':taskId', taskId);

                var payload = {
                    status_id: newStatusId,
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: apiUrl,
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        kanbanObject.options.draggable = true;
                        toastr['success'](response.message,
                            '{{ __('locale.labels.success') }}!!', {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                            });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        kanbanObject.options.draggable = true;

                        toastr['warning']("{{ __('locale.exceptions.something_went_wrong') }}",
                            '{{ __('locale.labels.warning') }}!', {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                            });
                    }
                });
            };


            $('.select2#user_ids').select2({
                placeholder: 'Search and select users',
                allowClear: true,
                data: {!! json_encode($users) !!},
                templateResult: formatUserOption,
                multiple: true,
            });


            function formatUserOption(user) {
                if (!user.uid) {
                    return user.name;
                }

                var avatarUrl = "{{ route('admin.users.avatar', ':uid') }}";
                avatarUrl = avatarUrl.replace(':uid', user.uid);

                var option = $(
                    '<div class="d-flex justify-content-left align-items-center">' +
                    '<div class="avatar me-1">' +
                    '<img src="' + avatarUrl + '" alt="Avatar" width="32" height="32">' +
                    '</div>' +
                    '<div class="d-flex flex-column">' +
                    '<span class="emp_name text-truncate fw-bold">' + user.name + '</span>' +
                    '<small class="emp_post text-truncate text-muted">' + user.email + '</small>' +
                    '</div>' +
                    '</div>'
                );

                return option;
            }

            $(".select2.role_id").on('change', function() {
                let $this = $(this);
                let userId = $this.closest('.d-flex').find('.avatar').data('user-id');
                let roleId = $this.val();

                let apiUrl = "{{ route('admin.projects.updateUserRole', ['project' => ':projectId']) }}";
                apiUrl = apiUrl.replace(':projectId', "{{ $project->uid }}");

                let payload = {
                    user_id: userId,
                    role_id: roleId,
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: apiUrl,
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        toastr['success'](response.message,
                            '{{ __('locale.labels.success') }}!!', {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                            });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr['warning']("{{ __('locale.exceptions.something_went_wrong') }}",
                            '{{ __('locale.labels.warning') }}!', {
                                closeButton: true,
                                positionClass: 'toast-top-right',
                                progressBar: true,
                                newestOnTop: true,
                            });
                    }
                });
            });


            $(".select2.role_id").each(function() {
                let $this = $(this);
                $this.wrap('<div class="position-relative half-width"></div>');
                $this.select2({
                    dropdownParent: $this.parent()
                });
            });


        });
    </script>
@endsection
