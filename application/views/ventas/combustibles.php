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
                    <!--<br>
                    <div align="center">
                        <img class="img-responsive" src="<?php echo base_url() ?>assets/images/logo-open.jpg" width="300px">
                    </div>-->
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