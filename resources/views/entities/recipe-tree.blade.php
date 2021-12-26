<nav id="recipe-tree"
     class="recipe-tree mb-xl"
     aria-label="{{ trans('entities.recipes_navigation') }}">

    <h5>{{ trans('entities.recipes_navigation') }}</h5>

    <ul class="sidebar-page-list mt-xs menu entity-list">
        @if (userCan('view', $recipe))
            <li class="list-item-recipe recipe">
                @include('entities.list-item-basic', ['entity' => $recipe, 'classes' => ($current->matches($recipe)? 'selected' : '')])
            </li>
        @endif

        @foreach($sidebarTree as $recipeChild)
            <li class="list-item-{{ $recipeChild->getType() }} {{ $recipeChild->getType() }} {{ $recipeChild->isA('page') && $recipeChild->draft ? 'draft' : '' }}">
                @include('entities.list-item-basic', ['entity' => $recipeChild, 'classes' => $current->matches($recipeChild)? 'selected' : ''])

                @if($recipeChild->isA('chapter') && count($recipeChild->visible_pages) > 0)
                    <div class="entity-list-item no-hover">
                        <span role="presentation" class="icon text-chapter"></span>
                        <div class="content">
                            @include('chapters.parts.child-menu', [
                                'chapter' => $recipeChild,
                                'current' => $current,
                                'isOpen'  => $recipeChild->matchesOrContains($current)
                            ])
                        </div>
                    </div>

                @endif

            </li>
        @endforeach
    </ul>
</nav>