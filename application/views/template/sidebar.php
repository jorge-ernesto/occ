<div id="wrapper">
   <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url() ?>index.php/"><img
            src="https://cdn.opensysperu.com/img/logolu.png" style="max-width: 100%; max-height: 100%" /></a>
      <hr class="sidebar-divider d-none d-md-block" />
      <li class="nav-item">
         <a class="nav-link" href="<?php echo base_url() ?>index.php/"><i class="fas fa-fw fa-home ocsicon"></i><span>Inicio</span></a>
      </li>
      <hr class="sidebar-divider d-none d-md-block" />
      <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConsultas" aria-expanded="true"
            aria-controls="collapseConsultas"><i class="fas fa-fw fa-file-invoice ocsicon"></i><span>Consultas</span></a>
         <div id="collapseConsultas" class="collapse" aria-labelledby="headingEbi" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
               <a class="collapse-item" href="<?php echo base_url() ?>index.php/flotas/despachos">Despachos</a>
               <a class="collapse-item" href="<?php echo base_url() ?>index.php/flotas/comprobantes_cobranza">Comprobantes cobranza</a>
            </div>
         </div>
      </li>
      <?php if($_SESSION['isadmin'] == 1) { ?>
      <li class="nav-item">
         <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSeguridad" aria-expanded="true"
            aria-controls="collapseSeguridad"><i class="fas fa-fw fa-file-invoice ocsicon"></i><span>Seguridad</span></a>
         <div id="collapseSeguridad" class="collapse" aria-labelledby="headingEbi" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
               <a class="collapse-item" href="<?php echo base_url() ?>index.php/users/view">Usuarios</a>
            </div>
         </div>
      </li>
      <?php } ?>
      <hr class="sidebar-divider d-none d-md-block" />
      <span class="mb-3"></span>
      <!-- <li class="nav-item">
         <a class="nav-link" href="/cpl.downloads.page"><i
               class="fas fa-fw fa-download ocsicon"></i><span>Descargas</span></a>
      </li> -->
      <div class="text-center d-none d-md-inline">
         <button class="rounded-circle border-0" id="sidebarToggle" />
      </div>
   </ul>