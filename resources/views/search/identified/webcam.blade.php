@extends('search.identified.camera')
@section('scripts')
    <script>
        // import {block} from "../../../../public/dist/app";

        const fileSelected = document.getElementById("fileSelect");
        const   fileElem = document.getElementById("image_uploads");
        const  fileList = document.getElementById("fileList");
        const btnSearch = document.getElementById("search");
        const btnIngredient= document.getElementById("id_image");

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
                        url: 'http://192.168.1.238:8080/upload', // point to server-side PHP script
                        //dataType: 'text', // what to expect, back from the PHP script, if anything
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (api_resp) {
                            btnIngredient.style.display="block";
                            fileSelected.style.display="none";
                            var element = document.getElementById('id_image');
                            btnSearch.style.display="block"
                            element.setAttribute('value', api_resp);
                        },
                        error: function (err) {
                            alert(err.responseText);
                        }
                    });

                });


                // fileList.innerHTML = "<img>No files selected!</img>";
                fileList.innerHTML = "";
                // const list = document.createElement("ul");
                for (let i = 0; i < this.files.length; i++) {
                //     const li = document.createElement("li");
                //     list.appendChild(li);

                    const img = document.createElement("img");
                    // img.className="cover"
                    img.src = URL.createObjectURL(this.files[i]);
                    // img.height = 1000;
                    img.height =664;
                    img.onload = function() {
                        URL.revokeObjectURL(this.src);
                    }
                    fileList.appendChild(img);
                    // document.querySelector('.container').appendChild(img)
                    // li.appendChild(img);
                    // const info = document.createElement("span");
                    // info.innerHTML = this.files[i].name + ": " + this.files[i].size + " bytes";
                    // li.appendChild(info);
                }

            }


        }

    </script>
@stop

@section('styles')
    <style>
         div img{
            max-width: 100%;
            width: auto;
            height: auto;
             max-height:100%;
            order: 1;
        }

        form p {
            line-height: 32px;
            padding-left: 10px;
        }

        /*form label, form button {*/
        /*    background-color: #7F9CCB;*/
        /*    padding: 5px 10px;*/
        /*    border-radius: 5px;*/
        /*    border: 1px ridge black;*/
        /*    font-size: 0.8rem;*/
        /*    height: auto;*/
        /*}*/

        /*form label:hover, form button:hover {*/
        /*    background-color: #2D5BA3;*/
        /*    color: white;*/
        /*}*/

        /*form label:active, form button:active {*/
        /*    background-color: #0D3F8F;*/
        /*    color: white;*/
        /*}*/
        /*.buttonFile {*/
        /*    font-size: 30px;*/
        /*}*/

        /*.identify{*/
        /*    background-color: #58257b;*/
        /*    border: none;*/
        /*    color: white;*/
        /*    padding: 15px 32px;*/
        /*    text-align: center;*/
        /*    text-decoration: none;*/
        /*    display: inline-block;*/
        /*    font-size: 16px;*/
        /*    position: absolute;*/
        /*    left: 50%;*/
        /*    bottom:25%;*/
        /*    margin: 4px 2px;*/
        /*    transform: translate(-50%, -50%);*/
        /*    -ms-transform: translate(-50%, -50%);*/
        /*    border-radius: 100px;*/

        /*}*/
    </style>
@stop