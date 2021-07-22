<?php $this->load->view('template/header.php'); ?>
<body id="page-top">    
    <?php $this->load->view('template/sidebar.php'); ?>
    <?php $this->load->view('template/menu.php'); ?>

    <!-- Container -->
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-3"> <!-- mb-4 -->
            <h1 class="h3 mb-0 text-gray-800">Editar Usuarios</h1>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <form method="post" action="<?php echo base_url() ?>index.php/users/update"> <!-- novalidate -->
                    <div class="col-lg-12 form-group">
                        <label>Nombre:</label>
                        <input type="text" name="name" value="<?php echo $user[0]->name; ?>" id="name" class="form-control" required> <!-- required -->
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo $user[0]->email; ?>" id="email" class="form-control" required> <!-- required -->
                    </div>                                                                                                              
                    <div class="col-lg-12 form-group">
                        <label>Admin:</label>                                                     
                        <select name="isadmin" id="isadmin" class="form-control">
                            <?php if($user[0]->isadmin == 0) { ?>
                                <option value="0" selected>NO</option> 
                                <option value="1">SI</option> 
                            <?php } else { ?>
                                <option value="0">NO</option> 
                                <option value="1" selected>SI</option> 
                            <?php } ?>                                                
                        </select>
                    </div>
                    <div class="col-lg-12 form-group">
                        <label>Activo:</label>
                        <select name="isactive" id="isactive" class="form-control">
                            <?php if($user[0]->isactive == 0) { ?>
                                <option value="0" selected>NO</option> 
                                <option value="1">SI</option> 
                            <?php } else { ?>
                                <option value="0">NO</option> 
                                <option value="1" selected>SI</option> 
                            <?php } ?>                                                                                                                                             
                        </select>
                    </div>
                    <!-- <div class="col-lg-12 form-group"> -->
                        <input type="submit" name="submit" value="Editar Usuario" class="btn btn-primary" style="width:49%;"></input>
                        <a class="btn btn-primary" href="<?php echo base_url() ?>index.php/users/view" style="width:49%;">Atr&aacute;s</a>
                    <!-- </div> -->

                    <input type="hidden" name="sec_user_id" value="<?php echo $user[0]->sec_user_id; ?>" class="form-control">
                </form>
                <div class="row">&nbsp;</div>

                <hr>
                <div class="table-responsive">
                    <table id="table_id" class="table table-striped table-bordered table-hover dataTable" cellspacing="0" style="width: 100%;" role="grid"> <!-- <table class="table table-sm table-bordered table-striped table-hover table-responsive"> -->
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>RUC</th>
                                <th>Razon Social</th>
                                <th class="noExport"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                                                
                                <th></th>                                                
                                <td></td>
                                <td></td>
                                <td></td>                                               
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h4>
                    <a id="wea" class="btn btn-primary mr-2" href="<?php echo base_url() ?>index.php/rucs/create/<?php echo $user[0]->sec_user_id ?>">Agregar RUC</a>
                </h4>

            </div>
        </div>
                  
    </div>
    <!-- Cerrar Container --> 

    <?php $this->load->view('template/footer.php'); ?>
    <?php $this->load->view('template/scripts.php'); ?>
    <script src="<?php echo base_url() ?>assets/js/rucs.js"></script>
</body>
</html>