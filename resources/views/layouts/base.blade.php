<!DOCTYPE html>
<html lang="{{ config('app.lang') }}"
      dir="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
      class="{{ setting()->getForCurrentUser('dark-mode-enabled') ? 'dark-mode ' : '' }}@yield('body-class')">
<head>
    <title>{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}</title>

    <!-- Meta -->
    <meta name="viewport" content="width=device-width">
    <meta name="token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <meta charset="utf-8">

    <!-- Social Cards Meta -->
    <meta property="og:title" content="{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @stack('social-meta')

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">

    @yield('head')

    <!-- Custom Styles & Head Content -->
    @include('common.custom-styles')
    @include('common.custom-head')

    @stack('head')

    <!-- Translations for JS -->
    @stack('translations')
</head>
<body class="@yield('body-class')">

    @include('common.skip-to-content')
    @include('common.notifications')
    @include('common.header')

    <div id="content" components="@yield('content-components')" class="block">
        @yield('content')
    </div>

    @include('common.footer')
    <div class="text-right">
        <nav refs="header-mobile-toggle@menu" class="header-links hide-over-l main-menu nav-tabs tri-layout-mobile-tab">
        <div class="links text-center">
            @if (hasAppAccess())
                @if(userCanOnAny('view', \DailyRecipe\Entities\Models\Recipemenu::class) || userCan('recipemenu-view-all') || userCan('recipemenu-view-own'))
{{--                    <a href="{{ url('/menus') }}">@icon('recipemenu')<br>{{ trans('entities.menus') }}</a>--}}
                    <a class="avatar items-center" href="{{ url('/menus') }}">@icon('recipemenu')</a>
                @endif

{{--                <a href="{{ url('/recipes') }}">@icon('recipes')<br>{{ trans('entities.recipes') }}</a>--}}
                <a class="avatar items-center"  href="{{ url('/recipes') }}">@icon('recipes')</a>

{{--                <a href="{{ url('/search/identified/ingredients') }}">@icon('recipes')<br>Identified</a>--}}
                <a class="avatar items-center"  href="{{ url('/search/identified/ingredients') }}">@icon('camera')</a>

                @if(signedInUser() && userCan('settings-manage'))
{{--                    <a href="{{ url('/settings') }}">@icon('settings')<br>{{ trans('settings.settings') }}</a>--}}
                    <a class="avatar items-center"  href="{{ url('/settings') }}">@icon('settings')</a>
                @endif

                @if(signedInUser() && userCan('users-manage') && !userCan('settings-manage'))
{{--                    <a href="{{ url('/settings/users') }}">@icon('users')<br>{{ trans('settings.users') }}</a>--}}
                    <a class="avatar items-center"  href="{{ url('/settings/users') }}">@icon('users')</a>
                @endif

                @if(!signedInUser())
                    @if(setting('registration-enabled') && config('auth.method') === 'standard')
                        <a href="{{ url('/register') }}">@icon('new-user'){{ trans('auth.sign_up') }}</a>
                    @endif
                    <a href="{{ url('/login')  }}">@icon('login'){{ trans('auth.log_in') }}</a>
                @endif

                @if(signedInUser())
                        <?php $currentUser = user(); ?>
                    <div class="dropup">
                        <a class="dropbtn">
                            <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}"><br>
                            <span class="name">{{ $currentUser->getShortName(9) }}</span></a>
                        <div class="dropup-content">
                            <a href="{{ url('/favourites') }}">@icon('star'){{ trans('entities.my_favourites') }}</a>
                            <a href="{{ $currentUser->getProfileUrl() }}">@icon('user'){{ trans('common.view_profile') }}</a>
                            <a href="{{ $currentUser->getEditUrl() }}">@icon('edit'){{ trans('common.edit_profile') }}</a>
                            <form action="{{ url(config('auth.method') === 'saml2' ? '/saml2/logout' : '/logout') }}"--}}
                                  method="post">
                                {{ csrf_field() }}
                                <button class="text-muted icon-list-item text-primary">
                                    @icon('logout'){{ trans('auth.logout') }}
                                </button>
                            </form>
                            @include('common.dark-mode-toggle')
                        </div>
                    </div>
                @endif
{{--                        <div class="dropdown-container" component="dropdown" option:dropdown:bubble-escapes="true"><span refs="dropup@toggle">@icon('login'){{ trans('auth.log_in') }}</span>--}}
{{--                            <ul refs="dropup@menu" class="dropdown-menu" role="menu">--}}
{{--                                <li>--}}
{{--                                    <a href="{{ url('/favourites') }}">@icon('star'){{ trans('entities.my_favourites') }}</a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a href="{{ $currentUser->getProfileUrl() }}">@icon('user'){{ trans('common.view_profile') }}</a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <a href="{{ $currentUser->getEditUrl() }}">@icon('edit'){{ trans('common.edit_profile') }}</a>--}}
{{--                                </li>--}}
{{--                                <li>--}}
{{--                                    <form action="{{ url(config('auth.method') === 'saml2' ? '/saml2/logout' : '/logout') }}"--}}
{{--                                          method="post">--}}
{{--                                        {{ csrf_field() }}--}}
{{--                                        <button class="text-muted icon-list-item text-primary">--}}
{{--                                            @icon('logout'){{ trans('auth.logout') }}--}}
{{--                                        </button>--}}
{{--                                    </form>--}}
{{--                                </li>--}}
{{--                                <li><hr></li>--}}
{{--                                <li>--}}
{{--                                    @include('common.dark-mode-toggle')--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                        </div>--}}
{{--                    @endif--}}
            @endif
        </div>

        </nav>
    </div>
    <div back-to-top class="primary-background print-hidden">
        <div class="inner">
            @icon('chevron-up') <span>{{ trans('common.back_to_top') }}</span>
        </div>
    </div>

    @yield('bottom')
    <script src="{{ versioned_asset('dist/app.js') }}" nonce="{{ $cspNonce }}"></script>
    @yield('scripts')

</body>
</html>
