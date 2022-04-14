@extends('layouts.tri')
@section('body')
    <!-- Styles and Fonts -->
{{--    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">--}}
{{--    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">--}}
    @yield('style')
    @yield('s')
    @yield('scripts1')
    <main class="content-wrap mt-m card">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <form action="{{ url('/search') }}" method="GET" role="search">
            <div enctype="multipart/form-data" >
                <div>
                    <label for="image_uploads">Choose images to search (PNG, JPG)</label>
                    <input type="file" id="image_uploads" name="image_uploads" accept=".jpg, .jpeg, .png" multiple>
                </div>
                <div class="preview">

                    <input id="lala" type="text" name="term"
                           aria-label="{{ trans('common.search') }}">
{{--                    <p id="lala">No files currently selected for upload</p>--}}
                </div>
                <a id="search" class="">Get</a>
            </div>

            <button type="submit" class="button">Search</button>
        </form>

    </main>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop
