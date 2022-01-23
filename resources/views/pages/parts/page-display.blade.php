<div dir="auto">

    <h1 class="break-text" id="bkmrk-page-title"><b>{{$recipe->name}}</b></h1>
    <h5 class="text-muted"><b>{!! nl2br(e($recipe->description)) !!}</b></h5>
    <div style="clear:left;"></div>

    @if (isset($diff) && $diff)
        {!! $diff !!}
    @else
        {!! isset($recipe->renderedHTML) ? $recipe->renderedHTML : $recipe->html !!}
    @endif
</div>