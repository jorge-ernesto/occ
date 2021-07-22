<?php $this->load->view('template/header.php'); ?>
</head>
<body class="bg-login">
	<?php $this->load->view('template/menu.php'); ?>
	<div class="container">
		<div class="row">
			<div class="col-md-4 in-credentials">
				<h3>Acceso</h3>
				<br>
				<form>
					<div class="form-group">
						<label for="username">Usuario</label>
						<input type="text" placeholder="Nombre de usuario" id="username" class="form-control keypress">
					</div>
					<div class="form-group">
						<label for="password">Contraseña</label>
						<input type="password" placeholder="Contraseña" id="password" class="form-control keypress">
					</div>
					<div class="msg-login"></div>
					<button class="btn btn-primary btn-block btn-login" type="button"><span class="glyphicon glyphicon-log-in"></span> Iniciar Sesión</button>
				</form>
			</div>
			<div class="col-md-8">
				<br>
				<div class="jumbotron container-desc">
					<div class="row">
						<div class="col-md-6">
							<div align="center" style="margin-top: 18px;">
								<img class="img-responsive" src="<?php echo base_url() ?>assets/images/logo_nuevo_ocs.png" width="600px">
							</div>
						</div>
						<div class="col-md-6">
							<h2><?php echo appName() ?></h2>
							<p>Consulta la información de tu empresa en tiempo real desde un dispositivo móvil.</p>
							<p><a role="button" class="btn btn-primary btn-lg" href="#">Más información</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>