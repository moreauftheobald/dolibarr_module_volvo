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
class CostPrice
{
	public $errors = array();
	public $error;
	private $db;

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
	public function calcCostPrice() {
		$prod_array = array();

		$this->db->begin();

		$sql = 'SELECT prod.rowid';
		$sql .= ' FROM ' . MAIN_DB_PREFIX . 'product as prod';
		$sql .= ' LEFT JOIN ' . MAIN_DB_PREFIX . 'product_extrafields as prodextra';
		$sql .= '  ON prod.rowid=prodextra.fk_object';
		$sql .= '  WHERE prodextra.notupdatecostreal IS NULL OR notupdatecostreal=0';

		dol_syslog(__METHOD__, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$prod_array[$obj->rowid] = $obj->rowid;
			}
			$this->db->free($resql);
		} else {
			$error ++;
			$this->error = "Error " . $this->db->lasterror();
			$this->errors[] = $this->error;
			dol_syslog(get_class($this) . "::" . __METHOD__ . $this->error, LOG_ERR);
		}

		if (empty($error) && count($prod_array) > 0) {

			foreach ( $prod_array as $prodid ) {
				$sql = 'SELECT AVG(cmdetextra.buyingprice_real/cmdet.qty) as avgprice';
				$sql .= ' FROM ' . MAIN_DB_PREFIX . 'commandedet as cmdet';
				$sql .= ' INNER JOIN ' . MAIN_DB_PREFIX . 'commandedet_extrafields as cmdetextra';
				$sql .= '  ON cmdet.rowid=cmdetextra.fk_object';
				$sql .= ' WHERE cmdet.fk_product=' . $prodid;
				$sql .= ' GROUP BY cmdet.fk_product';

				dol_syslog(__METHOD__, LOG_DEBUG);
				$resql = $this->db->query($sql);
				if ($resql) {
					while ( $obj = $this->db->fetch_object($resql) ) {
						if (! empty($obj->avgprice)) {
							$sql_upd = 'UPDATE ' . MAIN_DB_PREFIX . 'product ';
							$sql_upd .= ' SET cost_price=' . $this->db->escape(price2num($obj->avgprice));
							$sql_upd .= ' WHERE rowid=' . $prodid;

							dol_syslog(__METHOD__, LOG_DEBUG);
							$resql_upd = $this->db->query($sql_upd);
							if (! $resql_upd) {
								$error ++;
								$this->error = "Error " . $this->db->lasterror();
								$this->errors[] = $this->error;
								dol_syslog(get_class($this) . "::" . __METHOD__ . $this->error, LOG_ERR);
							}
						}
					}
					$this->db->free($resql);
				} else {
					$error ++;
					$this->error = "Error " . $this->db->lasterror();
					$this->errors[] = $this->error;
					dol_syslog(get_class($this) . "::" . __METHOD__ . $this->error, LOG_ERR);
				}
			}
		}

		if (! empty($error)) {

			$this->db->rollback();

			return - 1 * $error;
		} else {

			$this->db->commit();

			return 1;
		}
	}
}