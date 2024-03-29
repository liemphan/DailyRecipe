@extends('layouts.tri')

@section('body')
    @include('menus.parts.list', ['menus' => $menus, 'view' => $view])
@stop

@section('left')
    @include('home.parts.sidebar')
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
            @if(user()->can('recipemenu-create-all'))
                <a href="{{ url("/create-menu") }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.menus_new_action') }}</span>
                </a>
            @endif
            @include('entities.view-toggle', ['view' => $view, 'type' => 'menus'])
            @include('home.parts.expand-toggle', ['classes' => 'text-primary', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
            @include('common.dark-mode-toggle', ['classes' => 'icon-list-item text-primary'])
        </div>
    </div>
@stop
