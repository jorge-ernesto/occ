<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 * Global Status Requests
 * 1: ok
 * 4: error al acceder al servidor
 * [2-99] depende de cada peticion
 * 100: datos imcompletos
 * 101: no estas logueado
 */

class Rucs extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');	
		$this->load->model('ADUser_model');	
		$this->load->model('CRuc_model');	
		$this->load->helper('functions');
	}

	public function index()
	{
		//mensaje de error
	}

	public function create($id_usuario){
		// echo "Metodo edit";
		// echo $id_usuario;
		// return;

		$edit=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');								
			}else{
					$edit=true;						
			}				
		}

		if($edit==true){
			if(is_numeric($id_usuario)){
				//Validamos que exista el usuario
				$data['user'] = $this->ADUser_model->findUser($id_usuario);
				if(!$data['user']){
					redirect('secure/login', 'location');
				}
				
				//Redirecciono la pagina a la url por defecto
				$data['title'] = 'Seguridad > Usuarios';			
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('rucs/create', $data);		
			}else{
				redirect('secure/login', 'location');
			}  
		}		
	}

	public function store(){
		// echo "Metodo store";
		// echo "<pre>";
		// print_r($this->input->post());
		// echo "</pre>";
		// return;
		
		$store=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');								
			}else{
					$store=true;
			}
		}

		if($store==true){
			//Compruebo si se a enviado submit
			if($this->input->post("submit")){
    
					//Llamo al metodo add
					$add=$this->CRuc_model->storeRUC(
									$this->input->post("sec_user_id"),
									$this->input->post("name"),
									$this->input->post("razon_social")
					);
			}
			if($add==true){
				//Sesion de una sola ejecución
				$this->session->set_flashdata('correcto', 'RUC añadido correctamente');
			}else{
				$this->session->set_flashdata('incorrecto', 'RUC no se pudo añadir');
			}
				
			//Redirecciono la pagina a la url por defecto	
			redirect('users/edit/'. $this->input->post("sec_user_id"), 'location');		
		}		
	}
}