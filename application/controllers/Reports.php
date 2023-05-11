<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends CI_Controller {

	public $tmp;
	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('functions');
	}

	public function resumeSales() 
	{
		$msg = getMemory(array(''));
		if(checkSession()) {
			$return = array();
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null && $this->uri->segment(6) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(6);

				$typeStationDesc = getDescriptionTypeStation($typeStation);

				$mod = '';
				$typeEst = '';
				if($typeStation == 0) {
					$mod = 'DETAIL_SALE_COMB';
					$typeEst = 'comb';
					$titleDocument = 'Venta de Combustibles';
				} else {
					$mod = 'DETAIL_SALE_MARKET';
					$typeEst = 'market';
					$titleDocument = 'Venta en Market';
				}
				error_log("Parametros en variable return y typeStation");
         	error_log(json_encode(array($return, $typeStation)));

				$dateBegin = $this->uri->segment(4);
				$dateEnd = $this->uri->segment(5);

				$formatDateBegin = formatDateCentralizer($dateBegin,2);
				$formatDateEnd = formatDateCentralizer($dateEnd,2);

				$qty_sale = 0;
				$type_cost = 0;


				$totalQty = 0;
				$totalSale = 0;

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStationDesc == 'MP' ? 'C' : $typeStationDesc,$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStationDesc == 'MP' ? 'C' : $typeStationDesc);
					}
				}
				error_log("Estaciones cargadas");
         	error_log(json_encode($dataStations));

				//load our new PHPExcel library
				$this->load->library('calc');

				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:F1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fechas');
				$this->calc->getActiveSheet()->setCellValue('B3', $dateBegin);
				$this->calc->getActiveSheet()->setCellValue('C3', $dateEnd);

				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');

				//$this->calc->getActiveSheet()->setCellValue('B4', $msg['memory']);

				//Inicio de cabecera (tabla)
				if($typeStation == 0) {
					$this->calc->getActiveSheet()->setCellValue('A7', 'Combustible');
				} else {
					$this->calc->getActiveSheet()->setCellValue('A7', 'Línea');
				}
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				if($typeStation == 0) {
					$this->calc->getActiveSheet()->setCellValue('B7', 'Cantidad');
				}
				$this->calc->getActiveSheet()->setCellValue('C7', 'Venta');
				$this->calc->getActiveSheet()->setCellValue('D7', 'Costo');
				$this->calc->getActiveSheet()->setCellValue('E7', 'Margen');
				$this->calc->getActiveSheet()->setCellValue('F7', '%');
				$this->calc->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A7:F7')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera (tabla)

				$total_precio_ = 0.0;
				$total_costo_ = 0.0;
				$total_cantidad_ = 0.0;
				$total_utilidad_ = 0.0;
				$total_por_utilidad_ = 0.0;
				$row = 8;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('B7:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('C7:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('D7:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('E7:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('F7:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getStyle('F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&qty_sale='.$qty_sale.'&type_cost='.$type_cost;
						error_log("Url de la estacion cargada");
               	error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
						//codigo  | descripcion | total_cantidad | total_venta | af_cantidad |       af_total        |    costo     |        venta_sin_igv        | descuentos | neto_cantidad | neto_soles
					}
					error_log("Data de la estacion cargada");
            	error_log(json_encode($dataRemoteStations));
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->client_name);
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $dataStation->name);
					$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('B'.$row.':E'.$row);
					$row++;

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;
					$total_costo = 0.0;
					$total_margen = 0.0;
					$total_por_margen = 0.0;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						if($typeStation == 0) {
							//array para comb
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);

									if($d[0] == '11620308') {
										//$gal = $d[9];///3.7853;
										$gal = converterUM(array('type' => 1, 'co' => $d[9]));//gal
										$venta = $d[10];//venta
										$costo = $d[6];//costo
										$venta_sin_igv = $d[7];//venta sin igv
										$utilidad = $venta_sin_igv - $costo;//utilidad
										$por_utilidad = (($utilidad/$costo)*1)*100;
									} else if($d[0] != '11620307') {
										$gal = $d[9];
										$venta = $d[10];
										$costo = $d[6];
										$utilidad = $d[7] - $d[6];
										$por_utilidad = (($utilidad/$costo)*1)*100;
									} else {
										$gal = converterUM(array('type' => 0, 'co' => $d[9]));//gal
										$venta = $d[10];//venta
										$costo = $d[6];//costo
										$venta_sin_igv = $d[7];//venta sin igv
										$utilidad = $venta_sin_igv - $costo;//utilidad
										$por_utilidad = (($utilidad/$costo)*1)*100;
									}

									//$sheet->getStyle("A1")->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('A'.$row, $d[1]);
									$this->calc->getActiveSheet()->setCellValue('B'.$row, $gal)->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$venta, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$costo, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$utilidad, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
									$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$por_utilidad, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');

									$total_cantidad += $gal;
									$total_precio += $venta;
									$total_costo += $costo;
									$total_margen += $utilidad;
									$total_por_margen += (($utilidad/$costo)*1)*100;
								}
								$row++;
							}
						} else {
							//market
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);

									$this->calc->getActiveSheet()->setCellValue('A'.$row, $d[1]);
									//$this->calc->getActiveSheet()->setCellValue('B'.$row, $d[2]);
									$imp = $d[5] == '' ? 0.00 : $d[5];//round
									$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$imp, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');

									$cost = $d[4] == '' ? 0.00 : $d[4];
									$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$cost, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');

									$mar = $d[6] == '' ? 0.00 : $d[6];//round
									$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$mar, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');

									$_por_margen = (($mar/$cost)*1)*100;//round //Cuando el costo es 0, entonces la division falla, y el margen termina siendo 0
									$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$_por_margen, 2, '.', ','));
									$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');

									//$total_cantidad += $d[2];
									$total_precio += $d[5];
									$total_costo += $d[4];
									$total_margen += $d[6];
									$total_por_margen += $_por_margen;
								}
								$row++;
							}
						}

					} else {
						$return['status'] = 4;
					}

					$this->calc->getActiveSheet()->setCellValue('A'.$row, 'Total Estación');
					if($typeStation == 0) {
						$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$total_cantidad, 2, '.', ','));
					}

					$total_por_margen = (($total_margen/$total_costo)*1)*100;

					$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$total_precio, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
					$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$total_costo, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
					$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$total_margen, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
					$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$total_por_margen, 2, '.', ','));
					$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
					$row += 2;

					$total_cantidad_ += $total_cantidad;
					$total_precio_ += $total_precio;
					$total_costo_ += $total_costo;
					$total_utilidad_ += $total_margen;
					$total_por_utilidad_ += $total_por_margen;
				}
				$total_por_utilidad_ = (($total_utilidad_/$total_costo_)*1)*100;

				$row += 2;
				$this->calc->getActiveSheet()->setCellValue('A'.$row, 'Total Estaciones');
				if($typeStation == 0) {
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$total_cantidad_, 2, '.', ','));
				}
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$total_precio_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');

				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$total_costo_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');

				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$total_utilidad_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');

				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$total_por_utilidad_, 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');

				unset($dataRemoteStations);
				$msg2 = getMemory(array(''));
				//$this->calc->getActiveSheet()->setCellValue('C4', $msg2['memory']);

				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_sale_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache

				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');

			} else {
				echo 'No';
				//parametros vacios
				//error 404
			}
		} else {
			//no session
			//pagina de error 404
		}
	}

	public function resumeSalesForHours() //EDITAR //SALES
	{
		$msg = getMemory(array(''));
		if(checkSession()) {
			$return = array();
			// echo '3: '.$this->uri->segment(3).', 4: '.$this->uri->segment(4).', 5: '.$this->uri->segment(5).', 6: '.$this->uri->segment(6);
			error_log(  json_encode( $this->uri ) );
			// exit;
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null && $this->uri->segment(6) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(6);
				$local = $this->uri->segment(7);
				$importe = $this->uri->segment(8);
				$modo = $this->uri->segment(9);				
				$productos = $this->uri->segment(10);				
				$unidadmedida = $this->uri->segment(11);

				$typeStationDesc = getDescriptionTypeStation($typeStation);

				$mod = '';
				$typeEst = '';
				if($typeStation == 6) {
					$mod = 'TOTALS_SALE_FOR_HOURS';
					$typeEst = 'salesforhours';
					$titleDocument = 'Ventas por Horas';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$dateBegin = $this->uri->segment(4);
				$dateEnd = $this->uri->segment(5);

				$formatDateBegin = formatDateCentralizer($dateBegin,2);
				$formatDateEnd = formatDateCentralizer($dateEnd,2);

				$qty_sale = 0;
				$type_cost = 0;


				$totalQty = 0;
				$totalSale = 0;

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStationDesc == 'MP' ? 'C' : $typeStationDesc,$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStationDesc == 'MP' ? 'C' : $typeStationDesc);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				//load our new PHPExcel library
				$this->load->library('calc');

				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:F1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fechas');
				$this->calc->getActiveSheet()->setCellValue('B3', $dateBegin);
				$this->calc->getActiveSheet()->setCellValue('C3', $dateEnd);

				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');

				//$this->calc->getActiveSheet()->setCellValue('B4', $msg['memory']);

				//Inicio de cabecera (tabla)
				$this->calc->getActiveSheet()->setCellValue('A7', 'Horas');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				$this->calc->getActiveSheet()->setCellValue('B7', '0');
				$this->calc->getActiveSheet()->setCellValue('C7', '1');
				$this->calc->getActiveSheet()->setCellValue('D7', '2');
				$this->calc->getActiveSheet()->setCellValue('E7', '3');
				$this->calc->getActiveSheet()->setCellValue('F7', '4');
				$this->calc->getActiveSheet()->setCellValue('G7', '5');
				$this->calc->getActiveSheet()->setCellValue('H7', '6');
				$this->calc->getActiveSheet()->setCellValue('I7', '7');
				$this->calc->getActiveSheet()->setCellValue('J7', '8');
				$this->calc->getActiveSheet()->setCellValue('K7', '9');
				$this->calc->getActiveSheet()->setCellValue('L7', '10');
				$this->calc->getActiveSheet()->setCellValue('M7', '11');
				$this->calc->getActiveSheet()->setCellValue('N7', '12');
				$this->calc->getActiveSheet()->setCellValue('O7', '13');
				$this->calc->getActiveSheet()->setCellValue('P7', '14');
				$this->calc->getActiveSheet()->setCellValue('Q7', '15');
				$this->calc->getActiveSheet()->setCellValue('R7', '16');
				$this->calc->getActiveSheet()->setCellValue('S7', '17');	
				$this->calc->getActiveSheet()->setCellValue('T7', '18');	
				$this->calc->getActiveSheet()->setCellValue('U7', '19');
				$this->calc->getActiveSheet()->setCellValue('V7', '20');	
				$this->calc->getActiveSheet()->setCellValue('W7', '21');	
				$this->calc->getActiveSheet()->setCellValue('X7', '22');	
				$this->calc->getActiveSheet()->setCellValue('Y7', '23');									
				$this->calc->getActiveSheet()->setCellValue('Z7', 'Total');
				$this->calc->getActiveSheet()->setCellValue('AA7', 'Promedio');
				$this->calc->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A7:AA7')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera (tabla)

				// $total_precio_ = 0.0;
				// $total_costo_ = 0.0;
				// $total_cantidad_ = 0.0;
				// $total_utilidad_ = 0.0;
				// $total_por_utilidad_ = 0.0;
				$row = 8;

				$resultAllGroupByCombustible = array();

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&desde='.$dateBegin.'&hasta='.$dateEnd.'&local='.$local.'&importe='.$importe.'&modo='.$modo.'&productos='.$productos.'&unidadmedida='.$unidadmedida;
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$result = array();
					
					if($dataRemoteStations != false) {

						if($dataRemoteStations == array("")){ //Si hubo conexión a la estacion pero no hubo resultados
							$return['status'] = 1;
						}else{

						$return['status'] = 1;
						if($typeStation == 6) {
							/*OBTENEMOS VARIABLES PARA LA FUNCIONALIDAD*/
							$desde      = $dateBegin;
							$hasta      = $dateEnd;
							$diasemana  = "TODOS";
							$producto   = $productos;
							$lado       = "TODOS";
							$estaciones = "TODAS";
							$local      = $local;
							$importe    = $importe;
							$bResumido  = $modo;
							/*CERRAR OBTENEMOS VARIABLES PARA LA FUNCIONALIDAD*/							

							foreach($dataRemoteStations as $drs) {
								if($drs != '') {
									$a = explode("|", $drs);

									/*FUNCIONALIDAD PARA IMITAR RESULTADO DE SISTEMAWEB*/
									$ch_sucursal = $a[11]; //$ch_sucursal = $a[0];

									// si es que se muestra el importe o la cantidad (galones)
									if ($importe == "CANTIDAD"){
										$nu_ventagalon = $a[1];
										$nu_ventavalor = $a[2];
									}
									else
									{
										$nu_ventavalor = $a[1];
										$nu_ventagalon = $a[2];
									}

									$nu_afericion = $a[3];
									$nu_preciogalon = $a[4];
									$ch_codigocombustible = $a[5];
									$dt_fechaparte = $a[6];
									$dt_horaparte = $a[7];
									$dt_diaparte = $a[8];

									//$propio = ($propiedad[$ch_sucursal]=='S'?"ESTACION":"OTROS");
									//$ch_sucursal = $almacenes[$ch_sucursal];

									$propio = "ESTACION";

									/* Si no esta resumido, totalizar venta por dia */
									if ($bResumido == "DETALLADO") {
										if($local == "COMBUSTIBLE"){
											$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['partes'][$ch_codigocombustible][$dt_horaparte] += $nu_ventavalor;

											$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['partes'][$ch_codigocombustible]['total'] += $nu_ventavalor;
											$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['partes'][$ch_codigocombustible]['promedio'] += $nu_ventavalor/24;
										}else if($local == "MARKET"){
											$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['partes'][$dt_fechaparte][$dt_horaparte] += $nu_ventavalor;

											$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['partes'][$dt_fechaparte]['total'] += $nu_ventavalor;
											$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['partes'][$dt_fechaparte]['promedio'] += $nu_ventavalor/24;
										}
									}

									/* Calcula total por CC */
									$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['totales'][$dt_horaparte] += $nu_ventavalor;
									$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['totales']['total'] += $nu_ventavalor;
									$result['propiedades'][$propio]['almacenes'][$ch_sucursal]['totales']['promedio'] += $nu_ventavalor/24;

									/* Calcula total por Grupo */
									$result['propiedades'][$propio]['totales'][$dt_horaparte] += $nu_ventavalor;
									$result['propiedades'][$propio]['totales']['total'] += $nu_ventavalor;
									$result['propiedades'][$propio]['totales']['promedio'] += $nu_ventavalor/24;

									/* Calcula total General */
									$result['totales'][$dt_horaparte] += $nu_ventavalor;
									$result['totales']['total'] += $nu_ventavalor;
									$result['totales']['promedio'] += $nu_ventavalor/24;

									/* Calcula total General */
									$result['promedio'][$dt_horaparte] += $nu_ventavalor;
									$result['promedio']['total'] += $nu_ventavalor;
									$result['promedio']['promedio'] += $nu_ventavalor/24;

									/* Calcula Porcentaje */
									$result['porcentaje'][$dt_horaparte] += $nu_ventavalor;
									$result['porcentaje']['total'] += $nu_ventavalor;
									$result['porcentaje']['promedio'] += $nu_ventavalor/24;
									/*CERRAR FUNCIONALIDAD PARA IMITAR RESULTADO DE SISTEMAWEB*/

									/* Calcula el total General de todas las estaciones */
									$resultAllGroupByCombustible['totales'][$dt_horaparte] += $nu_ventavalor;
									$resultAllGroupByCombustible['totales']['total'] += $nu_ventavalor;
									$resultAllGroupByCombustible['totales']['promedio'] += $nu_ventavalor/24;

									$resultAllGroupByCombustible['promedio'][$dt_horaparte] += $nu_ventavalor;
									$resultAllGroupByCombustible['promedio']['total'] += $nu_ventavalor;
									$resultAllGroupByCombustible['promedio']['promedio'] += $nu_ventavalor/24;

									$resultAllGroupByCombustible['porcentaje'][$dt_horaparte] += $nu_ventavalor;
									$resultAllGroupByCombustible['porcentaje']['total'] += $nu_ventavalor;
									$resultAllGroupByCombustible['porcentaje']['promedio'] += $nu_ventavalor/24;

									/* Calcula total General de todas las estaciones agrupadas por combustible */
									if($local == "COMBUSTIBLE"){
										$resultAllGroupByCombustible['totales_combustibles'][$ch_codigocombustible][$dt_horaparte] += $nu_ventavalor;
										$resultAllGroupByCombustible['totales_combustibles'][$ch_codigocombustible]['total'] += $nu_ventavalor;
										$resultAllGroupByCombustible['totales_combustibles'][$ch_codigocombustible]['promedio'] += $nu_ventavalor/24;
									}else if($local == "MARKET"){
										$resultAllGroupByCombustible['totales_combustibles'][$dt_fechaparte][$dt_horaparte] += $nu_ventavalor;
										$resultAllGroupByCombustible['totales_combustibles'][$dt_fechaparte]['total'] += $nu_ventavalor;
										$resultAllGroupByCombustible['totales_combustibles'][$dt_fechaparte]['promedio'] += $nu_ventavalor/24;
									}

									/* Calcula total General de todas las estaciones agrupadas por estaciones y combustibles*/
									if($local == "COMBUSTIBLE"){
										$resultAllGroupByCombustible['estaciones'][$dataStation->name][$ch_codigocombustible][$dt_horaparte] += $nu_ventavalor;
										$resultAllGroupByCombustible['estaciones'][$dataStation->name][$ch_codigocombustible]['total'] += $nu_ventavalor;
										$resultAllGroupByCombustible['estaciones'][$dataStation->name][$ch_codigocombustible]['promedio'] += $nu_ventavalor/24;
									}else if($local == "MARKET"){
										$resultAllGroupByCombustible['estaciones'][$dataStation->name][$dt_fechaparte][$dt_horaparte] += $nu_ventavalor;
										$resultAllGroupByCombustible['estaciones'][$dataStation->name][$dt_fechaparte]['total'] += $nu_ventavalor;
										$resultAllGroupByCombustible['estaciones'][$dataStation->name][$dt_fechaparte]['promedio'] += $nu_ventavalor/24;
									}
								}
							}

							$numerodias = substr($hasta,0,2) - substr($desde,0,2) + 1;

							for($i=0;$i<24;$i++){
								$result['promedio'][$i] = $result['promedio'][$i]/$numerodias;
								$result['porcentaje'][$i] = $result['porcentaje'][$i]*100/$result['porcentaje']['total'];
							}

							$result['promedio']['total'] = $result['promedio']['total']/$numerodias;
							$result['promedio']['promedio'] = $result['promedio']['promedio']/$numerodias;

							$result['porcentaje']['total'] = '100';
							$result['porcentaje']['promedio'] = ' ';							
						}

						}
					}
					else {
						//NO HACE NADA
					}
						
					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'data' => $result,
						// 'total_qty' => $total_cantidad,
						// 'total_price' => $total_precio,
						// 'total_cost' => $total_costo,
						// 'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}

				$numerodias = substr($hasta,0,2) - substr($desde,0,2) + 1;

				for($i=0;$i<24;$i++){
					$resultAllGroupByCombustible['promedio'][$i] = $resultAllGroupByCombustible['promedio'][$i]/$numerodias;
					$resultAllGroupByCombustible['porcentaje'][$i] = $resultAllGroupByCombustible['porcentaje'][$i]*100/$resultAllGroupByCombustible['porcentaje']['total'];
				}

				$resultAllGroupByCombustible['promedio']['total'] = $resultAllGroupByCombustible['promedio']['total']/$numerodias;
				$resultAllGroupByCombustible['promedio']['promedio'] = $resultAllGroupByCombustible['promedio']['promedio']/$numerodias;

				$resultAllGroupByCombustible['porcentaje']['total'] = '100';
				$resultAllGroupByCombustible['porcentaje']['promedio'] = ' ';

				$return['all_stations'] = $resultAllGroupByCombustible;

				//DATOS PARA MOSTRAR EN EXCEL
				error_log(json_encode($return));
				foreach($dataStations as $key => $dataStation) {
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->client_name);
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $dataStation->name);
					$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('B'.$row.':E'.$row);
					$row++;

					$columnas_excel = array(0=>"A", 1=>"B", 2=>"C", 3=>"D" ,4=>"E", 5=>"F", 6=>"G", 7=>"H", 8=>"I", 9=>"J", 10=>"K", 11=>"L", 12=>"M", 13=>"N", 14=>"O", 15=>"P", 16=>"Q", 17=>"R", 18=>"S", 19=>"T", 20=>"U", 21=>"V", 22=>"W", 23=>"X", 24=>"Y", 25=>"Z", 26=>"AA");
					
					$array_combustible = $return['all_stations']['estaciones'][$dataStation->name];
					ksort($array_combustible);
					error_log("****** array_combustible ******");
					error_log( json_encode($array_combustible) );
					
					$nombre_combustible = array(
						'11620301' => '84',
						'11620302' => '90',
						'11620303' => '97',
						'11620304' => 'D2',
						'11620305' => '95',
						'11620307' => 'GLP',
						'11620308' => 'GNV'
					);

					foreach($array_combustible as $key=>$combustible){						
						$this->calc->getActiveSheet()->setCellValue("A".$row, $nombre_combustible[$key]);
						for ($i=1; $i<=24; $i++) {
							$combustible[$i-1] = ($combustible[$i-1] == NULL || $combustible[$i-1] == "") ? 0 : round($combustible[$i-1]);
							$this->calc->getActiveSheet()->setCellValue($columnas_excel[$i].$row, round($combustible[$i-1]));
						}
						$this->calc->getActiveSheet()->setCellValue("Z".$row, round($combustible['total']));
						$this->calc->getActiveSheet()->setCellValue("AA".$row, round($combustible['promedio']));
						$row++;						
					}					
					$row++;

					foreach($return['stations'] as $key=>$estaciones){
						if($dataStation->name == $estaciones['name']){							
														
							$totales = $estaciones['data']['totales'];
							$this->calc->getActiveSheet()->setCellValue("A".$row, "Totales");
							for ($i=1; $i<=24; $i++) {
								$totales[$i-1] = ($totales[$i-1] == NULL || $totales[$i-1] == "") ? 0 : round($totales[$i-1]);
								$this->calc->getActiveSheet()->setCellValue($columnas_excel[$i].$row, round($totales[$i-1]));
							}
							$this->calc->getActiveSheet()->setCellValue("Z".$row, round($totales['total']));
							$this->calc->getActiveSheet()->setCellValue("AA".$row, round($totales['promedio']));
							$row++;

							$promedio = $estaciones['data']['promedio'];
							$this->calc->getActiveSheet()->setCellValue("A".$row, "Promedio");
							for ($i=1; $i<=24; $i++) {
								$promedio[$i-1] = ($promedio[$i-1] == NULL || $promedio[$i-1] == "") ? 0 : round($promedio[$i-1]);
								$this->calc->getActiveSheet()->setCellValue($columnas_excel[$i].$row, round($promedio[$i-1]));
							}
							$this->calc->getActiveSheet()->setCellValue("Z".$row, round($promedio['total']));
							$this->calc->getActiveSheet()->setCellValue("AA".$row, round($promedio['promedio']));
							$row++;

							$porcentaje = $estaciones['data']['porcentaje'];
							$this->calc->getActiveSheet()->setCellValue("A".$row, "Porcentaje");
							for ($i=1; $i<=24; $i++) {
								$porcentaje[$i-1] = ($porcentaje[$i-1] == NULL || $porcentaje[$i-1] == "") ? 0 : $porcentaje[$i-1];
								$this->calc->getActiveSheet()->setCellValue($columnas_excel[$i].$row, round($porcentaje[$i-1], 2));
							}
							$this->calc->getActiveSheet()->setCellValue("Z".$row, round($porcentaje['total']));
							$this->calc->getActiveSheet()->setCellValue("AA".$row, round($porcentaje['promedio']));
							$row++;

						}
					}	
					$row++;
				}
				//CERRAR DATOS PARA MOSTRAR EN EXCEL

				//GENERACION EXCEL
				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_sale_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache

				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
				//CERRAR GENERACION EXCEL
			} else {
				echo 'No';
				//parametros vacios
				//error 404
			}
		} else {
			//no session
			//pagina de error 404
		}
	}

	public function resumeLiquidacionDiaria() //EDITAR //LIQ
	{
		$msg = getMemory(array(''));
		if(checkSession()) {
			$return = array();
			// echo '3: '.$this->uri->segment(3).', 4: '.$this->uri->segment(4).', 5: '.$this->uri->segment(5).', 6: '.$this->uri->segment(6);
			error_log(  json_encode( $this->uri ) );
			// exit;
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null && $this->uri->segment(6) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
         		$typeStation = $this->uri->segment(6);

				$typeStationDesc = getDescriptionTypeStation($typeStation);

				$mod = '';
				$typeEst = '';
				if($typeStation == 7) {
					$mod = 'TOTALS_LIQUIDACION_DIARIA';
					$typeEst = 'liquidaciondiaria';
					$titleDocument = 'Liquidacion Diaria';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$dateBegin = $this->uri->segment(4);
				$dateEnd = $this->uri->segment(5);

				/*Obtenemos fecha en formato correcto*/
				$porcionesDateBegin = explode("-", $dateBegin);
				$dateBegin_ = $porcionesDateBegin[0] . "/" . $porcionesDateBegin[1] . "/" . $porcionesDateBegin[2];
				$porcionesDateEnd = explode("-", $dateEnd);
				$dateEnd_ = $porcionesDateEnd[0] . "/" . $porcionesDateEnd[1] . "/" . $porcionesDateEnd[2];
				/*Cerrar Obtenemos fecha en formato correcto*/

				$formatDateBegin = formatDateCentralizer($dateBegin,2);
				$formatDateEnd = formatDateCentralizer($dateEnd,2);

				$qty_sale = 0;
				$type_cost = 0;


				$totalQty = 0;
				$totalSale = 0;

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStationDesc == 'MP' ? 'C' : $typeStationDesc,$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStationDesc == 'MP' ? 'C' : $typeStationDesc);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				//load our new PHPExcel library
				$this->load->library('calc');

				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:F1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
	
				$this->calc->getActiveSheet()->setCellValue('A3', 'Fechas');
				$this->calc->getActiveSheet()->setCellValue('B3', $dateBegin);
				$this->calc->getActiveSheet()->setCellValue('C3', $dateEnd);
	
				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');
	
				//$this->calc->getActiveSheet()->setCellValue('B4', $msg['memory']);

				//Inicio de cabecera (tabla)
				$this->calc->getActiveSheet()->setCellValue('A7', 'CONCEPTO');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('50');

				$this->calc->getActiveSheet()->setCellValue('B7', 'CANTIDAD');				
				$this->calc->getActiveSheet()->setCellValue('C7', 'IMPORTE');				
				$this->calc->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A7:C7')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				$this->calc->getActiveSheet()->freezePane('A8');
				//Fin de cabecera (tabla)
				
				// $total_precio_ = 0.0;
				// $total_costo_ = 0.0;
				// $total_cantidad_ = 0.0;
				// $total_utilidad_ = 0.0;
				// $total_por_utilidad_ = 0.0;
				$tab      = '                ';
				$espacio  = '    ';
				$dnone    = 'style="display:none;"';
				$fDecimal = 2;
				$row = 8;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('B7:B5000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('C7:C5000')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getStyle('B1:B5000')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00); //Formato con dos decimales (.00) a la celda B 
				$this->calc->getActiveSheet()->getStyle('C1:C5000')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00); //Formato con dos decimales (.00) a la celda C
				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&desde='.$dateBegin_.'&hasta='.$dateEnd_;
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));
					
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					//DATOS PARA MOSTRAR EN EXCEL
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->client_name);
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $dataStation->name);
					$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
					// $this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '7952b3')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 12,
								//'name'  => 'Verdana'
							)
						)
					);
					$row++;

					//Verificar data
					// echo "<pre>";
					// print_r($dataRemoteStations);
					// echo "</pre>";

					//1. Venta Combustible					
					$venta_combustible = $dataRemoteStations['1_venta_vombustible'];
					$liquido 	       = number_format($venta_combustible[0]['liquido'], 2);
					$liquido_canti 	 = number_format($venta_combustible[0]['liquido_canti'], 2);
					$glp 		          = number_format($venta_combustible[0]['glp'], 2);
					$glp_canti         = number_format($venta_combustible[0]['glp_canti'], 2);
					$TVCombustible 	       = $venta_combustible[0]['liquido'] + $venta_combustible[0]['glp']; //TVCombustible: Total Venta Combustible
					$TVCombustible_canti 	 = $venta_combustible[0]['liquido_canti'] + $venta_combustible[0]['glp_canti']; //TVCombustible: Total Venta Combustible CANTIDAD
					$total_venta_combustible = number_format($TVCombustible, 2);
					$total_canti_combustible = number_format($TVCombustible_canti, 2);					

					//2. Venta de Productos y Promociones
					$venta_tienda = $dataRemoteStations['2_venta_productos_promociones'];
					$VT = $venta_tienda[0]['ventatienda']; //VT: Venta Tienda
					$CT = $venta_tienda[0]['cantienda']; //CT: Cantidad Tienda
					$venta_de_tienda = number_format($VT, 2);
					$canti_de_tienda = number_format($CT, 2);
					
					//2. Venta de Productos y Promociones - Detalle
					$venta_tienda_detalle = $dataRemoteStations['2_venta_productos_promociones_detalle'];

					//Total Venta (1+2)
					$TVC = $TVCombustible_canti + $CT; //TVC: Total Venta Cantidad
					$TV  = $TVCombustible + $VT; //TV: Total Venta
					$total_venta = number_format($TV, 2);
					$total_canti = number_format($TVC, 2);

					//3. Credito Clientes
					$vales_credito_detalle = $dataRemoteStations['3_vales_credito_detalle'];
					$val_can = 0; 
					$val_imp = 0; 
					foreach($vales_credito_detalle as $val) {
						$val_can = $val_can + $val['cantidad']; 
						$val_imp = $val_imp + $val['importe'];
					}

					//4. Tarjetas de Credito
					$tarjetas_credito_detalle = $dataRemoteStations['4_tarjetas_credito_detalle'];					
					$val_importetarjeta = 0; 
					foreach($tarjetas_credito_detalle as $t) {
						$val_importetarjeta = $val_importetarjeta + $t['importetarjeta']; 						
					}

					//5. Descuentos
					$descuentos       = $dataRemoteStations['5_descuentos'];
					$descuentos_total = number_format(abs($descuentos[0]['descuentos']),2);					

					//6. Diferencia de Precio de Vales
					$difprecio       = $dataRemoteStations['6_diferencias_precio_vales'];
					$difprecio_total = number_format($difprecio[0]['difprecio'],2);

					//7. Afericiones
					$afericiones       = $dataRemoteStations['7_afericiones'];
					$afericiones_total = number_format($afericiones[0]['afericiones'],2);
								
					//Total Venta Creditos y Otros No al Contado
					$TVCO                       = $val_imp+$val_importetarjeta+abs($descuentos[0]['descuentos'])+$difprecio[0]['difprecio']+$afericiones[0]['afericiones']; //TVCO: Total Venta Creditos y Otros
					$total_venta_creditos_otros = number_format($TVCO,2);

					//Total Efectivo en Boveda (Total Depositos POS)
					$depositos_pos       = $dataRemoteStations['total_depositos_pos'];
					$TDP                 = $depositos_pos[0]['depositospos']; //TDP: Total Depositos POS
					$total_depositos_pos = number_format($TDP,2);
					
					//Total Venta contado
					$TVContado = $TV - $TVCO;
					$a1=$TVContado; //TVContado: Total Venta Contado
					$total_venta_contado = number_format($TVContado,2);

					//8. Sobrantes Faltantes por Trabajador
					$dif_trabajadores = $dataRemoteStations['8_sobrantes_faltantes_por_trabajador'];
					$importe_sobfaltrab = 0;
					$sumsobfal = 0;
					$a2 = 0;
					foreach($dif_trabajadores as $d){
						$importe_sobfaltrab = $importe_sobfaltrab  + $d['importe'];
						$sumsobfal = $sumsobfal + $d['importe'];
		  				$a2=$sumsobfal;
					}

					//Diferencia Diaria
					$DD =  $TDP - $importe_sobfaltrab - $TVContado;  //DD: Diferencia Diaria: TOTAL DEPOSITOS - DIFERENCIA TRABAJADOR - VENTA CONTADO - OTROS GASTOS
					$diferencia_diaria = number_format($DD,2);

					//10.1 Ingresos al contado del dia 
					$caja_ingresos_contado_dia = $dataRemoteStations['10_1_ingresos_contado_dia'];
					$val_igre = 0; 
					$a3_1 = 0;
					foreach($caja_ingresos_contado_dia as $igre) {
						$val_igre = $val_igre + $igre['ingresos'];
						$a3_1 = $a3_1 + $igre['ingresos'];
					}

					//10.2 Cobranzas y Amortizaciones por CC
					$caja_ingresos_cobranzas = $dataRemoteStations['10_2_ingresos_cobranzas_amortizaciones_por_cc'];
					$val_igre_cc = 0; 
					foreach($caja_ingresos_cobranzas as $igre) {
						$val_igre_cc = $val_igre_cc + $igre['ingresos'];
					}

					//12. Egresos
					$caja_egresos = $dataRemoteStations['12_egresos'];
					$val_egre = 0; 
					$a5 = 0;
					foreach($caja_egresos as $egre) {
						$val_egre = $val_egre + $egre['egresos'];		
						$a5=$val_egre;
					}

					//13. Documentos de Venta Manual - Total
					$totmanuales    = $dataRemoteStations['13_documento_venta_manual_total'];
					$total_manuales = number_format($totmanuales[0]['total'],2);

					//13. Documentos de Venta Manual - Detalle
					$manuales = $dataRemoteStations['13_documento_venta_manual_detalle'];

					//14. Saldo Neto a Depositar 
					$calculo=( ($a1+$a2) - ($a3_1) ) - $a5; //ESTO QUEDA
					$calculo = number_format($calculo,2);

					//15. Saldo acumulado Caja y Banco
					$saldo_acumulado_caja_banco = $dataRemoteStations['15_saldo_acumulado_caja_banco'][0];
					$saldo_acumulado_caja_banco = number_format($saldo_acumulado_caja_banco,2);

					//1. Venta Combustible
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '1. Venta Combustible');					
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $total_canti_combustible);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $total_venta_combustible);
					// $this->calc->getActiveSheet()->mergeCells('A'.$row.':C'.$row);				
					$row++;

					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.'1.1 Liquido');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $liquido_canti);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $liquido);
					$row++;

					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.'1.2 GLP');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $glp_canti);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $glp);
					$row++;

					// $this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Venta Combustible');
					// $this->calc->getActiveSheet()->setCellValue('B'.$row, $total_canti_combustible);
					// $this->calc->getActiveSheet()->setCellValue('C'.$row, $total_venta_combustible);
					// $this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					// $row++;
					$row++;

					//2. Venta de Productos y Promociones
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '2. Venta de Productos y Promociones');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $canti_de_tienda);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $venta_de_tienda);
					$row++;

					//2. Venta de Productos y Promociones - Detalle
					foreach($venta_tienda_detalle as $v) {
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$v['linea'] . " - " . $v['descripcion_linea']);
						$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format($v['cantidad'],2));
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($v['importe'],2));						
						$row++;
					}
					$row++;

					//Total Venta (1+2)
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Venta');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $total_canti);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $total_venta);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					$row++;
					$row++;

					//3. Credito Clientes
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '3. Credito Clientes');					
					$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format($val_can,2));
					$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($val_imp,2));
					// $this->calc->getActiveSheet()->mergeCells('A'.$row.':C'.$row);				
					$row++;
					
					foreach($vales_credito_detalle as $val) {
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$val['cliente']);
						$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format($val['cantidad'],2));
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($val['importe'],2));						
						$row++;
					}

					// $this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Credito Clientes');
					// $this->calc->getActiveSheet()->setCellValue('B'.$row, number_format($val_can,2));
					// $this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($val_imp,2));
					// $this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					// $row++;
					$row++;

					//4. Tarjetas de Credito
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '4. Tarjetas de Credito');		
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($val_importetarjeta,2));			
					// $this->calc->getActiveSheet()->mergeCells('A'.$row.':C'.$row);				
					$row++;

					foreach($tarjetas_credito_detalle as $t) {
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$t['descripciontarjeta']);
						$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($t['importetarjeta'],2));						
						$row++;
					}

					// $this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Credito Clientes');
					// $this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					// $this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($val_importetarjeta,2));
					// $this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					// $row++;
					$row++;

					//5. Descuentos
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '5. Descuentos');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $descuentos_total);					
					$row++;
					
					//6. Diferencias de Precio de Vales
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '6. Diferencias de Precio de Vales');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $difprecio_total);					
					$row++;

					//7. Afericiones
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '7. Afericiones');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $afericiones_total);					
					$row++;
					$row++;

					//Total Venta Creditos y Otros No al Contado
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Venta Creditos y Otros No al Contado');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $total_venta_creditos_otros);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					$row++;
					
					//Total Efectivo en Boveda (Total Depositos POS)
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Efectivo en Boveda (Total Depositos POS)');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $total_depositos_pos);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					$row++;

					//Total Venta contado
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Venta Contado');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $total_venta_contado);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					$row++;
					$row++;

					//8. Sobrantes Faltantes por Trabajador
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '8. Sobrantes Faltantes por Trabajador');					
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($sumsobfal,2));
					// $this->calc->getActiveSheet()->mergeCells('A'.$row.':C'.$row);				
					$row++;

					foreach($dif_trabajadores as $d){
						if($d['flag']=='0'){
							$sob_descripcion = "AUTO";
						}else{
							$sob_descripcion = "MANUAL";
						}
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$d['nom_trabajador']);						
						$this->calc->getActiveSheet()->setCellValue('B'.$row, $sob_descripcion);
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($d['importe'],2));						
						$row++;
					}

					// $this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Total Sobrantes y Faltantes por Trabajador');
					// $this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					// $this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($sumsobfal,2));
					// $this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					// $row++;
					$row++;

					//Diferencia diaria
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.'Diferencia Diaria');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $diferencia_diaria);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':C'.$row)->getFont()->setBold(true);	
					$row++;
					$row++;

					//10 Ingresos
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '10. Ingresos');					
					$this->calc->getActiveSheet()->mergeCells('A'.$row.':C'.$row);				
					$row++;

					//10.1 Ingresos al contado del dia 
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.'10.1 Ingresos al contado del dia');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($val_igre,2));					
					// $this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);	
					$row++;

					foreach($caja_ingresos_contado_dia as $igre) {
						$banco = ($igre['c_cash_mpayment_id'] == 1 || TRIM($igre['metodo_pago']) == "DEPOSITO BANCARIO") ? " - " . htmlentities($igre['banco']) : "";
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.$igre['documento'].$banco);
						$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($igre['ingresos'],2));						
						$row++;
					}					

					//10.2 Cobranzas y Amortizaciones por CC
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.'10.2 Cobranzas y Amortizaciones por CC');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($val_igre_cc,2));			
					// $this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);			
					$row++;

					foreach($caja_ingresos_cobranzas as $igre) {						
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$tab.$igre['documento']);
						$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($igre['ingresos'],2));						
						$row++;
					}					
					$row++;

					//12. Egresos
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '12. Egresos');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $val_egre);		
					// $this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);				
					$row++;

					foreach($caja_egresos as $egre) {
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$egre['documento']);
						$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($egre['egresos'],2));						
						$row++;
					}					
					$row++;

					//13. Documentos de Venta Manual
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '13. Documentos de Venta Manual');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $total_manuales);		
					// $this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);				
					$row++;

					foreach($manuales as $m) {
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $tab.$m['documento']);
						$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
						$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($m['importe'],2));						
						$row++;
					}					
					$row++;

					//14. Saldo Neto a Depositar 
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '14. Saldo Neto a Depositar');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $calculo);		
					$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);				
					$row++;

					//15. Saldo acumulado Caja y Banco
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '15. Saldo acumulado Caja y Banco');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $tab);
					$this->calc->getActiveSheet()->setCellValue('C'.$row, $saldo_acumulado_caja_banco);		
					$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);				
					$row++;
					$row++;					
					//CERRAR DATOS PARA MOSTRAR EN EXCEL					

					//INVENTARIO COMBUSTIBLE
					$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('12');
					$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('12');
					$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('12');
					$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('12');
					$this->calc->getActiveSheet()->getColumnDimension('H')->setWidth('12');
					$this->calc->getActiveSheet()->getColumnDimension('I')->setWidth('12');
					$this->calc->getActiveSheet()->getColumnDimension('J')->setWidth('12');
					$this->calc->getActiveSheet()->getColumnDimension('K')->setWidth('12');
					$this->calc->getActiveSheet()->getStyle('D:K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);	
					$this->calc->getActiveSheet()->getStyle('D:K')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);	

					$this->calc->getActiveSheet()->setCellValue('A'.$row, 'COMBUSTIBLES');
					$this->calc->getActiveSheet()->mergeCells('A'.$row.':K'.$row);				
					$this->calc->getActiveSheet()->getStyle('A'.$row.':K'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '7952b3')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 11,
								//'name'  => 'Verdana'
							)
						)
					);
					$row++;

					$this->calc->getActiveSheet()->setCellValue('A'.$row, 'PRODUCTO');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, 'STOCK INICIAL');
					$this->calc->getActiveSheet()->setCellValue('C'.$row, 'COMPRAS');
					$this->calc->getActiveSheet()->setCellValue('D'.$row, 'VENTAS');
					$this->calc->getActiveSheet()->setCellValue('E'.$row, '%');
					$this->calc->getActiveSheet()->setCellValue('F'.$row, 'TRANSFERENCIAS');
					$this->calc->getActiveSheet()->setCellValue('G'.$row, 'STOCK FINAL');
					$this->calc->getActiveSheet()->setCellValue('H'.$row, 'MEDICION');
					$this->calc->getActiveSheet()->setCellValue('I'.$row, 'DIF. DIA');
					$this->calc->getActiveSheet()->setCellValue('J'.$row, 'DIF. MES');
					$this->calc->getActiveSheet()->setCellValue('K'.$row, 'IMPORTE VENTA');
					$this->calc->getActiveSheet()->getStyle('A'.$row.':K'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '7952b3')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 11,
								//'name'  => 'Verdana'
							)
						)
					);
					$row++;

					//Obtenemos contenido para Inventario de Combustible
					$results1 = $dataRemoteStations['inventario_combustible'];
					
					foreach($results1['propiedades'] as $a => $almacenes) {
						foreach($almacenes['almacenes'] as $ch_almacen=>$venta) {
							//Si no es GLP		
							foreach($venta['partes'] as $dt_producto=>$producto) {
								/**
								 * dt_producto es el indice del array y esta formado por Nombre del Articulo y Codigo del Articulo (90 OCT|11620302)
								 * Obtenemos el codigo del articulo en dt_codigo
								 */
								$porciones = explode("|", $dt_producto);
								$dt_codigo = $porciones['1'];

								if ($dt_codigo != '11620307'){ //Si no es GLP									
			    					$array = $producto;
									$label = $dt_codigo;
									$totalventa = $venta['totales']['total']['ventas'];

									$this->calc->getActiveSheet()->setCellValue('A'.$row, $array['producto']);
									$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format($array['inicial'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($array['compras'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format($array['ventas'], 2, '.', ','));

									/* Si no es el total ni GLP hallamos el porcentaje de c/producto con respecto al total */
									if ($label != "Total" && $label != "11620307") {
										$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format($array['porcentaje']/$totalventa, 2, '.', ','));										
									} else {
										$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format($array['porcentaje'], 0, '.', ','));																				
									}

									$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format($array['transfe'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format($array['final'] + $array['transfe'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format($array['medicion'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format($array['dia'] + $array['transfesalida'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format($array['mes'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('K'.$row, number_format($array['importe'], 2, '.', ','));									
									$row++;
								}
							}	
							
							//Totales de combustibles que no es GLP
							$array = $venta['totales']['total'];
							$label = "Total";
							$totalventa = "";

							$this->calc->getActiveSheet()->setCellValue('A'.$row, $array['producto']);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format($array['inicial'], 2, '.', ','));
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($array['compras'], 2, '.', ','));
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format($array['ventas'], 2, '.', ','));

							/* Si no es el total ni GLP hallamos el porcentaje de c/producto con respecto al total */
							if ($label != "Total" && $label != "11620307") {
								$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format($array['porcentaje']/$totalventa, 2, '.', ','));										
							} else {
								$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format($array['porcentaje'], 0, '.', ','));																				
							}

							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format($array['transfe'], 2, '.', ','));
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format($array['final'] + $array['transfe'], 2, '.', ','));
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format($array['medicion'], 2, '.', ','));
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format($array['dia'] + $array['transfesalida'], 2, '.', ','));
							$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format($array['mes'], 2, '.', ','));
							$this->calc->getActiveSheet()->setCellValue('K'.$row, number_format($array['importe'], 2, '.', ','));
							$row++;
							$row++;

							//Es GLP
							foreach($venta['partes'] as $dt_producto=>$producto) {
								/**
								 * dt_producto es el indice del array y esta formado por Nombre del Articulo y Codigo del Articulo (90 OCT|11620302)
								 * Obtenemos el codigo del articulo en dt_codigo
								 */
								$porciones = explode("|", $dt_producto);
								$dt_codigo = $porciones['1'];

		            		if ($dt_codigo == '11620307'){ //Si es GLP									
									$array = $producto;
									$label = $dt_codigo;
									$totalventa = "";

									$this->calc->getActiveSheet()->setCellValue('A'.$row, $array['producto']);
									$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format($array['inicial'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format($array['compras'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format($array['ventas'], 2, '.', ','));

									/* Si no es el total ni GLP hallamos el porcentaje de c/producto con respecto al total */
									if ($label != "Total" && $label != "11620307") {
										$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format($array['porcentaje']/$totalventa, 2, '.', ','));										
									} else {
										$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format($array['porcentaje'], 0, '.', ','));																				
									}

									$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format($array['transfe'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format($array['final'] + $array['transfe'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format($array['medicion'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format($array['dia'] + $array['transfesalida'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format($array['mes'], 2, '.', ','));
									$this->calc->getActiveSheet()->setCellValue('K'.$row, number_format($array['importe'], 2, '.', ','));									
									$row++;
								}
							}
						}
					}
					//Cerrar Obtenemos contenido para Inventario de Combustible

					$row++;
					$row++;
					//CERRAR INVENTARIO COMBUSTIBLE
				}				

				//GENERACION EXCEL
				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache

				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
				//CERRAR GENERACION EXCEL
			}
		}
	}

	public function resumeSaldoSocio()
	{
		ini_set('memory_limit','-1');

		$msg = getMemory(array(''));
		if(checkSession()) {
			$return = array();
			// echo '3: '.$this->uri->segment(3).', 4: '.$this->uri->segment(4).', 5: '.$this->uri->segment(5).', 6: '.$this->uri->segment(6);
			error_log(  json_encode( $this->uri ) );
			// exit;
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null && $this->uri->segment(6) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(6);
				$socios = $this->uri->segment(7) == 'a' ? '' : $this->uri->segment(7);
				$vales = $this->uri->segment(8);
				$vista = $this->uri->segment(9);	

				$typeStationDesc = getDescriptionTypeStation($typeStation);

				$mod = '';
				$typeEst = '';
				if($typeStation == 8) {
					$mod = 'TOTALS_SALDO_SOCIO';
					$typeEst = 'saldoSocio';
					$titleDocument = 'Saldo Pendiente de Socio';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$dateBegin = $this->uri->segment(4);
				$dateEnd = $this->uri->segment(5);

				/*Obtenemos fecha en formato correcto*/
				$porcionesDateBegin = explode("-", $dateBegin);
				$dateBegin_ = $porcionesDateBegin[0] . "/" . $porcionesDateBegin[1] . "/" . $porcionesDateBegin[2];
				$porcionesDateEnd = explode("-", $dateEnd);
				$dateEnd_ = $porcionesDateEnd[0] . "/" . $porcionesDateEnd[1] . "/" . $porcionesDateEnd[2];
				/*Cerrar Obtenemos fecha en formato correcto*/

				/* Obtenemos Codigos de Socios */
				$socios_ = explode('-',$socios);
				$codigos_socios = implode("|", $socios_);
				error_log("Codigos de Socios");
				error_log(json_encode($socios_));
				error_log(json_encode($codigos_socios));
				/* Cerrar */

				$formatDateBegin = formatDateCentralizer($dateBegin,2);
				$formatDateEnd = formatDateCentralizer($dateEnd,2);

				$qty_sale = 0;
				$type_cost = 0;


				$totalQty = 0;
				$totalSale = 0;

				$this->load->model('COrg_model');
				$isAllStations = true;
				$stationsIdSelectMultiple = explode('-',$id);
				error_log("IDs de las estaciones seleccionadas en select multiple");
				error_log(json_encode($stationsIdSelectMultiple));
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndIdSelectMultiple($typeStationDesc == 'MP' ? 'C' : $typeStationDesc,$stationsIdSelectMultiple);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStationDesc == 'MP' ? 'C' : $typeStationDesc);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));
				
				$index = 0;
			
				/*
				//load our new PHPExcel library
				$this->load->library('calc');

				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:F1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fechas');
				$this->calc->getActiveSheet()->setCellValue('B3', '-');
				$this->calc->getActiveSheet()->setCellValue('C3', $dateEnd);
	
				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');
	
				//$this->calc->getActiveSheet()->setCellValue('B4', $msg['memory']);
			
				$row = 7;

				//Formatemos tamaño de columnas
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('24');
				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('16');
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('16');
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('16');
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('16');
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('16');
				$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('16');
				$this->calc->getActiveSheet()->getColumnDimension('H')->setWidth('16');
				$this->calc->getActiveSheet()->getColumnDimension('I')->setWidth('16');
				//Cerrar Formateamos tamaño de columnas
				*/

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&desde='.$dateBegin_.'&hasta='.$dateEnd_.'&socios='.$codigos_socios.'&vales='.$vales.'&vista='.$vista;
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$data = array();
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					//GENERAMOS ARRAY DE LA INFORMACION DE CUENTAS POR COBRAR Y VALES POR CLIENTE
					if($dataRemoteStations != false) {							
						error_log("****");
						error_log(json_encode($dataRemoteStations));

						foreach ($dataRemoteStations["1_cuentas_por_cobrar"] as $key => $cuenta) {
							$data["1_cuentas_por_cobrar"][ TRIM($cuenta['cliente']) . " - " . TRIM($cuenta['razonsocial']) ]['cuentas_por_cobrar'][] = $cuenta;
						}
						foreach ($dataRemoteStations["2_vales"] as $key => $vale) {
							$data["2_vales"][ TRIM($vale['cliente']) . " - " . TRIM($vale['razonsocial']) ]['vales'][] = $vale;
						}

						//POR SI FUERA NECESARIO SE REALIZO ESTO
						$data["1_cuentas_por_cobrar"] = empty($data["1_cuentas_por_cobrar"]) ? array() : $data["1_cuentas_por_cobrar"];
						$data["2_vales"]              = empty($data["2_vales"])              ? array() : $data["2_vales"];
						$data["cuentas_vales"] = array_merge_recursive( $data["1_cuentas_por_cobrar"], $data["2_vales"] );
					}else{
						//NO HACE NADA
					}

					//load our new PHPExcel library
					$this->load->library('calc');

					$this->calc->createSheet($index);//creamos la pestaña
					$this->calc->setActiveSheetIndex($index);//seteamos pestaña
					$this->calc->getActiveSheet()->setTitle($dataStation->name);
					$this->calc->getActiveSheet()->setCellValue('A1', appName());
					$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
					$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('A1:F1');
					$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

					$this->calc->getActiveSheet()->setCellValue('A3', 'Fechas');
					$this->calc->getActiveSheet()->setCellValue('B3', '-');
					$this->calc->getActiveSheet()->setCellValue('C3', $dateEnd);
		
					$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');
		
					//$this->calc->getActiveSheet()->setCellValue('B4', $msg['memory']);
				
					$row = 7;

					//Formatemos tamaño de columnas
					$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('24');
					$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('18'); //16
					$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('18'); //16
					$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('18'); //16
					$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('18'); //16
					$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('18'); //16
					$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('18'); //16
					$this->calc->getActiveSheet()->getColumnDimension('H')->setWidth('18'); //16
					$this->calc->getActiveSheet()->getColumnDimension('I')->setWidth('18'); //16
					//Cerrar Formateamos tamaño de columnas

					//DATOS PARA MOSTRAR EN EXCEL
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->client_name);
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $dataStation->name);
					$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
					// $this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
					$this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(16);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '7952b3')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 12,
								//'name'  => 'Verdana'
							)
						)
					);
					$row++;

					/**************************************************************** REPORTE CUENTAS POR COBRAR Y VALES ****************************************************************/
					//Inicio de cabecera (tabla)
					$this->calc->getActiveSheet()->setCellValue('A'.$row, '');
					$this->calc->getActiveSheet()->setCellValue('F'.$row, 'IMPORTE TOTAL');
					$this->calc->getActiveSheet()->setCellValue('H'.$row, 'SALDO');
					
					$this->calc->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
					$this->calc->getActiveSheet()->mergeCells('F'.$row.':G'.$row);
					$this->calc->getActiveSheet()->mergeCells('H'.$row.':I'.$row);
					$this->calc->getActiveSheet()->getStyle('F'.$row.':H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '337ab7')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 12,
								//'name'  => 'Verdana'
							)
						)
					);
					$row++;

					$this->calc->getActiveSheet()->setCellValue('A'.$row, 'DOCUMENTO');
					$this->calc->getActiveSheet()->setCellValue('B'.$row, 'F.EMISION');
					$this->calc->getActiveSheet()->setCellValue('C'.$row, 'F.VENCIMIENTO');
					$this->calc->getActiveSheet()->setCellValue('D'.$row, 'MONEDA');
					$this->calc->getActiveSheet()->setCellValue('E'.$row, 'T.CAMBIO');
					$this->calc->getActiveSheet()->setCellValue('F'.$row, 'DOLARES');
					$this->calc->getActiveSheet()->setCellValue('G'.$row, 'SOLES');
					$this->calc->getActiveSheet()->setCellValue('H'.$row, 'DOLARES');
					$this->calc->getActiveSheet()->setCellValue('I'.$row, 'SOLES');
					
					$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
					
					$this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(16);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => '337ab7')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 12,
								//'name'  => 'Verdana'
							)
						)
					);				
					$row++;		
					//Fin de cabecera (tabla)

					//VISTA
					$vista_documentos;
					$vista_vales;
					if($vista == "DETDOC_RESVAL"){
						$vista_documentos = true;
						$vista_vales = false;
					}else if($vista == "DET"){
						$vista_documentos = true;
						$vista_vales = true;
					}else if($vista == "RES"){
						$vista_documentos = false;
						$vista_vales = false;
					}

					$verificar_data = $data;

					//VARIABLES PARA SUMAR TOTALES  CUENTAS POR COBRAR	
					$sumTotalInicialSoles = 0.00;
					$sumTotalPagoSoles = 0.00;
					$sumTotalSaldoSoles = 0.00;

					$sumTotalInicialDolares = 0.00;
					$sumTotalPagoDolares = 0.00;
					$sumTotalSaldoDolares = 0.00;
					
					//VARIABLES PARA SUMAR TOTALES GENERALES  CUENTAS POR COBRAR
					$sumTotalGeneralInicialSoles = 0.00;
					$sumTotalGeneralPagoSoles = 0.00;
					$sumTotalGeneralSaldoSoles = 0.00;

					$sumTotalGeneralInicialDolares = 0.00;
					$sumTotalGeneralPagoDolares = 0.00;
					$sumTotalGeneralSaldoDolares = 0.00;

					//VARIABLES PARA SUMAR TOTALES VALES
					$sumTotalImporteVales = 0.00;

					//VARIABLES PARA SUMAR TOTALES GENERALES VALES
					$sumTotalGeneralImporteVales = 0.00;

					//OBTENEMOS CUENTAS POR COBRAR Y VALES
					$dataCuentasVales = $verificar_data["cuentas_vales"];

					//RECORREMOS CUENTAS POR COBRAR Y VALES
					foreach ($dataCuentasVales as $key => $value) {
						//LIMPIAMOS TOTALES POR CLIENTE
						$sumTotalInicialSoles = 0.00;
						$sumTotalPagoSoles = 0.00;
						$sumTotalSaldoSoles = 0.00;

						$sumTotalInicialDolares = 0.00;
						$sumTotalPagoDolares = 0.00;
						$sumTotalSaldoDolares = 0.00;

						//MOSTRAMOS NOMBRE DE CADA CLIENTE
						$this->calc->getActiveSheet()->setCellValue('A'.$row, $key);
						$this->calc->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
						$this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(16);
						$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'FF7F32')
								),
								'font' => array(
									'bold'  => true,
									'color' => array('rgb' => 'FFFFFF'),
									'size'  => 12,
									//'name'  => 'Verdana'
								)
							)
						);
						$row++;

						//OBTENEMOS CLIENTES
						$dataClientesCuentas = $dataCuentasVales[$key]['cuentas_por_cobrar'];
						$dataClientesVales = $dataCuentasVales[$key]['vales'];

						//RECORREMOS CLIENTES
						foreach ($dataClientesCuentas as $key2 => $value2) {
							$elemento = $dataClientesCuentas[$key2];
							if($vista_documentos == true){
								$this->calc->getActiveSheet()->setCellValue('A'.$row, $elemento['documento']);
								$this->calc->getActiveSheet()->setCellValue('B'.$row, $elemento['fechaemision']);
								$this->calc->getActiveSheet()->setCellValue('C'.$row, $elemento['fechavencimiento']);
								$this->calc->getActiveSheet()->setCellValue('D'.$row, $elemento['moneda']);
								$this->calc->getActiveSheet()->setCellValue('E'.$row, $elemento['tipocambio']);
								$this->calc->getActiveSheet()->setCellValue('F'.$row, $elemento['importeinicial_dolares']);
								$this->calc->getActiveSheet()->setCellValue('G'.$row, $elemento['importeinicial_soles']);
								$this->calc->getActiveSheet()->setCellValue('H'.$row, $elemento['saldo_dolares']);
								$this->calc->getActiveSheet()->setCellValue('I'.$row, $elemento['saldo_soles']);
								
								$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$row++;
							}

							//SUMAMOS TOTALES POR CLIENTE
							if($elemento['moneda'] == 'S/'){ //SOLES
								if($elemento['tipodocumento'] == '20' || $elemento['tipodocumento'] == '21'){ //ES NC Y ANTICIPO
									$sumTotalInicialSoles 	-= $elemento['importeinicial_soles'];
									$sumTotalPagoSoles 		-= $elemento['pago_soles'];
									$sumTotalSaldoSoles 	-= $elemento['saldo_soles'];
								}else{ //OTROS DOCUMENTOS
									$sumTotalInicialSoles 	+= $elemento['importeinicial_soles'];
									$sumTotalPagoSoles 		+= $elemento['pago_soles'];
									$sumTotalSaldoSoles     += $elemento['saldo_soles'];
								}
							} else { //DOLARES
								if($elemento['tipodocumento'] == "20" || $elemento['tipodocumento'] == '21'){ //ES NC Y ANTICIPO
									$sumTotalInicialDolares -= $elemento["importeinicial_dolares"];
									$sumTotalPagoDolares 	-= $elemento["pago_dolares"];
									$sumTotalSaldoDolares 	-= $elemento["saldo_dolares"];
								}else{ //OTROS DOCUMENTOS
									$sumTotalInicialDolares += $elemento["importeinicial_dolares"];
									$sumTotalPagoDolares 	+= $elemento["pago_dolares"];
									$sumTotalSaldoDolares 	+= $elemento["saldo_dolares"];
								}
							}

							//SUMAMOS TOTALES GENERALES POR CLIENTE
							if($elemento['moneda'] == 'S/'){ //SOLES
								if($elemento['tipodocumento'] == '20' || $elemento['tipodocumento'] == '21'){ //ES NC Y ANTICIPO
									$sumTotalGeneralInicialSoles 	-= $elemento['importeinicial_soles'];
									$sumTotalGeneralPagoSoles 		-= $elemento['pago_soles'];
									$sumTotalGeneralSaldoSoles 		-= $elemento['saldo_soles'];
								}else{ //OTROS DOCUMENTOS
									$sumTotalGeneralInicialSoles 	+= $elemento['importeinicial_soles'];
									$sumTotalGeneralPagoSoles 		+= $elemento['pago_soles'];
									$sumTotalGeneralSaldoSoles    	+= $elemento['saldo_soles'];
								}
							} else { //DOLARES
								if($elemento['tipodocumento'] == "20" || $elemento['tipodocumento'] == '21'){ //ES NC Y ANTICIPO
									$sumTotalGeneralInicialDolares 	-= $elemento["importeinicial_dolares"];
									$sumTotalGeneralPagoDolares 	-= $elemento["pago_dolares"];
									$sumTotalGeneralSaldoDolares 	-= $elemento["saldo_dolares"];
								}else{ //OTROS DOCUMENTOS
									$sumTotalGeneralInicialDolares 	+= $elemento["importeinicial_dolares"];
									$sumTotalGeneralPagoDolares 	+= $elemento["pago_dolares"];
									$sumTotalGeneralSaldoDolares 	+= $elemento["saldo_dolares"];
								}
							}
						}

						//MOSTRAMOS TOTALES POR CLIENTES
						$this->calc->getActiveSheet()->setCellValue('E'.$row, "TOTAL DOCUMENTOS");
						$this->calc->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->calc->getActiveSheet()->setCellValue('F'.$row, $sumTotalInicialDolares);
						$this->calc->getActiveSheet()->setCellValue('G'.$row, $sumTotalInicialSoles);
						$this->calc->getActiveSheet()->setCellValue('H'.$row, $sumTotalSaldoDolares);
						$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalSaldoSoles);						
						$this->calc->getActiveSheet()->getStyle('F'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F8CBAD')
								),
								'font' => array(
									'bold'  => true,
									'color' => array('rgb' => '000'),
									'size'  => 11,
									//'name'  => 'Verdana'
								)
							)
						);
						$row++;

						//LIMPIAMOS TOTALES POR VALES
						$sumTotalImporteVales = 0.00;

						if($vales == 1){
							//RECORREMOS CLIENTES
							foreach ($dataClientesVales as $key4 => $value4) {
								$elemento = $dataClientesVales[$key4];
								if($vista_vales == true){
									$this->calc->getActiveSheet()->setCellValue('A'.$row, $elemento['documentoval']);
									$this->calc->getActiveSheet()->setCellValue('B'.$row, $elemento['fecha']);
									$this->calc->getActiveSheet()->setCellValue('C'.$row, "-");
									$this->calc->getActiveSheet()->setCellValue('D'.$row, "S/.");
									$this->calc->getActiveSheet()->setCellValue('E'.$row, "-");
									$this->calc->getActiveSheet()->setCellValue('F'.$row, "-");
									$this->calc->getActiveSheet()->setCellValue('G'.$row, $elemento['importeval']);								
									$this->calc->getActiveSheet()->setCellValue('H'.$row, "-");
									$this->calc->getActiveSheet()->setCellValue('I'.$row, $elemento['importeval']);
									
									$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$row++;
								}

								$sumTotalImporteVales += $elemento['importeval'];
								$sumTotalGeneralImporteVales += $elemento['importeval'];
							}

							//TOTALIZAMOS VALES
							$this->calc->getActiveSheet()->setCellValue('E'.$row, "TOTAL VALES");
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);												
							$this->calc->getActiveSheet()->setCellValue('F'.$row, "-");
							$this->calc->getActiveSheet()->setCellValue('G'.$row, $sumTotalImporteVales);
							$this->calc->getActiveSheet()->setCellValue('H'.$row, "-");
							$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalImporteVales);
							$this->calc->getActiveSheet()->getStyle('F'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
								array(
									'fill' => array(
										'type' => PHPExcel_Style_Fill::FILL_SOLID,
										'color' => array('rgb' => 'F8CBAD')
									),
									'font' => array(
										'bold'  => true,
										'color' => array('rgb' => '000'),
										'size'  => 11,
										//'name'  => 'Verdana'
									)
								)
							);
							$row++;
						}

						//TOTALIZAMOS CLIENTES
						$this->calc->getActiveSheet()->setCellValue('E'.$row, "TOTAL CLIENTES");
						$this->calc->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);												
						$this->calc->getActiveSheet()->setCellValue('F'.$row, $sumTotalInicialDolares);
						$this->calc->getActiveSheet()->setCellValue('G'.$row, $sumTotalInicialSoles + $sumTotalImporteVales);
						$this->calc->getActiveSheet()->setCellValue('H'.$row, $sumTotalSaldoDolares);
						$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalSaldoSoles + $sumTotalImporteVales);
						$this->calc->getActiveSheet()->getStyle('F'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'F8CBAD')
								),
								'font' => array(
									'bold'  => true,
									'color' => array('rgb' => '000'),
									'size'  => 11,
									//'name'  => 'Verdana'
								)
							)
						);
						$row++;
						$row++;
						$row++;
					}

					//MOSTRAMOS TOTALES GENERALES POR DOCUMENTOS
					$this->calc->getActiveSheet()->setCellValue('E'.$row, "TOTAL DOCUMENTOS GENERAL");
					$this->calc->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$this->calc->getActiveSheet()->setCellValue('F'.$row, $sumTotalGeneralInicialDolares);
					$this->calc->getActiveSheet()->setCellValue('G'.$row, $sumTotalGeneralInicialSoles);
					$this->calc->getActiveSheet()->setCellValue('H'.$row, $sumTotalGeneralSaldoDolares);
					$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalGeneralSaldoSoles);						
					$this->calc->getActiveSheet()->getStyle('F'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'FF7F32')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 12,
								//'name'  => 'Verdana'
							)
						)
					);
					$row++;

					if($vales == 1){
						//MOSTRAMOS TOTALES GENERALES POR VALES
						$this->calc->getActiveSheet()->setCellValue('E'.$row, "TOTAL VALES GENERAL");
						$this->calc->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->calc->getActiveSheet()->setCellValue('F'.$row, "-");
						$this->calc->getActiveSheet()->setCellValue('G'.$row, $sumTotalGeneralImporteVales);
						$this->calc->getActiveSheet()->setCellValue('H'.$row, "-");
						$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalGeneralImporteVales);						
						$this->calc->getActiveSheet()->getStyle('F'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => 'FF7F32')
								),
								'font' => array(
									'bold'  => true,
									'color' => array('rgb' => 'FFFFFF'),
									'size'  => 12,
									//'name'  => 'Verdana'
								)
							)
						);
						$row++;
					}

					//MOSTRAMOS TOTALES GENERALES POR CLIENTES
					$this->calc->getActiveSheet()->setCellValue('E'.$row, "TOTAL CLIENTES GENERAL");
					$this->calc->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$this->calc->getActiveSheet()->setCellValue('F'.$row, $sumTotalGeneralInicialDolares);
					$this->calc->getActiveSheet()->setCellValue('G'.$row, $sumTotalGeneralInicialSoles + $sumTotalGeneralImporteVales);
					$this->calc->getActiveSheet()->setCellValue('H'.$row, $sumTotalGeneralSaldoDolares);
					$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalGeneralSaldoSoles + $sumTotalGeneralImporteVales);						
					$this->calc->getActiveSheet()->getStyle('F'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->applyFromArray(
						array(
							'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'color' => array('rgb' => 'FF7F32')
							),
							'font' => array(
								'bold'  => true,
								'color' => array('rgb' => 'FFFFFF'),
								'size'  => 12,
								//'name'  => 'Verdana'
							)
						)
					);
					$row++;
					$row++;
					$row++;

					/**************************************************************** REPORTE VALES ****************************************************************/
					/*
					if($vales == 1){
						//Inicio de cabecera (tabla)
						$this->calc->getActiveSheet()->setCellValue('A'.$row, '');
						$this->calc->getActiveSheet()->setCellValue('G'.$row, 'NRO.VALES');
						$this->calc->getActiveSheet()->setCellValue('H'.$row, 'F.EMISION');
						$this->calc->getActiveSheet()->setCellValue('I'.$row, 'IMPORTE');
						
						$this->calc->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
						$this->calc->getActiveSheet()->getStyle('G'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
						
						$this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(16);
						$this->calc->getActiveSheet()->getStyle('G'.$row.':I'.$row)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => '337ab7')
								),
								'font' => array(
									'bold'  => true,
									'color' => array('rgb' => 'FFFFFF'),
									'size'  => 12,
									//'name'  => 'Verdana'
								)
							)
						);				
						$row++;		
						//Fin de cabecera (tabla)					

						$verificar_data = $data;

						//VARIABLES PARA SUMAR TOTALES		
						$sumTotalImporteVales = 0.00;

						//VARIABLES PARA SUMAR TOTALES GENERALES		
						$sumTotalGeneralImporteVales = 0.00;

						//OBTENEMOS CUENTAS POR COBRAR
						$dataVales = $verificar_data["2_vales"];

						//RECORREMOS VALES
						foreach ($dataVales as $key3 => $value3) {
							//LIMPIAMOS TOTALES POR VALES
							$sumTotalImporteVales = 0.00;

							//MOSTRAMOS NOMBRE DE CADA CLIENTE
							$this->calc->getActiveSheet()->setCellValue('A'.$row, '');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, $key3);
							$this->calc->getActiveSheet()->mergeCells('A'.$row.':E'.$row);
							$this->calc->getActiveSheet()->getRowDimension($row)->setRowHeight(16);
							$this->calc->getActiveSheet()->getStyle('G'.$row.':I'.$row)->applyFromArray(
								array(
									'fill' => array(
										'type' => PHPExcel_Style_Fill::FILL_SOLID,
										'color' => array('rgb' => '337ab7')
									),
									'font' => array(
										'bold'  => true,
										'color' => array('rgb' => 'FFFFFF'),
										'size'  => 12,
										//'name'  => 'Verdana'
									)
								)
							);
							$row++;

							//OBTENEMOS CLIENTES
							$dataClientes = $dataVales[$key3]['vales'];

							//RECORREMOS CLIENTES
							foreach ($dataClientes as $key4 => $value4) {
								$elemento = $dataClientes[$key4];
								if($vista == "DET"){
									$this->calc->getActiveSheet()->setCellValue('A'.$row, '');
									$this->calc->getActiveSheet()->setCellValue('G'.$row, $elemento['documentoval']);
									$this->calc->getActiveSheet()->setCellValue('H'.$row, $elemento['fecha']);
									$this->calc->getActiveSheet()->setCellValue('I'.$row, $elemento['importeval']);
									
									$this->calc->getActiveSheet()->getStyle('A'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$row++;
								}

								$sumTotalImporteVales += $elemento['importeval'];
								$sumTotalGeneralImporteVales += $elemento['importeval'];
							}

							//TOTALIZAMOS VALES
							$this->calc->getActiveSheet()->setCellValue('H'.$row, "TOTAL VALES");
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);												
							$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalImporteVales);						
							$this->calc->getActiveSheet()->getStyle('I'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->calc->getActiveSheet()->getStyle('H'.$row.':I'.$row)->applyFromArray(
								array(
									// 'fill' => array(
									// 	'type' => PHPExcel_Style_Fill::FILL_SOLID,
									// 	'color' => array('rgb' => 'fceec9')
									// ),
									'font' => array(
										'bold'  => true,
										'color' => array('rgb' => '000'),
										'size'  => 11,
										//'name'  => 'Verdana'
									)
								)
							);
							$row++;						
							$row++;
						}					

						//TOTALIZAMOS VALES GENERALES
						$this->calc->getActiveSheet()->setCellValue('H'.$row, "TOTAL VALES GENERAL");
						$this->calc->getActiveSheet()->getStyle('H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);												
						$this->calc->getActiveSheet()->setCellValue('I'.$row, $sumTotalGeneralImporteVales);						
						$this->calc->getActiveSheet()->getStyle('I'.$row.':I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->calc->getActiveSheet()->getStyle('G'.$row.':I'.$row)->applyFromArray(
							array(
								'fill' => array(
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb' => '337ab7')
								),
								'font' => array(
									'bold'  => true,
									'color' => array('rgb' => 'FFFFFF'),
									'size'  => 12,
									//'name'  => 'Verdana'
								)
							)
						);
						$row++;
						$row++;
					}
					*/
					$index++;
				}

				//GENERACION EXCEL
				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache

				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
				//CERRAR GENERACION EXCEL
			}
		}
	}

	public function resumeStock()
	{
		if(checkSession()) {
			$return = array();
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(5);

				$mod = '';
				$typeEst = '';
				if($typeStation == 0) {
					$mod = 'STOCK_COMB_R';//termpral para este reporte con serialize
					$typeEst = 'comb';
					$titleDocument = 'Stock de Combustibles';
				} else {
					$mod = 'STOCk_MARKET';//falta
					$typeEst = 'market';
					$titleDocument = 'Stock de Market';
				}

				$return['dateEnd'] = date('d/m/Y', strtotime(formatDateCentralizer($this->uri->segment(4),3). ' - 7 days'));

				$formatDateBegin = formatDateCentralizer($this->uri->segment(4),2);
				$formatDateEnd = formatDateCentralizer($return['dateEnd'],1);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 0 ? 'C' : 'M',$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 0 ? 'C' : 'M');
					}
				}

				//load our new PHPExcel library
				$this->load->library('calc');

				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:G1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fecha');
				$this->calc->getActiveSheet()->setCellValue('B3', $this->uri->segment(4));

				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');

				//Inicio de cabecera	(tabla)
				$this->calc->getActiveSheet()->setCellValue('A7', 'Producto');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				$this->calc->getActiveSheet()->setCellValue('B7', 'Capacidad');
				$this->calc->getActiveSheet()->setCellValue('C7', 'Inventario');
				$this->calc->getActiveSheet()->setCellValue('D7', 'Promedio Venta día');
				$this->calc->getActiveSheet()->setCellValue('E7', 'Tiempo Vaciar');
				$this->calc->getActiveSheet()->setCellValue('F7', 'Cant. ult. Compra');
				$this->calc->getActiveSheet()->setCellValue('G7', 'Fecha ult. Compra');
				$this->calc->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A7:G7')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera		(tabla)

				$row = 8;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('B7:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('14');
				$this->calc->getActiveSheet()->getStyle('C7:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('D7:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('E7:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('22');
				$this->calc->getActiveSheet()->getStyle('F7:F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('20');
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$formatDateEnd.'&to='.$formatDateBegin.'&warehouse_id='.$dataStation->almacen_id.'&days=7&isvaliddiffmonths=si';
						$dataRemoteStations = getUncompressData($curl);
					}
					$this->calc->getActiveSheet()->setCellValue('A'.$row, $dataStation->client_name);
					$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->setCellValue('B'.$row, $dataStation->name);
					$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
					$this->calc->getActiveSheet()->mergeCells('B'.$row.':G'.$row);
					$row++;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$dataRemoteStations = unserialize($dataRemoteStations[0]);
						if($typeStation == 0) {

							foreach($dataRemoteStations as $key => $data) {
								$this->calc->getActiveSheet()->setCellValue('A'.$row, $data['desc_comb']);
								$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$data['nu_capacidad'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$data['nu_medicion'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$data['nu_venta'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('E'.$row, round((float)$data['tiempo']));
								$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$data['cantidad_ultima_compra'], 2, '.', ','));
								$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
								$this->calc->getActiveSheet()->setCellValue('G'.$row, date("d/m/Y", strtotime($data['fecha_ultima_compra'])));
								$row++;
							}

						} else {
							//market
						}
					} else {
						$return['status'] = 4;
					}
					$row++;
				}


				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_stock_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
					            
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
			} else {
				echo 'No';
				//parametros vacios
				//error 404
			}
		} else {
			//no session
			//pagina de error 404
		}
	}

	public function generateCaclSumary() {
		if(checkSession()) {
			$return = array();
			//echo '3: '.$this->uri->segment(3).', 4: '.$this->uri->segment(4).', 5: '.$this->uri->segment(5);
			//exit;
			//error_log(  json_encode( $this->uri ) );
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				$typeStation = $this->uri->segment(6);
				$include = $this->uri->segment(9);
				$mod = '';
				$typeEst = '';
				if($typeStation == 3) {
					$mod = 'TOTALS_SUMARY_SALE';//termpral para este reporte con serialize
					$typeEst = 'comb';
					$titleDocument = 'Resumen de Combustibles';
				} else {
					$mod = 'ERR';//falta
					$typeEst = 'market';
					$titleDocument = 'Resumen de Market';
				}
				//$return['dateEnd'] = date('d/m/Y', strtotime(formatDateCentralizer($this->uri->segment(4),3). ' - 7 days'));

				$formatDateBegin = formatDateCentralizer($this->uri->segment(4),2);
				$formatDateEnd = formatDateCentralizer($this->uri->segment(5),2);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 3 ? 'C' : 'M',$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 3 ? 'C' : 'M');
					}
				}

				//echo 'formatDateBegin: '.$formatDateBegin.', formatDateEnd: '.$formatDateEnd.', typeStation: '.$typeStation;

				//load our new PHPExcel library
				$this->load->library('calc');
				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:G1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Fecha');
				$this->calc->getActiveSheet()->setCellValue('B3', $this->uri->segment(4));

				$this->calc->getActiveSheet()->setCellValue('A4', 'Empresa');

				$tipo = '';
				$textInclude = '';
				if($include == 0) {
					$tipo = 'Galones';
					$textInclude = 'No';
				} else if($include == 1) {
					$tipo = 'Galones';
					$textInclude = 'Si';
				} else if($include == 2) {
					$tipo = 'Soles';
					$textInclude = 'No';
				} else if($include == 3) {
					$tipo = 'Soles';
					$textInclude = 'Si';
				}

				$this->calc->getActiveSheet()->setCellValue('A6', 'Tipo');
				$this->calc->getActiveSheet()->setCellValue('B6', $tipo);
				$this->calc->getActiveSheet()->setCellValue('A7', 'Excluir consumo interno');
				$this->calc->getActiveSheet()->setCellValue('B7', $textInclude);

				//Inicio de cabecera	(tabla)
				$this->calc->getActiveSheet()->setCellValue('A9', 'Estación');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				$this->calc->getActiveSheet()->setCellValue('B9', '84');
				$this->calc->getActiveSheet()->setCellValue('C9', '90');
				$this->calc->getActiveSheet()->setCellValue('D9', '95');
				$this->calc->getActiveSheet()->setCellValue('E9', '97');
				$this->calc->getActiveSheet()->setCellValue('F9', 'D2');
				$this->calc->getActiveSheet()->setCellValue('G9', 'GLP');
				$this->calc->getActiveSheet()->setCellValue('H9', 'GNV');
				$this->calc->getActiveSheet()->setCellValue('I9', 'Total');
				$this->calc->getActiveSheet()->setCellValue('J9', 'Tienda');
				$this->calc->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A9:J9')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera		(tabla)

				$row = 10;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('B9:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('14');
				$this->calc->getActiveSheet()->getStyle('C9:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('D9:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('E9:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('22');
				$this->calc->getActiveSheet()->getStyle('F9:F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('G9:G256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('H')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('H9:H256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('I')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('I9:I256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('J')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('J9:J256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
												
				// $dataStations = null;
				// $dataStations[] = array(
				// 	"client_name" => "ENERGIGAS S.A.C.",
				// 	"taxid"       => "20126634634",
				// 	"c_org_id"    => "58",
				// 	"name"        => "ENERGIGAS VENEZUELA 2",
				// 	"initials"    => "VEN2",
				// 	"value"       => "0",
				// 	"ip"          => "10.0.38.200",
				// 	"almacen_id"  => "038"
				// );
				// $dataStations[] = array(
				// 	"client_name" => "ENERGIGAS S.A.C.",
				// 	"taxid"       => "20126634634",
				// 	"c_org_id"    => "16",
				// 	"name"        => "EDS CHIMBOTE",
				// 	"initials"    => "CHB",
				// 	"value"       => "16",
				// 	"ip"          => "10.0.19.1",
				// 	"almacen_id"  => "003"
				// );
				// error_log( json_encode( $dataStations ) );
				
				$total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						if(is_object($dataStation)){
							$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
							$curl = $curl . '?mod='.$mod.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&days=7&isvaliddiffmonths=si&unserialize=1';
							$name = $dataStation->name;
						}else if(is_array($dataStations)){
							$curl = 'http://'.$dataStation['ip'].'/sistemaweb/centralizer_.php';
							$curl = $curl . '?mod='.$mod.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation['almacen_id'].'&days=7&isvaliddiffmonths=si&unserialize=1';
							$name = $dataStation['name'];
						}
						
						error_log("****** curl ******");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
						error_log("****** getUncompressData ******");
						error_log( json_encode( $dataRemoteStations ) );
					}
					//$row++;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$dataRemoteStations = unserialize($dataRemoteStations[0]);
						error_log("****** unserialize ******");
						error_log( json_encode( $dataRemoteStations ) );

						if($typeStation == 3) {

							$_data = array();
							$value = array(0,0,0,0,0,0,0,0,0,0,0,0);
							foreach($dataRemoteStations as $key => $data) {
								$_data[] = $data;
								
								if ($include == 0) {
									$value[8] = $name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_cantidad'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = converterUM(array('type' => 0, 'co' => $data['neto_cantidad']));//gal

										//$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_cantidad'] != '') {
											$value[6] = converterUM(array('type' => 1, 'co' => $data['neto_cantidad']));//gal

											//$value[7] += $value[6];
										}
									}
									if ($data['codigo'] == '11620307') {
										$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										$value[7] += $value[6];
									} else if ($data['codigo'] != '11620307' || $data['codigo'] != '11620308') {
										$value[7] += $data['neto_cantidad'] != '' ? $data['neto_cantidad'] : 0;
									}
								} else if ($include == 1) {
									$value[8] = $name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_cantidad'] - $data['cantidad_ci'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = $data['neto_cantidad'] - $data['cantidad_ci'];
										$value[5] = converterUM(array('type' => 0, 'co' => $value[5]));//gal

										//$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_cantidad'] != '') {
											$value[6] = $data['neto_cantidad'] - $data['cantidad_ci'];
											$value[6] = converterUM(array('type' => 1, 'co' => $value[6]));//gal

											//$value[7] += $value[6];
										}
									}
									if ($data['codigo'] == '11620307') {
										$value[7] += $value[5];
									} else if ($data['codigo'] == '11620308') {
										$value[7] += $value[6];
									} else if($data['codigo'] != '11620307' || $data['codigo'] != '11620308') {
										$value[7] += ($data['neto_cantidad'] != '' ? $data['neto_cantidad'] : 0) - $data['cantidad_ci'];
									}
								} else if ($include == 2) {
									$value[8] = $name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = $data['neto_soles'];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_soles'] != '') {
											$value[6] = $data['neto_soles'];
										}
									}
									$value[7] += $data['neto_soles'] != '' ? $data['neto_soles'] : 0;
								} else if ($include == 3) {
									$value[8] = $name;
									if($data['codigo'] == '11620301') {
										//84
										$value[0] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620302') {
										//90
										$value[1] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620305') {
										//95
										$value[2] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620303') {
										//97
										$value[3] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620304') {
										//D2
										$value[4] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620307') {
										//GLP
										$value[5] = $data['neto_soles'] - $data['importe_ci'];
									} else if ($data['codigo'] == '11620308') {
										//GNV
										if ($data['neto_soles'] != '') {
											$value[6] = $data['neto_soles'] - $data['importe_ci'];
										}
									}
									$value[7] += ($data['neto_soles'] != '' ? $data['neto_soles'] : 0) - $data['importe_ci'];
								}
								//$row++;
							}
							/*echo '<hr><pre>';
							var_dump($value);
							echo '</pre>';exit;*/

							$total[0] += $value[0];
							$total[1] += $value[1];
							$total[2] += $value[2];
							$total[3] += $value[3];
							$total[4] += $value[4];
							$total[5] += $value[5];
							$total[6] += $value[6];
							$total[7] += $value[7];

							$this->calc->getActiveSheet()->setCellValue('A'.$row, $name);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$value[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$value[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$value[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$value[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$value[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$value[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$value[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$value[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							
							/*echo 'mod: '.$mod.'<br>';
							echo '<pre>';
							var_dump($_data);
							echo '</pre>';
							exit;*/

						} else {
							//market
						}
					} else {
						$return['status'] = 4;
					}
					$row++;
				}
				$row += 2;

				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');

				///exit;

				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_sumary_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
					            
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
			} else {
				echo 'Error al enviar datos.';
			}
		}
	}

	public function generateCaclStatistics() {
		if(checkSession()) {
			$return = array();
			if($this->uri->segment(3) != null && $this->uri->segment(4) != null && $this->uri->segment(5) != null) {
				$id = $this->uri->segment(3) == 'a' ? '*' : $this->uri->segment(3);
				//var_dump($this->uri->segment(3));exit;
				$typeStation = $this->uri->segment(8);
				$include = $this->uri->segment(11);
				$mod = '';
				$typeEst = '';
				if($typeStation == 4) {
					$mod = 'TOTALS_STATISTICS_SALE';//termpral para este reporte con serialize
					$typeEst = 'comb';
					$titleDocument = 'Estadística de Ventas';
				} else {
					$mod = 'ERR';//falta
					$typeEst = 'market';
					$titleDocument = 'Stock de Market';
				}

				$formatDateBegin = formatDateCentralizer($this->uri->segment(6),2);
				$formatDateEnd = formatDateCentralizer($this->uri->segment(7),2);

				$_formatDateBegin = formatDateCentralizer($this->uri->segment(4),2);
				$_formatDateEnd = formatDateCentralizer($this->uri->segment(5),2);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($id != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 4 ? 'C' : 'M',$id);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 4 ? 'C' : 'M');
					}
				}

				//echo 'formatDateBegin: '.$formatDateBegin.', formatDateEnd: '.$formatDateEnd.', typeStation: '.$typeStation;

				//load our new PHPExcel library
				$this->load->library('calc');
				$this->calc->setActiveSheetIndex(0);
				$this->calc->getActiveSheet()->setTitle($titleDocument);
				$this->calc->getActiveSheet()->setCellValue('A1', appName());
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
				$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$this->calc->getActiveSheet()->mergeCells('A1:G1');
				$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->calc->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

				$this->calc->getActiveSheet()->setCellValue('A3', 'Anterior');
				$this->calc->getActiveSheet()->setCellValue('B3', $this->uri->segment(4));
				$this->calc->getActiveSheet()->setCellValue('C3', $this->uri->segment(5));

				$this->calc->getActiveSheet()->setCellValue('A4', 'Actual');
				$this->calc->getActiveSheet()->setCellValue('B4', $this->uri->segment(6));
				$this->calc->getActiveSheet()->setCellValue('C4', $this->uri->segment(7));

				$this->calc->getActiveSheet()->setCellValue('A5', 'Empresa');

				$textInclude = '';
				if ($include == 0) {
					$textInclude = 'No';
				} else if ($include == 1) {
					$textInclude = 'Si';
				}

				$this->calc->getActiveSheet()->setCellValue('A7', 'Excluir consumo interno');
				$this->calc->getActiveSheet()->setCellValue('B7', $textInclude);

				//Inicio de cabecera	(tabla)
				$this->calc->getActiveSheet()->setCellValue('A9', 'Estación');
				$this->calc->getActiveSheet()->getColumnDimension('A')->setWidth('25');
				$this->calc->getActiveSheet()->setCellValue('B9', '84');
				$this->calc->getActiveSheet()->setCellValue('C9', '90');
				$this->calc->getActiveSheet()->setCellValue('D9', '95');
				$this->calc->getActiveSheet()->setCellValue('E9', '97');
				$this->calc->getActiveSheet()->setCellValue('F9', 'D2');
				$this->calc->getActiveSheet()->setCellValue('G9', 'GLP');
				$this->calc->getActiveSheet()->setCellValue('H9', 'GNV');
				$this->calc->getActiveSheet()->setCellValue('I9', 'Total');
				$this->calc->getActiveSheet()->setCellValue('J9', 'Market');
				$this->calc->getActiveSheet()->getRowDimension('9')->setRowHeight(20);
				$this->calc->getActiveSheet()->getStyle('A9:J9')->applyFromArray(
					array(
						'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'color' => array('rgb' => '337ab7')
						),
						'font' => array(
							'bold'  => true,
							'color' => array('rgb' => 'FFFFFF'),
							'size'  => 12,
							//'name'  => 'Verdana'
						)
					)
				);
				//Fin de cabecera		(tabla)

				$row = 10;

				$this->calc->getActiveSheet()->getColumnDimension('B')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('B9:B256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('C')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('C9:C256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('D')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('D9:D256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('E')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('E9:E256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('F')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('F9:F256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('G')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('G9:G256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('H')->setWidth('15');
				$this->calc->getActiveSheet()->getStyle('H9:H256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('I')->setWidth('20');
				$this->calc->getActiveSheet()->getStyle('I9:I256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->calc->getActiveSheet()->getColumnDimension('J')->setWidth('16');
				$this->calc->getActiveSheet()->getStyle('I9:J256')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sale_total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$sale__total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$dif_total = array(0,0,0,0,0,0,0,0,0,0,0,0);
				$text = array();

				$data_ = array();
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';

						$curl = $curl . '?mod='.$mod.'&_from='.$_formatDateBegin.'&_to='.$_formatDateEnd.'&from='.$formatDateBegin.'&to='.$formatDateEnd.'&warehouse_id='.$dataStation->almacen_id.'&days=7&isvaliddiffmonths=si&unserialize=1';
						//var_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					//
					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$dataRemoteStations = unserialize($dataRemoteStations[0]);

						if($typeStation == 4) {
							$sale = array(0,0,0,0,0,0,0,0,0,0,0,0);
							$sale_ = array(0,0,0,0,0,0,0,0,0,0,0,0);
							$dif = array(0,0,0,0,0,0,0,0,0,0,0,0);

							foreach ($dataRemoteStations as $key => $data) {
								$data_[$dataStation->taxid][] = $data['neto_venta'];
								
								if ($include == 0) {
									if ($data['codigo'] == '11620301') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[0] = $data['neto_venta'];
										} else {
											$sale_[0] = $data['neto_venta'];
											$dif[0] = amountPercentage(array('num1' => $sale[0], 'num2' => $sale_[0]));
										}
									} else if($data['codigo'] == '11620302') {
										if ($data['_type'] == 'actual') {
											$sale[1] = $data['neto_venta'];
										} else {
											$sale_[1] = $data['neto_venta'];
											$dif[1] = amountPercentage(array('num1' => $sale[1], 'num2' => $sale_[1]));
										}
									} else if($data['codigo'] == '11620305') {
										if ($data['_type'] == 'actual') {
											$sale[2] = $data['neto_venta'];
										} else {
											$sale_[2] = $data['neto_venta'];
											$dif[2] = amountPercentage(array('num1' => $sale[2], 'num2' => $sale_[2]));
										}
									} else if($data['codigo'] == '11620303') {
										if ($data['_type'] == 'actual') {
											$sale[3] = $data['neto_venta'];
										} else {
											$sale_[3] = $data['neto_venta'];
											$dif[3] = amountPercentage(array('num1' => $sale[3], 'num2' => $sale_[3]));
										}
									} else if($data['codigo'] == '11620304') {
										if ($data['_type'] == 'actual') {
											$sale[4] = $data['neto_venta'];
										} else {
											$sale_[4] = $data['neto_venta'];
											$dif[4] = amountPercentage(array('num1' => $sale[4], 'num2' => $sale_[4]));
										}
									} else if($data['codigo'] == '11620307') {
										if ($data['_type'] == 'actual') {
											$sale[5] = $data['neto_venta'];
										} else {
											$sale_[5] = $data['neto_venta'];
											$dif[5] = amountPercentage(array('num1' => $sale[5], 'num2' => $sale_[5]));
										}
									} else if($data['codigo'] == '11620308') {
										if ($data['_type'] == 'actual') {
											if ($data['neto_venta'] != '') {
												$sale[6] = $data['neto_venta'];
											}
										} else {
											if ($data['neto_venta'] != '') {
												$sale_[6] = $data['neto_venta'];
												$dif[6] = amountPercentage(array('num1' => $sale[6], 'num2' => $sale_[6]));
											}
										}
									}

									if ($data['_type'] == 'actual' && $data['codigo'] != 'MARKET') {
										$sale[7] += $data['neto_venta'] != '' ? $data['neto_venta'] : 0;
									} else if ($data['codigo'] != 'MARKET') {
										$sale_[7] += ($data['neto_venta'] != '' ? $data['neto_venta'] : 0) - ($data['importe_ci']);
										$dif[7] = amountPercentage(array('num1' => $sale[7], 'num2' => $sale_[7]));
									}

									if($data['codigo'] == 'MARKET') {
										if ($data['_type'] == 'actual') {
											if($data['neto_venta'] != '') {
												$sale[8] = $data['neto_venta'];
											}
										} else {
											if($data['neto_venta'] != '') {
												$sale_[8] = $data['neto_venta'];
												$dif[8] = amountPercentage(array('num1' => $sale[8], 'num2' => $sale_[8]));
											}
										}
									}

									if($key == 0) {
										$text[0] = $dataStation->name;
									} else if ($key == 1) {
										$text[1] = 'Anterior';
									}
								} else {
									if ($data['codigo'] == '11620301') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[0] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[0] = $data['neto_venta'] - $data['importe_ci'];
											$dif[0] = amountPercentage(array('num1' => $sale[0], 'num2' => $sale_[0]));
										}
									} else if ($data['codigo'] == '11620302') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[1] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[1] = $data['neto_venta'] - $data['importe_ci'];
											$dif[1] = amountPercentage(array('num1' => $sale[1], 'num2' => $sale_[1]));
										}
									} else if ($data['codigo'] == '11620305') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[2] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[2] = $data['neto_venta'] - $data['importe_ci'];
											$dif[2] = amountPercentage(array('num1' => $sale[2], 'num2' => $sale_[2]));
										}
									} else if ($data['codigo'] == '11620303') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[3] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[3] = $data['neto_venta'] - $data['importe_ci'];
											$dif[3] = amountPercentage(array('num1' => $sale[3], 'num2' => $sale_[3]));
										}
									} else if ($data['codigo'] == '11620304') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[4] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[4] = $data['neto_venta'] - $data['importe_ci'];
											$dif[4] = amountPercentage(array('num1' => $sale[4], 'num2' => $sale_[4]));
										}
									} else if ($data['codigo'] == '11620307') {
										//84
										if ($data['_type'] == 'actual') {
											$sale[5] = $data['neto_venta'] - $data['importe_ci'];
										} else {
											$sale_[5] = $data['neto_venta'] - $data['importe_ci'];
											$dif[5] = amountPercentage(array('num1' => $sale[5], 'num2' => $sale_[5]));
										}
									} else if ($data['codigo'] == '11620308') {
										//84
										if ($data['_type'] == 'actual') {
											if ($data['neto_venta'] != '') {
												$sale[6] = $data['neto_venta'] - $data['importe_ci'];
											}
										} else {
											if ($data['neto_venta'] != '') {
												$sale_[6] = $data['neto_venta'] - $data['importe_ci'];
												$dif[6] = amountPercentage(array('num1' => $sale[6], 'num2' => $sale_[6]));
											}
										}
									}

									if ($data['_type'] == 'actual' && $data['codigo'] != 'MARKET') {
										$sale[7] += ($data['neto_venta'] != '' ? $data['neto_venta'] : 0) - $data['importe_ci'];
									} else if ($data['codigo'] != 'MARKET') {
										$sale_[7] += ($data['neto_venta'] != '' ? $data['neto_venta'] : 0) - ($data['importe_ci']);
										$dif[7] = amountPercentage(array('num1' => $sale[7], 'num2' => $sale_[7]));
									}

									if($data['codigo'] == 'MARKET') {
										if ($data['_type'] == 'actual') {
											if($data['neto_venta'] != '') {
												$sale[8] = $data['neto_venta'];
											}
										} else {
											if($data['neto_venta'] != '') {
												$sale_[8] = $data['neto_venta'];
												$dif[8] = amountPercentage(array('num1' => $sale[8], 'num2' => $sale_[8]));
											}
										}
									}

									if($key == 0) {
										$text[0] = $dataStation->name;
									} else if ($key == 1) {
										$text[1] = 'Anterior';
									}
								}

								$text[2] = 'Diferencia (%)';
							}

							$sale_total[0] += $sale[0];
							$sale_total[1] += $sale[1];
							$sale_total[2] += $sale[2];
							$sale_total[3] += $sale[3];
							$sale_total[4] += $sale[4];
							$sale_total[5] += $sale[5];
							$sale_total[6] += $sale[6];
							$sale_total[7] += $sale[7];
							$sale_total[8] += $sale[8];

							$sale__total[0] += $sale_[0];
							$sale__total[1] += $sale_[1];
							$sale__total[2] += $sale_[2];
							$sale__total[3] += $sale_[3];
							$sale__total[4] += $sale_[4];
							$sale__total[5] += $sale_[5];
							$sale__total[6] += $sale_[6];
							$sale__total[7] += $sale_[7];
							$sale__total[8] += $sale_[8];

							$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[0]);
							$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale[8], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');

							$row++;
							$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[1]);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale_[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale_[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale_[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale_[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale_[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale_[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale_[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale_[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale_[8], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');

							$row++;
							$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[2]);
							$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$dif[0], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$dif[1], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$dif[2], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$dif[3], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$dif[4], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$dif[5], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$dif[6], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$dif[7], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
							$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$dif[8], 2, '.', ','));
							$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');

						} else {
							//market
						}
					} else {
						$return['status'] = 4;
					}
					$row++;
				}

				$row += 2;

				$this->calc->getActiveSheet()->setCellValue('A'.$row, 'Totales');
				$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale_total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale_total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale_total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale_total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale_total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale_total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale_total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale_total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale_total[8], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);

				$row++;
				$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[1]);
				$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$sale__total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$sale__total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$sale__total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$sale__total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$sale__total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$sale__total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$sale__total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$sale__total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$sale__total[8], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);


				$dif_total[0] = amountPercentage(array('num1' => $sale_total[0], 'num2' => $sale__total[0]));
				$dif_total[1] = amountPercentage(array('num1' => $sale_total[1], 'num2' => $sale__total[1]));
				$dif_total[2] = amountPercentage(array('num1' => $sale_total[2], 'num2' => $sale__total[2]));
				$dif_total[3] = amountPercentage(array('num1' => $sale_total[3], 'num2' => $sale__total[3]));
				$dif_total[4] = amountPercentage(array('num1' => $sale_total[4], 'num2' => $sale__total[4]));
				$dif_total[5] = amountPercentage(array('num1' => $sale_total[5], 'num2' => $sale__total[5]));
				$dif_total[6] = amountPercentage(array('num1' => $sale_total[6], 'num2' => $sale__total[6]));
				$dif_total[7] = amountPercentage(array('num1' => $sale_total[7], 'num2' => $sale__total[7]));
				$dif_total[8] = amountPercentage(array('num1' => $sale_total[8], 'num2' => $sale__total[8]));

				$row++;
				$this->calc->getActiveSheet()->setCellValue('A'.$row, $text[2]);
				$this->calc->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('B'.$row, number_format((float)$dif_total[0], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('C'.$row, number_format((float)$dif_total[1], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('D'.$row, number_format((float)$dif_total[2], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('E'.$row, number_format((float)$dif_total[3], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('F'.$row, number_format((float)$dif_total[4], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('G'.$row, number_format((float)$dif_total[5], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('H'.$row, number_format((float)$dif_total[6], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('I'.$row, number_format((float)$dif_total[7], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
				$this->calc->getActiveSheet()->setCellValue('J'.$row, number_format((float)$dif_total[8], 2, '.', ','));
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getNumberFormat()->setFormatCode('0.00');
				$this->calc->getActiveSheet()->getStyle('J'.$row)->getFont()->setBold(true);

				/*echo '<pre>';
				var_dump($data_);
				echo '</pre>';
				exit;*/

				//componer nombre: ocsmanager_TYPEMOD_TYPESTATION_YYYYMMMDD_HHMMSS.xls
				$comp = date('Ymd_His');
				$filename='ocsmanager_statistics_'.$typeEst.'_'.$comp.'.xls'; //save our workbook as this file name
				header('Content-Type: application/vnd.ms-excel'); //mime type
				header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
				header('Cache-Control: max-age=0'); //no cache
					            
				//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
				//if you want to save it as .XLSX Excel 2007 format
				$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
				//force user to download the Excel file without writing it to server's HD
				$objWriter->save('php://output');
			} else {
				echo 'Error al enviar datos.';
			}
		}
	}

	/*public function generateCaclSumary() {
		if(checkSession()) {
			$data = $_GET;
			var_dump($data);

			//$this->tmp = $data;
			//$this->demoExcel();
			$_SESSION['data_tmp'] = $data;
		}
	}*/

	/**
	 * Demo
	 */
	public function combSales2()
	{
		$result = array();
		$stations = $this->input->post('stations');
		foreach ($stations as $key => $station) {
			$result[] = array('name' => $station, 'value' => 1);
		}
		echo json_encode($result);
	}

	/**
	 * Demo
	 */
	public function demoExcel()
	{
		//load our new PHPExcel library
		$this->load->library('calc');
		//$this->load->library('someclass');
		//var_dump($this->someclass);

		//activate worksheet number 1
		$this->calc->setActiveSheetIndex(0);
		//name the worksheet
		$this->calc->getActiveSheet()->setTitle('test worksheet');
		//set cell A1 content with some text
		$this->calc->getActiveSheet()->setCellValue('A1', 'This is just some text value');
		//change the font size
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		//make the font become bold
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		$this->calc->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$filename='just_some_random_name.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
	}

	public function demoajax()
	{
		//load our new PHPExcel library
		$this->load->library('calc');
		//$this->load->library('someclass');
		//var_dump($this->someclass);

		//activate worksheet number 1
		$this->calc->setActiveSheetIndex(0);
		//name the worksheet
		$this->calc->getActiveSheet()->setTitle('test worksheet');
		//set cell A1 content with some text
		$text = '';
		if($_SESSION['data_tmp'] != '') {
			$text = ' si existe TMP :)';
		}
		$this->calc->getActiveSheet()->setCellValue('A1', 'This is just some text value'.$text);
		//change the font size
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		//make the font become bold
		$this->calc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		$this->calc->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		$this->calc->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$filename='just_some_random_name.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache

		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->calc, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		unset($this->tmp);
		$objWriter->save('php://output');
	}
}
