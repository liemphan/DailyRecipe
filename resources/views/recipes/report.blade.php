@extends('layouts.tri')
@section('body')

    <main class="content-wrap mt-m card">
<h1 class="list-heading">{{trans('entities.report')}}</h1>
<label class="setting-list-label">{{trans('entities.report_info')}}</label>
<hr>
    <form action="{{ $recipe->getUrl("/storeReport")}}" method="POST">
        {{ csrf_field() }}
        <div class="form-group title-input">
            <label for="name">{{trans('entities.title')}}</label>
            @include('form.text', ['name' => 'name', 'autofocus' => true, 'required'=> true])
        </div>

        <div class="form-group description-input">
            <label for="description">{{trans('entities.reason_report')}}</label>
            @include('form.textarea', ['name' => 'description', 'required'=> true])
        </div>

        <div class="form-group text-right">
            <a href="{{ isset($recipe) ? $recipe->getUrl() : url('/') }}" class="button outline">{{ trans('common.cancel') }}</a>
            <button type="submit" class="button">{{trans('entities.submit')}}</button>
        </div>
    </form>
    </main>
    @stop