<a href="{{ $menu->getUrl() }}" class="menu entity-list-item" data-entity-type="recipemenu" data-entity-id="{{$menu->id}}">
    <div class="entity-list-item-image bg-recipemenu @if($menu->image_id) has-image @endif" style="background-image: url('{{ $menu->getBookCover() }}')">
        @icon('recipemenu')
    </div>
    <div class="content py-xs">
        <h4 class="entity-list-item-name break-text">{{ $menu->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-none">{{ $menu->getExcerpt() }}</p>
        </div>
    </div>
</a>
<div class="entity-menu-books grid third gap-y-xs entity-list-item-children">
    @foreach($menu->visibleBooks as $book)
        <div>
            <a href="{{ $book->getUrl('?menu=' . $menu->id) }}" class="entity-chip text-book">
                @icon('book')
                {{ $book->name }}
            </a>
        </div>
    @endforeach
</div>