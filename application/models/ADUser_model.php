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
	 * Buscar estado isreserved en la tabla sec_privilege
	 */
	public function searchReserved($sec_user_id)
	{
		$sql = "
		SELECT
			1 as privilege
		FROM
			sec_user su
			LEFT JOIN sec_user_privilege sup ON (su.sec_user_id = sup.sec_user_id)
			LEFT JOIN sec_privilege sp       ON (sup.sec_privilege_id = sp.sec_privilege_id)
		WHERE
			su.sec_user_id = $sec_user_id
			AND sp.isreserved = 1
		LIMIT 1;";
		$query = $this->db->query($sql);

		$result = $query->result();
		return ($result[0]->privilege == 1) ? 1 : 0;
	}

	/**
	 * Buscar permisos en la tabla sec_privilege: 
	 * 1 - Superuser
	 * 2 - Admin
	 * 3 - OrgReports
	 * 4 - FleetReports
	 */
	public function searchPrivilege($sec_user_id, $value_privilege)
	{
		$sql = "
		SELECT
			1 as privilege
		FROM
			sec_user su
			JOIN sec_user_privilege sup ON (su.sec_user_id = sup.sec_user_id)
			JOIN sec_privilege sp       ON (sup.sec_privilege_id = sp.sec_privilege_id)
		WHERE
			su.sec_user_id = $sec_user_id
			AND sp.value = '$value_privilege'
		LIMIT 1;";
		$query = $this->db->query($sql);

		$result = $query->result();
		return ($result[0]->privilege == 1) ? 1 : 0;
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
			email
		FROM
			sec_user
		WHERE
			email = ?
		AND password = ?";
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
			email
		FROM
			sec_user
		WHERE
			email = '".$params['loginname']."';";

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

				( SELECT
						sp.value
					FROM
						sec_user su
						JOIN sec_user_privilege sup ON (su.sec_user_id = sup.sec_user_id)
						JOIN sec_privilege sp       ON (sup.sec_privilege_id = sp.sec_privilege_id)
					WHERE
						su.sec_user_id = sec_user.sec_user_id
						AND sp.value = 'Superuser'
					LIMIT 1 ) as privilege
				
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
	public function storeUser($name,$email,$password){		
		//Verificamos que email no este registrado
		$consulta=$this->db->query("SELECT email FROM sec_user WHERE email LIKE '$email'");

		//Guardaremos si no existe email
		if($consulta->num_rows()==0){
			$consulta=$this->db->query("INSERT INTO sec_user VALUES(nextval('seq_sec_user_id'::regclass),'$name','$email','$password');");			
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
	public function updateUser($id_usuario,$name=NULL,$email=NULL){
		$consulta=$this->db->query("
				UPDATE 
					sec_user 
				SET 
					name='$name'
					,email='$email'
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

	/**
	 * Actualizar contraseña de usuarios
	 */
	public function updatePassword($id_usuario,$password=NULL){
		$consulta=$this->db->query("
				UPDATE 
					sec_user
				SET 
					password='$password'
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

	/**
	 * Eliminar usuarios
	 */
	public function destroyUser($id_usuario){
		//Obtenemos todos los RUCs asociados al usuario si los tuviera		
		$query = $this->db->query("SELECT * FROM sec_user_privilege WHERE sec_user_id = '$id_usuario';");
		$rucs = $query->result();		

		//Eliminamos todos los privilegios del usuario
		$consultaprivileges=$this->db->query("DELETE FROM sec_user_privilege WHERE sec_user_id = '$id_usuario';");
		
		if($consultaprivileges){			
			foreach ($rucs as $key => $ruc) {
				if(!empty($ruc->cnf_client_id)){
					//Eliminamos todos los RUCs asociados al usuario si los tuviera
					$this->db->query("DELETE FROM cnf_client WHERE cnf_client_id = '$ruc->cnf_client_id';");
				}
			}			
			
			//Eliminamos el usuario
			$consultauser=$this->db->query("DELETE FROM sec_user WHERE sec_user_id = '$id_usuario';");			
		}				

		if($consultaprivileges==true && $consultauser==true){
			return true;
		}else{
				$this->session->set_flashdata('database_error', $this->db->error());			
				// error_log(json_encode($this->db->error()));								
				return false;
		}
	}
}