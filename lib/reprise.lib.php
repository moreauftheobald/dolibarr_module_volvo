<?php
function reprise_prepare_head($object,$lines)
{
	global $langs, $conf;
  
  $h = 1;
	$head = array();
	 
  foreach ($lines as $obj) {
  	$head[$h][0] = dol_buildpath("/volvo/reprise/card.php", 1) . '?id=' . $object->id .'&action=&repid=' . $obj->id;
	  $head[$h][1] = 'Reprise '. $h;
	  $head[$h][2] = 'info' . $obj->id;
	  $h ++;
  }
	
  $head[$h][0] = dol_buildpath("/volvo/reprise/card.php", 1) . '?id=' . $object->id . '&repid=&action=create';
	$head[$h][1] = 'Ajouter une reprise';
	$head[$h][2] = 'info_create';
	$h ++;
	
 	return $head;
}

function info_prepare_head($object, $list_exp,$repid){
	global $langs, $conf;
	
	$langs->load("lead@lead");
	
	$h = 0;
	$head = array();
	
	$head[$h][0] = dol_buildpath("/volvo/reprise/card.php", 1) . '?id=' . $object->id . '&repid='. $repid;
	$head[$h][1] = 'informations générales';
	$head[$h][2] = 'card';
	$h ++;
	
	foreach ($list_exp as $exp){
		if ($h<3){
			$head[$h][0] = dol_buildpath("/volvo/reprise/expertise.php", 1) . '?action=&id=' . $object->id . '&repid='. $repid . '&exp=' . $exp;
			$head[$h][1] = 'Expertise ' . $h;
			$head[$h][2] = 'exp' . $exp;
			$h ++;
		}
	}
	
	if ($h<3){
	$head[$h][0] = dol_buildpath("/volvo/reprise/expertise.php", 1) . '?action=create&id=' . $object->id . '&repid='. $repid . '&exp=';
	$head[$h][1] = 'Ajouter une expertise';
	$head[$h][2] = 'exp_create';
	$h ++;
	}
	
	$head[$h][0] = dol_buildpath("/volvo/reprise/reception.php", 1) . '?action=&id=' . $object->id . '&repid='. $repid . '&exp=';
	$head[$h][1] = 'réception du véhicule';
	$head[$h][2] = 'recep';
	$h ++;
  
	
	return $head;
}

function info_prepare_head2($list_exp,$repid){
	global $langs, $conf;
	
	$langs->load("lead@lead");
	
	$h = 0;
	$head = array();
	
	$head[$h][0] = dol_buildpath("/volvo/reprise/card2.php", 1) . '?id=' . $repid;
	$head[$h][1] = 'informations générales';
	$head[$h][2] = 'card';
	$h ++;
	
	foreach ($list_exp as $exp){
		if ($h<3){
			$head[$h][0] = dol_buildpath("/volvo/reprise/expertise2.php", 1) . '?action=&id=' . $repid . '&exp=' . $exp;
			$head[$h][1] = 'Expertise ' . $h;
			$head[$h][2] = 'exp' . $exp;
			$h ++;
		}
	}
	
	if ($h<3){
	$head[$h][0] = dol_buildpath("/volvo/reprise/expertise2.php", 1) . '?action=create&id=' . $repid . '&exp=';
	$head[$h][1] = 'Ajouter une expertise';
	$head[$h][2] = 'exp_create';
	$h ++;
	}
	
	$head[$h][0] = dol_buildpath("/volvo/reprise/reception2.php", 1) . '?action=&id=' . $repid . '&exp=';
	$head[$h][1] = 'réception du véhicule';
	$head[$h][2] = 'recep';
	$h ++;
  
	
	return $head;
}