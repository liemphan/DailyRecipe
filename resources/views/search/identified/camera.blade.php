{{--<!DOCTYPE html>--}}
{{--<html lang=en>--}}
{{--<head>--}}
{{--    <meta charset=UTF-8>--}}
{{--    <meta name=viewport content="width=device-width,initial-scale=1">--}}
{{--    <meta http-equiv=X-UA-Compatible content="ie=edge">--}}
{{--    <title>Cách sử dụng JavaScript để truy cập camera của thiết bị (trước và sau)</title>--}}
@extends('layouts.tri')

@section('body')
@yield('style')
{{--</head>--}}
{{--<body>--}}

<main class="content-wrap card margin-32">
<h1 class="list-heading">Identify cooking ingredients</h1><br>
{{--<a href=https://anonystick.com/blog-developer/su-dung-javascript-truy-cap-camera-sau-truoc-va-chup-man-minh-thiet-bi-di-dong-2020061162339379>&#8592;--}}
{{--    Trở về với bài viết</a><br>--}}
{{--<div class=btns>--}}
{{--    <button class="button is-hidden" id=btnPlay>Play camera</button>--}}
{{--    <button class=button id=btnPause>Stop camera</button>--}}
{{--    <button class=button id=btnScreenshot>Chụp hình</button>--}}
{{--    <button class=button id=btnChangeCamera style="padding: 6px 10px;">Đổi camera</button>--}}
{{--</div>--}}
    <div class="items-center big-video">
<div class=video-screenshot>
    <video class="big-video" autoplay id=video></video>
{{--    <div>--}}
{{--        <div id=screenshotsContainer>--}}
{{--            <canvas id=canvas class=is-hidden></canvas>--}}
{{--        </div>--}}
{{--    </div>--}}
</div>

<div class="btns" >
{{--    <button class="button is-hidden" id=btnPlay1>Play Camera</button>--}}
{{--    <button class="button" id=btnPause1>Stop Camera</button>--}}
    <button class=flex-svg id=btnScreenshot1>@icon('shutter')</button>
{{--    <button class=button id=btnChangeCamera1>Change Camera</button>--}}
</div>
<div>

{{--    @yield('scripts1')--}}

    <ins class=adsbygoogle style="display:block; text-align:center;" data-ad-layout=in-article data-ad-format=fluid
         data-ad-client=ca-pub-1121308659421064 data-ad-slot=8232164616></ins>
    <script>(adsbygoogle = window.adsbygoogle || []).push({})</script>
    <div></div>
</div>
    </div>
</main>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
{{--            <a class="icon-list-item is-hidden" id=btnPlay>Play Camera</a>--}}
{{--            <a class=icon-list-item id=btnPause>Stop Camera</a>--}}
            <a class=icon-list-item id=btnScreenshot>Take a Photo</a>
{{--            <a class=icon-list-item id=btnChangeCamera>Change Camera</a>--}}
{{--            @if(user()->can('recipemenu-create-all'))--}}
{{--                <a href="{{ url("/create-menu") }}" class="icon-list-item">--}}
{{--                    <span>@icon('add')</span>--}}
{{--                    <span>{{ trans('entities.menus_new_action') }}</span>--}}
{{--                </a>--}}
{{--            @endif--}}
{{--            @include('entities.view-toggle', ['view' => $view, 'type' => 'menus'])--}}
{{--            @include('home.parts.expand-toggle', ['classes' => 'text-primary', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])--}}
{{--            @include('common.dark-mode-toggle', ['classes' => 'icon-list-item text-primary'])--}}
        </div>
    </div>
@stop

