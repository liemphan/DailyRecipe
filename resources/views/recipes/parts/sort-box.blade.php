<div class="sort-box" data-type="recipe" data-id="{{ $recipe->id }}">
    <h5 class="text-recipe entity-list-item no-hover py-xs pl-none">
        <span>@icon('recipe')</span>
        <span>{{ $recipe->name }}</span>
    </h5>
    <div class="sort-box-options pb-sm">
        <a href="#" data-sort="name" class="button outline small">{{ trans('entities.recipes_sort_name') }}</a>
        <a href="#" data-sort="created" class="button outline small">{{ trans('entities.recipes_sort_created') }}</a>
        <a href="#" data-sort="updated" class="button outline small">{{ trans('entities.recipes_sort_updated') }}</a>
        <a href="#" data-sort="chaptersFirst" class="button outline small">{{ trans('entities.recipes_sort_chapters_first') }}</a>
        <a href="#" data-sort="chaptersLast" class="button outline small">{{ trans('entities.recipes_sort_chapters_last') }}</a>
    </div>
    <ul class="sortable-page-list sort-list">

        @foreach($recipeChildren as $recipeChild)
            <li class="text-{{ $recipeChild->getType() }}"
                data-id="{{$recipeChild->id}}" data-type="{{ $recipeChild->getType() }}"
                data-name="{{ $recipeChild->name }}" data-created="{{ $recipeChild->created_at->timestamp }}"
                data-updated="{{ $recipeChild->updated_at->timestamp }}">
                <div class="entity-list-item">
                    <span>@icon($recipeChild->getType()) </span>
                    <div>
                        {{ $recipeChild->name }}
                        <div>

                        </div>
                    </div>
                </div>
                @if($recipeChild->isA('chapter'))
                    <ul>
                        @foreach($recipeChild->visible_pages as $page)
                            <li class="text-page"
                                data-id="{{$page->id}}" data-type="page"
                                data-name="{{ $page->name }}" data-created="{{ $page->created_at->timestamp }}"
                                data-updated="{{ $page->updated_at->timestamp }}">
                                <div class="entity-list-item">
                                    <span>@icon('page')</span>
                                    <span>{{ $page->name }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach

    </ul>
</div>