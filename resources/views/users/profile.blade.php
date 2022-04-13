@extends('layouts.simple')

@section('body')

    <div class="container pt-xl margin-settings">

        <div class="grid right-focus reverse-collapse">

            <div>
                <section id="recent-user-activity" class="mb-xl">
                    <h5>{{ trans('entities.recent_activity') }}</h5>
                    @include('common.activity-list', ['activity' => $activity])
                </section>
            </div>

            <div>
                <section class="card content-wrap auto-height">
                    <div class="grid half v-center">
                        <div>
                            <div class="mr-m float left">
                                <img class="avatar square huge" src="{{ $user->getAvatar(120) }}" alt="{{ $user->name }}">
                            </div>
                            <div>
                                <h4 class="mt-md">{{ $user->name }}</h4>
                                <p class="text-muted">
                                    {{ trans('entities.profile_user_for_x', ['time' => $user->created_at->diffForHumans(null, true)]) }}
                                </p>
                            </div>
                        </div>
                        <div id="content-counts">
                            <div class="text-muted">{{ trans('entities.profile_created_content') }}</div>
                            <div class="grid half v-center no-row-gap">
{{--                                <div class="icon-list">--}}
{{--                                    <a href="#recent-pages" class="text-page icon-list-item">--}}
{{--                                        <span>@icon('page')</span>--}}
{{--                                        <span>{{ trans_choice('entities.x_pages', $assetCounts['pages']) }}</span>--}}
{{--                                    </a>--}}
{{--                                    <a href="#recent-chapters" class="text-chapter icon-list-item">--}}
{{--                                        <span>@icon('chapter')</span>--}}
{{--                                        <span>{{ trans_choice('entities.x_chapters', $assetCounts['chapters']) }}</span>--}}
{{--                                    </a>--}}
{{--                                </div>--}}
                                <div class="icon-list">
                                    <a href="#recent-recipes" class="text-recipe icon-list-item">
                                        <span>@icon('recipe')</span>
                                        <span>{{ trans_choice('entities.x_recipes', $assetCounts['recipes']) }}</span>
                                    </a>
                                    <a href="#recent-menus" class="text-recipemenu icon-list-item">
                                        <span>@icon('recipemenu')</span>
                                        <span>{{ trans_choice('entities.x_menus', $assetCounts['menus']) }}</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

{{--                <section class="card content-wrap auto-height recipe-contents">--}}
{{--                    <h2 id="recent-pages" class="list-heading">--}}
{{--                        {{ trans('entities.recently_created_pages') }}--}}
{{--                        @if (count($recentlyCreated['pages']) > 0)--}}
{{--                            <a href="{{ url('/search?term=' . urlencode('{created_by:'.$user->slug.'} {type:page}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>--}}
{{--                        @endif--}}
{{--                    </h2>--}}
{{--                    @if (count($recentlyCreated['pages']) > 0)--}}
{{--                        @include('entities.list', ['entities' => $recentlyCreated['pages'], 'showPath' => true])--}}
{{--                    @else--}}
{{--                        <p class="text-muted">{{ trans('entities.profile_not_created_pages', ['userName' => $user->name]) }}</p>--}}
{{--                    @endif--}}
{{--                </section>--}}

{{--                <section class="card content-wrap auto-height recipe-contents">--}}
{{--                    <h2 id="recent-chapters" class="list-heading">--}}
{{--                        {{ trans('entities.recently_created_chapters') }}--}}
{{--                        @if (count($recentlyCreated['chapters']) > 0)--}}
{{--                            <a href="{{ url('/search?term=' . urlencode('{created_by:'.$user->slug.'} {type:chapter}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>--}}
{{--                        @endif--}}
{{--                    </h2>--}}
{{--                    @if (count($recentlyCreated['chapters']) > 0)--}}
{{--                        @include('entities.list', ['entities' => $recentlyCreated['chapters'], 'showPath' => true])--}}
{{--                    @else--}}
{{--                        <p class="text-muted">{{ trans('entities.profile_not_created_chapters', ['userName' => $user->name]) }}</p>--}}
{{--                    @endif--}}
{{--                </section>--}}

                <section class="card content-wrap auto-height recipe-contents">
                    <h2 id="recent-recipes" class="list-heading">
                        {{ trans('entities.recently_created_recipes') }}
                        @if (count($recentlyCreated['recipes']) > 0)
                            <a href="{{ url('/search?term=' . urlencode('{created_by:'.$user->slug.'} {type:recipe}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>
                        @endif
                    </h2>
                    @if (count($recentlyCreated['recipes']) > 0)
                        @include('entities.list', ['entities' => $recentlyCreated['recipes'], 'showPath' => true])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_recipes', ['userName' => $user->name]) }}</p>
                    @endif
                </section>

                <section class="card content-wrap auto-height recipe-contents">
                    <h2 id="recent-menus" class="list-heading">
                        {{ trans('entities.recently_created_menus') }}
                        @if (count($recentlyCreated['menus']) > 0)
                            <a href="{{ url('/search?term=' . urlencode('{created_by:'.$user->slug.'} {type:recipemenu}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>
                        @endif
                    </h2>
                    @if (count($recentlyCreated['menus']) > 0)
                        @include('entities.list', ['entities' => $recentlyCreated['menus'], 'showPath' => true])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_menus', ['userName' => $user->name]) }}</p>
                    @endif
                </section>
            </div>

        </div>


    </div>
@stop