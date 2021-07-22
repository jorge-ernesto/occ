<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Flotas extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('COrg_model');
		$this->load->model('CRuc_model');
		$this->load->helper('functions');
	}

	public function despachos()
	{
		/*echo '<pre>';
		var_dump($this->COrg_model->usuariosIntegrado());
		echo '</pre>';*/
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Consultas > Despachos';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg();

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 0;
			$this->load->view('flotas/despachos',$data);
		}
	}

	public function comprobantes_cobranza()
	{
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Consultas > Comprobantes de Cobranza';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg();
			$data['result_c_client'] = $this->CRuc_model->getRucByClient($_SESSION['user_id']);

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 1;
			$this->load->view('flotas/comprobantes_cobranza',$data);
		}
	}
}
