@extends('layouts.simple')

@section('body')
    <div class="container">

        <div class="grid left-focus v-center no-row-gap">
            <div class="py-m">
                @include('settings.parts.navbar', ['selected' => 'audit'])
            </div>
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">Report List</h2>
            <p class="text-muted">Below is the list of reported recipe(s), You will decide it should be keep or not</p>

            <div class="flex-container-row">
                <div component="dropdown" class="list-sort-type dropdown-container mr-m">
                    <label for="">{{ trans('settings.audit_event_filter') }}</label>
                    <button refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" aria-label="{{ trans('common.sort_options') }}" class="input-base text-left">{{ $listDetails['event'] ?: trans('settings.audit_event_filter_no_filter') }}</button>
                    <ul refs="dropdown@menu" class="dropdown-menu">
                        <li @if($listDetails['event'] === '') class="active" @endif><a href="{{ sortUrl('/settings/audit', $listDetails, ['event' => '']) }}">{{ trans('settings.audit_event_filter_no_filter') }}</a></li>
                        @foreach($activityTypes as $type)
                            <li @if($type === $listDetails['event']) class="active" @endif><a href="{{ sortUrl('/settings/audit', $listDetails, ['event' => $type]) }}">{{ $type }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <form action="{{ url('/settings/audit') }}" method="get" class="flex-container-row mr-m">
                    @if(!empty($listDetails['event']))
                        <input type="hidden" name="event" value="{{ $listDetails['event'] }}">
                    @endif

                    @foreach(['date_from', 'date_to'] as $filterKey)
                        <div class="mr-m">
                            <label for="audit_filter_{{ $filterKey }}">{{ trans('settings.audit_' . $filterKey) }}</label>
                            <input id="audit_filter_{{ $filterKey }}"
                                   component="submit-on-change"
                                   type="date"
                                   name="{{ $filterKey }}"
                                   value="{{ $listDetails[$filterKey] ?? '' }}">
                        </div>
                    @endforeach

                    <div class="form-group ml-auto"
                         component="submit-on-change"
                         option:submit-on-change:filter='[name="user"]'>
                        <label for="owner">{{ trans('settings.audit_table_user') }}</label>
                        @include('form.user-select', ['user' => $listDetails['user'] ? \DailyRecipe\Auth\User::query()->find($listDetails['user']) : null, 'name' => 'user', 'compact' =>  true])
                    </div>
                </form>
            </div>

            <hr class="mt-l mb-s">

            {{ $activities->links() }}

            <table class="table">
                <tbody>
                <tr>
                    <th>Recipe</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Created by</th>
                    <th>
                        <a href="{{ sortUrl('/settings/audit', $listDetails, ['sort' => 'created_at']) }}">Created Date</a></th>
                </tr>
{{--                @foreach()--}}
{{--                    <tr>--}}
{{--                        --}}
{{--                    </tr>--}}
{{--                @endforeach--}}
                </tbody>
            </table>

            {{ $activities->links() }}
        </div>

    </div>
@stop
