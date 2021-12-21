<main class="content-wrap mt-m card">
    <div class="grid half v-center no-row-gap">
        <h1 class="list-heading">{{ trans('entities.recipes') }}</h1>
        <div class="text-m-right my-m">

            @include('entities.sort', ['options' => [
                'name' => trans('common.sort_name'),
                'created_at' => trans('common.sort_created_at'),
                'updated_at' => trans('common.sort_updated_at'),
            ], 'order' => $order, 'sort' => $sort, 'type' => 'recipes'])

        </div>
    </div>
    @if(count($recipes) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($recipes as $book)
                    @include('recipes.parts.list-item', ['book' => $book])
                @endforeach
            </div>
        @else
             <div class="grid third">
                @foreach($recipes as $key => $book)
                    @include('entities.grid-item', ['entity' => $book])
                @endforeach
             </div>
        @endif
        <div>
            {!! $recipes->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.recipes') }}</p>
        @if(userCan('recipes-create-all'))
            <a href="{{ url("/create-book") }}" class="text-pos">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif
</main>