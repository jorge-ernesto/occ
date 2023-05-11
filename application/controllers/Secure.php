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

class Secure extends CI_Controller {

	public function __construct()
	{
		// Call the CI_Controller constructor
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('functions');
	}

	public function index()
	{
		//mensaje de error
	}

	public function login()
	{
		if(!checkSession()) {
			$data['title'] = 'Login';
			$this->load->view('secure/login',$data);
		} else {
			redirect('home', 'location');
		}
	}

	public function postLogin()
	{
		$return = array();
		if($this->input->post('username') != null && $this->input->post('password') != null) {
			$this->load->model('ADUser_model');
			$data = array(
				'loginname' => $this->input->post('username'),
				'password' => hash("SHA256", $this->input->post('password'))
			);
			$return['check_user'] = $this->ADUser_model->checkUser($data);
			if(count($return['check_user'])) {
				$return['result_ad_user'] = $this->ADUser_model->searchUserByUP($data);
				if(count($return['result_ad_user']) == 1) {
					$return['status'] = 1;
					//data session
					$_SESSION['user_id']   = $return['result_ad_user'][0]->sec_user_id;
					$_SESSION['name']      = $return['result_ad_user'][0]->name;
					$_SESSION['loginname'] = $return['result_ad_user'][0]->email;
					
					/* Obtenemos permisos de sec_privilege */					
					$this->load->model('CPrivilege_model');
					$sec_user_id = $return['result_ad_user'][0]->sec_user_id;
					$privileges  = $this->CPrivilege_model->getAllPrivileges();					

					foreach ($privileges as $key => $privilege) {						
						$_SESSION[$privilege->value] = $this->ADUser_model->searchPrivilege($sec_user_id, $privilege->value);												
					}

					$_SESSION['isreserved'] = $this->ADUser_model->searchReserved($sec_user_id);						
					/* Cerrar Obtenemos permisos de sec_privilege */

					error_log("SESSION");
					error_log(json_encode($_SESSION));
				} else {
					$return['status'] = 2;
					$return['message'] = 'El usuario y/o contraseña no es válido';
				}
			} else {
				$return['status'] = 3;
				$return['message'] = 'El usuario y/o contraseña no es válido';
			}
		} else {
			$return['status'] = 100;
			$return['message'] = 'Error al enviar datos.';
		}

		echo json_encode($return);
	}

	public function logout()
	{
		session_destroy();
		redirect('secure/login', 'location');
	}

	public function identity()
	{
		if(!checkSession()) {
			$data['title'] = '¿Has olvidado la contrase&ntilde;a?';
			$this->load->view('secure/identity',$data);
		} else {
			redirect('home', 'location');
		}
	}

	public function postIdentity()
	{
		$ip = $_SERVER["REMOTE_ADDR"];
		$captcha = $_POST['g_recaptcha_response'];
		$secretKey = '6Leams0eAAAAAPYOquTri7bLq0zyuFMq7FfMooka';

		$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}&remoteip={$ip}");
    	$atributos = json_decode($response, TRUE);

		$return = array();
		if($atributos['success'] == true){ //Si se verifico el captcha
			if($this->input->post('email') != null) {
				$this->load->model('ADUser_model');
				$data = array(
					'loginname' => $this->input->post('email')
				);
				$return['check_user'] = $this->ADUser_model->checkUser($data);
				if(count($return['check_user'])) {
					$return['status'] = 1;

					//op
					mail("jlachira@opensysperu.com,jlachira@opensysperu.com","Restablecer Contraseña","Enlace TOKEN");
				} else {
					$return['status'] = 2;
					$return['message'] = 'El usuario no es válido';
				}
			} else {
				$return['status'] = 100;
				$return['message'] = 'Error al enviar datos.';
			}
		} else { //Si no se verifico el captcha
			$return['status'] = 500;
			$return['message'] = 'Verifica el captcha.';
		}
		$return['captcha'] = $atributos;

		echo json_encode($return);
	}
}