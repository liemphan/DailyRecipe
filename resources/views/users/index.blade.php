@extends('layouts.simple')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'users'])
        </div>

        <main class="card content-wrap">

            <div class="flex-container-row wrap justify-space-between items-center">
                <h1 class="list-heading">{{ trans('settings.users') }}</h1>

                <div>
                    <div class="block inline mr-xs">
                        <form method="get" action="{{ url("/settings/users") }}">
                            @foreach(collect($listDetails)->except('search') as $name => $val)
                                <input type="hidden" name="{{ $name }}" value="{{ $val }}">
                            @endforeach
                            <input type="text" name="search" placeholder="{{ trans('settings.users_search') }}" @if($listDetails['search']) value="{{$listDetails['search']}}" @endif>
                        </form>
                    </div>
                    <a href="{{ url("/settings/users/create") }}" class="outline button mt-none">{{ trans('settings.users_add_new') }}</a>
                </div>
            </div>

            <table class="table">
                <tr>
                    <th></th>
                    <th>
                        <a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'name']) }}">{{ trans('auth.name') }}</a>
                        /
                        <a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'email']) }}">{{ trans('auth.email') }}</a>
                    </th>
                    <th>{{ trans('settings.role_user_roles') }}</th>
                    <th class="text-right">
                        <a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'last_activity_at']) }}">{{ trans('settings.users_latest_activity') }}</a>
                    </th>

                    <th class="text-center">
                        <a href="{{url('/settings/users')}}"> Request </a>
                    </th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td class="text-center" style="line-height: 0;"><img class="avatar med" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}"></td>
                        <td>
                            <a href="{{ url("/settings/users/{$user->id}") }}">
                                {{ $user->name }}
                                <br>
                                <span class="text-muted">{{ $user->email }}</span>
                                @if($user->mfa_values_count > 0)
                                    <span title="MFA Configured" class="text-pos">@icon('lock')</span>
                                @endif
                            </a>
                        </td>
                        <td>
                            @foreach($user->roles as $index => $role)
                                <small><a href="{{ url("/settings/roles/{$role->id}") }}">{{$role->display_name}}</a>@if($index !== count($user->roles) -1),@endif</small>
                            @endforeach
                        </td>
                        <td class="text-right text-muted">
                            @if($user->last_activity_at)
                                <small title="{{ $user->last_activity_at->format('Y-m-d H:i:s') }}">{{ $user->last_activity_at->diffForHumans() }}</small>
                            @endif
                        </td>

                        {{--                        Request--}}
                        <td class="text-center text-muted">
                            <small>
                            @foreach($requests as $request)
                                @if($request->created_by == $user->id)
                                    @if($request->status == 1)
                                        <a style="color: green" href="{{url("/settings/users/{$user->id}/acceptRequest")}}"> Accept </a>
                                        |
                                        <a style="color: red" href="{{url("/settings/users/{$user->id}/rejectRequest")}}"> Reject </a>
                                            @break
                                        @elseif($request->status == 2)
                                        <p style="color: green">Accepted</p>
                                            @break
                                        @elseif($request->status == 3)
                                            <p style="color: red">Rejected</p>
                                        @break
                                    @endif
                                @endif
                            @endforeach
                            </small>
                        </td>

                    </tr>
                @endforeach
            </table>

            <div>
                {{ $users->links() }}
            </div>
        </main>

    </div>

@stop
