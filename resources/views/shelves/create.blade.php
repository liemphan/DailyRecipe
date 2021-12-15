@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                '/shelves' => [
                    'text' => trans('entities.menus'),
                    'icon' => 'bookshelf',
                ],
                '/create-shelf' => [
                    'text' => trans('entities.menus_create'),
                    'icon' => 'add',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.menus_create') }}</h1>
            <form action="{{ url("/shelves") }}" method="POST" enctype="multipart/form-data">
                @include('shelves.parts.form', ['shelf' => null, 'books' => $books])
            </form>
        </main>

    </div>

@stop