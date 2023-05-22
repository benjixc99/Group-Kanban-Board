<div class="card">
    <div class="card-body py-2 my-25">
        <!-- header section -->
        <div class="d-flex">
            <a href="{{ route('admin.users.show', $user->uid) }}" class="me-25">
                <img src="{{ route('admin.users.avatar', $user->uid) }}" alt="{{ $user->displayName() }}"
                    class="uploadedAvatar rounded me-50" height="100" width="100" />
            </a>
            <!-- upload and reset button -->
            <div class="d-flex align-items-end mt-75 ms-1">
                <div>
                    @include('admin.user._update_avatar')
                    <button id="remove-avatar" data-id="{{ $user->uid }}"
                        class="btn btn-sm btn-danger mb-75 me-75"><i data-feather="trash-2"></i>
                        {{ __('locale.labels.remove') }}</button>

                </div>
            </div>
            <!--/ upload and reset button -->
        </div>
        <!--/ header section -->

        <!-- form -->
        <form class="form form-vertical mt-2 pt-50" action="{{ route('admin.users.update', $user->uid) }}"
            method="post">
            @method('PATCH')
            @csrf
            <div class="row">

                <div class="col-12 col-sm-6">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="mb-1">
                                <label for="name" class="form-label required">{{ __('locale.labels.name') }}</label>
                                <input type="text" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ $user->name }}"
                                    name="name" required>
                                @error('name')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-1">
                                <label for="email"
                                    class="form-label required">{{ __('locale.labels.email') }}</label>
                                <input type="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ $user->email }}" name="email" required>
                                @error('email')
                                    <p><small class="text-danger">{{ $message }}</small></p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <label for="locale"
                                    class="required form-label">{{ __('locale.labels.role') }}</label>
                                <select class="select2 form-select" id="role_id" name="role_id">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ $user->role->name == $role->name ? 'selected' : null }}>
                                            {{ __('locale.labels.' . $role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role_id')
                                <p><small class="text-danger">{{ $message }}</small></p>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="col-12 col-sm-6">

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label required"
                                    for="password">{{ __('locale.labels.password') }}</label>
                                <div class="input-group input-group-merge form-password-toggle">
                                    <input type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        value="{{ old('password') }}" name="password" />
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>

                                @if ($errors->has('password'))
                                    <p><small class="text-danger">{{ $errors->first('password') }}</small></p>
                                @else
                                    <p><small class="text-primary"> {{ __('locale.user.leave_blank_password') }}
                                        </small>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1">
                                <label class="form-label required"
                                    for="password_confirmation">{{ __('locale.labels.password_confirmation') }}</label>
                                <div class="input-group input-group-merge form-password-toggle">
                                    <input type="password" id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        value="{{ old('password_confirmation') }}" name="password_confirmation" />
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                    <button type="submit" class="btn btn-primary mt-1 me-1"><i data-feather="save"></i>
                        {{ __('locale.buttons.save_changes') }}</button>
                </div>

            </div>
        </form>
        <!--/ form -->
    </div>
</div>
