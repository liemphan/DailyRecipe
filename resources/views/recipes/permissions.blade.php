@extends('layouts.simple')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $recipe,
                $recipe->getUrl('/permissions') => [
                    'text' => trans('entities.recipes_permissions'),
                    'icon' => 'lock',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.recipes_permissions') }}</h1>
            @include('form.entity-permissions', ['model' => $recipe])
        </main>
    </div>

@stop
