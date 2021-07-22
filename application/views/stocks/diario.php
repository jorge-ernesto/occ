<?php $this->load->view('template/header.php'); ?>
</head>
<body>
	<?php $this->load->view('template/menu.php'); ?>
	<div class="container internal-app">
		<!--<div class="row">
			<div class="col-md-4">-->
			<?php $this->load->view('tags/controlsSearchStock.php'); ?>

			<!--</div>
			<div class="col-md-8">-->
                <div class="container-search">
                    <br>
                    <div class="result-search">
                    </div>
                    <div class="panel panel-primary container-chart-station none">
                        <div class="panel-heading"><div class="panel-title" align="center">Gráficos</div></div>
                            <div class="panel-body">
                            <div class="titleStation" align="center"></div>
                            <div align="center"><h4><b>Ventas por estaciones</b></h4></div>
                            <div class="chartStation"></div><hr>
                            <div align="center"><h4><b>Cantidades por estaciones</b></h4></div>
                            <div class="chartStationQty"></div><hr>
                            <div align="center"><h4><b>Utilidades por estaciones</b></h4></div>
                            <div class="chartStationUtil"></div>
                        </div>
                    </div>
                    <br>
                    <div class="container-ss-station none">
                        <button class="btn btn-success btn-block btn-lg download-comb-stock" title="Generar información en Hoja de Cálculo"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>
                    </div>
                    <br>
                </div>
            <!--</div>
        </div>-->
	</div>

<?php $this->load->view('template/footer.php'); ?>