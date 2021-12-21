
<main class="content-wrap mt-m card">

    <div class="grid half v-center">
        <h1 class="list-heading">{{ trans('entities.menus') }}</h1>
        <div class="text-right">
            @include('entities.sort', ['options' => $sortOptions, 'order' => $order, 'sort' => $sort, 'type' => 'bookshelves'])
        </div>
    </div>

    @if(count($menus) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($menus as $index => $shelf)
                    @if ($index !== 0)
                        <hr class="my-m">
                    @endif
                    @include('menus.parts.list-item', ['shelf' => $shelf])
                @endforeach
            </div>
        @else
            <div class="grid third">
                @foreach($menus as $key => $shelf)
                    @include('entities.grid-item', ['entity' => $shelf])
                @endforeach
            </div>
        @endif
        <div>
            {!! $menus->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.menus_empty') }}</p>
        @if(userCan('bookshelf-create-all'))
            <a href="{{ url("/create-shelf") }}" class="button outline">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif

</main>
