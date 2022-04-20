@extends('layouts.tri')
@section('body')
    <!-- Styles and Fonts -->
@yield('styles')
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <div class="container py-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{trans('entities.identify')}}</h1>
            <form method="get"  action="{{url('search/')}}" role="search">
                {{ csrf_field() }}
                    <div class="text-center align-center items-center">
                        <div id="fileList">
                            <img class="cover">{{trans('entities.no_image')}}
                        </div>
                        <div style="text-align: center">
                        <label class="button outline" id="fileSelect" style="font-size:30px" href="#">@icon('camera')</label>
                        <input type="file" id="image_uploads" accept="image/*" hidden>
                         <input  id="id_image" name="term" readonly  class="button outline text-center" style="display: none; margin: 0 auto;border:1px solid transparent;color: red;
">
                        </div>
                        <div class="align-center">
                            <button style="display: none; margin:10px auto" id="search" class="button" type="submit" >{{trans('entities.identify')}}</button>
                        </div>
                    </div>
            </form>

    </div>
</div>
@stop

@section('left')
    @include('home.parts.sidebar')
@stop













