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
                            <div class="card-header">RUCs</div>
                            <div class="card-body text-primary">

                                <form method="post" action="<?php echo base_url() ?>index.php/rucs/store"> <!-- novalidate -->
                                    <div class="row form-group">
                                        <label for="nombre" class="col-form-label col-md-2">RUC:</label> <!-- col-md-4 -->
                                        <div class="col-md-5"> <!-- col-md-8 -->
                                            <input type="number" name="ruc" value="" id="ruc" class="form-control" 
                                            maxlength="11" 
                                            oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                            required> <!-- required -->                                            
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="descripcion" class="col-form-label col-md-2">Razon Social:</label>
                                        <div class="col-md-5">
                                            <input type="text" name="razon_social" value="" id="razon_social" class="form-control" required> <!-- required -->
                                        </div>
                                    </div>                                                                                                              
                                    <h4>
                                        <input type="submit" name="submit" value="Crear RUC" class="btn btn-primary"></input>
                                        <a class="btn btn-primary" href="<?php echo base_url() ?>index.php/users/edit/<?php echo $user[0]->sec_user_id; ?>">Atras</a>
                                    </h4>

                                    <input type="hidden" name="sec_user_id" value="<?php echo $user[0]->sec_user_id; ?>" id="id" class="form-control">
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