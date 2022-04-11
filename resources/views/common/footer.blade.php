@if(count(setting('app-footer-links', [])) > 0)
<footer>
    @foreach(setting('app-footer-links', []) as $link)
        <a href="{{ $link['url'] }}" target="_blank" rel="noopener">{{ strpos($link['label'], 'trans::') === 0 ? trans(str_replace('trans::', '', $link['label'])) : $link['label'] }}</a>
    @endforeach
</footer>
@endif
{{--    <footer class="hide-over-xl primary-background-light">--}}
{{--        <div class="links text-center">--}}
{{--            @if (hasAppAccess())--}}
{{--                @if(userCanOnAny('view', \DailyRecipe\Entities\Models\Recipemenu::class) || userCan('recipemenu-view-all') || userCan('recipemenu-view-own'))--}}
{{--                    <a href="{{ url('/menus') }}">@icon('recipemenu'){{ trans('entities.menus') }}</a>--}}
{{--                @endif--}}
{{--                <a href="{{ url('/recipes') }}">@icon('recipes'){{ trans('entities.recipes') }}</a>--}}
{{--                <a href="{{ url('/search/identified/ingredients') }}">@icon('recipes')Identified</a>--}}
{{--                @if(signedInUser() && userCan('settings-manage'))--}}
{{--                    <a href="{{ url('/settings') }}">@icon('settings'){{ trans('settings.settings') }}</a>--}}
{{--                @endif--}}
{{--                @if(signedInUser() && userCan('users-manage') && !userCan('settings-manage'))--}}
{{--                    <a href="{{ url('/settings/users') }}">@icon('users'){{ trans('settings.users') }}</a>--}}
{{--                @endif--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </footer>--}}
