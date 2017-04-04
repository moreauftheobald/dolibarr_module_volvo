<?php
function stat_prov1($year, $monthlist){
	global $db;

	$sql = "SELECT  ";
	$sql.= "c.ref AS ref, ";
	$sql.= "CONCAT(u.firstname,' ',u.lastname) AS vendeur, ";
	$sql.= "s.nom AS client, ";
	$sql.= "DATE_FORMAT(c.date_commande,'%b-%Y') AS mois, ";
	$sql.= "MAX(detef.dt_invoice) AS last_update ";
	$sql.= "FROM llx_commande AS c ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS elm ON elm.fk_source = c.rowid AND elm.sourcetype ='commande' AND elm.targettype='lead' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead as l on elm.fk_target = l.rowid ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "user AS u on u.rowid = l.fk_user_resp ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "societe AS s on s.rowid = c.fk_soc ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commandedet as det on c.rowid = det.fk_commande ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commandedet as det on c.rowid = det.fk_commande ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "commandedet_extrafields AS detef on detef.fk_object=det.rowid ";
	$sql.= "GROUP BY MONTH(event.datep) ";
	$sql.= "HAVING (YEAR(last_update) ='" . $year . "'";
	if(!empty($monthlist)){
		$sql.= " AND MONTH(last_update) IN (" . $monthlist . ") ";
	}
	$sql.= ") OR (YEAR(mois) ='" . $year . "" ;
	if(!empty($monthlist)){
		$sql.= " AND MONTH(mois) IN (" . $monthlist . ") ";
	}
	$sql.=")";

	$resql = $db->query($sql);
	if($resql){
		$result =array();
		while($obj = $db->fetch_object($resql)){
			$result[$obj->ref]['vendeur'] = $obj->vendeur;
			$result[$obj->ref]['client'] = $obj->client;
			$result[$obj->ref]['mois'] = $obj->mois;
		}
		return $result;
	}else{
		return -1;
	}

}