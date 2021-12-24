@extends('layouts.simple')

@section('body')
    <div class="container small">
        <div class="my-s">
            @if (isset($recipemenu))
                @include('entities.breadcrumbs', ['crumbs' => [
                    $recipemenu,
                    $recipemenu->getUrl('/create-book') => [
                        'text' => trans('entities.recipes_create'),
                        'icon' => 'add'
                    ]
                ]])
            @else
                @include('entities.breadcrumbs', ['crumbs' => [
                    '/books' => [
                        'text' => trans('entities.recipes'),
                        'icon' => 'book'
                    ],
                    '/create-book' => [
                        'text' => trans('entities.recipes_create'),
                        'icon' => 'add'
                    ]
                ]])
            @endif
        </div>

        <main class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.recipes_create') }}</h1>
            <form action="{{ isset($recipemenu) ? $recipemenu->getUrl('/create-book') : url('/books') }}" method="POST" enctype="multipart/form-data">
                @include('books.parts.form', ['returnLocation' => isset($recipemenu) ? $recipemenu->getUrl() : url('/books')])
            </form>
        </main>
    </div>

@stop