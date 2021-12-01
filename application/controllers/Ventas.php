<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('COrg_model');
		$this->load->helper('functions');
	}

	public function combustibles()
	{	
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Combustibles';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('ventas/combustibles',$data);
			}
		}
	}

	public function market()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Market Tienda';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('M');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 1;
				$this->load->view('ventas/market',$data);
			}
		}
	}

	public function market_playa()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Market Playa';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 2;
				$this->load->view('ventas/market_playa',$data);
			}
		}
	}

	public function resumen()
	{	
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Resumen';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 3;
				$this->load->view('ventas/resumen',$data);
			}
		}
	}

	public function ventas_horas()
	{	
		error_log("Ventas por Horas");		

		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Ventas por Horas';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');			

				$data['typeStation'] = 6;

				error_log(json_encode($data));
				$this->load->view('ventas/ventas_horas',$data);
			}
		}
	}

	public function liquidacion_diaria()
	{	
		error_log("Liquidacion diaria");		
		error_log(json_encode( $this->input->get() ));

		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Liquidacion Diaria';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');			

				$data['typeStation'] = 7;

				error_log(json_encode($data));
				$this->load->view('ventas/liquidacion_diaria',$data);
			}
		}
	}

	public function estadistica()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Estadística';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');
				$data['previous_start_date'] = getDatePrevious('d/m/Y',2);

				$data['typeStation'] = 4;
				$this->load->view('ventas/estadistica',$data);
			}
		}
	}

	public function market_productos_linea()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Productos por Línea (MT)';
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('M');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 5;
				$this->load->view('ventas/market_productos_linea',$data);
			}
		}
	}

	/**
	 * Mercaderia representado en moneda
	 * Add 2017-12-27
	 */
	public function mercaderias()
	{
		if (!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) ? 1 : 0;
			if(!$privilege) {
				$this->error_404();
			}else{
				$data['title'] = 'Ventas > Mercaderías';
				$data['name'] = 'Mercaderías';
				$data['actions'] = array(
					'submit' => 'btn-search-merchandise-sale'
				);
				$data['result_c_org'] = $this->COrg_model->getAllCOrg('M');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 1;
				$this->load->view('ventas/mercaderias',$data);
			}
		}
	}

	public function error_404()
	{
		$data['heading'] = "404 Page Not Found";
		$data['message'] = "The page you requested was not found.";
		$this->load->view('errors/html/error_404', $data);
	}
}