@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Users'))

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">

@endsection

@section('content')

    <!-- Basic table -->
    <section id="datatables-basic">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <table class="table datatables-basic">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>{{ __('locale.labels.id') }}</th>
                                <th>{{ __('locale.labels.name') }} </th>
                                <th>{{ __('locale.labels.role') }}</th>
                                <th>{{ __('locale.labels.actions') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!--/ Basic table -->


@endsection


@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/polyfill.min.js')) }}"></script>

@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $(document).ready(function() {
            "use strict"

            //show response message
            function showResponseMessage(data) {

                if (data.status === 'success') {
                    toastr['success'](data.message, '{{ __('locale.labels.success') }}!!', {
                        closeButton: true,
                        positionClass: 'toast-top-right',
                        progressBar: true,
                        newestOnTop: true,
                    });
                    dataListView.draw();
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

            // init table dom
            let Table = $("table");

            // init list view datatable
            let dataListView = $('.datatables-basic').DataTable({

                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('admin.users.search') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        _token: "{{ csrf_token() }}"
                    }
                },
                "columns": [{
                        "data": 'responsive_id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": "uid"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "email",
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": "role",
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": "action",
                        orderable: false,
                        searchable: false
                    }
                ],

                searchDelay: 1500,
                columnDefs: [{
                        // For Responsive
                        className: 'control',
                        orderable: false,
                        responsivePriority: 2,
                        targets: 0
                    },
                    {
                        // For Checkboxes
                        targets: 1,
                        orderable: false,
                        responsivePriority: 3,
                        render: function(data) {
                            return (
                                '<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="" id="' +
                                data +
                                '" /><label class="form-check-label" for="' +
                                data +
                                '"></label></div>'
                            );
                        },
                        checkboxes: {
                            selectAllRender: '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>',
                            selectRow: true
                        }
                    },
                    {
                        targets: 2,
                        visible: false
                    },
                    {
                        // Avatar image/badge, Name and post
                        targets: 3,
                        responsivePriority: 1,
                        render: function(data, type, full) {
                            var $user_img = full['avatar'],
                                $name = full['name'],
                                $created_at = full['created_at'],
                                $email = full['email'];
                            if ($user_img) {
                                // For Avatar image
                                var $output =
                                    '<img src="' + $user_img +
                                    '" alt="Avatar" width="32" height="32">';
                            } else {
                                // For Avatar badge
                                var stateNum = full['status'];
                                var states = ['success', 'danger', 'warning', 'info', 'dark',
                                    'primary', 'secondary'
                                ];
                                var $state = states[stateNum],
                                    $name = full['name'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || ''))
                                    .toUpperCase();
                                $output = '<span class="avatar-content">' + $initials + '</span>';
                            }

                            var colorClass = $user_img === '' ? ' bg-light-' + $state + ' ' : '';
                            // Creates full output for row
                            return '<div class="d-flex justify-content-left align-items-center">' +
                                '<div class="avatar ' +
                                colorClass +
                                ' me-1">' +
                                $output +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate fw-bold">' +
                                $name +
                                '</span>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $email +
                                '</small>' +
                                '<small class="emp_post text-truncate text-muted">' +
                                $created_at +
                                '</small>' +
                                '</div>' +
                                '</div>';
                        }
                    },
                    {
                        // Actions
                        targets: -1,
                        title: '{{ __('locale.labels.actions') }}',
                        orderable: false,
                        render: function(data, type, full) {
                            var $super_user = '';

                            if (full['super_user'] === false) {
                                $super_user =
                                    '<span class="action-delete text-danger pe-1 cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title=' +
                                    full['delete_label'] + ' data-id=' + full['delete'] + '>' +
                                    feather.icons['trash'].toSvg({
                                        class: 'font-medium-4'
                                    }) +
                                    '</span>';
                            }
                            return (
                                $super_user +

                                '<a href="' + full['show'] +
                                '" class="text-primary pe-1" data-bs-toggle="tooltip" data-bs-placement="top" title=' +
                                full['show_label'] + '>' +
                                feather.icons['edit'].toSvg({
                                    class: 'font-medium-4'
                                }) +
                                '</a>'
                            );
                        }
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

                language: {
                    paginate: {
                        // remove previous & next text from pagination
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    },
                    sLengthMenu: "_MENU_",
                    sZeroRecords: "{{ __('locale.datatables.no_results') }}",
                    sSearch: "{{ __('locale.datatables.search') }}",
                    sProcessing: "{{ __('locale.datatables.processing') }}",
                    sInfo: "{{ __('locale.datatables.showing_entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}"
                },
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function(row) {
                                let data = row.data();
                                return 'Details of ' + data['name'];
                            }
                        }),
                        type: 'column',
                        renderer: function(api, rowIdx, columns) {
                            let data = $.map(columns, function(col) {
                                return col.title !==
                                    '' // ? Do not show row in modal popup if title is blank (for check box)
                                    ?
                                    '<tr data-dt-row="' +
                                    col.rowIdx +
                                    '" data-dt-column="' +
                                    col.columnIndex +
                                    '">' +
                                    '<td>' +
                                    col.title +
                                    ':' +
                                    '</td> ' +
                                    '<td>' +
                                    col.data +
                                    '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');

                            return data ? $('<table class="table"/>').append('<tbody>' + data +
                                '</tbody>') : false;
                        }
                    }
                },
                aLengthMenu: [
                    [10, 20, 50, 100],
                    [10, 20, 50, 100]
                ],
                select: {
                    style: "multi"
                },
                order: [
                    [2, "desc"]
                ],
                displayLength: 10,
            });

            // On Delete
            Table.delegate(".action-delete", "click", function(e) {
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
                            url: "{{ url(config('app.admin_path') . '/users') }}" +
                                '/' +
                                id,
                            type: "POST",
                            data: {
                                _method: 'DELETE',
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(data) {
                                showResponseMessage(data);
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
