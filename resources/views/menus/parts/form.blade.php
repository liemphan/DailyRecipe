{{ csrf_field() }}

<div class="form-group title-input">
    <label for="name">{{ trans('common.name') }}</label>
    @include('form.text', ['name' => 'name', 'autofocus' => true])
</div>

<div class="form-group description-input">
    <label for="description">{{ trans('common.description') }}</label>
    @include('form.textarea', ['name' => 'description'])
</div>

<div menu-sort class="grid half gap-xl">
    <div class="form-group">
        <label for="books">{{ trans('entities.menus_recipes') }}</label>
        <input type="hidden" id="books-input" name="books"
               value="{{ isset($menu) ? $menu->visibleBooks->implode('id', ',') : '' }}">
        <div class="scroll-box" menu-sort-assigned-books data-instruction="{{ trans('entities.menus_drag_recipes') }}">
            @if (count($menu->visibleBooks ?? []) > 0)
                @foreach ($menu->visibleBooks as $book)
                    <div data-id="{{ $book->id }}" class="scroll-box-item">
                        <a href="{{ $book->getUrl() }}" class="text-book">@icon('book'){{ $book->name }}</a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="form-group">
        <label for="books">{{ trans('entities.menus_add_recipes') }}</label>
        <div class="scroll-box">
            @foreach ($books as $book)
                <div data-id="{{ $book->id }}" class="scroll-box-item">
                    <a href="{{ $book->getUrl() }}" class="text-book">@icon('book'){{ $book->name }}</a>
                </div>
            @endforeach
        </div>
    </div>
</div>



<div class="form-group" collapsible id="logo-control">
    <button type="button" class="collapse-title text-primary" collapsible-trigger aria-expanded="false">
        <label>{{ trans('common.cover_image') }}</label>
    </button>
    <div class="collapse-content" collapsible-content>
        <p class="small">{{ trans('common.cover_image_description') }}</p>

        @include('form.image-picker', [
            'defaultImage' => url('/recipe_default_cover.png'),
            'currentImage' => (isset($menu) && $menu->cover) ? $menu->getBookCover() : url('/recipe_default_cover.png') ,
            'name' => 'image',
            'imageClass' => 'cover'
        ])
    </div>
</div>

<div class="form-group" collapsible id="tags-control">
    <button type="button" class="collapse-title text-primary" collapsible-trigger aria-expanded="false">
        <label for="tag-manager">{{ trans('entities.menu_tags') }}</label>
    </button>
    <div class="collapse-content" collapsible-content>
        @include('entities.tag-manager', ['entity' => $menu ?? null])
    </div>
</div>

<div class="form-group text-right">
    <a href="{{ isset($menu) ? $menu->getUrl() : url('/menus') }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button">{{ trans('entities.menus_save') }}</button>
</div>