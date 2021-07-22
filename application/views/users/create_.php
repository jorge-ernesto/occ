<?php $this->load->view('template/header.php'); ?>
</head>
<body>
	<?php $this->load->view('template/menu.php'); ?>
	
    <div class="container">
        <div class="row justify-content-center mt-3 pt-2"> <!-- mt-5 pt5 -->
            <div class="col-md-12 disabled-padding"> <!-- col-md-7 -->            

                <div id="formularioRegistros">
                        <!-- <h3 class="text-primary"><?php echo $title; ?></h3> -->

                        <div class="card border-primary">
                            <div class="card-header">Usuarios</div>
                            <div class="card-body text-primary">

                                <form method="post" action="<?php echo base_url() ?>index.php/users/store"> <!-- novalidate -->
                                    <div class="row form-group">
                                        <label for="nombre" class="col-form-label col-md-2">Nombre:</label> <!-- col-md-4 -->
                                        <div class="col-md-5"> <!-- col-md-8 -->
                                            <input type="text" name="name" value="" id="name" class="form-control" required> <!-- required -->
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="descripcion" class="col-form-label col-md-2">Email:</label>
                                        <div class="col-md-5">
                                            <input type="email" name="email" value="" id="email" class="form-control" required> <!-- required -->
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="descripcion" class="col-form-label col-md-2">Contraseña:</label>
                                        <div class="col-md-5">
                                            <input type="password" name="password" value="" id="password" class="form-control" required> <!-- required -->
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="tipoDocumento" class="col-form-label col-md-2">Admin:</label>
                                        <div class="col-md-5">                                            
                                            <select name="isadmin" id="isadmin" class="form-control">
                                                <option value="0" selected>NO</option> 
                                                <option value="1">SI</option> 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="tipoDocumento" class="col-form-label col-md-2">Activo:</label>
                                        <div class="col-md-5">
                                            <select name="isactive" id="isactive" class="form-control">
                                                <option value="0">NO</option>  
                                                <option value="1" selected>SI</option>                                                                                                                                              
                                            </select>
                                        </div>
                                    </div>
                                    <h4>
                                        <input type="submit" name="submit" value="Crear Usuario" class="btn btn-primary"></input>
                                        <a class="btn btn-primary" href="<?php echo base_url() ?>index.php/users/view">Atras</a>
                                    </h4>

                                    <!-- <input type="hidden" name="sec_user_id" value="" id="id" class="form-control"> -->
                                </form>

                            </div>
                        </div>
                </div>    

            <?php $this->load->view('template/footer.php'); ?>

            </div><!-- .col -->
        </div><!-- .row -->
    </div><!-- .container -->

<?php $this->load->view('template/scripts.php'); ?>
<script src="<?php echo base_url() ?>assets/js/users.js"></script>