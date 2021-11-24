@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    
                    

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function determiner_cat(this_select){
        if (this_select.value == "video") {
            $('#categorie')
                .empty()
                .append('<option selected="selected" value="0">choisir une categorie</option>')
                .append('<option value="musique">musique</option>')
                .append('<option value="film">film</option>')
                .append('<option value="serie">serie</option>')
                .append('<option value="education">education</option>')
                .append('<option value="sport">sport</option>')
        } else if (this_select.value == "audio") {
            $('#categorie')
                .empty()
                .append('<option selected="selected" value="0">choisir une categorie</option>')
                .append('<option value="musique">musique</option>')
                .append('<option value="education">education</option>')
                .append('<option value="journal">journal</option>')
                .append('<option value="conseils">conseils</option>')
        } else if (this_select.value == "lecture") {
            $('#categorie')
                .empty()
                .append('<option selected="selected" value="0">choisir une categorie</option>')
                .append('<option value="bible">bible</option>')
                .append('<option value="journal">journal</option>')
                .append('<option value="education">education</option>')
                .append('<option value="roman">roman</option>')
        } else {
            $('#categorie')
                .empty()
        }
    }

    $(document).ready(function(){
        $('#upload_form').on('submit', function(event){
            var typ = $('#type');
            var cat = $('#categorie');
            var fic = $('#fichier');
            var aff = $('#affiche');
            var tit = $('#titre');
            var descr = $('#description');
            var affVal;
            var ficVal;
            var etat = 0;
            var valTyp;

            if(typ.children("option:selected").val() == '0' || cat.children("option:selected").val() == '0'){
                alert("veuillez choisir un type ou une categorie");
            }else if (fic.get(0).files.length == 0 || aff.get(0).files.length == 0 || $.trim(tit.val()) == "" || $.trim(descr.val()) == "" ) {
                alert("veuillez entrer le fichier correspondant a film ou le titre ou la description");
            } else {
                if(aff.get(0).files.length == 0){
                    affVal == "/affiches/filmdefaut.jpg";
                    etat = 1;
                } else {
                    affVal = aff[0].files[0].name;
                }

                event.preventDefault();
                $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                }

                });
                $.ajax({
                    url: "{{url('/home/enreg')}}",
                    method: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function()
                    {

                    }
                });
            }
        });
    });

   /* $("#btnClick").click(function(e){
        var typ = $('#type');
        var cat = $('#categorie');
        var fic = $('#fichier');
        var aff = $('#affiche');
        var tit = $('#titre');
        var descr = $('#description');
        var affVal;
        var ficVal;
        var etat = 0;
        var valTyp;

        if(typ.children("option:selected").val() == '0' || cat.children("option:selected").val() == '0'){
            alert("veuillez choisir un type ou une categorie");
        }else if (fic.get(0).files.length == 0 || $.trim(tit.val()) == "" || $.trim(descr.val()) == "" ) {
            alert("veuillez entrer le fichier correspondant a film ou le titre ou la description");
        } else {
            if(aff.get(0).files.length == 0){
                affVal == "/affiches/filmdefaut.jpg";
                etat = 1;
            } else {
                affVal = aff[0].files[0].name;
            }

            if (typ.children("option:selected").val() == '1') 
                valTyp = "video";
            if (typ.children("option:selected").val() == '2') 
                valTyp = "audio";
            if (typ.children("option:selected").val() == '3') 
                valTyp = "lecture";

            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                }

            });

            $.ajax({
                url: "{{url('/home/enreg')}}",
                method: 'POST',
                data: {
                    type: valTyp,
                    categorie: cat.children("option:selected").val(),
                    fichier: fic,
                    affiche: aff,
                    titre: tit.val(),
                    description: descr.val(),
                    etat: etat,
                },
                success: function(result){
                    console.log(result);
                },
                error: function (data) {
                    console.log(data);
                }
            });
        }
    });*/ 


</script>
@endsection
