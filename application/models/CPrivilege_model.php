<?php
class CPrivilege_model extends CI_Model {
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
	 * Listar Privilegios
	 */
	public function getAllPrivileges(){
		$consulta=$this->db->query("SELECT * FROM sec_privilege ORDER BY 1");
      return $consulta->result();
	}

	/**
	 * Listar Privilegios
	 * Solo listamos Privilegio "Superuser" a usuarios que tengan el privilegio "Superuser" o estado isreserved
	 */
	public function getPrivileges(){
		if(!$_SESSION['Superuser']) {
			$consulta=$this->db->query("SELECT * FROM sec_privilege WHERE value != 'Superuser' ORDER BY 1");			
		}else{
			$consulta=$this->db->query("SELECT * FROM sec_privilege ORDER BY 1");
		}			
      return $consulta->result();
	}

	/**
	 * Listar Privilegios por usuario
	 */
	public function listPrivileges($id_usuario){
		$sql = "
			SELECT
				sp.sec_privilege_id, --Estas variables se pusieron para que ordene datatables
				corg.cnf_org_id,     --Estas variables se pusieron para que ordene datatables
				cli.cnf_client_id,   --Estas variables se pusieron para que ordene datatables

				sup.sec_user_privilege_id,	--Estas variables se utilizan para eliminar el privilegio			
				cli.cnf_client_id,         --Estas variables se utilizan para eliminar el privilegio			
				su.sec_user_id,            --Estas variables se utilizan para eliminar el privilegio			
				
				sp.name as privilegio,                          --Estas variables se muestran en la tabla
				corg.name as centro_costo,								--Estas variables se muestran en la tabla
				cli.value || ' - ' || cli.name AS ruc_razsocial --Estas variables se muestran en la tabla								
			FROM
				sec_privilege sp
				LEFT JOIN sec_user_privilege sup ON (sp.sec_privilege_id = sup.sec_privilege_id)
				LEFT JOIN sec_user su            ON (sup.sec_user_id = su.sec_user_id)
				LEFT JOIN cnf_org corg           ON (sup.cnf_org_id = corg.cnf_org_id)
				LEFT JOIN cnf_client cli         ON (sup.cnf_client_id = cli.cnf_client_id)
			WHERE
				su.sec_user_id = '$id_usuario'
			ORDER BY
				sp.sec_privilege_id, corg.cnf_org_id, cli.cnf_client_id;
		";

		$query = $this->db->query($sql);
		return $query->result();
	}

	/**
	 * Guardar privilegios
	 * 1 - Superuser
	 * 2 - Admin
	 * 3 - OrgReports
	 * 4 - FleetReports
	 */
	public function storePrivilege($sec_user_id,$sec_privilege_id,$cnf_org_id,$ruc,$razon_social){		
		
		if($sec_privilege_id == 1 || $sec_privilege_id == 2) {
			
			$consulta = true;
			$consulta_=$this->db->query("INSERT INTO sec_user_privilege VALUES(nextval('seq_sec_user_privilege_id'::regclass),'$sec_user_id','$sec_privilege_id',NULL,NULL);");					

		}else if($sec_privilege_id == 3) {
			
			$consulta = true;
			$consulta_=$this->db->query("INSERT INTO sec_user_privilege VALUES(nextval('seq_sec_user_privilege_id'::regclass),'$sec_user_id','$sec_privilege_id','$cnf_org_id',NULL);");		

		}else if($sec_privilege_id == 4) {

			//Guardamos RUCs
			$consulta=$this->db->query("INSERT INTO cnf_client VALUES(nextval('seq_cnf_client_id'::regclass),'$razon_social','$ruc');");
			// error_log("INSERT INTO cnf_client VALUES(nextval('seq_cnf_client_id'::regclass),'$razon_social','$ruc');");						

			//Solo si guarda el RUC en cnf_client
			if($consulta==true){
				$last_id_cnf_client_id = $this->db->insert_id();
				// error_log($last_id);

				$consulta_=$this->db->query("INSERT INTO sec_user_privilege VALUES(nextval('seq_sec_user_privilege_id'::regclass),'$sec_user_id','$sec_privilege_id',NULL,'$last_id_cnf_client_id');");		
				// error_log("INSERT INTO sec_user_client VALUES(nextval('seq_sec_user_client_id'::regclass),'$sec_user_id','$last_id_cnf_client_id');");
			}
							
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
	 * Eliminar Privilegios
	 */
	public function destroyPrivilege($sec_user_privilege_id,$cnf_client_id=""){
		//Eliminamos privilegios
		$consulta=$this->db->query("DELETE FROM sec_user_privilege WHERE sec_user_privilege_id = '$sec_user_privilege_id';");
		error_log("DELETE FROM sec_user_privilege WHERE sec_user_privilege_id = '$sec_user_privilege_id';");

		//Solo si elimino de sec_user_privilege
		if($consulta){
			if(empty($cnf_client_id)) {
				$consulta_=true;
			}else{
				$consulta_=$this->db->query("DELETE FROM cnf_client WHERE cnf_client_id = '$cnf_client_id';");
				error_log("DELETE FROM cnf_client WHERE cnf_client_id = '$cnf_client_id';");
			}			
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