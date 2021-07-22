<?php $this->load->view('template/header.php'); ?>
<body class="bg-gradient-primary">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-xl-6 col-lg-12 col-md-9">
				
				<div class="card o-hidden border-0 shadow-lg my-5">
					<div class="card-body p-0">
						<div class="p-5">
							<div class="text-center">
								<h1 class="h4 text-gray-900 mb-4"><img src="https://cdn.opensysperu.com/img/logocu.png" style="max-width: 100%; max-height: 100%;" /></h1>
								<h1 class="h4 text-gray-900 mb-4">&Aacute;rea de Cliente</h1>
							</div>							
							
							<form method="post" id="formularioLogin" class="user"> <!-- novalidate -->
								<div class="form-group">
									<input type="email" name="username" id="username" class="form-control form-control-user" placeholder="Email" autofocus required />
								</div>
								<div class="form-group">
									<input type="password" name="password" id="password" class="form-control form-control-user" placeholder="Contrase&ntilde;a" required />
								</div>
								<input type="submit" id="signIn" class="btn btn-primary btn-user btn-block btn-login" value="Iniciar sesi&oacute;n" />
							</form>
							
							<hr>
							<div class="text-center">
								<a class="small" target="_blank" href="https://opensysperu.com/">Open Comb Systems</a>
							</div>							
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

  <?php $this->load->view('template/scripts.php'); ?>
</body>
</html>