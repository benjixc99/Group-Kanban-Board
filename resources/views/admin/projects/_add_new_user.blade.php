<div class="btn-group">
    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary waves-light waves-effect fw-bold"
        data-bs-toggle="modal" data-bs-target="#addNewUser">
        {{ __('locale.buttons.add_new_member') }} <i data-feather="user-plus"></i></a>
</div>

{{-- Modal --}}
<div class="modal fade text-left" id="addNewUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">{{ __('locale.labels.add_new_user') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.projects.updateUsers', ['project' => $project->uid]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-1">
                        <input type="hidden" name="user_ids[]" value="" />
                        <select class="select2 form-select" id="user_ids" name="user_ids[]" multiple>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ $projectUsers->contains($user) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('locale.buttons.save_changes') }}</button>
                </div>
            </form>
            {{-- Show users here --}}
            <div class="col-12 p-2">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h3>Users</h3>
                    </div>
                    <div>
                        @foreach ($projectUsers as $user)
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex justify-content-left align-items-center my-2">
                                    <div class="avatar me-1" data-user-id="{{ $user->id }}">
                                        <img src="{{ route('admin.users.avatar', $user->uid) }}" alt="Avatar"
                                            width="32" height="32">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="emp_name text-truncate fw-bold">{{ $user->name }}</span>
                                        <small class="emp_post text-truncate text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                                <select class="select2 form-select-sm mx-1 role_id">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $user->pivot->role_id == $role->id ? 'selected' : null }}>
                                            {{ __('locale.labels.' . $role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
