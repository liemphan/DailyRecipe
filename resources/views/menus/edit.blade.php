@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $menu,
                $menu->getUrl('/edit') => [
                    'text' => trans('entities.menus_edit'),
                    'icon' => 'edit',
                ]
            ]])
        </div>

        <main class="card content-wrap margin-settings">
            <h1 class="list-heading">{{ trans('entities.menus_edit') }}</h1>
            <form action="{{ $menu->getUrl() }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                @include('menus.parts.form', ['model' => $menu])
            </form>
        </main>
    </div>

@stop