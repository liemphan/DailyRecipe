@extends('layouts.tri')

@section('container-attrs')
    component="entity-search"
    option:entity-search:entity-id="{{ $recipe->id }}"
    option:entity-search:entity-type="recipe"
@stop

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($recipe->description, 100, '...') }}">
    @if($recipe->cover)
        <meta property="og:image" content="{{ $recipe->getRecipeCover() }}">
    @endif
@endpush

@section('body')

    <div class="mb-s">
        @include('entities.breadcrumbs', ['crumbs' => [
            $recipe,
        ]])
    </div>

    <main class="content-wrap card">
        <h1 class="break-text">{{$recipe->name}}</h1>
        <div refs="entity-search@contentView" class="recipe-content">
            <p class="text-muted">{!! nl2br(e($recipe->description)) !!}</p>
            @if(count($recipeChildren) > 0)
                <div class="entity-list recipe-contents">
                    @foreach($recipeChildren as $childElement)
                        @if($childElement->isA('chapter'))
                            @include('chapters.parts.list-item', ['chapter' => $childElement])
                        @else
                            @include('pages.parts.list-item', ['page' => $childElement])
                        @endif
                    @endforeach
                </div>
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.recipes_empty_contents') }}</p>

                    <div class="icon-list block inline">
                        @if(userCan('page-create', $recipe))
                            <a href="{{ $recipe->getUrl('/create-page') }}" class="icon-list-item text-page">
                                <span class="icon">@icon('page')</span>
                                <span>{{ trans('entities.recipes_empty_create_page') }}</span>
                            </a>
                        @endif
                        @if(userCan('chapter-create', $recipe))
                            <a href="{{ $recipe->getUrl('/create-chapter') }}" class="icon-list-item text-chapter">
                                <span class="icon">@icon('chapter')</span>
                                <span>{{ trans('entities.recipes_empty_add_chapter') }}</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endif
        </div>

        @include('entities.search-results')
    </main>

@stop

@section('right')
    <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="text-small text-muted blended-links">
            @include('entities.meta', ['entity' => $recipe])
            @if($recipe->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $recipe))
                        <a href="{{ $recipe->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.recipes_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.recipes_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

            @if(userCan('page-create', $recipe))
                <a href="{{ $recipe->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif
            @if(userCan('chapter-create', $recipe))
                <a href="{{ $recipe->getUrl('/create-chapter') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.chapters_new') }}</span>
                </a>
            @endif

            <hr class="primary-background">

            @if(userCan('recipe-update', $recipe))
                <a href="{{ $recipe->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
                <a href="{{ $recipe->getUrl('/sort') }}" class="icon-list-item">
                    <span>@icon('sort')</span>
                    <span>{{ trans('common.sort') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $recipe))
                <a href="{{ $recipe->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('recipe-delete', $recipe))
                <a href="{{ $recipe->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            <hr class="primary-background">

            @if(signedInUser())
                @include('entities.favourite-action', ['entity' => $recipe])
            @endif
            @if(userCan('content-export'))
                @include('entities.export-menu', ['entity' => $recipe])
            @endif
        </div>
    </div>

@stop

@section('left')

    @include('entities.search-form', ['label' => trans('entities.recipes_search_this')])

    @if($recipe->tags->count() > 0)
        <div class="mb-xl">
            @include('entities.tag-list', ['entity' => $recipe])
        </div>
    @endif

    @if(count($recipeParentMenus) > 0)
        <div class="actions mb-xl">
            <h5>{{ trans('entities.menus_long') }}</h5>
            @include('entities.list', ['entities' => $recipeParentMenus, 'style' => 'compact'])
        </div>
    @endif

    @if(count($activity) > 0)
        <div class="mb-xl">
            <h5>{{ trans('entities.recent_activity') }}</h5>
            @include('common.activity-list', ['activity' => $activity])
        </div>
    @endif
@stop

