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
				'password' => $this->input->post('password')
			);
			$return['check_user'] = $this->ADUser_model->checkUser($data);
			if(count($return['check_user'])) {
				$return['result_ad_user'] = $this->ADUser_model->searchUserByUP($data);
				if(count($return['result_ad_user']) == 1) {
					$return['status'] = 1;
					//data session
					$_SESSION['user_id'] = $return['result_ad_user'][0]->ad_user_id;
					$_SESSION['loginname'] = $return['result_ad_user'][0]->loginname;
					$_SESSION['name'] = $return['result_ad_user'][0]->name;
				} else {
					$return['status'] = 2;
					$return['message'] = 'Contraseña incorrecta.';
				}
			} else {
				$return['status'] = 3;
				$return['message'] = 'Error, el usuario ingresado no existe';
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
}