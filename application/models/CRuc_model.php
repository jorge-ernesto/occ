<?php
class CRuc_model extends CI_Model {
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
	 * Listar RUCs por usuario
	 */
	public function listRUC($id_usuario){
		$sql = "
			SELECT	
				cli.cnf_client_id,
				cli.name as razon_social,
				cli.value as ruc,
				secuser.sec_user_id
			FROM
				cnf_client                 cli
				INNER JOIN sec_user_client usercli ON (cli.cnf_client_id   = usercli.cnf_client_id)
				INNER JOIN sec_user        secuser ON (secuser.sec_user_id = usercli.sec_user_id)
			WHERE
				secuser.sec_user_id = '$id_usuario'
			ORDER BY
				cli.cnf_client_id;
		";

		$query = $this->db->query($sql);
		return $query->result();
	}

	/**
	 * Guardar RUCs
	 */
	public function storeRUC($sec_user_id,$ruc,$razon_social){		
		
		//Guardamos RUCs
		$consulta=$this->db->query("INSERT INTO cnf_client VALUES(nextval('seq_cnf_client_id'::regclass),'$razon_social','$ruc');");
		// error_log("INSERT INTO cnf_client VALUES(nextval('seq_cnf_client_id'::regclass),'$razon_social','$ruc');");						

		//Solo si guarda el RUC en cnf_client
		if($consulta==true){
			$last_id_cnf_client_id = $this->db->insert_id();
			// error_log($last_id);

			$consulta_=$this->db->query("INSERT INTO sec_user_client VALUES(nextval('seq_sec_user_client_id'::regclass),'$sec_user_id','$last_id_cnf_client_id');");		
			// error_log("INSERT INTO sec_user_client VALUES(nextval('seq_sec_user_client_id'::regclass),'$sec_user_id','$last_id_cnf_client_id');");
		}

		if($consulta==true && $consulta_==true){
				return true;
		}else{
				$this->session->set_flashdata('database_error', $this->db->error());			
				// error_log(json_encode($this->db->error()));								
				return false;
		}
	}

	/**
	 * Eliminar RUCs
	 */
	public function destroyRUC($sec_user_id,$cnf_client_id){
		//Eliminamos RUCs
		$consulta=$this->db->query("DELETE FROM sec_user_client WHERE sec_user_id = '$sec_user_id' AND cnf_client_id = '$cnf_client_id';");
		error_log("DELETE FROM sec_user_client WHERE sec_user_id = '$sec_user_id' AND cnf_client_id = '$cnf_client_id';");

		//Solo si elimino de sec_user_client
		if($consulta){
			$consulta_=$this->db->query("DELETE FROM cnf_client WHERE cnf_client_id = '$cnf_client_id';");
			error_log("DELETE FROM cnf_client WHERE cnf_client_id = '$cnf_client_id';");
		}

		if($consulta==true && $consulta_==true){
			return true;
		}else{
				$this->session->set_flashdata('database_error', $this->db->error());			
				// error_log(json_encode($this->db->error()));								
				return false;
		}
	}

	/**
	 * Obtener RUCs por usuario logueado
	 */
	public function getRucByClient($user_id){
		$sql = "
			SELECT 
				* 
			FROM 
				sec_user_client secuser
				INNER JOIN cnf_client cnfcli ON (secuser.cnf_client_id = cnfcli.cnf_client_id)
			WHERE
				secuser.sec_user_id = '$user_id';
		";

		$query = $this->db->query($sql);
		return $query->result();		
	}
}