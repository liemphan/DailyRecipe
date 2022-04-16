@extends('layouts.tri')
@section('body')
    <!-- Styles and Fonts -->
@yield('styles')
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">
{{--    @include('search.identified.webcam')--}}
    <main class="content-wrap mt-m card">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

            <form method="get"  action="{{url('search/')}}" role="search">
{{--                <div class="hello">--}}
{{--                <div class="grid half hello">--}}
                    <div id="fileList">
                        <img class="cover">No files currently selected for upload</img>
                    </div>
                    <div class="text-center">
                        <a class="button outline" id="fileSelect" href="#">@icon('camera')</a>
                        <input type="file" id="image_uploads" accept="image/*" hidden>

                        <input type="hidden" id="id_image" name="term">
                    </div>
{{--                </div>--}}
{{--                </div>--}}
                <button style="display: none" id="search" class="button" type="submit">Search</button>
            </form>

    </main>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop













