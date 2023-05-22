@extends('layouts/fullLayoutMaster')

@section('title', __('locale.auth.register'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
    <div class="auth-wrapper auth-cover">
        <div class="auth-inner row m-0">
            <!-- Left Text-->
            <div class="col-lg-3 d-none d-lg-flex align-items-center p-0">
                <div class="w-100 d-lg-flex align-items-center justify-content-center">
                    <img class="img-fluid w-100" src="{{ asset('images/pages/create-account.png') }}"
                        alt="{{ config('app.name') }}" />
                </div>
            </div>
            <!-- /Left Text-->

            <!-- Register-->
            <div class="col-lg-9 d-flex align-items-center auth-bg px-2 px-sm-3 px-lg-5 pt-3">
                <div class="width-700 mx-auto">

                    <div class="px-0 mt-4">

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-danger" role="alert">
                                    <div class="alert-body">{{ $error }}</div>
                                </div>
                            @endforeach
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div id="account-details" class="content get_form_data" role="tabpanel"
                                aria-labelledby="account-details-trigger">
                                <div class="content-header mb-2">
                                    <h2 class="fw-bolder mb-75">{{ __('locale.auth.account_information') }}</h2>
                                    <span>{{ __('locale.auth.create_new_account') }}</span>
                                </div>

                                <div class="row">
                                    <div class="mb-1 col-12">
                                        <label class="form-label required"
                                            for="name">{{ __('locale.labels.name') }}</label>
                                        <input id="name" type="text"
                                            class="form-control @error('name') is-invalid @enderror" name="name"
                                            placeholder="{{ __('locale.labels.name') }}" value="{{ old('name') }}"
                                            required autocomplete="name" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>

                                    <div class="col-12 mb-1">
                                        <label class="form-label required"
                                            for="email">{{ __('locale.labels.email') }}</label>
                                        <input type="email" id="email"
                                            class="form-control required @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" name="email" required />

                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label class="form-label required"
                                            for="password">{{ __('locale.labels.password') }}</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" id="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                value="{{ old('password') }}" name="password" required />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>

                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-1">
                                        <label class="form-label required"
                                            for="password_confirmation">{{ __('locale.labels.password_confirmation') }}</label>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input type="password" id="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                value="{{ old('password_confirmation') }}" name="password_confirmation"
                                                required />
                                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                    </div>

                                    <p class="mt-1 mb-1">
                                        <a href="{{ url('login') }}">
                                            <i data-feather="chevron-left"></i> {{ __('locale.auth.back_to_login') }}
                                        </a>
                                    </p>

                                    <div class="d-flex justify-content-between mt-1">
                                        <button class="btn btn-success btn-submit" type="submit">
                                            <i data-feather="check" class="align-middle me-sm-25 me-0"></i>
                                            <span class="align-middle d-sm-inline-block d-none">Submit</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection