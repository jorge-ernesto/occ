<?php $this->load->view('template/header.php'); ?>
<body id="page-top">    
    <?php $this->load->view('template/sidebar.php'); ?>
    <?php $this->load->view('template/menu.php'); ?>

    <!-- Container -->
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4"> <!-- mb-4 -->
            <h1 class="h3 mb-0 text-gray-800"><?php echo $title; ?></h1>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <?php $this->load->view('tags/controlsSearchSale.php'); ?>
                <div class="row">&nbsp;</div>
                <hr>
                
                <div class="result-search">
                </div>
                <div class="card shadow container-chart-station d-none">
                    <div class="card-header bg-primary text-white"><div align="center">Gráficos</div></div>
                        <div class="card-body">
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
                <div class="container-ss-station d-none">
                    <button class="btn btn-primary btn-block btn-lg download-comb-sales" title="Generar información en Hoja de Cálculo"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>
                </div>               
                <div class="row">&nbsp;</div>
                
            </div>
        </div>
                  
    </div>
    <!-- Cerrar Container -->

    <script type="text/javascript">
        $(function () {
            $.datepicker.setDefaults($.datepicker.regional["es"]);
            $("#start-date-request").datepicker({
                firstDay: 1,
                maxDate: '0',
                dateFormat: 'dd/mm/yy',
            });
            $("#end-date-request").datepicker({
                firstDay: 1,
                maxDate: '0',
                dateFormat: 'dd/mm/yy',
            });
            $("#_start-date-request").datepicker({
                firstDay: 1,
                maxDate: '0',
                dateFormat: 'dd/mm/yy',
            });
            $("#_end-date-request").datepicker({
                firstDay: 1,
                maxDate: '0',
                dateFormat: 'dd/mm/yy',
            });
        });
    </script>

    <?php $this->load->view('template/footer.php'); ?>
    <?php $this->load->view('template/scripts.php'); ?>
</body>
</html>