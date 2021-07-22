<?php
class ADUser_model extends CI_Model {
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
	 * Buscar usuario por user y password
	 * @param string $params['loginname'], string $param['password']
	 * @return array (result query)
	 */
	public function searchUserByUP($params)
	{
		$sql = "
		SELECT
			ad_user_id,
			name,
			loginname
		FROM
			ad_user
		WHERE
			loginname = ?
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
			ad_user_id,
			name,
			loginname
		FROM
			ad_user
		WHERE
			loginname = '".$params['loginname']."'
		AND isactive = 1;";

		//$query = $this->db->query($sql, $params);
		$query = $this->db->query($sql);
		return $query->result();
	}
}