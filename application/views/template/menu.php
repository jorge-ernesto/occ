<nav class="navbar navbar-expand-lg navbar-dark bd-navbar">
    <a class="navbar-brand" href="<?php echo base_url() ?>"><?php echo appName() ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <?php if(checkSession()) { ?>
        <ul class="navbar-nav mr-auto">      
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
              Consultar
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
              <li><a class="dropdown-item" href="#">Cliente Credito</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/resumen">               Resumen</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/estadistica">           Estadística</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/combustibles">          Combustible</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/market">                Market Tienda</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/market_productos_linea">Productos por Línea (MT)</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/market_playa">          Market Playa</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/ventas_horas">          Ventas por Horas</a></li>
              <li><a class="d-none dropdown-item" href="<?php echo base_url() ?>index.php/ventas/liquidacion_diaria">    Liquidacion diaria</a></li>              
            </ul>
          </li>
          <?php if($_SESSION['isadmin'] == 1) { ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
              Seguridad
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
              <li><a class="dropdown-item" href="#">       Usuarios</a></li>
              <li><a class="d-none dropdown-item" href="#">Another action</a></li>
              <li><hr class="d-none dropdown-divider"></li>
              <li><a class="d-none dropdown-item" href="#">Something else here</a></li>
            </ul>
          </li>
          <?php } ?>
        </ul>      
      <?php } ?>

      <ul class="navbar-nav navbar-right">                
        <?php if(checkSession()) { ?>
          <li class="dropdown">            
            <a id="dropdownMenuLink" class="btn btn-outline-primary dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php echo $_SESSION['loginname']; ?>
            </a>            
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#"><span class="glyphicon glyphicon-user"></span> Mi cuenta</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?php echo base_url() ?>index.php/secure/logout"><span class="glyphicon glyphicon-log-out"></span> Cerrar Sesión</a></li>
            </ul>        
          </li>
        <?php } ?>
      </ul>
    </div>
</nav>