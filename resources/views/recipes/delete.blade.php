@extends('layouts.simple')

@section('body')

    <div class="container small margin-settings">

        <div class="my-s ma">
            @include('entities.breadcrumbs', ['crumbs' => [
                $recipe,
                $recipe->getUrl('/delete') => [
                    'text' => trans('entities.recipes_delete'),
                    'icon' => 'delete',
                ]
            ]])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('entities.recipes_delete') }}</h1>
            <p>{{ trans('entities.recipes_delete_explain', ['recipeName' => $recipe->name]) }}</p>
            <p class="text-neg"><strong>{{ trans('entities.recipes_delete_confirmation') }}</strong></p>

            <form action="{{$recipe->getUrl()}}" method="POST" class="text-right">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="DELETE">
                <a href="{{$recipe->getUrl()}}" class="button outline">{{ trans('common.cancel') }}</a>
                <button type="submit" class="button">{{ trans('common.confirm') }}</button>
            </form>
        </div>

    </div>

@stop