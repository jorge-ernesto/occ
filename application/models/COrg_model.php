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
	cnf_org_id as c_org_id,
	name as name,
	ipaddress as ip
FROM 
	cnf_org
ORDER BY
	1;");
		return $query->result();
	}
}