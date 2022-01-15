@extends('layouts.export')

@section('title', $recipe->name)

@section('content')
    <h1 style="font-size: 4.8em">{{$recipe->name}}</h1>

    <p>{{ $recipe->description }}</p>

    @if(count($recipeChildren) > 0)
        <ul class="contents">
            @foreach($recipeChildren as $recipeChild)
                <li><a href="#{{$recipeChild->getType()}}-{{$recipeChild->id}}">{{ $recipeChild->name }}</a></li>
{{--                @if($recipeChild->isA('chapter') && count($recipeChild->visible_pages) > 0)--}}
{{--                    <ul>--}}
{{--                        @foreach($recipeChild->visible_pages as $page)--}}
{{--                            <li><a href="#page-{{$page->id}}">{{ $page->name }}</a></li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                @endif--}}
            @endforeach
        </ul>
    @endif

    @foreach($recipeChildren as $recipeChild)
        <div class="page-break"></div>
        <h1 id="{{$recipeChild->getType()}}-{{$recipeChild->id}}">{{ $recipeChild->name }}</h1>

{{--        @if($recipeChild->isA('chapter'))--}}
{{--            <p>{{ $recipeChild->description }}</p>--}}

{{--            @if(count($recipeChild->visible_pages) > 0)--}}
{{--                @foreach($recipeChild->visible_pages as $page)--}}
{{--                    <div class="page-break"></div>--}}
{{--                    <div class="chapter-hint">{{$recipeChild->name}}</div>--}}
{{--                    <h1 id="page-{{$page->id}}">{{ $page->name }}</h1>--}}
{{--                    {!! $page->html !!}--}}
{{--                @endforeach--}}
{{--            @endif--}}

{{--        @else--}}
            {!! $recipeChild->html !!}
{{--        @endif--}}

    @endforeach
@endsection