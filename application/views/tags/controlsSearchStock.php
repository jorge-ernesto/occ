                <?php
                $submit = $actions['submit'];
                ?>

                <!-- <h3><span>Ventas</span> - <span><?php echo $nameTStation ?></span></h3> -->
                <!-- <label>Consultar en:</label> -->

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
                    <label>Fecha:</label>
                    <input type="text" class="form-control" required id="start-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                </div>
                <input type="hidden" class="form-control" id="days-prom" placeholder="Días para promediar venta" value="7">

                <?php if($typeStation == 0) { ?>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkProyeccion">
                            <label class="form-check-label" for="checkProyeccion">
                                Proyección
                            </label>
                        </div>
                    </div>
                    
                    <div class="d-none" id="divProyeccion">
                        <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                            <label>Desde:</label>
                            <input type="text" class="form-control" required id="start-date-request_proyeccion" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                        </div>
                        <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                            <label>Hasta:</label>
                            <input type="text" class="form-control" required id="end-date-request_proyeccion" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                        </div>
                        <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                            <label>Dias Proyección:</label>
                            <input type="number" class="form-control" required id="days_proyeccion" value="1">
                        </div>
                    </div>

                <?php } ?>

                <input type="hidden" id="qty_sale" value="kardex"><!--y tickets-->
                <input type="hidden" id="type_cost" value="last"><!--y prom-->
                <!--<select id="chart-mode" class="form-control">
                    <option value="0">Gráfico de Barras</option>
                    <option value="1">Gráfico Circular</option>
                </select>-->
                <!-- <br> -->
                <input type="hidden" id="typeStation" value="<?php echo $typeStation ?>">
                <button class="btn btn-primary btn-block <?php echo $submit ?>" data-ismarket="<?php echo $typeStation ?>"><span class="glyphicon glyphicon-search"></span> Buscar</button>