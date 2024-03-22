@extends('layouts.front')

@section('css')
    <style>
                #ad_video::-webkit-media-controls {
            display: none;
        }
        #video-overlay {
            top: 50%;
            left: 50%;
            position: absolute;
            transform: translate(-50%, -50%);
            margin:auto;
            display: none;
            z-index: 1;
        }
        #video-overlay img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin:auto;
            width:50%;
            z-index: 1;
        }

        #top-overlay {
            position: absolute;
            top: 0;
            left: 0;
        }

        #bottom-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
        }

        #video-chat-container:-webkit-full-screen #top-overlay,
        #video-chat-container:-webkit-full-screen #bottom-overlay {
            position: fixed;
        }
        #video-chat-container:fullscreen #top-overlay,
        #video-chat-container:fullscreen #bottom-overlay {
            position: fixed;
        }
        .time {
            font-size: 1em;
            color: white;
            width: 15%;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        input[type="range"] {
            -webkit-appearance: none;
            background: transparent;
            margin: 0;
            width: 75%;
            padding: 0 10px;
        }
        input[type="range"]:focus {
            outline: none;
        }
        input[type="range"]::-webkit-slider-runnable-track {
            width: 100%;
            height: 10px;
            cursor: pointer;
            background: white;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 10px;
            width: 10px;
            background: orangered;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="py-7 min-vh-100 flq-background d-flex align-items-center">
        <div class="flq-background-image">
                <span class="flq-image jarallax" data-speed=0.7>
                    <img src="{{$episode->getFirstMediaUrl('poster')}}" class="jarallax-img" alt="">
                </span>
        </div>
        <div class="flq-background-overlay" style="background-color: hsla(var(--flq-color-black), 0.8);"></div>
        <div class="container pt-navbar" data-sr="movie" data-sr-interval="70" data-sr-duration="1200" data-sr-distance="10">
            <div class="row gy-6 gx-6" id="video-chat-container">
                <div id='video-overlay'>
                    <img src="" id="ad_image" class="jarallax-img" alt="">
                </div>
                <video style="margin: 0; display: none; position: relative;
  z-index: 0;" id="video" controlsList="nodownload" oncontextmenu="return false;">
                    @php
                        if ($episode->episode_file_type == 'local'){
        $url =  $episode->getFirstMediaUrl('episode');
    }elseif ($episode->episode_file_type == 'link'){
        $url = url('/').'/'.env('LINK_MOVIE_FOLDER').'/'. $episode->episode_link;
    }elseif ($episode->episode_file_type == 'external_link'){
        $url = $episode->episode_link;
    }

                    @endphp
                    <source id="mp4" src="{{$url}}" type="video/mp4">
                </video>
                <div
                        id="top-overlay"
                        class="w-100 p-2 d-flex align-items-center justify-content-between unHide d-none">
                    <div style="position: fixed; right: 58px; top: 20px">
                        <i id="fullscreen-toggle-btn" role="button" class="fa-solid fa-expand text-white text-outline"></i>
                    </div>
                </div>
                <div
                        id="bottom-overlay"
                        class="w-100 p-2 d-flex align-items-center justify-content-between unHide d-none">
                    <div class="me-1">
                        <button id="play-btn" class="btn btn-sm btn-outline" style="margin-left: 50px">
                            <i id="play-btn-icon" class="fa-solid fa-play"></i>
                        </button>
                    </div>
                    <div>
                        <button id="mute-btn" class="btn btn-sm btn-outline" style="margin-right: 50px">
                            <i id="mute-btn-icon" class="fa-solid fa-volume-xmark"></i>
                        </button>
                    </div>
                    <div class="video-progress">
                        <div class="video-progress-filled"></div>
                    </div>
                    <input
                            type="range"
                            class="volume"
                            min="0"
                            max="100"
                            step="0.01"
                            value="1"
                    />

                    <div class="time">
                        <span class="current">0:00</span> / <span class="duration">0:00</span>
                    </div>
                </div>
                <video style="margin: 0; padding: 0;" id="ad_video" controls controlsList="nodownload" autoplay muted oncontextmenu="return false;">
                    <source id="ad_video_src" src="" type="video/mp4">
                </video>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var isMobile = window.matchMedia("only screen and (max-width: 767px)").matches;
        if (isMobile) {
            console.log('in mobile')
            $("#video").attr("controls", "controls");
            $("#video-player").attr("nodownload", "nodownload");
            $("#top-overlay").hide();
            $("#bottom-overlay").hide();
        } else {
            const togglePlayback = (evt) => {
                const btn = document.getElementById('play-btn-icon');
                const video = document.getElementById('video');
                if (video.paused) {
                    video.play();
                    $('#play-btn-icon').removeClass("fa-play");
                    $('#play-btn-icon').addClass("fa-pause");
                } else {
                    video.pause();

                    $('#play-btn-icon').addClass("fa-play");
                    $('#play-btn-icon').removeClass("fa-pause");
                }
            };
            const toggleMute = (evt) => {
                const btn = document.getElementById('mute-btn-icon');
                const video = document.getElementById('video');
                video.muted = !video.muted;
                if (video.muted) {
                    btn.classList.remove('fa-volume-xmark');
                    btn.classList.add('fa-volume-high');
                } else {
                    btn.classList.add('fa-volume-xmark');
                    btn.classList.remove('fa-volume-high');
                }
            };
            const toggleFullScreen = async () => {
                const container = document.getElementById('video-chat-container');
                const fullscreenApi = container.requestFullscreen
                    || container.webkitRequestFullScreen
                    || container.mozRequestFullScreen
                    || container.msRequestFullscreen;
                if (!document.fullscreenElement) {
                    fullscreenApi.call(container);
                } else {
                    document.exitFullscreen();
                }
            };

            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('fullscreen-toggle-btn').addEventListener('click', toggleFullScreen);
                // document.getElementById('submit-chat').addEventListener('click', fakeChat);
                document.getElementById('mute-btn').addEventListener('click', toggleMute);
                document.getElementById('play-btn').addEventListener('click', togglePlayback);

            });
        }

        $( document ).ready(function() {
            var overlay         = document.getElementById('video-overlay'),
                video           = document.getElementById('video');
                
                var playedAdIds = [];
            var noAdds = {{$noAdds ?? null}}
            console.log(noAdds)


            video.onpause = function() {
                // Save the current time to the database
                saveCurrentTime(video.currentTime);
            }

            function hideOverlay() {
                overlay.style.display = "none";
                video.play()
            }
            function showOverlay() {
                console.log('showing overlay')
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var movie_id = "{{$episode->id}}"
                $.ajax({
                    type:'POST',
                    url:"{{ route('episode.get_ad_episode_images') }}",
                    data:{id:movie_id, },
                    success:function(data){
                        if(data.image == null)
                            return;
                        var image = data.image;
                        console.log(image.link)
                        $('#ad_image').attr("src", image.link);
                        overlay.style.display = "block";
                    }
                });
            }
            video.addEventListener('pause', showOverlay);
            video.addEventListener('play', hideOverlay);
            // overlay.addEventListener('click', hideOverlay);


            var savedTime = {{$savedTime}} ;
            var ad_video = document.getElementById('ad_video');
            // if(savedTime > 0 || noAdds){
            if(noAdds){
                if(!isMobile){
                    $('.unHide').removeClass('d-none')
                }
                console.log('also here')
                
                ad_video.style.display = "none";
                video.style.display = "block";
                video.load()
                video.currentTime="{{$savedTime}}";
            }else{
                // For Video Ads
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var total_length = 0;
                var movie_id = "{{$episode->id}}"

                var loadAds = function() {
                    $.ajax({
                        type:'POST',
                        url:"{{ route('episode.get_ad_episode_videos') }}",
                        data:{id:movie_id, total: total_length,playedAdIds},
                        success:function(data){
                            var item_video = data.video;
                            if(item_video) {
                                $('#ad_video_src').attr("src", item_video.link);
                                total_length += parseInt(item_video.length)
                                console.log(total_length, item_video.length)
                                playedAdIds.push(item_video.id);
                                ad_video.muted = true;
                                ad_video.autoplay = true;
                                ad_video.load();
                                ad_video.currentTime = 0;
                                // video.style.display = "none";
                                // ad_video.style.display = "block";
                                
                            }else{
                                if(!isMobile) {
                                    $('.unHide').removeClass('d-none')
                                }
                                ad_video.style.display = "none";
                                video.style.display = "block";
                                video.load()
                                video.currentTime="{{$savedTime}}";
                                video.play()

                                $('#play-btn-icon').removeClass("fa-play");
                                $('#play-btn-icon').addClass("fa-pause");
                            }
                        }
                    });
                }

                loadAds();
                
                ad_video.onended = function() {
                    loadAds();
                };
            }


            function saveCurrentTime(currentTime){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var movie_id = "{{$episode->id}}"
                $.ajax({
                    type:'POST',
                    url:"{{ route('episode.currentTime') }}",
                    data:{currentTime:currentTime, id:movie_id, },
                    success:function(data){
                    }
                });
            }

            var viewTimeLimit = 60 * 1000;

            video.onplay = function (){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type:'POST',
                    url:"{{ route('profile_streaming_on') }}",
                });
                setTimeout(function () {
                    console.log("Post Create View");
                    $.ajax({
                        type:'POST',
                        url:"{{ route('create_episode_view') }}",
                        data:{
                            type : "episode",
                            id : movie_id
                        }
                    });
                }, viewTimeLimit);
            }

            window.onbeforeunload = function() {
                saveCurrentTime(video.currentTime);
                // send an AJAX request to delete the streaming session
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type:'POST',
                    url:"{{ route('profile_streaming_off') }}",
                });
            };

            ad_video.onclick = function () {
                    console.log("Click")
                    ad_video.play()
                }

            // For Progress bar
            const videoPlayer = document.querySelector('.row')
            const currentTimeElement = videoPlayer.querySelector('.current')
            const durationTimeElement = videoPlayer.querySelector('.duration')
            const progress = videoPlayer.querySelector('.volume')
            const progressBar = videoPlayer.querySelector('.video-progress-filled')
            //current time and duration
            const currentTime = () => {
                let currentMinutes = Math.floor(video.currentTime / 60)
                let currentSeconds = Math.floor(video.currentTime - currentMinutes * 60)
                let durationMinutes = Math.floor(video.duration / 60)
                let durationSeconds = Math.floor(video.duration - durationMinutes * 60)

                let a = `${currentMinutes}:${currentSeconds < 10 ? '0'+currentSeconds : currentSeconds}`
                let b = `${durationMinutes}:${durationSeconds}`
                currentTimeElement.innerHTML = a
                durationTimeElement.innerHTML = b
            }
            video.addEventListener('timeupdate', currentTime)
            //Progress bar
            video.addEventListener('timeupdate', () =>{
                const percentage = (video.currentTime / video.duration) * 100;
                if(percentage){
                    $('.volume').val(percentage);
                }
            })
            //change progress bar on click
            progress.addEventListener('click', (e) =>{
                const progressTime = (e.offsetX / progress.offsetWidth) * video.duration
                video.currentTime = progressTime
            })
        });
    </script>
@endsection