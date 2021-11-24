<!DOCTYPE html>	

<html>
<head>
	<link rel="stylesheet" type="text/css" href="{{url('/css/style.css')}}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/
font-awesome.min.css">
<script src="/js/jquery.min.js"></script>
<script src="/js/owl.caroussel.js"></script>
	<title>Musique</title>

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
	
	<div class="ecran4" style="">
		
		@isset($audio)
			@foreach($audio as $aud)
			 	<div class="espace_lecteur">
			 		<div class="lecteur">
			 			<video id="myVideo" src="{{$aud->lien_fichier}}" style="background: url({{$aud->lien_affiche}}) no-repeat center; background-size: cover;" autoplay="true" playsinline controls="true" loop muted></video>
			 		</div>
			 	</div>
			@endforeach
		@endisset

		<div class="slid owl-carousel">

			@foreach($audios as $aud)
			 	<div class="film2">
					<div class="movie-image movie-image-1" style="background: url({{$aud->lien_affiche}}); height: 200px; background-size: cover; background-repeat: no-repeat;">
						<span> <i class="fa fa-align-left"></i></span>
					</div>
					<div class="movie-info">
						<p class="titre" style="color: yellow;">{{$aud->titre}}</p>
						<div class="actions">
							<button onclick="window.location='/audio/item/{{$aud->id}}'"><i class="fa fa-play"></i> Voir audio</button>
						</div>
					</div>
				</div>
			@endforeach
			
		</div>

	</div>

	<script type="text/javascript">

		$(".slid").owlCarousel({
			loop: true,
			autoplay: true,
			autoplayTimeout: 5000,
			autoplayHoverPause: true,

		});

		var vid = document.getElementById("myVideo");
			var fillBar = document.getElementById("fill");
			var currentTime = document.getElementById("currentTime");
			var volumeSlider = document.getElementById("volume");

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
				$("#speaker").attr("src", "/images/.png");
			}
		}
	</script>
</body>
</html>