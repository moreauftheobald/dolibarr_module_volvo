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

/**
 * \file    immat/immat.class.php
 * \ingroup immat
 * \brief   This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *          Put some comments here
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
//require_once DOL_DOCUMENT_ROOT . '/societe/class/societe.class.php';
//require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

/**
 * Class Immat
 *
 * Put here description of your class
 * @see CommonObject
 */
class Immat extends CommonObject
{
	/**
	 * @var string Id to identify managed objects
	 */
	public $element = 'immat';
	/**
	 * @var string Name of table without prefix where object is stored
	 */
	public $table_element = 'immat';

	/**
	 * @var ImmatLine[] Lines
	 */
	public $lines = array();

	/**
	 */
	
	public $genre;
	public $marque;
	public $type_veh;
	public $energie;
	public $carrosserie;
	public $const_dist;
	public $ptr;
	public $gvw;
	public $charutpl;
	public $puissfisc;
	public $fk_soc;
	public $status;
	public $csp_prop;
	public $dt_carte_grise = '';
	public $immat;
	public $vin;
	public $num_serie;
	public $modele;
	public $volume;
	public $county;
	public $fk_user;
	public $fk_lead;
	public $fk_order;
	public $import_key;
	public $tms = '';

	/**
	 */
	

	/**
	 * Constructor
	 *
	 * @param DoliDb $db Database handler
	 */
	public function __construct(DoliDB $db)
	{
		$this->db = $db;
	}

	/**
	 * Create object into database
	 *
	 * @param  User $user      User that creates
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, Id of created object if OK
	 */
	public function create(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		// Clean parameters
		
		if (isset($this->genre)) {
			 $this->genre = trim($this->genre);
		}
		if (isset($this->marque)) {
			 $this->marque = trim($this->marque);
		}
		if (isset($this->type_veh)) {
			 $this->type_veh = trim($this->type_veh);
		}
		if (isset($this->energie)) {
			 $this->energie = trim($this->energie);
		}
		if (isset($this->carrosserie)) {
			 $this->carrosserie = trim($this->carrosserie);
		}
		if (isset($this->const_dist)) {
			 $this->const_dist = trim($this->const_dist);
		}
		if (isset($this->ptr)) {
			 $this->ptr = trim($this->ptr);
		}
		if (isset($this->gvw)) {
			 $this->gvw = trim($this->gvw);
		}
		if (isset($this->charutpl)) {
			 $this->charutpl = trim($this->charutpl);
		}
		if (isset($this->puissfisc)) {
			 $this->puissfisc = trim($this->puissfisc);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}
		if (isset($this->csp_prop)) {
			 $this->csp_prop = trim($this->csp_prop);
		}
		if (isset($this->immat)) {
			 $this->immat = trim($this->immat);
		}
		if (isset($this->vin)) {
			 $this->vin = trim($this->vin);
		}
		if (isset($this->num_serie)) {
			 $this->num_serie = trim($this->num_serie);
		}
		if (isset($this->modele)) {
			 $this->modele = trim($this->modele);
		}
		if (isset($this->volume)) {
			 $this->volume = trim($this->volume);
		}
		if (isset($this->county)) {
			 $this->county = trim($this->county);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_lead)) {
			 $this->fk_lead = trim($this->fk_lead);
		}
		if (isset($this->fk_order)) {
			 $this->fk_order = trim($this->fk_order);
		}
		if (isset($this->import_key)) {
			 $this->import_key = trim($this->import_key);
		}

		

		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = 'INSERT INTO ' . MAIN_DB_PREFIX . $this->table_element . '(';
		
		$sql.= 'genre,';
		$sql.= 'marque,';
		$sql.= 'type_veh,';
		$sql.= 'energie,';
		$sql.= 'carrosserie,';
		$sql.= 'const_dist,';
		$sql.= 'ptr,';
		$sql.= 'gvw,';
		$sql.= 'charutpl,';
		$sql.= 'puissfisc,';
		$sql.= 'fk_soc,';
		$sql.= 'status,';
		$sql.= 'csp_prop,';
		$sql.= 'dt_carte_grise,';
		$sql.= 'immat,';
		$sql.= 'vin,';
		$sql.= 'num_serie,';
		$sql.= 'modele,';
		$sql.= 'volume,';
		$sql.= 'county,';
		$sql.= 'fk_user,';
		$sql.= 'fk_lead,';
		$sql.= 'fk_order';
		$sql.= 'import_key';

		
		$sql .= ') VALUES (';
		
		$sql .= ' '.(! isset($this->genre)?'NULL':$this->genre).',';
		$sql .= ' '.(! isset($this->marque)?'NULL':$this->marque).',';
		$sql .= ' '.(! isset($this->type_veh)?'NULL':"'".$this->db->escape($this->type_veh)."'").',';
		$sql .= ' '.(! isset($this->energie)?'NULL':"'".$this->db->escape($this->energie)."'").',';
		$sql .= ' '.(! isset($this->carrosserie)?'NULL':$this->carrosserie).',';
		$sql .= ' '.(! isset($this->const_dist)?'NULL':"'".$this->db->escape($this->const_dist)."'").',';
		$sql .= ' '.(! isset($this->ptr)?'NULL':$this->ptr).',';
		$sql .= ' '.(! isset($this->gvw)?'NULL':"'".$this->db->escape($this->gvw)."'").',';
		$sql .= ' '.(! isset($this->charutpl)?'NULL':$this->charutpl).',';
		$sql .= ' '.(! isset($this->puissfisc)?'NULL':$this->puissfisc).',';
		$sql .= ' '.(! isset($this->fk_soc)?'NULL':$this->fk_soc).',';
		$sql .= ' '.(! isset($this->status)?'NULL':"'".$this->db->escape($this->status)."'").',';
		$sql .= ' '.(! isset($this->csp_prop)?'NULL':"'".$this->db->escape($this->csp_prop)."'").',';
		$sql .= ' '.(! isset($this->dt_carte_grise) || dol_strlen($this->dt_carte_grise)==0?'NULL':"'".$this->db->idate($this->dt_carte_grise)."'").',';
		$sql .= ' '.(! isset($this->immat)?'NULL':"'".$this->db->escape($this->immat)."'").',';
		$sql .= ' '.(! isset($this->vin)?'NULL':"'".$this->db->escape($this->vin)."'").',';
		$sql .= ' '.(! isset($this->num_serie)?'NULL':"'".$this->db->escape($this->num_serie)."'").',';
		$sql .= ' '.(! isset($this->modele)?'NULL':"'".$this->db->escape($this->modele)."'").',';
		$sql .= ' '.(! isset($this->volume)?'NULL':"'".$this->db->escape($this->volume)."'").',';
		$sql .= ' '.(! isset($this->county)?'NULL':"'".$this->db->escape($this->county)."'").',';
		$sql .= ' '.(! isset($this->fk_user)?'NULL':$this->fk_user).',';
		$sql .= ' '.(! isset($this->fk_lead)?'NULL':$this->fk_lead).',';
		$sql .= ' '.(! isset($this->fk_order)?'NULL':$this->fk_order).',';
		$sql .= ' '.(! isset($this->import_key)?'NULL':"'".$this->db->escape($this->import_key)."'");

		
		$sql .= ')';

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error) {
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX . $this->table_element);

			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action to call a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_CREATE',$user);
				//if ($result < 0) $error++;
				//// End call triggers
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return $this->id;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param int    $id  Id object
	 * @param string $ref Ref
	 *
	 * @return int <0 if KO, 0 if not found, >0 if OK
	 */
	public function fetch($id, $ref = null)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.genre,";
		$sql .= " t.marque,";
		$sql .= " t.type_veh,";
		$sql .= " t.energie,";
		$sql .= " t.carrosserie,";
		$sql .= " t.const_dist,";
		$sql .= " t.ptr,";
		$sql .= " t.gvw,";
		$sql .= " t.charutpl,";
		$sql .= " t.puissfisc,";
		$sql .= " t.fk_soc,";
		$sql .= " t.status,";
		$sql .= " t.csp_prop,";
		$sql .= " t.dt_carte_grise,";
		$sql .= " t.immat,";
		$sql .= " t.vin,";
		$sql .= " t.num_serie,";
		$sql .= " t.modele,";
		$sql .= " t.volume,";
		$sql .= " t.county,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_lead,";
		$sql .= " t.fk_order,";
		$sql .= " t.import_key,";
		$sql .= " t.tms";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element . ' as t';
		if (null !== $ref) {
			$sql .= ' WHERE t.ref = ' . '\'' . $ref . '\'';
		} else {
			$sql .= ' WHERE t.rowid = ' . $id;
		}

		$resql = $this->db->query($sql);
		if ($resql) {
			$numrows = $this->db->num_rows($resql);
			if ($numrows) {
				$obj = $this->db->fetch_object($resql);

				$this->id = $obj->rowid;
				
				$this->genre = $obj->genre;
				$this->marque = $obj->marque;
				$this->type_veh = $obj->type_veh;
				$this->energie = $obj->energie;
				$this->carrosserie = $obj->carrosserie;
				$this->const_dist = $obj->const_dist;
				$this->ptr = $obj->ptr;
				$this->gvw = $obj->gvw;
				$this->charutpl = $obj->charutpl;
				$this->puissfisc = $obj->puissfisc;
				$this->fk_soc = $obj->fk_soc;
				$this->status = $obj->status;
				$this->csp_prop = $obj->csp_prop;
				$this->dt_carte_grise = $this->db->jdate($obj->dt_carte_grise);
				$this->immat = $obj->immat;
				$this->vin = $obj->vin;
				$this->num_serie = $obj->num_serie;
				$this->modele = $obj->modele;
				$this->volume = $obj->volume;
				$this->county = $obj->county;
				$this->fk_user = $obj->fk_user;
				$this->fk_lead = $obj->fk_lead;
				$this->fk_order = $obj->fk_order;
				$this->import_key = $obj->import_key;
				$this->tms = $this->db->jdate($obj->tms);

				
			}
			$this->db->free($resql);

			if ($numrows) {
				return 1;
			} else {
				return 0;
			}
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Load object in memory from the database
	 *
	 * @param string $sortorder Sort Order
	 * @param string $sortfield Sort field
	 * @param int    $limit     offset limit
	 * @param int    $offset    offset limit
	 * @param array  $filter    filter array
	 * @param string $filtermode filter mode (AND or OR)
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function fetchAll($sortorder='', $sortfield='', $limit=0, $offset=0, array $filter = array(), $filtermode='AND')
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$sql = 'SELECT';
		$sql .= ' t.rowid,';
		
		$sql .= " t.genre,";
		$sql .= " t.marque,";
		$sql .= " t.type_veh,";
		$sql .= " t.energie,";
		$sql .= " t.carrosserie,";
		$sql .= " t.const_dist,";
		$sql .= " t.ptr,";
		$sql .= " t.gvw,";
		$sql .= " t.charutpl,";
		$sql .= " t.puissfisc,";
		$sql .= " t.fk_soc,";
		$sql .= " t.status,";
		$sql .= " t.csp_prop,";
		$sql .= " t.dt_carte_grise,";
		$sql .= " t.immat,";
		$sql .= " t.vin,";
		$sql .= " t.num_serie,";
		$sql .= " t.modele,";
		$sql .= " t.volume,";
		$sql .= " t.county,";
		$sql .= " t.fk_user,";
		$sql .= " t.fk_lead,";
		$sql .= " t.fk_order,";
		$sql .= " t.import_key,";
		$sql .= " t.tms";

		
		$sql .= ' FROM ' . MAIN_DB_PREFIX . $this->table_element. ' as t';

		// Manage filter
		$sqlwhere = array();
		if (count($filter) > 0) {
			foreach ($filter as $key => $value) {
				$sqlwhere [] = $key . ' LIKE \'%' . $this->db->escape($value) . '%\'';
			}
		}
		if (count($sqlwhere) > 0) {
			$sql .= ' WHERE ' . implode(' '.$filtermode.' ', $sqlwhere);
		}
		
		if (!empty($sortfield)) {
			$sql .= $this->db->order($sortfield,$sortorder);
		}
		if (!empty($limit)) {
		 $sql .=  ' ' . $this->db->plimit($limit + 1, $offset);
		}
		$this->lines = array();

		$resql = $this->db->query($sql);
		if ($resql) {
			$num = $this->db->num_rows($resql);

			while ($obj = $this->db->fetch_object($resql)) {
				$line = new ImmatLine();

				$line->id = $obj->rowid;
				
				$line->genre = $obj->genre;
				$line->marque = $obj->marque;
				$line->type_veh = $obj->type_veh;
				$line->energie = $obj->energie;
				$line->carrosserie = $obj->carrosserie;
				$line->const_dist = $obj->const_dist;
				$line->ptr = $obj->ptr;
				$line->gvw = $obj->gvw;
				$line->charutpl = $obj->charutpl;
				$line->puissfisc = $obj->puissfisc;
				$line->fk_soc = $obj->fk_soc;
				$line->status = $obj->status;
				$line->csp_prop = $obj->csp_prop;
				$line->dt_carte_grise = $this->db->jdate($obj->dt_carte_grise);
				$line->immat = $obj->immat;
				$line->vin = $obj->vin;
				$line->num_serie = $obj->num_serie;
				$line->modele = $obj->modele;
				$line->volume = $obj->volume;
				$line->county = $obj->county;
				$line->fk_user = $obj->fk_user;
				$line->fk_lead = $obj->fk_lead;
				$line->fk_order = $obj->fk_order;
				$line->import_key = $obj->import_key;
				$line->tms = $this->db->jdate($obj->tms);

				

				$this->lines[$line->id] = $line;
			}
			$this->db->free($resql);

			return $num;
		} else {
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);

			return - 1;
		}
	}

	/**
	 * Update object into database
	 *
	 * @param  User $user      User that modifies
	 * @param  bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function update(User $user, $notrigger = false)
	{
		$error = 0;

		dol_syslog(__METHOD__, LOG_DEBUG);

		// Clean parameters
		
		if (isset($this->genre)) {
			 $this->genre = trim($this->genre);
		}
		if (isset($this->marque)) {
			 $this->marque = trim($this->marque);
		}
		if (isset($this->type_veh)) {
			 $this->type_veh = trim($this->type_veh);
		}
		if (isset($this->energie)) {
			 $this->energie = trim($this->energie);
		}
		if (isset($this->carrosserie)) {
			 $this->carrosserie = trim($this->carrosserie);
		}
		if (isset($this->const_dist)) {
			 $this->const_dist = trim($this->const_dist);
		}
		if (isset($this->ptr)) {
			 $this->ptr = trim($this->ptr);
		}
		if (isset($this->gvw)) {
			 $this->gvw = trim($this->gvw);
		}
		if (isset($this->charutpl)) {
			 $this->charutpl = trim($this->charutpl);
		}
		if (isset($this->puissfisc)) {
			 $this->puissfisc = trim($this->puissfisc);
		}
		if (isset($this->fk_soc)) {
			 $this->fk_soc = trim($this->fk_soc);
		}
		if (isset($this->status)) {
			 $this->status = trim($this->status);
		}
		if (isset($this->csp_prop)) {
			 $this->csp_prop = trim($this->csp_prop);
		}
		if (isset($this->immat)) {
			 $this->immat = trim($this->immat);
		}
		if (isset($this->vin)) {
			 $this->vin = trim($this->vin);
		}
		if (isset($this->num_serie)) {
			 $this->num_serie = trim($this->num_serie);
		}
		if (isset($this->modele)) {
			 $this->modele = trim($this->modele);
		}
		if (isset($this->volume)) {
			 $this->volume = trim($this->volume);
		}
		if (isset($this->county)) {
			 $this->county = trim($this->county);
		}
		if (isset($this->fk_user)) {
			 $this->fk_user = trim($this->fk_user);
		}
		if (isset($this->fk_lead)) {
			 $this->fk_lead = trim($this->fk_lead);
		}
		if (isset($this->fk_order)) {
			 $this->fk_order = trim($this->fk_order);
		}
		if (isset($this->import_key)) {
			 $this->import_key = trim($this->import_key);
		}

		

		// Check parameters
		// Put here code to add a control on parameters values

		// Update request
		$sql = 'UPDATE ' . MAIN_DB_PREFIX . $this->table_element . ' SET';
		
		$sql .= ' genre = '.(isset($this->genre)?$this->genre:"null").',';
		$sql .= ' marque = '.(isset($this->marque)?$this->marque:"null").',';
		$sql .= ' type_veh = '.(isset($this->type_veh)?"'".$this->db->escape($this->type_veh)."'":"null").',';
		$sql .= ' energie = '.(isset($this->energie)?"'".$this->db->escape($this->energie)."'":"null").',';
		$sql .= ' carrosserie = '.(isset($this->carrosserie)?$this->carrosserie:"null").',';
		$sql .= ' const_dist = '.(isset($this->const_dist)?"'".$this->db->escape($this->const_dist)."'":"null").',';
		$sql .= ' ptr = '.(isset($this->ptr)?$this->ptr:"null").',';
		$sql .= ' gvw = '.(isset($this->gvw)?"'".$this->db->escape($this->gvw)."'":"null").',';
		$sql .= ' charutpl = '.(isset($this->charutpl)?$this->charutpl:"null").',';
		$sql .= ' puissfisc = '.(isset($this->puissfisc)?$this->puissfisc:"null").',';
		$sql .= ' fk_soc = '.(isset($this->fk_soc)?$this->fk_soc:"null").',';
		$sql .= ' status = '.(isset($this->status)?"'".$this->db->escape($this->status)."'":"null").',';
		$sql .= ' csp_prop = '.(isset($this->csp_prop)?"'".$this->db->escape($this->csp_prop)."'":"null").',';
		$sql .= ' dt_carte_grise = '.(! isset($this->dt_carte_grise) || dol_strlen($this->dt_carte_grise) != 0 ? "'".$this->db->idate($this->dt_carte_grise)."'" : 'null').',';
		$sql .= ' immat = '.(isset($this->immat)?"'".$this->db->escape($this->immat)."'":"null").',';
		$sql .= ' vin = '.(isset($this->vin)?"'".$this->db->escape($this->vin)."'":"null").',';
		$sql .= ' num_serie = '.(isset($this->num_serie)?"'".$this->db->escape($this->num_serie)."'":"null").',';
		$sql .= ' modele = '.(isset($this->modele)?"'".$this->db->escape($this->modele)."'":"null").',';
		$sql .= ' volume = '.(isset($this->volume)?"'".$this->db->escape($this->volume)."'":"null").',';
		$sql .= ' county = '.(isset($this->county)?"'".$this->db->escape($this->county)."'":"null").',';
		$sql .= ' fk_user = '.(isset($this->fk_user)?$this->fk_user:"null").',';
		$sql .= ' fk_lead = '.(isset($this->fk_lead)?$this->fk_lead:"null").',';
		$sql .= ' fk_order = '.(isset($this->fk_order)?$this->fk_order:"null").',';
		$sql .= ' import_key = '.(isset($this->import_key)?"'".$this->db->escape($this->import_key)."'":"null").',';
		$sql .= ' tms = '.(dol_strlen($this->tms) != 0 ? "'".$this->db->idate($this->tms)."'" : "'".$this->db->idate(dol_now())."'");

        
		$sql .= ' WHERE rowid=' . $this->id;

		$this->db->begin();

		$resql = $this->db->query($sql);
		if (!$resql) {
			$error ++;
			$this->errors[] = 'Error ' . $this->db->lasterror();
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		if (!$error && !$notrigger) {
			// Uncomment this and change MYOBJECT to your own tag if you
			// want this action calls a trigger.

			//// Call triggers
			//$result=$this->call_trigger('MYOBJECT_MODIFY',$user);
			//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
			//// End call triggers
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Delete object in database
	 *
	 * @param User $user      User that deletes
	 * @param bool $notrigger false=launch triggers after, true=disable triggers
	 *
	 * @return int <0 if KO, >0 if OK
	 */
	public function delete(User $user, $notrigger = false)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		$error = 0;

		$this->db->begin();

		if (!$error) {
			if (!$notrigger) {
				// Uncomment this and change MYOBJECT to your own tag if you
				// want this action calls a trigger.

				//// Call triggers
				//$result=$this->call_trigger('MYOBJECT_DELETE',$user);
				//if ($result < 0) { $error++; //Do also what you must do to rollback action if trigger fail}
				//// End call triggers
			}
		}

		if (!$error) {
			$sql = 'DELETE FROM ' . MAIN_DB_PREFIX . $this->table_element;
			$sql .= ' WHERE rowid=' . $this->id;

			$resql = $this->db->query($sql);
			if (!$resql) {
				$error ++;
				$this->errors[] = 'Error ' . $this->db->lasterror();
				dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
			}
		}

		// Commit or rollback
		if ($error) {
			$this->db->rollback();

			return - 1 * $error;
		} else {
			$this->db->commit();

			return 1;
		}
	}

	/**
	 * Load an object from its id and create a new one in database
	 *
	 * @param int $fromid Id of object to clone
	 *
	 * @return int New id of clone
	 */
	public function createFromClone($fromid)
	{
		dol_syslog(__METHOD__, LOG_DEBUG);

		global $user;
		$error = 0;
		$object = new Immat($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		// Reset object
		$object->id = 0;

		// Clear fields
		// ...

		// Create clone
		$result = $object->create($user);

		// Other options
		if ($result < 0) {
			$error ++;
			$this->errors = $object->errors;
			dol_syslog(__METHOD__ . ' ' . join(',', $this->errors), LOG_ERR);
		}

		// End
		if (!$error) {
			$this->db->commit();

			return $object->id;
		} else {
			$this->db->rollback();

			return - 1;
		}
	}

	/**
	 *  Return a link to the user card (with optionaly the picto)
	 * 	Use this->id,this->lastname, this->firstname
	 *
	 *	@param	int		$withpicto			Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
	 *	@param	string	$option				On what the link point to
     *  @param	integer	$notooltip			1=Disable tooltip
     *  @param	int		$maxlen				Max length of visible user name
     *  @param  string  $morecss            Add more css on link
	 *	@return	string						String with URL
	 */
	function getNomUrl($withpicto=0, $option='', $notooltip=0, $maxlen=24, $morecss='')
	{
		global $langs, $conf, $db;
        global $dolibarr_main_authentication, $dolibarr_main_demo;
        global $menumanager;


        $result = '';
        $companylink = '';

        $label = '<u>' . $langs->trans("MyModule") . '</u>';
        $label.= '<div width="100%">';
        $label.= '<b>' . $langs->trans('Ref') . ':</b> ' . $this->ref;

        $link = '<a href="'.DOL_URL_ROOT.'/immat/card.php?id='.$this->id.'"';
        $link.= ($notooltip?'':' title="'.dol_escape_htmltag($label, 1).'" class="classfortooltip'.($morecss?' '.$morecss:'').'"');
        $link.= '>';
		$linkend='</a>';

        if ($withpicto)
        {
            $result.=($link.img_object(($notooltip?'':$label), 'label', ($notooltip?'':'class="classfortooltip"')).$linkend);
            if ($withpicto != 2) $result.=' ';
		}
		$result.= $link . $this->ref . $linkend;
		return $result;
	}
	
	/**
	 *  Retourne le libelle du status d'un user (actif, inactif)
	 *
	 *  @param	int		$mode          0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return	string 			       Label of status
	 */
	function getLibStatut($mode=0)
	{
		return $this->LibStatut($this->status,$mode);
	}

	/**
	 *  Renvoi le libelle d'un status donne
	 *
	 *  @param	int		$status        	Id status
	 *  @param  int		$mode          	0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
	 *  @return string 			       	Label of status
	 */
	function LibStatut($status,$mode=0)
	{
		global $langs;

		if ($mode == 0)
		{
			$prefix='';
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 1)
		{
			if ($status == 1) return $langs->trans('Enabled');
			if ($status == 0) return $langs->trans('Disabled');
		}
		if ($mode == 2)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 3)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5');
		}
		if ($mode == 4)
		{
			if ($status == 1) return img_picto($langs->trans('Enabled'),'statut4').' '.$langs->trans('Enabled');
			if ($status == 0) return img_picto($langs->trans('Disabled'),'statut5').' '.$langs->trans('Disabled');
		}
		if ($mode == 5)
		{
			if ($status == 1) return $langs->trans('Enabled').' '.img_picto($langs->trans('Enabled'),'statut4');
			if ($status == 0) return $langs->trans('Disabled').' '.img_picto($langs->trans('Disabled'),'statut5');
		}
	}
	
	
	/**
	 * Initialise object with example values
	 * Id must be 0 if object instance is a specimen
	 *
	 * @return void
	 */
	public function initAsSpecimen()
	{
		$this->id = 0;
		
		$this->genre = '';
		$this->marque = '';
		$this->type_veh = '';
		$this->energie = '';
		$this->carrosserie = '';
		$this->const_dist = '';
		$this->ptr = '';
		$this->gvw = '';
		$this->charutpl = '';
		$this->puissfisc = '';
		$this->fk_soc = '';
		$this->status = '';
		$this->csp_prop = '';
		$this->dt_carte_grise = '';
		$this->immat = '';
		$this->vin = '';
		$this->num_serie = '';
		$this->modele = '';
		$this->volume = '';
		$this->county = '';
		$this->fk_user = '';
		$this->fk_lead = '';
		$this->fk_order = '';
		$this->import_key = '';
		$this->tms = '';

		
	}

}

/**
 * Class ImmatLine
 */
class ImmatLine
{
	/**
	 * @var int ID
	 */
	public $id;
	/**
	 * @var mixed Sample line property 1
	 */
	
	public $genre;
	public $marque;
	public $type_veh;
	public $energie;
	public $carrosserie;
	public $const_dist;
	public $ptr;
	public $gvw;
	public $charutpl;
	public $puissfisc;
	public $fk_soc;
	public $status;
	public $csp_prop;
	public $dt_carte_grise = '';
	public $immat;
	public $vin;
	public $num_serie;
	public $modele;
	public $volume;
	public $county;
	public $fk_user;
	public $fk_lead;
	public $fk_order;
	public $import_key;
	public $tms = '';

	/**
	 * @var mixed Sample line property 2
	 */
	
}
