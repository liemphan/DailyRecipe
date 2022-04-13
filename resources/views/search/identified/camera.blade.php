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

<main class="content-wrap mt-m card">
<h1 class="list-heading">Identify cooking ingredients</h1><br>
{{--<a href=https://anonystick.com/blog-developer/su-dung-javascript-truy-cap-camera-sau-truoc-va-chup-man-minh-thiet-bi-di-dong-2020061162339379>&#8592;--}}
{{--    Trở về với bài viết</a><br>--}}
{{--<div class=btns>--}}
{{--    <button class="button is-hidden" id=btnPlay>Play camera</button>--}}
{{--    <button class=button id=btnPause>Stop camera</button>--}}
{{--    <button class=button id=btnScreenshot>Chụp hình</button>--}}
{{--    <button class=button id=btnChangeCamera style="padding: 6px 10px;">Đổi camera</button>--}}
{{--</div>--}}

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    ch1>Demo file upload to Deep Learning API</hl>
    <input id="sortpicture" type="file" name="sortpic" />
    <button id="upload">Upload</button>





</main>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
            <a class="icon-list-item is-hidden" id=btnPlay>Play Camera</a>
            <a class=icon-list-item id=btnPause>Stop Camera</a>
            <a class=icon-list-item id=btnScreenshot>Take a Photo</a>
            <a class=icon-list-item id=btnChangeCamera>Change Camera</a>
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

