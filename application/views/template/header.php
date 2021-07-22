<!DOCTYPE html>
<head>
	<title><?php echo isset($title) ? $title : 'Page' ?> - <?php echo appName() ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!-- base_url() -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/plugins/datatables/datatables.min.css"/> <!-- CDN de DataTables -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css"> --> <!-- CDN de Bootstrap Select -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/PrintArea.min.css"> --> <!-- CDN de PrintArea -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/estilos.css">