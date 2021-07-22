<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuration extends CI_Controller {
	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('CClient_model');
		$this->load->model('COrg_model');
		$this->load->model('IWarehouse_model');
		$this->load->helper('functions');
	}

	public function index() {
		//Revisar autenticación
		$data['title'] = 'Configuración OCSManager';
		$data['result_c_client'] = $this->CClient_model->getAllClient();
		$data['section'] = 'client';

		$this->load->view('configuration/index',$data);
	}

	public function client() {
		//Revisar autenticación
		$data['title'] = 'Configuración OCSManager';
		$data['result_c_client'] = $this->CClient_model->getAllClient();
		$data['section'] = 'client';

		$this->load->view('configuration/index',$data);
	}

	public function org() {
		//Revisar autenticación
		$data['title'] = 'Configuración OCSManager';
		$data['result_c_org'] = $this->COrg_model->getAll();
		$data['section'] = 'org';

		$this->load->view('configuration/org',$data);
	}

	public function warehouse() {
		//Revisar autenticación
		$data['title'] = 'Configuración OCSManager';
		$data['result_i_warehouse'] = $this->IWarehouse_model->getAllWarehouse();
		$data['section'] = 'warehouse';

		$this->load->view('configuration/warehouse',$data);
	}

	public function viewClientAdd() {
		$this->load->view('partials/client_add.php');
	}
}