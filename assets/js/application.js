$( document ).ready(function() {
	if($('#username').length) {
		$('#username').focus();
	}

	// if( $('#start-date-request').length ) {
	// 	$('#start-date-request').datepicker({
	// 		language: 'es',
	// 		autoclose: true
	// 	});
	// }

	// $('[data-toggle="popover"]').popover();

	// /**
	//  * NAME: Bootstrap 3 Multi-Level by Johne
	//  * This script will active Triple level multi drop-down menus in Bootstrap 3.*
	//  */
	// $('li.dropdown-submenu').on('click', function(event) {
	// 	event.stopPropagation();
	// 	if ($(this).hasClass('open')) {
	// 		$(this).removeClass('open');
	// 	} else {
	// 		$('li.dropdown-submenu').removeClass('open');
	// 		$(this).addClass('open');
	// 	}
	// });

	$(document).on('click', '.btn-login', function() {
		login();
	});
	$('.keypress').keypress(function( event ) {
		if ( event.which == 13 ) {
			login();
		}
	});

	$(document).on('click', '.btn-identity', function() {
		identity();
	});
	$('.keypressIdentity').keypress(function( event ) {
		if ( event.which == 13 ) {
			$('.btn-identity').focus();
			event.preventDefault();
		}
	});

	$(document).on('click', '.btn-search-fleet', function() {
		console.log($(this).attr('data-ismarket'));
		$(this).prop('disabled', true);
		if ($('#typeStation').val() == 0) {
			searchValesClientesCredito($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 1) {
			searchComprobantesCobranza($(this).attr('data-ismarket'));
		}
	});

	$(document).on('click', '.btn-search-sale', function() {
		console.log($(this).attr('data-ismarket'));
		$(this).prop('disabled', true);
		if ($('#typeStation').val() == 3) {
			searchSumarySales($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 4) {
			searchStatisticsSales($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 5) {
			searchLineProduct($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 6) {			
			searchSalesForHours($(this).attr('data-ismarket'));
		} else if ($('#typeStation').val() == 7) {			
			searchLiquidacionDiaria($(this).attr('data-ismarket'));	
		} else if ($('#typeStation').val() == 8) {			
			searchSaldoSocio($(this).attr('data-ismarket'));	
		} else if ($('#typeStation').val() == 9) {			
			searchSobrantesFaltantes($(this).attr('data-ismarket'));	
		} else if ($('#typeStation').val() == 10) {
			searchMargenCliente($(this).attr('data-ismarket'));
		} else {
			searchSale($(this).attr('data-ismarket'));
		}
	});
	$(document).on('click', '.detail-station', function() {
		viewDetailStation($(this));
	});

	/**
	 * Chart demo
	 * viewChart sin uso
	 */
	$(document).on('click', '.view-chart', function() {
		viewChart();
	});

	$(document).on('click', '.show-ltr', function() {
		if($(this).attr('data-ltr')) {
			var title = $(this).attr('data-ltr');
			$(this).prepend(title+' - ');
			$(this).attr('data-ltr', '');
		}
	});

	$(document).on('click', '.download-comb-sales', function() {
		downloadCombSales($(this));
	});

	$(document).on('click', '.all-result-sales-comb', function() {
		detailAllResult($(this));
	});

	$('#event_period').datepicker({
		language: 'es',
		autoclose: true,
		inputs: $('.actual_range')
	});

	$('#_event_period').datepicker({
		language: 'es',
		autoclose: true,
		inputs: $('.previous_range')
	});

	$(document).on('click', '.btn-search-stock', function() {
		console.log('search-stock');
		$(this).prop('disabled', true);
		searchStock($(this).attr('data-ismarket'));
	});

	/**
	 * Evento click sobre tanque para ver detalle
	 *
	 */
	$(document).on('click', '.canvas-tank', function() {
		console.log('canvas-tank');
		//detailInfoTank($(this));
	});

	$(document).on('click', '.resume-info-tank', function() {
		console.log('resume-info-tank');
		showPO($(this),true);
	});

	$(document).on('click', '.download-comb-stock', function() {
		downloadCombStock($(this));
	});

	$(document).on('click', '.download-sumary', function() {
		downloadSumary($(this));
	});

	$(document).on('click', '.download-sales-for-hours', function() {
		downloadSalesForHours($(this));
	});

	$(document).on('click', '.download-liquidacion-diaria', function() {
		downloadLiquidacionDiaria($(this));
	});

	$(document).on('click', '.download-saldo-socio', function() {
		downloadSaldoSocio($(this));
	});

	$(document).on('click', '.download-sobrantes-faltantes', function() {
		downloadSobrantesFaltantes($(this));
	});

	$(document).on('click', '.download-margen-cliente', function() {
		downloadMargenCliente($(this));
	});

	$(document).on('click', '.btn-money-include, .btn-money-exclude', function() {
		actionExclude($(this), 0);
	});
	$(document).on('click', '.btn-quantity-include, .btn-quantity-exclude', function() {
		actionExclude($(this), 1);
	});

	$(document).on('click', '.download-statistics', function() {
		downloadStatistics($(this));
	});

	$(document).on('click', '.search-detail-products-line', function() {
		console.log('click en search-detail-products-line');
		searchDetailProductsLine($(this));
	});

	$(document).on('click', '.btn-search-merchandise', function() {
		$(this).prop('disabled', true);
		searchMerchandise($(this), true);
	});

	$(document).on('click', '.btn-search-merchandise-sale', function() {
		$(this).prop('disabled', true);
		searchMerchandise($(this), false);
	});

	/**
	 * Add item
	 */
	$(document).on('click', '.add-client', function() {
		loadModalAddClient($(this));
	});
	$(document).on('click', '.add-org', function() {
		loadModalAddOrg($(this));
	});
	$(document).on('click', '.add-warehouse', function() {
		loadModalAddWarehouse($(this));
	});

	/**
	 * Edit item
	 */
	$(document).on('click', '.edit-client', function() {
		loadModalEditClient($(this));
	});
	$(document).on('click', '.edit-org', function() {
		loadModalEditOrg($(this));
	});
	$(document).on('click', '.edit-warehouse', function() {
		loadModalEditWarehouse($(this));
	});

	/**
	 * Funcionalidad para mostrar opcion Producto en modulo Ventas por Horas
	 */
	$(document).on('change', 'input[name=local]', function() {		
		var local = $(this).val();
		if(local == "COMBUSTIBLE"){			
			$('#productos').removeClass('d-none');			
			$('#unidadmedida').removeClass('d-none');			
		}else if(local == "MARKET"){			
			$('#productos').addClass('d-none');
			$('#unidadmedida').addClass('d-none');
		}
	});	

	function decimalAdjust(type, value, exp) {
		// Si el exp no está definido o es cero...
		if (typeof exp === 'undefined' || +exp === 0) {
		  	return Math[type](value);
		}
		value = +value;
		exp = +exp;
		// Si el valor no es un número o el exp no es un entero...
		if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
		  	return NaN;
		}
		// Shift
		value = value.toString().split('e');
		value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
		// Shift back
		value = value.toString().split('e');
		return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
	}

	// Decimal round
	if (!Math.round10) {
		Math.round10 = function(value, exp) {
		  	return decimalAdjust('round', value, exp);
		};
	}
	// Decimal floor
	if (!Math.floor10) {
		Math.floor10 = function(value, exp) {
		  	return decimalAdjust('floor', value, exp);
		};
	}
	// Decimal ceil
	if (!Math.ceil10) {
		Math.ceil10 = function(value, exp) {
		  	return decimalAdjust('ceil', value, exp);
		};
	}

	// Capturamos enter en input socio
	$("#socio").keypress(function(e) {
		if(e.which == 13) {
		   // Acciones a realizar, por ej: enviar formulario.
		   console.log("Enter en input socio");
		
		   // Obtenemos el socio ingresado
		   var socio = $("#socio").val();
		   socio = socio.trim();

		   // Validamos que se haya ingresado socio
		   if(socio == "") {
				Swal.fire(
					'Error!',
					'Debe ingresar el RUC del socio!',
					'error'
				);
				return;
		   }

		   // Agregamos socios uno a uno en HTML
		   $("#lista-socios").append(
			   `<div class="alert alert-primary alert-dismissible fade show p-1 m-1" role="alert">
			  		${socio} &nbsp;&nbsp;&nbsp;&nbsp;
					<input name="socios[]" type="hidden" value="${socio}"></input>

					<button type="button" class="close p-1" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>`
		   );

		   // Limpiamos input de socio
		   $("#socio").val("");

		   // Obtenemos valores
		   var values = $("input[name='socios[]']").map(function(){return $(this).val();}).get();
		   console.log('Socios: ', values);
		}
	});

	// Validamos seleccion unica de checkbox
	$('#checkDetallado').click(function() {
		if ($(this).prop('checked')) {
		  	$('#checkResumido').prop('checked', false);
		}
	});
	$('#checkResumido').click(function() {
		if ($(this).prop('checked')) {
		  	$('#checkDetallado').prop('checked', false);
		}
	});	

	// Funcionalidad para hacer aparecer y desaparecer el div de proyeccion en Stock Diario
	$('#checkProyeccion').click(function() {
		$('#divProyeccion').toggleClass('d-none');
	});
});