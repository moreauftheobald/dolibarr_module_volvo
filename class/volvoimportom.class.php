<?php
/* Volvo
 * Copyright (C) 2015       Florian Henry		<florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 * \file volvo/class/volvoimportcmcust.class.php
 * \ingroup volvo
 * \brief File to load import files with XSLX format
 */
require_once 'volvoimport.class.php';

/**
 * Class to import consogazoil CSV specific files
 */
class VolvoImportom extends VolvoImport
{
	public $lines = array();
	protected $db;
	public $error;
	public $errors = array();
	protected $filesource;
	public $objWorksheet;
	public $sheetArray = array();
	public $columnArray = array();
	protected $objPHPExcel;
	protected $startcell;
	protected $maxcol;
	protected $maxrow;
	public $columnData;
	protected $tempTable;
	public $targetInfoArray = array();

	/**
	 * Constructor
	 *
	 * @param DoliDB $db
	 */
	function __construct($db) {
		global $conf, $langs;

		$langs->load('volvo@volvo');
		$langs->load('companies');

		$this->db = $db;

		$this->targetInfoArray[] = array(
				'column' => 'numom',
				'type' => 'text',
				'columntrans' => $langs->trans('Numéro d\'OM'),
				'table' => MAIN_DB_PREFIX . 'commande_fournisseur_extrafields',
				'tabletrans' => $langs->trans('commande fournisseur extrfields'),
				'filecolumntitle' => 'Numéro de commande',
				'editable' => 0,
				'noinsert' => 1,
				'isnumom' =>1
		);
		$this->targetInfoArray[] = array(
				'column' => 'dt_blockupdate',
				'type' => 'date',
				'columntrans' => $langs->trans('Date de bocage de modification'),
				'table' => MAIN_DB_PREFIX . 'commande_fournisseur_extrafields',
				'tabletrans' => $langs->trans('commande fournisseur extrfields'),
				'filecolumntitle' => 'Ultime date de modification',
				'editable' => 0,
				'noinsert' => 1

		);
		$this->targetInfoArray[] = array(
				'column' => 'dt_lim_annul',
				'type' => 'date',
				'columntrans' => $langs->trans('Date limite d\'annulation'),
				'table' => MAIN_DB_PREFIX . 'commande_fournisseur_extrafields',
				'tabletrans' => $langs->trans('commande fournisseur extrfields'),
				'filecolumntitle' => 'Ultime date d\'annulation',
				'editable' => 0,
				'noinsert' => 1

		);
		$this->targetInfoArray[] = array(
				'column' => 'date_livraison',
				'type' => 'date',
				'columntrans' => $langs->trans('Date de livraison demandée'),
				'table' => MAIN_DB_PREFIX . 'commande_fournisseur',
				'tabletrans' => $langs->trans('Commande Fournisseur'),
				'filecolumntitle' => 'Date de livraison demandée',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'dt_liv_maj',
				'type' => 'date',
				'columntrans' => $langs->trans('Date de livraison Mise a jour'),
				'table' => MAIN_DB_PREFIX . 'commande_fournisseur_extrafields',
				'tabletrans' => $langs->trans('commande fournisseur extrfields'),
				'filecolumntitle' => 'Date de livraison mise à jour',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'dt_fact',
				'type' => 'date',
				'columntrans' => $langs->trans('Date de facturation'),
				'table' => MAIN_DB_PREFIX . 'commande',
				'tabletrans' => $langs->trans('Commande Client'),
				'filecolumntitle' => 'Date de facturation (niveau final)',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'vin',
				'type' => 'text',
				'columntrans' => $langs->trans('VIN'),
				'table' => MAIN_DB_PREFIX . 'commande_extrafields',
				'tabletrans' => $langs->trans('commande extrafields'),
				'filecolumntitle' => 'VIN',
				'editable' => 0,
				'noinsert' => 1,
				'isvin' =>1
		);

	}

	/**
	 *
	 * @return number
	 */
	public function loadData() {
		$error = 0;

		dol_syslog(get_class($this) . '::' . __METHOD__, LOG_DEBUG);

		// Build header column array
		try {
			$rowIterator = $this->objWorksheet->getRowIterator($this->objWorksheet->getCell($this->startcell)->getRow())->current();

			$cellIterator = $rowIterator->getCellIterator($this->objWorksheet->getCell($this->startcell)->getColumn());
			$cellIterator->setIterateOnlyExistingCells(false);

			foreach ( $cellIterator as $cell ) {
				$cellValue = trim($cell->getCalculatedValue());
				if (empty($cellValue)) {
					$this->maxcol = $prevcol;
					break;
				}
				$this->columnArray[$cell->getColumn()] = array(
						'name' => dol_trunc($this->volvo_string_nospecial($cellValue), 64, 'right', 'UTF-8', 1),
						'label' => $cellValue
				);
				dol_syslog(get_class($this) . '::' . __METHOD__ . ' Build columnArray=' . var_export($this->columnArray, true), LOG_DEBUG);
				$prevcol = $cell->getColumn();
			}
		} catch ( Exception $e ) {
			$this->errors[] = $e->getMessage();
			$error ++;
		}

		$this->db->begin();
		if (empty($error)) {

			// Delete old temp table
			$sql = 'DROP TABLE IF EXISTS ' . $this->tempTable;
			dol_syslog(get_class($this) . '::' . __METHOD__ . ' Delete old temp table', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			}
		}
		// Build sql temp table
		if (empty($error)) {

			$sql = 'CREATE TABLE ' . $this->tempTable;
			$sql .= '(';
			$sql .= 'rowid integer NOT NULL auto_increment PRIMARY KEY,';
			$sql .= 'fourn_cmd_id integer DEFAULT NULL,';
			$sql .= 'cust_cmd_id integer DEFAULT NULL,';
			$sql .= 'integration_status integer DEFAULT NULL,';
			$sql .= 'integration_action varchar(20) DEFAULT NULL,';
			$sql .= 'integration_comment text DEFAULT NULL,';
			foreach ( $this->columnArray as $data ) {
				$sql .= $data['name'] . ' text,';
			}
			$sql .= 'tms timestamp NOT NULL';
			$sql .= ')ENGINE=InnoDB;';

			dol_syslog(get_class($this) . '::' . __METHOD__ . ' Build sql temp table', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			}
		}

		// Build Data array
		if (empty($error)) {
			try {

				$rowIterator = $this->objWorksheet->getRowIterator($this->objWorksheet->getCell($this->startcell)->getRow() + 1);
				foreach ( $rowIterator as $row ) {
					$cellIterator = $row->getCellIterator($this->objWorksheet->getCell($this->startcell)->getColumn());
					$cellIterator->setIterateOnlyExistingCells(false);

					foreach ( $cellIterator as $cell ) {
						if (PHPExcel_Shared_Date::isDateTime($cell)) {
							$cellValue = $cell->getValue();
							$dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);
							$cellValue = date('Ymd', $dateValue);
						} else {
							$cellValue = trim($cell->getCalculatedValue());
						}
						$this->columnData[$cell->getRow()][$cell->getColumn()] = array(
								'sqlvalue' => ($cellValue == '' ? 'NULL' : '\'' . $this->db->escape($cellValue) . '\''),
								'data' => $cellValue
						);

						if ($cell->getColumn() == $this->maxcol) {
							break;
						}
					}

					// insert Data into temp table
					if (empty($error)) {

						$sqlInsert = array();

						$sql_insertheader = 'INSERT INTO ' . $this->tempTable;
						$sql_insertheader .= '(';
						foreach ( $this->columnArray as $data ) {
							$sql_insertheader .= $data['name'] . ',';
						}
						$sql_insertheader .= 'tms';
						$sql_insertheader .= ')';

						$i = 0;
						foreach ( $this->columnData as $rowindex => $datarow ) {
							$sql = $sql_insertheader . ' VALUES (';
							foreach ( $datarow as $colinex => $data ) {
								$sql .= $data['sqlvalue'] . ',';
							}
							$sql .= 'NOW())';

							$sqlInsert[] = $sql;
							$i ++;
						}

						foreach ( $sqlInsert as $sql ) {
							dol_syslog(get_class($this) . '::' . __METHOD__ . ' insert data into temp table', LOG_DEBUG);
							$resql = $this->db->query($sql);
							if (! $resql) {
								$this->errors[] = $this->db->lasterror;
								$error ++;
							}
						}
					}

					if (empty($error)) {
						$this->columnData = array();
					}
				}
			} catch ( Exception $e ) {
				$this->errors[] = $e->getMessage();
				$error ++;
			}
		}

		if (empty($error)) {
			$this->db->commit();
			return 1;
		} else {
			$this->db->rollback();
			return - 1 * $error;
		}
	}

	/**
	 *
	 * @param unknown $matchColmunArray
	 */
	public function checkData($matchColmunArray = array()) {
		global $langs;

		$error = 0;

		$sql = 'UPDATE ' . $this->tempTable . ' SET integration_status=NULL, integration_comment=\'\'';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' remove all comment', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		$sql = 'UPDATE ' . $this->tempTable . ' SET integration_action=NULL WHERE integration_action<>1';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' remove all comment', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		// Find vin column
		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('isvin', $data) && ! empty($data['isvin'])) {
				$columnTmpName = $matchColmunArray[$key];
				$colvin_tmptable = $columnTmpName;
				$colvin_desttable = $data['column'];
			}
		}

		// Find OM number column
		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('isnumom', $data) && ! empty($data['isnumom'])) {
				$columnTmpName = $matchColmunArray[$key];
				$colnumom_tmptable = $columnTmpName;
				$colnumom_desttable = $data['column'];
			}
		}


		//update customer order id
		$sql = 'UPDATE ' . $this->tempTable .' as tmp, ' . MAIN_DB_PREFIX . 'commande_extrafields as ef ';
		$sql.= 'SET tmp.cust_cmd_id = ef.fk_object ';
		$sql.= 'WHERE tmp.numero_de_commande = ef.numom';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update cust_cmd_id', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		// Add customer order not found integration comment
		$sql = 'SELECT rowid FROM ' . $this->tempTable;
		$sql .= ' WHERE cust_cmd_id IS NULL';

		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update dictionnary problem', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		} else {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$integration_comment = array(
						'column' => $colnumom_tmptable,
						'color' => 'red',
						'message' => 'Commande client non trouvée',
						'outputincell' => 1
				);
				$result = $this->addIntegrationComment($obj->rowid, $integration_comment, 3);
				if ($result < 0) {
					$error ++;
				}
			}
		}


		//update customer order id
		$sql = 'UPDATE ' . $this->tempTable .' as tmp, ' . MAIN_DB_PREFIX . 'commande_fournisseur_extrafields as ef, ' . MAIN_DB_PREFIX . 'commande_fournisseur as cmd ';
		$sql.= 'SET tmp.fourn_cmd_id = ef.fk_object ';
		$sql.= 'WHERE tmp.numero_de_commande = ef.numom AND ef.fk_object = cmd.rowid AND cmd.fk_soc = 32553';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update fourn_cmd_id', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}


		// Add supplier order not found integration comment
		$sql = 'SELECT rowid FROM ' . $this->tempTable;
		$sql .= ' WHERE fourn_cmd_id IS NULL';

		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update dictionnary problem', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		} else {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$integration_comment = array(
						'column' => $colnumom_tmptable,
						'color' => 'red',
						'message' => 'Commande fournisseur non trouvée',
						'outputincell' => 1
				);
				$result = $this->addIntegrationComment($obj->rowid, $integration_comment, 3);
				if ($result < 0) {
					$error ++;
				}
			}
		}

		$sql = 'UPDATE ' . $this->tempTable . ' SET integration_status=1';
		$sql .= ' WHERE fourn_cmd_id IS NOT NULL AND cust_cmd_id IS NOT NULL';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' ok for import', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		// check date structure
		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('type', $data) && $data['type'] == 'date') {
				$columnTmpName = $matchColmunArray[$key];
				$sql = ' SELECT rowid,' . $columnTmpName . ' as dateinfo FROM ' . $this->tempTable;
				$resql = $this->db->query($sql);
				if (! $resql) {
					$this->errors[] = $this->db->lasterror;
					$this->errors[] = $sql;
					$error ++;
				} else {
					while ( $obj = $this->db->fetch_object($resql) ) {
						if (! empty($obj->dateinfo)) {
							try {
								$day = substr($obj->dateinfo, 0,2);
								$month = substr($obj->dateinfo, 2,2);
								$year = substr($obj->dateinfo, 4,4);
								$datetime = new DateTime($year . '-' . $month . '-' . $day);
							} catch ( Exception $e ) {
								$integration_comment = array(
										'column' => $columnTmpName,
										'color' => 'red',
										'message' => $langs->trans('VolvoDateCannotBeConvert'),
										'outputincell' => 1
								);
								$result = $this->addIntegrationComment($obj->rowid, $integration_comment, 0);
								if ($result < 0) {
									$error ++;
								}
							}
						}
					}
				}
			}
		}




		// Update intégration status OK
		$sql = 'UPDATE ' . $this->tempTable . ' SET integration_status=1 WHERE integration_status IS NULL';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' set status to 1', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		// Set to NULL integration comment where the is no remark
		$sql = 'UPDATE ' . $this->tempTable . ' SET integration_comment=NULL WHERE integration_comment=\'\'';
		dol_syslog(get_class($this) . '::' . __METHOD__, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		if (empty($error)) {
			return 1;
		} else {
			return - 1 * $error;
		}
	}

	/**
	 *
	 * @param unknown $matchColmunArray
	 * @return number
	 */
	public function importData($matchColmunArray = array()) {
		global $langs, $conf,$user;
		dol_include_once('/volvo/class/commandevolvo.class.php');
		dol_include_once('/fourn/class/fournisseur.class.php');

		$error = 0;

		$now = dol_now();

		$this->columnArray[] = array(
				'name' => 'fourn_cmd_id',
				'type' => 'int'
		);
		$this->columnArray[] = array(
				'name' => 'cust_cmd_id',
				'type' => 'int'
		);

		$this->db->begin();
		$result = $this->fetchAllTempTable('', '', 0, 0, array(
				'integration_status' => 1
		));
		if ($result < 0) {
			$error ++;
		}
		// Insert New customer
		$cmd_data_array= array();
		$cmdfourn_data_array= array();
		foreach ( $this->lines as $line ) {
			$cmd_data_array[$line->rowid]['cust_cmd_id'] = $line->cust_cmd_id;
			$cmdfourn_data_array[$line->rowid]['fourn_cmd_id'] = $line->fourn_cmd_id;
			foreach ($this->targetInfoArray as $key => $col){
				if($col['column'] == 'numom'){
					$cmd_data_array[$line->rowid]['numom'] = $line->$matchColmunArray[$key];
					$cmdfourn_data_array[$line->rowid]['numom'] = $line->$matchColmunArray[$key];
				}
				if($col['column'] == 'vin'){
					$cmd_data_array[$line->rowid]['vin'] = $line->$matchColmunArray[$key];
					$cmdfourn_data_array[$line->rowid]['vin'] = $line->$matchColmunArray[$key];
				}
				if($col['column'] == 'dt_fact') $cmd_data_array[$line->rowid]['dt_fact'] = $line->$matchColmunArray[$key];
				if($col['column'] == 'date_livraison') $cmdfourn_data_array[$line->rowid]['date_livraison'] = $line->$matchColmunArray[$key];
				if($col['column'] == 'dt_blockupdate') $cmdfourn_data_array[$line->rowid]['dt_blockupdate'] = $line->$matchColmunArray[$key];
				if($col['column'] == 'dt_liv_maj') $cmdfourn_data_array[$line->rowid]['dt_liv_maj'] = $line->$matchColmunArray[$key];
				if($col['column'] == 'dt_lim_annul') $cmdfourn_data_array[$line->rowid]['dt_lim_annul'] = $line->$matchColmunArray[$key];
			}
		}


		foreach ($cmd_data_array as $key => $value){
			$cmd = new CommandeVolvo($this->db);
			$res = $cmd->fetch('326');
			if ($res < 0) {
				$error ++;
			}else{
				$cmd->array_options['options_numom'] = $value['numom'];
				$cmd->array_options['options_vin'] = $value['vin'];
				$cmd->insertExtraFields();
				if(!empty($value['date_facture'])){
					$day = substr($value['date_facture'], 0,2);
					$month = substr($value['date_facture'], 2,2);
					$year = substr($value['date_facture'], 4,4);
					$cmd->date_billed = dol_mktime(0, 0, 0, $month, $day, $year);
					$cmd->classifyBilled($user);
				}

			}
		}
		foreach ($cmdfourn_data_array as $key => $value){
			$cmd_fourn = new CommandeFournisseur($this->db);
			$res = $cmd_fourn->fetch($value['fourn_cmd_id']);
			if ($res < 0) {
				$error ++;
			}else{
				$cmd_fourn->array_options['options_numom'] = $value['numom'];
				$cmd_fourn->array_options['options_vin'] = $value['vin'];
				if(!empty($value['dt_blockupdate'])){
					$day = substr($value['dt_blockupdate'], 0,2);
					$month = substr($value['dt_blockupdate'], 2,2);
					$year = substr($value['dt_blockupdate'], 4,4);
					$cmd_fourn->array_options['options_dt_blockupdate'] = dol_mktime(0, 0, 0, $month, $day, $year);
				}
				if(!empty($value['dt_liv_maj'])){
					$day = substr($value['dt_liv_maj'], 0,2);
					$month = substr($value['dt_liv_maj'], 2,2);
					$year = substr($value['dt_liv_maj'], 4,4);
					$cmd_fourn->array_options['options_dt_liv_maj'] = dol_mktime(0, 0, 0, $month, $day, $year);
				}
				if(!empty($value['dt_lim_annul'])){
					$day = substr($value['dt_lim_annul'], 0,2);
					$month = substr($value['dt_lim_annul'], 2,2);
					$year = substr($value['dt_lim_annul'], 4,4);
					$cmd_fourn->array_options['options_dt_lim_annul'] = dol_mktime(0, 0, 0, $month, $day, $year);
				}
				$cmd_fourn->insertExtraFields();


				if(!empty($value['date_livraison'])){
					$day = substr($value['date_livraison'], 0,2);
					$month = substr($value['date_livraison'], 2,2);
					$year = substr($value['date_livraison'], 4,4);
					$date = dol_mktime(0, 0, 0, $month, $day, $year);
					$result=$cmd_fourn->set_date_livraison($user,dol_mktime(0, 0, 0, $month, $day, $year));
				}
			}
		}

		if (empty($error)) {
			$this->db->commit();
			return $now;
		} else {
			$this->db->rollback();
			return - 1 * $error;
		}
	}

	/**
	 *
	 * @param unknown $batch_number
	 * @param string $type
	 */
	public function getResultCnt($batch_number, $type = '') {
		if ($type == 'create' || $type == 'update') {
			$sql = 'SELECT count(rowid) as cnt FROM ' . MAIN_DB_PREFIX . 'immat WHERE import_key=\'' . $this->db->escape($batch_number) . '\'';
			dol_syslog(get_class($this) . '::' . __METHOD__ . '', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			} else {
				$obj = $this->db->fetch_object($resql);
				$num = $obj->cnt;
			}
		} elseif ($type == 'failed') {
			$sql = 'SELECT count(rowid) as cnt FROM ' . $this->tempTable . ' WHERE integration_status IN (0,4)';
			dol_syslog(get_class($this) . '::' . __METHOD__ . '', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			} else {
				$obj = $this->db->fetch_object($resql);
				$num = $obj->cnt;
			}
		}

		if (empty($error)) {
			return $num;
		} else {
			return - 1 * $error;
		}
	}

	/**
	 * Remove column from collection that is not used during import
	 *
	 * @param string $onlymatch
	 * @param unknown $matchColmunArray
	 */
	public function getCustomerColumnArray($forceDisplaycolumn = array(), $matchColmunArray = array(), $matchParam = '') {
		$this->columnArrayCustomer = array();
		// Build match column name
		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists($key, $matchColmunArray) && array_key_exists($matchParam, $data))
				$mathcolumnname[] = $matchColmunArray[$key];
		}
		foreach ( $this->columnArray as $key => $col ) {
			if (in_array($col['name'], $mathcolumnname) || in_array($col['name'], $forceDisplaycolumn)) {
				$this->columnArrayCustomer[$key] = $col;
			}
		}
	}
}