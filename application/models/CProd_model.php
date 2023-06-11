<?php
class CProd_model extends CI_Model {
    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
        $this->load->database();

        /**
         * Usar pg_escape_string para parametros en querys fuera de CI
         */
    }

    /**
     * CONSULTAS REPORTES ESTACIONES
     */

    public function getAllActiveCProd()
    {
        $query = $this->db->query("SELECT
	cp.cnf_product_id as cnf_product_id,
	cp.name,
	cp.value
FROM
	cnf_product cp
WHERE
	cp.is_active = 1
ORDER BY cp.value ASC;");
        return $query->result();
    }

    public function getAllCProd()
    {
        $query = $this->db->query("SELECT
	cp.cnf_product_id as cnf_product_id,
	cp.name,
    cp.abbreviation,
	cp.value
FROM
	cnf_product cp
ORDER BY cp.value ASC;");
        return $query->result();
    }
}