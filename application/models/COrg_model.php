<?php
class COrg_model extends CI_Model {
	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		$this->load->database();

		/**
		 * Usar pg_escape_string para parametros en querys fuera de CI
		 */
	}

	public function getAllCOrg()
	{
		$query = $this->db->query("SELECT
	cnf_org_id AS c_org_id,
	name AS name,
	ipaddress AS ip
FROM 
	cnf_org
ORDER BY
	1;");
		return $query->result();
	}

	public function getOrgByTypeAndId($id){
		$query = $this->db->query("SELECT
	cnf_org_id AS c_org_id,	
	name AS name,	
	ipaddress AS ip,
	value AS almacen_id
FROM
	cnf_org
WHERE
	cnf_org_id = '$id'
ORDER BY
	1;");
		return $query->result();
	}

	public function getCOrgByType(){
		$query = $this->db->query("SELECT
	cnf_org_id AS c_org_id,	
	name AS name,	
	ipaddress AS ip,
	value AS almacen_id
FROM
	cnf_org
ORDER BY
	1;");
		return $query->result();
	}
}