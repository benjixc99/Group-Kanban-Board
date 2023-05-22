<div class="btn-group">
    <button class="btn btn-success waves-light waves-effect fw-bold mx-1" data-bs-toggle="modal"
        data-bs-target="#escalateTask">
        {{ __('locale.buttons.escalate_task') }} <i data-feather="clock"></i></button>
</div>

{{-- Modal --}}
<div class="modal fade text-left" id="escalateTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">{{ __('locale.labels.escalate_task') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.tasks.escalateTask', ['project' => $project->uid, 'task' => $task->uid]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-1">
                            <label for="reason" class="form-label required">{{ __('locale.labels.reason') }}</label>
                            <input type="text" id="reason" class="form-control text-start"
                                name="reason" placeholder={{ __('locale.labels.reason') }} value={{ old('reason') }}/>
                            @error('reason')
                                <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label for="comment"
                                    class="form-label">{{ __('locale.log_progress.comment') }}</label>
                                <textarea class="form-control" id="comment" name="comment" style="height: 200px">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">{{ __('locale.buttons.escalate_task') }}</button>
                </div>
            </form>

        </div>
    </div>
</div>
