{{--
$selected - String name of the selected tab
$version - Version of dailyrecipe to display
--}}
<div class="flex-container-row v-center wrap">
    <div class="py-m flex fit-content">
        @include('settings.parts.navbar', ['selected' => $selected])
    </div>
    <div class="flex"></div>
    <div class="text-right p-m flex fit-content">
        <a target="_blank" rel="noopener noreferrer" href="https://github.com/DailyRecipeApp/DailyRecipe/releases">
            DailyRecipe @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
        </a>
    </div>
</div>