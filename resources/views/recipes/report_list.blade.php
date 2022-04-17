@extends('layouts.simple')

@section('body')
    <div class="container">

        <div class="grid left-focus v-center no-row-gap">
            <div class="py-m">
                @include('settings.parts.navbar', ['selected' => 'reportlist'])
            </div>
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{trans('settings.report_list')}}</h2>
            <p class="text-muted">{{trans('settings.report_detail')}}</p>

            <div class="flex-container-row">
{{--                <div component="dropdown" class="list-sort-type dropdown-container mr-m">--}}
{{--                    <label for="">{{ trans('settings.audit_event_filter') }}</label>--}}
{{--                    <button refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" aria-label="{{ trans('common.sort_options') }}" class="input-base text-left">{{ $listDetails['event'] ?: trans('settings.audit_event_filter_no_filter') }}</button>--}}
{{--                    <ul refs="dropdown@menu" class="dropdown-menu">--}}
{{--                        <li @if($listDetails['event'] === '') class="active" @endif><a href="{{ sortUrl('/settings/audit', $listDetails, ['event' => '']) }}">{{ trans('settings.audit_event_filter_no_filter') }}</a></li>--}}
{{--                        @foreach($activityTypes as $type)--}}
{{--                            <li @if($type === $listDetails['event']) class="active" @endif><a href="{{ sortUrl('/settings/audit', $listDetails, ['event' => $type]) }}">{{ $type }}</a></li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                </div>--}}

                <form action="{{ url('/settings/reportlist') }}" method="get" class="flex-container-row mr-m">
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

                    <div class="form-group ml-auto" style="margin-right: 12px"
                         component="submit-on-change"
                         option:submit-on-change:filter='[name="user"]'>
                        <label for="owner">{{ trans('settings.audit_table_user') }}</label>
                        @include('form.user-select', ['user' => $listDetails['user'] ? \DailyRecipe\Auth\User::query()->find($listDetails['user']) : null, 'name' => 'user', 'compact' =>  true])
                    </div>
                    <div class="form-group ml-auto" style="margin-right: 12px; margin-top: -5px;">
                        <label for="owner">{{trans('settings.sort_by_date')}}</label><a class="button" href="{{ sortUrl('/settings/reportlist', $listDetails, ['sort' => 'created_at']) }}">{{trans('settings.sort_by_date')}}</a>
                    </div>
                        <div class="form-group mr-m " style="margin-top: -5px;"> <label for="owner">{{trans('settings.sort_by_recipe')}}</label><a class="button" href="{{ sortUrl('/settings/reportlist', $listDetails, ['sort' => 'entity_id']) }}">{{trans('settings.sort_by_recipe')}}</a>
                    </div>
                </form>

            </div>
            <hr class="mt-l mb-s">

            {{ $reports->links() }}

            <table class="table">
                <tbody>
                <tr>
                    <th>{{trans('entities.recipe')}}</th>
                    <th>{{trans('settings.status')}}</th>
                    <th>{{trans('settings.title')}}</th>
                    <th>{{trans('settings.content')}}</th>
                    <th>{{trans('settings.created_by')}}</th>
                    <th>{{trans('settings.created_date')}}</th>
                    <th>{{trans('settings.active_deactive')}}</th>
                </tr>
                @foreach($reports as $report)
                    @if($report->entity && is_null($report->entity->deleted_at))
                    <tr>
                        <td width="40%">
                            <a href="{{ $report->entity->getUrl() }}" class="table-entity-item">
                                <span role="presentation" class="icon text-{{$report->entity->getType()}}">@icon($report->entity->getType())</span>
                                <div class="text-{{ $report->entity->getType() }}">
                                    {{ $report->entity->name }}
                                </div>
                            </a>
                        </td>
                        <td class="text-center">{{ $report->status ? 'Active' : 'Deactive' }}</td>
                        <td>{{ $report->content }}</td>
                        <td class="text-center">{{ $report->description }}</td>
                        <td>
                            @include('settings.parts.table-user', ['user' => $report->user, 'user_id' => $report->user_id])
                        </td>
                        <td>{{ $report->created_at }}</td>
                        <td class="text-center text-muted">
{{--                            <a href="{{url($report->entity->getUrl().'/restore')}}" style="color:forestgreen"> Active </a>--}}
{{--                                |--}}
                                <a href="{{url($report->entity->getUrl().'/'.$report->id.'/deactive')}}" style="color:red"> Deactive </a>
                        </td>
                {{--                @foreach()--}}
{{--                    <tr>--}}
{{--                        --}}
{{--                    </tr>--}}
{{--                @endforeach--}}
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>

            {{ $reports->links() }}
        </div>

    </div>
@stop
