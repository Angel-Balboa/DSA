<?php

include_once ("init.php");
use dsa\api\controller\sesion\CSesion;

$sesion = CSesion::inits(false);

if ($sesion->is_logged) {
  $url = $sesion->url2go();
  header("Location: $url");
}
?>
<!doctype html>
<html lang="en">
  <head>
  	<title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link href="static/assets/notiflix/dist/notiflix-3.2.2.min.css" rel="stylesheet"/>
	
	<link rel="stylesheet" href="static/css/login.css">

	</head>
	<body>
	<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-7 col-lg-5">
					<div class="wrap">
						<div class="img" style="background-image: url(static/img/bg-1.jpg);"></div>
						<div class="login-wrap p-4 p-md-5">
			      	<div class="d-flex">
			      		<div class="w-100">
			      			<h3 class="mb-4">Acceder</h3>
			      		</div>
			      	</div>
							<form id="frmLogin" class="form" method="post" action="">
			      		<div class="form-group mt-3">
			      			<input type="text" class="form-control" id="username-field" name="username" required>
			      			<label class="form-control-placeholder" for="username-field">Username</label>
			      		</div>
		            <div class="form-group">
		              <input id="password-field" name="password" type="password" class="form-control" required>
		              <label class="form-control-placeholder" for="password-field">Password</label>
		              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
		            </div>
		            <div class="form-group">
		            	<input type="submit" class="form-control btn btn-primary rounded submit px-3" value="Acceder" />
		            </div>
<!--		            <div class="form-group d-md-flex">-->
<!--		            	<div class="w-50 text-left">-->
<!--			            	<label class="checkbox-wrap checkbox-primary mb-0">Remember Me-->
<!--									  <input type="checkbox" checked>-->
<!--									  <span class="checkmark"></span>-->
<!--										</label>-->
<!--									</div>-->
<!--                </div>-->
									<div class="text-left">
										<a href="#">¿Reestablecer contraseña?</a>
									</div>
		          
		          </form>
		        </div>
		      </div>
				</div>
			</div>
		</div>
	</section>

	<script src="static/assets/js/core/jquery.min.js"></script>
  <script src="static/assets/js/core/popper.min.js"></script>
  <script src="static/assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="static/assets/notiflix/dist/notiflix-loading-aio-3.2.2.min.js"></script>
  <script src="static/assets/notiflix/dist/notiflix-3.2.2.min.js"></script>
  <script src="static/js/login.js"></script>

  <script>
    $(document).ready(function() {
      initd();
    });
  </script>

	</body>
</html>

