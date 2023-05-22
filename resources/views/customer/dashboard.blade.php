@extends('layouts/contentLayoutMaster')

@section('title', __('locale.menu.Dashboard'))

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
@endsection
@section('page-style')
    {{-- Page css files --}}
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/dashboard-ecommerce.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/charts/chart-apex.css')) }}">
@endsection

@section('content')
    {{-- Dashboard Analytics Start --}}
    <section>

        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header"></div>
                    <div class="card-body">
                        <h3 class="text-primary">{{ \App\Helpers\Helper::greetingMessage() }}</h3>
                        <p class="font-medium-2 mt-2">
                            {{ __('locale.description.dashboard', ['brandname' => config('app.name')]) }}</p>
                    </div>
                </div>
            </div>

        </div>



    </section>
    <!-- Dashboard Analytics end -->
@endsection


@section('vendor-script')
    {{--     Vendor js files --}}
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection
