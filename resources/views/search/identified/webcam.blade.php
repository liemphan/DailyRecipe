@extends('search.identified.camera')
@section('scripts')
        <script>
            $('#upload').on('click', function() {
            var file_data = $('#image_uploads').prop('files')[0];
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

                const input = document.querySelector('input');
                const preview = document.querySelector('.preview');

                input.style.opacity = 0;

                input.addEventListener('change', updateImageDisplay);

                function updateImageDisplay() {
                while(preview.firstChild) {
                preview.removeChild(preview.firstChild);
            }

                const curFiles = input.files;
                if(curFiles.length === 0) {
                const para = document.createElement('p');
                para.textContent = 'No files currently selected for upload';
                preview.appendChild(para);
            } else {
                const list = document.createElement('ol');
                preview.appendChild(list);

                for(const file of curFiles) {
                const listItem = document.createElement('li');
                const para = document.createElement('p');

                if(validFileType(file)) {
                para.textContent = `File name ${file.name}, file size ${returnFileSize(file.size)}.`;
                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);

                listItem.appendChild(image);
                listItem.appendChild(para);
            } else {
                para.textContent = `File name ${file.name}: Not a valid file type. Update your selection.`;
                listItem.appendChild(para);
            }

                list.appendChild(listItem);
            }
            }
            }

                // https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types
                const fileTypes = [
                'image/apng',
                'image/bmp',
                'image/gif',
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/svg+xml',
                'image/tiff',
                'image/webp',
                `image/x-icon`
                ];

                function validFileType(file) {
                return fileTypes.includes(file.type);
            }

                function returnFileSize(number) {
                if(number < 1024) {
                return number + 'bytes';
            } else if(number > 1024 && number < 1048576) {
                return (number/1024).toFixed(1) + 'KB';
            } else if(number > 1048576) {
                return (number/1048576).toFixed(1) + 'MB';
            }
            }
        </script>
@stop

@section('style')
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
    </style>
@stop
@yield('bottom')

@yield('scripts')