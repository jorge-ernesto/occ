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
	 * Guardar RUCs
	 */
	public function storeRUC($sec_user_id,$name,$razon_social){		
		
		//Guardamos RUCs
		$consulta=$this->db->query("INSERT INTO cnf_client VALUES(nextval('seq_cnf_client_id'::regclass),'$name','$razon_social');");
		// error_log("INSERT INTO cnf_client VALUES(nextval('seq_cnf_client_id'::regclass),'$name','$razon_social');");						

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
}