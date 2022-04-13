@extends('search.identified.camera')
@section('scripts')
    <script>
        window.onload = async function () {
            if (
                !"mediaDevices" in navigator ||
                !"getUserMedia" in navigator.mediaDevices
            ) {
                document.write('Not support API camera')
                return;
            }

            const video = document.querySelector("#video");
            const canvas = document.querySelector("#canvas");
            const screenshotsContainer = document.querySelector("#screenshotsContainer");
            let videoStream = null
            let useFrontCamera = true; //camera trước
            const constraints = {
                video: {
                    width: {
                        min: 1280,
                        ideal: 1920,
                        max: 2560,
                    },
                    height: {
                        min: 720,
                        ideal: 1080,
                        max: 1440,
                    }
                },
            };
            //
            // // play
            // btnPlay.addEventListener("click", function () {
            //     video.play();
            //     btnPlay.classList.add("is-hidden");
            //     btnPause.classList.remove("is-hidden");
            //
            //     btnPlay1.classList.add("is-hidden");
            //     btnPause1.classList.remove("is-hidden");
            // });
            //
            // // pause
            // btnPause.addEventListener("click", function () {
            //     video.pause();
            //     btnPause.classList.add("is-hidden");
            //     btnPlay.classList.remove("is-hidden");
            //
            //     btnPause1.classList.add("is-hidden");
            //     btnPlay1.classList.remove("is-hidden");
            // });
            //
            //
            // btnChangeCamera.addEventListener("click", function () {
            //     useFrontCamera = !useFrontCamera;
            //     init();
            // });
            //
            //
            // // play
            // btnPlay1.addEventListener("click", function () {
            //     video.play();
            //     btnPlay1.classList.add("is-hidden");
            //     btnPause1.classList.remove("is-hidden");
            //
            //     btnPlay.classList.add("is-hidden");
            //     btnPause.classList.remove("is-hidden");
            // });
            //
            // // pause
            // btnPause1.addEventListener("click", function () {
            //     video.pause();
            //     btnPause1.classList.add("is-hidden");
            //     btnPlay1.classList.remove("is-hidden");
            //
            //     btnPause.classList.add("is-hidden");
            //     btnPlay.classList.remove("is-hidden");
            // });
            //
            //
            // btnChangeCamera1.addEventListener("click", function () {
            //     useFrontCamera = !useFrontCamera;
            //     init();
            // });

            function stopVideoStream() {
                if (videoStream) {
                    videoStream.getTracks().forEach((track) => {
                        track.stop();
                    });
                }
            }

            btnScreenshot.addEventListener("click", function () {
                let img = document.getElementById('screenshot');
                if (!img) {
                    img = document.createElement("img");
                    img.id = 'screenshot';
                    img.style.width = '100%';
                }
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext("2d").drawImage(video, 0, 0);
                img.src = canvas.toDataURL("image/png");
                screenshotsContainer.prepend(img);
            });


            btnScreenshot1.addEventListener("click", function () {
                let img = document.getElementById('screenshot');
                if (!img) {
                    img = document.createElement("img");
                    img.id = 'screenshot';
                    img.style.width = '100%';
                }
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext("2d").drawImage(video, 0, 0);
                img.src = canvas.toDataURL("image/png");
                screenshotsContainer.prepend(img);
            });
            async function init() {
                stopVideoStream();
                constraints.video.facingMode = useFrontCamera ? "user" : "environment";
                try {
                    videoStream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = videoStream;
                } catch (error) {
                    console.log(error)
                }
            }

            init();
        }

        function gtag() {
            dataLayer.push(arguments)
        }

        window.dataLayer = window.dataLayer || [], gtag("js", new Date), gtag("config", "UA-111717926-1")
    </script>
@stop
@section('style')
    <style>
        #video {
            width: 100%;
        }

        .is-hidden {
            display: none;
        }

        .iconfont {
            font-size: 24px;
        }

        .btns {
            margin-top: 50px;
            margin-bottom: auto;
            text-align: center;

        }

        buttons-couple {
            font-size: 22px;
            padding: 8px 10px;
            border: 2px solid #ccc;
            border-radius: 10px;
        }

        .video-screenshot {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-column-gap: 10px;
        }

        /*footer {*/
        /*    margin: 20px 0;*/
        /*    font-size: 16px;*/
        /*}*/
    </style>
@stop