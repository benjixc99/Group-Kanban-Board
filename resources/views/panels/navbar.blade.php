@if ($configData['mainLayoutType'] == 'horizontal' && isset($configData['mainLayoutType']))
    <nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center {{ $configData['navbarColor'] }}"
        data-nav="brand-center">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav">
                @if (Auth::user()->isCustomer)
                    <li class="nav-item"><a class="navbar-brand" href="{{ route('user.home') }}">
                            <span class="brand-logo"><img src="{{ asset(config('app.logo')) }}" alt="app logo" /></span>
                        </a>
                    </li>
                @else
                    <li class="nav-item"><a class="navbar-brand" href="{{ route('admin.home') }}">
                            <span class="brand-logo"><img src="{{ asset(config('app.logo')) }}" alt="app logo" /></span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    @else
        <nav
            class="header-navbar navbar navbar-expand-lg align-items-center {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }} {{ $configData['layoutWidth'] === 'boxed' && $configData['verticalMenuNavbarType'] === 'navbar-floating' ? 'container-xxl' : '' }}">
@endif


<div class="navbar-container d-flex content">
    <div class="bookmark-wrapper d-flex align-items-center">
        <ul class="nav navbar-nav d-xl-none">
            <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon"
                        data-feather="menu"></i></a></li>
        </ul>
    </div>


    <ul class="nav navbar-nav align-items-center ms-auto">


        <li class="dropdown dropdown-user nav-item">
            <a class="dropdown-toggle nav-link dropdown-user-link" id="dropdown-user" href="javascript:void(0);"
                data-bs-toggle="dropdown" aria-haspopup="true">
                <div class="user-nav d-sm-flex d-none">
                    <span class="user-name fw-bolder">
                        @if (Auth::check())
                            {{ Auth::user()->displayName() }}
                        @else
                            {{ config('app.name') }}
                        @endif
                    </span>
                    <span class="user-status">{{ __('locale.labels.available') }}</span>
                </div>
                <span class="avatar">
                    <img class="round" src="{{ route('user.avatar', Auth::user()->uid) }}"
                        alt="{{ config('app.name') }}" height="40" width="40" />
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">



                <h6 class="dropdown-header">{{ __('locale.labels.manage_profile') }}</h6>
                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="{{ route('user.account') }}"><i class="me-50"
                        data-feather="user"></i>{{ __('locale.labels.profile') }}</a>


                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="me-50"
                        data-feather="power"></i> {{ __('locale.menu.Logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </div>
        </li>
    </ul>
</div>

</nav>
<!-- END: Header-->
