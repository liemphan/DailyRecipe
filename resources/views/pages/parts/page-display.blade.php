<div dir="auto">

    <h1 class="break-text" id="bkmrk-page-title">{{$page->name}}</h1>
    <p class="text-muted">{!! nl2br(e($recipe->description)) !!}</p>
    <div style="clear:left;"></div>

    @if (isset($diff) && $diff)
        {!! $diff !!}
    @else
        {!! isset($page->renderedHTML) ? $page->renderedHTML : $page->html !!}
    @endif
</div>