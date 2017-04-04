<?php
function stat_prov1($year, $monthlist){
	global $db;

	$sql = "SELECT  ";
	$sql.= "MONTH(event.datep) as Mois, ";
	$sql.= "COUNT(DISTINCT c.rowid) as nb_facture, ";
	$sql.= "SUM(c.total_ht) AS catotalht, ";
	$sql.= "SUM(IF(lef.type = 1,1,0)) AS nbporteur, ";
	$sql.= "SUM(IF(lef.type = 2,1,0)) AS nbtracteur ";
	$sql.= "FROM llx_commande AS c ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "actioncomm AS event ON event.fk_element = c.rowid AND event.elementtype = 'order' AND event.label LIKE '%Commande V% classée Facturée%' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "element_element AS elm ON elm.fk_source = c.rowid AND elm.sourcetype ='commande' AND elm.targettype='lead' ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead as l on elm.fk_target = l.rowid ";
	$sql.= "LEFT JOIN " . MAIN_DB_PREFIX . "lead_extrafields lef on lef.fk_object = l.rowid ";
	$sql.= "WHERE YEAR(event.datep) ='" . $year . "' ";
	if(!empty($monthlist)){
		$sql.= "AND MONTH(event.datep) IN (" . $monthlist . ") ";
	}
	if ($commercial > 0){
		$sql.= "AND l.fk_user_resp = '" . $commercial . "' ";
	}
	$sql.= "GROUP BY MONTH(event.datep) ";

	$resql = $db->query($sql);
	if($resql){
		$result =array();
		while($obj = $db->fetch_object($resql)){
			$result['nb_fact'][$obj->Mois] = $obj->nb_facture;
			$result['catotalht'][$obj->Mois] = $obj->catotalht;
			$result['nbporteur'][$obj->Mois] = $obj->nbporteur;
			$result['nbtracteur'][$obj->Mois] = $obj->nbtracteur;
		}
		return $result;
	}else{
		return -1;
	}

}