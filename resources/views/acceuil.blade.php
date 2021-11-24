<!DOCTYPE html>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="{{url('/css/style.css')}}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/
font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="{{url('/css/swiper.min.css')}}">
	
	<title>acceuil</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
<body class="containeur2">
	<div class="entete">
		<div>
			<img src="/images/logo3.PNG" style="width: 40%; height: 80%" class="center">
		</div>
		<div style="margin-right: 10px; margin-top: 20px;">
			<h1 style="width: 138px;"><a class="lien_auth" href="/home"> Se connecter</a></h1>
		</div>
	</div>
	<div class="ecran_principal">
		<div class="ecran2">
			<ul>
				<li>
					<a href="/video/categorie/{{$resultatFinal[0]['idType']}}" > <i class="fa fa-video-camera"></i>
						<span> Video </span>
					</a>
				</li>

				<li>
					<a href="/audio/{{$resultatFinal[1]['idType']}}" > <i class="fa fa-music"></i>
						<span> Musique </span>
					</a>
				</li>

				<li>
					<a href="/lecture/{{$resultatFinal[2]['idType']}}" > <i class="fa fa-book"></i>
						<span> Lecture </span>
					</a>
				</li>
			</ul>
		</div>
		<div class="partiel">

			
			<div class="swiper_container">
				<div>
					<h1 style="color: white">liste video</h1>
				</div>
				<div class="swiper-wrapper">
					
						@foreach($resultatFinal[0]['donnee'] as $vid)
							<div class="swiper-slide">
								<div class="film1	">
									<div class="movie-image movie-image-1" style="background: url({{$vid->lienA}}); background-size: cover; background-repeat: no-repeat; object-fit: cover">
										<span> <i class="fa fa-align-left"></i></span>
									</div>
									<div class="movie-info">
										<p class="titre">{{$vid->auteur}}</p>
										<div class="actions">
											<button onclick="window.location='/video/item/{{$vid->id}}'"><i class="fa fa-play"></i> Voir video</button>
										</div>
									</div>
								</div>
							</div>
						@endforeach
				</div>
				<div class="btnSP">
					<div class="swiper-button-prev2" style="color: white; height: 60px; width: 60px;"> <button style="color: white">Precedent</button> </div>
					<div class="swiper-pagination2"></div>
    			 	<div class="swiper-button-next2" style="color: white; height: 60px; width: 60px;"> <button style="color: white">Suivant</button> </div>
				</div>
				
			</div>

			<!--
			<div class="swiper_container1">
				<div>
					<h1 style="color: white">liste audio</h1>
				</div>
				<div class="swiper-wrapper">
					
						<div class="swiper-slide">
						 	<div class="film1">
								<div class="movie-image movie-image-1" style="background: background-size: cover; background-repeat: no-repeat; object-fit: cover;">
									<span> <i class="fa fa-align-left"></i></span>
								</div>
								<div class="movie-info">
									<p class="titre"></p>
									<div class="actions">
										<button onclick="window.location='/audio/item/'"><i class="fa fa-play"></i> Voir audio</button>
									</div>
								</div>
							</div>
						</div>
					
				</div>
				<div class="btnSP">
					<div class="swiper-button-prev1" style="color: black; height: 60px; width: 60px;"><button style="color: white">Precedent</button></div>
					<div class="swiper-pagination1"></div>
    				<div class="swiper-button-next1" style="color: black; height: 60px; width: 60px;"><button style="color: white">Suivant</button></div>
    				
				</div>
			</div>
			<div class="swiper_container2">
				<div>
					<h1 style="color: white">liste lecture</h1>
				</div>

				<div class="swiper-wrapper">
					
						<div class="swiper-slide">
						 	<div class="film1">
								<div class="movie-image movie-image-1" style="background: ; background-size: cover; background-repeat: no-repeat; object-fit: cover;">
									<span> <i class="fa fa-align-left"></i></span>
								</div>
								<div class="movie-info">
									<p class="titre"></p>
									<div class="actions">
										<button onclick="window.location='/lecture/item/'"><i class="fa fa-play"></i> lire livre</button>
									</div>
								</div>
							</div>
						</div>
				</div>
				<div class="btnSP">
					<div class="swiper-button-prev3" style="color: black; height: 60px; width: 60px;"><button style="color: white">Precedent</button></div>
					<div class="swiper-pagination3"></div>
    				<div class="swiper-button-next3" style="color: black; height: 60px; width: 60px;"><button style="color: white">Suivant</button></div>
    				
				</div>
				
			</div> -->
		</div>
	</div>

	<script type="text/javascript" src="/js/idangerous.swiper-2.0.min.js"></script>

	<script type="text/javascript">
		var swiper = new Swiper('.swiper_container', {
			effect: 'coverflow',
			spaceBetween: 10,
			centeredSlides: true,
			centeredSlidesBounds: true,
			slidesOffsetBefore: 50 ,
			slidesOffsetAfter: 10,
			grabCursor: true,
			centeredSlides: true,
			slidesPerView: 'auto',
			watchOverflow: false,
			width: 100,
			updateOnWindowResize: true,
			coverflowEffect: {
				rotate: 50,
				stretch: 0,
				depth: 100,
				modifier: 1,
				slideShadows: true,
			},
			pagination: {
				el: '.swiper-pagination2',
			},
			navigation: {
				nextEl: '.swiper-button-next2',
    			prevEl: '.swiper-button-prev2',
			},
		});
	</script>

	<script type="text/javascript">
		var swiper = new Swiper('.swiper_container1', {
			effect: 'coverflow',
			spaceBetween: 10,
			centeredSlides: true,
			centeredSlidesBounds: true,
			slidesOffsetBefore: 50 ,
			slidesOffsetAfter: 10,
			grabCursor: true,
			centeredSlides: true,
			slidesPerView: 'auto',
			watchOverflow: false,
			width: 100,
			updateOnWindowResize: true,
			coverflowEffect: {
				rotate: 50,
				stretch: 0,
				depth: 100,
				modifier: 1,
				slideShadows: true,
			},
			pagination: {
				el: '.swiper-pagination1',
			},
			navigation: {
				nextEl: '.swiper-button-next1',
    			prevEl: '.swiper-button-prev1',
			},
		});
	</script>

	<script type="text/javascript">
		var swiper = new Swiper('.swiper_container2', {
			effect: 'coverflow',
			spaceBetween: 10,
			centeredSlides: true,
			centeredSlidesBounds: true,
			slidesOffsetBefore: 50 ,
			slidesOffsetAfter: 10,
			grabCursor: true,
			centeredSlides: true,
			slidesPerView: 'auto',
			watchOverflow: false,
			width: 100,
			updateOnWindowResize: true,
			coverflowEffect: {
				rotate: 50,
				stretch: 0,
				depth: 100,
				modifier: 1,
				slideShadows: true,
			},
			pagination: {
				e1: '.swiper-pagination3',
			},
			navigation: {
				nextE1: '.swiper-button-next3',
    			prevE1: '.swiper-button-prev3',
			},
		});
	</script>
</body>
</html>