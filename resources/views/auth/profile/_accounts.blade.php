<div class="card">
    <div class="card-body py-2 my-25">
        <!-- header section -->
        <div class="d-flex">
            <a href="{{ route('user.account') }}" class="me-25">
                <img src="{{ route('user.avatar') }}" alt="{{ $user->displayName() }}" class="uploadedAvatar rounded me-50"
                    height="100" width="100" />
            </a>
            <!-- upload and reset button -->
            <div class="d-flex align-items-end mt-75 ms-1">
                <div>
                    @include('auth.profile._update_avatar')
                    <button id="remove-avatar" data-id="{{ $user->uid }}"
                        class="btn btn-sm btn-danger mb-75 me-75"><i data-feather="trash-2"></i>
                        {{ __('locale.labels.remove') }}</button>
                    <p class="mb-0"> {{ __('locale.customer.profile_image_size') }} </p>
                </div>
            </div>
            <!--/ upload and reset button -->
        </div>
        <!--/ header section -->

        <!-- form -->
        <form class="form form-vertical mt-2 pt-50" action="{{ route('user.account.update') }}" method="post">
            @method('PATCH')
            @csrf
            <div class="row">

                <div class="ol-12 col-sm-6">
                    <div class="mb-1">
                        <label for="email" class="form-label required">{{ __('locale.labels.email') }}</label>
                        <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ $user->email }}" name="email" required>
                        @error('email')
                            <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="mb-1">
                        <label for="name" class="form-label required">{{ __('locale.labels.name') }}</label>
                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ $user->name }}" name="name" required>
                        @error('name')
                            <p><small class="text-danger">{{ $message }}</small></p>
                        @enderror
                    </div>
                </div>

                <div class="col-12 d-flex flex-sm-row flex-column justify-content-start mt-1">
                    <button type="submit" class="btn btn-primary mt-1 me-1"><i data-feather="save"></i>
                        {{ __('locale.buttons.save_changes') }}</button>
                </div>

            </div>
        </form>
        <!--/ form -->
    </div>
</div>
