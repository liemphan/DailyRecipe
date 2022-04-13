@extends('search.identified.camera')
@section('scripts')

        <script>
            $('#upload').on('click', function() {
            var file_data = $('#sortpicture').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            //alert(form_data);
            $.ajax({
            url: 'http://10.66.171.232:8080/upload', // point to server-side PHP script
            //dataType: 'text', // what to expect, back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            success: function(api_resp){
                    alert('Tấn ảnh nay là :'+ api_resp); // display response from the PHP script, if any
            },
            error: function(err) {
                alert(err.responseText);
            }
        });
            });
    </script>
@stop
@section('style')

@stop