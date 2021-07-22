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
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');								
			}else{
				$data['title'] = 'Seguridad > Usuarios';			
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

				$this->load->helper('functions');
				$data['default_start_date'] = getDateDefault('d/m/Y');

				$data['typeStation'] = 0;
				$this->load->view('users/view',$data);		
			}				
		}
	}

	public function list(){
		if(!checkSession()) {
			redirect('secure/login', 'location');
		} else {
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');
			}else{
				$users = $this->ADUser_model->listUser();
				// error_log(json_encode($users));		
				
				$listJson = array();
				foreach ($users as $key => $user) {
					$listJson[] = array(
						"0" => $user->sec_user_id,
						"1" => $user->name,
						"2" => $user->email,
						"3" => ($user->isadmin == 1) ?
								'<h6><span class="badge badge-primary">Si</span></h6>' :
								'<h6><span class="badge badge-danger">No</span></h6>',
						"4" => ($user->isactive == 1) ?
								'<h6><span class="badge badge-primary">Si</span></h6>' :
								'<h6><span class="badge badge-danger">No</span></h6>',
						"5" => '<a class="btn btn-sm btn-primary" href="'.base_url().'index.php/users/edit/'.$user->sec_user_id.'">Editar</a>
								  <a class="btn btn-sm btn-primary" href="'.base_url().'index.php/users/editpass/'.$user->sec_user_id.'">Cambiar contraseña</a>',
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
			if(!$_SESSION['isadmin']) {
				redirect('secure/login', 'location');								
			}else{
				$data['title'] = 'Seguridad > Usuarios';			
				// $data['result_c_org'] = $this->COrg_model->getAllCOrg('C');

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
					$add=$this->ADUser_model->storeUser(
									$this->input->post("name"),
									$this->input->post("email"),
									hash("SHA256", $this->input->post("password")),
									$this->input->post("isadmin"),
									$this->input->post("isactive")
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
			if(!$_SESSION['isadmin']) {
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
										$this->input->post("email"),
										$this->input->post("isadmin"),
										$this->input->post("isactive")
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
			if(!$_SESSION['isadmin']) {
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
}