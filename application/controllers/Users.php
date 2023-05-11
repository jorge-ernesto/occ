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

class Users extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');	
		$this->load->model('ADUser_model');	
		$this->load->helper('functions');
	}

	public function index()
	{
		//mensaje de error
	}

	public function view()
	{
		/*echo '<pre>';
		var_dump($this->COrg_model->usuariosIntegrado());
		echo '</pre>';*/
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');								
			}else{
				$data['title'] = 'Seguridad > Usuarios';			
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C'); //Esto solo esta aqui como referencia

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('users/view',$data);		
			}				
		}
	}

	public function list_(){
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');
			}else{
				$users = $this->ADUser_model->listUser();
				// error_log(json_encode($users));		
				
				$listJson = array();
				foreach ($users as $key => $user) {					
					//Validacion para excluir registro superusuario en caso el usuario logueado no lo sea					
					if($user->privilege == "Superuser") { //El registro listado cuenta con privilegio "Superuser"
						if(!$_SESSION['Superuser']) { //El usuario logueado no es "Superuser"
							continue;
						}
					}
					//Cerrar Validacion para excluir registro superusuario en caso el usuario logueado no lo sea

					$listJson[] = array(
						"0" => $user->sec_user_id,
						"1" => $user->name,
						"2" => $user->email,
						"3" => '<div class="d-flex">
										<div class="dropdown mr-1">
											<!-- Boton desplegable de configuracion -->
											<button type="button" class="btn btn-secondary" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
												<i class="fas fa-cog"></i>
											</button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
												<a class="dropdown-item" href="'.base_url().'index.php/users/edit/'.$user->sec_user_id.'">Editar</a>
												<a class="dropdown-item" href="'.base_url().'index.php/users/editpass/'.$user->sec_user_id.'">Cambiar contraseña</a>												
											</div>

											<!-- Boton eliminar -->
											<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal_delete_'.$user->sec_user_id.'" style="background-color: #e74a3b;!important">
												<i class="far fa-trash-alt"></i>
											</button>
											
											<!-- Modal -->
											<div class="modal fade animated fadeIn" id="modal_delete_'.$user->sec_user_id.'">
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

																<form method="POST" action="'.base_url().'index.php/users/destroy">
																	<!-- <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button> -->
																	<input type="submit" name="submit" value="Eliminar" class="btn btn-danger"></input>                                

																	<input type="hidden" name="sec_user_id" value="'. $user->sec_user_id .'" class="form-control">
																</form>

															</div>
													</div>        
												</div>
											</div>
										</div>
									</div>
								  ',
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

	public function create(){
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');								
			}else{
				$data['title'] = 'Seguridad > Usuarios';			
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C'); //Esto solo esta aqui como referencia

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('users/create',$data);		
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
    
					//Llamo al metodo add
					$add=$this->ADUser_model->storeUser(
									$this->input->post("name"),
									$this->input->post("email"),
									hash("SHA256", $this->input->post("password"))
					);
			}
			if($add==true){
				//Sesion de una sola ejecución
				$this->session->set_flashdata('correcto', 'Usuario añadido correctamente');
			}else{
				$this->session->set_flashdata('incorrecto', 'Usuario no se pudo añadir');
			}
				
			//Redirecciono la pagina a la url por defecto	
			redirect('users/view', 'location');		
		}		
	}

	public function edit($id_usuario){
		// echo "Metodo edit";
		// echo $user_id;
		// return;

		$edit=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
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
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C'); //Esto solo esta aqui como referencia

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('users/edit',$data);		
			}else{
				redirect('secure/login', 'location');
			}  
		}
	}

	public function update(){
		// echo "Metodo update";
		// echo "<pre>";
		// print_r($this->input->post());
		// echo "</pre>";
		// return;

		$update=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');								
			}else{
					$update=true;						
			}				
		}		

		//Obtenemos id del usuario
		$id_usuario = $this->input->post("sec_user_id");

		if($update==true){
			if(is_numeric($id_usuario)){
				//Validamos que exista el usuario
				$data['user'] = $this->ADUser_model->findUser($id_usuario);
				if(!$data['user']){
					redirect('secure/login', 'location');
				}
				
				//Compruebo si se a enviado submit
				if($this->input->post("submit")){

						$mod=$this->ADUser_model->updateUser(
										$id_usuario,
										$this->input->post("name"),
										$this->input->post("email")
						);											
				}
				if($mod==true){
					//Sesion de una sola ejecución
					$this->session->set_flashdata('correcto', 'Usuario modificado correctamente');
				}else{
					$this->session->set_flashdata('incorrecto', 'Usuario no se pudo modificar');
				}	

				//Redirecciono la pagina a la url por defecto
				redirect('users/view', 'location');		
			}else{
				redirect(base_url());
			}  
		}
	}

	public function editpass($id_usuario){
		// echo "Metodo edit";
		// echo $user_id;
		// return;

		$edit=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
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
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C'); //Esto solo esta aqui como referencia

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('users/editpass',$data);		
			}else{
				redirect('secure/login', 'location');
			}  
		}
	}

	public function updatepass(){
		// echo "Metodo update";
		// echo "<pre>";
		// print_r($this->input->post());
		// echo "</pre>";
		// return;

		$update=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
			if(!$privilege) {
				redirect('secure/login', 'location');								
			}else{
					$update=true;						
			}				
		}		

		//Obtenemos id del usuario
		$id_usuario            = $this->input->post("sec_user_id");
		$password              = $this->input->post("password");
		$password_confirmation = $this->input->post("password_confirmation");

		if($update==true){
			if(is_numeric($id_usuario)){
				//Validamos que exista el usuario
				$data['user'] = $this->ADUser_model->findUser($id_usuario);
				if(!$data['user']){
					redirect('secure/login', 'location');
				}

				//Validamos que contraseña y su confirmacion sean iguales
				if($password != $password_confirmation){
					$this->session->set_flashdata('incorrecto', 'Contraseñas no son iguales');
					redirect('users/view', 'location');		
				}
				
				//Compruebo si se a enviado submit
				if($this->input->post("submit")){

						$mod=$this->ADUser_model->updatePassword(
										$id_usuario,
										Hash("SHA256", $this->input->post("password"))
						);											
				}
				if($mod==true){
					//Sesion de una sola ejecución
					$this->session->set_flashdata('correcto', 'Contraseña modificada correctamente');
				}else{
					$this->session->set_flashdata('incorrecto', 'Contraseña no se pudo modificar');
				}	

				//Redirecciono la pagina a la url por defecto
				redirect('users/view', 'location');		
			}else{
				redirect(base_url());
			}  
		}
	}

	public function editpass_autoservice(){
		// echo "Metodo edit";
		// echo $user_id;
		// return;				

		$edit=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$edit=true;									
		}

		//Obtenemos id del usuario
		$id_usuario = $_SESSION['user_id'];

		if($edit==true){
			if(is_numeric($id_usuario)){
				//Validamos que exista el usuario
				$data['user'] = $this->ADUser_model->findUser($id_usuario);
				if(!$data['user']){
					redirect('secure/login', 'location');
				}
				
				//Redirecciono la pagina a la url por defecto
				$data['title'] = 'Seguridad > Usuarios';			
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C'); //Esto solo esta aqui como referencia

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('users/editpass_autoservice',$data);		
			}else{
				redirect('secure/login', 'location');
			}  
		}
	}

	public function updatepass_autoservice(){
		// echo "Metodo update";
		// echo "<pre>";
		// print_r($this->input->post());
		// echo "</pre>";
		// return;

		$update=false;
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			$update=true;
		}		

		//Obtenemos id del usuario
		$id_usuario            = $this->input->post("sec_user_id");
		$password_current      = $this->input->post("password_current");
		$password              = $this->input->post("password");
		$password_confirmation = $this->input->post("password_confirmation");

		if($update==true){
			if(is_numeric($id_usuario)){
				//Validamos que exista el usuario
				$data['user'] = $this->ADUser_model->findUser($id_usuario);
				if(!$data['user']){
					redirect('secure/login', 'location');
				}

				//Validamos que el usuario ingrese la contraseña actual correctamente
				$user = array(
					'loginname' => $data['user'][0]->email,
					'password' => hash("SHA256", $password_current)
				);
				$return['result_ad_user'] = $this->ADUser_model->searchUserByUP($user);		

				if(count($return['result_ad_user']) != 1){
					$this->session->set_flashdata('incorrecto', 'Contraseña actual no es correcta');
					redirect('users/editpass_autoservice', 'location');	
				}

				//Validamos que contraseña y su confirmacion sean iguales
				if($password != $password_confirmation){
					$this->session->set_flashdata('incorrecto', 'Contraseñas no son iguales');
					redirect('users/editpass_autoservice', 'location');		
				}
				
				//Compruebo si se a enviado submit
				if($this->input->post("submit")){

						$mod=$this->ADUser_model->updatePassword(
										$id_usuario,
										Hash("SHA256", $this->input->post("password"))
						);											
				}
				if($mod==true){
					//Sesion de una sola ejecución
					$this->session->set_flashdata('correcto', 'Contraseña modificada correctamente');
				}else{
					$this->session->set_flashdata('incorrecto', 'Contraseña no se pudo modificar');
				}	

				//Redirecciono la pagina a la url por defecto
				redirect('users/editpass_autoservice', 'location');		
			}else{
				redirect(base_url());
			}  
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
			//Validamos que el usuario a eliminar no sea el usuario logueado
			if($this->input->post("sec_user_id") == $_SESSION['user_id']){
				$this->session->set_flashdata('incorrecto', 'No puede eliminar usuario actual');
				redirect('users/view', 'location');
			}

			//Compruebo si se a enviado submit
			if($this->input->post("submit")){
    
					//Llamo al metodo destroy
					$des=$this->ADUser_model->destroyUser(
									$this->input->post("sec_user_id")
					);
			}
			if($des==true){
				//Sesion de una sola ejecución
				$this->session->set_flashdata('correcto', 'Usuario eliminado correctamente');
			}else{
				$this->session->set_flashdata('incorrecto', 'Usuario no se pudo eliminar');
			}
				
			//Redirecciono la pagina a la url por defecto	
			redirect('users/view', 'location');		
		}
	}
}