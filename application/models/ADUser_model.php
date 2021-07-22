<?php
class ADUser_model extends CI_Model {
	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		$this->load->database();
		$this->load->library('session');

		/**
		 * Usar pg_escape_string para parametros en querys fuera de CI
		 */
	}

	/**
	 * Buscar usuario por user y password
	 * @param string $params['loginname'], string $param['password']
	 * @return array (result query)
	 */
	public function searchUserByUP($params)
	{
		$sql = "
		SELECT
			sec_user_id,
			name,
			email,
			isadmin			
		FROM
			sec_user
		WHERE
			email = ?
		AND password = ?
		AND isactive = 1;";
		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	/**
	 * Verificar existencia de usuario
	 * @param string $params['loginname']
	 * @return array (result query)
	 */
	public function checkUser($params)
	{
		$sql = "
		SELECT
			sec_user_id,
			name,
			email,
			isadmin,
			isactive
		FROM
			sec_user
		WHERE
			email = '".$params['loginname']."'
		AND isactive = 1;";

		//$query = $this->db->query($sql, $params);
		$query = $this->db->query($sql);
		return $query->result();
	}

	/**
	 * Listar usuarios
	 */
	public function listUser(){
		$sql = "
			SELECT	
				sec_user_id,			
				name,
				email,				
				isadmin,
				isactive
			FROM
				sec_user
			ORDER BY 
				1;
		";

		$query = $this->db->query($sql);
		return $query->result();
	}

	/**
	 * Guardar usuarios
	 */
	public function storeUser($name,$email,$password,$isadmin,$isactive){		
		//Verificamos que email no este registrado
		$consulta=$this->db->query("SELECT email FROM sec_user WHERE email LIKE '$email'");

		//Guardaremos si no existe email
		if($consulta->num_rows()==0){
			$consulta=$this->db->query("INSERT INTO sec_user VALUES(nextval('seq_sec_user_id'::regclass),'$name','$email','$password','$isadmin','$isactive');");			
			// error_log("INSERT INTO sec_user VALUES(nextval('seq_sec_user_id'::regclass),'$name','$email','$password','$isadmin','$isactive');");						
			if($consulta==true){
					return true;
			}else{
					$this->session->set_flashdata('database_error', $this->db->error());			
					// error_log(json_encode($this->db->error()));								
					return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * Buscar usuarios
	 */
	public function findUser($id_usuario){
		$consulta=$this->db->query("SELECT * FROM sec_user WHERE sec_user_id=$id_usuario");
      return $consulta->result();
	}

	/**
	 * Actualizar usuarios
	 */
	public function updateUser($id_usuario,$name=NULL,$email=NULL,$isadmin=NULL,$isactive=NULL,$password=NULL,$check_actualizar=NULL){
		$actualizar_password="";
		if(isset($check_actualizar) && $check_actualizar == "on"){
			$actualizar_password = ",password='$password'";
		}

		$consulta=$this->db->query("
				UPDATE 
					sec_user 
				SET 
					name='$name'
					,email='$email' 								
					,isadmin='$isadmin'
					,isactive='$isactive'					
					$actualizar_password
				WHERE 
					sec_user_id=$id_usuario;
		");
		if($consulta==true){
				return true;
		}else{
				$this->session->set_flashdata('database_error', $this->db->error());						
				return false;
		}		
  	}
}