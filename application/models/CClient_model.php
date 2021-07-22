<?php
class CClient_model extends CI_Model {
	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		$this->load->database();

		/**
		 * Usar pg_escape_string para parametros en querys fuera de CI
		 */
	}

	public function getAllClient()
	{
		$sql = "SELECT client.* FROM c_client client ORDER BY client.c_client_id ASC;";

		$query = $this->db->query($sql);
		return $query->result();
	}
}