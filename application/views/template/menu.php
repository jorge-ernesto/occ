<div id="content-wrapper" class="d-flex flex-column">
   <div id="content">
      <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
         <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars">
            </i></button>
         <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
               <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false"><i class="fas fa-fw fa-user"></i></a>
               <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                  aria-labelledby="userDropdown">
                  <a class="dropdown-item" href="#"><i
                        class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Mi cuenta</a>
                  <!-- <a class="dropdown-item" href="/cpl.changepass.page"><i
                        class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Cambiar Contrase&ntilde;a</a> -->
                  <!-- <a class="dropdown-item" href="/cpl.config.page"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Configuraci&oacute;n</a>-->                                    
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="<?php echo base_url() ?>index.php/secure/logout"><i
                        class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Cerrar Sesi&oacute;n</a>
               </div>
            </li>
         </ul>
      </nav>      

      <!-- Container -->
      <!-- <div class="container-fluid"> -->