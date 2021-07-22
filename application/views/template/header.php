<!DOCTYPE html>
<head>
	<title><?php echo isset($title) ? $title : 'Page' ?> - <?php echo appName() ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!-- base_url() -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/af-2.3.0/b-1.5.2/b-colvis-1.5.2/b-flash-1.5.2/b-html5-1.5.2/b-print-1.5.2/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.4.0/r-2.2.2/rg-1.0.3/rr-1.2.4/sc-1.5.0/sl-1.2.6/datatables.min.css"/> <!-- CDN de DataTables -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/css/bootstrap-select.min.css"> <!-- CDN de Bootstrap Select -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/PrintArea.min.css"> <!-- CDN de PrintArea -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/estilos.css">