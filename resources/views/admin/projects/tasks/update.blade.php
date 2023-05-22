@extends('layouts/contentLayoutMaster')

@section('title', __('locale.task.editing_task') . ': ' . $task->name)

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('content')


    <!-- Basic Vertical form layout section start -->
    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-md-6 col-12">

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"> {{ __('locale.task.edit_task') }} </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical"
                                action="{{ route('admin.tasks.update', ['project' => $project->uid, 'task' => $task->uid]) }}"
                                method="POST" enctype="multipart/form-data">
                                @method('PATCH')
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="name"
                                                class="required form-label">{{ __('locale.labels.name') }}</label>
                                            <input type="name" id="name"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ $task->name }}" name="name" required>
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label for="description"
                                                class="form-label required">{{ __('locale.project.description') }}</label>
                                            <textarea class="form-control" id="description" name="description" required>{{ $task->description }}</textarea>
                                            @error('description')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="mb-1">
                                            <label for="priority_id"
                                                class="required form-label">{{ __('locale.labels.priority') }}</label>
                                            <select class="select2 form-select" id="priority_id" name="priority_id"
                                                required>
                                                @foreach ($priorities as $priority)
                                                    <option value="{{ $priority->id }}"
                                                        @if ($task->priority_id === $priority->id) {{ 'selected' }} @endif>
                                                        {{ $priority->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('priority_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="mb-1">
                                            <label for="status_id"
                                                class="required form-label">{{ __('locale.labels.status') }}</label>
                                            <select class="select2 form-select" id="status_id" name="status_id" required>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status->id }}"
                                                        @if ($task->status_id === $status->id) {{ 'selected' }} @endif>
                                                        {{ $status->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('status_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="mb-1">
                                            <label for="assignee_id"
                                                class="required form-label">{{ __('locale.labels.assignee') }}</label>
                                            <select class="select2 form-select" id="assignee_id" name="assignee_id"
                                                required>
                                                @foreach ($projectUsers as $user)
                                                    <option value="{{ $user->id }}"
                                                        @if ($task->assignee_id === $user->id) {{ 'selected' }} @endif>
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('assignee_id')
                                                <p><small class="text-danger">{{ $message }}</small></p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 mb-1">
                                        <label for="estimate"
                                            class="form-label required">{{ __('locale.labels.estimate') }}</label>
                                        <input type="number" id="estimate" class="form-control text-start" name="estimate"
                                            value="{{ $task->estimate }}" placeholder={{ $task->estimate }} />
                                        @error('estimate')
                                            <p><small class="text-danger">{{ $message }}</small></p>
                                        @enderror
                                    </div>


                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1">
                                            <i data-feather="save"></i> {{ __('locale.buttons.update') }}
                                        </button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>
    <!-- // Basic Vertical form layout section end -->


@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {


            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            // Basic Select2 select
            $(".select2").each(function() {
                let $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    // the following code is used to disable x-scrollbar when click in select input and
                    // take 100% width in responsive also
                    dropdownAutoWidth: true,
                    width: '100%',
                    dropdownParent: $this.parent()
                });
            });

            $('.select2#assignee_id').select2({
                placeholder: 'Search and select users',
                allowClear: true,
                data: @json($projectUsers),
                templateResult: formatUserOption
            });

            function formatUserOption(user) {
                if (!user.id) {
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
        });
    </script>
@endsection
