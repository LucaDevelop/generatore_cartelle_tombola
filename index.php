<?php
include 'config.inc.php';
?>
<!doctype html>
<html lang="it">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Generatore Cartelle Tombola">
    <meta name="author" content="LucaDevelop">
    <title>Generatore Cartelle Tombola</title>

    <!-- Bootstrap core CSS -->	
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<meta name="theme-color" content="#563d7c">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="cover.css" rel="stylesheet">
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
  </head>
  <body class="text-center">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
  <header class="masthead mb-auto">
    <div class="inner">
      <h3 class="masthead-brand">Generatore Cartelle Tombola</h3>
      <nav class="nav nav-masthead justify-content-center">
        <a class="nav-link" href="<?php echo GITHUB_REPO; ?>">GitHub <i class="fab fa-github"></i></a>
        <a class="nav-link" href="<?php echo TELEGRAM_URL; ?>">Contattami <i class="fab fa-telegram-plane"></i></a>
      </nav>
    </div>
  </header>

  <main role="main" class="inner cover">
    <h1 class="cover-heading">Quante cartelle vuoi generare?</h1>
    <form>
		<div class="form-group">
			<input type="number" min="1" max="100" class="form-control" id="cartnumber" placeholder="Inserisci un numero">
			<div class="invalid-feedback">Inserire un numero da 1 a 10</div>
		</div>
		<div class="g-recaptcha" data-sitekey="6Lecbs0UAAAAAC5UU1FUpViCv-nucTWIhF6C2H_j"></div>
		<br>
		<button type="button" id="generatebtn" class="btn btn-primary">Genera</button>
	</form>
  </main>
  <br>
  <div id="results"></div>
  <footer class="mastfoot mt-auto">
    <div class="inner">
      <p></p>
    </div>
  </footer>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script>
$('#generatebtn').click(function(){
	var cart_number = $('#cartnumber').val();
	if(!isNaN(cart_number) && cart_number >= 1 && cart_number <= 10){
		var captcha_response = grecaptcha.getResponse();
		if(captcha_response == ''){
			alert("Completare la verifica del captcha");
		}
		else {
			$('#results').load("generateresults.php", {captcha_response:captcha_response, cart_number:cart_number},function(){grecaptcha.reset()});
		}
	}
	else{
		$('#cartnumber').addClass('is-invalid');
	}
});
</script>
</body>
</html>