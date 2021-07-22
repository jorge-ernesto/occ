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
		$this->load->helper('functions');
	}

	public function clientes_credito()
	{
		/*echo '<pre>';
		var_dump($this->COrg_model->usuariosIntegrado());
		echo '</pre>';*/
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Consultas > Cliente Credito';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg();

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 0;
			$this->load->view('flotas/clientes_credito',$data);
		}
	}
}
