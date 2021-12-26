<a href="{{ $recipe->getUrl() }}" class="recipe entity-list-item" data-entity-type="recipe" data-entity-id="{{$recipe->id}}">
    <div class="entity-list-item-image bg-recipe" style="background-image: url('{{ $recipe->getRecipeCover() }}')">
        @icon('recipe')
    </div>
    <div class="content">
        <h4 class="entity-list-item-name break-text">{{ $recipe->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-s text-limit-lines-1">{{ $recipe->description }}</p>
        </div>
    </div>
</a>