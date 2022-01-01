<nav class="breadcrumbs text-center" aria-label="{{ trans('common.breadcrumb') }}">
    <?php use DailyRecipe\Entities\Models\Entity;$breadcrumbCount = 0; ?>

    {{-- Show top level recipes item --}}
    @if (count($crumbs) > 0 && ($crumbs[0] ?? null) instanceof  \DailyRecipe\Entities\Models\Recipe)
        <a href="{{  url('/recipes')  }}" class="text-recipe icon-list-item outline-hover">
            <span>@icon('recipes')</span>
            <span>{{ trans('entities.recipes') }}</span>
        </a>
        <?php $breadcrumbCount++; ?>
    @endif

    {{-- Show top level menus item --}}
    @if (count($crumbs) > 0 && ($crumbs[0] ?? null) instanceof  \DailyRecipe\Entities\Models\Recipemenu)
        <a href="{{  url('/menus')  }}" class="text-recipemenu icon-list-item outline-hover">
            <span>@icon('recipemenu')</span>
            <span>{{ trans('entities.menus') }}</span>
        </a>
        <?php $breadcrumbCount++; ?>
    @endif

    @foreach($crumbs as $key => $crumb)
        <?php $isEntity = ($crumb instanceof Entity); ?>

        @if (is_null($crumb))
            <?php continue; ?>
        @endif
        @if ($breadcrumbCount !== 0 && !$isEntity)
            <div class="separator">@icon('chevron-right')</div>
        @endif

        @if (is_string($crumb))
            <a href="{{  url($key)  }}">
                {{ $crumb }}
            </a>
        @elseif (is_array($crumb))
            <a href="{{  url($key)  }}" class="icon-list-item outline-hover">
                <span>@icon($crumb['icon'])</span>
                <span>{{ $crumb['text'] }}</span>
            </a>
        @elseif($isEntity && userCan('view', $crumb))
            @if($breadcrumbCount > 0)
                @include('entities.breadcrumb-listing', ['entity' => $crumb])
            @endif
            <a href="{{ $crumb->getUrl() }}" class="text-{{$crumb->getType()}} icon-list-item outline-hover">
                <span>@icon($crumb->getType())</span>
                <span>
                    {{ $crumb->getShortName() }}
                </span>
            </a>
        @endif
        <?php $breadcrumbCount++; ?>
    @endforeach
</nav>