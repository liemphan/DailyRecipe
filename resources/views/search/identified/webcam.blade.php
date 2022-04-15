@extends('search.identified.camera')
@section('scripts')
    <script>
        const fileSelected = document.getElementById("fileSelect");
        const   fileElem = document.getElementById("image_uploads");
        const  fileList = document.getElementById("fileList");

        fileSelected.addEventListener("click", function (e) {
            if (fileElem) {
                fileElem.click();
            }
            e.preventDefault(); // prevent navigation to "#"
        }, false);

        fileElem.addEventListener("change", handleFiles, false);

        function handleFiles() {
            if (!this.files.length) {
                fileList.innerHTML = "<p>No files selected!</p>";
            } else {
                $(document).ready(function() {

                    var file_data = $('#image_uploads').prop('files')[0];
                    var form_data = new FormData();
                    form_data.append('file', file_data);
                    //alert(form_data);
                    $.ajax({
                        url: 'http://10.66.164.112:8080/upload', // point to server-side PHP script
                        //dataType: 'text', // what to expect, back from the PHP script, if anything
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (api_resp) {
                            var element = document.getElementById('id_image');
                            element.setAttribute('value', api_resp);
                        },
                        error: function (err) {
                            alert(err.responseText);
                        }
                    });

                });

                fileList.innerHTML = "";
                const list = document.createElement("ul");
                fileList.appendChild(list);
                for (let i = 0; i < this.files.length; i++) {
                    const li = document.createElement("li");
                    list.appendChild(li);

                    const img = document.createElement("img");
                    img.src = URL.createObjectURL(this.files[i]);
                    img.height = 60;
                    img.onload = function() {
                        URL.revokeObjectURL(this.src);
                    }
                    li.appendChild(img);
                    const info = document.createElement("span");
                    info.innerHTML = this.files[i].name + ": " + this.files[i].size + " bytes";
                    li.appendChild(info);
                }

            }


        }

    </script>
@stop
@section('styles')
    <style>
        html {
            font-family: sans-serif;
        }

        form ol {
            padding-left: 0;
        }

        form li, div > p {
            background: #eee;
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            list-style-type: none;
            border: 1px solid black;
        }

        form img {
            height: 64px;
            order: 1;
        }

        form p {
            line-height: 32px;
            padding-left: 10px;
        }

        form label, form button {
            background-color: #7F9CCB;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px ridge black;
            font-size: 0.8rem;
            height: auto;
        }

        form label:hover, form button:hover {
            background-color: #2D5BA3;
            color: white;
        }

        form label:active, form button:active {
            background-color: #0D3F8F;
            color: white;
        }
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            border-radius: 5px;
        }
        .img_uploads{
            opacity: 0
        }
    </style>
@stop