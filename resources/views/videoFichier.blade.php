<!DOCTYPE html>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="{{url('/css/style.css')}}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/
font-awesome.min.css">
	<script type="text/javascript" src="/js/jquery.min.js"></script>
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
	
	<div style="text-align: center; margin-top: 20px;">
		<h1 style="font-size: 50px; color: #333;">Sous-Categorie de video disponible</h1>
	</div>

	<div class="ecran3">
		@foreach($resultatFinal as $vid)
			<div class="film">
				<div class="movie-image movie-image-1" style="background: url({{$vid->lienA}}); height: 150px; background-size: cover; background-repeat: no-repeat; object-fit: cover;">
					<span> <i class="fa fa-align-left"></i></span>
				</div>
				<div class="movie-info">
					<p class="titre">{{$vid->nomf}}</p>
					<p class="genre">{{$vid->auteur}}</p>
					<div class="movie-text">
						<p> SOMMAIRE</p>
						<div class="likes">
							<span><i class="fa fa-thumbs-up"></i>123</span>
							<span><i class="fa fa-comment"></i>5</span>
						</div>
					</div>
					<div class="summary">
						<p class="text">
							{{$vid->description}}
						</p>
					</div>
					<div class="actions">
						<button onclick="window.location='/video/categorie/sous-categorie/fichier/lire/{{$vid->id}}'" style="font-size: 10px;"><i class="fa fa-play"></i> Lire Video</button>
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
	<script type="text/javascript">
		function detailler(){
			$(this).css("height", "auto");
		}
	</script>
</body>
</html>