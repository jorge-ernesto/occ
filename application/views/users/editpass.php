<?php $this->load->view('template/header.php'); ?>
<body id="page-top">    
    <?php $this->load->view('template/sidebar.php'); ?>
    <?php $this->load->view('template/menu.php'); ?>

    <!-- Container -->
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4"> <!-- mb-4 -->
            <h1 class="h3 mb-0 text-gray-800">Cambiar Contrase&ntilde;a:</h1>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <form method="post" action="<?php echo base_url() ?>index.php/users/updatepass"> <!-- novalidate -->
                    <div class="col-lg-12 form-group">
                        <label>Contrase&ntilde;a:</label>
                        <input type="password" name="password" value="" id="password" class="form-control" required> <!-- required -->                        
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Confirmar contraseña:</label>
                        <input type="password" name="password_confirmation" value="" id="password" class="form-control" required> <!-- required -->                        
                    </div>
                    <!-- <div class="col-lg-12 form-group"> -->
                        <input type="submit" name="submit" value="Cambiar contrase&ntilde;a" class="btn btn-primary" style="width:49%;"></input>
                        <a class="btn btn-primary" href="<?php echo base_url() ?>index.php/users/view" style="width:49%;">Atr&aacute;s</a>
                    <!-- </div> -->

                    <input type="hidden" name="sec_user_id" value="<?php echo $user[0]->sec_user_id; ?>" class="form-control">
                </form>
                <div class="row">&nbsp;</div>

            </div>
        </div>
                  
    </div>
    <!-- Cerrar Container --> 

    <?php $this->load->view('template/footer.php'); ?>
    <?php $this->load->view('template/scripts.php'); ?>
    <script src="<?php echo base_url() ?>assets/js/rucs.js"></script>
</body>
</html>