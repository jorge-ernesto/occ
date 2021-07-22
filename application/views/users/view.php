<?php $this->load->view('template/header.php'); ?>
<body id="page-top">    
    <?php $this->load->view('template/sidebar.php'); ?>
    <?php $this->load->view('template/menu.php'); ?>

    <!-- Container -->
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?php echo $title; ?></h1>
        </div>       

        <?php
            //Si existen las sesiones flashdata que se muestren
            if($this->session->flashdata('correcto')){
                echo "<div class='alert alert-success' role='alert'>{$this->session->flashdata('correcto')}</div>";
            }                                    
            if($this->session->flashdata('incorrecto')){
                echo "<div class='alert alert-danger' role='alert'>{$this->session->flashdata('incorrecto')}</div>";                        
            } 
            if($this->session->flashdata('database_error')){
                $errors = $this->session->flashdata('database_error');
                echo "<div class='alert alert-danger' role='alert'>{$errors['message']}</div>";
            }                                       
        ?>

        <!-- <div class="row">
            <div class="col-lg-12">                
            </div>
        </div>  -->
                
        <div class="table-responsive">
            <table id="table_id" class="table table-striped table-bordered table-hover dataTable" cellspacing="0" style="width: 100%;" role="grid"> <!-- <table class="table table-sm table-bordered table-striped table-hover table-responsive"> -->
                <thead> <!-- class="thead-dark" -->
                    <tr>
                        <th>ID</th>                                                
                        <th>Nombre</th>
                        <th>Email</th>                                                
                        <th>Admin</th>
                        <th>Activo</th>
                        <th style="width: 0%;" class="noExport"></th>                                                
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
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>
            <a id="wea" class="btn btn-primary mr-2" href="<?php echo base_url() ?>index.php/users/create">Crear Usuario</a>
        </h4>        
    </div>

    <?php $this->load->view('template/footer.php'); ?>
    <?php $this->load->view('template/scripts.php'); ?>
    <script src="<?php echo base_url() ?>assets/js/users.js"></script>
</body>
</html>