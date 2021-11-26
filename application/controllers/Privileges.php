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

class Privileges extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');	
		$this->load->model('ADUser_model');	
		$this->load->model('COrg_model');	
		$this->load->model('CPrivilege_model');	
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
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');
			}else{
				$privileges = $this->CPrivilege_model->listPrivileges($id_usuario);
				// error_log(json_encode($privileges));		
				
				$listJson = array();
				foreach ($privileges as $key => $privilege) {
					$listJson[] = array(
						"0" => $privilege->sec_privilege_id, //Estas variables se pusieron para que ordene datatables
						"1" => $privilege->cnf_org_id,       //Estas variables se pusieron para que ordene datatables
						"2" => $privilege->cnf_client_id,    //Estas variables se pusieron para que ordene datatables
						"3" => $privilege->privilegio,
						"4" => $privilege->centro_costo,
						"5" => $privilege->ruc_razsocial,
						"6" => '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal_delete_'.$privilege->sec_user_privilege_id.'" style="background-color: #e74a3b;!important">
										<i class="far fa-trash-alt"></i>
									</button>
									
									<!-- Modal -->
									<div class="modal fade animated fadeIn" id="modal_delete_'.$privilege->sec_user_privilege_id.'">
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

														<form method="POST" action="'.base_url().'index.php/privileges/destroy">
															<!-- <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button> -->
															<input type="submit" name="submit" value="Eliminar" class="btn btn-danger"></input>                                

															<input type="hidden" name="cnf_client_id" value="'. $privilege->cnf_client_id .'" class="form-control">
															<input type="hidden" name="sec_user_privilege_id" value="'. $privilege->sec_user_privilege_id .'" class="form-control">
															<input type="hidden" name="sec_user_id" value="'. $privilege->sec_user_id .'" class="form-control">
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
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
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
				$data['privileges'] = $this->CPrivilege_model->getPrivileges();
				$data['result_c_org'] = $this->COrg_model->getAllCOrgFlotas();

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('privileges/create', $data);		
			}else{
				redirect('secure/login', 'location');
			}  
		}		
	}

	/**
	 * Guardar privilegios
	 * 1 - Superuser
	 * 2 - Admin
	 * 3 - OrgReports
	 * 4 - FleetReports
	 */
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
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');								
			}else{
					$store=true;
			}
		}

		if($store==true){
			//Compruebo si se a enviado submit
			if($this->input->post("submit")){
    
					//Obtenemos sec_privilege_id
					$sec_user_id      = $this->input->post("sec_user_id");
					$sec_privilege_id = $this->input->post("select-privilege");
					$cnf_org_id       = $this->input->post("select-station");
					$ruc              = TRIM($this->input->post("ruc"));
					$razon_social     = $this->input->post("razon_social");
					
					//Llamo al metodo add
					$add=$this->CPrivilege_model->storePrivilege(
									$sec_user_id,
									$sec_privilege_id,
									$cnf_org_id,
									$ruc,
									$razon_social
					);				
			}
			if($add==true){
				//Sesion de una sola ejecución
				$this->session->set_flashdata('correcto', 'Privilegio añadido correctamente');
			}else{
				$this->session->set_flashdata('incorrecto', 'Privilegio no se pudo añadir');
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
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');								
			}else{
					$destroy=true;
			}
		}

		if($destroy==true){
			//Compruebo si se a enviado submit
			if($this->input->post("submit")){
    
					//Llamo al metodo destroy
					$destroy=$this->CPrivilege_model->destroyPrivilege(
									$this->input->post("sec_user_privilege_id"),
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