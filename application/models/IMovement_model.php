<?php
class IMovement_model extends CI_Model {
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
	 * Metodo de prueba
	 */
	public function getByOrgId($params) {
		$whereOrg = 'mheader.c_org_id IN ('.$params[0].') AND';

		$sql = "SELECT
 FIRST(org.c_org_id) AS c_org_id,
 product.c_product_id,
 FIRST(TRIM(productgroup.description)) AS productgroup_code,
 FIRST(productgroup.name) AS productgroup_name,
 FIRST(productuom.name) AS uom_name,
 product.name AS product_name,

 SUM(stock_manual.sale_qty) AS _countsale,
 --SUM(stock_manual.in), SUM(stock_manual.out),
 SUM(stock_manual.in - stock_manual.out) AS _stk_quantity,
 SUM(stock_manual.in - stock_manual.out) AS _stk_real,
 FIRST(COALESCE(stock.quantity,0)) AS stk_quantity,
 FIRST(COALESCE(stock.amount,0)) AS stk_amount,

 (SUM(stock_manual.sale_qty) * SUM(stock_manual.sale_amount)) AS _amountsale,
 (SUM(stock_manual.in - stock_manual.out) * SUM(stock_manual.sale_amount)) AS _amount_real

FROM c_product product
JOIN c_productgroup productgroup ON (product.c_productgroup_id = productgroup.c_productgroup_id)
JOIN c_productuom productuom ON (product.c_productuom_id = productuom.c_productuom_id)
JOIN i_movementdetail mdetail ON (mdetail.c_product_id = product.c_product_id)
JOIN i_movementheader mheader ON (mheader.i_movementheader_id = mdetail.i_movementheader_id)
JOIN i_movementtype movementtype ON (mheader.i_movementtype_id = movementtype.i_movementtype_id)
LEFT JOIN i_stock stock ON (product.c_product_id = stock.c_product_id)

JOIN  (
SELECT
product.value as product_value,
SUM(CASE WHEN movementtype.optype = 1 THEN
 mdetail.quantity
ELSE
 0.000
END) AS in,
SUM(CASE WHEN movementtype.optype = 2 THEN
 mdetail.quantity
ELSE
 0.0000
END) AS out,
SUM(CASE WHEN movementtype.value = '01' THEN
 mdetail.quantity
ELSE
 0.0000
END) AS sale_qty,--Venta cantidad
SUM(CASE WHEN movementtype.value = '01' THEN
 mdetail.unitprice
ELSE
 0.0000
END) AS sale_amount--Venta soles


FROM c_product product
JOIN i_movementdetail mdetail ON (mdetail.c_product_id = product.c_product_id)
JOIN i_movementheader mheader ON (mheader.i_movementheader_id = mdetail.i_movementheader_id)
JOIN i_movementtype movementtype ON (mheader.i_movementtype_id = movementtype.i_movementtype_id)
WHERE
$whereOrg
mheader.created <= '".$params[1]."'
GROUP BY product.value
 ) stock_manual ON (product.value = stock_manual.product_value)

JOIN c_org org ON(mheader.c_org_id = org.c_org_id)

WHERE
$whereOrg
mheader.created BETWEEN '".$params[1]."' AND '".$params[2]."' AND
mdetail.c_product_id IN (".$params[3].")
GROUP BY product.c_product_id
;
";

		/*$sql = "SELECT
 FIRST(org.c_org_id) AS c_org_id,
 product.c_product_id,
 FIRST(TRIM(productgroup.description)) AS productgroup_code,
 FIRST(productgroup.name) AS productgroup_name,
 FIRST(productuom.name) AS uom_name,
 product.name AS product_name,

SUM(CASE WHEN movementtype.value = '01' THEN
  mdetail.quantity
 END
 ) AS _countsale,
 --SUM(stock_manual.in), SUM(stock_manual.out),
 SUM(stock_manual.in - stock_manual.out) AS _stk_quantity,
 
 SUM(stock_manual.in - stock_manual.out) - SUM(CASE WHEN movementtype.value = '01' THEN
  mdetail.quantity
 END
 ) AS _stk_real,
 FIRST(stock.quantity) AS stk_quantity,
 FIRST(stock.amount) AS stk_amount

FROM c_product product
JOIN c_productgroup productgroup ON (product.c_productgroup_id = productgroup.c_productgroup_id)
JOIN c_productuom productuom ON (product.c_productuom_id = productuom.c_productuom_id)
JOIN i_movementdetail mdetail ON (mdetail.c_product_id = product.c_product_id)
JOIN i_movementheader mheader ON (mheader.i_movementheader_id = mdetail.i_movementheader_id)
JOIN i_movementtype movementtype ON (mheader.i_movementtype_id = movementtype.i_movementtype_id)
JOIN i_stock stock ON (product.c_product_id = stock.c_product_id)

JOIN  (
SELECT
product.value as product_value,
SUM(CASE WHEN movementtype.optype = 1 THEN
 mdetail.quantity
ELSE
 0.000
END) AS in,
SUM(CASE WHEN movementtype.optype = 2 THEN
 mdetail.quantity
ELSE
 0.0000
END) AS out

FROM c_product product
JOIN i_movementdetail mdetail ON (mdetail.c_product_id = product.c_product_id)
JOIN i_movementheader mheader ON (mheader.i_movementheader_id = mdetail.i_movementheader_id)
JOIN i_movementtype movementtype ON (mheader.i_movementtype_id = movementtype.i_movementtype_id)
WHERE
$whereOrg
mheader.created <= '".$params[1]."'
GROUP BY product.value
 ) stock_manual ON (product.value = stock_manual.product_value)

JOIN c_org org ON(mheader.c_org_id = org.c_org_id)

WHERE
$whereOrg
mheader.created BETWEEN '".$params[1]."' AND '".$params[2]."' AND
mdetail.c_product_id IN (".$params[3].")
GROUP BY product.c_product_id
;
";*/
		log_message('error', 'SQL (getByOrgId): '.$sql);

		//echo $sql;
		$query = $this->db->query($sql, $params);
		return $query->result();
		//return $sql;
	}

	public function getProductgroupByMoviments($params) {
		$whereOrg = 'mheader.c_org_id IN ('.$params[0].') AND';

		$sql = "SELECT
 product.c_product_id,
 FIRST(TRIM(productgroup.description)) AS productgroup_code,
 FIRST(productgroup.name) AS productgroup_name,
 FIRST(productuom.name) AS uom_name,
 FIRST(product.name) AS product_name,
 FIRST(TRIM(product.value)) AS product_code
FROM c_product product
JOIN c_productgroup productgroup ON (product.c_productgroup_id = productgroup.c_productgroup_id)
JOIN c_productuom productuom ON (product.c_productuom_id = productuom.c_productuom_id)
JOIN i_movementdetail mdetail ON (mdetail.c_product_id = product.c_product_id)
JOIN i_movementheader mheader ON (mheader.i_movementheader_id = mdetail.i_movementheader_id)
JOIN i_movementtype movementtype ON (mheader.i_movementtype_id = movementtype.i_movementtype_id)
LEFT JOIN i_stock stock ON (product.c_product_id = stock.c_product_id)
JOIN (
	SELECT
	product.c_product_id,
	SUM(CASE WHEN movementtype.optype = 1 THEN
	 mdetail.quantity
	ELSE
	 0.000
	END) AS in,
	SUM(CASE WHEN movementtype.optype = 2 THEN
	 mdetail.quantity
	ELSE
	 0.0000
	END) AS out

	FROM c_product product
	JOIN i_movementdetail mdetail ON (mdetail.c_product_id = product.c_product_id)
	JOIN i_movementheader mheader ON (mheader.i_movementheader_id = mdetail.i_movementheader_id)
	JOIN i_movementtype movementtype ON (mheader.i_movementtype_id = movementtype.i_movementtype_id)
	JOIN c_org org ON(mheader.c_org_id = org.c_org_id)
	WHERE
	$whereOrg
	mheader.created <= '".$params[1]."'
	AND org.description = 'M'
	GROUP BY product.c_product_id
 ) stock_manual ON (product.c_product_id = stock_manual.c_product_id)

JOIN c_org org ON(mheader.c_org_id = org.c_org_id)
WHERE
 $whereOrg
 mheader.created BETWEEN '".$params[1]."' AND '".$params[2]."'
 AND org.description = 'M'
GROUP BY product.c_product_id
ORDER BY product.c_product_id
;";
		/*$sql = "SELECT
 FIRST(TRIM(productgroup.description)) AS productgroup_code,
 FIRST(productgroup.name) AS productgroup_name,

FROM c_product product
JOIN c_productgroup productgroup ON (product.c_productgroup_id = productgroup.c_productgroup_id)
JOIN i_movementdetail mdetail ON (mdetail.c_product_id = product.c_product_id)
JOIN i_movementheader mheader ON (mheader.i_movementheader_id = mdetail.i_movementheader_id)

JOIN c_org org ON(mheader.c_org_id = org.c_org_id)
WHERE
 $whereOrg mheader.created BETWEEN '".$params[1]."' AND '".$params[2]."'
GROUP BY mheader.c_org_id, product.value
ORDER BY _countsale DESC
;
";*/

		log_message('error', 'SQL (getProductgroupByMoviments): '.$sql);
		//echo $sql;
		$query = $this->db->query($sql, $params);
		return $query->result();
		//return $sql;
	}
}
