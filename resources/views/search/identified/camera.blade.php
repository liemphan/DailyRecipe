@extends('layouts.tri')
@section('body')
    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">
    @yield('style')
    <main class="content-wrap mt-m card">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

            <form method="post" enctype="multipart/form-data" >
                <div>
                    <label for="image_uploads">Choose images to search (PNG, JPG)</label>
                    <input type="file" id="image_uploads" name="image_uploads" accept=".jpg, .jpeg, .png" multiple>
                </div>
                <div class="preview">
                    <p>No files currently selected for upload</p>
                </div>
            </form>
        <button id="search" class="button">Search</button>


    </main>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop













