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

                <?php $this->load->view('tags/controlsSearch.php'); ?>
                <div class="row">&nbsp;</div>
                <hr>
                
                <div class="result-search">
                </div>
                <div class="table-responsive d-none">
                    <table id="table_id" class="table table-striped table-bordered table-hover dataTable" cellspacing="0" style="width: 100%;" role="grid"> <!-- <table class="table table-sm table-bordered table-striped table-hover table-responsive"> -->
                        <thead>
                            <tr>
                                <th>Correlativo</th>
                                <th>Comprobante</th>                                                
                                <th>RUC</th>
                                <th>Fecha</th> 
                                <th>EDS</th>                                 
                                <th>Dias</th>
                                <th>Vencimiento</th>
                                <th>Moneda</th>
                                <th>Importe</th>
                                <th>Pagos</th>                                
                                <th>Saldo</th>
                                <th>Fecha Saldo</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                                                
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
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
        });
    </script>

    <?php $this->load->view('template/footer.php'); ?>
    <?php $this->load->view('template/scripts.php'); ?>
</body>
</html>