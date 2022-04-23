<div dir="auto" style="text-align:justify">

    <h1 class="break-text" id="bkmrk-page-title"><b>{{$recipe->name}}</b></h1>
    <h5 class="text-muted " ><i>{!! nl2br(e($recipe->description)) !!}</i></h5>
    <div style="clear:left;"></div>

    @if (isset($diff) && $diff)
        {!! $diff !!}
    @else
        {!! isset($recipe->renderedHTML) ? $recipe->renderedHTML : $recipe->html !!}
    @endif
</div>