<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stocks extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('COrg_model');
		$this->load->helper('functions');
	}

	public function diario()
	{	
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Stocks > Diario';
			$data['name'] = 'Stock Diario';
			$data['actions'] = array(
				'submit' => 'btn-search-stock'
			);
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 0;
			$this->load->view('stocks/diario',$data);
		}
	}

	/**
	 * Último método desarollado(Solicitado por Copetrol)
	 */
	public function mercaderias() {
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Stocks > Mercaderías';
			$data['name'] = 'Mercaderías';
			$data['actions'] = array(
				'submit' => 'btn-search-merchandise'
			);
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('M');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 1;
			$this->load->view('stocks/mercaderias',$data);
		}
	}
}