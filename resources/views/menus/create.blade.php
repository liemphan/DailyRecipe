@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                '/menus' => [
                    'text' => trans('entities.menus'),
                    'icon' => 'recipemenu',
                ],
                '/create-menu' => [
                    'text' => trans('entities.menus_create'),
                    'icon' => 'add',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.menus_create') }}</h1>
            <form action="{{ url("/menus") }}" method="POST" enctype="multipart/form-data">
                @include('menus.parts.form', ['menu' => null, 'recipes' => $books])
            </form>
        </main>

    </div>

@stop