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
                @foreach($recipes as $recipe)
                    @include('recipes.parts.list-item', ['recipe' => $recipe])
                @endforeach
            </div>
        @else
             <div class="grid third">
                @foreach($recipes as $key => $recipe)
                    @include('entities.gird-item-recipe', ['entity' => $recipe])
                @endforeach
             </div>
        @endif
        <div>
            {!! $recipes->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.recipes_empty') }}</p>
        @if(userCan('recipes-create-all'))
            <a href="{{ url("/create-recipe") }}" class="text-pos">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif
</main>