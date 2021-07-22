<?php $this->load->view('template/header.php'); ?>
<body id="page-top">    
    <?php $this->load->view('template/sidebar.php'); ?>
    <?php $this->load->view('template/menu.php'); ?>

    <!-- Container -->
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-3"> <!-- mb-4 -->
            <h1 class="h3 mb-0 text-gray-800">Crear Usuarios</h1>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <form role="form" method="post" action="<?php echo base_url() ?>index.php/users/store" style="width: 100%;">
                    <div class="col-lg-12 form-group">
                        <label>Nombre:</label>
                        <input type="text" name="name" value="" id="name" class="form-control" required> <!-- required -->
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Email:</label>                                        
                        <input type="email" name="email" value="" id="email" class="form-control" required> <!-- required -->
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Contrase&ntilde;a:</label>                        
                        <input type="password" name="password" value="" id="password" class="form-control" required> <!-- required -->                        
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Admin:</label>
                        <select name="isadmin" id="isadmin" class="form-control">
                            <option value="0" selected>NO</option> 
                            <option value="1">SI</option> 
                        </select>
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Activo:</label>                                        
                        <select name="isactive" id="isactive" class="form-control">
                            <option value="0">NO</option>  
                            <option value="1" selected>SI</option>                                                                                                                                              
                        </select>                                        
                    </div>                                        
                    <!-- <div class="col-lg-12 form-group"> -->
                        <input type="submit" name="submit" value="Crear Usuario" class="btn btn-primary" style="width:49%;"></input>&nbsp;
                        <a class="btn btn-primary" href="<?php echo base_url() ?>index.php/users/view" style="width:49%;">Atr&aacute;s</a>
                    <!-- </div> -->
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