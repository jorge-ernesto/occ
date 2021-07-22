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
						$dataStations = $this->COrg_model->getOrgByTypeAndId($this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType();
					}
				}
				error_log("Estaciones cargadas");
				error_log(json_encode($dataStations));

				/*Obtener RUCs por usuario logueado*/
				$this->load->model('CRuc_model');
				$clientes = $this->CRuc_model->getRucByClient($_SESSION['user_id']);

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
					$mod = 'VALES_CLIENTES_CREDITO';
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
							$listJson[] = array(
								"0" => $countJson,
								"1" => $vales['fecha'] . " " . $vales['hora'],								
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
						$dataStations = $this->COrg_model->getOrgByTypeAndId($this->input->post('id'));
					}
				} else {
					if($isAllStations) {
						$dataStations = $this->COrg_model->getCOrgByType();
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