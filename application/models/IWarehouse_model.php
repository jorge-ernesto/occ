<?php
class IWarehouse_model extends CI_Model {
	public function __construct()
	{
		// Call the CI_Model constructor
		parent::__construct();
		$this->load->database();

		/**
		 * Usar pg_escape_string para parametros en querys fuera de CI
		 */
	}

	public function getAllWarehouse()
	{
		$sql = "SELECT warehouse.*, org.name AS org_name FROM
i_warehouse warehouse
JOIN c_org org ON (
 warehouse.c_org_id = org.c_org_id
)
ORDER BY warehouse.i_warehouse_id ASC;";
		$query = $this->db->query($sql);
		return $query->result();
	}
}