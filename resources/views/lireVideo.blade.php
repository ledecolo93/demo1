<!DOCTYPE html>
<?php
	//dd($resultatFinal);
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="{{url('/css/style.css')}}">
	<link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/
	font-awesome.min.css">
	<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script src="/js/owl.caroussel.js"></script>
	<title>acceuil</title>

</head>
<body class="containeur2">
	<div class="entete">
		<div>
			<img src="/images/logo3.PNG" class="center">
		</div>
		<div>
			<h1><a class="lien_auth" href="/home"> Se connecter</a></h1>
		</div>
	</div>
	
	<div class="ecran4">
		
		@isset($resultatFinal)
			 	<div class="espace_lecteur">
			 		<div class="lecteur">
			 			<video width="320" height="240" id="my-video" class="video-js vjs-big-play-centered" controls preload="metadata" data-setup="{}">
			 				<source src="{{$resultatFinal->lienF}}" type="video/mp4">
			 			</video>
			 		</div>
			 	</div>
		@endisset

	</div>

	<script type="text/javascript">

		var theVideo = document.getElementById("my-video");
		document.onkeydown = function(event) {
		    switch (event.keyCode) {
			    case 37:
		            event.preventDefault();
		              
		            vid_currentTime = theVideo.currentTime;
		            theVideo.currentTime = vid_currentTime - 5;
		        break;
		         

		        case 39:
		            event.preventDefault();
		              
		            vid_currentTime = theVideo.currentTime;
		            theVideo.currentTime = vid_currentTime + 5;
		        break;
		         
		    }
		};

		function CallAction1() {
			switch (event.keyCode) {
			    case 37:
		            event.preventDefault();
		              
		            vid_currentTime = theVideo.currentTime;
		            theVideo.currentTime = vid_currentTime - 5;
		        break;
		         

		        case 39:
		            event.preventDefault();
		              
		            vid_currentTime = theVideo.currentTime;
		            theVideo.currentTime = vid_currentTime + 5;
		        break;
		         
		    }
		}


		function CallAction2() {
			switch (event.keyCode) {
			    case 37:
		            event.preventDefault();
		              
		            vid_currentTime = theVideo.currentTime;
		            theVideo.currentTime = vid_currentTime - 5;
		        break;
		         

		        case 39:
		            event.preventDefault();
		              
		            vid_currentTime = theVideo.currentTime;
		            theVideo.currentTime = vid_currentTime + 5;
		        break;
		         
		    }
		}

		var vid = document.getElementById("myVideo");
		var fillBar = document.getElementById("fill");
		var fillBar1 = document.getElementById("fill1");
		var currentTime = document.getElementById("currentTime");
		var volumeSlider = document.getElementById("volume");
		var volumeCourant = 0 ;

		$(".slid").owlCarousel({
			loop: true,
			autoplay: true,
			autoplayTimeout: 5000,
			autoplayHoverPause: true,

		}); 

		/*window.onload = function(){
			vid.play();
			$("#playBtn").attr("src","/images/pause.png");
			volumeSlider.value = vid.volume;
			fillBar1.value = 0;

		}

		function niveauFilm(){


		}

		function mettrePause(){
			if (vid.paused) {
				vid.play();
				$("#playBtn").attr("src","/images/pause.png")
			} else {
				vid.pause();
				$("#playBtn").attr("src","/images/play.png")
			}
		}

		function fullScreen(){
			var mediaElement = document.getElementById('myVideo');
			mediaElement.seekable.start();  // Returns the starting time (in seconds)
			mediaElement.seekable.end();    // Returns the ending time (in seconds)
			mediaElement.currentTime = 122; // Seek to 122 seconds
			mediaElement.played.end();
		}

		function playOrpause() {
		
			if (vid.paused) {
				vid.play();
				$("#playBtn").attr("src","/images/pause.png")
			} else {
				vid.pause();
				$("#playBtn").attr("src","/images/play.png")
			}
		}

		var vid = document.getElementById("myVideo");
		vid.addEventListener('timeupdate', function(){
				var position = vid.currentTime / vid.duration;
				fillBar.style.width = position * 100 +'%';
				convertTime(Math.round(vid.currentTime));

				if (vid.ended) {
					$("#playBtn").attr("src", "/images/play.png")
				}
		});

		function convertTime(seconds)
		{
			var min = Math.floor(seconds / 60);
			var sec = seconds % 60;

			min = (min < 10) ? "0" + min : min;
			sec = (sec < 10) ? "0" + sec : sec;
			currentTime.textContent = min + ":" + sec;
			totalTime(Math.round(vid.duration));
		}

		function totalTime(seconds){
			var min = Math.floor(seconds / 60);
			var sec = seconds % 60;

			min = (min < 10) ? "0" + min : min;
			sec = (sec < 10) ? "0" + sec : sec;
			currentTime.textContent += "/" + min + ":" + sec;
		}

		function changeVolume(){
			vid.volume = volumeSlider.value;
			if (volumeSlider.value == 0) {
				$("#speaker").attr("src", "/images/mute.png");
			} else {
				$("#speaker").attr("src", "/images/speaker.png");
			}
		}

		function putMute(){
			if (volumeCourant == 0) {
				$("#speaker").attr("src", "/images/mute.png");
				volumeCourant = vid.volume;
				volumeSlider.value = 0;
				vid.volume = 0;
			} else {
				$("#speaker").attr("src", "/images/speaker.png");
				vid.volume = volumeCourant;
				volumeSlider.value = volumeCourant;
				volumeCourant = 0;

			}

		}*/
	</script>
	<script src="https://vjs.zencdn.net/7.11.4/video.min.js"></script>
</body>
</html>