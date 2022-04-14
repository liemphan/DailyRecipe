@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $recipe,
                $recipe->getUrl('/deactive') => [
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
{{--                <form action="{{ $recipe->getUrl()  }}" method="POST">--}}
{{--                    <input type="hidden" name="_method" value="PUT">--}}
{{--                    <input type="hidden" name="_method" value="UPDATE">--}}
{{--                </form>--}}
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="status" id="status" value="0">
                <input type="hidden" name="_method" value="DELETE">
                <a href="{{'/settings/reportlist'}}" class="button outline">{{ trans('common.cancel') }}</a>
                <button type="submit" class="button">{{ trans('common.confirm') }}</button>
            </form>
        </div>

    </div>

@stop