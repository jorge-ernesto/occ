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

	/**
	 * CONSULTAS REPORTES ESTACIONES
	 */

	public function getAllCOrg($type)
	{
		$type = ($type == 'C') ? '1' : '0';

		//Condicional de organizaciones por usuario logueado
		$whereOrgsByUser = $this->getWhereOrgsByUser($_SESSION['user_id']);

		$query = $this->db->query("SELECT
	co.cnf_org_id as c_org_id,
	co.name,
	co.ipaddress as ip
FROM
	cnf_org co
WHERE
	co.orgtype = ?
	$whereOrgsByUser 
ORDER BY co.cnf_org_id ASC;", array($type));
		return $query->result();
	}

	public function getOrgByTypeAndId($type,$id)
	{
		$type = ($type == 'C') ? '1' : '0';

		//Condicional de organizaciones por usuario logueado
		$whereOrgsByUser = $this->getWhereOrgsByUser($_SESSION['user_id']);		

		$query = $this->db->query("SELECT
	cc.name AS client_name,
	cc.taxid,
	co.cnf_org_id as c_org_id,
	co.name,
	co.abbreviation as initials,
	'' as value,
	co.ipaddress as ip,
	co.value as almacen_id
FROM
	cnf_org co
	JOIN cnf_company cc ON (co.cnf_company_id = cc.cnf_company_id)
WHERE
	co.orgtype = '$type'
	AND co.cnf_org_id = ?
	$whereOrgsByUser
ORDER BY cc.cnf_company_id ASC, co.cnf_org_id ASC;", array($id));
		
error_log("Query getOrgByTypeAndId");
error_log($this->db->last_query());

		return $query->result();
	}

	public function getOrgByTypeAndIdSelectMultiple($type,$stationsId)
	{
		$type = ($type == 'C') ? '1' : '0';

		//Condicional de organizaciones por usuario logueado
		$whereOrgsByUser = $this->getWhereOrgsByUser($_SESSION['user_id']);

		//Condicional de organizaciones por select multiple
		$tmp = implode(',', $stationsId);
		$whereOrgsBySelect = "AND co.cnf_org_id IN ( $tmp )";

		$query = $this->db->query("SELECT
	cc.name AS client_name,
	cc.taxid,
	co.cnf_org_id as c_org_id,
	co.name,
	co.abbreviation as initials,
	'' as value,
	co.ipaddress as ip,
	co.value as almacen_id
FROM
	cnf_org co
	JOIN cnf_company cc ON (co.cnf_company_id = cc.cnf_company_id)
WHERE
	co.orgtype = '$type'
	$whereOrgsBySelect
	$whereOrgsByUser
ORDER BY cc.cnf_company_id ASC, co.cnf_org_id ASC;");

error_log("Query getOrgByTypeAndIdSelectMultiple");
error_log($this->db->last_query());

		return $query->result();
	}

	public function getCOrgByType($type) {
		$type = ($type == 'C') ? '1' : '0';

		//Condicional de organizaciones por usuario logueado
		$whereOrgsByUser = $this->getWhereOrgsByUser($_SESSION['user_id']);

		$query = $this->db->query("SELECT
	cc.name AS client_name,
	cc.taxid,
	co.cnf_org_id as c_org_id,
	co.name,
	co.abbreviation as initials,
	'' as value,
	co.ipaddress as ip,
	co.value as almacen_id
FROM
	cnf_org co
	JOIN cnf_company cc ON (co.cnf_company_id = cc.cnf_company_id)
WHERE
	co.orgtype = ?
	$whereOrgsByUser
ORDER BY cc.cnf_company_id ASC, co.cnf_org_id ASC;", array($type));

error_log("Query getCOrgByType");
error_log($this->db->last_query());

		return $query->result();
	}

	public function getValueOrgById($id)
	{
		$sql = "SELECT
	org.c_org_id,
	org.value
FROM
	c_org org
WHERE
org.c_org_id = $id";
		$query = $this->db->query($sql);
		return $query->result();
		//return $sql;
	}

	public function getValueOrgByIdAndType($type)
	{
		$sql = "SELECT
	org.c_org_id,
	org.value
FROM
	c_org org
WHERE
org.description = '$type';";
		$query = $this->db->query($sql);
		return $query->result();
		//return $sql;
	}

	public function getOrgByIds($ids)
	{
		$sql = "SELECT
	client.name AS client_name,
	client.taxid,
	org.c_org_id,
	org.name,
	org.initials,
	org.value,
	remote.ip,
	warehouse.description AS almacen_id
FROM
	c_org org
JOIN mig_cowmap cowmap ON (
	org.c_org_id = cowmap.c_org_id
)
JOIN c_client client ON (
	org.c_client_id = client.c_client_id
)
JOIN mig_remote remote ON (
	cowmap.id_remote = mig_remote_id
)
JOIN i_warehouse warehouse ON (
	org.c_org_id = warehouse.c_org_id
)
WHERE
	org.c_org_id IN ($ids) AND remote.view = 1
ORDER BY client.c_client_id ASC;";
		$query = $this->db->query($sql, array($ids));
		return $query->result();
	}

	public function getAllOrg()
	{
		$sql = "SELECT
	client.name AS client_name,
	client.taxid,
	org.c_org_id,
	org.name,
	org.initials,
	org.value,
	remote.ip,
	warehouse.description AS almacen_id
FROM
	c_org org
JOIN mig_cowmap cowmap ON (
	org.c_org_id = cowmap.c_org_id
)
JOIN c_client client ON (
	org.c_client_id = client.c_client_id
)
JOIN mig_remote remote ON (
	cowmap.id_remote = mig_remote_id
)
JOIN i_warehouse warehouse ON (
	org.c_org_id = warehouse.c_org_id
)
WHERE
	remote.view = 1
ORDER BY client.c_client_id ASC;";
		$query = $this->db->query($sql, array($ids));
		return $query->result();
	}

	public function getOrgById($id)
	{
		$query = $this->db->query("SELECT
	client.name AS client_name,
	client.taxid,
	org.c_org_id,
	org.name,
	org.initials,
	org.value,
	remote.ip,
	warehouse.description AS almacen_id
FROM
	c_org org
JOIN mig_cowmap cowmap ON (
	org.c_org_id = cowmap.c_org_id
)
JOIN c_client client ON (
	org.c_client_id = client.c_client_id
)
JOIN mig_remote remote ON (
	cowmap.id_remote = mig_remote_id
)
JOIN i_warehouse warehouse ON (
	org.c_org_id = warehouse.c_org_id
)
WHERE
	org.c_org_id = ? AND remote.view = 1
ORDER BY client.c_client_id ASC;", array($id));
		return $query->result();
	}

	public function usuariosIntegrado() {
		$int = $this->load->database('int', TRUE);

		$sql = "SELECT * FROM int_usuarios_passwd;";
		$query = $int->query($sql);
		return $query->result();
	}

	public function getOrgRelationId($id, $type) {
		$c_org_id = $id == '*' ? '' : 'WHERE org.c_org_id = '.$id;

		$sql = "SELECT
org.c_org_id,
org.c_client_id,
org.name,
org.initials,
remote.ip
FROM c_org org
JOIN mig_cowmap cow ON (cow.c_org_id = org.c_org_id)
JOIN mig_remote remote ON (cow.id_remote = remote.mig_remote_id)
$c_org_id AND org.description = '$type'
ORDER BY org.c_org_id
;";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function getCentralizationsByOrgId($data) {
		$c_org_id = $data['c_org_id'] == '*' ? '' : 'AND org.c_org_id = '.$data['c_org_id'];
		$created = 'AND process.created BETWEEN '.$data['created'].' AND '.$data['created'];
		$sql = "SELECT
 FIRST(remote.mig_remote_id),
 FIRST(remote.name),
 max(process.mig_process_id),
 TO_CHAR(max(process.created),'DD/MM/YYYY HH24:MI:SS'),
 TO_CHAR(max(process.systemdate),'DD/MM/YYYY')
 ,TO_CHAR(max(process.systemdate) + interval '1 day','YYYY-MM-DD')
 ,FIRST(process.status)
FROM c_org org
JOIN mig_cowmap cowmap ON (org.c_org_id = cowmap.c_org_id)
JOIN mig_remote remote ON (cowmap.id_remote = remote.mig_remote_id)
JOIN mig_process process ON (remote.mig_remote_id = process.mig_remote_id AND process.status IS NOT NULL)
WHERE
 org.isactive = 1
 $c_org_id
 $created
GROUP BY
 process.mig_remote_id
ORDER BY process.mig_remote_id ASC;
";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function getAll()
	{
		$sql = "SELECT org.*, client.name AS client_name
FROM
c_org org
JOIN c_client client ON (
 org.c_client_id = client.c_client_id
)
ORDER BY org.c_org_id ASC;";
		$query = $this->db->query($sql);
		return $query->result();
	}

	public function getWhereOrgsByUser($id){
		$privilege = ($_SESSION['Superuser'] || $_SESSION['Admin']) ? 1 : 0;
		
		if(!$privilege) { //NO TIENE PRIVILEGIO DE SUPERUSER O ADMIN
			$query = $this->db->query("SELECT
	sup.*
FROM
	sec_user su
	JOIN sec_user_privilege sup ON (su.sec_user_id = sup.sec_user_id)
	JOIN sec_privilege sp       ON (sup.sec_privilege_id = sp.sec_privilege_id)
WHERE
	su.sec_user_id = '$id'
	AND sp.value = 'OrgReports'
ORDER BY
	sup.cnf_org_id;
			");
			$result = $query->result();			

			$orgs = "";
			foreach ($result as $key => $value) {
				$orgs .= $value->cnf_org_id . ",";
			}			
			$orgs = $orgs == "" ? 0 : substr($orgs, 0, -1);
			
			return "AND co.cnf_org_id IN ($orgs)";
		}

		return ""; //TIENE PRIVILEGIO DE SUPERUSER O ADMIN, DE MODO QUE NO HAY RESTRICCION EN WHERE Y LISTARA TODAS LAS ORGANIZACIONES
	}

	/**
	 * CONSULTAS REPORTES FLOTAS
	 */

	public function getAllCOrgFleets()
	{
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

	public function getOrgByTypeAndIdFleets($type, $id){
		$query = $this->db->query("SELECT
	cnf_org_id AS c_org_id,	
	name AS name,	
	ipaddress AS ip,
	value AS almacen_id
FROM
	cnf_org
WHERE
	orgtype = $type
	AND cnf_org_id = '$id'
ORDER BY
	1;");
		return $query->result();
	}

	public function getCOrgByTypeFleets($type){
		$query = $this->db->query("SELECT
	cnf_org_id AS c_org_id,	
	name AS name,	
	ipaddress AS ip,
	value AS almacen_id
FROM
	cnf_org
WHERE
	orgtype = $type
ORDER BY
	1;");
		return $query->result();
	}
	
}