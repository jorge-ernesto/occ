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

	public function getAllCOrg($type)
	{
		$query = $this->db->query("SELECT
	org.c_org_id,
	org.name,
	remote.ip
FROM
	c_org org
JOIN mig_cowmap cowmap ON (
	org.c_org_id = cowmap.c_org_id
)
JOIN mig_remote remote ON (
	cowmap.id_remote = mig_remote_id
)
WHERE
	org.description = ? AND remote.view = 1;", array($type));
		return $query->result();
	}

	public function getOrgByTypeAndId($type,$id)
	{
		$query = $this->db->query("SELECT
	client.name AS client_name,
	client.taxid,
	org.c_org_id,
	org.name,
	org.initials,
	org.value,
	-- remote.ip,
	-- warehouse.description AS almacen_id
	'172.18.8.12' AS ip,
	'003' AS almacen_id
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
	org.description = '$type'
AND org.c_org_id = ? AND remote.view = 1
ORDER BY client.c_client_id ASC;", array($id));
		
error_log("Query getOrgByTypeAndId");
error_log("
	SELECT
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
		org.description = '$type'
	AND org.c_org_id = '$id' AND remote.view = 1
	ORDER BY client.c_client_id ASC;
");

		return $query->result();
	}

	public function getCOrgByType($type) {
		$query = $this->db->query("SELECT
	client.name AS client_name,
	client.taxid,
	org.c_org_id,
	org.name,
	org.initials,
	org.value,
	-- remote.ip,
	-- warehouse.description AS almacen_id
	'172.18.8.12' AS ip,
	'003' AS almacen_id
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
	org.description = ? AND remote.view = 1
ORDER BY client.c_client_id ASC;", array($type));

error_log("Query getCOrgByType");
error_log("
SELECT
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
	org.description = '$type' AND remote.view = 1
ORDER BY client.c_client_id ASC;
");

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
}