@extends('layouts.tri')
@section('body')
    <!-- Styles and Fonts -->
@yield('styles')
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">
{{--    @include('search.identified.webcam')--}}
    <main class="content-wrap mt-m card">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

            <form method="get"  action="{{url('search/')}}" role="search">
                <div>
                    <a class="items-center big-svg border" id="fileSelect" href="#">@icon('camera')</a>
                    <input type="file" id="image_uploads" accept=".jpg, .jpeg, .png" multiple hidden>
                </div>
                <div class="preview" id="fileList">
                    <p>No files currently selected for upload</p>
                </div>
                <div>
                    <input  id="id_image" name="term">
                </div>
                <a id="search">Get</a>
                <button id="search" class="button" type="submit">Search</button>
            </form>

    </main>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop













