<?php
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
class Reception extends CommonObject
{
   var $db; // !< To store db handler
	var $error; // !< To return error code (or message)
	var $errors = array (); // !< To return several error codes (or messages)
	var $element = 'reception'; // !< Id that identify managed objects
	var $table_element = 'reception'; // !< Name of table without prefix where object is stored
	protected $ismultientitymanaged = 1; // 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
	var $value_array = array('fk_reprise','date_reception','km','etat_conforme','comm_etat','fk_site_actuel','presentation_produit','fk_receptionnaire','fk_financeur','buyer');

  var $id;
  var $fk_reprise;
  var $date_reception;
  var $km;
  var $etat_conforme;
  var $comm_etat;
  var $fk_site_actuel;
  var $presentation_produit;
  var $fk_receptionnaire;
  var $fk_financeur;
  var $buyer;

  function __construct($db) {
		global $conf, $user, $langs;
 		$this->db = $db;
	}

  function create() {
		global $conf, $langs;
		$error = 0;
		foreach($this->value_array as $value){
			if (isset($this->{$value}))
			$this->{$value} = trim($this->{$value});
		}

		if (empty($this->fk_reprise)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'fk_reprise');
			}
		if (empty($this->fk_receptionnaire)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'fk_receptionnaire');
			}
		if (empty($this->fk_site_actuel)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'fk_site_actuel');
		}
		if (empty($this->km)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'km');
		}
		if (empty($this->date_reception)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'date_reception');
		}

		if (! $error) {
			// Insert request
			$sql = "INSERT INTO " . MAIN_DB_PREFIX . "reception(";
			foreach ($this->value_array as $value){
				$sql .= "`" . $value . "`, ";
			}
			$sql = substr($sql,0,-2);
			$sql .= ") VALUES ( ";
			foreach ($this->value_array as $value){
				if (strpos($value,'date')!==false) {
					$sql .= (dol_strlen($this->{$value}) !=0 ? "'" . $this->db->idate($this->{$value}) . "'" : "null") . ", ";
				} elseif (strpos($value,'fk')!==false) {
					$sql .= (isset($this->{$value})  ? "'" . $this->{$value} . "'" : "null") . ", ";
				} else {
					$sql .= (isset($this->{$value})  ? "'" . $this->db->escape($this->{$value}) . "'" : "null") . ", ";
				}
			}
			$sql = substr($sql,0,-2);
			$sql .= ")";

			$this->db->begin();
			dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
				$this->errors[] = "Error " . $sql;
			}
		if (! $error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "reception");
		}
    // Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::create " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return $sql;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}
	}

  function update() {
  	global $langs;
		foreach($this->value_array as $value){
			if (isset($this->{$value}))
			$this->{$value} = trim($this->{$value});
		}

		if (empty($this->fk_reprise)) {
			$error ++;
			$this->errors[] = 'ErrorFieldRequired fk_reprise';
			}
		if (empty($this->fk_receptionnaire)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'fk_receptionnaire');
			}
		if (empty($this->fk_site_actuel)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'fk_site_actuel');
		}
		if (empty($this->km)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'km');
		}
		if (empty($this->date_reception)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'date_reception');
		}

		if (! $error) {

			$sql = "UPDATE " . MAIN_DB_PREFIX . "reception SET ";
			foreach ($this->value_array as $value){
				if (strpos($value,'date')!==false) {
					$sql .= $value . '=' . (dol_strlen($this->{$value}) !=0 ? "'" . $this->db->idate($this->{$value}) . "'" : "null") . ", ";
				} elseif (strpos($value,'fk')!==false) {
					$sql .= $value . '=' . (isset($this->{$value})  ? "'" . $this->{$value} . "'" : "null") . ", ";
				} else {
					$sql .= $value . '=' . (isset($this->{$value})  ? "'" . $this->db->escape($this->{$value}) . "'" : "null") . ", ";
				}
			}
			$sql = substr($sql,0,-2);
			$sql .= " WHERE rowid=" . $this->id;

			$this->db->begin();
			dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
				$this->errors[] = "Error " . $sql;
			}
		}
		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::update " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return $this->errors;
		} else {
			$this->db->commit();
			return 1;
		}
	}


	public function delete(){
		$error = 0;
		$this->db->begin();
		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "reception";
			$sql .= " WHERE rowid=" . $this->id;
			dol_syslog(get_class($this) . "::delete sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}

		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::delete " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return $sql;
		} else {
			$this->db->commit();
			return 1;
		}
	}



function fetch($recepid) {
		global $langs;
		$sql = "SELECT rowid,";
		foreach ($this->value_array as $value){
			$sql .= $value . ", ";
		}
		$sql = substr($sql,0,-2);
		$sql .= " FROM " . MAIN_DB_PREFIX . "reception ";
		$sql .= " WHERE rowid = " . $recepid;
		dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
 		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
 				$obj = $this->db->fetch_object($resql);
				$this->id = $obj->rowid;
        foreach ($this->value_array as $value){
					$this->{$value} = $obj->{$value};
				}
				$this->db->free($resql);
				return 1;
			} else {
				$this->db->free($resql);
				return 0;
			}
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
			return -1;
		}
	}

  function fetchbyrep($repid) {
		$sql = "SELECT rowid, ";
		foreach ($this->value_array as $value){
			$sql .= $value . ", ";
		}
		$sql = substr($sql,0,-2);
		$sql .= " FROM " . MAIN_DB_PREFIX . "reception ";
		$sql .= " WHERE fk_reprise = " . $repid;
		dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
 		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
 				$obj = $this->db->fetch_object($resql);
				$this->id = $obj->rowid;
        foreach ($this->value_array as $value){
					$this->{$value} = $obj->{$value};
				}
				$this->db->free($resql);
				return 1;
			} else {
				$this->db->free($resql);
				return 0;
			}
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
			return -1;
		}
 	}

}