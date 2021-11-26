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
 * Buscar ventas en estacion(es), combustibles y market
 * typeStation:
 * 0 - origen: ventas/combustibles, mod: TOTALS_SALE_COMB
 * 1 - 
 */
function searchSale() {
	console.log('function searchSale');
	clearStations();
	$('.container-chart-station').addClass('d-none');
	$('.container-ss-station').addClass('d-none');
	$('.result-search').html(loading_bootstrap4());
	var valStartDate = checkDate($('#start-date-request').val(),'/');
	var valEndDate = checkDate($('#end-date-request').val(),'/');

	console.log('valStartDate: '+valStartDate);
	console.log('valEndDate: '+valEndDate);

	if(valStartDate == 0) {
		//Error en formato de fecha
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	} else if(valStartDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	}

	if(valEndDate == 0) {
		//error en formato de fecha
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	} else if(valEndDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-sale').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	}

	paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),
		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	}
	console.log('paramsRequest:', paramsRequest);
	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);

	var sBeing = paramsRequest.dateBegin.split("/");
	var sEnd = paramsRequest.dateEnd.split("/");

	var sBeing = sBeing[1]+'/'+sBeing[2];
	var sEnd = sEnd[1]+'/'+sEnd[2];

	console.log('sBeing: '+sBeing+', sEnd: '+sEnd);

	if(sBeing == sEnd) {
		var charMode = $('#chart-mode').val();
		var count = 0;
		console.log('searchsale!');
		console.log('typeStation: '+paramsRequest.typeStation);
		//return false;
		// $.ajax({
		// 	type: 'post',
		// 	url: url+'requests/getSales',
		// 	data: paramsRequest,
		// 	dataType: 'json'
		// })
		// .done(function(data){
		// 	checkSession(data);
		// 	$('.btn-search-sale').prop('disabled', false);
		// 	console.log('Dentro del callback');
		// 	console.log(data);
		// 	$('.result-search').html(templateStationsSearch(data, data.typeStation, charMode));
		// });
		$.post(url+'requests/getSales', paramsRequest, function(data) {
			checkSession(data);
			$('.btn-search-sale').prop('disabled', false);
			console.log('Dentro del callback');
			console.log(data);
			$('.result-search').html(templateStationsSearch(data, data.typeStation, charMode));
		}, 'json');
	} else {
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Las fechas a consultar deben estar en el mismo mes.'))
		$('.btn-search-sale').prop('disabled', false);
	}
}

/**
 * Plantilla de estaciones buscadas para Ventas - Combustible
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearch(data,t,cm) {
	console.log('data en templateStationsSearch:', data);

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
	for(var i = 0; i<count; i++) {
		color_id = getRandomColor();
		if(taxid != detail[i].group.taxid) {
			html += (i != 0 ? '<hr>' : '');
			html += `<div class="card shadow">
							<div class="card-header bg-primary text-white">
								<h5 class="m-0" title="RUC: ${detail[i].group.taxid}">${detail[i].group.name}</h5>
							</div>
						</div>`;
			taxid = detail[i].group.taxid;
		}
		if(!detail[i].isConnection) {
			html += `<div class="">
							<div class="card shadow mb-4">
								<div class="card-header bg-danger text-white">
									<span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Sin conexión.</strong>
								</div>`;
		} else {
			html += `<div class="">
							<div class="card shadow mb-4">`;
		}
					html += `<div class="card-body detail-station" data-station="${detail[i].id}"
									data-begindate="${data.beginDate}" data-enddate="${data.endDate}" data-typestation="${data.typeStation}"
									data-typecost="${data.typeCost}" title="Ver detalle de ${detail[i].name}"
									>
										<span class="glyphicon glyphicon-stop" style="color: ${color_id}">
										</span> ${num}. ${detail[i].name}
								</div>
									
								<div class="card-footer bg-gray text-dark">
									<div class="row">
										<div class="col-md-6">
											<div class="mid">
												<b>Venta: S/ ${numeral(detail[i].total_venta).format('0,0')}</b>
											</div>
											<div class="mid">
												<b>${numeral(detail[i].total_cantidad).format('0,0')} ${unit}</b>
											</div>
										</div>
										<div class="col-md-6">
											<div class="mid"><b>Costo: S/ ${numeral(detail[i].total_costo).format('0,0')}</b></div>
											<div class="mid"><b>Margen: S/ ${numeral(detail[i].total_utilidad).format('0,0')}</b></div>
										</div>
									</div>
								</div>

							</div>
						</div>`;

		console.log('> gran_util: '+gran_util);
		gran_total += detail[i].total_venta != '' ? parseFloat(detail[i].total_venta) : parseFloat(0);
		gran_qty += detail[i].total_cantidad != '' ? parseFloat(detail[i].total_cantidad) : parseFloat(0);
		gran_util += detail[i].total_utilidad != '' ? parseFloat(detail[i].total_utilidad) : parseFloat(0);
		gran_cost += detail[i].total_costo != '' ? parseFloat(detail[i].total_costo) : parseFloat(0);

		/**
		 * Importante: considerar que PUSH esta agregando venta, cantidad y utilidad
		 * solo si el monto es positivo, caso contrario solo se agrega 0(cero)
		 */
		dataStations.push({
			name: detail[i].initials,
			total: detail[i].total_venta > 0 ? parseFloat(detail[i].total_venta) : parseFloat(0),
			qty: detail[i].total_cantidad > 0 ? parseFloat(detail[i].total_cantidad) : parseFloat(0),
			util: detail[i].total_utilidad > 0 ? parseFloat(detail[i].total_utilidad) : parseFloat(0),
			color: color_id,
			data: detail[i].data,
		});
		num++;
	}

	//gran_util
	console.log('> gran_util: '+gran_util);
	html += `<div class="card shadow mb-4">
					<div class="card-header bg-primary text-white" title="Ver total de productos">
						<div>Total General</div>
					</div>
					<div class="card-footer bg-gray text-dark all-result-sales-comb" data-station="${data.id}" data-begindate="${data.beginDate}" 
					data-enddate="${data.endDate}" data-typecost="${data.typeCost}" data-typestation="${data.typeStation}">
						<div class="row">
							<div class="col-md-6">
								<div class="mid">
									<b>Venta: S/ ${numeral(gran_total).format('0,0')}</b>
								</div>
								<div class="mid">
									<b>${numeral(gran_qty).format('0,0')} ${unit}</b>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mid">
									<b>Costo: S/ ${numeral(gran_cost).format('0,0')}</b>
								</div>
								<div class="mid">
									<b>Margen: S/ ${numeral(gran_util).format('0,0')}</b>
								</div>
							</div>
						</div>
					</div>
				</div>`;

	storageStations();
	if(count > 1) {
		$('.container-chart-station').removeClass('d-none');
			
		if(cm == 0) {
			viewChartBarStation();
			viewChartBarStationQty();
			viewChartBarStationUtil();
		} else {
			viewChartStation();
		}
	}
	$('.container-ss-station').removeClass('d-none');

	setDataResultRequest('.download-comb-sales',data);

	return html;
}

/**
 * Plantilla de estaciones buscadas para Ventas - Ventas por Horas
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearchSalesForHours(data,t,cm) { //POR EDITAR
	console.log('data en templateStationsSearchSalesForHours:', data);

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
	var unit = t == 6 ? 'Gln' : '';

	var mostrar_cards = ""; //none

	var color_id, taxid;
	for(var i = 0; i<count; i++) {
		color_id = getRandomColor();
		if(taxid != detail[i].group.taxid) {
			html += (i != 0 ? '<hr class="'+mostrar_cards+'">' : '');
			html += '<div class="panel-group-station '+mostrar_cards+'"><h5 title="RUC: '+detail[i].group.taxid+'">'+detail[i].group.name+'</h5></div>';
			taxid = detail[i].group.taxid;
		}

		var mostrar = true;
		if(!detail[i].isConnection) { //Si no hay conexion
			html += '<div class="container-station"><div class="panel panel-danger '+mostrar_cards+'">'
			+'<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Sin conexión.</strong></div>';

			mostrar = false;
		} else {
			verificar_data = detail[i].data;
			
			if(verificar_data.length == 0){ //Si hay conexion pero datos vacios
				html += '<div class="container-station"><div class="panel panel-success '+mostrar_cards+'">'
				+'<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>No hay informacion.</strong></div>';

				mostrar = false;
			}else{ //Si hay conexion y hay datos
				html += '<div class="container-station"><div class="panel panel-default '+mostrar_cards+'">';
			}
		}

		if(mostrar == true){
			//REPORTE VENTAS POR HORAS
			var dataAlmacenes = detail[i].data.propiedades.ESTACION.almacenes;
			console.log('dataAlmacenes', dataAlmacenes);

			for (var almacenes in dataAlmacenes) {
				// console.log(`${almacenes}: ${dataAlmacenes[almacenes]}`);
				
				var tbody_almacenes = `<tr class="success">
											<th colspan="28">${almacenes}</th>
										</tr>`;

				var nombre_combustible = {
					'11620301': '84',
					'11620302': '90',
					'11620303': '97',
					'11620304': 'D2',
					'11620305': '95',
					'11620307': 'GLP',
					'11620308': 'GNV'
				};
				var partes = dataAlmacenes[almacenes].partes;
				for (var fecha in partes) {
					var th = "";
					for (var k=0; k<24; k++) {
						th += `<th>${ (partes[fecha][k] === undefined) ? 0 : partes[fecha][k].toFixed(0) }</th>`;					
					}

					tbody_almacenes += `<tr>
											<th>${nombre_combustible[fecha]}</th>
											${th}
											<th>${partes[fecha].total.toFixed(0)}</th>
											<th>${partes[fecha].promedio.toFixed(0)}</th>
										</tr>`;
				}
			}

			var th_general = "";
			for (var k=0; k<24; k++) {
				th_general += `<th>${ (detail[i].data.totales[k] === undefined) ? 0 : detail[i].data.totales[k].toFixed(0)}</th>`;					
			}

			var th_promedio = "";
			for (var k=0; k<24; k++) {
				th_promedio += `<th>${ (detail[i].data.promedio[k] === undefined) ? 0 : detail[i].data.promedio[k].toFixed(0)}</th>`;					
			}

			var th_porcentaje = "";
			for (var k=0; k<24; k++) {
				th_porcentaje += `<th>${ (detail[i].data.porcentaje[k] === undefined) ? 0 : detail[i].data.porcentaje[k].toFixed(2)}</th>`;					
			}

			var tbody = `<tbody>
							${tbody_almacenes}
							<tr class="success">
								<th>Total General</th>
								${th_general}
								<th>${detail[i].data.totales.total.toFixed(0)}</th>
								<th>${detail[i].data.totales.promedio.toFixed(0)}</th>
							</tr>
							<tr class="success">
								<th>Promedio</th>
								${th_promedio}
								<th>${detail[i].data.promedio.total.toFixed(0)}</th>
								<th>${detail[i].data.promedio.promedio.toFixed(0)}</th>
							</tr>
							<tr class="success">
								<th>Porcentaje (%)</th>
								${th_porcentaje}
								<th>${detail[i].data.porcentaje.total}</th>
								<th>${detail[i].data.porcentaje.promedio}</th>
							</tr>
						</tbody>`;
			//CERRAR REPORTE VENTAS POR HORAS
		}

		var tabla = "";
		if(mostrar == true){
			tabla += `<br>
					<br>
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr class="success">
									<th>Horas</th>
									<th>00</th>
									<th>01</th>
									<th>02</th>
									<th>03</th>
									<th>04</th>
									<th>05</th>
									<th>06</th>
									<th>07</th>
									<th>08</th>
									<th>09</th>
									<th>10</th>
									<th>11</th>
									<th>12</th>
									<th>13</th>
									<th>14</th>
									<th>15</th>
									<th>16</th>
									<th>17</th>
									<th>18</th>
									<th>19</th>
									<th>20</th>
									<th>21</th>
									<th>22</th>
									<th>23</th>
									<th>Total</th>
									<th>Promedio</th>
								</tr>
							</thead>
							${tbody}	
						</table>
					</div>`;
		}
		
		html += `<div class="panel-body" data-station="${detail[i].id}" 
						data-begindate="${data.beginDate}" data-enddate="${data.endDate}" data-typestation="${data.typeStation}"
						data-typecost="${data.typeCost}" title="Ver detalle de ${detail[i].name}">
						<span class="glyphicon glyphicon-stop" style="color: ${color_id}"></span> ${num + ". " + detail[i].name}

						<!-- REPORTE VENTAS POR HORAS -->
						${tabla}
						<!-- CERRRAR REPORTE VENTAS POR HORAS -->
					</div>
					<!-- 
					<div class="panel-footer">
						<div class="row">
							<div class="col-md-6">
								<div class="mid"><b>Venta: S/ ${numeral(detail[i].total_venta).format('0,0')}</b></div>
								<div class="mid"><b>${numeral(detail[i].total_cantidad).format('0,0') + " " + unit}</b></div>
							</div>
							<div class="col-md-6">
								<div class="mid"><b>Costo: S/ ${numeral(detail[i].total_costo).format('0,0')}</b></div>
								<div class="mid"><b>Margen: S/ ${numeral(detail[i].total_utilidad).format('0,0')}</b></div>
							</div>
						</div>
					</div>
					-->
					</div>`;

		// console.log('> gran_util: '+gran_util);
		// gran_total += detail[i].total_venta != '' ? parseFloat(detail[i].total_venta) : parseFloat(0);
		// gran_qty += detail[i].total_cantidad != '' ? parseFloat(detail[i].total_cantidad) : parseFloat(0);
		// gran_util += detail[i].total_utilidad != '' ? parseFloat(detail[i].total_utilidad) : parseFloat(0);
		// gran_cost += detail[i].total_costo != '' ? parseFloat(detail[i].total_costo) : parseFloat(0);

		// /**
		//  * Importante: considerar que PUSH esta agregando venta, cantidad y utilidad
		//  * solo si el monto es positivo, caso contrario solo se agrega 0(cero)
		//  */
		// dataStations.push({
		// 	name: detail[i].initials,
		// 	total: detail[i].total_venta > 0 ? parseFloat(detail[i].total_venta) : parseFloat(0),
		// 	qty: detail[i].total_cantidad > 0 ? parseFloat(detail[i].total_cantidad) : parseFloat(0),
		// 	util: detail[i].total_utilidad > 0 ? parseFloat(detail[i].total_utilidad) : parseFloat(0),
		// 	color: color_id,
		// 	data: detail[i].data,
		// });
		num++;
	}

	//CUADRO FINAL Y TOTAL
	var data_all_stations = data.all_stations;
	var data_totales_combustibles = data.all_stations.totales_combustibles;
	var data_estaciones = data.all_stations.estaciones;
	
	var tbody_totales_combustible = '';
	var i = 0;
	for (var combustible in data_totales_combustibles) {
		// console.log(`${combustible}: ${data_totales_combustibles[combustible]}`);

		var th = "";
		for (var k=0; k<24; k++) {
			th += `<th>${ (data_totales_combustibles[combustible][k] === undefined) ? 0 : data_totales_combustibles[combustible][k].toFixed(0) }</th>`;					
		}

		//RECORREMOS LA DATA DE LAS ESTACIONES
		var tr_estaciones = "";
		for (var estacion in data_estaciones) {
			// console.log(`${estacion}: ${data_estaciones[estacion]}`);

			//RECORREMOS LOS DATOS DE LOS COMBUSTIBLES 
			var th_estaciones = "";
			var data_estaciones_array = data_estaciones[estacion];
			for(var combustible_dentro_estaciones in data_estaciones_array){
				if(combustible_dentro_estaciones == combustible){
					for (var k=0; k<24; k++) {
						th_estaciones += `<th>${ (data_estaciones_array[combustible_dentro_estaciones][k]) === undefined ? 0 : data_estaciones_array[combustible_dentro_estaciones][k].toFixed(0) }</th>`;					
					}
					th_estaciones += `<th>${ (data_estaciones_array[combustible_dentro_estaciones].total) === undefined ? 0 : data_estaciones_array[combustible_dentro_estaciones].total.toFixed(0) }</th>`;
					th_estaciones += `<th>${ (data_estaciones_array[combustible_dentro_estaciones].promedio) === undefined ? 0 : data_estaciones_array[combustible_dentro_estaciones].promedio.toFixed(0) }</th>`;
				}
			}
			//CERRAR RECORREMOS LOS DATOS DE LOS COMBUSTIBLES 

			//SI NO ES EL COMBUSTIBLE
			if(th_estaciones == ""){
				for (var k=0; k<24; k++) {
					th_estaciones += `<th>0</th>`;					
				}
				th_estaciones += `<th>0</th>`;					
				th_estaciones += `<th>0</th>`;					
			}
			//CERRAR SI NO ES EL COMBUSTIBLE

			tr_estaciones += `<tr class="text-primary collapse order${i}">
									<th>${estacion}</th>
									${th_estaciones}
								</tr>`;
		}
		//CERRAR RECORREMOS LA DATA DE LAS ESTACIONES

		tbody_totales_combustible += `<tr data-toggle='collapse' data-target='.order${i}'>
										<th>${nombre_combustible[combustible]}</th>
										${th}
										<th>${data_totales_combustibles[combustible].total.toFixed(0)}</th>
										<th>${data_totales_combustibles[combustible].promedio.toFixed(0)}</th>
									</tr>
									${tr_estaciones}`;
		i++;
	}

	var th_all_general = "";
	for (var k=0; k<24; k++) {
		th_all_general += `<th>${ (data_all_stations.totales[k] === undefined) ? 0 : data_all_stations.totales[k].toFixed(0)}</th>`;					
	}

	var th_all_promedio = "";
	for (var k=0; k<24; k++) {
		th_all_promedio += `<th>${ (data_all_stations.promedio[k] === undefined) ? 0 : data_all_stations.promedio[k].toFixed(0)}</th>`;					
	}

	var th_all_porcentaje = "";
	for (var k=0; k<24; k++) {
		th_all_porcentaje += `<th>${ (data_all_stations.porcentaje[k] === undefined) ? 0 : data_all_stations.porcentaje[k].toFixed(2)}</th>`;					
	}

	var tbody_total_general = `<tbody>
									${tbody_totales_combustible}
									<tr class="success">
										<th>Total General</th>
										${th_all_general}
										<th>${data_all_stations.totales.total.toFixed(0)}</th>
										<th>${data_all_stations.totales.promedio.toFixed(0)}</th>
									</tr>
									<tr class="success">
										<th>Promedio</th>
										${th_all_promedio}
										<th>${data_all_stations.promedio.total.toFixed(0)}</th>
										<th>${data_all_stations.promedio.promedio.toFixed(0)}</th>
									</tr>
									<tr class="success">
										<th>Porcentaje (%)</th>
										${th_all_porcentaje}
										<th>${data_all_stations.porcentaje.total}</th>
										<th>${data_all_stations.porcentaje.promedio}</th>
									</tr>
								</tbody>`;

	var tabla_total_general = `<div class="table-responsive">
									<table class="table table-bordered table-hover">
										<thead>
											<tr class="success">
												<th>Horas</th>
												<th>00</th>
												<th>01</th>
												<th>02</th>
												<th>03</th>
												<th>04</th>
												<th>05</th>
												<th>06</th>
												<th>07</th>
												<th>08</th>
												<th>09</th>
												<th>10</th>
												<th>11</th>
												<th>12</th>
												<th>13</th>
												<th>14</th>
												<th>15</th>
												<th>16</th>
												<th>17</th>
												<th>18</th>
												<th>19</th>
												<th>20</th>
												<th>21</th>
												<th>22</th>
												<th>23</th>
												<th>Total</th>
												<th>Promedio</th>
											</tr>
										</thead>
										${tbody_total_general}	
									</table>
								</div>`;

	//gran_util
	// console.log('> gran_util: '+gran_util);
	html += `<div class="panel panel-primary"> 
				<div class="panel-heading" title="Ver total de productos"><div class="panel-title">Total General</div></div>
					<div class="panel-body" data-station="${data.id}" data-begindate="
					${data.beginDate}" data-enddate="${data.endDate}" data-typecost="${data.typeCost}"
					data-typestation="${data.typeStation}">
					<!-- REPORTE VENTAS POR HORAS -->
						${tabla_total_general}
					<!-- CERRRAR REPORTE VENTAS POR HORAS -->
				</div>
			</div>`;
	//CERRAR CUADRO FINAL Y TOTAL

	storageStations();
	// if(count > 1) {
	// 	$('.container-chart-station').removeClass('d-none');
			
	// 	if(cm == 0) {
	// 		viewChartBarStation();
	// 		viewChartBarStationQty();
	// 		viewChartBarStationUtil();
	// 	} else {
	// 		viewChartStation();
	// 	}
	// }
	$('.container-ss-station').removeClass('d-none');

	setDataResultRequest2('.download-sales-for-hours',data);

	return html;
}

/**
 * Plantilla de estaciones buscadas para Ventas - Liquidacion diaria
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearchLiquidacionDiaria_(data,t,cm) {
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
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	/*Cabecera*/
	var thead_name_stations = '';
	var thead_stations = '';

	/*1. Venta Combustible*/
	var tbody_1_1_liquido = '';
	var tbody_1_1_glp = '';
	var tbody_total_venta_combustible = '';
	
	/*2. Venta de Productos y Promociones*/
	var tbody_detalle_venta_productos_promociones = '';
	var tbody_total_venta_productos_promociones = '';

	//Modal Venta de Productos y Promociones
	var modal_venta_productos_promociones = '';

	/*Total Venta (1+2)*/
	var tbody_total_venta = '';		

	/*3. Credito Clientes*/		
	var tbody_detalle_credito_clientes = '';	
	var tbody_total_credito_clientes = '';

	//Modal Credito Clientes
	var modal_credito_clientes = '';

	/*4. Tarjetas de Credito*/	
	var tbody_detalle_tarjetas_credito = '';
	var tbody_total_tarjetas_credito = '';

	//Modal Tarjetas de Credito
	var modal_tarjetas_credito = ''

	/*5. Descuentos*/	
	var tbody_descuentos = '';
	
	/*6. Diferencia de Precio de Vales*/	
	var tbody_diferencia_precio_vales = '';

	/*7. Afericiones*/	
	var tbody_afericiones = '';
		
	/*Total Venta Creditos y Otros No al Contado*/			
	var tbody_TVCO = '';

	/*Total Efectivo en Boveda (Total Depositos POS)*/
	var tbody_TDP = '';

	/*Total Venta contado*/
	var tbody_TVContado = '';

	/*8. Sobrantes Faltantes por Trabajador*/
	var tbody_detalle_sobrantes_faltantes_por_trabajador = '';
	var tbody_total_sobrantes_faltantes_por_trabajador = '';		

	//Modal Sobrantes Faltantes por Trabajador
	var modal_sobrantes_faltantes_por_trabajador = '';

	/*Diferencia Diaria*/
	var tbody_DD = '';

	/*10.1 Ingresos al contado del día*/
	var tbody_detalle_ingreso_contado_dia = '';
	var tbody_total_ingreso_contado_dia = '';

	//Modal Ingresos al contado del día
	var modal_ingresos_contado_dia = '';

	/*10.2 Cobranzas y Amortizaciones por CC*/
	var tbody_detalle_cobranzas_amortizaciones_cc = '';
	var tbody_total_cobranzas_amortizaciones_cc = '';

	//Modal Cobranzas y Amortizaciones por CC
	var modal_cobranza_amortizaciones_cc = '';

	/*12. Egresos*/
	var tbody_detalle_egresos = '';
	var tbody_total_egresos = '';

	//Modal Egresos
	var modal_egresos = '';

	/*13. Documentos de Venta Manual*/
	var tbody_detalle_documentos_venta_manual = '';
	var tbody_total_documentos_venta_manual = '';

	//Modal Documentos de Venta Manual
	var modal_documentos_venta_manual = '';

	/*14. Saldo Neto a Depositar*/
	var tbody_saldo_neto_a_depositar = '';

	/*15. Saldo acumulado Caja y Banco*/
	var tbody_saldo_acumulado_caja_banco = '';

	for(var i = 0; i<count; i++) {
		/*Cabecera*/
		thead_name_stations += `<th style="font-weight: 200;">${detail[i].name}</th>`;			
		thead_stations      += `<th style="font-weight: 200;">IMP.</th>`;
		
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
				<tr> 
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
				<tr> 
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
				<tr>
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
				<tr> 
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
				<tr> 
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
				<tr> 
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
				<tr> 
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
				<tr> 
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

		/**************************************************************************************************************** */
		
		/*1. Venta Combustible*/
		tbody_1_1_liquido             += `<td>${ parseFloat(venta_combustible[0].liquido) }</td>`;
		tbody_1_1_glp                 += `<td>${ parseFloat(venta_combustible[0].glp) }</td>`;
		tbody_total_venta_combustible += `<th>${ total_venta_combustible.toFixed(2) }</th>`;

		/*2. Venta de Productos y Promociones*/
		tbody_detalle_venta_productos_promociones += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_venta_productos_promociones_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_venta_productos_promociones += `<td>${ venta_de_tienda.toFixed(2) }</td>`;	

		//Modal Venta de Productos y Promociones
		modal_venta_productos_promociones += crear_modal_venta_productos_promociones(detail[i], venta_productos_promociones_detalle_html, canti_de_tienda, venta_de_tienda);

		/*Total Venta (1+2)*/
		tbody_total_venta += `<th>${ total_venta.toFixed(2) }</th> `;

		/*3. Credito Clientes*/			
		tbody_detalle_credito_clientes += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_credito_clientes_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_credito_clientes += `<th>${ total_val_imp.toFixed(2) }</th>`;			

		//Modal Credito Clientes
		modal_credito_clientes += crear_modal_credito_clientes(detail[i], vales_credito_detalle_html, total_val_can, total_val_imp);			

		/*4. Tarjetas de Credito*/	
		tbody_detalle_tarjetas_credito += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_tarjetas_credito_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_tarjetas_credito += `<th>${ total_val_importetarjeta.toFixed(2) }</th>`;
		
		//Modal Tarjetas de Credito
		modal_tarjetas_credito += crear_modal_tarjetas_credito(detail[i], tarjetas_credito_detalle_html, total_val_importetarjeta);			

		/*5. Descuentos*/	
		tbody_descuentos += `<td>${ descuentos_total_.toFixed(2) }</td>`;

		/*6. Diferencia de Precio de Vales*/
		tbody_diferencia_precio_vales += `<td>${ difprecio_total_.toFixed(2) }</td>`;

		/*7. Afericiones*/
		tbody_afericiones += `<td>${ afericiones_total_.toFixed(2) }</td>`;

		/*Total Venta Creditos y Otros No al Contado*/			
		tbody_TVCO += `<th>${ TVCO_.toFixed(2) }</th>`;

		/*Total Efectivo en Boveda (Total Depositos POS)*/
		tbody_TDP += `<th>${ TDP_.toFixed(2) }</th>`;

		/*Total Venta contado*/
		tbody_TVContado += `<th>${ TVContado_.toFixed(2) }</th>`;

		/*8. Sobrantes Faltantes por Trabajador*/
		tbody_detalle_sobrantes_faltantes_por_trabajador += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_sobrantes_faltantes_por_trabajador_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_sobrantes_faltantes_por_trabajador += `<th>${ total_val_imp_sob.toFixed(2) }</th>`;

		//Modal Sobrantes Faltantes por Trabajador
		modal_sobrantes_faltantes_por_trabajador += crear_modal_sobrantes_faltantes_por_trabajador(detail[i], sobrantes_faltantes_por_trabajador_html, total_val_imp_sob);			

		/*Diferencia Diaria*/
		tbody_DD += `<th>${ DD_.toFixed(2) }</th>`;

		/*10.1 Ingresos al contado del día*/
		tbody_detalle_ingreso_contado_dia += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_ingresos_contado_dia_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_ingreso_contado_dia += `<th>${ total_val_ingresos.toFixed(2) }</th>`;

		//Modal Ingresos al contado del día
		modal_ingresos_contado_dia += crear_modal_ingresos_contado_dia(detail[i], ingresos_contado_dia_html, total_val_ingresos);			

		/*10.2 Cobranzas y Amortizaciones por CC*/
		tbody_detalle_cobranzas_amortizaciones_cc += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_cobranza_amortizaciones_cc_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_cobranzas_amortizaciones_cc += `<th>${ total_val_ingresos_cc.toFixed(2) }</th>`;

		//Modal Cobranzas y Amortizaciones por CC
		modal_cobranza_amortizaciones_cc += crear_modal_cobranza_amortizaciones_cc(detail[i], ingresos_cobranzas_cc_html, total_val_ingresos_cc);			

		/*12. Egresos*/
		tbody_detalle_egresos += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_egresos_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_egresos += `<th>${ total_val_egresos.toFixed(2) }</th>`;

		//Modal Egresos
		modal_egresos += crear_modal_egresos(detail[i], egresos_html, total_val_egresos);			

		/*13. Documentos de Venta Manual*/
		tbody_detalle_documentos_venta_manual += `<td><button type="button" class="btn btn-sm" ${btnpurple} data-toggle="modal" data-target="#modal_documentos_venta_manual_${detail[i].id}">Ver det.</button></td>`;
		tbody_total_documentos_venta_manual += `<th>${ total_manuales_.toFixed(2) }</th>`;

		//Modal Documentos de Venta Manual
		modal_documentos_venta_manual += crear_documentos_venta_manual(detail[i], manuales_html, total_manuales_);			

		/*14. Saldo Neto a Depositar*/
		tbody_saldo_neto_a_depositar += `<th>${ total_calculo_.toFixed(2) }</th>`;

		/*15. Saldo acumulado Caja y Banco*/
		tbody_saldo_acumulado_caja_banco += `<th>${ saldo_acumulado_caja_banco_.toFixed(2) }</th>`;
	}

	var html = `${modal_venta_productos_promociones}
					${modal_credito_clientes}
					${modal_tarjetas_credito}
					${modal_sobrantes_faltantes_por_trabajador}
					${modal_ingresos_contado_dia}
					${modal_cobranza_amortizaciones_cc}
					${modal_egresos}
					${modal_documentos_venta_manual}
					<div class="container-station" style="margin-bottom: 60px;">
						<div class="panel panel-default">
							<div class="panel-heading">
								LIQUIDACION DIARIA
							</div>
							
							<div class="table-responsive">
								<table class="table table-bordered table-hover"> <!-- tab-responsive -->
									<thead>										
										<tr style="background-color: #7952b3; color: #fff;"> 
											<th width="75%" style="font-weight: 200;">ESTACIONES</th> 												
											${thead_name_stations}
										</tr>
										<tr style="background-color: #7952b3; color: #fff;"> 
											<th width="75%" style="font-weight: 200;">CONCEPTO</th> 												
											${thead_stations}
										</tr>
									</thead>
									<tbody>
										<!-- 1. Venta Combustible --> 
										<tr> 
											<td colspan="9999">1. Venta Combustible</td> 												
										</tr>
										<tr> 
											<td>1.1 Liquido</td> 
											${tbody_1_1_liquido}
										</tr>
										<tr> 
											<td>1.2 GLP</td> 
											${tbody_1_1_glp}
										</tr>
										<tr> 
											<th>Total Venta Combustible</th> 
											${tbody_total_venta_combustible}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 2. Venta de Productos y Promociones -->
										<tr> 
											<td>2. Venta de Productos y Promociones</td> 
											${tbody_detalle_venta_productos_promociones}
										</tr>
										<tr>
											<th>Total Venta de Productos y Promociones</th>			
											${tbody_total_venta_productos_promociones}																					
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>
										
										<!-- Total Venta (1+2) -->
										<tr>
											<th>Total Venta${ espacio }<b style="font-size:0.6em; color:red;">(1+2)</b></th> 
											${tbody_total_venta}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 3. Credito Clientes -->
										<tr> 
											<td>3. Credito Clientes</td> 
											${tbody_detalle_credito_clientes}
										</tr> 
										<tr>
											<th>Total Credito Clientes</th>			
											${tbody_total_credito_clientes}																					
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 4. Tarjetas de Credito -->
										<tr> 
											<td>4. Tarjetas de Credito</td> 
											${tbody_detalle_tarjetas_credito}
										</tr> 
										<tr>
											<th>Total Tarjetas de Credito</th>												
											${tbody_total_tarjetas_credito}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 5. Descuentos -->
										<tr> 
											<td>5. Descuentos</td> 
											${tbody_descuentos}
										</tr>	

										<!-- 6. Diferencia de Precio de Vales -->
										<tr> 
											<td>6. Diferencia de Precio de Vales</td> 
											${tbody_diferencia_precio_vales}
										</tr>
										
										<!-- 7. Afericiones -->
										<tr> 
											<td>7. Afericiones</td> 
											${tbody_afericiones}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- Total Venta Creditos y Otros No al Contado -->
										<tr> 
											<th>
												Total Venta Creditos y Otros No al Contado
												<br><b style="font-size:0.6em; color:red;">(3+4+5+6+7)</b>
											</th> 												
											${tbody_TVCO}
										</tr>

										<!-- Total Efectivo en Boveda (Total Depositos POS) -->
										<tr> 
											<th>
												Total Efectivo en Boveda (Total Depositos POS)												
											</th> 												
											${tbody_TDP}
										</tr>

										<!-- Total Venta Contado -->
										<tr> 
											<th>
												Total Venta Contado
												<br><b style="font-size:0.6em; color:red;">(Total Venta - Total Venta Creditos)</b>
											</th> 
											${tbody_TVContado}												
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 8. Sobrantes Faltantes por Trabajador -->
										<tr> 
											<td>8. Sobrantes Faltantes por Trabajador</td> 											
											${tbody_detalle_sobrantes_faltantes_por_trabajador}
										</tr>
										<tr>
											<th>Total Sobrantes y Faltantes por Trabajador</th>																								
											${tbody_total_sobrantes_faltantes_por_trabajador}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- Diferencia Diaria -->
										<tr> 
											<th>
												Diferencia Diaria
												<br><b style="font-size:0.6em; color:red;">(Total Efectivo en Boveda + Sobrantes y Faltantes + Total Venta Contado)</b>
											</th> 												
											${tbody_DD}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 10. Ingresos -->
										<tr> 
											<td colspan="9999">10. Ingresos</td> 											
										</tr>

										<!-- 10.1 Ingresos al contado del día -->
										<tr> 
											<td>10.1 Ingresos al contado del día</td> 											
											${tbody_detalle_ingreso_contado_dia}
										</tr>
										<tr>
											<th>Total Ingresos al contado del día</th>																								
											${tbody_total_ingreso_contado_dia}
										</tr>

										<!-- 10.2 Cobranzas y Amortizaciones por CC -->
										<tr> 
											<td>10.2 Cobranzas y Amortizaciones por CC</td> 																							
											${tbody_detalle_cobranzas_amortizaciones_cc}
										</tr>		
										<tr>
											<th>Total Cobranzas y Amortizaciones por CC</th>																								
											${tbody_total_cobranzas_amortizaciones_cc}
										</tr>									
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>	

										<!-- 12. Egresos -->
										<tr> 
											<td>12. Egresos</td> 
											${tbody_detalle_egresos}
										</tr> 
										<tr>
											<th>Total Egresos</th>												
											${tbody_total_egresos}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 13. Documentos de Venta Manual -->
										<tr> 
											<td>13. Documentos de Venta Manual</td> 
											${tbody_detalle_documentos_venta_manual}
										</tr> 
										<tr>
											<th>Total Documentos de Venta Manual</th>												
											${tbody_total_documentos_venta_manual}
										</tr>
										<tr> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 14. Saldo Neto a Depositar -->
										<tr> 
											<th>
												14. Saldo Neto a Depositar
												<br><b style="font-size:0.6em; color:red;">(Total Venta Contado + Sobrantes y Faltantes - Ingresos al contado del dia - Egresos)</b>
											</th> 												
											${tbody_saldo_neto_a_depositar}
										</tr>
										<tr ${dnone}> 
											<td colspan="9999">${tab}</td> 
										</tr>

										<!-- 15. Saldo acumulado Caja y Banco -->
										<tr> 
											<th>15. Saldo acumulado Caja y Banco</th> 												
											${tbody_saldo_acumulado_caja_banco}
										</tr>											
									</tbody>
								</table>
							</div>
						</div>
					</div>`;

	storageStations();

	$('.container-ss-station').removeClass('d-none');

	setDataResultRequest2('.download-liquidacion-diaria',data);

	return html;
}

//Modal Venta de Productos y Promociones
function crear_modal_venta_productos_promociones(detail, venta_productos_promociones_detalle_html, canti_de_tienda, venta_de_tienda){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_venta_productos_promociones_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">2. Venta de Productos y Promociones - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${venta_productos_promociones_detalle_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ canti_de_tienda.toFixed(2) }</th> 
										<th>${ venta_de_tienda.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

//Modal Credito Clientes
function crear_modal_credito_clientes(detail, vales_credito_detalle_html, total_val_can, total_val_imp){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_credito_clientes_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">3. Credito Clientes - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${vales_credito_detalle_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ total_val_can.toFixed(2) }</th> 
										<th>${ total_val_imp.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

//Modal Tarjetas de Credito
function crear_modal_tarjetas_credito(detail, tarjetas_credito_detalle_html, total_val_importetarjeta){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_tarjetas_credito_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">4. Tarjetas Credito - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${tarjetas_credito_detalle_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ tab }</th> 
										<th>${ total_val_importetarjeta.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

//Modal Sobrantes Faltantes por Trabajador
function crear_modal_sobrantes_faltantes_por_trabajador(detail, sobrantes_faltantes_por_trabajador_html, total_val_imp_sob){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_sobrantes_faltantes_por_trabajador_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">8. Sobrantes Faltantes por Trabajador - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${sobrantes_faltantes_por_trabajador_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ tab }</th> 
										<th>${ total_val_imp_sob.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

//Modal Ingresos al contado del día
function crear_modal_ingresos_contado_dia(detail, ingresos_contado_dia_html, total_val_ingresos){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_ingresos_contado_dia_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">8. Sobrantes Faltantes por Trabajador - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${ingresos_contado_dia_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ tab }</th> 
										<th>${ total_val_ingresos.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

//Modal Ingresos al contado del día
function crear_modal_cobranza_amortizaciones_cc(detail, ingresos_cobranzas_cc_html, total_val_ingresos_cc){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_cobranza_amortizaciones_cc_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">8. Sobrantes Faltantes por Trabajador - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${ingresos_cobranzas_cc_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ tab }</th> 
										<th>${ total_val_ingresos_cc.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

//Modal Egresos
function crear_modal_egresos(detail, egresos_html, total_val_egresos){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_egresos_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">12. Egresos - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${egresos_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ tab }</th> 
										<th>${ total_val_egresos.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

//Modal Documentos de Venta Manual
function crear_documentos_venta_manual(detail, manuales_html, total_manuales_){		
	var tab = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	var btnpurple = 'style="background-color:#7952b3; border-color:#7952b3; color:#fff;"';

	return `			
		<div id="modal_documentos_venta_manual_${ detail.id }" class="modal fade" role="dialog">
			<div class="modal-dialog">
		
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">13. Documentos de Venta Manual - ${ detail.name }</h4>
					</div>
					<div class="modal-body">						
						<br>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead> 
									<tr> 
										<th>Concepto</th> 
										<th>Cantidad</th> 
										<th>Importe</th> 
									</tr> 
								</thead>
								<tbody>
									${manuales_html}
									<tr> 
										<th>${ tab }Total</th> 
										<th>${ tab }</th> 
										<th>${ total_manuales_.toFixed(2) }</th> 
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn" ${btnpurple} data-dismiss="modal">Cerrar</button>
					</div>
				</div>
		
			</div>
		</div>
	`;
}

/**
 * Plantilla de estaciones buscadas para Ventas - Liquidacion diaria
 * @param obj data, int t(typo estacion), int cm(chart mode)
 * @return string html
 */
function templateStationsSearchLiquidacionDiaria(data,t,cm) { //ACA
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

	$('.container-ss-station').removeClass('d-none');

	setDataResultRequest2('.download-liquidacion-diaria',data);

	return html;
}

function imprimirLinea(array, label, totalventa = ""){
	var fDecimal = 2;
	var result  = '<tr>';
	var decimal = 0;
	var negrita1;
	var negrita2;

	if (label == "Total") {
		negrita1 = ' style="color:black; font-weight:bold"">';
		negrita2 = '';
	} else {
		negrita1 = '>';
		negrita2 = '';
	} 

	var inicial = Math.round10(+array['inicial'], -fDecimal);
	var compras = Math.round10(+array['compras'], -fDecimal);
	var ventas = Math.round10(+array['ventas'], -fDecimal);

	result += '<td align="left" style="font-weight:bold">'+ array['producto'] + '</td>';
	result += '<td align="right"' +negrita1+ inicial.toFixed(2) +negrita2+ '</td>';
	result += '<td align="right"' +negrita1+ compras.toFixed(2) +negrita2+ '</td>';
	result += '<td align="right"' +negrita1+ ventas.toFixed(2) +negrita2+ '</td>';

	var porcentaje = 0;
	if (label != "Total" && label != "11620307") {
		porcentaje = Math.round10((+array['porcentaje'])/(+totalventa), -fDecimal);
	} else {
		porcentaje = Math.round10(+array['porcentaje'], -fDecimal);
	}

	/* Si no es el total ni GLP hallamos el porcentaje de c/producto con respecto al total */
	if (label != "Total" && label != "11620307") {			
		result += '<td align="right"' +negrita1+ porcentaje.toFixed(2) +negrita2+ '</td>';
	} else {			
		result += '<td align="right"' +negrita1+ porcentaje.toFixed(2) +negrita2+ '</td>';
	}

	/*The Number.isNaN() method determines whether a value is NaN (Not-A-Number).*/						
	if( Number.isNaN(+array['transfesalida']) ){
		array['transfesalida'] = 0;
	}		

	var transfe = Math.round10(+array['transfe'], -fDecimal);
	var final = Math.round10((+array['final']) + (+array['transfe']), -fDecimal);
	var medicion = Math.round10(+array['medicion'], -fDecimal);
	var dia = Math.round10((+array['dia']) + (+array['transfesalida']), -fDecimal);
	var mes = Math.round10(+array['mes'], -fDecimal);
	var importe = Math.round10(+array['importe'], -fDecimal);

	result += '<td align="right"' +negrita1+ transfe.toFixed(2) +negrita2+ '</td>';
	result += '<td align="right"' +negrita1+ final.toFixed(2) +negrita2+ '</td>';
	result += '<td align="right"' +negrita1+ medicion.toFixed(2) +negrita2+ '</td>';
	result += '<td align="right"' +negrita1+ dia.toFixed(2) +negrita2+ '</td>';
	result += '<td align="right"' +negrita1+ mes.toFixed(2) +negrita2+ '</td>';
	result += '<td align="right" style="font-weight:bold">' +importe.toFixed(2) + '</td>';
	result += '</tr>';

	return result;
}

/**
 * Visualizar información estación en Modal
 * @param obj - element t
 * data-typestation: 1 - ventas/market
 */
function viewDetailStation(t) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading_bootstrap4(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();

	var params = {
		id: t.attr('data-station'),
		dateBegin: t.attr('data-begindate'),
		dateEnd: t.attr('data-enddate'),
		typeStation: t.attr('data-typestation'),
		typeCost: t.attr('data-typecost')
	};
	console.log('start: '+params.dateBegin+', end: '+params.dateEnd);
	$.post(url+'requests/getDetailComb', params, function(data) {
		checkSession(data);
		console.log('requests/getDetailComb');
		console.log(data);
		setContendModal('#normal-modal', '.modal-title', 'Detalle en '+data.stations[0].name, true);
		setContendModal('#normal-modal', '.modal-body', templateDetailStation(data, data.typeStation), true);
		setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>', true);
	}, 'json');
}

/**
 * Cuerpo del modal detalle estación
 * @param obj data(estacion), int type(tipo de estación)
 * @return string
 */
function templateDetailStation(data, type) { //DETALLE COMBUSTIBLE / MARKET
	console.log('start: '+data.dateBegin+', end: '+data.dateEnd);
	var html = '<div class="row"><div class="col-md-6">Fecha:</div><div class="col-md-6">'
	+data.dateBegin+' - '+data.dateEnd+'</div></div><br>';
	console.log('isConnection: '+data.stations[0].isConnection);
	if(!data.stations[0].isConnection) {
		html += '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Sin conexión.</strong></div>';
	}
	html += '<div class="table-responsive"><table class="table table-bordered"> <thead> <tr> <th>Producto</th> <th align="right">Cantidad</th> <th align="right">Venta</th> <th align="right">Costo</th> <th align="right">Margen</th> </tr> </thead> <tbody>';

	var detail = data.stations[0].data;
	var count = detail.length;

	if(type == 0) {
		var qty_nglp = 0.0
		var total_nglp = 0.0;
		var cost_nglp = 0.0;
		var util_nglp = 0.0;

		var qty_glp = 0.0;
		var total_glp = 0.0;
		var cost_glp = 0.0;
		var util_glp = 0.0;

		var total_qty = 0.0;
		var gran_total = 0.0;
		var cost_total = 0.0;
		var util_total = 0.0;

		var qty = 0.0;
		var qtygal = 0.0;
		var html_ = ''; html__ = '';

		//comb
		for(var i = 0; i<count; i++) {
			console.log('For detail comb/line: '+detail[i].neto_venta);
			if(detail[i].product_id == '11620308') {
				console.log('GNV. detail[i].neto_venta; '+detail[i].neto_venta);

				if(detail[i].neto_venta != '') {
					qty = converterUM({type : 1, co : parseFloat(detail[i].neto_cantidad)});
					qtygal = converterUM({type : 1, co : parseFloat(detail[i].neto_cantidad)});
					qty_nglp += parseFloat(qtygal);

					//alert(''+parseFloat(detail[i].neto_cantidad)+', '+qty_nglp);
					total_nglp += parseFloat(detail[i].neto_venta);
					cost_nglp += parseFloat(detail[i].consumo_galon);
					console.log(': '+detail[i].consumo_galon);
					util_nglp += parseFloat(detail[i].utilidad);

					html += itemTableModal(3, i, detail, qtygal);
				} else {
					qty = parseFloat(0);
					qtygal = parseFloat(0);

					qty_nglp += parseFloat(0);
					total_nglp += parseFloat(0);
					cost_nglp += parseFloat(0);
					util_nglp += parseFloat(0);
				}

			} else if(detail[i].product_id != '11620307') {
				html += itemTableModal(1, i, detail, '');
				qty_nglp += parseFloat(detail[i].neto_cantidad);
				total_nglp += parseFloat(detail[i].neto_venta);
				cost_nglp += parseFloat(detail[i].consumo_galon);
				util_nglp += parseFloat(detail[i].utilidad);
				console.log(detail[i].product_id+': '+detail[i].neto_venta);
			} else {
				qty = converterUM({type : 0, co : parseFloat(detail[i].neto_cantidad)});
				qtygal = converterUM({type : 0, co : parseFloat(detail[i].neto_cantidad)});
				html__ += itemTableModal(2, i, detail, qtygal);
				qty_glp = parseFloat(qtygal);
				total_glp = parseFloat(detail[i].neto_venta);
				cost_glp += parseFloat(detail[i].consumo_galon);
				util_glp += parseFloat(detail[i].utilidad);
				console.log(detail[i].product_id+': '+detail[i].neto_venta);
			}
		}

		if(qty_nglp > 0) {
			html_ = '<tr class="table-info"><th scope="row"></th>'
			+'<td align="right">'+numeral(qty_nglp).format('0,0')+' Gl</td>'
			+'<td align="right">S/ '+numeral(total_nglp).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(cost_nglp).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(util_nglp).format('0,0')+'</td>'
			+'</tr>';
		} else {
			html_ = '';
		}

		total_qty = qty_nglp + qty_glp;
		gran_total = total_nglp + total_glp;
		cost_total = cost_nglp + cost_glp;
		util_total = util_nglp + util_glp;

		console.log('qty: '+qty_nglp + ' - '+qty_glp);
		
		return html+html_+html__
		+'<tr class="table-success"><th scope="row">Total General</th>'
		+'<td align="right">'+numeral(total_qty).format('0,0')+' Gl</td>'
		+'<td align="right">S/ '+numeral(gran_total).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(cost_total).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(util_total).format('0,0')+'</td>'
		+'</tr></tbody> </table></div>';

	} else if(type == 1 || type == 2) {
		//market
		console.log('detalle de market');
		var qty = 0.0;
		var sale = 0.0;
		var cost = 0.0;
		var util = 0.0;
		for(var i = 0; i<count; i++) {
			html += itemTableModal(4, i, detail, '');
			qty += clearFloat(detail[i].neto_cantidad);
			sale += clearFloat(detail[i].neto_venta);
			cost += clearFloat(detail[i].consumo_galon);
			console.log('pre util: '+util+', sumará: '+detail[i].utilidad);
			util += clearFloat(detail[i].utilidad);
			console.log('last util: '+util+'\n');
		}
		console.log('qty: '+qty+', sale: '+sale+' cost: '+cost+', util: '+util);
		return html+'<tr class="table-success"><th scope="row">Total General</th>'
		+'<td align="right">'+numeral(qty).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(sale).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(cost).format('0,0')+'</td>'
		+'<td align="right">S/ '+numeral(util).format('0,0')+'</td>'
		+'</tr></tbody> </table>';
	}
}

/**
 * Retorna filas para la tabla de productos(Modal)
 * @param int type 1,2 y 3 comb; 4: market
 * @return string
 */
function itemTableModal(type, i, detail, qtygal) {
	console.log('itemTableModal: '+detail[i].product);
	if(type == 1) {
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right">'
				+numeral(detail[i].neto_cantidad).format('0,0')+' Gl</td>'
				+'<td align="right">S/ '+numeral(detail[i].neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].utilidad).format('0,0')+'</td>'
				+'</tr>';
	} else if(type == 2) {
		//07 GLP
		var neto_venta = parseFloat(detail[i].neto_venta);
		var consumo_galon = parseFloat(detail[i].consumo_galon);
		var utilidad = parseFloat(detail[i].utilidad);
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right" title="'+detail[i].neto_cantidad+' Litros">'
				+'<a class="show-ltr" data-ltr="'+numeral(detail[i].neto_cantidad).format('0,0')+' L">'+numeral(qtygal).format('0,0')+' Gl</a></td>'
				+'<td align="right">S/ '+numeral(neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(utilidad).format('0,0')+'</td>'
				+'</tr>';
	} else if(type == 3) {
		//08 GNV
		var neto_venta = parseFloat(detail[i].neto_venta);
		var consumo_galon = parseFloat(detail[i].consumo_galon);
		//alert(consumo_galon);
		var utilidad = parseFloat(detail[i].utilidad);
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right" title="'+detail[i].neto_cantidad+' Metros Cúbicos">'
				//+'<a >'+numeral(detail[i].neto_cantidad).format('0,0')+' M3</a></td>'
				+'<a class="show-ltr" data-ltr="'+numeral(detail[i].neto_cantidad).format('0,0')+' M3">'+numeral(qtygal).format('0,0')+' Gl</a></td>'
				+'<td align="right">S/ '+numeral(neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(utilidad).format('0,0')+'</td>'
				+'</tr>';
	} else if(type == 4) {
		return '<tr><th scope="row">'+detail[i].product+'</th>'
				+'<td align="right" title="Unidad">'
				+'<a >'+numeral(detail[i].neto_cantidad).format('0,0')+'</a></td>'
				+'<td align="right">S/ '+numeral(detail[i].neto_venta).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].consumo_galon).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(detail[i].utilidad).format('0,0')+'</td>'
				+'</tr>';
	}
}

//deprecated
function getAllProductsDetail(data) {
	var product_id = [];
	var station = data.stations;
	var count = station.length;
	for(var i = 0; i < count; i++) {
		console.log('station: '+station[i].name);
		var data_ = station[i].data;
		var count_ = data_.length;
		console.log('data_: '+data_+' count_: '+count_);
		for(var j = 0; j < count_; j++) {
			console.log('product: '+data_[j].product_id);
			product_id['"'+data_[j].product_id+'"'] = [data_[j].product, 0.0, 0.0];//sales
			//product_id['"'+data_[j].product_id+'"'][] = 0.0;//qty
			console.log('product_id["'+data_[j].product_id+'"]: '+product_id['"'+data_[j].product_id+'"']);
		};
	};
	return product_id;
}

//deprecated
//ejemplo de grafico
function viewChart() {
	setContendModal('#normal-modal', '.modal-title', 'Productos vendidos', true);
	setContendModal('#normal-modal', '.modal-body', '<canvas id="myChart"></canvas>', true);
	var ctx = document.getElementById('myChart').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'pie',
		data: {
			labels: ['Producto A', 'Producto B', 'P.C', 'P.D', 'P.E', 'P.F', 'P.G'],
			datasets: [{
				backgroundColor: [
				"#2ecc71",
				"#3498db",
				"#95a5a6",
				"#9b59b6",
				"#f1c40f",
				"#e74c3c",
				"#34495e"
				],
				data: [12, 19, 3, 17, 28, 24, 7]
			}]
		}
	});
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

/**
 * Vista de grafico tipo pie 
 */
function viewChartStation() {
	$('.chartStation').html('<canvas id="chartStation"></canvas>');
	var ctx = document.getElementById('chartStation').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'pie',
		data: {
			//labels: stations,
			labels : [],
			datasets: [{
				label: '---',
				backgroundColor: stationColor,
				data: stationsTotal
			}]
		}
	});
}

/**
 * Vista de grafico de barras principal (ventas)
 */
function viewChartBarStation() {
	$('.chartStation').html('<canvas id="chartStation"></canvas>');
	var ctx = document.getElementById('chartStation').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: stations,
			datasets: [
				{
					label: 'Ventas',
					backgroundColor: stationColor,
					data: stationsTotal,
				}
			]
		}
	});

	//viewChartBarDemo();
	listAllObjects();//solo para comprobar lo que esta añadido en el objeto stations
}

/**
 * Vista de grafico de barras cantidad
 */
function viewChartBarStationQty() {
	$('.chartStationQty').html('<canvas id="chartStationQty"></canvas>');
	var ctx = document.getElementById('chartStationQty').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: stations,
			datasets: [
				{
					label: 'Cantidades',
					backgroundColor: stationColor,
					data: stationsQty,
				}
			]
		}
	});
	//viewChartBarDemo();
	listAllObjects();//solo para comprobar lo que esta añadido en el objeto stations
}

/**
 * Vista de grafico de barras utilidad
 */
function viewChartBarStationUtil() {
	$('.chartStationUtil').html('<canvas id="chartStationUtil"></canvas>');
	var ctx = document.getElementById('chartStationUtil').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: stations,
			datasets: [
				{
					label: 'Utilidades',
					backgroundColor: stationColor,
					data: stationsUtil,
				}
			]
		}
	});
	$('.chartStationUtil').append('<br><div class="alert alert-info" align="center">Nota: Las estaciones con Utilidad negativo tendrán 0 en este gráfico</div>');

	//viewChartBarDemo();
	listAllObjects();//solo para comprobar lo que esta añadido en el objeto stations
}

/**
 * Ejemplo de grafico de barras (demo)
 */
function viewChartBarDemo() {
	$('.chartStation').append('<canvas id="chartStation2"></canvas>');
	var ctx = document.getElementById('chartStation2').getContext('2d');
	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			datasets: [
				{
					label: 'Ventas',
					backgroundColor: [
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'rgba(255, 206, 86, 0.2)',
					'rgba(75, 192, 192, 0.2)',
					'rgba(153, 102, 255, 0.2)',
					'rgba(255, 159, 64, 0.2)'
					],
					borderColor: [
					'rgba(255,99,132,1)',
					'rgba(54, 162, 235, 1)',
					'rgba(255, 206, 86, 1)',
					'rgba(75, 192, 192, 1)',
					'rgba(153, 102, 255, 1)',
					'rgba(255, 159, 64, 1)'
					],
					data: [65, 59, 80, 81, 56, 55, 40],
				}
			]
		}
	});
}

/**
 * Asignar contenido al modal(secciones)
 * @param string modal(element), string elem(elemento a modificar), string cont(contenido a usar), boolean isVisible
 */
function setContendModal(modal, elem, cont, isVisible) {
	isVisible = isVisible ? 'block' : 'none';
	$( modal ).find( elem ).html( cont ).css( 'display',isVisible );
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

/**
 * Retorna 0 si el contenido es NaN
 * @param val
 * @return val
 */
function formatNaN(val) {
	if (isNaN(val)) {
		return 0;
	}
	return val;
}

/**
 * Completa caracteres a dos digitos para fechas
 * @param int m
 * @return string m
 */
function completeMonth(m) {
	if(m.length != 2) {
		return '0'+m;
	} else {
		return m;
	}
}


/**
 * Alamacena informacion de estaciones en objetos y arreglos
 *
 */
function storageStations() {
	console.log(stations);
	var count = dataStations.length;
	console.log('station l: '+dataStations.length);
	var name = '';
	for(var i = 0; i < count; i++) {
		console.log('station: '+dataStations[i].name);
		name = dataStations[i].name;
		stations.push(
			name
		);

		stationsTotal.push(
			numeral(dataStations[i].total).format('0.00')
		);

		stationsQty.push(
			numeral(dataStations[i].qty).format('0.00')
		);

		stationsUtil.push(
			numeral(dataStations[i].util).format('0.00')
		);

		var por = (dataStations[i].total / gran_total) * 100;
		por = parseFloat(por);

		por = numeral(por).format('0.00');

		stationsPor.push(
			por
		);

		stationColor.push(
			dataStations[i].color
		);
	};
}

/**
 * Lista informacion de estaciones almacenada en objetos y arreglos
 * Solo para visualizar(comprobar) data
 */
function listAllObjects() {
	var count = stations.length;
	for(var i = 0; i < count; i++) {
		console.log('stations: '+stations[i]);
	}
	console.log('------');
	var count = stationsDesc.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsDesc: '+stationsDesc[i]);
	}
	console.log('------');
	var count = stationsTotal.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsTotal: '+stationsTotal[i]);
	}
	console.log('------');
	var count = stationsQty.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsQty: '+stationsQty[i]);
	}
	console.log('------');
	var count = stationsUtil.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsUtil: '+stationsUtil[i]);
	}
	console.log('------');
	var count = stationsPor.length;
	for(var i = 0; i < count; i++) {
		console.log('stationsPor: '+stationsPor[i]);
	}
	console.log('------');
	var count = stationColor.length;
	for(var i = 0; i < count; i++) {
		console.log('stationColor: '+stationColor[i]);
	}
}

/**
 * Descargar Hoja de Cálculo de Venta de Combustibles
 * @param obj t (atributos del boton de descarga)
 */
function downloadCombSales(t) {
	console.log(t);
	var dateB = t.attr('data-begindate').split("/");
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	var dateE = t.attr('data-enddate').split("/");
	dateE = dateE[0] + '-' + dateE[1] + '-' + dateE[2];

	console.log('dateB: '+dateB+', dateE: '+dateE);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB,
		endDate: dateE,
		typeStation: t.attr('data-typestation'),
		qtySale: t.attr('data-qtysale'),
		typeCost: t.attr('data-typecost'),
		typeResult: 1,
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/resumeSales/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.typeStation+'/'+params.qtySale+'/'+params.typeCost;
	console.log('url_: '+url_);
	window.location = url_;
}

function downloadSumary(t) {
	console.log(t);
	var dateB = t.attr('data-begindate').split("/");
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	var dateE = t.attr('data-enddate').split("/");
	dateE = dateE[0] + '-' + dateE[1] + '-' + dateE[2];

	console.log('dateB: '+dateB+', dateE: '+dateE);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB,
		endDate: dateE,
		typeStation: t.attr('data-typestation'),
		qtySale: t.attr('data-qtysale'),
		typeCost: t.attr('data-typecost'),
		typeResult: 1,
		include: t.attr('data-include'),
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/generateCaclSumary/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.typeStation+'/'+params.qtySale+'/'+params.typeCost+'/'+params.include;
	console.log('url_: '+url_);
	window.location = url_;
}

function downloadSalesForHours(t){
	console.log(t);

	var dateB = t.attr('data-begindate').split("/");
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	var dateE = t.attr('data-enddate').split("/");
	dateE = dateE[0] + '-' + dateE[1] + '-' + dateE[2];

	console.log('dateB: '+dateB+', dateE: '+dateE);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB,
		endDate: dateE,
		typeStation: t.attr('data-typestation'),				
		local: t.attr('data-local'),
		importe: t.attr('data-importe'),
		modo: t.attr('data-modo'),
		productos: t.attr('data-productos'),
		unidadmedida: t.attr('data-unidadmedida')
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/resumeSalesForHours/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.typeStation+'/'+params.local+'/'+params.importe+'/'+params.modo+'/'+params.productos+'/'+params.unidadmedida;
	console.log('url__: '+url_);
	window.location = url_;
}

function downloadLiquidacionDiaria(t){
	console.log(t);

	var dateB = t.attr('data-begindate').split("/");
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	var dateE = t.attr('data-enddate').split("/");
	dateE = dateE[0] + '-' + dateE[1] + '-' + dateE[2];

	console.log('dateB: '+dateB+', dateE: '+dateE);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB,
		endDate: dateE,
		typeStation: t.attr('data-typestation'),
		inventariocombustible: t.attr('data-inventariocombustible'),
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/resumeLiquidacionDiaria/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.typeStation+'/'+params.inventariocombustible;
	console.log('url__: '+url_);
	window.location = url_;
}

/**
 * Otorgar data obtenida en atributos de solicitud
 * @param string element, obj dfata
 */
function setDataResultRequest(element,data) {
	console.log('SET: '+element);
	console.log('\n\n\n');
	console.log(data);
	console.log('\n\n\n');
	$(element).attr('data-typestation',data.typeStation).attr('data-enddate',data.endDate).attr('data-begindate',data.beginDate).attr('data-station',data.id).attr('data-typecost',data.typeCost).attr('data-qtysale',data.qtySale);
}

/**
 * Otorgar data obtenida en atributos de solicitud
 * @param string element, obj dfata
 */
function setDataResultRequest2(element,data) {
	console.log('SET: '+element);
	console.log('\n\n\n');
	console.log(data);
	console.log('\n\n\n');
	$(element).attr('data-typestation',data.typeStation)
				.attr('data-begindate',data.dateBegin)
				.attr('data-enddate',data.dateEnd)
				.attr('data-station',data.id)			  
				.attr('data-local',data.local)
				.attr('data-importe',data.importe)
				.attr('data-modo',data.modo)
				.attr('data-productos',data.productos)
				.attr('data-unidadmedida',data.unidadmedida)
				.attr('data-inventariocombustible',data.inventariocombustible);
}

/**
 * Detalle de todos los productos - linea en todas las estaciones seleccionadas (Modal)
 * @param obj element click
 */
function detailAllResult(t) {
	console.log('click 666');
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();

	var params = {
		id: t.attr('data-station'),
		dateBegin: t.attr('data-begindate'),
		dateEnd: t.attr('data-enddate'),
		typeStation: $('#typeStation').val(),
		typeCost: t.attr('data-typecost')
	};

	var html = '';
	var unit = params.typeStation == 0 ? 'Gl' : '';

	if(params.typeStation == 0) {
		var qty_nglp = 0.0
		var total_nglp = 0.0;
		var cost_nglp = 0.0;
		var util_nglp = 0.0;

		var qty_glp = 0.0;
		var total_glp = 0.0;
		var cost_glp = 0.0;
		var util_glp = 0.0;

		var total_qty = 0.0;
		var gran_total = 0.0;
		var cost_total = 0.0;
		var util_total = 0.0;

		var qty = 0.0;
		var qtygal = 0.0;
		var html_ = ''; html__ = '';
	} else if(params.typeStation == 1 || params.typeStation == 2) {
		var total_qty = 0.0;
		var gran_total = 0.0;
		var cost_total = 0.0;
		var util_total = 0.0;
	}

	$.post(url+'requests/getDetailProducts', params, function(data) {
		checkSession(data);
		console.log(data);
		console.log(params.typeStation);
		html = '<div class="row"><div class="col-md-6">Fecha:</div><div class="col-md-6">'
		+data.dateBegin+' - '+data.dateEnd+'</div></div><br>';
		html += '<div class="table-responsive"><table class="table table-bordered"> <thead> <tr> <th>Producto</th> <th align="right">Cantidad</th> <th align="right">Venta</th> <th align="right">Costo</th> <th align="right">Utilidad</th> </tr> </thead> <tbody>';

		var product = data.dataProducts;
		var sales = 0.0;

		if(params.typeStation == 0) {
			for(var i = 0; i < product.length; i++) {
				console.log('formatDateEnd: '+product[i].code);
				if(product[i].code == '11620308') {
					qty = converterUM({type : 1, co : product[i].neto_cantidad});
					qtygal = converterUM({type : 1, co : product[i].neto_cantidad});

					//qty_nglp += parseFloat(0);
					if(product[i].neto_venta != '') {
						qty_nglp += parseFloat(qtygal);
						total_nglp += parseFloat(product[i].neto_venta);
						cost_nglp += parseFloat(product[i].consumo_galon);
						util_nglp += parseFloat(product[i].utilidad);
					} else {
						qty_nglp += parseFloat(0);
						total_nglp += parseFloat(0);
						cost_nglp += parseFloat(0);
						util_nglp += parseFloat(0);
					}
					html += itemTableModal(3, i, product, qtygal);
					//total_nglp += parseFloat(product[i].neto_venta);
				} else if(product[i].code != '11620307') {
					qty_nglp += parseFloat(product[i].neto_cantidad);
					total_nglp += parseFloat(product[i].neto_venta);
					cost_nglp += parseFloat(product[i].consumo_galon);
					util_nglp += parseFloat(product[i].utilidad);
					html += itemTableModal(1, i, product, '');
				} else {
					qty = converterUM({type : 0, co : product[i].neto_cantidad});
					qtygal = converterUM({type : 0, co : product[i].neto_cantidad});
					html__ += itemTableModal(2, i, product, qtygal);
					qty_glp = parseFloat(qtygal);
					total_glp += parseFloat(product[i].neto_venta);
					cost_glp += parseFloat(product[i].consumo_galon);
					util_glp += parseFloat(product[i].utilidad);
				}
			};

			if(qty_nglp > 0) {
				html_ = '<tr class="table-info"><th scope="row"></th>'
				+'<td align="right">'+numeral(qty_nglp).format('0,0')+' '+unit+'</td>'
				+'<td align="right">S/ '+numeral(total_nglp).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(cost_nglp).format('0,0')+'</td>'
				+'<td align="right">S/ '+numeral(util_nglp).format('0,0')+'</td>'
				+'</tr>';
			} else {
				html_ = '';
			}

			total_qty = qty_nglp + qty_glp;
			gran_total = total_nglp + total_glp;
			cost_total = cost_nglp + cost_glp;
			util_total = util_nglp + util_glp;
			
			var _html = html+html_+html__
			+'<tr class="table-success"><th scope="row">Total General</th>'
			+'<td align="right">'+numeral(total_qty).format('0,0')+' '+unit+'</td>'
			+'<td align="right">S/ '+numeral(gran_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(cost_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(util_total).format('0,0')+'</td>'
			+'</tr></tbody> </table></div>';
		} else if(params.typeStation == 1 || params.typeStation == 2) {
			for(var i = 0; i < product.length; i++) {
				console.log('code: '+product[i].code);
				html += itemTableModal(4, i, product, '');
				total_qty += clearFloat(product[i].neto_cantidad);
				gran_total += clearFloat(product[i].neto_venta);
				cost_total += clearFloat(product[i].consumo_galon);
				util_total += clearFloat(product[i].utilidad);
			}
			var _html = html+'<tr class="success"><th scope="row">Total General</th>'
			+'<td align="right">'+numeral(total_qty).format('0,0')+' '+unit+'</td>'
			+'<td align="right">S/ '+numeral(gran_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(cost_total).format('0,0')+'</td>'
			+'<td align="right">S/ '+numeral(util_total).format('0,0')+'</td>'
			+'</tr> </tbody> </table></div>';
		}

		setContendModal('#normal-modal', '.modal-title', 'Resumen del total de productos vendidos', true);
		setContendModal('#normal-modal', '.modal-body', _html, true);
		setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>', true);
	}, 'json');
}

/**
 * Buscar stock de Combustible y Market TP
 */
function searchStock() {
	$('.container-ss-station').addClass('d-none');
	$('.result-search').html(loading_bootstrap4());
	var valStartDate = checkDate($('#start-date-request').val(),'/');

	if(valStartDate == 0) {
		//formato no valido
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-stock').prop('disabled', false);
	} else if(valStartDate == 2) {
		//fechas futuras
		$('.result-search').html(_alert('warning', '<span class="glyphicon glyphicon-alert"></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-stock').prop('disabled', false);
	}

	paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		daysProm: $('#days-prom').val(),
		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qty_sale: $('#qty_sale').val(),
		type_cost: $('#type_cost').val(),
		type_result: 1,
	}
	console.log('start: '+paramsRequest.dateBegin);
	$.post(url+'requests/getStocks', paramsRequest, function(data) {
		checkSession(data);
		$('.btn-search-stock').prop('disabled', false);
		console.log('Dentro del callback');
		console.log(data);
		//$('.result-search').html(templateStationsSearch(data,data.typeStation,charMode));
		$('.result-search').html(templateStock(data,data.typeStation,0));
		templateTankSimulation(data);

		setDataResultRequest('.download-comb-stock',data);
	}, 'json');
}

/**
 * Platilla contenedores de Stock
 * @param obj data, type(type de estacion), 
 * @return string return html
 */
function templateStock(data,type,chart) {
	var html = '<br>';
	var detail = data.stations;
	var count = detail.length;
	var num = 1;
	var color_id, taxid;
	for(var i = 0; i<count; i++) {
		color_id = getRandomColor();

		if(taxid != detail[i].group.taxid) {
			html += (i != 0 ? '<hr>' : '');
			html += '<div class="panel-group-station"><h5 title="RUC: '+detail[i].group.taxid+'">'+detail[i].group.name+'</h5></div>';
			taxid = detail[i].group.taxid;
		}
		if(!detail[i].isConnection) {
			html += '<div class="container-station"><div class="panel panel-danger">'
			+'<div class="panel-heading"><span class="glyphicon glyphicon-exclamation-sign"></span> <strong>Sin conexión.</strong></div>';
		} else {
			html += '<div class="container-station"><div class="panel panel-default">';
		}
		html += '<div class="panel-body detail" data-station="'+detail[i].id+'"'
		+'data-begindate="'+data.beginDate+'" data-enddate="'+data.endDate+'" data-typestation="'+data.typeStation+'"'
		+'><span class="glyphicon glyphicon-stop" style="color: '+color_id+'"></span> '+num+'. '+detail[i].name
		+'<br><table class="table table-bordered table-striped d-none">'
		+'<thead> <tr> <th>Prod.</th> <th align="right">Cap.</th> <th align="right">% Disp.</th> <th align="right">Días Aprox.</th> </tr> </thead>'
		+'<tbody> ';
		html += templateTableDetailStock(detail[i].data,detail[i].id);
		html += ' </tbody> </table><br><div class="row container-canvas canvas-station-'+detail[i].id+'"></div>'
		+'</div></div>'
		+'</div></div>';
		num++;
	}
	return html;
}

/**
 * Contenido(tr-td) de tabla stocks combustible
 * @param obj detail, int id (codigo de estacion)
 */
function templateTableDetailStock(detail,id) {
	var html = '';
	var count = detail.length;
	for(var i = 0; i < count; i++) {
		console.log('Producto: '+detail[i].desc_comb);
		html += '<tr>'
		+'<th scope="row">'+detail[i].desc_comb+'</th>'
		+'<td align="right">'+numeral(detail[i].nu_capacidad).format('0,0')+'</td>'
		+'<td align="right">'+numeral(detail[i].porcentaje_existente).format('0,0')+'</td>'
		+'<td align="right">'+numeral(detail[i].tiempo_vaciar).format('0,0')+'</td>'
		+'</tr>';
		console.log('.canvas-station-'+id);
		//$('.canvas-station-'+id).append('<canvas id="canvas-tank-'+id+'-'+detail[i].cod_comb+'"></canvas>');
	};
	return html;
}

/**
 * Platilla para los graficos e información de los tanques
 * @param obj data $post
 */
function templateTankSimulation(data) {
	console.log('templateTankSimulation');
	var detail = data.stations;
	var count = detail.length;
	var append = '';
	var detailTank = '';
	var typeMedition = '';
	for(var i = 0; i<count; i++) {
		var id = detail[i].id;
		var data_ = detail[i].data;
		var count_ = data_.length;
		var inn = 0;

		for(var j = 0; j < count_; j++) {
			//Solo no se considera GNV
			typeMedition = data_[j].cod_comb != '11620307' ? 'gal' : 'ltr';
			append += '<div class="col-md-4 tank-in">'
			+'<div class="panel panel-default"><div class="panel-body panel-body-tank">'
			+'<div class="row info-tank info-tank-'+id+'-'+data_[j].cod_comb+'">'
			+'<div class="col-md-4" align="center">'
			+'<div class="name-tank msg-tank-'+id+'-'+data_[j].cod_comb+'"><label>'+data_[j].desc_comb+'</label></div>'
			+'<canvas id="canvas-tank-'+id+'-'+data_[j].cod_comb+'" class="canvas-tank" data-estation-id="'+id+'" data-cod-comb="'+data_[j].cod_comb+'" width="120" height="72"></canvas>'
			+'</div>'
			+'<div class="col-md-8 detail-tank detail-tank-'+id+'-'+data_[j].cod_comb+'"></div>'
			+'</div></div>'
			//+'<div class="panel-footer msg-tank msg-tank-'+id+'-'+data_[j].cod_comb+'" align="center"></div>'
			+'</div></div>';
			
			$('.canvas-station-'+id).append(append);
			renderTankSimulate({
				stock: data_[j].nu_medicion,
				percentaje: data_[j].porcentaje_existente,
				capacity: data_[j].nu_capacidad,
				unit: typeMedition,
				text: data_[j].desc_comb,
				elementId: id+'-'+data_[j].cod_comb,
				color: getColorComb(data_[j].cod_comb,true),
				debug: false
			});

			console.log('data_[j].nu_capacidad: '+data_[j].nu_capacidad+', data_[j].porcentaje_existente: '+data_[j].porcentaje_existente+' | '+id+'-'+data_[j].cod_comb);
			detailTank = '';
			detailTank += '<label class="label-detail-comb">Inventario:</label> '+numeral(data_[j].nu_medicion).format('0')+' '+typeMedition+'</div>'
			+'<div><label class="label-detail-comb">Promedio:</label> '+numeral(data_[j].nu_venta_promedio_dia).format('0')+' '+typeMedition+' por día</div>'
			+'<div><label class="label-detail-comb">Tiempo en vaciar:</label> '+numeral(data_[j].tiempo_vaciar).format('0')+' día(s)</div>'
			+'<div><label class="label-detail-comb">Última compra:</label> '+numeral(data_[j].cantidad_ultima_compra).format('0')+' '+typeMedition+', '+data_[j].fecha_ultima_compra;
			$('.detail-tank-'+id+'-'+data_[j].cod_comb).append(detailTank);
			if(data_[j].nu_capacidad <= 0) {
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).prepend('<button class="resume-info-tank" title="Información" data-content="Se registró cero o menos<br>como capacidad del tanque.<br><p>Capacidad: '+numeral(data_[j].nu_capacidad).format('0')+' '+typeMedition+'</p>" data-placement="top" data-html="true" data-trigger="focus"><span style="color: #C65959;" class="glyphicon glyphicon-exclamation-sign"></span></button> ');
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).addClass('msg-tank-mobile');
			}
			if(data_[j].porcentaje_existente > 100) {
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).prepend('<button class="resume-info-tank" title="Información" data-content="La medición excede la<br>capacidad del tanque.<br><p>Medición: '+numeral(data_[j].nu_medicion).format('0')+' '+typeMedition+'</p><p>Capacidad: '+numeral(data_[j].nu_capacidad).format('0')+' '+typeMedition+'</p>" data-placement="top" data-html="true" data-trigger="focus"><span style="color: #F12F2F;" class="glyphicon glyphicon-exclamation-sign"></span></button> ');
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).addClass('msg-tank-mobile');
			}
			if(parseFloat(data_[j].nu_medicion) <= 0 || isNaN(parseFloat(data_[j].nu_medicion))) {
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).prepend('<button class="resume-info-tank" title="Información" data-content="No existe medición<br>para este producto." data-placement="top" data-html="true" data-trigger="focus"><span style="color: #F12F2F;" class="glyphicon glyphicon-exclamation-sign"></span></button> ');
				$('.msg-tank-'+id+'-'+data_[j].cod_comb).addClass('msg-tank-mobile');
			}
			append = '';
			detailTank = '';
		}
	}
	if(count > 0) {
		$('.container-ss-station').removeClass('d-none');
	}
}

/**
 * Simulación de medida del tanque
 * @param obj data $post
 */
function renderTankSimulate(data) {
	var isErrorCapacity = false, isErrorPercentaje = false, isErrorStock = false;
	$('.msg-tank-'+data.elementId).html('<span>'+data.text+'</span>');
	if(data.debug) {
		$('.msg-tank-'+data.elementId).append('<br>percentaje: '+data.percentaje+', capacity: '+data.capacity,', ');
	}

	var y = 10, x = 10, ye = 60, xe = 110;
	var conf = {widthLine: 2, colorTank: '200,0,0'};
	console.log('ancho de line: '+conf.widthLine);
	var canvas = document.getElementById('canvas-tank-'+data.elementId);
	var context = canvas.getContext('2d');

	var realMedition = data.stock;
	if(data.debug) {
		$('.msg-tank-'+data.elementId).append('<br>Real Medition: '+realMedition);
	}

	var medition = ((ye-y)*data.percentaje)/100;
	medition = ye-medition;
	if(data.debug) {
		$('.msg-tank-'+data.elementId).append('<br>Medition: '+medition);
	}

	if(data.capacity <= 0) {
		isErrorCapacity = true;
	}
	if(data.percentaje > 100) {
		isErrorPercentaje = true;
	}
	if(parseFloat(data.stock) <= 0 || isNaN(parseFloat(data.stock))) {
		isErrorStock = true;
	}

	//barra superior
	context.beginPath();
	context.moveTo(x, y);
	context.lineTo(xe, y);
	context.stroke();

	//barra derecha
	context.beginPath();
	context.moveTo(x, y);
	context.lineTo(x, ye);
	context.lineWidth = conf.widthLine;
	context.strokeStyle = '#5D5D5D';
	context.stroke();

	//barra izquierda
	context.beginPath();
	context.moveTo(xe, y);
	context.lineTo(xe, ye);
	context.stroke();

	//barra base
	context.beginPath();
	context.moveTo(x, ye);//60,150
	context.lineTo(xe, ye);
	context.stroke();

	if(!isErrorCapacity && !isErrorPercentaje && !isErrorStock) {
		context.fillStyle = data.color;
		context.fillRect (x+1, medition, (xe-x)-2, (ye-medition)-1);
	}

	if(!isErrorStock) {
		//(text) porcentaje de medicion
		context.fillStyle = 'rgb(0,0,0)';
		context.font = "20px Arial";
		if(data.percentaje >= 53 && data.percentaje <= 100) {
			context.fillText(numeral(data.percentaje).format('0')+'%',x+36,medition+20);
		} else if(data.percentaje > 0 && data.percentaje < 53) {
			context.fillText(numeral(data.percentaje).format('0')+'%',x+36,medition-5);
		}
	} else {
		console.log('\nOcurrió un error en la medición');
	}
}

/**
 * Función para el evento click en el gráfico del tanque(No usado)
 */
function detailInfoTank(t) {
	alert('data-estation-id: '+t.attr('data-estation-id')+', data-cod-comb: '+t.attr('data-cod-comb'));
}

/**
 * Mostar/Ocultar Popover Bootstrap
 */
function showPO(t,b) {
	if(b) {
		t.popover('show');
	} else {
		t.popover('hide');
	}
}

/**
 * Descargar hoja de calculo Stock de Combustibles
 * @param obj t (atributos del boton de descarga)
 */
function downloadCombStock(t) {
	console.log(t);

	var dateB = $('#start-date-request').val().split('/');//attr
	dateB = dateB[0] + '-' + dateB[1] + '-' + dateB[2];

	console.log('dateB: '+dateB);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		dateBegin: dateB,
		typeStation: t.attr('data-typestation'),
		type_result: 1,
	};
	console.log('params.dateBegin: '+params.dateBegin);

	var url_ = url+'reports/resumeStock/'+params.id+'/'+params.dateBegin+'/'+params.typeStation;
	console.log('url_: '+url_);
	window.location = url_;
}

/**
 * Visualizar información estación en Modal
 * @param obj - element t
 */
function searchSumarySales(t) {
	$('.container-ss-station').addClass('d-none');
	$('.result-search').html(loading_bootstrap4());
	var paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),
		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	}
	console.log(paramsRequest);

	/*setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();*/

	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getSumarySale', paramsRequest, function(data) {
		checkSession(data);
		dataSumarySale = data;
		console.log('requests/getSumarySale');
		console.log(data);
		var html = `<ul class="nav nav-tabs">
							<li class="nav-item">
								<button class="nav-link active" href="#quantity" data-toggle="tab">Galones</button>														
							</li>
							<li class="nav-item">
								<button class="nav-link" href="#money" data-toggle="tab">Soles</button>
							</li>
					</ul>`;
		html += `<div class="tab-content clearfix">
						<div class="tab-pane fade show active" id="quantity">
							<div class="quantity-include"></div>
							<div class="quantity-exclude d-none"></div>
						</div>
						<div class="tab-pane fade" id="money">
							<div class="money-include"></div>
							<div class="money-exclude d-none"></div>
						</div>
					</div>
				<div class="graphics"></div>`;


		$('.result-search').html(html);

		$('.money-include').html(templateTableSumarySales(data, 'money-include'));
		renderGraphicResume('money-include',paramsRequest);//0
		clearDataResumen();
		$('.quantity-include').html(templateTableSumarySales(data, 'quantity-include'));
		renderGraphicResume('quantity-include',paramsRequest);//1
		clearDataResumen();

		$('.money-exclude').html(templateTableSumarySales(data, 'money-exclude'));
		renderGraphicResume('money-exclude',paramsRequest);//2
		clearDataResumen();
		$('.quantity-exclude').html(templateTableSumarySales(data, 'quantity-exclude'));
		renderGraphicResume('quantity-exclude',paramsRequest);//3
		clearDataResumen();

		$('.btn-search-sale').prop('disabled', false);

		/*setContendModal('#normal-modal', '.modal-title', 'Detalle en '+data.stations[0].name, true);
		setContendModal('#normal-modal', '.modal-body', templateDetailStation(data,data.typeStation), true);
		setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);*/
	}, 'json');
}

function searchSalesForHours(t) {
	$('.container-chart-station').addClass('d-none');
	$('.container-ss-station').addClass('d-none');
	$('.result-search').html(loading_bootstrap4());
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

		/*PERSONALIZADO PARA EL REPORTE DE VENTAS POR HORAS*/
		local: $('input[name=local]:checked').val(),
		importe: $('input[name=importe]:checked').val(),
		modo: $('input[name=modo]:checked').val(),
		productos: $('select[name=productos]').val(),
		unidadmedida: $('select[name=unidadmedida]').val()
		/*CERRAR PERSONALIZADO PARA EL REPORTE DE VENTAS POR HORAS*/
	}
	console.log(paramsRequest);

	/*setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();*/

	var charMode = $('#chart-mode').val();

	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getSalesForHours', paramsRequest, function(data) {
		// console.log('data:', data); //ELIMINAR
		console.log('data:', JSON.stringify(data)); //ELIMINAR
		// return; //ELIMINAR
		
		checkSession(data);
		$('.btn-search-sale').prop('disabled', false);
		console.log('Dentro del callback');
		console.log(data);
		$('.result-search').html(templateStationsSearchSalesForHours(data, data.typeStation, charMode));
	}, 'json');
}

function searchLiquidacionDiaria(t){ //ACA
	$('.container-chart-station').addClass('d-none');
	$('.container-ss-station').addClass('d-none');
	$('.result-search').html(loading_bootstrap4());
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

function templateTableSumarySales(data, type) {
	//limpiar arrays
	/*
	11620306	KEROSENE		KEROSENE
	11620303	GASOHOL 97		97 OCT
	11620305	GASOHOL 95		95 OCT
	11620304	DIESEL B5 UV	D2 PET
	11620301	GASOHOL 84		84 OCT
	11620302	GASOHOL 90		90 OCT
	11620307	GLP				GLP
	*/

	//var _type = type == 'money-include' || 'money-exclude' ? 'money' : 'quantity';
	var html = '<br><div>Excluir consumo <i class="fas fa-info-circle" title="Consumo interno de la empresa"></i>: '
	+'<div class="btn-group" aria-label="Default button group" role="group"><div class="btn-'+type+' true btn btn-default" data-action="true">Si</div><div class="btn-'+type+' false btn btn-primary" data-action="false">No</div></div>'
	+'</div>'
	+'<div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table table-striped">'
	+'<thead>'
	+'<tr class="bg-primary text-white">'
	+'<th colspan="9" style="text-align: center">Resumen de venta por estación y producto</th>'
	+'</tr>'
	+'<tr class="bg-primary text-white">'
	+'<th>Estación</th>'
	+'<th style="text-align: right;">84</th>'
	+'<th style="text-align: right;">90</th>'
	+'<th style="text-align: right;">95</th>'
	+'<th style="text-align: right;">97</th>'
	+'<th style="text-align: right;">D2</th>'
	+'<th style="text-align: right;">GLP</th>'
	+'<th style="text-align: right;">GNV</th>'
	+'<th style="text-align: right;">Total</th>'
	+'</tr>'
	+'</thead>'
	+'<tbody>';
	var stations = data.stations;
	var countStations = stations.length;
	console.log('statios: '+stations);
	console.log('countStations: '+countStations);
	var total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	for (var i = 0; i < countStations; i++) {
		var attr = stations[i].isConnection ? '' : 'style="background-color: #ebccd1" title="Sin Conexión"';
		html += '<tr '+attr+'>'
		+'<th scope="row">'+stations[i].name+'</th>';
		var _data = stations[i].data;
		var _countData = _data.length;
		var product = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];

		if (type == 'money-include') {
			for (var j = 0; j < _countData; j++) {
				console.log('neto venta: ('+j+') '+_data[j].neto_venta);

				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = _data[j].neto_venta;

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_venta != '') {
						product[6] = _data[j].neto_venta;
					}
				}
				product[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0);

			}
		} else if (type == 'money-exclude') {
			for (var j = 0; j < _countData; j++) {
				console.log('neto venta: ('+j+') '+_data[j].neto_venta);
				console.log('importe_ci: '+_data[j].importe_ci);
				console.log('cantidad_ci: '+_data[j].cantidad_ci);

				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = _data[j].neto_venta - _data[j].importe_ci;

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_venta != '') {
						product[6] = _data[j].neto_venta - _data[j].importe_ci;
					}
				}
				product[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);//menos importe_ci

			}
		} else if (type == 'quantity-include') {
			for (var j = 0; j < _countData; j++) {
					console.log('neto cantidad: ('+j+') '+_data[j].neto_cantidad);
				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_cantidad;
				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = converterUM({type: 0, co: _data[j].neto_cantidad});

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_cantidad != '') {
						product[6] = converterUM({type: 1, co: _data[j].neto_cantidad});

					}
				}

				if(_data[j].product_id == '11620307') {
					product[7] += parseFloat(product[5]);
				} else if(_data[j].product_id == '11620308') {
					product[7] += parseFloat(product[6]);
				} else if(_data[j].product_id != '11620307' || _data[j].product_id == '11620308') {
					product[7] += parseFloat(_data[j].neto_cantidad != '' ? _data[j].neto_cantidad : 0);
				}
			}
		} else if (type == 'quantity-exclude') {
			for (var j = 0; j < _countData; j++) {
					console.log('neto cantidad: ('+j+') '+_data[j].neto_cantidad);
				if (_data[j].product_id == '11620301') {
					//84
					product[0] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620302') {
					//90
					product[1] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620305') {
					//95
					product[2] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620303') {
					//97
					product[3] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620304') {
					//D2
					product[4] = _data[j].neto_cantidad - _data[j].cantidad_ci;
				} else if (_data[j].product_id == '11620307') {
					//GLP
					product[5] = converterUM({type: 0, co: _data[j].neto_cantidad}) - converterUM({type: 0, co: _data[j].cantidad_ci});

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_cantidad != '') {
						product[6] = converterUM({type: 1, co: _data[j].neto_cantidad}) - converterUM({type: 1, co: _data[j].cantidad_ci});

					}
				}

				if (_data[j].product_id == '11620307') {
					product[7] += parseFloat(product[5]);
				} else if (_data[j].product_id == '11620308') {
					product[7] += parseFloat(product[6]);
				} else if (_data[j].product_id != '11620307' || _data[j].product_id == '11620308') {
					product[7] += parseFloat(_data[j].neto_cantidad != '' ? _data[j].neto_cantidad : 0) - parseFloat(_data[j].cantidad_ci);
				}
			}
		}

		html += '<td align="right">'+numeral(product[0]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[1]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[2]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[3]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[4]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[5]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[6]).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(product[7]).format('0,0')+'</td>';

		total[0] += parseFloat(product[0]);
		total[1] += parseFloat(product[1]);
		total[2] += parseFloat(product[2]);
		total[3] += parseFloat(product[3]);
		total[4] += parseFloat(product[4]);
		total[5] += parseFloat(product[5]);
		total[6] += parseFloat(product[6]);
		total[7] += parseFloat(product[7]);

		html += '</tr>';
	}
	html += '</tbody>';
	html += '<tfoot>';
	html += '<tr class="bg-primary text-white" style="font-weight: bold;">';
	html += '<td>Total</td>';
	html += '<td align="right">'+numeral(total[0]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[1]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[2]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[3]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[4]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[5]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[6]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(total[7]).format('0,0')+'</td>';
	html += '</tr>';
	html += '</tfoot>'
	+'</table></div><br><div class="graphics-'+type+'"></div><br>';

	stationColor.push(
		getColorComb('11620301', true)
	);
	stationColor.push(
		getColorComb('11620302', true)
	);
	stationColor.push(
		getColorComb('11620305', true)
	);
	stationColor.push(
		getColorComb('11620303', true)
	);
	stationColor.push(
		getColorComb('11620304', true)
	);
	stationColor.push(
		getColorComb('11620307', true)
	);
	stationColor.push(
		getColorComb('11620308', true)
	);

	for (var i = 0; i < (total.length) -1 ; i++) {
		if (type == 'money-include') {
			totalProductsInclude.push(
				numeral(total[i]).format('0')
			);
		} else if (type == 'money-exclude') {
			totalProductsExclude.push(
				numeral(total[i]).format('0')
			);
		} else if (type == 'quantity-include') {
			quiantityProductInclude.push(
				numeral(total[i]).format('0')
			);
		} else if (type == 'quantity-exclude') {
			quiantityProductExclude.push(
				numeral(total[i]).format('0')
			);
		}
	};

	return html;
}

function renderGraphicResume(type, paramsRequest) {
	$('.'+type).append('<canvas id="my-chart-'+type+'"></canvas><br><br><div class="btn-download-'+type+'"></div>');
	var ctx = document.getElementById('my-chart-'+type).getContext('2d');

	for (var i = 0; i < stationColor.length; i++) {
		console.log('stationColor: '+stationColor[i]);
	};

	var data = [];
	var label = '';
	var par;

	if (type == 'money-include') {
		label = 'Soles';
		data = totalProductsInclude;
		par = 2;
	} else if (type == 'money-exclude') {
		label = 'Soles';
		data = totalProductsExclude;
		par = 3;
	} else if (type == 'quantity-include') {
		label = 'Galones';
		data = quiantityProductInclude;
		par = 0;
	} else if (type == 'quantity-exclude') {
		label = 'Galones';
		data = quiantityProductExclude;
		par = 1;
	}

	for (var i = 0; i < data.length; i++) {
		console.log('data: '+data[i]);
	};

	var myChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['84', '90', '95', '97', 'D2', 'GLP', 'GNV'],
			datasets: [
				{
					label: label,
					backgroundColor: stationColor,
					data: data,
				}
			]
		}
	});
	$('.btn-download-'+type).append('<button class="btn btn-primary btn-block btn-lg download-sumary download-sumary-'+type+'" title="Generar información en Hoja de Cálculo"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>');
	$('.download-sumary-'+type).attr('data-typestation',paramsRequest.typeStation).attr('data-enddate',paramsRequest.dateEnd).attr('data-begindate',paramsRequest.dateBegin).attr('data-station',paramsRequest.id).attr('data-typecost',paramsRequest.typeCost).attr('data-qtysale',paramsRequest.qtySale).attr('data-include',par);
}

function generateCaclSumary() {
	$.get(url+'reports/generateCaclSumary', dataSumarySale, function(data) {
		checkSession(data);
		console.log('requests/generateCaclSumary');
		window.location = url+'reports/demoajax';
		console.log(data);
	});
}

function searchStatisticsSales(t) {
	$('.result-search').html(loading_bootstrap4());
	var paramsRequest = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),

		_dateBegin: $('#_start-date-request').val(),
		_dateEnd: $('#_end-date-request').val(),

		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	}

	console.log('start: '+paramsRequest.dateBegin+', end: '+paramsRequest.dateEnd);
	$.post(url+'requests/getStatisticsSale', paramsRequest, function(data) {
		checkSession(data);
		console.log('requests/getStatisticsSale');
		console.log(data);

		var html = '<div class="money-include"></div><div class="money-exclude d-none"></div>';

		$('.result-search').html(html);

		$('.money-include').html(templateTableStatistics(data, paramsRequest, 'money-include'));
		renderGraphicStatistics('money-include',paramsRequest);//1
		//clearDataResumen();

		$('.money-exclude').html(templateTableStatistics(data, paramsRequest, 'money-exclude'));
		renderGraphicStatistics('money-exclude',paramsRequest);//3
		//clearDataResumen();
		
		$('.btn-search-sale').prop('disabled', false);
	}, 'json');
}

function templateTableStatistics(data, pr, type) {
	var html = '<br><div>Excluir consumo <i class="fas fa-info-circle" title="Consumo interno de la empresa"></i>: '
	+'<div class="btn-group" aria-label="Default button group" role="group"><div class="btn-'+type+' true btn btn-default" data-action="true">Si</div><div class="btn-'+type+' false btn btn-primary" data-action="false">No</div></div>'
	+'</div>';
	html += '<div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table">'
	+'<thead>'
	+'<tr class="header-table-sumary">'
	+'<th colspan="10" style="text-align: center">Estadística de Ventas</th>'
	+'</tr>'
	+'<tr class="header-table-sumary">'
	+'<th></th><th colspan="8" style="text-align: center">Galones</th><th style="text-align: center">Soles</th>'
	+'</tr>'
	+'<tr class="header-table-sumary">'
	+'<th>Estación</th>'
	+'<th style="text-align: right;">84</th>'
	+'<th style="text-align: right;">90</th>'
	+'<th style="text-align: right;">95</th>'
	+'<th style="text-align: right;">97</th>'
	+'<th style="text-align: right;">D2</th>'
	+'<th style="text-align: right;">GLP</th>'
	+'<th style="text-align: right;">GNV</th>'
	+'<th style="text-align: right;">Total</th>'
	+'<th style="text-align: right;">Tienda</th>'
	+'</tr>'
	+'</thead>'
	+'<tbody>';
	var stations = data.stations;
	var countStations = stations.length;
	console.log('statios: '+stations);
	console.log('countStations: '+countStations);
	var total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	var item = [];
	var sale_total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	var sale__total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
	var dif_total = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];

	for (var i = 0; i < countStations; i++) {
		var attr = stations[i].isConnection ? '' : 'style="background-color: #ebccd1" title="Sin Conexión"';
		/*html += '<tr '+attr+'>'
		+'<th scope="row">'+stations[i].name+'</th>';*/
		var _data = stations[i].data;
		var _countData = _data.length;
		console.log(type+' -> _countData: '+_countData);
		//var product = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
		var sale = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
		var sale_ = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];
		var dif = [0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0];

		var text = [];

		text[0] = stations[i].name;

		text[1] = 'Anterior';

		if (type == 'money-include') {
			for (var j = 0; j < _countData; j++) {

				console.log('{type: '+_data[j].type);
				console.log('neto venta: ('+j+') '+_data[j].neto_venta+'}');

				if (_data[j].product_id == '11620301') {
					//84
					if(_data[j].type == 'actual') {
						sale[0] = _data[j].neto_venta;
					} else {
						sale_[0] = _data[j].neto_venta;
						dif[0] = amountPercentage({num1: sale[0], num2: sale_[0]});
					}

				} else if (_data[j].product_id == '11620302') {
					//90
					if(_data[j].type == 'actual') {
						sale[1] = _data[j].neto_venta;
					} else {
						sale_[1] = _data[j].neto_venta;
						dif[1] = amountPercentage({num1: sale[1], num2: sale_[1]});
					}

				} else if (_data[j].product_id == '11620305') {
					//95
					if(_data[j].type == 'actual') {
						sale[2] = _data[j].neto_venta;
					} else {
						sale_[2] = _data[j].neto_venta;
						dif[2] = amountPercentage({num1: sale[2], num2: sale_[2]});
					}

				} else if (_data[j].product_id == '11620303') {
					//97
					if(_data[j].type == 'actual') {
						sale[3] = _data[j].neto_venta;
					} else {
						sale_[3] = _data[j].neto_venta;
						dif[3] = amountPercentage({num1: sale[3], num2: sale_[3]});
					}

				} else if (_data[j].product_id == '11620304') {
					//D2
					if(_data[j].type == 'actual') {
						sale[4] = _data[j].neto_venta;
					} else {
						sale_[4] = _data[j].neto_venta;
						dif[4] = amountPercentage({num1: sale[4], num2: sale_[4]});
					}

				} else if (_data[j].product_id == '11620307') {
					//GLP
					if(_data[j].type == 'actual') {
						sale[5] = _data[j].neto_venta;
					} else {
						sale_[5] = _data[j].neto_venta;
						dif[5] = amountPercentage({num1: sale[5], num2: sale_[5]});
					}
					
					console.log('0.5: '+sale[5]);

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if(_data[j].type == 'actual') {
						if (_data[j].neto_venta != '') {
							sale[6] = _data[j].neto_venta;
						}
					} else {
						if (_data[j].neto_venta != '') {
							sale_[6] = _data[j].neto_venta;
							dif[6] = amountPercentage({num1: sale[6], num2: sale_[6]});
						}
					}
				}
				if(_data[j].type == 'actual' && _data[j].product_id != 'MARKET') {
					sale[7] += parseFloat(_data[j].neto_venta != '' || _data[j].neto_venta != null ? _data[j].neto_venta : 0);
					console.log('(actual) _data[j].neto_venta: '+_data[j].neto_venta+', sale[7]: '+sale[7]);
				} else if(_data[j].product_id != 'MARKET') {
					sale_[7] += parseFloat(_data[j].neto_venta != '' || _data[j].neto_venta != null ? _data[j].neto_venta : 0);
					dif[7] = amountPercentage({num1: sale[7], num2: sale_[7]});
					dif[7] = parseFloat(dif[7]);
					console.log('(anterior) _data[j].neto_venta: '+_data[j].neto_venta+', sale[7]: '+sale[7]+', sale_[7]: '+sale_[7]+', dif[7]: '+dif[7]);
				}

				if (_data[j].product_id == 'MARKET') {
					//GNV
					if(_data[j].type == 'actual') {
						if (_data[j].neto_venta != '') {
							sale[8] = _data[j].neto_venta;
						}
					} else {
						if (_data[j].neto_venta != '') {
							sale_[8] = _data[j].neto_venta;
							dif[8] = amountPercentage({num1: sale[8], num2: sale_[8]});
						}
					}
				}

				console.log('stations.name: '+stations[i].name);
				if (j == 0) {
					text[0] = stations[i].name;
				} else if (j == 1) {
					text[1] = 'Anterior';
				}

			}
		} else if (type == 'money-exclude') {
			for (var j = 0; j < _countData; j++) {
				console.log('neto venta: ('+j+') '+_data[j].neto_venta);
				console.log('importe_ci: '+_data[j].importe_ci);
				console.log('cantidad_ci: '+_data[j].cantidad_ci);

				if (_data[j].product_id == '11620301') {
					//84
					if(_data[j].type == 'actual') {
						sale[0] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[0] = _data[j].neto_venta - _data[j].importe_ci;
						dif[0] = amountPercentage({num1: sale[0], num2: sale_[0]});
					}

				} else if (_data[j].product_id == '11620302') {
					//90
					if(_data[j].type == 'actual') {
						sale[1] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[1] = _data[j].neto_venta - _data[j].importe_ci;
						dif[1] = amountPercentage({num1: sale[1], num2: sale_[1]});
					}

				} else if (_data[j].product_id == '11620305') {
					//95
					if(_data[j].type == 'actual') {
						sale[2] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[2] = _data[j].neto_venta - _data[j].importe_ci;
						dif[2] = amountPercentage({num1: sale[2], num2: sale_[2]});
					}

				} else if (_data[j].product_id == '11620303') {
					//97
					if(_data[j].type == 'actual') {
						sale[3] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[3] = _data[j].neto_venta - _data[j].importe_ci;
						dif[3] = amountPercentage({num1: sale[3], num2: sale_[3]});
					}

				} else if (_data[j].product_id == '11620304') {
					//D2
					if(_data[j].type == 'actual') {
						sale[4] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[4] = _data[j].neto_venta - _data[j].importe_ci;
						dif[4] = amountPercentage({num1: sale[4], num2: sale_[4]});
					}

				} else if (_data[j].product_id == '11620307') {
					//GLP
					if(_data[j].type == 'actual') {
						sale[5] = _data[j].neto_venta - _data[j].importe_ci;
					} else {
						sale_[5] = _data[j].neto_venta - _data[j].importe_ci;
						dif[5] = amountPercentage({num1: sale[5], num2: sale_[5]});
					}

				} else if (_data[j].product_id == '11620308') {
					//GNV
					if (_data[j].neto_venta != '') {
						if(_data[j].type == 'actual') {
							sale[6] = _data[j].neto_venta - _data[j].importe_ci;
						} else {
							sale_[6] = _data[j].neto_venta - _data[j].importe_ci;
							dif[6] = amountPercentage({num1: sale[6], num2: sale_[6]});
						}
					}
				}
				//sale[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);//menos importe_ci
				//sale_[7]
				//dif[7]
				if(_data[j].type == 'actual' && _data[j].product_id != 'MARKET') {
					sale[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);
				} else if(_data[j].product_id != 'MARKET') {
					sale_[7] += parseFloat(_data[j].neto_venta != '' ? _data[j].neto_venta : 0) - parseFloat(_data[j].importe_ci);
					dif[7] = amountPercentage({num1: sale[7], num2: sale_[7]});
					dif[7] = parseFloat(dif[7]);
				}

				if (_data[j].product_id == 'MARKET') {
					//GNV
					if (_data[j].neto_venta != '') {
						if(_data[j].type == 'actual') {
							sale[8] = _data[j].neto_venta - _data[j].importe_ci;
						} else {
							sale_[8] = _data[j].neto_venta - _data[j].importe_ci;
							dif[8] = amountPercentage({num1: sale[8], num2: sale_[8]});
						}
					}
				}

				console.log('stations.name: '+stations[i].name);
				if (j == 0) {
					text[0] = stations[i].name;
				} else if (j == 1) {
					text[1] = 'Anterior';
				}

			}
		}

		text[2] = 'Diferencia (%)';

		html += renderRowTableStatistics({
			col_0: text[0],
			col_1: sale[0],
			col_2: sale[1],
			col_3: sale[2],
			col_4: sale[3],
			col_5: sale[4],
			col_6: sale[5],
			col_7: sale[6],
			col_8: sale[7],
			col_9: sale[8],
			format: '0,0',
			attr: ' title="Periodo actual: '+pr.dateBegin+' - '+pr.dateEnd+'"',
		});

		html += renderRowTableStatistics({
			col_0: text[1],
			col_1: sale_[0],
			col_2: sale_[1],
			col_3: sale_[2],
			col_4: sale_[3],
			col_5: sale_[4],
			col_6: sale_[5],
			col_7: sale_[6],
			col_8: sale_[7],
			col_9: sale_[8],
			format: '0,0',
			attr: ' title="Periodo anterior: '+pr._dateBegin+' - '+pr._dateEnd+'"',
		});

		html += renderRowTableStatistics({
			col_0: text[2],
			col_1: dif[0],
			col_2: dif[1],
			col_3: dif[2],
			col_4: dif[3],
			col_5: dif[4],
			col_6: dif[5],
			col_7: dif[6],
			col_8: dif[7],
			col_9: dif[8],
			format: '0.00',
			attr: ' class="col-dif" title=""',
		});

		sale_total[0] += parseFloat(sale[0]);
		sale_total[1] += parseFloat(sale[1]);
		sale_total[2] += parseFloat(sale[2]);
		sale_total[3] += parseFloat(sale[3]);
		sale_total[4] += parseFloat(sale[4]);
		sale_total[5] += parseFloat(sale[5]);
		sale_total[6] += parseFloat(sale[6]);
		sale_total[7] += parseFloat(sale[7]);
		sale_total[8] += parseFloat(sale[8]);

		sale__total[0] += parseFloat(sale_[0]);
		sale__total[1] += parseFloat(sale_[1]);
		sale__total[2] += parseFloat(sale_[2]);
		sale__total[3] += parseFloat(sale_[3]);
		sale__total[4] += parseFloat(sale_[4]);
		sale__total[5] += parseFloat(sale_[5]);
		sale__total[6] += parseFloat(sale_[6]);
		sale__total[7] += parseFloat(sale_[7]);
		sale__total[8] += parseFloat(sale_[8]);

		dif_total[0] = amountPercentage({num1: sale_total[0], num2: sale__total[0]});
		dif_total[1] = amountPercentage({num1: sale_total[1], num2: sale__total[1]});
		dif_total[2] = amountPercentage({num1: sale_total[2], num2: sale__total[2]});
		dif_total[3] = amountPercentage({num1: sale_total[3], num2: sale__total[3]});
		dif_total[4] = amountPercentage({num1: sale_total[4], num2: sale__total[4]});
		dif_total[5] = amountPercentage({num1: sale_total[5], num2: sale__total[5]});
		dif_total[6] = amountPercentage({num1: sale_total[6], num2: sale__total[6]});
		dif_total[7] = amountPercentage({num1: sale_total[7], num2: sale__total[7]});
		dif_total[8] = amountPercentage({num1: sale_total[8], num2: sale__total[8]});

		//html += '</tr>';
	};
	html += '</tbody>';
	html += '<tfoot>';
	html += '<tr class="header-table-sumary" style="font-weight: bold;">';
	html += '<td>Totales</td>';
	html += '<td align="right">'+numeral(sale_total[0]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[1]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[2]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[3]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[4]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[5]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[6]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[7]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale_total[8]).format('0,0')+'</td>';
	html += '</tr>';

	html += '<tr class="header-table-sumary" style="font-weight: bold;">';
	html += '<td>Anterior</td>';
	html += '<td align="right">'+numeral(sale__total[0]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[1]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[2]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[3]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[4]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[5]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[6]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[7]).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(sale__total[8]).format('0,0')+'</td>';
	html += '</tr>';

	html += '<tr class="col-dif" style="font-weight: bold;">';
	html += '<td> '+text[2]+'</td>';
	html += '<td align="right">'+numeral(dif_total[0]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[1]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[2]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[3]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[4]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[5]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[6]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[7]).format('0.00')+'</td>';
	html += '<td align="right">'+numeral(dif_total[8]).format('0.00')+'</td>';
	html += '</tr>';

	html += '</tfoot>'
	+'</table></div><br><div class="graphics-'+type+'"></div><br>';

	return html;
}

function renderRowTableStatistics(data) {
	var html = '<tr'+data.attr+'><th scope="row">'+data.col_0+'</th>';
	html += '<td align="right">'+numeral(data.col_1).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_2).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_3).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_4).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_5).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_6).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_7).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_8).format(data.format)+'</td>';
	html += '<td align="right">'+numeral(data.col_9).format(data.format)+'</td></tr>';
	return html;
}

function renderGraphicStatistics(type, paramsRequest) {
	$('.'+type).append('<div class="btn-download-'+type+'"></div>');
	//var ctx = document.getElementById('my-chart-'+type).getContext('2d');

	for (var i = 0; i < stationColor.length; i++) {
		console.log('stationColor: '+stationColor[i]);
	};
	var par;

	if (type == 'money-include') {
		label = 'Soles';
		par = 0;
	} else if (type == 'money-exclude') {
		label = 'Soles';
		par = 1;
	}

	$('.btn-download-'+type).append('<button class="btn btn-primary btn-block btn-lg download-statistics download-statistics-'+type+'" title="Generar información en Hoja de Cálculo"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>');
	$('.download-statistics-'+type).attr('data-typestation',paramsRequest.typeStation).attr('data-enddate2',paramsRequest.dateEnd).attr('data-begindate2',paramsRequest.dateBegin).attr('data-enddate1',paramsRequest._dateEnd).attr('data-begindate1',paramsRequest._dateBegin).attr('data-station',paramsRequest.id).attr('data-typecost',paramsRequest.typeCost).attr('data-qtysale',paramsRequest.qtySale).attr('data-include',par);
}

function downloadStatistics(t) {
	console.log('downloadStatistics');
	console.log(t);
	var dateB1 = t.attr('data-begindate1').split("/");
	dateB1 = dateB1[0] + '-' + dateB1[1] + '-' + dateB1[2];

	var dateE1 = t.attr('data-enddate1').split("/");
	dateE1 = dateE1[0] + '-' + dateE1[1] + '-' + dateE1[2];


	var dateB2 = t.attr('data-begindate2').split("/");
	dateB2 = dateB2[0] + '-' + dateB2[1] + '-' + dateB2[2];

	var dateE2 = t.attr('data-enddate2').split("/");
	dateE2 = dateE2[0] + '-' + dateE2[1] + '-' + dateE2[2];

	console.log('dateB1: '+dateB1+', dateE1: '+dateE1);

	//validaciones
	var params = {
		id: t.attr('data-station') == '*' ? 'a' : t.attr('data-station'),
		beginDate: dateB1,
		endDate: dateE1,
		beginDate_: dateB2,
		endDate_: dateE2,
		typeStation: t.attr('data-typestation'),
		qtySale: t.attr('data-qtysale'),
		typeCost: t.attr('data-typecost'),
		typeResult: 1,
		include: t.attr('data-include'),
	};
	console.log('params.beginDate: '+params.beginDate+', params.endDate: '+params.endDate);
	var url_ = url+'reports/generateCaclStatistics/'+params.id+'/'+params.beginDate+'/'+params.endDate+'/'+params.beginDate_+'/'+params.endDate_+'/'+params.typeStation+'/'+params.qtySale+'/'+params.typeCost+'/'+params.include;
	console.log('url_: '+url_);
	window.location = url_;
}

/**
	* Resumen de margen por lineas (ventas/market_productos_linea)
	*/
function searchLineProduct() {
	var params = {
		id: $('#select-station').val(),
		dateBegin: $('#start-date-request').val(),
		dateEnd: $('#end-date-request').val(),

		typeStation: $('#typeStation').val(),
		isMarket: $('#data-ismarket').val(),
		qtySale: $('#qty_sale').val(),
		typeCost: $('#type_cost').val(),
		typeResult: 1,
	};
	$.post(url+'requests/getStationLines', params, function(data) {
		console.log(data);
		var req = {
			id: params.id,
			startDate: params.dateBegin,
			endDate: params.dateEnd,
		};
		var _prod = data.dataProducts;
		console.log('_prod.length:');
		console.log(_prod.length);
		if (_prod.length < 1) {
			$('.btn-search-sale').prop('disabled', false);
			$('.result-search').html('<div class="alert alert-info">No existe información</div>');
			return false;
		} else {
			var html = templateTableLineProduct(data,req);
			$('.result-search').html(html);
		}
		$('.btn-search-sale').prop('disabled', false);
		//$('.container-ss-station').removeClass('d-none');
	}, 'json');
}

function templateTableLineProduct(data, req) {
	var neto_cantidad = 0, neto_venta = 0, consumo_galon = 0, utilidad = 0;
	var html = '<br><div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table table-striped">';
	html += '<thead>';
	html += '<tr class="header-table-sumary">';
	html += '<th>Línea</th>';
	html += '<th style="text-align: right;">Cantidad</th>';
	html += '<th style="text-align: right;">Venta</th>';
	html += '<th style="text-align: right;">Costo</th>';
	html += '<th style="text-align: right;">Margen</th>';
	html += '</tr>'
	html += '</thead>';
	html += '<tbody class="result-line-product">';
	console.log(data.dataProducts);

	var dataProducts = data.dataProducts;
	for (var i = 0; i < dataProducts.length; i++) {
		html += renderRowTableLineProduct(dataProducts[i], req, true);
		neto_cantidad += parseFloat(dataProducts[i].neto_cantidad);
		neto_venta += parseFloat(dataProducts[i].neto_venta);
		consumo_galon += parseFloat(dataProducts[i].consumo_galon);
		utilidad += parseFloat(dataProducts[i].utilidad);
	};

	html += renderRowTableLineProduct({
		name: 'TOTAL',
		neto_cantidad: neto_cantidad,
		neto_venta: neto_venta,
		consumo_galon: consumo_galon,
		utilidad: utilidad,
	}, {}, false);

	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	return html;
}

function renderRowTableLineProduct(data,req,isLink) {
	var html = '<tr>';
	if (isLink) {
		html += '<th scope="row"><a class="search-detail-products-line" data-start-date="'+req.startDate+'" data-end-date="'+req.endDate+'" data-id="'+req.id+'" data-line-id="'+data.code+'" data-line-name="'+data.product+'" title="Ver detalle">'+data.product+'</a></th>';
	} else {
		html += '<th scope="row">'+data.name+'</th>';
	}
	html += '<td align="right">'+numeral(data.neto_cantidad).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(data.neto_venta).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(data.consumo_galon).format('0,0')+'</td>';
	html += '<td align="right">'+numeral(data.utilidad).format('0,0')+'</td></tr>';
	return html;
}

function searchDetailProductsLine(t) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
	var params = {
		id: t.attr('data-id'),
		lineId: t.attr('data-line-id'),
		lineName: t.attr('data-line-name'),
		startDate: t.attr('data-start-date'),
		endDate: t.attr('data-end-date'),
		typeStation: 1,
		typeCost: 'avg',
	};
	console.log('console de parametros');
	console.log(params);
	$.post(url+'requests/getStationProductsLine', params, function(data) {
		console.log(data);
		var req = {
			id: params.id,
			startDate: params.startDate,
			endDate: params.endDate,
		};
		var html = templateTableProductLine(data,req);
		//$('.container-search').html(html);

		setContendModal('#normal-modal', '.modal-title', params.lineName, true);
		setContendModal('#normal-modal', '.modal-body', html, true);
		//var btn = '<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"></span> Hoja de Cálculo</button>';
		var btn = '';
		btn += '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>';
		setContendModal('#normal-modal', '.modal-footer', btn, true);
	}, 'json');
}

function templateTableProductLine(data,req) {
	var neto_cantidad = 0, neto_venta = 0, consumo_galon = 0, utilidad = 0;
	var html = '<div class="row">';
	html += '<div class="col-md-6"><label>Fecha Inicio: </label> '+req.startDate+'</div>';
	html += '<div class="col-md-6"><label>Fecha Final: </label> '+req.endDate+'</div>';
	html += '</div>';
	html += '<br><div class="table-responsive" style="background-color: #fff; border-radius: 4px; padding: 5px;">';
	html += '<table class="table table-striped">';
	html += '<thead>';
	html += '<tr class="header-table-sumary">';
	html += '<th>Estación</th>';
	html += '<th>Producto</th>';
	html += '<th>Cantidad</th>';//cantidad vendida
	html += '<th>Venta</th>';//cantidad ingresada
	html += '<th>Costo</th>';//precio
	html += '<th>Margen</th>';//costo
	//stock
	html += '</tr>';
	html += '</thead>';
	html += '<tbody class="result-line-product">';
	console.log(data.stations);

	var stations = data.stations;
	for (var i = 0; i < stations.length; i++) {
		html += renderRowTableProductLine(stations[i], req);
		//agregar linea de total, asi como (templateTableLineProduct)
		console.log('stations[i]:');
		console.log(stations[i]);
		var _stations = sumRowTableProductLine(stations[i]);

		neto_cantidad += parseFloat(_stations.neto_cantidad);
		neto_venta += parseFloat(_stations.neto_venta);
		consumo_galon += parseFloat(_stations.consumo_galon);
		utilidad += parseFloat(_stations.utilidad);

		console.log('_stations.neto_cantidad');
		console.log(_stations.neto_cantidad);
		console.log('_stations.neto_venta');
		console.log(_stations.neto_venta);
		console.log('_stations.consumo_galon');
		console.log(_stations.consumo_galon);
		console.log('_stations.utilidad');
		console.log(_stations.utilidad);
	};

	html += `<th scope="row">TOTAL</th>
	<td></td>
	<td align="right">${numeral(neto_cantidad).format('0,0')}</td>
	<td align="right">${numeral(neto_venta).format('0,0')}</td>
	<td align="right">${numeral(consumo_galon).format('0,0')}</td>
	<td align="right">${numeral(utilidad).format('0,0')}</td></tr>`;

	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	return html;
}

function renderRowTableProductLine(data,req) {
	console.log('renderRowTableProductLine!');
	console.log(data);
	var dataProducts = data.data;
	console.log('dataProducts count: '+dataProducts.length);
	console.log(dataProducts);
	var html = '';
	for (var i = 0; i < dataProducts.length; i++) {
		html += '<tr>';
		html += '<th scope="row">'+data.name+'</th>';
		html += '<td>'+dataProducts[i].product_name+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].neto_cantidad).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].neto_venta).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].consumo_galon).format('0,0')+'</td>';
		html += '<td align="right">'+numeral(dataProducts[i].utilidad).format('0,0')+'</td></tr>';
	};
	return html;
}

function sumRowTableProductLine(data) {
	console.log('sumRowTableProductLine!');
	var neto_cantidad = 0, neto_venta = 0, consumo_galon = 0, utilidad = 0;
	console.log(data);
	var dataProducts = data.data;
	for (var i = 0; i < dataProducts.length; i++) {
		neto_cantidad += parseFloat(dataProducts[i].neto_cantidad);
		neto_venta += parseFloat(dataProducts[i].neto_venta);
		consumo_galon += parseFloat(dataProducts[i].consumo_galon);
		utilidad += parseFloat(dataProducts[i].utilidad);
	};
	return {neto_cantidad: neto_cantidad, neto_venta: neto_venta, consumo_galon: consumo_galon, utilidad: utilidad};
}


function searchMerchandise(_this, isQty) {
	$('.result-search').html(loading_bootstrap4());
	console.log(_this);
	var th1 = '';
	var th2 = '';
	var td = '';
	var params = {
		orgId: $('#select-station').val(),
		startDate: $('#start-date-request').val(),
		endDate: $('#start-date-request').val(),
	};
	if (isQty) { 
		$('.btn-search-merchandise').prop('disabled', false);
	} else {
		$('.btn-search-merchandise-sale').prop('disabled', false);
	}
	$.post(url+'requests/getMovementsByOrgId', params, function(data) {
		console.log('[searchMerchandise]');
		console.log(data);
		checkSession(data);
		if (data.status == 4) {
			$('.result-search').html(templateTableSearchMerchandise(data, isQty));
		} else {
			$('.result-search').html('No existen resultados');
		}
	}, 'json');
}

function templateTableSearchMerchandise(data, is) {
	console.log('-> templateTableSearchMerchandise IS: ');
	console.log(is);
	var _products = data._products;
	var _dataProducts = data._dataProducts;
	var _orgs = data._orgs;

	var dataStation = data.dataStation;
	var countStations = data.stations;
	var products = data.products;
	console.log('estaciones:');
	console.log(countStations);
	var stations = countStations;
	countStations = countStations.length;
	console.log('cantidad de estaciones: '+countStations);
	var countStations_ = countStations * 2;
	var headStations = '';
	var headStations_ = '';
	var bodyProducts = '';
	var bodyData = '';

	console.log('------->');
	console.log(dataStation);
	console.log('------->');
	
	console.log('bodyData');
	console.log(bodyData);
	var bodyData_ = [];
	var _bodyData = '';
	var cad = '';

	for (var key in _orgs) {
		headStations += '<th colspan="2">'+_orgs[key].name+'</th>';
		console.log('concatenado con html (th): '+headStations);
		headStations_ += '<th>STOCK</th><th>VENTA</th>';
	}

	console.log('bodyData_:');
	console.log(bodyData_);

	for (var key1 in _products) {
		bodyProducts += `<tr><th scope="row">${_products[key1].product_code}</th>
			<td>${_products[key1].productgroup_name}</td>
			<td>${_products[key1].product_name}</td>
			<td>${_products[key1].uom_name}</td>`;
		var org = _dataProducts[key1];
		for (key2 in org) {
			if (is) {
				bodyProducts += `<td>${org[key2]._stk_real}</td><td>${org[key2]._countsale}</td>`;
			} else {
				bodyProducts += `<td>S/ ${org[key2]._amount_real}</td><td>S/ ${org[key2]._amountsale}</td>`;
			}
		}
		bodyProducts += `</tr>`;
	}

	console.log('bodyProducts:');
	console.log(bodyProducts);

	console.log(headStations);
	console.log(headStations_);
	var html = `<br><div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th colspan="3">CONSOLIDADOS TODAS LAS EESS</th>
				<th></th>
				${headStations}
			</tr>
			<tr>
				<th>Código</th>
				<th>Linea</th>
				<th>Producto</th>
				<th>Unidad de Medida</th>
				${headStations_}
			</tr>
		</thead>
		<tbody>
			${bodyProducts}
		</tbody>
	</table>
	</div>`;
	console.log(html);
	return html;
}

/**
	* Add
	*/
function loadModalAddClient(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
	$.ajax({
		url: url+'configuration/viewClientAdd',
		type: 'POST',
		//dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
		data: {param1: 'value1'},
		success: function(data) {
			console.log('loadModalAddClient');
			console.log(data);
			setContendModal('#normal-modal', '.modal-title', 'Agregar Cliente', true);
			setContendModal('#normal-modal', '.modal-body', data, true);
			setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
		}
	});
}

function loadModalAddOrg(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

function loadModalAddWarehouse(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

/**
	* Edit
	*/
function loadModalEditClient(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

function loadModalEditOrg(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}

function loadModalEditWarehouse(_this) {
	setContendModal('#normal-modal', '.modal-title', 'Cargando...', true);
	setContendModal('#normal-modal', '.modal-body', loading(), true);
	setContendModal('#normal-modal', '.modal-footer', '<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>', true);
	$('#normal-modal').modal();
}









/**
	************************
* Funciones OCSManager *
************************
* A partir de esta linea se implementan las funciones generales para OCSManager
* Funciones para todo el frontend
*/


/**
	* Limpia arreglos y objetos de estaciones
	*/
function clearStations() {
	stations = [];
	stationsDesc = [];
	stationsTotal = [];
	stationsQty = [];
	stationsUtil = [];
	stationsPor = [];
	stationColor = [];
	dataStations = [];
	nameStation = {};
	porStation = {};
	gran_total = 0.0;

}

function clearDataResumen() {
	stationColor = [];
	totalProductsInclude = [];
	totalProductsExclude = [];
	quiantityProductInclude = [];
	quiantityProductExclude = [];
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
	* Generar color aleatorio
	* @return string color
	*/
function getRandomColor() {
	var letters = '0123456789ABCDEF';
	var color = '#';
	for(var i = 0; i < 6; i++ ) {
		color += letters[Math.floor(Math.random() * 16)];
	}
	return color;
}

/**
	* Copia de función converterUM(helper php)
	*/
function converterUM(data) {
	if(data.type == 0) {
		// return data.co / 3.7853;//11620307 - GLP
		return data.co / 1;//11620307 - GLP
	} else if(data.type == 1) {
		return data.co / 3.15;//11620308 - GNV
	} else {
		return data.co;
	}
}

/**
	* Obtener Colores de combutible
	* @param string combId, boolean isHEX
	* @return string color
	*/
function getColorComb(combId,isHEX) {
	var color = '';
	if(combId == '11620301') {
		//84
		color = '#EB281D';//rojo
	} else if(combId == '11620302') {
		//90
		color = '#36A133';//verde
	} else if(combId == '11620303') {
		//97
		color = '#F76516';//naranja
	} else if(combId == '11620304') {
		//Diesel
		color = '#C8C8C8';//gris
	} else if(combId == '11620305') {
		//95
		color = '#3336A1';//azulino
	} else if(combId == '11620306') {
		//Kerosene
		color = '#384636';//---
	} else if(combId == '11620307') {
		//GLP
		color = '#CEF523';//blanco
	} else if(combId == '11620308') {
		//GNV
		color = '#38AFFA';//celeste
	}
	return color;
}

/**
	* Limpar float en caso sea vacio
	* @param float val
	* @return float
	*/
function clearFloat(val) {
	if(val != '') {
		return parseFloat(val);
	} else {
		return parseFloat(0.0);
	}
}

/**
	* Demo GET/JSON para obtener IP del cliente
	* @return string ip
	*/
function getIPClient() {
	var ip = '0.0.0.0';
	$.getJSON("http://jsonip.com/?callback=?", function (data) {
		console.log(data);
		console.log(data.ip);
		ip = data.ip;
	});
	return ip;
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

function actionExclude(t, type) {
	console.log('type: '+type);
	console.log('data-action: '+t.attr('data-action'));
	if (type == 0) {
		if (t.attr('data-action') == 'true') {
			//$('.download-sumary').attr('data-include',2);
			console.log('excluir si 0');
			$('.money-include').addClass('d-none');
			$('.money-exclude').removeClass('d-none');

			$('.btn-money-exclude').removeClass('btn-default').removeClass('btn-primary');
			$('.btn-money-exclude.true').addClass('btn-primary');
			$('.btn-money-exclude.false').addClass('btn-default');
		} else {
			//$('.download-sumary').attr('data-include',3);
			console.log('excluir no 0');
			$('.money-exclude').addClass('d-none');
			$('.money-include').removeClass('d-none');

			$('.btn-money-include').removeClass('btn-default').removeClass('btn-primary');
			$('.btn-money-include.false').addClass('btn-primary');
			$('.btn-money-include.true').addClass('btn-default');
		}

		/*$('.btn-money-exclude').removeClass('btn-primary');
		$('.btn-money-include').addClass('btn-default');*/
	} else {
		if (t.attr('data-action') == 'true') {
			//$('.download-sumary').attr('data-include',1);
			console.log('excluir si 1');
			$('.quantity-include').addClass('d-none');
			$('.quantity-exclude').removeClass('d-none');

			$('.btn-quantity-exclude').removeClass('btn-default').removeClass('btn-primary');
			$('.btn-quantity-exclude.true').addClass('btn-primary');
			$('.btn-quantity-exclude.false').addClass('btn-default');
		} else {
			//$('.download-sumary').attr('data-include',0);
			console.log('excluir no 1');
			$('.quantity-exclude').addClass('d-none');
			$('.quantity-include').removeClass('d-none');

			$('.btn-quantity-include').removeClass('btn-default').removeClass('btn-primary');
			$('.btn-quantity-include.false').addClass('btn-primary');
			$('.btn-quantity-include.true').addClass('btn-default');
		}

		/*$('.btn-quantity-exclude').removeClass('btn-primary');
		$('.btn-quantity-include').addClass('btn-default');*/
	}
	/*t.removeClass('btn-default');
	t.addClass('btn-primary');*/
}

function  amountPercentage(data) {
	if(data.num1 == 0 && data.num2 == 0) {
		return 0;
	} else if(data.num1 > 0 && data.num2 == 0) {
		return 100;
	} else if(data.num1 == 0 && data.num2 > 0) {
		return -100;
	} else {
		return (((data.num1*100)/data.num2) - 100);
	}
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
		$('.btn-search-fleet').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	} else if(valStartDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span>, No se puede consultar con esta fecha'));
		$('.btn-search-fleet').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	}

	if(valEndDate == 0) {
		//error en formato de fecha
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-fleet').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	} else if(valEndDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-fleet').prop('disabled', false);
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
		$('.btn-search-fleet').prop('disabled', false);
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
		$('.btn-search-fleet').prop('disabled', false);
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
		$('.btn-search-fleet').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	} else if(valStartDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span>, No se puede consultar con esta fecha'));
		$('.btn-search-fleet').prop('disabled', false);
		//$('#start-date-request').focus();
		return false;
	}

	if(valEndDate == 0) {
		//error en formato de fecha
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, Error en formato de fecha.'));
		$('.btn-search-fleet').prop('disabled', false);
		//$('#end-date-request').focus();
		return false;
	} else if(valEndDate == 2) {
		//fecha futura
		$('.result-search').html(_alert('warning', '<span><i class="fas fa-exclamation-triangle"></i></span> <b>Importante</b>, No se puede consultar con esta fecha'));
		$('.btn-search-fleet').prop('disabled', false);
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
		$('.btn-search-fleet').prop('disabled', false);
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
		$('.btn-search-fleet').prop('disabled', false);
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