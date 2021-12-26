@extends('layouts.simple')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $recipe,
                $recipe->getUrl('/sort') => [
                    'text' => trans('entities.recipes_sort'),
                    'icon' => 'sort',
                ]
            ]])
        </div>

        <div class="grid left-focus gap-xl">
            <div>
                <div recipe-sort class="card content-wrap">
                    <h1 class="list-heading mb-l">{{ trans('entities.recipes_sort') }}</h1>
                    <div recipe-sort-boxes>
                        @include('recipes.parts.sort-box', ['recipe' => $recipe, 'recipeChildren' => $recipeChildren])
                    </div>

                    <form action="{{ $recipe->getUrl('/sort') }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input recipe-sort-input type="hidden" name="sort-tree">
                        <div class="list text-right">
                            <a href="{{ $recipe->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                            <button class="button" type="submit">{{ trans('entities.recipes_sort_save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <main class="card content-wrap">
                    <h2 class="list-heading mb-m">{{ trans('entities.recipes_sort_show_other') }}</h2>

                    @include('entities.selector', ['name' => 'recipes_list', 'selectorSize' => 'compact', 'entityTypes' => 'recipe', 'entityPermission' => 'update', 'showAdd' => true])

                </main>
            </div>
        </div>

    </div>

@stop
