<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model('COrg_model');
		$this->load->helper('functions');
	}

	public function index()
	{
		/*echo '<pre>';
		var_dump($this->COrg_model->usuariosIntegrado());
		echo '</pre>';*/
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports'] || $_SESSION['FleetReports']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');								
			}else{
				//AQUI TAMBIEN PODRIA IR UNA VISTA HOME DE BIENVENIDA, LO DEJAMOS PENDIENTE		
				if($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['FleetReports']) {

					redirect('flotas/despachos', 'location');
				}else if($_SESSION['Superuser'] || $_SESSION['Admin'] || $_SESSION['OrgReports']) {

					redirect('ventas/combustibles', 'location');
				}
			}
		}
	}

	function centralizations() {
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$data['title'] = 'Home > Centralizaciones';
			$data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

			$this->load->helper('functions');
			$data['default_start_date'] = getDateDefault('d/m/Y');

			$data['typeStation'] = 0;
			$this->load->view('ventas/combustibles',$data);
		}
	}
}
