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

class Webservice extends CI_Controller {

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

	public function download()
	{		
		// https://www.php.net/manual/es/features.http-auth.php	
		// if (!isset($_SERVER['PHP_AUTH_USER'])) {
		// 	header('WWW-Authenticate: Basic realm="Mi dominio"');
		// 	header('HTTP/1.0 401 Unauthorized');
		// 	echo 'Texto a enviar si el usuario pulsa el botón Cancelar';
		// 	exit;
		// } else {
		// 	echo "<p>Hola {$_SERVER['PHP_AUTH_USER']}.</p>";
		// 	echo "<p>Introdujo {$_SERVER['PHP_AUTH_PW']} como su contraseña.</p>";
		// }		
		
		$data = $this->input->post("data");
		$json = json_decode($_SERVER, true);

		$data = array(
			"status"  => "success",
			"code"    => "200",
			"message" => "Usuario encontrado",
			"data"    => $_SERVER
	  	);
	  	echo json_encode($data);
	}
}