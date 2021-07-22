<?php $this->load->view('template/header.php'); ?>
<body id="page-top">    
    <?php $this->load->view('template/sidebar.php'); ?>
    <?php $this->load->view('template/menu.php'); ?>

    <!-- Container -->
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4"> <!-- mb-4 -->
            <h1 class="h3 mb-0 text-gray-800">Crear Usuarios</h1>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <form role="form" method="post" action="<?php echo base_url() ?>index.php/rucs/store" style="width: 100%;">
                    <div class="col-lg-12 form-group">
                        <label>RUC:</label>
                        <input type="number" name="ruc" value="" id="ruc" class="form-control" 
                                maxlength="11" 
                                pattern="[0-9]"
                                oninput="if(this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                required> <!-- required -->
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Razon Social:</label>
                        <input type="text" name="razon_social" value="" id="razon_social" class="form-control" required> <!-- required -->                        
                    </div>                                                                                                              
                    <!-- <div class="col-lg-12 form-group"> -->
                        <input type="submit" name="submit" value="Crear RUC" class="btn btn-primary" style="width:49%;"></input>&nbsp;
                        <a class="btn btn-primary" href="<?php echo base_url() ?>index.php/users/edit/<?php echo $user[0]->sec_user_id; ?>" style="width:49%;">Atr&aacute;s</a>
                    <!-- </div> -->

                    <input type="hidden" name="sec_user_id" value="<?php echo $user[0]->sec_user_id; ?>" id="id" class="form-control">
                </form>
                <div class="row">&nbsp;</div>

            </div>
        </div>
                  
    </div>
    <!-- Cerrar Container --> 

    <?php $this->load->view('template/footer.php'); ?>
    <?php $this->load->view('template/scripts.php'); ?>
    <script src="<?php echo base_url() ?>assets/js/users.js"></script>
</body>
</html>