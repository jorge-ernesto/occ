<div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
    <label>Consultar en:</label>
    <select name="select-station" id="select-station" class="form-control">
        <option value="*">Todas las Estaciones</option>
        <?php
        foreach ($result_c_org as $key => $cOrg) {
            echo '<option value="'.$cOrg->c_org_id.'">'.$cOrg->name.'</option>';
        }
        ?>
    </select>
</div>

<div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
    <label>Desde:</label>
    <input type="text" class="form-control" required id="start-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span> <!-- readonly -->
</div>

<div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
    <label>Hasta:</label>
    <input type="text" class="form-control" required id="end-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span> <!-- readonly -->
</div>

<!-- Comprobantes de Cobranza -->
<?php if($typeStation == 1) { ?>
    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
        <label>RUC:</label>
        <select name="select-ruc" id="select-ruc" class="form-control">
            <?php
            if( is_null($result_c_client) || empty($result_c_client) ){
                echo '<option value="-">Ningun dato disponible</option>';
            } else {
                foreach ($result_c_client as $key => $cRuc) {
                    echo '<option value="'.$cRuc->value.'">'.$cRuc->name.' - '.$cRuc->value.'</option>';
                }
            }
            ?>
        </select>
    </div>
    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
        <label>Estado:</label>
        <select name="select-state" id="select-state" class="form-control">
            <option value="0">Pendiente</option>
            <option value="1">Cancelado</option>
            <option value="2">Todos</option>
        </select>
    </div>
<?php } ?>

<input type="hidden" id="qty_sale" value="kardex"><!--kardex y tickets-->
<input type="hidden" id="type_cost" value="avg"><!--last y avg-->
<input type="hidden" id="chart-mode" value="0">

<input type="hidden" id="typeStation" value="<?php echo $typeStation ?>">
<button class="btn btn-primary btn-block btn-search-fleet" data-ismarket="<?php echo $typeStation ?>"><span class="glyphicon glyphicon-search"></span> Buscar</button>
