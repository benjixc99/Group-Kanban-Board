@extends('layouts.contentLayoutMaster')

@section('title', $user->displayName())

@section('vendor-style')
    {{-- Page Css files --}}

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@endsection

@section('content')
    <!-- users edit start -->
    <section class="users-edit">

        <ul class="nav nav-pills mb-2" role="tablist">

            <li class="nav-item">
                <a class="nav-link @if ((old('tab') == 'account' || old('tab') == null) && request()->input('tab') == null) active @endif" id="account-tab" data-bs-toggle="tab" href="#account" aria-controls="account" role="tab" aria-selected="true">
                    <i data-feather="user"></i> {{__('locale.labels.account')}}
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ old('tab') == 'security' ? 'active':null }}" id="security-tab" data-bs-toggle="tab" href="#security" aria-controls="security" role="tab" aria-selected="true">
                    <i data-feather="lock"></i> {{__('locale.labels.security')}}
                </a>
            </li>

        </ul>


        <div class="tab-content">

            <div class="tab-pane @if ((old('tab') == 'account' || old('tab') == null) && request()->input('tab') == null) active @endif" id="account" aria-labelledby="account-tab" role="tabpanel">
                <!-- users edit account form start -->
                @include('auth.profile._accounts')
                <!-- users edit account form ends -->
            </div>

            <div class="tab-pane {{ old('tab') == 'security' ? 'active':null }}" id="security" aria-labelledby="security-tab" role="tabpanel">
                <!-- users edit Info form start -->
                @include('auth.profile._security')
                <!-- users edit Info form ends -->
            </div>

        </div>
    </section>
    <!-- users edit ends -->
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

@endsection

@section('page-script')
    {{-- Page js files --}}

    <script>

        $(document).ready(function () {
            "use strict"

            let firstInvalid = $('form').find('.is-invalid').eq(0);

            if (firstInvalid.length) {
                $('body, html').stop(true, true).animate({
                    'scrollTop': firstInvalid.offset().top - 200 + 'px'
                }, 200);
            }

            // Basic Select2 select
            $(".select2").each(function () {
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

            //show response message
            function showResponseMessage(data) {

                if (data.status === 'success') {
                    toastr['success'](data.message, '{{__('locale.labels.success')}}!!', {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                    });
                    dataListView.draw();
                } else {
                    toastr['warning']("{{__('locale.exceptions.something_went_wrong')}}", '{{ __('locale.labels.warning') }}!', {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                    });
                }
            }

            // On Remove Avatar
            $('#remove-avatar').on("click", function (e) {

                e.stopPropagation();
                Swal.fire({
                    title: "{{ __('locale.labels.are_you_sure') }}",
                    text: "{{ __('locale.labels.able_to_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: "{{ __('locale.labels.delete_it') }}",
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ms-1'
                    },
                    buttonsStyling: false,

                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('user.remove_avatar') }}",
                            type: "POST",
                            data: {
                                _method: 'POST',
                                _token: "{{csrf_token()}}"
                            },
                            success: function (data) {
                                showResponseMessage(data);
                                setTimeout(function () {
                                    location.reload();
                                }, 5000);
                            },
                            error: function (reject) {
                                if (reject.status === 422) {
                                    let errors = reject.responseJSON.errors;
                                    $.each(errors, function (key, value) {
                                        toastr['warning'](value[0], "{{__('locale.labels.attention')}}", {
                                            closeButton: true,
                                            positionClass: 'toast-top-right',
                                            progressBar: true,
                                            newestOnTop: true,
                                        });
                                    });
                                } else {
                                    toastr['warning'](reject.responseJSON.message, "{{__('locale.labels.attention')}}", {
                                        positionClass: 'toast-top-right',
                                        containerId: 'toast-top-right',
                                        progressBar: true,
                                        closeButton: true,
                                        newestOnTop: true
                                    });
                                }
                            }
                        })
                    }
                })
            });

        });
    </script>

@endsection
