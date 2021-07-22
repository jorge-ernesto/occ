<?php $this->load->view('template/header.php'); ?>
</head>
<body>
	<?php $this->load->view('template/menu.php'); ?>
	
    <div class="container">
        <div class="row justify-content-center mt-3 pt-2"> <!-- mt-5 pt5 -->
            <div class="col-md-12 disabled-padding"> <!-- col-md-7 -->            

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

                <div id="listadoRegistros">
                        <h3 class="text-primary"><?php echo $title; ?></h3>

                        <div class="card border-primary"> <!-- card bg-light -->
                            <div class="card-header">Usuarios</div>
                            <div class="card-body text-primary"> <!-- card-body -->

                                <h5 class="card-title">Listado de usuarios</h5>
                                <div class="table-responsive">
                                    <table id="table_id" class="table table-bordered table-striped"> <!-- <table class="table table-sm table-bordered table-striped table-hover table-responsive"> -->
                                        <thead> <!-- class="thead-dark" -->
                                            <tr>
                                                <th>ID</th>                                                
                                                <th>Nombre</th>
                                                <th>Email</th>                                                
                                                <th>Admin</th>
                                                <th>Activo</th>
                                                <th>Editar</th>                                                
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
                        </div>
                </div>

            <?php $this->load->view('template/footer.php'); ?>

            </div><!-- .col -->
        </div><!-- .row -->
    </div><!-- .container -->

<?php $this->load->view('template/scripts.php'); ?>
<script src="<?php echo base_url() ?>assets/js/users.js"></script>