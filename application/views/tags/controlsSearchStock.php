                <h3><span><?php echo $name ?></span></h3>
                <label>Consultar en:</label>
                <?php
                $submit = $actions['submit'];
                ?>

                <select name="select-station" id="select-station" class="form-control size-text-select">
                    <option value="*">Todas las Estaciones</option>
                    <?php
                    foreach ($result_c_org as $key => $cOrg) {
                        echo '<option value="'.$cOrg->c_org_id.'">'.$cOrg->name.'</option>';
                    }
                    ?>
                </select>
                <br>
                <!--<div class="row">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="start-date-request" value="<?php echo $default_start_date; ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="days-prom" placeholder="Días para promediar venta">
                    </div>
                </div>-->
                <input type="text" class="form-control" id="start-date-request" value="<?php echo $default_start_date; ?>">
                <input type="hidden" class="form-control" id="days-prom" placeholder="Días para promediar venta" value="7">

                <input type="hidden" id="qty_sale" value="kardex"><!--y tickets-->
                <input type="hidden" id="type_cost" value="last"><!--y prom-->
                <!--<select id="chart-mode" class="form-control">
                    <option value="0">Gráfico de Barras</option>
                    <option value="1">Gráfico Circular</option>
                </select>-->
                <br>
                <input type="hidden" id="typeStation" value="<?php echo $typeStation ?>">
                <button class="btn btn-primary btn-block <?php echo $submit ?>" data-ismarket="<?php echo $typeStation ?>"><span class="glyphicon glyphicon-search"></span> Buscar</button>