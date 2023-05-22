<!-- BEGIN: Vendor JS-->
<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset(mix('vendors/js/ui/jquery.sticky.js')) }}"></script>
@yield('vendor-script')
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>

<!-- custom scripts file for user -->
<script src="{{ asset(mix('js/core/scripts.js')) }}"></script>

<!-- END: Theme JS-->


<script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

{{-- page script --}}
@yield('page-script')
<script>
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });
</script>

@if (session()->has('message'))
    <script>
        let type = "{{ Session::get('status', 'success') }}";
        switch (type) {
            case 'info':
                toastr['info']("{{ Session::get('message') }}", '{{ __('locale.labels.information') }}!', {
                    closeButton: true,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    newestOnTop: true,
                });

                break;

            case 'warning':
                toastr['warning']("{{ Session::get('message') }}", '{{ __('locale.labels.warning') }}!', {
                    closeButton: true,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    newestOnTop: true,
                });
                break;

            case 'success':
                toastr['success']("{{ Session::get('message') }}", '{{ __('locale.labels.success') }}!!', {
                    closeButton: true,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    newestOnTop: true,
                });
                break;

            case 'error':
                toastr['error']("{{ Session::get('message') }}", '{{ __('locale.labels.ops') }}..!!', {
                    closeButton: true,
                    positionClass: 'toast-top-right',
                    progressBar: true,
                    newestOnTop: true,
                });
                break;
        }
    </script>
@endif
