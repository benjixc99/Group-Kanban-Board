@extends('layouts/contentLayoutMaster')

@section('title', 'Task #' . $project->uid)

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection

@section('page-style')
    <style>
        .max-h-60 {
            max-height: 60vh !important;
        }
    </style>
@endsection
@section('content')
    <section class="">
        <div class="row">
            <div class="col-12 col-sm-8 mb-3 mt-2">
                <div class="d-flex justify-content-between items-center">
                    <h1>{{ $task->name }}</h1>
                    @can('edit_task_details')
                        <a href={{ route('admin.tasks.edit', ['project' => $project->uid, 'task' => $task->uid]) }}
                            class="btn btn-secondary waves-light waves-effect fw-bold mx-1">
                            {{ __('locale.buttons.edit') }} <i data-feather="edit"></i></a>
                    @endcan
                </div>
                <p>{{ $task->description }}</p>
                <div class="d-flex justify-content-between">
                    @can('request_task_escalation')
                        @include('admin.projects.tasks._request_escalation')
                    @endcan
                    @can('log_task_progress')
                        @include('admin.projects.tasks._log_progress')
                    @endcan
                </div>
                <div class="row mt-3">

                    <div class="col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="fw-light mb-0">
                                        {{ $task->priority->name }}</h3>
                                    <p class="card-text text-secondary">{{ __('locale.labels.priority') }}</p>
                                </div>
                                <div class="avatar bg-light-info p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="alert-circle" class="text-info font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="fw-light text-small mb-0">
                                        {{ $task->status->name }}
                                    </h3>
                                    <p class="card-text text-secondary">{{ __('locale.labels.status') }}</p>
                                </div>
                                <div class="avatar bg-light-success p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="flag" class="text-success font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-6 col-12">
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="fw-light mb-0">
                                        {{ \App\Library\Tool::formatHoursForHumans($task->estimate) }}
                                    </h3>
                                    <p class="card-text text-secondary">{{ __('locale.labels.effort_required') }}</p>
                                </div>
                                <div class="avatar bg-light-warning p-50 m-0">
                                    <div class="avatar-content">
                                        <i data-feather="clock" class="text-warning font-medium-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($task->assignee)
                        <div class="col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div>
                                        <h3 class="fw-bolder mb-0"> {{ $task->assignee->name }}</h3>
                                        <p class="card-text text-secondary">{{ __('locale.labels.assignee') }}</p>
                                    </div>
                                    <div class="avatar bg-light-warning p-50 m-0">
                                        <div class="avatar-content">
                                            <i data-feather="user" class="text-warning font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="row mt-3">

                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-end">
                                <h4 class="card-title">{{ __('locale.labels.progress') }}</h4>
                            </div>

                            <div class="card-body p-0">
                                <div id="progress-chart" class="my-2"></div>
                            </div>
                            <div class="row border-top text-center mx-0">
                                <div class="col-6 border-end py-1">
                                    <p class="card-text text-muted mb-0">{{ __('locale.labels.total') }}</p>
                                    <h3 class="fw-bolder mb-0">
                                        {{ $task->estimate }} {{ __('locale.labels.hours') }}
                                    </h3>
                                </div>
                                <div class="col-6 py-1">
                                    <p class="card-text text-muted mb-0">{{ __('locale.labels.remaining') }}</p>
                                    <h3 class="fw-bolder mb-0">
                                        {{ $task->estimate - $task->progress }} {{ __('locale.labels.hours') }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-end">
                                <h4 class="card-title">{{ __('locale.labels.remaining_work') }}</h4>
                            </div>

                            <div class="card-body p-0">
                                <div id="remaining-work-chart" class="my-2"></div>
                            </div>
                            <div class="row border-top text-center mx-0">
                                <div class="col-6 border-end py-1">
                                    <p class="card-text text-muted mb-0">{{ __('locale.labels.total_done') }}</p>
                                    <h3 class="fw-bolder mb-0">
                                        {{ $task->progress }} {{ __('locale.labels.hours') }}
                                    </h3>
                                </div>
                                <div class="col-6 py-1">
                                    <p class="card-text text-muted mb-0">{{ __('locale.labels.remaining') }}</p>
                                    <h3 class="fw-bolder mb-0">
                                        {{ $task->estimate - $task->progress }} {{ __('locale.labels.hours') }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12 col-sm-4">
                <div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3>Comments</h3>
                    </div>
                    <div class="max-h-60 overflow-y-scroll">
                        @foreach ($task->comments as $comment)
                            <div class="card mb-1 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center my-1">
                                        <div class="avatar me-1">
                                            <img src="{{ route('admin.users.avatar', $comment->user->uid) }}"
                                                alt="Avatar" width="32" height="32">
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate fw-bold">{{ $comment->user->name }}</span>
                                            <small
                                                class="emp_post text-truncate text-muted">{{ $comment->user->email }}</small>
                                        </div>
                                        @can('manage_own_comments')
                                            @if ($comment->user->id == auth()->user()->id)
                                                <button class="btn btn-sm btn-info waves-light waves-effect fw-bold mx-1"><i
                                                        data-feather="edit" data-bs-toggle="modal"
                                                        data-bs-target="#updateComment"></i></button>
                                                <a href={{ route('admin.comment.delete', ['project' => $project->uid, 'task' => $task->uid, 'comment' => $comment->id]) }}
                                                    class="btn btn-sm btn-danger waves-light waves-effect fw-bold mx-1"><i
                                                        data-feather="x-circle"></i></a>


                                                {{-- Modal --}}
                                                <div class="modal fade text-left" id="updateComment" tabindex="-1"
                                                    role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                        role="document">
                                                        <div class="modal-content">

                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel33">
                                                                    {{ __('locale.labels.update_comment') }}</h4>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>

                                                            <div class="modal-body">
                                                                <form class="form form-vertical"
                                                                    action="{{ route('admin.comment.edit', ['project' => $project->uid, 'task' => $task->uid, 'comment' => $comment->id]) }}"
                                                                    method="GET">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="mb-1">
                                                                                <textarea class="form-control" rows="4" required name="comment">{{ $comment->comment }}</textarea>
                                                                                @error('comment')
                                                                                    <p><small
                                                                                            class="text-danger">{{ $message }}</small>
                                                                                    </p>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <input type="hidden" name="sms_type"
                                                                                value="plain">
                                                                            <button type="submit"
                                                                                class="btn btn-primary mr-1 mb-1 float-end">
                                                                                <i data-feather="send"></i>
                                                                                {{ __('locale.buttons.update') }}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endcan
                                    </div>
                                    <div class="col-12">{{ $comment->comment }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form class="form form-vertical"
                        action="{{ route('admin.comment.add', ['project' => $project->uid, 'task' => $task->uid]) }}"
                        method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-1">
                                    <label for="comment" class="required">{{ __('locale.labels.add_comment') }}</label>
                                    <textarea class="form-control" rows="4" required name="comment">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <p><small class="text-danger">{{ $message }}</small> </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="hidden" name="sms_type" value="plain">
                                <button type="submit" class="btn btn-primary mr-1 mb-1 float-end">
                                    <i data-feather="send"></i> {{ __('locale.buttons.send') }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

        </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {

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
                    dropdownParent: $this.parent(),
                    scrollAfterSelect: true,
                });
            });
        });

        let $primary = '#7367F0';
        let $success = '#00db89';
        let $strok_color = '#b9c3cd';
        let $label_color = '#e7eef7';
        let $purple = '#df87f2';

        let estimate = "{{ $task->estimate }}";
        let progress = "{{ $task->progress }}";

        let remainingWork = estimate - progress;
        let maxProgressPercent = (progress / estimate) * 100;
        let maxRemainingWorkPercent = (remainingWork / estimate) * 100;

        // contact list  Chart
        // -----------------------------

        let contactListChartoptions = {
            chart: {
                height: 245,
                type: 'radialBar',
                sparkline: {
                    enabled: true,
                },
                dropShadow: {
                    enabled: true,
                    blur: 3,
                    left: 1,
                    top: 1,
                    opacity: 0.1
                },
            },
            colors: [$success],
            plotOptions: {
                radialBar: {
                    offsetY: -10,
                    startAngle: -150,
                    endAngle: 150,
                    hollow: {
                        size: '60%'
                    },
                    track: {
                        background: $strok_color,
                        strokeWidth: '50%',
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: 18,
                            color: $strok_color,
                            fontSize: '3rem'
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#00b5b5'],
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                },
            },
            series: [parseFloat(maxProgressPercent).toFixed(1)],
            stroke: {
                lineCap: 'round'
            },
            grid: {
                padding: {
                    bottom: 30
                }
            }
        }

        let contactListChart = new ApexCharts(
            document.querySelector("#progress-chart"),
            contactListChartoptions
        );

        contactListChart.render();


        // Remaining Work Chart
        // -----------------------------

        let contactChartoptions = {
            chart: {
                height: 245,
                type: 'radialBar',
                sparkline: {
                    enabled: true,
                },
                dropShadow: {
                    enabled: true,
                    blur: 3,
                    left: 1,
                    top: 1,
                    opacity: 0.1
                },
            },
            colors: [$success],
            plotOptions: {
                radialBar: {
                    offsetY: -10,
                    startAngle: -150,
                    endAngle: 150,
                    hollow: {
                        size: '60%'
                    },
                    track: {
                        background: $strok_color,
                        strokeWidth: '50%',
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: 18,
                            color: $strok_color,
                            fontSize: '3rem'
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'horizontal',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#00b5b5'],
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                },
            },
            series: [parseFloat(maxRemainingWorkPercent).toFixed(1)],
            stroke: {
                lineCap: 'round'
            },
            grid: {
                padding: {
                    bottom: 30
                }
            }
        }

        let contactChart = new ApexCharts(
            document.querySelector("#remaining-work-chart"),
            contactChartoptions
        );

        contactChart.render();
    </script>
@endsection
