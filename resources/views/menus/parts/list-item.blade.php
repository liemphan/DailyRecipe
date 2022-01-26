<a href="{{ $menu->getUrl() }}" class="menu entity-list-item" data-entity-type="recipemenu" data-entity-id="{{$menu->id}}">
    <div class="entity-list-item-image bg-recipemenu @if($menu->image_id) has-image @endif" style="background-image: url('{{ $menu->getRecipeCover() }}')">
        @icon('recipemenu')
    </div>
    <div class="content py-xs">
        <h4 class="entity-list-item-name break-text">{{ $menu->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-none">{{ $menu->getExcerpt() }}</p>
        </div>
    </div>
</a>
<div class="entity-menu-recipes grid third gap-y-xs entity-list-item-children">
    @foreach($menu->visibleRecipes as $recipe)
        <div>
            <a href="{{ $recipe->getUrl('?menu=' . $menu->id) }}" class="entity-chip text-recipe">
                @icon('recipe')
                {{ $recipe->name }}
            </a>
        </div>
    @endforeach
</div>