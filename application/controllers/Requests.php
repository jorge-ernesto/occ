<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Requests extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('functions');
	}

	// public function getSales()
	// {
	// 	$return = array();
	// 	$return['memory'][] = getMemory(array('start function'));
	// 	if(checkSession()) {
	// 		if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
	// 			$return['status'] = 1;
	// 			//$return['formatDateBegin'] =  date("Ymd", strtotime($this->input->post('dateBegin')));
	// 			$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
	// 			$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
	// 			//$return['formatDateEnd'] = $return['formatDateBegin'];

	// 			$return['beginDate'] = $this->input->post('dateBegin');
	// 			$return['endDate'] = $this->input->post('dateEnd');
	// 			$return['qtySale'] = $this->input->post('qtySale');
	// 			$return['typeCost'] = $this->input->post('typeCost');
	// 			$return['id'] = $this->input->post('id');

	// 			$return['typeStation'] = $this->input->post('typeStation');

	// 			$typeStation = getDescriptionTypeStation($return['typeStation']);
	// 			//$return['isMarket'] = $this->input->post('isMarket');

	// 			$this->load->model('COrg_model');
	// 			$isAllStations = true;
	// 			if($this->input->post('id') != '*') {
	// 				if($isAllStations) {
	// 					$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
	// 				}
	// 			} else {
	// 				if($isAllStations) {
	// 					$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
	// 				}
	// 			}

	// 			$mod = '';
	// 			if($return['typeStation'] == 0) {
	// 				$mod = 'TOTALS_SALE_COMB';
	// 			} else if($return['typeStation'] == 1 || $return['typeStation'] == 2) {
	// 				$mod = 'TOTALS_SALE_MARKET';
	// 			} else {
	// 				$mod = 'ERR';
	// 			}

	// 			$ext = array();

	// 			foreach($dataStations as $key => $dataStation) {
	// 				if($isAllStations) {
	// 					$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
	// 					$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&qty_sale='.$return['qtySale'].'&type_cost='.$return['typeCost'];
	// 					$dataRemoteStations = getUncompressData($curl);
	// 				}

	// 				$data = array();
	// 				$total_cantidad = 0.0;
	// 				$total_venta = 0.0;
	// 				$total_utilidad = 0.0;
	// 				$total_costo = 0.0;

	// 				//$return['query'] = $dataRemoteStations;
	// 				if($dataRemoteStations != false) {
	// 					$return['status'] = 1;
	// 					if($return['typeStation'] == 0) {
	// 						$dataRemoteStations = json_decode($dataRemoteStations[0]);
	// 						foreach($dataRemoteStations as $key => $drs) {
	// 							$utilidad = 0.0;
	// 							if($drs->codigo == '11620307') {
	// 								$gal = converterUM(array('type' => 0, 'co' => $drs->total_ventagalon));
	// 								$total_cantidad += $gal;
	// 								$venta = $drs->total_ventavalor;
	// 								$costo = $drs->costo;
	// 								$venta_sin_igv = $drs->venta_sin_igv;
	// 								$utilidad = $venta_sin_igv - $costo;
	// 							} else if($drs->codigo == '11620308') {
	// 								$gal = converterUM(array('type' => 1, 'co' => $drs->total_ventagalon));
	// 								$total_cantidad += $gal;
	// 								$venta = $drs->total_ventavalor;
	// 								$costo = $drs->costo;
	// 								$venta_sin_igv = $drs->venta_sin_igv;
	// 								$utilidad = $venta_sin_igv - $costo;
	// 							} else {
	// 								$gal = $drs->total_ventagalon;
	// 								$total_cantidad += $gal;
	// 								$venta = $drs->total_ventavalor;
	// 								$costo = $drs->costo;
	// 								$venta_sin_igv = $drs->venta_sin_igv;
	// 								$utilidad = $venta_sin_igv - $costo;
	// 							}

	// 							$total_venta += $venta;
	// 							$total_utilidad += $utilidad;
	// 							$total_costo += $costo;

	// 							$data[] = array(
	// 								'product' => $drs->codigo,
	// 								'galon' => $gal,
	// 								'venta' => $venta,
	// 								'costo' => $costo,
	// 								'venta_sin_igv' => $venta_sin_igv,
	// 								'utilidad' => $utilidad,
	// 							);
	// 						}

	// 						/*
	// 						//array para comb
	// 						foreach($dataRemoteStations as $drs) {
	// 							$utilidad = 0.0;
	// 							if($drs != '') {
	// 								$d = explode("|", $drs);
	// 								if($d[2] == '11620307') {
	// 									$gal = converterUM(array('type' => 0, 'co' => $d[0]));
	// 									$total_cantidad += $gal;
	// 									$venta = $d[1];
	// 									$costo = $d[3];
	// 									$venta_sin_igv = $d[4];
	// 									$utilidad = $venta_sin_igv - $costo;
	// 								} else if($d[2] == '11620308') {
	// 									$gal = converterUM(array('type' => 1, 'co' => $d[0]));
	// 									$total_cantidad += $gal;
	// 									$venta = $d[1];
	// 									$costo = $d[3];
	// 									$venta_sin_igv = $d[4];
	// 									$utilidad = $venta_sin_igv - $costo;
	// 								} else {
	// 									$gal = $d[0];
	// 									$total_cantidad += $gal;
	// 									$venta = $d[1];
	// 									$costo = $d[3];
	// 									$venta_sin_igv = $d[4];
	// 									$utilidad = $venta_sin_igv - $costo;
	// 								}

	// 								$total_venta += $venta;
	// 								//$utilidad = $d[4]-$d[3];
	// 								$total_utilidad += $utilidad;
	// 								$total_costo += $costo;

	// 								$data[] = array(
	// 									'product' => $d[2],
	// 									'galon' => $gal,
	// 									'venta' => $venta,
	// 									'costo' => $costo,
	// 									'venta_sin_igv' => $venta_sin_igv,
	// 									'utilidad' => $utilidad,
	// 								);
	// 							}
	// 						}// /. for each combu
	// 						*/
	// 					} else {
	// 						//array para market

	// 						//acumular directo de query
	// 						foreach($dataRemoteStations as $drs) {
	// 							$utilidad = 0.0;
	// 							if($drs != '') {
	// 								$d = explode("|", $drs);
	// 								$total_venta = $d[0];
	// 								$total_cantidad = $d[1];
	// 								$total_costo = $d[2];
	// 								$total_utilidad = $d[3];

	// 								$data[] = array(
	// 									'venta' => $d[0],
	// 									'cantidad' => $d[1],
	// 									'costo' => $d[2],
	// 									'utilidad' => $d[3],
	// 								);
	// 							}
	// 						}
	// 					}
	// 				} else {
	// 					$return['status'] = 4;
	// 				}

	// 				$dataOrder = subval_sort($data,'venta','DESC');

	// 				$ext[] = array(
	// 					'taxid' => $dataStation->taxid,
	// 					'total_cantidad' => $total_cantidad,
	// 					'total_venta' => $total_venta,
	// 					'total_utilidad' => $total_utilidad,
	// 					'total_costo' => $total_costo,
	// 				);

	// 				$return['stations'][] = array(
	// 					'name' => $dataStation->name,
	// 					'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
	// 					'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
	// 					'url' => $curl,
	// 					'id' => $dataStation->c_org_id,
	// 					'warehouse_id' => $dataStation->almacen_id,
	// 					'total_cantidad' => $total_cantidad,
	// 					'total_venta' => $total_venta,
	// 					'total_utilidad' => $total_utilidad,
	// 					'total_costo' => $total_costo,
	// 					'isConnection' => $return['status'] == 4 ? false : true,
	// 					/*'data' => $data,
	// 					'dataOrder' => $dataOrder,*/
	// 					'data' => $dataOrder,
	// 					'dataOrder' => $data,
	// 				);
	// 			}

	// 			/*$songs = array(
	// 				array('artist' => 'Smashing Pumpkins', 'songname' => 'Soma', 'id' => 4),
	// 				array('artist' => 'The Decemberists', 'songname' => 'The Island', 'id' => 1),
	// 				array('artist' => 'Fleetwood Mac', 'songname' => 'Second-hand News', 'id' => 3)
	// 			);

	// 			$return['songs'] = subval_sort($songs,'id','DESC');*/

	// 			$return['stationsOrder'] = subval_sort($return['stations'],'total_utilidad','DESC'); 

	// 		} else {
	// 			$return['status'] = 100;
	// 			$return['message'] = 'Error al enviar datos.';
	// 		}
	// 	} else {
	// 		$return['status'] = 101;
	// 		$return['message'] = 'No existe sesión.';
	// 	}
	// 	$return['memory'][] = getMemory(array('end function'));
	// 	unset($curl);
	// 	unset($dataStations);
	// 	unset($dataRemoteStations);
	// 	unset($ext);
	// 	unset($data);
	// 	unset($dataOrder);//usado?

	// 	$return['memory'][] = getMemory(array('unset function'));
	// 	echo json_encode($return);
	// }

	public function getSales()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				//$return['formatDateBegin'] =  date("Ymd", strtotime($this->input->post('dateBegin')));
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				//$return['formatDateEnd'] = $return['formatDateBegin'];

				$return['beginDate'] = $this->input->post('dateBegin');
				$return['endDate'] = $this->input->post('dateEnd');
				$return['qtySale'] = $this->input->post('qtySale');
				$return['typeCost'] = $this->input->post('typeCost');
				$return['id'] = $this->input->post('id');

				$return['typeStation'] = $this->input->post('typeStation');

				$typeStation = getDescriptionTypeStation($return['typeStation']);
				//$return['isMarket'] = $this->input->post('isMarket');
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				$mod = '';
				if($return['typeStation'] == 0) {
					$mod = 'TOTALS_SALE_COMB';
				} else if($return['typeStation'] == 1 || $return['typeStation'] == 2) {
					$mod = 'TOTALS_SALE_MARKET';
				} else {
					$mod = 'ERR';
				}

				$ext = array();

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&qty_sale='.$return['qtySale'].'&type_cost='.$return['typeCost'];
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);						
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$data = array();
					$total_cantidad = 0.0;
					$total_venta = 0.0;
					$total_utilidad = 0.0;
					$total_costo = 0.0;

					//$return['query'] = $dataRemoteStations;
					if($dataRemoteStations != false) {
						$return['status'] = 1;
						if($return['typeStation'] == 0) {
							//array para comb
							foreach($dataRemoteStations as $drs) {
								$utilidad = 0.0;
								if($drs != '') {
									$d = explode("|", $drs);
									if($d[2] == '11620307') {
										$gal = converterUM(array('type' => 0, 'co' => $d[0]));
										$total_cantidad += $gal;
										$venta = $d[1];
										$costo = $d[3];
										$venta_sin_igv = $d[4];
										$utilidad = $venta_sin_igv - $costo;
									} else if($d[2] == '11620308') {
										$gal = converterUM(array('type' => 1, 'co' => $d[0]));
										$total_cantidad += $gal;
										$venta = $d[1];
										$costo = $d[3];
										$venta_sin_igv = $d[4];
										$utilidad = $venta_sin_igv - $costo;
									} else {
										$gal = $d[0];
										$total_cantidad += $gal;
										$venta = $d[1];
										$costo = $d[3];
										$venta_sin_igv = $d[4];
										$utilidad = $venta_sin_igv - $costo;
									}

									$total_venta += $venta;
									//$utilidad = $d[4]-$d[3];
									$total_utilidad += $utilidad;
									$total_costo += $costo;

									$data[] = array(
										'product' => $d[2],
										'galon' => $gal,
										'venta' => $venta,
										'costo' => $costo,
										'venta_sin_igv' => $venta_sin_igv,
										'utilidad' => $utilidad,
									);
								}
							}
						} else {
							//array para market

							//acumular directo de query
							foreach($dataRemoteStations as $drs) {
								$utilidad = 0.0;
								if($drs != '') {
									$d = explode("|", $drs);
									$total_venta = $d[0];
									$total_cantidad = $d[1];
									$total_costo = $d[2];
									$total_utilidad = $d[3];

									$data[] = array(
										'venta' => $d[0],
										'cantidad' => $d[1],
										'costo' => $d[2],
										'utilidad' => $d[3],
									);
								}
							}
						}
					} else {
						$return['status'] = 4;
					}

					$dataOrder = subval_sort($data,'venta','DESC');

					$ext[] = array(
						'taxid' => $dataStation->taxid,
						'total_cantidad' => $total_cantidad,
						'total_venta' => $total_venta,
						'total_utilidad' => $total_utilidad,
						'total_costo' => $total_costo,
					);

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'total_cantidad' => $total_cantidad,
						'total_venta' => $total_venta,
						'total_utilidad' => $total_utilidad,
						'total_costo' => $total_costo,
						'isConnection' => $return['status'] == 4 ? false : true,
						/*'data' => $data,
						'dataOrder' => $dataOrder,*/
						'data' => $dataOrder,
						'dataOrder' => $data,
					);
				}

				/*$songs = array(
					array('artist' => 'Smashing Pumpkins', 'songname' => 'Soma', 'id' => 4),
					array('artist' => 'The Decemberists', 'songname' => 'The Island', 'id' => 1),
					array('artist' => 'Fleetwood Mac', 'songname' => 'Second-hand News', 'id' => 3)
				);

				$return['songs'] = subval_sort($songs,'id','DESC');*/

				$return['stationsOrder'] = subval_sort($return['stations'],'total_utilidad','DESC'); 

			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($ext);
		unset($data);
		unset($dataOrder);//usado?	

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);		
	}

	public function getSumarySale()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');

				$typeStation = getDescriptionTypeStation($return['typeStation']);
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				/* Obtenemos Codigos de Productos */
				$this->load->model('CProd_model');
				$dataProductos = $this->CProd_model->getAllCProd();
				$arrayProductos = array();
				foreach ($dataProductos as $key => $producto) {
					$arrayProductos[$producto->value] = array(
						'name' => $producto->name,
						'abbreviation' => $producto->abbreviation
					);
				}
				$return['result_c_product'] = $arrayProductos;
				/* Cerrar */

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				$mod = '';
				if($return['typeStation'] == 3) {
					$mod = 'TOTALS_SUMARY_SALE';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&type_cost='.$return['typeCost'];
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;
					$total_utilidad = 0.0;
					$total_costo = 0.0;

					if($dataRemoteStations != false) {

						$return['status'] = 1;
						//array para comb
						if($return['typeStation'] == 3) {
							foreach($dataRemoteStations as $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$utilidad = $d[7] - $d[6];
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										//'cantidad' => $d[2],
										//'venta' => $d[3],
										'neto_cantidad' => $d[9],
										'neto_venta' => $d[10],
										'importe_ci' => $d[11] != '' ? $d[11] : 0.0,
										'cantidad_ci' => $d[12] != '' ? $d[12] : 0.0,
										//'utilidad' => $utilidad,
									);

									$total_cantidad += $d[9];
									$total_precio += $d[10];
									$total_costo += $d[6];
									$total_utilidad += $utilidad;
								}
							}
						} else {
							//array para market (sin usar por ahora)
							foreach($dataRemoteStations as $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'neto_cantidad' => $d[2],
										'neto_venta' => $d[5],
										'consumo_galon' => $d[4],//costo
										'utilidad' => $d[6],
									);

									$total_cantidad += $d[2];
									$total_precio += $d[5];
									$total_costo += $d[4];
									$total_utilidad += $d[6];
								}
							}
						}

					} else {
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'url' => $curl,
						'data' => $data,
						'total_qty' => $total_cantidad,
						'total_price' => $total_precio,
						'total_cost' => $total_costo,
						'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);
	}

	public function getSalesForHours()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				/*PERSONALIZADO PARA EL REPORTE DE VENTAS POR HORAS*/
				$return['local'] = $this->input->post('local');
				$return['importe'] = $this->input->post('importe');
				$return['modo'] = $this->input->post('modo');
				$return['productos'] = $this->input->post('productos');
				$return['unidadmedida'] = $this->input->post('unidadmedida');
				/*CERRAR PERSONALIZADO PARA EL REPORTE DE VENTAS POR HORAS*/

				$typeStation = getDescriptionTypeStation($return['typeStation']);
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				$mod = '';
				if($return['typeStation'] == 6) {
					$mod = 'TOTALS_SALE_FOR_HOURS';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/

				$resultAllGroupByCombustible = array();

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&desde='.$return['dateBegin'].'&hasta='.$return['dateEnd'].'&local='.$return['local'].'&importe='.$return['importe'].'&modo='.$return['modo'].'&productos='.$return['productos'].'&unidadmedida='.$return['unidadmedida'];
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$result = array();
					// $total_cantidad = 0.0;
					// $total_precio = 0.0;
					// $total_utilidad = 0.0;
					// $total_costo = 0.0;

					if($dataRemoteStations != false) {

						if(empty($dataRemoteStations) || $dataRemoteStations == array() || $dataRemoteStations == array("")){ //Si hubo conexión a la estacion pero no hubo resultados														
							$return['status'] = 1;							
						}else{							

							$return['status'] = 1;
							//array para comb
							if($return['typeStation'] == 6) {
								/*OBTENEMOS VARIABLES PARA LA FUNCIONALIDAD*/
								$desde      = $return['dateBegin'];
								$hasta      = $return['dateEnd'];
								$diasemana  = "TODOS";
								$producto   = $return['productos'];
								$lado       = "TODOS";
								$estaciones = "TODAS";
								$local      = $return['local'];
								$importe    = $return['importe'];
								$bResumido  = $return['modo'];
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

										// $utilidad = $d[7] - $d[6];
										// $data[] = array(
										// 	'product_id' => $d[0],
										// 	'product' => $d[1],
										// 	//'cantidad' => $d[2],
										// 	//'venta' => $d[3],
										// 	'neto_cantidad' => $d[9],
										// 	'neto_venta' => $d[10],
										// 	'importe_ci' => $d[11] != '' ? $d[11] : 0.0,
										// 	'cantidad_ci' => $d[12] != '' ? $d[12] : 0.0,
										// 	//'utilidad' => $utilidad,
										// );

										// $total_cantidad += $d[9];
										// $total_precio += $d[10];
										// $total_costo += $d[6];
										// $total_utilidad += $utilidad;
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

					} else {
						$return['status'] = 4;
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
				
				if (!empty($resultAllGroupByCombustible)) {
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
				}						
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);
	}

	public function getLiquidacionDiaria()
	{		
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				$return['inventariocombustible'] = $this->input->post('inventariocombustible');
				$return['demo'] = $this->input->post('demo');

				$typeStation = getDescriptionTypeStation($return['typeStation']);		
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				$mod = '';
				if($return['typeStation'] == 7) {
					$mod = 'TOTALS_LIQUIDACION_DIARIA';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/				

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&desde='.$return['dateBegin'].'&hasta='.$return['dateEnd'];						
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$data = $dataRemoteStations;
					}else{
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'data' => $data,
						// 'total_qty' => $total_cantidad,
						// 'total_price' => $total_precio,
						// 'total_cost' => $total_costo,
						// 'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);
	}

	public function getSaldoSocio()
	{
		ini_set('memory_limit','-1');

		// Verificamos datos enviados por POST
		error_log('getSaldoSocio');
		error_log(json_encode($this->input->post()));

		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateEnd') != null && /*$this->input->post('id') != null &&*/ $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateEnd');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				$return['socios'] = $this->input->post('socios');
				$return['vales'] = $this->input->post('vales');
				$return['vista'] = $this->input->post('vista');

				$typeStation = getDescriptionTypeStation($return['typeStation']);		
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));
    
				$this->load->model('COrg_model');
				$isAllStations = true;
				$stationsIdSelectMultiple = ($this->input->post('id') == NULL) ? '*' : $this->input->post('id');
				error_log("IDs de las estaciones seleccionadas en select multiple");
				error_log(json_encode($stationsIdSelectMultiple));
				if($stationsIdSelectMultiple != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndIdSelectMultiple($typeStation == 'MP' ? 'C' : $typeStation,$stationsIdSelectMultiple);
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				/* Obtenemos Codigos de Socios */
				$codigos_socios = implode("|", $this->input->post('socios'));
				error_log("Codigos de Socios");
				error_log(json_encode($this->input->post('socios')));
				error_log(json_encode($codigos_socios));
				/* Cerrar */

				$mod = '';
				if($return['typeStation'] == 8) {
					$mod = 'TOTALS_SALDO_SOCIO';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&desde='.$return['dateBegin'].'&hasta='.$return['dateEnd'].'&socios='.$codigos_socios.'&vales='.$return['vales'].'&vista='.$return['vista'];						
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						// $data = $dataRemoteStations;
						
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
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'data' => $data,
						// 'total_qty' => $total_cantidad,
						// 'total_price' => $total_precio,
						// 'total_cost' => $total_costo,
						// 'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);
	}

	public function getSobrantesFaltantes()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				$return['productos'] = $this->input->post('productos');
				$return['unidadmedida'] = $this->input->post('unidadmedida');
				$return['checkResumido'] = $this->input->post('checkResumido');
				$return['checkDetallado'] = $this->input->post('checkDetallado');

				$typeStation = getDescriptionTypeStation($return['typeStation']);
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation, $this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				/* Obtenemos Codigos de Productos */
				if ($this->input->post('productos') == '*') {
					$this->load->model('CProd_model');				
					$codigos_productos = '';
					$result_c_product = $this->CProd_model->getAllActiveCProd();
					foreach ($result_c_product as $key => $cProd) {
						$codigos_productos .= $cProd->value . '|';
					}
					$codigos_productos = rtrim($codigos_productos, '|');
					error_log("Codigos de Productos");
					error_log(json_encode($result_c_product));
					error_log(json_encode($codigos_productos));
				} else {
					$codigos_productos = $this->input->post('productos');
				}
				/* Cerrar */

				$mod = '';
				if($return['typeStation'] == 9) {
					$mod = 'TOTALS_SOBRANTES_FALTANTES';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&desde='.$return['dateBegin'].'&hasta='.$return['dateEnd'].'&productos='.$codigos_productos.'&unidadmedida='.$return['unidadmedida'].'&isvaliddiffmonths=si';
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$data = $dataRemoteStations;
					}else{
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'data' => $data,
						// 'total_qty' => $total_cantidad,
						// 'total_price' => $total_precio,
						// 'total_cost' => $total_costo,
						// 'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);
	}

	public function getMargenCliente()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				$return['clientes'] = TRIM($this->input->post('clientes'));

				$typeStation = getDescriptionTypeStation($return['typeStation']);
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				/* Obtenemos Codigos de Productos */
				$this->load->model('CProd_model');
				$dataProductos = $this->CProd_model->getAllCProd();
				$arrayProductos = array();
				foreach ($dataProductos as $key => $producto) {
					$arrayProductos[$producto->value] = array(
						'name' => $producto->name,
						'abbreviation' => $producto->abbreviation
					);
				}
				$return['result_c_product'] = $arrayProductos;
				/* Cerrar */

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				$mod = '';
				if($return['typeStation'] == 10) {
					$mod = 'TOTALS_MARGEN_CLIENTE';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&desde='.$return['dateBegin'].'&hasta='.$return['dateEnd'].'&clientes='.$return['clientes'].'&isvaliddiffmonths=si';
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$data = $dataRemoteStations;
					}else{
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'data' => $data,
						// 'total_qty' => $total_cantidad,
						// 'total_price' => $total_precio,
						// 'total_cost' => $total_costo,
						// 'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}

				error_log("****** Recorremos empresas cargadas ******");
				$return['companies'] = $this->aguparMargenClientePorEmpresa($return['stations'], $return['result_c_product']);
			}			
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);
	
		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);
	}

	public function aguparMargenClientePorEmpresa($stations, $result_c_product)
	{
		// return $stations;

		$companies = array();
		foreach ($stations as $key => $station) {
			$taxIdCompany      = $station['group']['taxid'];
			$nameCompany       = $station['group']['name'];
			$dataMargenCliente = $station['data']['data_margen_cliente'];

			foreach ($dataMargenCliente as $key => $margenCliente) {
				$ruc          = TRIM($margenCliente['ruc']);
				$razon_social = TRIM($margenCliente['razon_social']);
				$cliente      = $ruc." - ".$razon_social;
				$codigo       = TRIM($margenCliente['codigo']);

				$companies[$nameCompany]['group']['name']  = $nameCompany;
				$companies[$nameCompany]['group']['taxid'] = $taxIdCompany;

				// AGRUPADO POR CLIENTE Y ARTICULO
				$companies[$nameCompany]['data'][$cliente][$codigo]['ruc']          = $ruc;
				$companies[$nameCompany]['data'][$cliente][$codigo]['razon_social'] = $razon_social;
				$companies[$nameCompany]['data'][$cliente][$codigo]['codigo']       = $codigo;
				$companies[$nameCompany]['data'][$cliente][$codigo]['descripcion']  = $result_c_product[$codigo]['name'];
				if ($margenCliente['tipo_documento'] == '20') { // Nota de Credito
					$companies[$nameCompany]['data'][$cliente][$codigo]['cantidad']     -= $margenCliente['cantidad'];
					$companies[$nameCompany]['data'][$cliente][$codigo]['importe_neto'] -= $margenCliente['importe_neto'];
					$companies[$nameCompany]['data'][$cliente][$codigo]['costo']        -= ($margenCliente['cantidad'] * $margenCliente['costo_art']);
				} else {
					$companies[$nameCompany]['data'][$cliente][$codigo]['cantidad']     += $margenCliente['cantidad'];
					$companies[$nameCompany]['data'][$cliente][$codigo]['importe_neto'] += $margenCliente['importe_neto'];
					$companies[$nameCompany]['data'][$cliente][$codigo]['costo']        += ($margenCliente['cantidad'] * $margenCliente['costo_art']);
				}

				// AGRUPADO POR CLIENTE
				$companies[$nameCompany]['data'][$cliente]['TOTALES']['ruc']          = $ruc;
				$companies[$nameCompany]['data'][$cliente]['TOTALES']['razon_social'] = $razon_social;
				$companies[$nameCompany]['data'][$cliente]['TOTALES']['codigo']       = $codigo;
				$companies[$nameCompany]['data'][$cliente]['TOTALES']['descripcion']  = $result_c_product[$codigo]['name'];
				if ($margenCliente['tipo_documento'] == '20') { // Nota de Credito
					$companies[$nameCompany]['data'][$cliente]['TOTALES']['cantidad']     -= $margenCliente['cantidad'];
					$companies[$nameCompany]['data'][$cliente]['TOTALES']['importe_neto'] -= $margenCliente['importe_neto'];
					$companies[$nameCompany]['data'][$cliente]['TOTALES']['costo']        -= ($margenCliente['cantidad'] * $margenCliente['costo_art']);
				} else {
					$companies[$nameCompany]['data'][$cliente]['TOTALES']['cantidad']     += $margenCliente['cantidad'];
					$companies[$nameCompany]['data'][$cliente]['TOTALES']['importe_neto'] += $margenCliente['importe_neto'];
					$companies[$nameCompany]['data'][$cliente]['TOTALES']['costo']        += ($margenCliente['cantidad'] * $margenCliente['costo_art']);
				}
			}
		}

		return $companies;
	}

	public function getStatisticsSale()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);

				$return['_formatDateBegin'] = formatDateCentralizer($this->input->post('_dateBegin'),1);
				$return['_formatDateEnd'] = formatDateCentralizer($this->input->post('_dateEnd'),1);

				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');

				$typeStation = getDescriptionTypeStation($return['typeStation']);

				/* Obtenemos Codigos de Productos */
				$this->load->model('CProd_model');
				$dataProductos = $this->CProd_model->getAllCProd();
				$arrayProductos = array();
				foreach ($dataProductos as $key => $producto) {
					$arrayProductos[$producto->value] = array(
						'name' => $producto->name,
						'abbreviation' => $producto->abbreviation
					);
				}
				$return['result_c_product'] = $arrayProductos;
				/* Cerrar */

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}

				$mod = '';
				if($return['typeStation'] == 4) {
					$mod = 'TOTALS_STATISTICS_SALE';
					//$mod = 'TOTALS_STATISTICS_SALE_';
					//getStatisticsSale
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/

				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&_from='.$return['_formatDateBegin'].'&_to='.$return['_formatDateEnd'].'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&type_cost='.$return['typeCost'];
						$dataRemoteStations = getUncompressData($curl);
						/*
						echo "<pre>";
						var_dump($dataRemoteStations);
						echo "</pre>";
						*/
					}

					$return['curl'][] = $curl;

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;
					$total_utilidad = 0.0;
					$total_costo = 0.0;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						//array para comb
						if($return['typeStation'] == 4) {
							/*
							$dataRemoteStations = json_decode($dataRemoteStations[0]);
							foreach($dataRemoteStations as $key => $drs) {
								$data[] = array(
									'type' => $drs->_type,
									'product_id' => $drs->codigo,
									'product' => $drs->descripcion,
									'neto_cantidad' => $drs->neto_cantidad,
									'neto_venta' => $drs->neto_venta,
									'importe_ci' => $drs->importe_ci,
									'cantidad_ci' => $drs->cantidad_ci,
								);
							}
							*/
							$dataRemoteStations = unserialize($dataRemoteStations[0]);
							foreach($dataRemoteStations as $key => $data_) {
								$data[] = array(
									'type' => $data_['_type'],
									'product_id' => $data_['codigo'],
									'product' => $data_['descripcion'],
									'neto_cantidad' => $data_['neto_cantidad'] != '' ? $data_['neto_cantidad'] : 0.0,
									'neto_venta' => $data_['neto_venta'] != '' ? $data_['neto_venta'] : 0.0,
									'importe_ci' => $data_['importe_ci'] != '' ? $data_['importe_ci'] : 0.0,
									'cantidad_ci' => $data_['cantidad_ci'] != '' ? $data_['cantidad_ci'] : 0.0,
								);
								/*if($drs != '') {
									$d = explode("|", $drs);
									$utilidad = $d[7] - $d[6];
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										//'cantidad' => $d[2],
										//'venta' => $d[3],
										'neto_cantidad' => $d[9],
										'neto_venta' => $d[10],
										'importe_ci' => $d[11] != '' ? $d[11] : 0,
										'cantidad_ci' => $d[12] != '' ? $d[12] : 0,
										//'utilidad' => $utilidad,
									);

									$total_cantidad += $d[9];
									$total_precio += $d[10];
									$total_costo += $d[6];
									$total_utilidad += $utilidad;
								}*/
							}
						} else {
							//array para market (sin usar por ahora)
							foreach($dataRemoteStations as $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'neto_cantidad' => $d[2],
										'neto_venta' => $d[5],
										'consumo_galon' => $d[4],//costo
										'utilidad' => $d[6],
									);

									$total_cantidad += $d[2];
									$total_precio += $d[5];
									$total_costo += $d[4];
									$total_utilidad += $d[6];
								}
							}
						}

					} else {
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'url' => $curl,
						'data' => $data,
						'total_qty' => $total_cantidad,
						'total_price' => $total_precio,
						'total_cost' => $total_costo,
						'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		echo json_encode($return);
		unset($return);
	}

	public function getDetailComb()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');

				$typeStation = getDescriptionTypeStation($return['typeStation']);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}

				$mod = '';
				if($return['typeStation'] == 0) {
					$mod = 'DETAIL_SALE_COMB';
				} else {
					$mod = 'DETAIL_SALE_MARKET';
				}

				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&type_cost='.$return['typeCost'];
						$dataRemoteStations = getUncompressData($curl);
					}

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;
					$total_utilidad = 0.0;
					$total_costo = 0.0;

					if($dataRemoteStations != false) {

						$return['status'] = 1;
						//array para comb
						if($return['typeStation'] == 0) {
							foreach($dataRemoteStations as $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$utilidad = $d[7] - $d[6];
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'cantidad' => $d[2],
										'venta' => $d[3],
										'af_cantidad' => $d[4],
										'af_total' => $d[5],
										'consumo_galon' => $d[6],//costo
										'consumo_valor' => $d[7],
										'descuentos' => $d[8],
										'neto_cantidad' => $d[9],
										'neto_venta' => $d[10],
										'utilidad' => $utilidad,
									);

									$total_cantidad += $d[9];
									$total_precio += $d[10];
									$total_costo += $d[6];
									$total_utilidad += $utilidad;
								}
							}
						} else {
							//array para market
							foreach($dataRemoteStations as $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'neto_cantidad' => $d[2],
										'neto_venta' => $d[5],
										'consumo_galon' => $d[4],//costo
										'utilidad' => $d[6],
									);

									$total_cantidad += $d[2];
									$total_precio += $d[5];
									$total_costo += $d[4];
									$total_utilidad += $d[6];
								}
							}
						}

					} else {
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'url' => $curl,
						'data' => $data,
						'total_qty' => $total_cantidad,
						'total_price' => $total_precio,
						'total_cost' => $total_costo,
						'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		echo json_encode($return);
		unset($return);
	}

	public function getDetailProducts()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');
				//$return['formatDateEnd'] = $return['formatDateBegin'];

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');

				$typeStation = getDescriptionTypeStation($return['typeStation']);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				$allProducts = array();
				$allData = array();

				$mod = '';
				if($return['typeStation'] == 0) {
					$mod = 'DETAIL_SALE_COMB';
				} else {
					$mod = 'DETAIL_SALE_MARKET';
				}

				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&type_cost='.$return['typeCost'];
						$dataRemoteStations = getUncompressData($curl);
					}

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;

					$total_costo = 0.0;
					$total_utilidad = 0.0;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						if($return['typeStation'] == 0) {
							//array para comb
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$utilidad = $d[7] - $d[6];
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'cantidad' => $d[2],
										'venta' => $d[3],
										'af_cantidad' => $d[4],
										'af_total' => $d[5],
										'consumo_galon' => $d[6],
										'consumo_valor' => $d[7],
										'descuentos' => $d[8],
										'neto_cantidad' => $d[9],
										'neto_venta' => $d[10],
										'utilidad' => $utilidad,
									);

									$allProducts[$d[0]]['code'] = $d[0];
									$allProducts[$d[0]]['product'] = $d[1];
									$allProducts[$d[0]]['sale'] = 0;
									$allProducts[$d[0]]['qty'] = 0;

									$allProducts[$d[0]]['cost'] = 0;
									$allProducts[$d[0]]['util'] = 0;

									$total_cantidad += $d[9];
									$total_precio += $d[10];

									$total_costo += $d[6];
									$total_utilidad += $utilidad;
								}
							}
						} else {
							//market
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'neto_cantidad' => $d[2],
										'neto_venta' => $d[5],
										'consumo_galon' => $d[4],
										'utilidad' => $d[6],
									);

									$allProducts[$d[0]]['code'] = $d[0];
									$allProducts[$d[0]]['product'] = $d[1];
									$allProducts[$d[0]]['sale'] = 0;
									$allProducts[$d[0]]['qty'] = 0;

									$allProducts[$d[0]]['cost'] = 0;
									$allProducts[$d[0]]['util'] = 0;

									$total_cantidad += $d[2];
									$total_precio += $d[5];

									$total_costo += $d[4];
									$total_utilidad += $d[6];
								}
							}
						}

					} else {
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'url' => $curl,
						'data' => $data,
						'total_qty' => $total_cantidad,
						'total_price' => $total_precio,
						'total_cost' => $total_costo,
						'total_util' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
					$allData[] = $data;
				}

				$pullProducts = array();
				foreach ($allData as $key => $data) {
					foreach ($data as $key => $dat) {
						$allProducts[$dat['product_id']]['sale'] += $dat['neto_venta'];
						$allProducts[$dat['product_id']]['qty'] += $dat['neto_cantidad'];
						$allProducts[$dat['product_id']]['cost'] += $dat['consumo_galon'];
						$allProducts[$dat['product_id']]['util'] += $dat['utilidad'];
					}
				}
				foreach ($allProducts as $key => $allProduct) {
					$pullProducts[] = array(
						'code' => $allProduct['code'],
						'product' => $allProduct['product'],
						'neto_venta' => $allProduct['sale'],
						'neto_cantidad' => $allProduct['qty'],
						'consumo_galon' => $allProduct['cost'],
						'utilidad' => $allProduct['util'],
					);
				}
				$return['dataProducts'] = $pullProducts;
				$return['products'] = $allProducts;

			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($allData);
		unset($pullProducts);

		$return['memory'][] = getMemory(array('unset function'));
		echo json_encode($return);
		unset($return);
	}

	public function getStocks()
	{
		//STOCK_COMB
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			//falta modificar
			if($this->input->post('dateBegin') != null && $this->input->post('daysProm') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['beginDate'] = $this->input->post('dateBegin');
				$return['endDate'] = date('d/m/Y', strtotime(formatDateCentralizer($return['beginDate'],3). ' - 6 days'));

				$return['formatDateBegin'] = formatDateCentralizer($return['beginDate'],1);
				$return['formatDateEnd'] = formatDateCentralizer($return['endDate'],1);
				$return['daysProm'] = $this->input->post('daysProm');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				$return['proyeccion']['checkProyeccion'] = $this->input->post('checkProyeccion');
				$return['proyeccion']['beginDateProyeccion'] = $this->input->post('dateBeginProyeccion');
				$return['proyeccion']['endDateProyeccion'] = $this->input->post('dateEndProyeccion');				
				$fecha1 = DateTime::createFromFormat('d/m/Y', $return['proyeccion']['beginDateProyeccion']);
				$fecha2 = DateTime::createFromFormat('d/m/Y', $return['proyeccion']['endDateProyeccion']);
				$diferencia = $fecha1->diff($fecha2);
				$return['proyeccion']['daysDiffProyeccion'] = $diferencia->days + 1;
				$return['proyeccion']['daysProyeccion'] = is_numeric($this->input->post('daysProyeccion')) ? $this->input->post('daysProyeccion') : "0";

				$typeStation = getDescriptionTypeStation($return['typeStation']);
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				$return['dataStations'] = $dataStations;
				$mod = '';
				if($return['typeStation'] == 0) {
					$mod = 'STOCK_COMB';
				} else {
					$mod = '';
				}

				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateEnd'].'&to='.$return['formatDateBegin'].'&warehouse_id='.$dataStation->almacen_id.'&days='.$return['daysProm'].'&isvaliddiffmonths=si'.'&checkProyeccion='.$return['proyeccion']['checkProyeccion'].'&desdeProyeccion='.$return['proyeccion']['beginDateProyeccion'].'&hastaProyeccion='.$return['proyeccion']['endDateProyeccion'].'&diasDiferenciaProyeccion='.$return['proyeccion']['daysDiffProyeccion'].'&diasProyeccion='.$return['proyeccion']['daysProyeccion'];
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
						error_log(json_encode($dataRemoteStations));
					}

					$data = array();

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						if($return['typeStation'] == 0) {
							//array para comb
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									// | ch_tanque | nu_medicion |  porcentaje_existente   |        dias
									$data[] = array(
										'cod_comb' => $d[0],
										'desc_comb' => $d[1],
										'nu_capacidad' => $d[2],
										'nu_venta_promedio_dia' => $d[3], // Promedio diario vendido en galones
										'ch_tanque' => $d[4],
										'nu_medicion' => $d[5],
										'porcentaje_existente' => $d[6],
										'tiempo_vaciar' => $d[7],
										'suma_ventas_dias' => $d[8],
										'promedio_consumo_dia' => $d[9], // Dias a proyectar
										'cantidad_ultima_compra' => $d[10],
										'fecha_ultima_compra' => date("d/m/Y", strtotime($d[11])),
										'fecha_ultima_compra_o' => $d[11],
										'nu_venta_proyeccion_dia' => $d[14], // Proyeccion: Promedio diario vendido en galones * Dias a proyectar
										'costo_comb' => $d[15], // Costo Promedio
										'precio_venta' => $d[16], // Ultimo Precio de Venta
									);
								}
							}
						}
					} else {
						$return['status'] = 4;
					}
					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'isConnection' => $return['status'] == 4 ? false : true,
						'data' => $data,
					);
				}

			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));

		unset($curl);
		unset($dataStations);
		unset($dataRemoteStations);
		$return['memory'][] = getMemory(array('unset function'));
		echo json_encode($return);
		unset($return);
	}

	public function getStationLines() {
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');
				//$return['formatDateEnd'] = $return['formatDateBegin'];

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');

				$typeStation = getDescriptionTypeStation($return['typeStation']);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				$allProducts = array();
				$allData = array();

				$mod = '';
				if($return['typeStation'] == 0) {
					$mod = 'DETAIL_SALE_COMB';//se esta usando?
				} else {
					$mod = 'UTILITY_LINES';
					//$mod = 'DETAIL_SALE_MARKET';
					//$mod = 'DETAIL_PRODUCTS_LINE';
				}

				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&type_cost='.$return['typeCost'];
						$dataRemoteStations = getUncompressData($curl);
					}

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;

					$total_costo = 0.0;
					$total_utilidad = 0.0;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						if($return['typeStation'] == 0) {
							//array para comb
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$utilidad = $d[7] - $d[6];
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'cantidad' => $d[2],
										'venta' => $d[3],
										'af_cantidad' => $d[4],
										'af_total' => $d[5],
										'consumo_galon' => $d[6],
										'consumo_valor' => $d[7],
										'descuentos' => $d[8],
										'neto_cantidad' => $d[9],
										'neto_venta' => $d[10],
										'utilidad' => $utilidad,
									);

									$allProducts[$d[0]]['code'] = $d[0];
									$allProducts[$d[0]]['product'] = $d[1];
									$allProducts[$d[0]]['sale'] = 0;
									$allProducts[$d[0]]['qty'] = 0;

									$allProducts[$d[0]]['cost'] = 0;
									$allProducts[$d[0]]['util'] = 0;

									$total_cantidad += $d[9];
									$total_precio += $d[10];

									$total_costo += $d[6];
									$total_utilidad += $utilidad;
								}
							}
						} else {
							//market
							$dataRemoteStations = json_decode($dataRemoteStations[0]);
							foreach($dataRemoteStations as $key => $drs) {
								//var_dump($drs);
								if($drs->co_linea != '') {

									/*
									codigo_linea as co_linea,
									FIRST(nombre_linea) as no_linea,
									SUM(qt_cantidad) AS nu_cantidad,
									0.0 AS nu_costo_promedio,
									SUM(ss_kardex_promedio_total) AS nu_costo_total,
									SUM(ss_tickets_sigv_total) AS nu_venta_soles,
									SUM(ss_tickets_sigv_total) - SUM(ss_kardex_promedio_total) AS nu_margen


									$data[] = array(
										'product_id' => $drs->co_linea,
										'product' => $drs->no_linea,
										'product_name' => utf8_decode($drs->no_producto),
										'neto_cantidad' => $drs->nu_cantidad,
										'neto_venta' => $drs->nu_venta_soles,
										'consumo_galon' => $drs->nu_costo_total,
										'utilidad' => $drs->nu_margen,
									);

									$total_cantidad += $drs->nu_cantidad;
									$total_precio += $drs->nu_venta_soles;

									$total_costo += $drs->nu_costo_total;
									$total_utilidad += $drs->nu_margen;
									

									//$d = explode("|", $drs);
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'neto_cantidad' => $d[2],
										'neto_venta' => $d[5],
										'consumo_galon' => $d[4],
										'utilidad' => $d[6],
									);
									*/

									/*
									codigo_linea as co_linea,
									FIRST(nombre_linea) as no_linea,
									SUM(qt_cantidad) AS nu_cantidad,
									0.0 AS nu_costo_promedio,
									SUM(ss_kardex_promedio_total) AS nu_costo_total,
									SUM(ss_tickets_sigv_total) AS nu_venta_soles,
									SUM(ss_tickets_sigv_total) - SUM(ss_kardex_promedio_total) AS nu_margen
									*/

									$data[] = array(
										'product_id' => $drs->co_linea,
										'product' => $drs->no_linea,
										'neto_cantidad' => $drs->nu_cantidad,
										'neto_venta' => $drs->nu_venta_soles,
										'consumo_galon' => $drs->nu_costo_total,
										'utilidad' => $drs->nu_margen,
									);

									$allProducts[$drs->co_linea]['code'] = $drs->co_linea;
									$allProducts[$drs->co_linea]['product'] = $drs->no_linea;
									$allProducts[$drs->co_linea]['sale'] = 0;
									$allProducts[$drs->co_linea]['qty'] = 0;

									$allProducts[$drs->co_linea]['cost'] = 0;
									$allProducts[$drs->co_linea]['util'] = 0;

									$total_cantidad += $drs->nu_cantidad;
									$total_precio += $drs->nu_venta_soles;

									$total_costo += $drs->nu_costo_total;
									$total_utilidad += $drs->nu_margen;
								}
							}
						}

					} else {
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'url' => $curl,
						'data' => $data,
						'total_qty' => $total_cantidad,
						'total_price' => $total_precio,
						'total_cost' => $total_costo,
						'total_util' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
					$allData[] = $data;
				}

				$pullProducts = array();
				foreach ($allData as $key => $data) {
					foreach ($data as $key => $dat) {
						$allProducts[$dat['product_id']]['sale'] += $dat['neto_venta'];
						$allProducts[$dat['product_id']]['qty'] += $dat['neto_cantidad'];
						$allProducts[$dat['product_id']]['cost'] += $dat['consumo_galon'];
						$allProducts[$dat['product_id']]['util'] += $dat['utilidad'];
					}
				}
				foreach ($allProducts as $key => $allProduct) {
					$pullProducts[] = array(
						'code' => $allProduct['code'],
						'product' => $allProduct['product'],
						'neto_venta' => $allProduct['sale'],
						'neto_cantidad' => $allProduct['qty'],
						'consumo_galon' => $allProduct['cost'],
						'utilidad' => $allProduct['util'],
					);
				}
				$return['dataProducts'] = $pullProducts;
				$return['products'] = $allProducts;

			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($allData);
		unset($pullProducts);

		$return['memory'][] = getMemory(array('unset function'));
		echo json_encode($return);
		unset($return);
	}

	function getStationProductsLine() {
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('startDate') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null && $this->input->post('lineId') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('startDate'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('endDate'),1);
				$return['typeCost'] = $this->input->post('typeCost');
				$return['lineName'] = $this->input->post('lineName');
				//$return['formatDateEnd'] = $return['formatDateBegin'];

				$return['startDate'] = $this->input->post('startDate');
				$return['endDate'] = $this->input->post('endDate');
				$return['typeStation'] = $this->input->post('typeStation');

				$return['lineId'] = $this->input->post('lineId');

				$typeStation = getDescriptionTypeStation($return['typeStation']);

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndId($typeStation == 'MP' ? 'C' : $typeStation,$this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType($typeStation == 'MP' ? 'C' : $typeStation);
					}
				}
				$allProducts = array();
				$allData = array();

				$mod = '';
				if($return['typeStation'] == 0) {
					$mod = 'DETAIL_SALE_COMB';
				} else {
					//$mod = 'DETAIL_PRODUCTS_LINE';
					$mod = 'UTILITY_LINES_DETAIL';
				}

				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php';
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&type_cost='.$return['typeCost'].'&line_id='.$return['lineId'];
						$dataRemoteStations = getUncompressData($curl);
					}

					$data = array();
					$total_cantidad = 0.0;
					$total_precio = 0.0;

					$total_costo = 0.0;
					$total_utilidad = 0.0;

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						if($return['typeStation'] == 0) {
							$dataRemoteStations = unserialize($dataRemoteStations[0]);
							//NO USADO PARA LINEA
							//array para comb
							foreach($dataRemoteStations as $key => $drs) {
								if($drs != '') {
									$d = explode("|", $drs);
									$utilidad = $d[7] - $d[6];
									$data[] = array(
										'product_id' => $d[0],
										'product' => $d[1],
										'cantidad' => $d[2],
										'venta' => $d[3],
										'af_cantidad' => $d[4],
										'af_total' => $d[5],
										'consumo_galon' => $d[6],
										'consumo_valor' => $d[7],
										'descuentos' => $d[8],
										'neto_cantidad' => $d[9],
										'neto_venta' => $d[10],
										'utilidad' => $utilidad,
									);

									$allProducts[$d[0]]['code'] = $d[0];
									$allProducts[$d[0]]['product'] = $d[1];
									$allProducts[$d[0]]['sale'] = 0;
									$allProducts[$d[0]]['qty'] = 0;

									$allProducts[$d[0]]['cost'] = 0;
									$allProducts[$d[0]]['util'] = 0;

									$total_cantidad += $d[9];
									$total_precio += $d[10];

									$total_costo += $d[6];
									$total_utilidad += $utilidad;
								}
							}
						} else {
							//$dataRemoteStations = unserialize($dataRemoteStations[0]);
							//market
							$dataRemoteStations = json_decode($dataRemoteStations[0]);
							//var_dump($dataRemoteStations);
							foreach($dataRemoteStations as $key => $drs) {
								//var_dump($drs);
								/*if($drs != '') {
									$d = explode("|", $drs);*/
									$data[] = array(
										'product_id' => $drs->co_linea,
										'product' => $drs->no_linea,
										'product_name' => $drs->no_producto,
										'neto_cantidad' => $drs->nu_cantidad,
										'neto_venta' => $drs->nu_venta_soles,
										'consumo_galon' => $drs->nu_costo_total,
										'utilidad' => $drs->nu_margen,
									);

									$total_cantidad += $drs->nu_cantidad;
									$total_precio += $drs->nu_venta_soles;

									$total_costo += $drs->nu_costo_total;
									$total_utilidad += $drs->nu_margen;
								//}
							}
						}

					} else {
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'url' => $curl,
						'data' => $data,
						'total_qty' => $total_cantidad,
						'total_price' => $total_precio,
						'total_cost' => $total_costo,
						'total_util' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);

				}

			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($allData);
		unset($pullProducts);

		$return['memory'][] = getMemory(array('unset function'));
		echo json_encode($return);
		unset($return);
	}

	function getMovementsByOrgId() {
		$return = array();
		$movement = array();
		$dataStation = array();
		$return['memory'][] = getMemory(array('start function'));
		$stations = array();
		if (checkSession()) {
			if (isset($_POST['orgId'], $_POST['startDate'], $_POST['endDate'])) {
				$this->load->model('IMovement_model');
				$params = array(
					$_POST['orgId'],
					formatDateCentralizer($_POST['startDate'], 3).' 00:00:00',
					formatDateCentralizer($_POST['endDate'], 3).' 23:59:59'
				);
				$this->load->model('COrg_model');

				$orgs = array();
				$orgValue = $this->COrg_model->getOrgRelationId($params[0], 'M');
				//falta consultar las lineas en las que centralizan los movimientos, y enviarlo en un array

				foreach ($orgValue as $key => $value) {
					$orgs[] = $value->c_org_id;
				}
				$orgs = implode(",", $orgs);
				$params[0] = $orgs;


				$products_ = array();
				$products = array();
				$products = $this->IMovement_model->getProductgroupByMoviments($params);
				$_products = array();
				$_orgs = array();
				if ($products != null) {
					foreach ($products as $key => $value) {
						$products_[] = $value->c_product_id;
						$products[] = $value;
						$_products[$value->c_product_id] = $value;
					}
					$products_ = implode(",", $products_);
					$params[3] = $products_;
					$concatProducts = $params[3];

					$dataProducts = array();

					foreach ($orgValue as $key => $org) {
						$params[0] = $org->c_org_id;

						/*foreach ($products as $key => $product) {
							$params[3] = $product->c_product_id;
							$movements = $this->IMovement_model->getByOrgId($params);
							if($movements != null) {
								foreach ($movements as $key => $movement) {
									$movements_[$movement->c_product_id] = array(
										'c_org_id' => $movement->c_org_id,
										'productgroup_code' => $movement->productgroup_code,
										'productgroup_name' => $movement->productgroup_name,
										'product_name' => $movement->product_name,
										'uom_name' => $movement->uom_name,
										'_countsale' => $movement->_countsale,
										'_stk_real' => $movement->_stk_real,
									);
								}
							} else {
								$movements_[$product->c_product_id] = array(
									'c_org_id' => $params[0],
									'productgroup_code' => '',
									'productgroup_name' => '',
									'product_name' => '',
									'uom_name' => '',
									'_countsale' => 0.0,
									'_stk_real' => 0.0,
								);
							}
						}*/

						$movements = $this->IMovement_model->getByOrgId($params);
						foreach ($products as $key => $product) {
							$prod = array(
								'c_org_id' => $params[0],
								'productgroup_code' => '',
								'productgroup_name' => '',
								'product_name' => '',
								'uom_name' => '',
								'_countsale' => 0.0,
								'_stk_real' => 0.0,
								'_amountsale' => 0.0,
								'_amount_real' => 0.0,
							);
							//$movements_[$product->c_product_id] = $prod;
							$_dataProducts[$product->c_product_id][$org->c_org_id] = $prod;
							$_orgs[$org->c_org_id] = $org;

							//if ($movements != null) {
								foreach ($movements as $key => $movement) {
									$prod = array(
										'c_org_id' => $movement->c_org_id,
										'product_id' => $movement->c_product_id,
										'productgroup_code' => $movement->productgroup_code,
										'productgroup_name' => $movement->productgroup_name,
										'product_name' => $movement->product_name,
										'uom_name' => $movement->uom_name,
										'_countsale' => $movement->_countsale,
										'_stk_real' => $movement->_stk_real,
										'_amountsale' => $movement->_amountsale,
										'_amount_real' => $movement->_amount_real,
									);
									//$movements_[$movement->c_product_id] = $prod;
									$_dataProducts[$movement->c_product_id][$movement->c_org_id] = $prod;
									$_orgs[$org->c_org_id] = $org;
								}
							/*} else {
								$_dataProducts[$product->c_product_id][$org->c_org_id] = $prod;
								$_orgs[$org->c_org_id] = $org;
							}*/
						}

						/*$dataStation[] = array(
							'name' => $org->name,
							'c_org_id' => $org->c_org_id,
							'data' => $movements_,
						);*/
					}

					$return = array(
						'status' => 4,
						'params' => $params,
						'stations' => $stations,
						'_products' => $_products,
						'products' => $products,
						//'dataStation' => $dataStation,
						'movements' => $movements,
						'post' => $_POST,
						'orgs' => $orgs,
						'_orgs' => $_orgs,
						'concatProducts' => $concatProducts,
						'_dataProducts' => $_dataProducts,
						'reload' => $reLoad,
					);
				} else {
					$return = array(
						'status' => 5,
						'params' => $params,
						'post' => $_POST,
						'orgs' => $orgs,
					);
				}


			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return = array(
				'status' => 101,
				'messages' => 'No existe sesión.',
			);
		}
		$return['memory'][] = getMemory(array('end function'));
		echo json_encode($return);
	}

	public function getValesClientesCredito()
	{
		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				$typeStation = getDescriptionTypeStation($return['typeStation']);		
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndIdFleets(1, $this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByTypeFleets(1);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				/*Obtener RUCs por usuario logueado*/
				$this->load->model('CPrivilege_model');
				$clientes = $this->CPrivilege_model->getRucByUser($_SESSION['user_id']);

				$clientes_ = '';
				foreach ($clientes as $key => $cliente) {
					$clientes_ .= $cliente->value . "|";
				}
				$clientes_ = substr($clientes_, 0, -1);

				error_log("RUCs por usuario logueado");
				error_log(json_encode($clients));
				error_log(json_encode($clients_));
				/*Cerra Obtener RUCs por usuario logueado*/

				$mod = '';
				if($return['typeStation'] == 0) {
					$mod = 'VALES_CLIENTES_CREDITO'; //
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/	
				
				$listJson = array();
				$countJson = 0;

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&desde='.$return['dateBegin'].'&hasta='.$return['dateEnd'].'&clientes='.$clientes_;
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$data = $dataRemoteStations;

						//ARRAY PARA DATATABLES
						foreach ($data['vales_clientes_credito'] as $key => $vales) {
							$hora = $vales['hora'];
							$hora_replicacion = $vales['hora_replicacion'];

							$listJson[] = array(
								"0" => $countJson,
								"1" => $vales['fecha'] . " " . (!empty($hora) ? $hora : $hora_replicacion) ,
								"2" => $dataStation->name,
								"3" => $vales['placa'],
								"4" => $vales['odometro'],
								"5" => $vales['chofer'],								
								"6" => $vales['producto'],
								"7" => number_format($vales['cantidad'], 3, '.', ','), //number_format($vales['cantidad'], 3, '.', ',')
								"8" => number_format($vales['importe'], 2, '.', ','),  //number_format($vales['importe'], 2, '.', ',')
								"9" => $vales['documento'],
								"10" => TRIM($vales['codcliente']) . " - " . $vales['nomcliente']
							);
							$countJson++;
						}
						//CERRAR ARRAY PARA DATATABLES						
					}else{
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'data' => $data,
						// 'total_qty' => $total_cantidad,
						// 'total_price' => $total_precio,
						// 'total_cost' => $total_costo,
						// 'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
				$return['listJson'] = $listJson;
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);		
	}

	public function getComprobantesCobranza()
	{
		// $return = array();
		// $return['listJson'] = array();
		// echo json_encode($return);

		$return = array();
		$return['memory'][] = getMemory(array('start function'));
		if(checkSession()) {
			if($this->input->post('dateBegin') != null && $this->input->post('id') != null && $this->input->post('typeStation') != null) {
				$return['status'] = 1;
				$return['formatDateBegin'] = formatDateCentralizer($this->input->post('dateBegin'),1);
				$return['formatDateEnd'] = formatDateCentralizer($this->input->post('dateEnd'),1);
				$return['typeCost'] = $this->input->post('typeCost');

				$return['dateBegin'] = $this->input->post('dateBegin');
				$return['dateEnd'] = $this->input->post('dateEnd');
				$return['typeStation'] = $this->input->post('typeStation');
				$return['id'] = $this->input->post('id');

				$return['ruc'] = $this->input->post('ruc');
				$return['state'] = $this->input->post('state');

				$typeStation = getDescriptionTypeStation($return['typeStation']);		
				error_log("Parametros en variable return y typeStation");
				error_log(json_encode(array($return, $typeStation)));

				$this->load->model('COrg_model');
				$isAllStations = true;
				if($this->input->post('id') != '*') {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getOrgByTypeAndIdFleets(1, $this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByTypeFleets(1);
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				/*Obtener RUCs por usuario logueado*/
				// $this->load->model('CRuc_model');
				// $clientes = $this->CRuc_model->getRucByClient($_SESSION['user_id']);
	
				// $clientes_ = '';
				// foreach ($clientes as $key => $cliente) {
				// 	$clientes_ .= $cliente->value . "|";
				// }
				// $clientes_ = substr($clientes_, 0, -1);
	
				// error_log("RUCs por usuario logueado");
				// error_log(json_encode($clients));
				// error_log(json_encode($clients_));
				/*Cerra Obtener RUCs por usuario logueado*/

				$mod = '';
				if($return['typeStation'] == 1) {
					$mod = 'COMPROBANTES_COBRANZA';
				} else {
					$mod = 'ERR';
				}
				$return['mode'] = $mod;

				/*echo json_encode(array('http' => $mod));
				exit;*/

				$listJson = array();
         	$countJson = 0;

				error_log("****** Recorremos estaciones cargadas ******");
				foreach($dataStations as $key => $dataStation) {
					if($isAllStations) {
						$curl = 'http://'.$dataStation->ip.'/sistemaweb/centralizer_.php'; //CAMBIAR URL PARA PRUEBAS
						$curl = $curl . '?mod='.$mod.'&from='.$return['formatDateBegin'].'&to='.$return['formatDateEnd'].'&warehouse_id='.$dataStation->almacen_id.'&desde='.$return['dateBegin'].'&hasta='.$return['dateEnd'].'&ruc='.$return['ruc'].'&state='.$return['state'];
						error_log("Url de la estacion cargada");
						error_log($curl);
						$dataRemoteStations = getUncompressData($curl);
					}
					error_log("Data de la estacion cargada");
					error_log(json_encode($dataRemoteStations));

					$return['curl'][] = $curl;

					$data = array();
					$dataRemoteStations = json_decode($dataRemoteStations[0], true);

					if($dataRemoteStations != false) {
						$return['status'] = 1;
						$data = $dataRemoteStations;

						//ARRAY PARA DATATABLES
						foreach ($data['comprobantes_cobranza'] as $key => $comprobantes) {
							$listJson[] = array(
								"0" => $countJson,
								"1" => $comprobantes['ch_tipdocumento'] . " " . $comprobantes['ch_seriedocumento'] . "-" . $comprobantes['ch_numdocumento'],								
								"2" => $comprobantes['cli_codigo'],
								"3" => $comprobantes['dt_fechaemision'],
								"4" => $dataStation->name,								
								"5" => $comprobantes['nu_dias_vencimiento'],								
								"6" => $comprobantes['dt_fechavencimiento'],
								"7" => $comprobantes['ch_moneda'],
								"8" => $comprobantes['nu_importetotal'],
								"9" => $comprobantes['nu_importepagos'],
								"10" => $comprobantes['nu_importesaldo'],
								"11" => $comprobantes['dt_fechasaldo'],
							);
							$countJson++;
						}
						//CERRAR ARRAY PARA DATATABLES						
					}else{
						$return['status'] = 4;
					}

					$return['stations'][] = array(
						'name' => $dataStation->name,
						'initials' => $dataStation->initials == null ? '<s/n>' : $dataStation->initials,
						'group' => array('taxid' => $dataStation->taxid, 'name' => $dataStation->client_name),
						'url' => $curl,
						'id' => $dataStation->c_org_id,
						'warehouse_id' => $dataStation->almacen_id,
						'data' => $data,
						// 'total_qty' => $total_cantidad,
						// 'total_price' => $total_precio,
						// 'total_cost' => $total_costo,
						// 'margin' => $total_utilidad,
						'isConnection' => $return['status'] == 4 ? false : true
					);
				}
				$return['listJson'] = $listJson;
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else {
			$return['status'] = 101;
			$return['message'] = 'No existe sesión.';
		}
		$return['memory'][] = getMemory(array('end function'));
		unset($curl);
		unset($typeStation);
		unset($dataStations);
		unset($dataRemoteStations);
		unset($data);

		$return['memory'][] = getMemory(array('unset function'));
		error_log("return final");
		error_log(json_encode($return));
		echo json_encode($return);
		unset($return);
	}

	function decode() {
		echo <<< EOT
<form action="/ocsmanager/index.php/requests/getdecode" method="POST">
	<input type="text" style="width: 100%" name="url">
	<input type="submit">
</form>
EOT;
	}

	function getdecode() {
		$curl = $_POST['url'];
		$dataRemoteStations = $this->getUncompressData($curl);
		echo '<pre>';
		var_dump($dataRemoteStations);
		echo '</pre>';
		echo '<pre>';
		var_dump($dataRemoteStations[0]);
		echo '</pre>';
		if ($dataRemoteStations != false) {
			$dataRemoteStations = json_decode($dataRemoteStations[0]);
			echo '--><br>';
			var_dump($dataRemoteStations);
		}
	}

	function centralizations() {
		$return = array();
		$movement = array();
		$dataStation = array();
		$return['memory'][] = getMemory(array('start function'));
		$stations = array();
		if (checkSession()) {
			$this->load->model('COrg_model');
			$centralizations = $this->COrg_model->getCentralizationsByOrgId(
				array(
					'created' => date('Y-m-d').' 00:00:00',
					'c_org_id' => '*',
				)
			);
			$return = array(
				'status' => 1,
				'centralizations' => $centralizations,
			);
		} else {
			$return = array(
				'status' => 101,
				'messages' => 'No existe sesión.',
			);
		}
		$return['memory'][] = getMemory(array('end function'));
		echo json_encode($return);
	}

	function checkRequest() {
		if(isset($_REQUEST['ip'])) {
			$curl = 'http://'.$_REQUEST['ip'].'/sistemaweb/centralizer.php';
			if((isset($_REQUEST['ip']) && isset($_REQUEST['mod']) && isset($_REQUEST['from']) && isset($_REQUEST['to']))) {
				echo 'MODO 1<br>';
				$curl = $curl . '?mod='.$_REQUEST['mod'].'&from='.$_REQUEST['from'].'&to='.$_REQUEST['to'];
			} else if($_REQUEST['mod'] == 'BI' || $_REQUEST['mod'] == 'PI') {
				echo 'MODO 2<br>';
				$curl = $curl . '?mod='.$_REQUEST['mod'].'&sk='.$_REQUEST['sk'];
			} else {
				echo 'MODO 2<br>';
				$curl = $curl . '?mod='.$_REQUEST['mod'].'&doctype='.$_REQUEST['doctype'].'&documentserial='.$_REQUEST['documentserial'];
			}
			echo 'URL: '.$curl;
			$dataRemoteStations = getUncompressData($curl);
			var_dump($dataRemoteStations);
			echo '<hr>';
			echo json_encode($dataRemoteStations);
		} else {
			var_dump($_REQUEST);
		}
	}

	/*
	funciones de pruebas
	*/
	function demo()
	{
		$cturl = 'http://192.168.4.1/sistemaweb/centralizer_.php';
		$dt = '20161201';
		//echo $cturl . "?mod=ID&from={$dt}&to={$dt}";
		$ctdata = $this->getUncompressData($cturl . "?mod=ID&from={$dt}&to={$dt}");

		//$ctdata = getCentralizedData($cturl . "?mod=ID&from={$dt}&to={$dt}");
		echo '<br>';
		echo '<hr>$ctdata :<br>';
		var_dump($ctdata);
		echo '<hr><br>';

		foreach ($ctdata as $crv) {
			$cr = explode("|", $crv);
			echo '<br>';
			var_dump($cr);
			echo '<hr>'.$cr[0].'<hr>';
		}
	}

	function demoserialize()
	{
		$cturl = 'http://192.168.4.1/sistemaweb/centralizer_.php';
		$ctdata = $this->getUncompressData($cturl . "?mod=DEMO_SERIAL");

		$ctdata = unserialize($ctdata[0]);

		echo '<br>';
		echo '<hr>$ctdata :<br>';
		var_dump($ctdata);
		echo '<hr><br>';

		for ($i = 0; $i < count($ctdata); $i++) { 
			var_dump($ctdata['name']);
		}
	}

	function demoserialize2()
	{
		$cturl = 'http://192.168.4.1/sistemaweb/centralizer_.php';
		$ctdata = $this->getUncompressData($cturl . "?mod=DEMO_");

		$ctdata = unserialize($ctdata[0]);

		echo '<br>';
		echo '<hr>$ctdata :<br><pre>';
		var_dump($ctdata);
		echo '</pre><hr><br>';

		echo 'count: '.count($ctdata).'<br>';
		foreach ($ctdata as $k => $v) {
			echo 'Codigo: '.$v[0];
			echo '<br>';
		}

		echo '<br>';
	}


	function getUncompressData($url) {
		$old = ini_set('default_socket_timeout', 120);
		//$old = ini_set('default_socket_timeout', 5);
		$fh = fopen($url, 'rb');
		if ($fh === FALSE) {
			log_message('Error', 'Error al conectarse a '.$url.' $fh: '.$fh);
			return FALSE;
		}
		$res = '';
		while (!feof($fh)) {
			$res .= fread($fh, 8192);
		}
		fclose($fh);
		$descomprimido = gzuncompress($res);
		return explode("\n", $descomprimido);
	}
}