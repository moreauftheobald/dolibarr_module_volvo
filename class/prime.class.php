<?php
/* Copyright (C) 2007-2012  Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2014       Juanjo Menent       <jmenent@2byte.es>
 * Copyright (C) 2015       Florian Henry       <florian.henry@open-concept.pro>
 * Copyright (C) 2015       Raphaël Doursenaud  <rdoursenaud@gpcsolutions.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';

/**
 * Put here description of your class
 */
class Prime extends CommonObject
{
	var $db; // !< To store db handler
	var $error; // !< To return error code (or message)
	var $errors = array (); // !< To return several error codes (or messages)
	var $element = 'payment_primes'; // !< Id that identify managed objects
	var $table_element = 'payment_primes'; // !< Name of table without prefix where object is stored
	var $id;
	var $date;
	var $fk_commercial;
	var $total;
	var $date_valid;
	var $fk_statut;
	var $lines = array();


	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db) {
		$this->db = $db;
	}

	/**
	 *
	 * @param unknown $prodid
	 */

	function create($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;

		if (isset($this->fk_commercial))
			$this->fk_commercial = trim($this->fk_commercial);
		if (isset($this->total))
			$this->total = trim($this->total);
			if (isset($this->fk_statut))
				$this->fk_statut = trim($this->fk_statut);

		if (empty($this->fk_commercial)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'commercial');
		}
		if (empty($this->date)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'date');
		}

		if (! $error) {
			// Insert request
			$sql = "INSERT INTO " . MAIN_DB_PREFIX . "payment_primes(";

			$sql .= "date,";
			$sql .= "fk_commercial,";
			$sql .= "total,";
			$sql .= "date_valid,";
			$sql .= "fk_statut";

			$sql .= ") VALUES (";

			$sql .= " '" . $this->db->idate($this->date) . "',";
			$sql .= " " . (! isset($this->fk_commecial) ? 'NULL' : "'" . $this->fk_commercial . "'") . ",";
			$sql .= " " . (! isset($this->total) ? 'NULL' : "'" . price2num($this->total) . "'"). ",";
			$sql .= " " . (! isset($this->date_valid) ? 'NULL' : "'" . $this->db->idate($this->date_valid) . "'"). ",";
			$sql.= " 0";
			$sql .= ")";

			$this->db->begin();

			dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}


		if (! $error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "payment_primes");

			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				// // Call triggers
				include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('PAYMENT_PRIMES_CREATE', $this, $user, $langs, $conf);
				if ($result < 0) {
					$error ++;
					$this->errors = $interface->errors;
				}
				// // End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::create " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return $this->id;
		}

	}

	function fetch($id) {
		global $langs;
		$sql = "SELECT";
		$sql .= " rowid,";
		$sql .= " date,";
		$sql .= " fk_commercial,";
		$sql .= " total,";
		$sql .= " date_valid,";
		$sql .= " fk_statut";

		$sql .= " FROM " . MAIN_DB_PREFIX . "payment_primes";
		$sql .= " WHERE rowid = " . $id;

		dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				$this->date = $this->db->jdate($obj->date);
				$this->fk_commercial = $obj->fk_commercial;
				$this->date_valid = $obj->date_valid;
				$this->total = $obj->total;
				$this->fk_statut = $obj->fk_statut;
			}
			$this->db->free($resql);
			$this->fetch_lines();
			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
			return - 1;
		}
	}

	function update($user = null, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;

		if (isset($this->fk_commercial))
			$this->fk_commercial = trim($this->fk_commercial);
		if (isset($this->total))
			$this->total = trim($this->total);
		if (isset($this->fk_statut))
			$this->fk_statut = trim($this->fk_statut);

		if (empty($this->fk_commercial)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'commercial');
		}
		if (empty($this->date)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'date');
		}

		if (! $error) {
			// Update request
			$sql = "UPDATE " . MAIN_DB_PREFIX . "lead SET";

			$sql .= " date=" . (dol_strlen($this->date) != 0 ? "'" . $this->db->idate($this->date) . "'" : 'null') . ",";
			$sql .= " fk_commercial=" . (isset($this->fk_commercial) ? "'" . $this->db->escape($this->fk_commercial) . "'" : "null") . ",";
			$sql .= " total=" . (isset($this->total) ? "'" . $this->db->escape($this->total) . "'" : "null") . ",";
			$sql .= " date_valid=" . (dol_strlen($this->date_valid) != 0 ? "'" . $this->db->idate($this->date_valid) . "'" : 'null'). ",";
			$sql .= " total=" . (isset($this->fk_statut) ? "'" . $this->db->escape($this->fk_statut) . "'" : "null") ;

			$sql .= " WHERE rowid=" . $this->id;

			$this->db->begin();

			dol_syslog(get_class($this) . "::update sql=" . $sql, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}

		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				// // Call triggers
				include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('PAYMENT_PRIMES_MODIFY', $this, $user, $langs, $conf);
				if ($result < 0) {
					$error ++;
					$this->errors = $interface->errors;
				}
				// // End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::update " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}


	function delete($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;

		$this->db->begin();

		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				// // Call triggers
				include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('PAYMENT_PRIMES_DELETE', $this, $user, $langs, $conf);
				if ($result < 0) {
					$error ++;
					$this->errors = $interface->errors;
				}
				// // End call triggers
			}
		}

		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "payment_primes_det";
			$sql .= " WHERE fk_payment_primes=" . $this->id;

			dol_syslog(get_class($this) . "::delete sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}

		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "payment_primes";
			$sql .= " WHERE rowid=" . $this->id;

			dol_syslog(get_class($this) . "::delete sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::delete " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	function fetch_lines(){
		$this->lines=array();

		$sql = "SELECT rowid, fk_payment_prime, fk_commande, prime_date, prime_payed, ecart, commentaire ";
		$sql.= "FROM " . MAIN_DB_PREFIX ."payment_primes_det ";
		$sql.= "WHERE fk_payment_prime =". $this->id;

		dol_syslog(get_class($this)."::fetch_lines", LOG_DEBUG);
		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);

			$i = 0;
			while ($i < $num)
			{
				$objp = $this->db->fetch_object($result);

				$line = new PrimeLigne($this->db);
				$line->id = $objp->rowid;
				$line->fk_payment_prime = $objp->fk_payment_prime;
				$line->prime_date = $objp->prime_date;
				$line->prime_payed = $objp->prime_payed;
				$line->ecart = $objp->ecart;
				$line->commentaire = $objp->commentaire;

				$this->lines[$i] = $line;

				$i++;
				}

				$this->db->free($result);

				return 1;
				}
				else
				{
					$this->error=$this->db->error();
					return -3;
				}


	}

}
class PrimeLigne extends CommonObjectLine
{
	public $element='payment_prines_det';
	public $table_element='payment_prines_det';

	var $id;
	var $fk_payment_prime;
	var $fk_commande;
	var $prime_date;
	var $prime_payed;
	var $ecart;
	var $payment;
	var $commentaire;

	function __construct($db)
	{
		$this->db = $db;
	}


	function create($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;

		if (isset($this->fk_paiment_prime))
			$this->fk_paiment_prime = trim($this->fk_paiment_prime);
		if (isset($this->fk_commande))
			$this->fk_commande = trim($this->fk_commande);
		if (isset($this->prime_date))
			$this->prime_date = trim($this->prime_date);
		if (isset($this->prime_payed))
			$this->prime_payed = trim($this->prime_payed);
		if (isset($this->ecart))
			$this->ecart = trim($this->ecart);
		if (isset($this->payment))
			$this->payment = trim($this->payment);
		if (isset($this->commentaire))
			$this->commentaire = trim($this->commentaire);

		if (empty($this->fk_payement_prime)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'payment_prime');
		}
		if (empty($this->fk_commande)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'commande');
		}
		if (empty($this->prime_date)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'prime a date');
		}
		if (empty($this->prime_payed)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'prime deja payees');
		}
		if (empty($this->ecart)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'ecart');
		}

		if (! $error) {
			// Insert request
			$sql = "INSERT INTO " . MAIN_DB_PREFIX . "payment_primes(";
			$sql .= "fk_payment_prime,";
			$sql .= "fk_commande,";
			$sql .= "prime_date,";
			$sql .= "prime_payed,";
			$sql .= "ecart,";
			$sql .= "payment,";
			$sql .= "commentaire";

			$sql .= ") VALUES (";
			$sql .= " " . (! isset($this->fk_payment_prime) ? 'NULL' : "'" . $this->fk_payment_prime . "'") . ",";
			$sql .= " " . (! isset($this->fk_commande) ? 'NULL' : "'" . $this->fk_commande . "'") . ",";
			$sql .= " " . (! isset($this->prime_date) ? 'NULL' : "'" . price2num($this->prime_date) . "'") . ",";
			$sql .= " " . (! isset($this->prime_payed) ? 'NULL' : "'" . price2num($this->prime_payed) . "'") . ",";
			$sql .= " " . (! isset($this->ecart) ? 'NULL' : "'" . price2num($this->ecart) . "'") . ",";
			$sql .= " " . (! isset($this->payment) ? 'NULL' : "'" . price2num($this->payment) . "'") . ",";
			$sql .= " " . (! isset($this->commentaire) ? 'NULL' : "'" . $this->commentaire . "'");
			$sql .= ")";

			$this->db->begin();

			dol_syslog(get_class($this) . "::create sql=" . $sql, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}


		if (! $error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . "payment_primes_det");

			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				// // Call triggers
				include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('PAYMENT_PRIMES_DET_CREATE', $this, $user, $langs, $conf);
				if ($result < 0) {
					$error ++;
					$this->errors = $interface->errors;
				}
				// // End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::create " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return $this->id;
		}
	}

	function update($user = null, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;

	if (isset($this->fk_paiment_prime))
			$this->fk_paiment_prime = trim($this->fk_paiment_prime);
		if (isset($this->fk_commande))
			$this->fk_commande = trim($this->fk_commande);
		if (isset($this->prime_date))
			$this->prime_date = trim($this->prime_date);
		if (isset($this->prime_payed))
			$this->prime_payed = trim($this->prime_payed);
		if (isset($this->ecart))
			$this->ecart = trim($this->ecart);
		if (isset($this->payment))
			$this->payment = trim($this->payment);
		if (isset($this->commentaire))
			$this->commentaire = trim($this->commentaire);

		if (empty($this->fk_payement_prime)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'payment_prime');
		}
		if (empty($this->fk_commande)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'commande');
		}
		if (empty($this->prime_date)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'prime a date');
		}
		if (empty($this->prime_payed)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'prime deja payees');
		}
		if (empty($this->ecart)) {
			$error ++;
			$this->errors[] = $langs->trans('ErrorFieldRequired', 'ecart');
		}

		if (! $error) {
			// Update request
			$sql = "UPDATE " . MAIN_DB_PREFIX . "lead SET";
			$sql .= " fk_payment_prime=" . (isset($this->fk_payment_prime) ? "'" . $this->db->escape($this->fk_payment_prime) . "'" : "null") . ",";
			$sql .= " fk_commande=" . (isset($this->fk_commande) ? "'" . $this->db->escape($this->fk_commande) . "'" : "null") . ",";
			$sql .= " prime_date=" . (isset($this->prime_date) ? "'" . $this->db->escape($this->prime_date) . "'" : "null") . ",";
			$sql .= " prime_payed=" . (isset($this->prime_payed) ? "'" . $this->db->escape($this->prime_payed) . "'" : "null") . ",";
			$sql .= " ecart=" . (isset($this->ecart) ? "'" . $this->db->escape($this->ecart) . "'" : "null") . ",";
			$sql .= " payment=" . (isset($this->payment) ? "'" . $this->db->escape($this->payment) . "'" : "null") . ",";
			$sql .= " commentaire=" . (isset($this->commentaire) ? $this->commentaire : "null");

			$sql .= " WHERE rowid=" . $this->id;

			$this->db->begin();

			dol_syslog(get_class($this) . "::update sql=" . $sql, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}
		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.
				// // Call triggers
				include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('PAYMENT_PRIMES_DET_MODIFY', $this, $user, $langs, $conf);
				if ($result < 0) {
					$error ++;
					$this->errors = $interface->errors;
				}
				// // End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::update " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}

	function fetch($id) {
		global $langs;
		$sql = "SELECT";
		$sql .= " rowid,";
		$sql .= " fk_payment_prime,";
		$sql .= " fk_commande,";
		$sql .= " prime_date,";
		$sql .= " prime_payed,";
		$sql .= " ecart,";
		$sql .= " payment,";
		$sql .= " commentaire";

		$sql .= " FROM " . MAIN_DB_PREFIX . "payment_primes_det";
		$sql .= " WHERE rowid = " . $id;

		dol_syslog(get_class($this) . "::fetch sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			if ($this->db->num_rows($resql)) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				$this->fk_payment_prime = $obj->fk_payment_prime;
				$this->fk_commande = $obj->fk_commande;
				$this->prime_date = $obj->prime_date;
				$this->prime_payed = $obj->prime_payed;
				$this->ecart = $obj->ecart;
				$this->payment = $obj->payment;
				$this->commentaire = $obj->commentaire;
			}
			$this->db->free($resql);
			return 1;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::fetch " . $this->error, LOG_ERR);
			return - 1;
		}
	}


	function delete($user, $notrigger = 0) {
		global $conf, $langs;
		$error = 0;

		$this->db->begin();

		if (! $error) {
			if (! $notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				// // Call triggers
				include_once DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php';
				$interface = new Interfaces($this->db);
				$result = $interface->run_triggers('PAYMENT_PRIMES_DET_DELETE', $this, $user, $langs, $conf);
				if ($result < 0) {
					$error ++;
					$this->errors = $interface->errors;
				}
				// // End call triggers
			}
		}

		if (! $error) {
			$sql = "DELETE FROM " . MAIN_DB_PREFIX . "payment_primes_det";
			$sql .= " WHERE rowid=" . $this->id;

			dol_syslog(get_class($this) . "::delete sql=" . $sql);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$error ++;
				$this->errors[] = "Error " . $this->db->lasterror();
			}
		}

		// Commit or rollback
		if ($error) {
			foreach ( $this->errors as $errmsg ) {
				dol_syslog(get_class($this) . "::delete " . $errmsg, LOG_ERR);
				$this->error .= ($this->error ? ', ' . $errmsg : $errmsg);
			}
			$this->db->rollback();
			return - 1 * $error;
		} else {
			$this->db->commit();
			return 1;
		}
	}
}