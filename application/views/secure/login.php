<?php $this->load->view('template/header.php'); ?>
</head>
<body class="bg-login">
	<?php $this->load->view('template/menu.php'); ?>
	
	<div class="container">
		<div class="row justify-content-center mt-3 pt-2"> <!-- mt-5 pt5 -->
			<div class="col-md-12"> <!-- col-md-7 -->

					<div class="card border-primary text-center">
						<div class="card-header">Acceso Clientes 👋</div>
						<div class="card-body">

							<form method="post" id="formularioLogin"> <!-- novalidate -->
									<div class="form-group col-sm-6">
										<input type="email" name="username" value="" id="username" class="form-control" placeholder="Email" autofocus required> <!-- required -->
									</div>
									<div class="form-group col-sm-6">
										<input type="password" name="password" value="" id="password" class="form-control" placeholder="Contraseña" required> <!-- required -->
									</div>
									<h4 class="form-group col-sm-6">
										<button type="submit" id="signIn" class="btn btn-lg btn-primary btn-block btn-login">Acceder</button>
									</h4>
							</form>

						</div>
					</div>

					<?php $this->load->view('template/footer.php'); ?>

			</div><!-- .col -->
		</div><!-- .row -->
	</div><!-- .container -->

	<?php $this->load->view('template/scripts.php'); ?>
</body>
</html>