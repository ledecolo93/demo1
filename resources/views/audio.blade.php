<!DOCTYPE html>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="{{url('/css/style.css')}}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/
font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="{{url('/css/swiper.min.css')}}">
	<title>acceuil</title>
</head>
<body class="containeur2">
	<div class="entete">
		<div>
			<img src="/images/logo3.PNG" class="center">
		</div>
		<div>
			<h1> <a class="lien_auth"href="/home"> Se connecter</a></h1>
		</div>
	</div>
	
	<div class="ecran3">
		
		@foreach($audio as $aud)
		 	<div class="film">
				<div class="movie-image movie-image-1" style="background: url({{$aud->lien_affiche}}); height: 200px; background-size: cover; background-repeat: no-repeat; object-fit: cover">
					<span> <i class="fa fa-align-left"></i></span>
				</div>
				<div class="movie-info">
					<p class="titre">{{$aud->titre}}</p>
					<p class="genre">{{$aud->categorie}}</p>
					<div class="movie-text">
						<p> SOMMAIRE</p>
						<div class="likes">
							<span><i class="fa fa-thumbs-up"></i>123</span>
							<span><i class="fa fa-comment"></i>5</span>
						</div>
					</div>
					<div class="summary">
						<p class="text">
							{{$aud->description}}
						</p>
						<a href="javascript:void(0)" class="readmore-btn" > lire </a>
					</div>
					<div class="actions">
						<button onclick="window.location='/audio/item/{{$aud->id}}'"><i class="fa fa-play"></i> Voir audio</button>
						<div class="actions-more">
							<i class="fa fa-save"></i>
							<i class="fa fa-bookmark"></i>
							<i class="fa fa-share-alt"></i>
						</div>
					</div>
				</div>
			</div>
		@endforeach

	</div>
	<div class="pied"></div>
</body>
</html>