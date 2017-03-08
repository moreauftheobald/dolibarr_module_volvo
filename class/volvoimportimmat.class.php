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
class VolvoImportImmat extends VolvoImport
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
				'column' => 'date_dt',
				'type' => 'text',
				'columntrans' => $langs->trans('Date'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'DATE',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'genre',
				'type' => 'text',
				'columntrans' => $langs->trans('Genre'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Genre',
				'editable' => 0,
				'dict' => 'volvo_genre',
				'dictmatch' => 'labelexcel'
		);
		$this->targetInfoArray[] = array(
				'column' => 'marque',
				'type' => 'text',
				'columntrans' => $langs->trans('Marque'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Marque',
				'editable' => 0,
				'dict' => 'volvo_marques',
				'dictmatch' => 'labelexcel'
		);
		$this->targetInfoArray[] = array(
				'column' => 'type_veh',
				'type' => 'text',
				'columntrans' => $langs->trans('Type'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Type',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'energie',
				'type' => 'text',
				'columntrans' => $langs->trans('Energie'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Energie',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'carrosserie',
				'type' => 'text',
				'columntrans' => $langs->trans('Carrosserie'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Carrosserie',
				'editable' => 0,
				'dict' => 'volvo_carrosserie',
				'dictmatch' => 'labelexcel'
		);
		$this->targetInfoArray[] = array(
				'column' => 'const_dist',
				'type' => 'text',
				'columntrans' => $langs->trans('Const/Dist'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Const/Dist',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'ptr',
				'type' => 'text',
				'columntrans' => $langs->trans('PTR'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'PTR',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'gvw',
				'type' => 'text',
				'columntrans' => $langs->trans('GVW'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'GVW',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'charutpl',
				'type' => 'text',
				'columntrans' => $langs->trans('Charges utile - places assises'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Charges utile - places assises',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'puissfisc',
				'type' => 'text',
				'columntrans' => $langs->trans('Puissance fiscale'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Puissance fiscale',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'cd_dep',
				'type' => 'text',
				'columntrans' => $langs->trans('Code département'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Code département',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'cd_canton',
				'type' => 'text',
				'columntrans' => $langs->trans('Code canton'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Code canton',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'num_voie_address',
				'type' => 'text',
				'columntrans' => $langs->trans('Numéro de voie'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Numéro de voie',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'comp_address',
				'type' => 'text',
				'columntrans' => $langs->trans('Bis - Ter - Q...'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Bis - Ter - Q...',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'nat_voie_address',
				'type' => 'text',
				'columntrans' => $langs->trans('Nature de voie'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Nature de voie',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'nom_de_voie_address',
				'type' => 'text',
				'columntrans' => $langs->trans('Nom de voie'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Nom de voie',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'zip',
				'type' => 'text',
				'columntrans' => $langs->trans('Code postal'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Code postal',
				'editable' => 0,
				'noinsert' => 1,
				'iszip' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'town',
				'type' => 'text',
				'columntrans' => $langs->trans('Commune'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Commune',
				'editable' => 0,
				'noinsert' => 1,
				'istown' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'civility',
				'type' => 'text',
				'columntrans' => $langs->trans('Civilité'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Civilité',
				'editable' => 0,
				'noinsert' => 1,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'name_alias',
				'column_dest' => 'fk_soc',
				'type' => 'text',
				'type_dest' => 'int',
				'columntrans' => $langs->trans('Patronyme'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'fk_table' => MAIN_DB_PREFIX . 'societe',
				'tabletrans' => $langs->trans('Societe'),
				'filecolumntitle' => 'Patronyme',
				'editable' => 1,
				'ispatronyme' => 1,
				'ForCustomerStep' => 1,
				'tmpcolumnname' => 'thirdparty_id'
		);
		$this->targetInfoArray[] = array(
				'column' => 'status',
				'type' => 'text',
				'columntrans' => $langs->trans('ETAT'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'ETAT',
				'editable' => 0,
				'isstatus' => 0
		);

		$this->targetInfoArray[] = array(
				'column' => 'csp_prop',
				'type' => 'text',
				'columntrans' => $langs->trans('CSP du propriétaire'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'CSP du propriétaire',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'cd_ape',
				'type' => 'text',
				'columntrans' => $langs->trans('Code APE'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Code APE',
				'editable' => 0,
				'noinsert' => 1,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'siren',
				'type' => 'text',
				'columntrans' => $langs->trans('Numéro de SIREN'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Numéro de SIREN',
				'editable' => 0,
				'issiren' => 1,
				'noinsert' => 1,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'dt_carte_grise',
				'type' => 'date',
				'columntrans' => $langs->trans('Date de carte grise'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Date de carte grise',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'immat',
				'type' => 'text',
				'columntrans' => $langs->trans('Immatriculation'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Immatriculation',
				'editable' => 0,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'vin',
				'type' => 'text',
				'columntrans' => $langs->trans('Codif VIN PRF'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Codif VIN PRF',
				'editable' => 0,
				'isunique' => 1,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'num_serie',
				'type' => 'text',
				'columntrans' => $langs->trans('Numéro de série'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Numéro de série',
				'editable' => 0,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'modele',
				'type' => 'text',
				'columntrans' => $langs->trans('Modèle'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Modèle',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'date_arr',
				'type' => 'text',
				'columntrans' => $langs->trans('DATE_ARRETE'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'DATE_ARRETE',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'volume',
				'type' => 'text',
				'columntrans' => $langs->trans('VOLUME'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'VOLUME',
				'editable' => 0
		);
		$this->targetInfoArray[] = array(
				'column' => 'jo',
				'type' => 'text',
				'columntrans' => $langs->trans('JO'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'JO',
				'editable' => 0,
				'noinsert' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'county',
				'type' => 'text',
				'columntrans' => $langs->trans('County'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'County',
				'editable' => 0,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'dealer',
				'type' => 'text',
				'columntrans' => $langs->trans('Dealer'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'Dealer',
				'editable' => 0,
				'noinsert' => 1,
				'ForCustomerStep' => 1
		);
		$this->targetInfoArray[] = array(
				'column' => 'fk_user',
				'type' => 'text',
				'type_dest' => 'int',
				'columntrans' => $langs->trans('vendeur'),
				'table' => MAIN_DB_PREFIX . 'immat',
				'tabletrans' => $langs->trans('Immat'),
				'filecolumntitle' => 'vendeur',
				'editable' => 1,
				'isvendeur' => 1,
				'ForCustomerStep' => 1,
				'fk_table' => MAIN_DB_PREFIX . 'user',
				'tmpcolumnname' => 'vendeur_id'
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
			$sql .= 'thirdparty_id integer DEFAULT NULL,';
			$sql .= 'vendeur_id integer DEFAULT NULL,';
			$sql .= 'integration_status integer DEFAULT NULL,';
			$sql .= 'integration_action varchar(20) DEFAULT NULL,';
			$sql .= 'integration_comment text DEFAULT NULL,';
			$sql .= 'realaddress text DEFAULT NULL,';
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

		// Find Contract column
		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('isunique', $data) && ! empty($data['isunique'])) {
				$columnTmpName = $matchColmunArray[$key];
				$colvin_tmptable = $columnTmpName;
				$colvin_desttable = $data['column'];
			}
		}

		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('issiren', $data) && ! empty($data['issiren'])) {
				$columnTmpName = $matchColmunArray[$key];
				$colsiren_tmptable = $columnTmpName;
				$colsiren_desttable = $data['column'];
			}
		}

		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('ispatronyme', $data) && ! empty($data['ispatronyme'])) {
				$columnTmpName = $matchColmunArray[$key];
				$colpatronyme_tmptable = $columnTmpName;
				$colpatronyme_desttable = $data['column'];
			}
		}

		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('isvendeur', $data) && ! empty($data['isvendeur'])) {
				$columnTmpName = $matchColmunArray[$key];
				$colvendeur_tmptable = $columnTmpName;
				$colvendeur_desttable = $data['column'];
			}
		}

		// Delete vin already imported
		// Must be done one by one because MySQL work hard with DELETE IN
		$sql = 'SELECT DISTINCT immat.' . $colvin_desttable . ' as vin  FROM ' . MAIN_DB_PREFIX . 'immat as immat ';
		$sql .= ' INNER JOIN ' . $this->tempTable . ' as tmptable ON tmptable.' . $colvin_tmptable . '=immat.' . $colvin_desttable;
		$sql .= ' ORDER BY immat.' . $colvin_desttable;
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		} else {
			while ( ($obj_del = $this->db->fetch_object($resql)) && empty($error) ) {
				$sql = 'DELETE FROM ' . $this->tempTable . ' WHERE ' . $colvin_tmptable . '=\'' . $this->db->escape($obj_del->vin) . '\'';
				dol_syslog(get_class($this) . '::' . __METHOD__ . ' remove bin already imported', LOG_DEBUG);
				$resql_inner = $this->db->query($sql);
				if (! $resql_inner) {
					$this->errors[] = $this->db->lasterror;
					$error ++;
				}
			}
		}

		// Find adress Fields
		$coladdress_tmptable = array();
		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('column', $data) && strpos($data['column'], 'address') !== false) {
				$columnTmpName = $matchColmunArray[$key];
				if (! empty($columnTmpName)) {
					$coladdress_tmptable[$columnTmpName] = 'IFNULL(' . $columnTmpName . ',\'\')';
				}
				// $colnumcdb_desttable = $data['column'];
			}
		}
		if (count($coladdress_tmptable > 0)) {
			// update adresse realfields column
			$sql = 'UPDATE ' . $this->tempTable . '  SET realaddress=TRIM(CONCAT_WS("\n",' . implode(',', $coladdress_tmptable) . '))';
			dol_syslog(get_class($this) . '::' . __METHOD__ . ' update realadress', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			}
		}
		if (count($coladdress_tmptable > 0)) {
			// update adresse realfields column
			$sql = 'UPDATE ' . $this->tempTable . '  SET realaddress=REPLACE(realaddress, "\n\n", "\n")';
			dol_syslog(get_class($this) . '::' . __METHOD__ . ' update realadress', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			}
		}
		if (count($coladdress_tmptable > 0)) {
			// update adresse realfields column
			$sql = 'UPDATE ' . $this->tempTable . '  SET realaddress=REPLACE(realaddress, "\n\n", "\n")';
			dol_syslog(get_class($this) . '::' . __METHOD__ . ' update realadress', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			}
		}

		// update thirdparty_id column with siren
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . 'societe as src,' . $this->tempTable . ' as dest SET dest.thirdparty_id=src.rowid ';
		$sql .= ' WHERE src.siren=dest.' . $colsiren_tmptable . ' AND dest.thirdparty_id IS NULL';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update thirdparty_id', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		// update thirdparty_id column with alias name
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . 'societe as src,' . $this->tempTable . ' as dest SET dest.thirdparty_id=src.rowid ';
		$sql .= ' WHERE src.' . $colpatronyme_desttable . '=dest.' . $colpatronyme_tmptable . ' AND dest.thirdparty_id IS NULL';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update thirdparty_id', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		// Add thirdparty not found integration comment
		$sql = 'SELECT rowid FROM ' . $this->tempTable;
		$sql .= ' WHERE thirdparty_id IS NULL';

		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update dictionnary problem', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		} else {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$integration_comment = array(
						'column' => $colpatronyme_tmptable,
						'color' => 'red',
						'message' => $langs->trans('VolvoCustomerNotFound'),
						'outputincell' => 1
				);
				$result = $this->addIntegrationComment($obj->rowid, $integration_comment, 3);
				if ($result < 0) {
					$error ++;
				}
			}
		}

		// update vendeur column
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . 'user as src,' . $this->tempTable . ' as dest SET dest.vendeur_id=src.rowid ';
		$sql .= ' WHERE CONCAT_WS(\' \',UPPER(src.firstname), UPPER(src.lastname))=dest.' . $colvendeur_tmptable . ' AND dest.vendeur_id IS NULL';
		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update thirdparty_id', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		}

		// Add vendeur not found integration comment
		$sql = 'SELECT rowid FROM ' . $this->tempTable;
		$sql .= ' WHERE vendeur_id IS NULL';

		dol_syslog(get_class($this) . '::' . __METHOD__ . ' update dictionnary problem', LOG_DEBUG);
		$resql = $this->db->query($sql);
		if (! $resql) {
			$this->errors[] = $this->db->lasterror;
			$error ++;
		} else {
			while ( $obj = $this->db->fetch_object($resql) ) {
				$integration_comment = array(
						'column' => $colvendeur_tmptable,
						'color' => 'red',
						'message' => $langs->trans('VolvoVendeurNotFound'),
						'outputincell' => 1
				);
				$result = $this->addIntegrationComment($obj->rowid, $integration_comment, 3);
				if ($result < 0) {
					$error ++;
				}
			}
		}

		$sql = 'UPDATE ' . $this->tempTable . ' SET integration_status=1';
		$sql .= ' WHERE thirdparty_id IS NOT NULL AND vendeur_id IS NOT NULL';
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
					$error ++;
				} else {
					while ( $obj = $this->db->fetch_object($resql) ) {
						if (! empty($obj->dateinfo)) {
							try {
								$datetime = new DateTime($obj->dateinfo);
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

		// Check if dictionnay values
		foreach ( $this->targetInfoArray as $key => $data ) {
			if (array_key_exists('dict', $data)) {
				$columnTmpName = $matchColmunArray[$key];

				$sql = 'SELECT DISTINCT ' . $data['dictmatch'] . ' as dictmatch FROM ' . MAIN_DB_PREFIX . 'c_' . $data['dict'] . ' WHERE ' . $data['dictmatch'] . ' IS NOT NULL';
				dol_syslog(get_class($this) . '::' . __METHOD__ . ' update dictionnary problem', LOG_DEBUG);
				$resql = $this->db->query($sql);
				if (! $resql) {
					$this->errors[] = $this->db->lasterror;
					$error ++;
				}
				$val_array = array();
				while ( $obj = $this->db->fetch_object($resql) ) {
					$cleandata = explode(',', str_replace("\n", '', $obj->dictmatch));
					$val_array = array_merge($val_array, $cleandata);
				}

				$sql = 'SELECT rowid, ' . $columnTmpName . ' as datatest FROM ' . $this->tempTable;
				$sql .= ' WHERE ' . $columnTmpName . ' NOT IN ';
				$sql .= '(\'' . implode('\',\'', $val_array) . '\')';

				dol_syslog(get_class($this) . '::' . __METHOD__ . ' update dictionnary problem', LOG_DEBUG);
				$resql = $this->db->query($sql);
				if (! $resql) {
					$this->errors[] = $this->db->lasterror;
					$error ++;
				} else {
					while ( $obj = $this->db->fetch_object($resql) ) {
						$integration_comment = array(
								'column' => $columnTmpName,
								'color' => 'red',
								'message' => $langs->trans('VolvoCkImpNoFound', $data['columntrans']),
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

		// Remove from column Array unit 1 and unit 2 to force unitcode
		$this->columnArray[] = array(
				'name' => 'realaddress',
				'label' => $langs->trans('Address')
		);
		foreach ( $this->columnArray as $key => $value ) {
			if (array_key_exists($value['name'], $coladdress_tmptable)) {
				unset($this->columnArray[$key]);
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
		global $langs, $conf;

		$error = 0;

		$now = dol_now();

		$this->columnArray[] = array(
				'name' => 'thirdparty_id',
				'type' => 'int'
		);
		$this->columnArray[] = array(
				'name' => 'vendeur_id',
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
		foreach ( $this->lines as $line ) {
			$sqlcol = array();
			$sqlvalue = array();
			foreach ( $this->targetInfoArray as $key => $col ) {
				if ($col['table'] == MAIN_DB_PREFIX . 'immat') {
					if (! array_key_exists('noinsert', $col)) {

						$columnTmpName = $matchColmunArray[$key];
						if (array_key_exists('tmpcolumnname', $col) && ! empty($col['tmpcolumnname'])) {
							$columnTmpName = $col['tmpcolumnname'];
						}

						if (! empty($columnTmpName)) {

							if (array_key_exists('column_dest', $col)) {
								$sqlcol[] = $col['column_dest'];
							} else {
								$sqlcol[] = $col['column'];
							}

							if (array_key_exists('type_dest', $col) && ! empty($col['type_dest'])) {
								$typecol = $col['type_dest'];
							} else {
								$typecol = $col['type'];
							}

							if (array_key_exists('dict', $col)) {
								// Value from dict
								$sql_dict = 'SELECT rowid FROM ' . MAIN_DB_PREFIX . 'c_' . $col['dict'] . ' WHERE';

								$sql_dict .= ' ' . $col['dictmatch'] . ' LIKE ' . "'%" . $this->db->escape($line->$columnTmpName) . "%'";

								dol_syslog(get_class($this) . '::' . __METHOD__ . ' find dict value', LOG_DEBUG);
								$resql_dict = $this->db->query($sql_dict);
								if (! $resql_dict) {
									$this->errors[] = $this->db->lasterror;
									$error ++;
								} else {
									$obj_dict = $this->db->fetch_object($resql_dict);
									$sqlvalue[] = $this->formatSqlType($typecol, $obj_dict->rowid);
								}
							} else {
								// Simple value

								$valtoinsert = $this->formatSqlType($typecol, $line->$columnTmpName);
								/*dol_syslog('$col[column]=' . var_export($col['column'], true));
								 dol_syslog('$typecol=' . var_export($typecol, true));
								 dol_syslog('$columnTmpName=' . var_export($columnTmpName, true));
								 dol_syslog('$line->$columnTmpName=' . var_export($line->$columnTmpName, true));
								 dol_syslog('$valtoinsert=' . var_export($valtoinsert, true));*/
								$sqlvalue[] = $valtoinsert;
							}
						}
					}
				}
			}

			$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . 'immat(' . implode(',', $sqlcol) . ',import_key) VALUES (' . implode(',', $sqlvalue) . ',\'' . $now . '\')';
			dol_syslog(get_class($this) . '::' . __METHOD__, LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			}

			// Update/insert salesman
			$sql = 'SELECT DISTINCT fk_soc,fk_user FROM ' . MAIN_DB_PREFIX . 'immat';
			dol_syslog(get_class($this) . '::' . __METHOD__, LOG_DEBUG);
			dol_syslog(get_class($this) . '::' . __METHOD__ . ' update dictionnary problem', LOG_DEBUG);
			$resql = $this->db->query($sql);
			if (! $resql) {
				$this->errors[] = $this->db->lasterror;
				$error ++;
			} else {
				while ( $obj = $this->db->fetch_object($resql) ) {
					$sql_insert = 'INSERT INTO ' . MAIN_DB_PREFIX . 'societe_commerciaux(fk_soc,fk_user,import_key)';
					$sql_insert .= ' VALUES (' . $obj->fk_soc . ',' . $obj->fk_user . ',\'' . $now . '\')';
					$resql_insert = $this->db->query($sql_insert);
					if (! $resql_insert) {
						$this->errors[] = $this->db->lasterror;
						//Do not throw error because only case of error should be uk index already exists
						// $error ++;
					}
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