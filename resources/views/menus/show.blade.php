@extends('layouts.tri')

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($menu->description, 100, '...') }}">
    @if($menu->cover)
        <meta property="og:image" content="{{ $menu->getBookCover() }}">
    @endif
@endpush

@section('body')

    <div class="mb-s">
        @include('entities.breadcrumbs', ['crumbs' => [
            $menu,
        ]])
    </div>

    <main class="card content-wrap">

        <div class="flex-container-row wrap v-center">
            <h1 class="flex fit-content break-text">{{ $menu->name }}</h1>
            <div class="flex"></div>
            <div class="flex fit-content text-m-right my-m ml-m">
                @include('entities.sort', ['options' => [
                    'default' => trans('common.sort_default'),
                    'name' => trans('common.sort_name'),
                    'created_at' => trans('common.sort_created_at'),
                    'updated_at' => trans('common.sort_updated_at'),
                ], 'order' => $order, 'sort' => $sort, 'type' => 'recipemenus_books'])
            </div>
        </div>

        <div class="book-content">
            <p class="text-muted">{!! nl2br(e($menu->description)) !!}</p>
            @if(count($sortedVisibleMenuBooks) > 0)
                @if($view === 'list')
                    <div class="entity-list">
                        @foreach($sortedVisibleMenuBooks as $book)
                            @include('books.parts.list-item', ['book' => $book])
                        @endforeach
                    </div>
                @else
                    <div class="grid third">
                        @foreach($sortedVisibleMenuBooks as $book)
                            @include('entities.grid-item', ['entity' => $book])
                        @endforeach
                    </div>
                @endif
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mt-xl mb-m">{{ trans('entities.menus_empty_contents') }}</p>
                    <div class="icon-list inline block">
                        @if(userCan('book-create-all') && userCan('recipemenu-update', $menu))
                            <a href="{{ $menu->getUrl('/create-book') }}" class="icon-list-item text-book">
                                <span class="icon">@icon('add')</span>
                                <span>{{ trans('entities.recipes_create') }}</span>
                            </a>
                        @endif
                        @if(userCan('recipemenu-update', $menu))
                            <a href="{{ $menu->getUrl('/edit') }}" class="icon-list-item text-recipemenu">
                                <span class="icon">@icon('edit')</span>
                                <span>{{ trans('entities.menus_edit_and_assign') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </main>

@stop

@section('left')

    @if($menu->tags->count() > 0)
        <div id="tags" class="mb-xl">
            @include('entities.tag-list', ['entity' => $menu])
        </div>
    @endif

    <div id="details" class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="text-small text-muted blended-links">
            @include('entities.meta', ['entity' => $menu])
            @if($menu->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $menu))
                        <a href="{{ $menu->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.menus_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.menus_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if(count($activity) > 0)
        <div class="mb-xl">
            <h5>{{ trans('entities.recent_activity') }}</h5>
            @include('common.activity-list', ['activity' => $activity])
        </div>
    @endif
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

            @if(userCan('book-create-all') && userCan('recipemenu-update', $menu))
                <a href="{{ $menu->getUrl('/create-book') }}" class="icon-list-item">
                    <span class="icon">@icon('add')</span>
                    <span>{{ trans('entities.recipes_new_action') }}</span>
                </a>
            @endif

            @include('entities.view-toggle', ['view' => $view, 'type' => 'menu'])

            <hr class="primary-background">

            @if(userCan('recipemenu-update', $menu))
                <a href="{{ $menu->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif

            @if(userCan('restrictions-manage', $menu))
                <a href="{{ $menu->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif

            @if(userCan('recipemenu-delete', $menu))
                <a href="{{ $menu->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            @if(signedInUser())
                <hr class="primary-background">
                @include('entities.favourite-action', ['entity' => $menu])
            @endif

        </div>
    </div>
@stop




