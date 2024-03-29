<?php
$nameTStation = '';
if ($typeStation == 0) {
    $nameTStation = 'Combustible';
} else if ($typeStation == 1) {
    $nameTStation = 'Market Tienda';
} else if ($typeStation == 2) {
    $nameTStation = 'Market Playa';
} else if ($typeStation == 3) {
    $nameTStation = 'Resumen';
} else if ($typeStation == 4) {
    $nameTStation = 'Estadística';
} else if ($typeStation == 5) {
    $nameTStation = 'Productos por Línea';
} else if ($typeStation == 6) {
    $nameTStation = 'Ventas por Horas';
} else if ($typeStation == 7) {
    $nameTStation = 'Liquidacion Diaria';
} else if ($typeStation == 8) {
    $nameTStation = 'Saldos Pendiente de Socio';
} else if ($typeStation == 9) {
    $nameTStation = 'Sobrantes y Faltantes';
}
?>

                <!-- <h3><span>Ventas</span> - <span><?php echo $nameTStation ?></span></h3> -->
                <!-- <label>Consultar en:</label> -->

                <?php if($typeStation == 8) { ?>

                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label>Consultar en:</label>
                        <select name="select-station" id="select-station" class="form-control selectpicker" multiple title="Elige una de las siguientes opciones, por defecto todos." data-style="btn-primary"> <!-- data-style="btn-primary" -->
                            <?php
                            foreach ($result_c_org as $key => $cOrg) {
                                echo '<option value="'.$cOrg->c_org_id.'">'.$cOrg->name.'</option>';
                            }
                            ?>
                        </select>
                    </div>

                <?php } else { ?>

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
    
                <?php } ?>

                <?php if($typeStation == 4) { ?>

                    <label for="">Periodo Anterior:</label>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <input type="text" class="form-control" required id="_start-date-request" value="<?php echo $previous_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                        <input type="text" class="form-control" required id="_end-date-request" value="<?php echo $previous_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                    </div>

                    <label for="">Periodo Actual:</label>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <input type="text" class="form-control" required id="start-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                        <input type="text" class="form-control" required id="end-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                    </div>

                <?php } else if($typeStation == 8) { ?>

                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label>Hasta:</label>
                        <input type="text" class="form-control" required id="end-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                    </div>

                <?php } else { ?>

                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label>Desde:</label>
                        <input type="text" class="form-control" required id="start-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                    </div>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label>Hasta:</label>
                        <input type="text" class="form-control" required id="end-date-request" value="<?php echo $default_start_date; ?>" style="background-color: #eaecf4; opacity: 1;" /> <!-- readonly -->
                    </div>

                <?php } ?>                
                
                <?php if($typeStation == 6) { ?>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons" style="margin-bottom:5px!important">
                        <label class="btn btn-sm btn-primary active">
                            <input type="radio" name="local" id="local_combustible" value="COMBUSTIBLE" checked> COMBUSTIBLE
                        </label>
                        <label class="btn btn-sm btn-primary">
                            <input type="radio" name="local" id="local_market" value="MARKET"> MARKET
                        </label>                        
                    </div>  
                    <br>                  

                    <div class="btn-group btn-group-toggle" data-toggle="buttons" style="margin-bottom:5px!important">
                        <label class="btn btn-sm btn-primary active">
                            <input type="radio" name="importe" id="importe_importe" value="IMPORTE" checked> IMPORTE
                        </label>
                        <label class="btn btn-sm btn-primary">
                            <input type="radio" name="importe" id="importe_cantidad" value="CANTIDAD"> CANTIDAD
                        </label>                        
                    </div>                                
                    <br>

                    <div class="btn-group btn-group-toggle" data-toggle="buttons" style="margin-bottom:15px!important">
                        <label class="btn btn-sm btn-primary active">
                            <input type="radio" name="modo" id="modo_detallado" VALUE="DETALLADO" checked> DETALLADO
                        </label>                        
                        <label class="btn btn-sm btn-primary">
                            <input type="radio" name="modo" id="modo_resumido" VALUE="RESUMIDO"> RESUMIDO
                        </label>
                    </div>  

                    <div id="productos" class="form-group">
                        <label>Productos:</label>
                        <select name="productos" class="form-control">
                            <option value="TODOS">TODOS LOS PRODUCTOS</option>
                            <option value="11620301">84</option>
                            <option value="11620302">90</option>
                            <option value="11620303">97</option>
                            <option value="11620304">D2</option>
                            <option value="11620305">95</option>
                            <!-- <option value="11620306">11620306 - KEROSENE</option> -->
                            <option value="11620307">GLP</option>
                        </select>                              
                    </div>

                    <div id="unidadmedida" class="form-group">
                        <label>Seleccionar Unidad de Medida (Solo para GLP):</label>
                        <select name="unidadmedida" class="form-control">
                            <option value="-">No convertir unidades</option>
                            <option value="Litros_a_Galones">Convertir de litros a galones</option>
                            <option value="Galones_a_Litros">Convertir de galones a litros</option>
                        </select>                              
                    </div>
                <?php } ?>

                <?php if($typeStation == 7) { ?>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label for="">Inventario de Combustible:</label>
                        <select name="inventariocombustible" class="form-control">
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label for="">Formato:</label>
                        <select name="demo" class="form-control">
                            <option value="Demo1">Formato 1</option>
                            <option value="Demo2">Formato 2</option>
                        </select>
                    </div>
                <?php } ?>

                <?php if($typeStation == 8) { ?>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label for="">Socio:</label>
                        <input type="text" name="socio" value="" id="socio" class="form-control">
                    </div>

                    <div class="d-flex align-content-start flex-wrap bd-highlight mb-3" id="lista-socios"></div>

                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label for="">Vales no Liquidados:</label>
                        <select name="vales" class="form-control">
                            <option value="1">Si</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label for="">Vista:</label>
                        <select name="vista" class="form-control">
                            <option value="DETDOC_RESVAL">Detallada Documentos / Resumida Vales</option>
                            <option value="DET">Detallada</option>
                            <option value="RES">Resumida</option>
                        </select>
                    </div>
                <?php } ?>

                <?php if($typeStation == 9) { ?>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label>Productos:</label>
                        <select name="productos" class="form-control">
                            <option value="*">Todos los Productos</option>
                            <?php
                            foreach ($result_c_product as $key => $cProd) {
                                echo '<option value="'.$cProd->value.'">'.$cProd->name.'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Seleccionar Unidad de Medida (Solo para GLP):</label>
                        <select name="unidadmedida" class="form-control">
                            <option value="-">No convertir unidades</option>
                            <option value="Litros_a_Galones">Convertir de litros a galones</option>
                            <option value="Galones_a_Litros">Convertir de galones a litros</option>
                        </select>
                    </div>

                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkDetallado" checked=true>
                            <label class="form-check-label" for="checkDetallado">
                                Detallado
                            </label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkResumido">
                            <label class="form-check-label" for="checkResumido">
                                Resumido
                            </label>
                        </div>
                    </div>
                <?php } ?>

                <?php if($typeStation == 10) { ?>
                    <div class="form-group"> <!-- <div class="col-lg-12 form-group"> -->
                        <label for="">Cliente:</label>
                        <input type="text" name="cliente" value="" id="cliente" class="form-control">
                    </div>
                <?php } ?>

                <input type="hidden" id="qty_sale" value="kardex"><!--kardex y tickets-->
                <input type="hidden" id="type_cost" value="avg"><!--last y avg-->
                <input type="hidden" id="chart-mode" value="0">
                <!--<select id="chart-mode" class="form-control">
                    <option value="0">Gráfico de Barras</option>
                    <option value="1">Gráfico Circular</option>
                </select>-->
                <!-- <br> -->
                <input type="hidden" id="typeStation" value="<?php echo $typeStation ?>">
                <button class="btn btn-primary btn-block btn-search-sale" data-ismarket="<?php echo $typeStation ?>"><span class="glyphicon glyphicon-search"></span> Buscar</button>