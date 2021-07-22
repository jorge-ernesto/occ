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

	public function list($id_usuario){
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');
			}else{
				$rucs = $this->CRuc_model->listRUC($id_usuario);
				// error_log(json_encode($rucs));		
				
				$listJson = array();
				foreach ($rucs as $key => $ruc) {
					$listJson[] = array(
						"0" => $ruc->cnf_client_id,
						"1" => $ruc->razon_social,
						"2" => $ruc->ruc,
						"3" => '<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal_delete_'.$ruc->cnf_client_id.'">Eliminar</button>
									
									<!-- Modal -->
									<div class="modal fade animated fadeIn" id="modal_delete_'.$ruc->cnf_client_id.'">
										<div class="modal-dialog" role="document">                    
											<div class="modal-content text-dark">
													<div class="modal-header">                
														<h5 class="modal-title">Eliminar</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">                
														¿Desea eliminar el registro?
													</div>
													<div class="modal-footer">

														<form method="POST" action="'.base_url().'index.php/rucs/destroy">
															<!-- <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button> -->
															<input type="submit" name="submit" value="Eliminar" class="btn btn-danger"></input>                                

															<input type="hidden" name="cnf_client_id" value="'. $ruc->cnf_client_id .'" class="form-control">
															<input type="hidden" name="sec_user_id" value="'. $ruc->sec_user_id .'" class="form-control">
														</form>

													</div>
											</div>        
										</div>
									</div>
									'
					);
				}
				// error_log(json_encode($listJson));

				$json = array(
					"draw"            => 1,
					"recordsTotal"    => count($listJson),
					"recordsFiltered" => count($listJson),
					"data"            => $listJson
			  	);
			  
			 	echo json_encode($json);
			}						
		}
	}

	public function create($id_usuario){
		// echo "Metodo edit";
		// echo $id_usuario;
		// return;

		$create=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');								
			}else{
					$create=true;						
			}				
		}

		if($create==true){
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
									$this->input->post("ruc"),
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

	public function destroy(){
		// echo "Metodo destroy";
		// echo "<pre>";
		// print_r($this->input->post());
		// echo "</pre>";
		// return;
		
		$destroy=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');								
			}else{
					$destroy=true;
			}
		}

		if($destroy==true){
			//Compruebo si se a enviado submit
			if($this->input->post("submit")){
    
					//Llamo al metodo destroy
					$destroy=$this->CRuc_model->destroyRUC(
									$this->input->post("sec_user_id"),
									$this->input->post("cnf_client_id")
					);
			}
			if($add==true){
				//Sesion de una sola ejecución
				$this->session->set_flashdata('correcto', 'RUC eliminado correctamente');
			}else{
				$this->session->set_flashdata('incorrecto', 'RUC no se pudo eliminar');
			}
				
			//Redirecciono la pagina a la url por defecto	
			redirect('users/edit/'. $this->input->post("sec_user_id"), 'location');		
		}
	}
}