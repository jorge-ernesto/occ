var url = '/occ/index.php/';
//var url = 'http://192.168.0.191/ocsmanager/index.php/';

var stations = [], stationsDesc = [], stationsPor = [], stationsTotal = [];
var stationsQty = [], stationsUtil = [], stationColor = [], dataStations = [];
var nameStation = {}, porStation = {};
var gran_total = 0.0;
var gran_qty = 0.0;
var gran_cost = 0.0;
var gran_util = 0.0;
var paramsRequest = [];

var totalProductsExclude = [], totalProductsInclude = [],
	quiantityProductExclude = [], quiantityProductInclude = [],
	dataSumarySale = {};

/**
 * Funcion para acceso al sistema
 */
function login() {
	$('.msg-login').html(loading());
	console.log('username: '+$('#username').val()+', password: '+$('#password').val());
	if(empty($('#username').val())) {
		$('#username').focus();
		$('.msg-login').html(_alert('warning', 'Debe ingresar Usuario'));
		return false;
	}
	if(empty($('#password').val())) {
		$('.msg-login').html(_alert('warning', 'Debe ingresar Contraseña'));
		$('#password').focus();
		return false;
	}

	var params = {
		username: $('#username').val(),
		password: $('#password').val(),
	};
	$.post(url+'secure/postLogin', params, function(data) {
		console.log(data);
		if(data.status == 1) {
			window.location = url;
		} else if(data.status == 2) {
			$('.msg-login').html(_alert('warning', data.message));
			// $('#password').focus();
		} else if(data.status == 3) {
			$('.msg-login').html(_alert('warning', data.message));
			// $('#username').focus();
		} else if(data.status == 100) {
			$('.msg-login').html(_alert('warning', data.message));
		}
	}, 'json');
}

/**
 * Comprobar si un valor es vacio
 * @param string input
 * @return boolean
 */
function empty(input) {
	console.log('input.length: '+input.length);
	if(input == '' || input.length < 1) {//error
		return true;
	} else {
		return false;
	}
}

/**
 * Mensaje de alerta
 * @param string type(doc bootstrap), string text(contenido a mostrar)
 * @return string
 */
function _alert(type, text) {
	return '<div class="alert alert-'+type+'" role="alert">'+text+'</div>';
}

function _alertJS(type, text) {
	return '<div role="alert" class="alert alert-'+type+' alert-dismissible fade in"> <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span></button> '+text+' </div>';
}

/**
 * Validar fecha
 * Ej valida: XXXX-02-30, XXXX-12-32, fechas futuras
 * @param string input, string sep
 * @return 
 */
 function checkDate(input, sep) {
	var status = 1;
	console.log('input: '+input+', sep: '+sep);
	input = input.split(sep);
	console.log('split: '+input[2]+'/'+input[1]+'/'+input[0]);
	console.log('input: '+input);
	var month = parseInt(input[1]) -1;
	//var month = input[1];
	var d = new Date(input[2], month, input[0]);

	var rightnow = new Date();
	if (d.getFullYear() == input[2] && d.getMonth() == input[1] && d.getDate() == input[0]) {
		status = 0;
	} else if(rightnow < d) {
		status = 2;
	}
	console.log('---> input[2], month, input[0]: '+input[2]+'-'+month+'-'+input[0]);
	console.log('---> d: '+d);
	console.log('---> rightnow: '+rightnow);

	return status;
}

function loading() {
	return '<div class="spinner">'
	+'<div class="bounce1"></div>'
	+'<div class="bounce2"></div>'
	+'<div class="bounce3"></div>'
	+'</div>';
}

function loading_bootstrap4() {
	return `<div class="d-flex justify-content-center">
				<div class="spinner-grow text-warning" role="status" style="width: 3rem; height: 3rem;">
					<span class="sr-only">Loading...</span>
				</div>
			</div>`;
}

/**
	* Verifica si existe una sesion activa en el servidor por medio del callback POST/JSON
	* @param obj data
	*/
function checkSession(data) {
	if(data.status == 101) {
		window.location = url+'secure/login';
	}
}

function searchLiquidacionDiaria(t){
	$('.container-chart-station').addClass('none');
	$('.container-ss-station').addClass('none');
	$('.result-search').html('<br><br>'+loading());
	var paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),
		typeStation: $('#typeStation').val(),
		
		/*No sirve en este reporte*/
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
		/*Cerrar no sirve en este reporte*/  

		inventariocombustible: $('select[name=inventariocombustible]').val(),
		demo: $('select[name=demo]').val()
	}
	console.log(paramsRequest);

	/*setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();*/

	var charMode = $('#chart-mode').val();

	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getLiquidacionDiaria', paramsRequest, function(data) {
		// console.log('data:', data); //ELIMINAR
		console.log('data:', JSON.stringify(data)); //ELIMINAR
		// return; //ELIMINAR
		
		checkSession(data);
		$('.btn-search-sale').prop('disabled', false);
		console.log('Dentro del callback');
		console.log(data);
		if(data.demo == "Demo1"){
			$('.result-search').html(templateStationsSearchLiquidacionDiaria(data, data.typeStation, charMode));
		}else{
			$('.result-search').html(templateStationsSearchLiquidacionDiaria_(data, data.typeStation, charMode));
		}					
	}, 'json');
}

/**
 * Plantilla de estaciones buscadas para Ventas - Liquidacion diaria
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearchLiquidacionDiaria(data,t,cm) {
	console.log('data en templateStationsSearchLiquidacionDiaria:', data);

	// $('.result-search').addClass('container');
	// $('.result-search').attr('style', 'width:100%');
	// $('.result-search').attr('style', 'font-size:1.2em');

	clearStations();
	var html = '<br>';
	var detail = data.stations;
	if (typeof detail == "undefined") {
		return '<div class="alert alert-info">No existe información</div>';
	}
	var count = detail.length;
	gran_total = 0.0;
	gran_qty = 0.0;
	gran_util = 0.0;
	gran_cost = 0.0;
	var num = 1;
	var unit = t == 0 ? 'Gln' : '';

	var color_id, taxid;
	var tab     = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var espacio = '&nbsp;&nbsp;';
	var dnone   = 'style="display:none;"';
	var fDecimal = 2;
	for(var i = 0; i<count; i++) {
		/*1. Venta Combustible*/
		var venta_combustible   = detail[i].data['1_venta_vombustible'];
		TVCombustible         	= +venta_combustible[0].liquido + +venta_combustible[0].glp; //TVCombustible: Total Venta Combustible
		TVCombustible_canti 	   = +venta_combustible[0].liquido_canti + +venta_combustible[0].glp_canti; //TVCombustible: Total Venta Combustible CANTIDAD
		total_venta_combustible = Math.round10(TVCombustible, -fDecimal);
		total_canti_combustible = Math.round10(TVCombustible_canti, -fDecimal);
		
		/*2. Venta de Productos y Promociones*/
		var venta_productos_promociones = detail[i].data['2_venta_productos_promociones'];
		VT                              = +venta_productos_promociones[0].ventatienda; //VT: Venta Tienda
		CT                              = +venta_productos_promociones[0].cantienda; //CT: Cantidad Tienda
		venta_de_tienda                 = Math.round10(VT, -fDecimal);
		canti_de_tienda                 = Math.round10(CT, -fDecimal);

		/*2. Venta de Productos y Promociones - Detalle*/
		var venta_productos_promociones_detalle = detail[i].data['2_venta_productos_promociones_detalle'];
		// can_prod_prom = 0;
		// imp_prod_prom = 0;

		venta_productos_promociones_detalle_html = '';
		for (var v in venta_productos_promociones_detalle) {	
			cantidad_prod_prom = +venta_productos_promociones_detalle[v].cantidad;
			importe_prod_prom = +venta_productos_promociones_detalle[v].importe;

			// can_prod_prom = can_prod_prom + cantidad_prod_prom;
			// imp_prod_prom = imp_prod_prom + importe_prod_prom;

			venta_productos_promociones_detalle_html += `
				<tr class="collapse venta_producto_promociones_${detail[i].id}"> 
					<td> ${ tab } ${ venta_productos_promociones_detalle[v].linea } ${ espacio } - ${ espacio } ${ venta_productos_promociones_detalle[v].descripcion_linea } </td> 
					<td> ${ (Math.round10(cantidad_prod_prom, -fDecimal)).toFixed(2) } </td> 
					<td> ${ (Math.round10(importe_prod_prom, -fDecimal)).toFixed(2) } </td> 
				</tr>
			`;								
		}

		/*Total Venta (1+2)*/
		TVC = TVCombustible_canti + CT; //TVC: Total Venta Cantidad
		TV  = TVCombustible + VT; //TV: Total Venta			
		total_venta = Math.round10(TV, -fDecimal);
		total_canti = Math.round10(TVC, -fDecimal);		
		
		TVC_ = Math.round10(TVC, -fDecimal);
		TV_  = Math.round10(TV, -fDecimal);

		/*3. Credito Clientes*/			
		var vales_credito_detalle = detail[i].data['3_vales_credito_detalle'];
		var val_can = 0; 
		var val_imp = 0;

		var vales_credito_detalle_html = '';
		for (var val in vales_credito_detalle) {	
			cantidad = +vales_credito_detalle[val].cantidad;
			importe = +vales_credito_detalle[val].importe;	
			val_can = val_can + cantidad; 
			val_imp = val_imp + importe;				
			
			vales_credito_detalle_html += `
				<tr class="collapse credito_clientes_${detail[i].id}"> 
					<td> ${ tab } ${ vales_credito_detalle[val].codcliente } ${ espacio } ${ vales_credito_detalle[val].ruc } ${ espacio } ${ vales_credito_detalle[val].cliente } </td> 
					<td> ${ (Math.round10(cantidad, -fDecimal)).toFixed(2) } </td> 
					<td> ${ (Math.round10(importe, -fDecimal)).toFixed(2) } </td> 
				</tr>
			`;								
		}
		total_val_can = Math.round10(val_can, -fDecimal);
		total_val_imp = Math.round10(val_imp, -fDecimal);

		/*4. Tarjetas de Credito*/	
		var tarjetas_credito_detalle = detail[i].data['4_tarjetas_credito_detalle'];
		var val_importetarjeta = 0;

		var tarjetas_credito_detalle_html = '';
		for (var t in tarjetas_credito_detalle) {	
			importetarjeta = +tarjetas_credito_detalle[t].importetarjeta;
			val_importetarjeta = val_importetarjeta + importetarjeta;

			tarjetas_credito_detalle_html += `
				<tr class="collapse tarjetas_credito_${detail[i].id}">
					<td> ${ tab } ${ tarjetas_credito_detalle[t].descripciontarjeta } </td>
					<td></td>
					<td> ${ (Math.round10(importetarjeta, -fDecimal)).toFixed(2) } </td>
				</tr>
			`;
		}
		total_val_importetarjeta = Math.round10(val_importetarjeta, -fDecimal);

		/*5. Descuentos*/	
		var descuentos       = detail[i].data['5_descuentos'];
		var descuentos_total = Math.abs(+descuentos[0].descuentos);
		descuentos_total_    = Math.round10(descuentos_total, -fDecimal);
		
		/*6. Diferencia de Precio de Vales*/
		var diferencias_precio_vales = detail[i].data['6_diferencias_precio_vales'];			
		var difprecio_total 	        = +diferencias_precio_vales[0].difprecio;			
		difprecio_total_             = Math.round10(difprecio_total, -fDecimal);

		/*7. Afericiones*/
		var afericiones       = detail[i].data['7_afericiones'];			
		var afericiones_total = +afericiones[0].afericiones; 		
		afericiones_total_    = Math.round10(afericiones_total, -fDecimal);

		/*Total Venta Creditos y Otros No al Contado*/			
		//$TVCO = $vales_credito[0]['valescredito']+$tarjetas_credito_total[0]['tarjetascredito']+abs($descuentos[0]['descuentos'])+$difprecio[0]['difprecio']+$afericiones[0]['afericiones']; //TVCO: Total Venta Creditos y Otros
		var TVCO = (+val_imp) + (+val_importetarjeta) + (+descuentos_total) + (+difprecio_total) + (+afericiones_total); //TVCO: Total Venta Creditos y Otros									
		// TVCO_ = TVCO.toFixed(2);			
		TVCO     = Math.round10(TVCO, -fDecimal);
		TVCO_    = Math.round10(TVCO, -fDecimal);
		

		/*Total Efectivo en Boveda (Total Depositos POS)*/
		var total_depositos_pos = detail[i].data['total_depositos_pos'];			
		var TDP                 = +total_depositos_pos[0].depositospos;	
		TDP_                    = Math.round10(TDP, -fDecimal);
		
		/*Total Venta contado*/
		var TVContado = TV - TVCO;			
		// TVContado_ = TVContado.toFixed(2);
		TVContado     = Math.round10(TVContado, -fDecimal);
		TVContado_    = Math.round10(TVContado, -fDecimal);
		var a1        = TVContado; //TVContado: Total Venta Contado			

		/*8. Sobrantes Faltantes por Trabajador*/
		var sobrantes_faltantes_por_trabajador = detail[i].data['8_sobrantes_faltantes_por_trabajador'];
		var val_imp_sob = 0;
		var a2 = 0;

		var sobrantes_faltantes_por_trabajador_html = '';
		for (var d in sobrantes_faltantes_por_trabajador) {									
			var sob_descripcion = '';
			if ( sobrantes_faltantes_por_trabajador[d].flag == '0' ) {
				sob_descripcion = 'AUTO';
			} else {
				sob_descripcion = 'MANUAL';
			}

			imp_sob     = +sobrantes_faltantes_por_trabajador[d].importe;				
			val_imp_sob = val_imp_sob + imp_sob; 									
			a2          = a2 + imp_sob;

			sobrantes_faltantes_por_trabajador_html += `
				<tr class="collapse sobrantes_faltantes_por_trabajador_${detail[i].id}">
					<td> ${ tab } ${ sobrantes_faltantes_por_trabajador[d].nom_trabajador } </td> 
					<td> ${ sob_descripcion } </td> 
					<td> ${ (Math.round10(imp_sob, -fDecimal)).toFixed(2) } </td> 
				</tr>
			`;								
		}
		total_val_imp_sob = Math.round10(val_imp_sob, -fDecimal);

		/*Diferencia Diaria*/
		//$DD =  $TDP - $importe_sobfaltrab - $TVContado;  //DD: Diferencia Diaria: TOTAL DEPOSITOS - DIFERENCIA TRABAJADOR - VENTA CONTADO - OTROS GASTOS
		var DD = TDP_ - total_val_imp_sob - TVContado_;	
		DD_    = Math.round10(DD, -fDecimal);

		/*10.1 Ingresos al contado del día*/
		var ingresos_contado_dia = detail[i].data['10_1_ingresos_contado_dia'];
		var val_ingresos = 0;
		var a31 = 0;

		var ingresos_contado_dia_html = '';			
		for (var igre in ingresos_contado_dia) {		
			ingresos = +ingresos_contado_dia[igre].ingresos;
			val_ingresos = val_ingresos + ingresos;	
			a31          = a31 + ingresos;			
			
			var banco = '';
			if(ingresos_contado_dia[igre].c_cash_mpayment_id == 1 || (ingresos_contado_dia[igre].metodo_pago).trim() == "DEPOSITO BANCARIO"){
				banco += ` - ${ingresos_contado_dia[igre].banco}`;
			}

			ingresos_contado_dia_html += `
				<tr class="collapse ingresos_contado_dia_${detail[i].id}">
					<td> ${ tab } ${ tab } ${ ingresos_contado_dia[igre].documento } ${ banco } </td> 
					<td></td> 
					<td> ${ (Math.round10(ingresos, -fDecimal)).toFixed(2) } </td> 
				</tr>
			`;						
		}
		total_val_ingresos = Math.round10(val_ingresos, -fDecimal);

		/*10.2 Cobranzas y Amortizaciones por CC*/
		var ingresos_cobranzas_cc = detail[i].data['10_2_ingresos_cobranzas_amortizaciones_por_cc'];
		var val_ingresos_cc = 0;

		var ingresos_cobranzas_cc_html = '';			
		for (var igre in ingresos_cobranzas_cc) {		
			ingresos_cc = +ingresos_cobranzas_cc[igre].ingresos;
			val_ingresos_cc = val_ingresos_cc + ingresos_cc;				
							
			ingresos_cobranzas_cc_html += `
				<tr class="collapse ingresos_cobranzas_cc_${detail[i].id}">
					<td> ${ tab } ${ tab } ${ ingresos_cobranzas_cc[igre].documento } </td> 
					<td></td> 
					<td> ${ (Math.round10(ingresos_cc, -fDecimal)).toFixed(2) } </td> 
				</tr>
			`;						
		}
		total_val_ingresos_cc = Math.round10(val_ingresos_cc, -fDecimal);

		/*12. Egresos*/
		var arreglo_egresos = detail[i].data['12_egresos'];
		var val_egresos = 0;
		var a5 = 0;

		var egresos_html = '';			
		for (var e in arreglo_egresos) {					
			egresos     = +arreglo_egresos[e].egresos;				
			val_egresos = val_egresos + egresos;	
			a5          = a5 + egresos;							

			egresos_html += `
				<tr class="collapse egresos_${detail[i].id}">
					<td> ${ tab } ${ arreglo_egresos[e].documento } </td> 
					<td></td> 
					<td> ${ (Math.round10(egresos, -fDecimal)).toFixed(2) } </td> 
				</tr>
			`;						
		}
		total_val_egresos = Math.round10(val_egresos, -fDecimal);

		/*13. Documentos de Venta Manual - Detalle*/
		var documento_venta_manual_total = detail[i].data['13_documento_venta_manual_total'];
		var total_manuales               = +documento_venta_manual_total[0].total;
		total_manuales_                  = Math.round10(total_manuales, -fDecimal);
		a6                               = total_manuales;
		
		/*13. Documentos de Venta Manual - Total*/
		var manuales = detail[i].data['13_documento_venta_manual_detalle'];
		
		var manuales_html = '';			
		for (var m in manuales) {					
			imp_manual  = +manuales[m].importe;								

			manuales_html += `
				<tr class="collapse manuales_${detail[i].id}">
					<td> ${ tab } ${ manuales[m].documento } </td> 
					<td></td> 
					<td> ${ (Math.round10(imp_manual, -fDecimal)).toFixed(2) } </td> 
				</tr>
			`;						
		}

		/*14. Saldo Neto a Depositar*/			
		// console.log(a1);
		// console.log(a2);
		// console.log(a31);
		// console.log(a5);			

		//$calculo=( ($a1+$a2) - ($a3_1) ) - $a5; //ESTO QUEDA
		var calculo = ( ( ( (+a1) + (+a2) ) - (+a31) ) - (+a5) ).toFixed(2);
		// if(calculo <= 0 && calculo >= -0.3){
		// 	calculo = 0;
		// }							
		calculo_ = +calculo;			
		var total_calculo_ = Math.round10(calculo_, -fDecimal);			

		/*15. Saldo acumulado Caja y Banco*/
		var saldo_acumulado_caja_banco = detail[i].data['15_saldo_acumulado_caja_banco'][0];	
		saldo_acumulado_caja_banco_    = Math.round10(saldo_acumulado_caja_banco, -fDecimal);	

		var table = `<!-- Table -->
						<div class="table-responsive">
							<table class="table table-bordered table-hover tab-responsive"> 
								<thead> 
									<tr style="background-color: #7952b3; color: #fff;"> 
										<th width="70%" style="font-weight: 200;">CONCEPTO</th> 
										<th style="font-weight: 200;">CANTIDAD</th> 
										<th style="font-weight: 200;">IMPORTE</th> 
									</tr> 
								</thead> 
								<tbody>
									<!-- 1. Venta Combustible --> 
									<tr data-toggle="collapse" data-target=".venta_combustible_${detail[i].id}"> 
										<td>1. Venta Combustible</td> 
										<td>${ total_canti_combustible.toFixed(2) }</td> 
										<td>${ total_venta_combustible.toFixed(2) }</td> 
									</tr> 
									<tr class="collapse venta_combustible_${detail[i].id}"> 
										<td>${ tab }1.1 Liquido</td> 
										<td>${ parseFloat(venta_combustible[0].liquido_canti) }</td> 
										<td>${ parseFloat(venta_combustible[0].liquido) }</td> 
									</tr> 
									<tr class="collapse venta_combustible_${detail[i].id}"> 
										<td>${ tab }1.2 GLP</td> 
										<td>${ parseFloat(venta_combustible[0].glp_canti) }</td> 
										<td>${ parseFloat(venta_combustible[0].glp) }</td> 
									</tr>
									<tr ${dnone} class="collapse venta_combustible_${detail[i].id}"> 
										<th>${ tab }${ tab }Total Venta Combustible</th> 
										<th>${ total_canti_combustible.toFixed(2) }</th> 
										<th>${ total_venta_combustible.toFixed(2) }</th> 
									</tr> 
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr> 
									
									<!-- 2. Venta de Productos y Promociones -->
									<tr data-toggle="collapse" data-target=".venta_producto_promociones_${detail[i].id}"> 
										<td>2. Venta de Productos y Promociones</td> 
										<td>${ canti_de_tienda.toFixed(2) }</td> 
										<td>${ venta_de_tienda.toFixed(2) }</td> 
									</tr>	
									${ venta_productos_promociones_detalle_html }
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr> 	
									
									<!-- Total Venta (1+2) -->
									<tr> 
										<th>${ tab }${ tab }Total Venta${ espacio }<b style="font-size:0.6em; color:red;">(1+2)</b></th> 
										<th>${ total_canti.toFixed(2) }</th> 
										<th>${ total_venta.toFixed(2) }</th> 
									</tr>
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>
									
									<!-- 3. Credito Clientes -->
									<tr data-toggle="collapse" data-target=".credito_clientes_${detail[i].id}"> 
										<td>3. Credito Clientes</td> 
										<td>${ total_val_can.toFixed(2) }</td>
										<td>${ total_val_imp.toFixed(2) }</td>
									</tr> 
									${ vales_credito_detalle_html }
									<tr ${dnone} class="collapse credito_clientes_${detail[i].id}">
										<th>${ tab }${ tab }Total Credito Clientes</th>
										<th>${ total_val_can.toFixed(2) }</th>
										<th>${ total_val_imp.toFixed(2) }</th>
									</tr>
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>

									<!-- 4. Tarjetas de Credito -->
									<tr data-toggle="collapse" data-target=".tarjetas_credito_${detail[i].id}"> 
										<td>4. Tarjetas de Credito</td> 
										<td></td>
										<td>${ total_val_importetarjeta.toFixed(2) }</td>
									</tr> 
									${ tarjetas_credito_detalle_html }
									<tr ${dnone} class="collapse tarjetas_credito_${detail[i].id}">
										<th>${ tab }${ tab }Total Tarjetas de Credito</th>
										<th></th>
										<th>${ total_val_importetarjeta.toFixed(2) }</th>
									</tr>
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>

									<!-- 5. Descuentos -->
									<tr> 
										<td>5. Descuentos</td> 
										<td></td> 
										<td>${ descuentos_total_.toFixed(2) }</td> 
									</tr>										

									<!-- 6. Diferencia de Precio de Vales -->
									<tr> 
										<td>6. Diferencia de Precio de Vales</td> 
										<td></td> 
										<td>${ difprecio_total_.toFixed(2) }</td> 
									</tr>
									
									<!-- 7. Afericiones -->
									<tr> 
										<td>7. Afericiones</td> 
										<td></td> 
										<td>${ afericiones_total_.toFixed(2) }</td> 
									</tr>
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>

									<!-- Total Venta Creditos y Otros No al Contado -->
									<tr> 
										<th>
											${ tab }${ tab }Total Venta Creditos y Otros No al Contado
											<br>${ tab }${ tab }<b style="font-size:0.6em; color:red;">(3+4+5+6+7)</b>
										</th> 
										<th></th> 
										<th>${ TVCO_.toFixed(2) }</th> 
									</tr>

									<!-- Total Efectivo en Boveda (Total Depositos POS) -->
									<tr> 
										<th>
											${ tab }${ tab }Total Efectivo en Boveda (Total Depositos POS)												
										</th> 
										<th></th> 
										<th>${ TDP_.toFixed(2) }</th> 
									</tr>

									<!-- Total Venta Contado -->
									<tr> 
										<th>
											${ tab }${ tab }Total Venta Contado
											<br>${ tab }${ tab }<b style="font-size:0.6em; color:red;">(Total Venta - Total Venta Creditos)</b>
										</th> 
										<th></th> 
										<th>${ TVContado_.toFixed(2) }</th> 
									</tr>
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>

									<!-- 8. Sobrantes Faltantes por Trabajador -->
									<tr data-toggle="collapse" data-target=".sobrantes_faltantes_por_trabajador_${detail[i].id}"> 
										<td>8. Sobrantes Faltantes por Trabajador</td>
										<td></td>
										<td>${ total_val_imp_sob.toFixed(2) }</td> 											
									</tr>
									${ sobrantes_faltantes_por_trabajador_html }
									<tr ${dnone} class="collapse sobrantes_faltantes_por_trabajador_${detail[i].id}">
										<th>${ tab }${ tab }Total Sobrantes y Faltantes por Trabajador</th>
										<th></th>
										<th>${ total_val_imp_sob.toFixed(2) }</th>
									</tr>
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>

									<!-- Diferencia Diaria -->
									<tr> 
										<th>
											${ tab }${ tab }Diferencia Diaria
											<br>${ tab }${ tab }<b style="font-size:0.6em; color:red;">(Total Efectivo en Boveda - Sobrantes y Faltantes - Total Venta Contado)</b>
										</th> 
										<th></th> 
										<th>${ DD_.toFixed(2) }</th> 
									</tr>
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>

									<!-- 10. Ingresos -->
									<tr> 
										<td colspan="3">10. Ingresos</td> 											
									</tr>

									<!-- 10.1 Ingresos al contado del día -->
									<tr data-toggle="collapse" data-target=".ingresos_contado_dia_${detail[i].id}"> 
										<td>${ tab }10.1 Ingresos al contado del día</td> 											
										<td></td> 
										<td>${ total_val_ingresos.toFixed(2) }</td> 
									</tr>
									${ ingresos_contado_dia_html }										

									<!-- 10.2 Cobranzas y Amortizaciones por CC -->
									<tr data-toggle="collapse" data-target=".ingresos_cobranzas_cc_${detail[i].id}"> 
										<td>${ tab }10.2 Cobranzas y Amortizaciones por CC</td> 											
										<td></td> 
										<td>${ total_val_ingresos_cc.toFixed(2) }</td> 
									</tr>
									${ ingresos_cobranzas_cc_html }
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>										

									<!-- 12. Egresos -->
									<tr data-toggle="collapse" data-target=".egresos_${detail[i].id}"> 
										<td>12. Egresos</td> 											
										<td></td> 
										<td>${ total_val_egresos.toFixed(2) }</td> 
									</tr>
									${ egresos_html }										
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>

									<!-- 13. Documentos de Venta Manual -->
									<tr data-toggle="collapse" data-target=".manuales_${detail[i].id}"> 
										<td>13. Documentos de Venta Manual</td> 											
										<td></td> 
										<td>${ total_manuales_.toFixed(2) }</td> 
									</tr>
									${ manuales_html }	
									<tr> 
										<td colspan="3">${tab}</td> 
									</tr>		
									
									<!-- 14. Saldo Neto a Depositar -->
									<tr> 
										<td>
											14. Saldo Neto a Depositar
											<br><b style="font-size:0.6em; color:red;">(Total Venta Contado + Sobrantes y Faltantes - Ingresos al contado del dia - Egresos)</b>
										</td> 
										<td></td> 
										<th>${ total_calculo_.toFixed(2) }</th> 
									</tr>										
									<tr ${dnone}> 
										<td colspan="3">${tab}</td> 
									</tr>											

									<!-- 15. Saldo acumulado Caja y Banco -->
									<tr> 
										<td>15. Saldo acumulado Caja y Banco</td> 
										<td></td> 
										<th>${ saldo_acumulado_caja_banco_.toFixed(2) }</th> 
									</tr>
								</tbody> 
							</table>
						</div>
						`;

		//Obtenemos contenido para Inventario de Combustible
		var contenido_inv = '';
		var numfilas = 0;
		var results1 = detail[i].data['inventario_combustible'];
		
		for (var a in results1['propiedades']) {				
			var almacenes = results1['propiedades'][a];
			
			for (var ch_almacen in almacenes['almacenes']) {
				var venta = almacenes['almacenes'][ch_almacen];
				
				for (var dt_producto in venta['partes']) { 
					/**
					 * dt_producto es el indice del array y esta formado por Nombre del Articulo y Codigo del Articulo (90 OCT|11620302)
					 * Obtenemos el codigo del articulo en dt_codigo
					 */
					var porciones = dt_producto.split('|');
					dt_codigo     = porciones['1'];

					var producto = venta['partes'][dt_producto];

					if (dt_codigo != '11620307'){ //Si no es GLP
						numfilas = numfilas +1;
						contenido_inv += imprimirLinea(producto, dt_codigo, venta['totales']['total']['ventas']);
					}
				}

				contenido_inv += imprimirLinea(venta['totales']['total'], "Total");
				contenido_inv += '<tr><td colspan="11">&nbsp;</td></tr><tr>';
				numfilas = 0;
									
				for (var dt_producto in venta['partes']) {
					/**
					 * dt_producto es el indice del array y esta formado por Nombre del Articulo y Codigo del Articulo (90 OCT|11620302)
					 * Obtenemos el codigo del articulo en dt_codigo
					 */
					var porciones = dt_producto.split('|');
					dt_codigo     = porciones['1'];

					var producto = venta['partes'][dt_producto];

					if (dt_codigo == '11620307'){ //Si no es GLP
						numfilas = numfilas +1;
						contenido_inv += imprimirLinea(producto, dt_codigo, '');
					}
				}
			}
		}

		//Inventario Combustible
		var table_inv = '';
		if ( data.inventariocombustible == 'Si' ) {
			table_inv = `<!-- Table -->
							<hr style="margin-top:0px">
							<br>
							<div class="table-responsive">
								<table class="table table-bordered table-hover"> 
									<thead> 
										<tr class="bg-primary" style="color: #fff;"> 
											<th colspan="11" style="font-weight: 200;">COMBUSTIBLES</th> 
										</tr> 
										<tr class="bg-primary" style="color: #fff;"> 
											<th style="font-weight: 200;">PRODUCTO</th> 
											<th style="font-weight: 200;">STOCK INICIAL</th> 
											<th style="font-weight: 200;">COMPRAS</th> 
											<th style="font-weight: 200;">VENTAS</th> 
											<th style="font-weight: 200;">%</th> 
											<th style="font-weight: 200;">TRANSFERENCIAS</th> 
											<th style="font-weight: 200;">STOCK FINAL</th> 
											<th style="font-weight: 200;">MEDICION</th> 
											<th style="font-weight: 200;">DIF. DIA</th> 
											<th style="font-weight: 200;">DIF. MES</th> 
											<th style="font-weight: 200;">IMPORTE VENTA</th> 
										</tr> 
									</thead> 
									<tbody> 
										${contenido_inv}
									</tbody> 
								</table>
							</div>	
			`;
		}
		

		color_id = getRandomColor();
		if(taxid != detail[i].group.taxid) {
			html += (i != 0 ? '<hr>' : '');
			html += '<div class="panel-group-station"><h5 title="RUC: '+detail[i].group.taxid+'">'+detail[i].group.name+'</h5></div>';
			taxid = detail[i].group.taxid;
		}
		if(!detail[i].isConnection) {
			html += `<div class="container-station">
							<div class="panel panel-danger">
							<div class="panel-heading">
								<span class="glyphicon glyphicon-exclamation-sign"></span> 
								<strong>Sin conexión.</strong>
							</div>`;
		} else {
			html += `<div class="container-station" style="margin-bottom: 60px;">
							<div class="panel panel-default">`;
		}
		html += `<div class="panel-heading">
						<span class="glyphicon glyphicon-stop" style="color: ${color_id}"></span> ${num} ${detail[i].name} 
					</div>
		
					<!--
					<div class="panel-body detail-station" data-station="${detail[i].id}"
						data-begindate="${data.beginDate}" data-enddate="${data.endDate}" data-typestation="${data.typeStation}"
						data-typecost="${data.typeCost}" title="Ver detalle de ${detail[i].name}">
						<span class="glyphicon glyphicon-stop" style="color: ${color_id}"></span> ${num} ${detail[i].name} 
					</div>
					-->
					
					${table}						
					${table_inv}
					
					<!-- 
					<div class="panel-footer">
						<div class="row">
							<div class="col-md-6">
								<div class="mid"></div>
								<div class="mid"></div>
							</div>
							<div class="col-md-6">
								<div class="mid"></div>
								<div class="mid"></div>
							</div>
						</div>
					</div>
					-->

				</div>
			</div>`;
		num++;
	}

	storageStations();

	$('.container-ss-station').removeClass('none');

	setDataResultRequest2('.download-liquidacion-diaria',data);

	return html;
}

function searchValesClientesCredito(t){
	//Loading Viejo
	// $('.result-search').html('<br><br>'+loading());
	
	//Loading Bootstrap 4
	$('.table-responsive').addClass('d-none');
	$('.result-search').html(loading_bootstrap4());

	//Validar fecha
	var valStartDate = checkDate($('#start-date-request').val(),'/');
	var valEndDate = checkDate($('#end-date-request').val(),'/');

	console.log('valStartDate: '+valStartDate);
	console.log('valEndDate: '+valEndDate);

	if(valStartDate == 0) {
		//Error en formato de fecha
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span>, Error en formato de fecha.'));
		$('.btn-search').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	} else if(valStartDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span>, No se puede consultar con esta fecha'));
		$('.btn-search').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	}

	if(valEndDate == 0) {
		//error en formato de fecha
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	} else if(valEndDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	}

	//Obtenemos parametros
	var paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),
		typeStation: $('#typeStation').val(),

		/*No sirve en este reporte*/
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
		/*Cerrar no sirve en este reporte*/  
	}
	console.log(paramsRequest);
	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);

	var sBeing = paramsRequest.dateBegin.split("/");
	var sEnd = paramsRequest.dateEnd.split("/");

	var sBeing = sBeing[1]+'/'+sBeing[2];
	var sEnd = sEnd[1]+'/'+sEnd[2];

	console.log('sBeing: '+sBeing+', sEnd: '+sEnd);

	if(sBeing != sEnd) {
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, Las fechas a consultar deben estar en el mismo mes.'))
		$('.btn-search').prop('disabled', false);
		return false;
	}

	var charMode = $('#chart-mode').val();

	//Enviamos parametros
	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getValesClientesCredito', paramsRequest, function(data) {
		// console.log('data:', data); //ELIMINAR
		console.log('data:', JSON.stringify(data)); //ELIMINAR
		// return; //ELIMINAR
		
		checkSession(data);
		$('.btn-search').prop('disabled', false);
		console.log('Dentro del callback');
		console.log(data);
		$('.result-search').html(templateStationsSearchValesClientesCredito(data, data.typeStation, charMode));

		//Datatables		
		// var data = [
		// 	[
		// 		 "Tiger Nixon",
		// 		 "System Architect",
		// 		 "Edinburgh",
		// 		 "5421",
		// 		 "2011/04/25",
		// 		 "$3,120"
		// 	],
		// 	[
		// 		 "Garrett Winters",
		// 		 "Director",
		// 		 "Edinburgh",
		// 		 "8422",
		// 		 "2011/07/25",
		// 		 "$5,300"
		// 	]
	  	// ];
		// console.log(data);

		var tabla;		
		tabla = $('#table_id').DataTable( {
			"processing": true,
        	"serverSide": false,
			data: data.listJson,
			language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
				// "buttons": {
				// 	"copy": "<i class='fas fa-copy'></i>",
				// 	"csv": "<i class='fas fa-file-csv'></i>",
				// 	"excel": "<i class='fas fa-file-excel'></i>",
				// 	"pdf": "<i class='fas fa-pdf'></i>",
				// 	"print": "<i class='fas fa-print'></i>",
				// 	"pageLength": {
				// 		_: "Mostrar %d registros",
           	// 		'-1': "Mostrar todos los registros"
				// 	}
				// }
        	},
			"lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
		  	"pageLength": 10,
			dom: 'Bfrtip', // Blfrtip
			// buttons: [
			//     'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
			// ],
        	buttons: [
				{
					extend: 'copy',    
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'csv',    
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'excel',            
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'pdf',
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'print',
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'pageLength',        
				}
			],
			"columnDefs": [
			    {
			        "targets": [ 0 ],
			        "visible": false,                
			    }
			],
			"bDestroy": true
	 	});						

		var div = $('<div class="row mb-3">\n\
				<div id="div" class="col-sm-12 col-md-7">\n\
				</div>\n\
				<div id="div2" class="col-sm-12 col-md-5">\n\
				</div>\n\
				</div>');
		$('#table_id').before(div);              // Creamos el div antes de id='table_id'
		$('#wea').appendTo('#div');              // Agregamos id='wea' dentro del div
		$('.dt-buttons').appendTo('#div');       // Agregamos class="dt-buttons" dentro del div
		$('#table_id_filter').appendTo('#div2'); // Agregamos id="table_id_filter" dentro del div

		var div2 = $('<div class="row">\n\
				<div id="div3" class="col-sm-12 col-md-5">\n\
				</div>\n\
				<div id="div4" class="col-sm-12 col-md-7">\n\
				</div>\n\
				</div>');
		$('#table_id').after(div2);                // Creamos el div2 antes de id='table_id'
		$('#table_id_info').appendTo('#div3');     // Agregamos id="table_id_info" dentro del div2
		$('#table_id_paginate').appendTo('#div4'); // Agregamos id="table_id_paginate" dentro del div2

		$('.dt-buttons button').removeClass('btn-secondary'); // Remueve class="btn-secondary"
		$('.dt-buttons button').addClass('btn-primary');      // Agrega class="btn-primary"

		$(".table-responsive").removeClass('d-none');
	}, 'json');
}

/**
 * Plantilla de estaciones buscadas para Consultas - Clientes Credito
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearchValesClientesCredito(data,t,cm) {	
	return '';
}

function searchComprobantesCobranza(t){
	//Loading Viejo
	// $('.result-search').html('<br><br>'+loading());
	
	//Loading Bootstrap 4
	$('.table-responsive').addClass('d-none');
	$('.result-search').html(loading_bootstrap4());

	//Validar fecha
	var valStartDate = checkDate($('#start-date-request').val(),'/');
	var valEndDate = checkDate($('#end-date-request').val(),'/');

	console.log('valStartDate: '+valStartDate);
	console.log('valEndDate: '+valEndDate);

	if(valStartDate == 0) {
		//Error en formato de fecha
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span>, Error en formato de fecha.'));
		$('.btn-search').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	} else if(valStartDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span>, No se puede consultar con esta fecha'));
		$('.btn-search').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	}

	if(valEndDate == 0) {
		//error en formato de fecha
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	} else if(valEndDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	}

	//Obtenemos parametros
	var paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),
		typeStation: $('#typeStation').val(),
		
		ruc: $('#select-ruc').val(),
		state: $('#select-state').val(),

		/*No sirve en este reporte*/
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
		/*Cerrar no sirve en este reporte*/  
	}
	console.log(paramsRequest);
	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);

	var sBeing = paramsRequest.dateBegin.split("/");
	var sEnd = paramsRequest.dateEnd.split("/");

	var sBeing = sBeing[1]+'/'+sBeing[2];
	var sEnd = sEnd[1]+'/'+sEnd[2];

	console.log('sBeing: '+sBeing+', sEnd: '+sEnd);

	if(sBeing != sEnd) {
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, Las fechas a consultar deben estar en el mismo mes.'))
		$('.btn-search').prop('disabled', false);
		return false;
	}

	var charMode = $('#chart-mode').val();

	//Enviamos parametros
	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getComprobantesCobranza', paramsRequest, function(data) {
		// console.log('data:', data); //ELIMINAR
		console.log('data:', JSON.stringify(data)); //ELIMINAR
		// return; //ELIMINAR
		
		checkSession(data);
		$('.btn-search').prop('disabled', false);
		console.log('Dentro del callback');
		console.log(data);
		$('.result-search').html(templateStationsSearchComprobantesCobranza(data, data.typeStation, charMode));

		//Datatables		
		// var data = [
		// 	[
		// 		 "Tiger Nixon",
		// 		 "System Architect",
		// 		 "Edinburgh",
		// 		 "5421",
		// 		 "2011/04/25",
		// 		 "$3,120"
		// 	],
		// 	[
		// 		 "Garrett Winters",
		// 		 "Director",
		// 		 "Edinburgh",
		// 		 "8422",
		// 		 "2011/07/25",
		// 		 "$5,300"
		// 	]
	  	// ];
		// console.log(data);

		var tabla;		
		tabla = $('#table_id').DataTable( {
			"processing": true,
        	"serverSide": false,
			data: data.listJson,
			language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
				// "buttons": {
				// 	"copy": "<i class='fas fa-copy'></i>",
				// 	"csv": "<i class='fas fa-file-csv'></i>",
				// 	"excel": "<i class='fas fa-file-excel'></i>",
				// 	"pdf": "<i class='fas fa-pdf'></i>",
				// 	"print": "<i class='fas fa-print'></i>",
				// 	"pageLength": {
				// 		_: "Mostrar %d registros",
           	// 		'-1': "Mostrar todos los registros"
				// 	}
				// }
        	},
			"lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
		  	"pageLength": 10,
			dom: 'Bfrtip', // Blfrtip
			// buttons: [
			//     'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
			// ],
        	buttons: [
				{
					extend: 'copy',    
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'csv',    
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'excel',            
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'pdf',
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'print',
					exportOptions: {
						columns: "thead th:not(.noExport)"
					}
				},
				{
					extend: 'pageLength',        
				}
			],
			"columnDefs": [
			    {
			        "targets": [ 0 ],
			        "visible": false,                
			    }
			],
			"bDestroy": true
	 	});						

		var div = $('<div class="row mb-3">\n\
				<div id="div" class="col-sm-12 col-md-7">\n\
				</div>\n\
				<div id="div2" class="col-sm-12 col-md-5">\n\
				</div>\n\
				</div>');
		$('#table_id').before(div);              // Creamos el div antes de id='table_id'
		$('#wea').appendTo('#div');              // Agregamos id='wea' dentro del div
		$('.dt-buttons').appendTo('#div');       // Agregamos class="dt-buttons" dentro del div
		$('#table_id_filter').appendTo('#div2'); // Agregamos id="table_id_filter" dentro del div

		var div2 = $('<div class="row">\n\
				<div id="div3" class="col-sm-12 col-md-5">\n\
				</div>\n\
				<div id="div4" class="col-sm-12 col-md-7">\n\
				</div>\n\
				</div>');
		$('#table_id').after(div2);                // Creamos el div2 antes de id='table_id'
		$('#table_id_info').appendTo('#div3');     // Agregamos id="table_id_info" dentro del div2
		$('#table_id_paginate').appendTo('#div4'); // Agregamos id="table_id_paginate" dentro del div2

		$('.dt-buttons button').removeClass('btn-secondary'); // Remueve class="btn-secondary"
		$('.dt-buttons button').addClass('btn-primary');      // Agrega class="btn-primary"

		$(".table-responsive").removeClass('d-none');
	}, 'json');
}

/**
 * Plantilla de estaciones buscadas para Consultas - Comprobantes cobranza
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearchComprobantesCobranza(data,t,cm) {	
	return '';
}