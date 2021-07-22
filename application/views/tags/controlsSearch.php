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
    <input type="text" class="form-control" readonly required id="start-date-request" value="<?php echo $default_start_date; ?>" /><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
</div>

<div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
    <label>Hasta:</label>
    <input type="text" class="form-control" readonly required id="end-date-request" value="<?php echo $default_start_date; ?>" /><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
</div>

<input type="hidden" id="typeStation" value="<?php echo $typeStation ?>">
<button class="btn btn-primary btn-block btn-search" data-ismarket="<?php echo $typeStation ?>"><span class="glyphicon glyphicon-search"></span> Buscar</button>
