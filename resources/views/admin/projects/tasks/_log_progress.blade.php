<div class="btn-group">
    <a href="{{ route('admin.tasks.show', ['project' => $project->id, 'task' => $task->id]) }}"
        class="btn btn-primary waves-light waves-effect fw-bold mx-1" data-bs-toggle="modal" data-bs-target="#logProgress">
        {{ __('locale.buttons.log_progress') }} <i data-feather="book"></i></a>
</div>

{{-- Modal --}}
<div class="modal fade text-left" id="logProgress" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">{{ __('locale.labels.log_progress') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.tasks.logProgress', ['project' => $project->uid, 'task' => $task->uid]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <label for="hours_spent"
                                class="form-label required">{{ __('locale.labels.hours_spent') }}</label>
                            <input type="text" id="hours_spent" class="form-control flatpickr-time text-start"
                                name="hours_spent" placeholder={{ __('locale.labels.five_hours') }} />
                            @error('hours_spent')
                                <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-1">
                                <label for="priority_id"
                                    class="required form-label">{{ __('locale.labels.priority') }}</label>
                                <select class="select2 form-select" id="priority_id" name="priority_id" required>
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
                        <div class="col-12">
                            <div class="mb-1">
                                <label for="description"
                                    class="form-label required">{{ __('locale.log_progress.description') }}</label>
                                <textarea class="form-control" id="description" name="description" required style="height: 200px">{{ old('description') }}</textarea>
                                @error('description')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" @if ($task->status->name == 'Done') {{ 'disabled' }} @endif
                        class="btn btn-primary">{{ __('locale.buttons.log_progress') }}</button>
                </div>
            </form>

        </div>
    </div>
</div>
