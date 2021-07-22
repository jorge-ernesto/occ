<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <?php if(checkSession()) { ?>
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <?php } ?>
      <a class="navbar-brand" href="<?php echo base_url() ?>"><?php echo appName() ?></a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <?php if(checkSession()) { ?>
      <ul class="nav navbar-nav">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Consultar <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu">
              <a tabindex="-1" href="#">Ventas</a>
              <ul class="dropdown-menu">
                <li><a tabindex="-1" href="<?php echo base_url() ?>index.php/ventas/resumen">Resumen</a></li>
                <li><a href="<?php echo base_url() ?>index.php/ventas/estadistica">Estadística</a></li>
                <li><a href="<?php echo base_url() ?>index.php/ventas/combustibles">Combustible</a></li>
                <li><a href="<?php echo base_url() ?>index.php/ventas/market">Market Tienda</a></li>
                <li><a href="<?php echo base_url() ?>index.php/ventas/market_productos_linea">Productos por Línea (MT)</a></li>
                <li><a href="<?php echo base_url() ?>index.php/ventas/market_playa">Market Playa</a></li>
                <li><a href="<?php echo base_url() ?>index.php/ventas/ventas_horas">Ventas por Horas</a></li>
                <li><a href="<?php echo base_url() ?>index.php/ventas/liquidacion_diaria">Liquidacion diaria</a></li>
                <!-- <li><a href="<?php echo base_url() ?>index.php/ventas/mercaderias">Mercaderías</a></li> --> <!-- Se encontraba comentado -->
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a tabindex="-1" href="#">Stocks</a>
              <ul class="dropdown-menu">
                <li><a tabindex="-1" href="<?php echo base_url() ?>index.php/stocks/diario">Diario</a></li>
                <!-- <li><a href="<?php echo base_url() ?>index.php/stocks/mercaderias">Mercaderías</a></li> --> <!-- Se encontraba comentado -->
                <!--<li><a href="#">Telemedición OPW</a></li>-->
              </ul>
            </li>
          </ul>
        </li>
      </ul>
      <?php } ?>

      <ul class="nav navbar-nav navbar-right">
        <?php if(checkSession()) { ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['loginname'] ?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#"><span class="glyphicon glyphicon-user"></span> Mi cuenta</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="<?php echo base_url() ?>index.php/secure/logout"><span class="glyphicon glyphicon-log-out"></span> Cerrar Sesión</a></li>
          </ul>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>