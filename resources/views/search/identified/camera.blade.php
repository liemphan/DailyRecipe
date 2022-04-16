@extends('layouts.tri')
@section('body')
    <!-- Styles and Fonts -->
@yield('styles')
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">

    <main class="content-wrap mt-m card">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <form method="get"  action="{{url('search/')}}" role="search">
                    <div class="text-center">
                        <div id="fileList">
                            <img class="cover">No image currently selected for upload</img>
                        </div>
                        <div>
                        <a class="button outline" id="fileSelect" href="#">@icon('camera')</a>
                        <input type="file" id="image_uploads" accept="image/*" hidden>
                        <input  id="id_image" name="term" readonly  class="button" style="display: none">
                        </div>
                <button style="display: none" id="search" class="identify " type="submit" >Identify</button>
                    </div>
            </form>

    </main>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop













