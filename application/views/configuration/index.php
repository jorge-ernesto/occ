<?php $this->load->view('template/header.php'); ?>
</head>
<body>
	<?php $this->load->view('template/menu.php'); ?>
	<div class="container internal-app">

		<div class="row">
			<div class="col-md-3">
				<?php $this->load->view('configuration/submenu.php'); ?>
			</div>
			<div class="col-md-9">
				<?php $this->load->view('partials/client_admin.php'); ?>
			</div>
		</div>
	</div>

<?php $this->load->view('template/footer.php'); ?>