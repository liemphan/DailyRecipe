@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $recipe,
                $recipe->getUrl('/edit') => [
                    'text' => trans('entities.recipes_edit'),
                    'icon' => 'edit',
                ]
            ]])
        </div>

        <main class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.recipes_edit') }}</h1>
            <form action="{{ $recipe->getUrl() }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                @include('recipes.parts.form', ['model' => $recipe, 'returnLocation' => $recipe->getUrl()])
            </form>
        </main>
    </div>
@stop