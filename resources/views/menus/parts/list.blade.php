
<main class="content-wrap card margin-32">

    <div class="grid half v-center">
        <h1 class="list-heading">{{ trans('entities.menus') }}</h1>
        <div class="text-right">
            @include('entities.sort', ['options' => $sortOptions, 'order' => $order, 'sort' => $sort, 'type' => 'recipemenus'])
        </div>
    </div>

    @if(count($menus) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($menus as $index => $menu)
                    @if ($index !== 0)
                        <hr class="my-m">
                    @endif
                    @include('menus.parts.list-item', ['menu' => $menu])
                @endforeach
            </div>
        @else
            <div class="grid third">
                @foreach($menus as $key => $menu)
                    @include('entities.grid-item', ['entity' => $menu])
                @endforeach
            </div>
        @endif
        <div>
            {!! $menus->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.menus_empty') }}</p>
        @if(userCan('recipemenu-create-all'))
            <a href="{{ url("/create-menu") }}" class="button outline">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif

</main>
