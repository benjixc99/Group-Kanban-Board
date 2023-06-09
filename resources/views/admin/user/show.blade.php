@extends('layouts/contentLayoutMaster')

@section('title', $user->displayName())
@section('vendor-style')
    <!-- vendor css files -->
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    <link rel='stylesheet' href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
@endsection


@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
@endsection

@section('content')

    <section class="users-edit">

        <div class="row">
            <div class="col-12">

                <ul class="nav nav-pills mb-2" role="tablist">
                    <!-- Account -->
                    <li class="nav-item">
                        <a class="nav-link @if (old('tab') == 'account' || old('tab') == null) active @endif" id="account-tab"
                            data-bs-toggle="tab" href="#account" aria-controls="account" role="tab"
                            aria-selected="true">
                            <i data-feather="user" class="font-medium-3 me-50"></i>
                            <span class="fw-bold">{{ __('locale.labels.account') }}</span>
                        </a>
                    </li>

                </ul>


                <div class="tab-content">

                    <div class="tab-pane  @if (old('tab') == 'account' || old('tab') == null) active @endif" id="account"
                        aria-labelledby="account-tab" role="tabpanel">
                        <!-- users edit account form start -->
                        @include('admin.user._account')
                        <!-- users edit account form ends -->

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection


@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset('js/scripts/components/components-navs.js') }}"></script>

    <script>
        $(document).ready(function() {
            "use strict"

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


            //show response message
            function showResponseMessage(data) {
                if (data.status === 'success') {
                    toastr['success'](data.message, '{{ __('locale.labels.success') }}!!', {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                    });
                } else {
                    toastr['warning']("{{ __('locale.exceptions.something_went_wrong') }}",
                        '{{ __('locale.labels.warning') }}!', {
                            closeButton: true,
                            positionClass: 'toast-top-right',
                            progressBar: true,
                            newestOnTop: true,
                        });
                }
            }


            // On Remove Avatar
            $('#remove-avatar').on("click", function(e) {

                e.stopPropagation();
                let id = $(this).data('id');
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

                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ url(config('app.admin_path') . '/users') }}" + '/' +
                                id + '/remove-avatar',
                            type: "POST",
                            data: {
                                _method: 'POST',
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data) {
                                showResponseMessage(data);
                                setTimeout(function() {
                                    location.reload();
                                }, 5000);
                            },
                            error: function(reject) {
                                if (reject.status === 422) {
                                    let errors = reject.responseJSON.errors;
                                    $.each(errors, function(key, value) {
                                        toastr['warning'](value[0],
                                            "{{ __('locale.labels.attention') }}", {
                                                closeButton: true,
                                                positionClass: 'toast-top-right',
                                                progressBar: true,
                                                newestOnTop: true,
                                            });
                                    });
                                } else {
                                    toastr['warning'](reject.responseJSON.message,
                                        "{{ __('locale.labels.attention') }}", {
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
