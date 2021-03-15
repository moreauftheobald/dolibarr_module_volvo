<?php
/* Copyright (C) 2005-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2015 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2013      Juanjo Menent		<jmenent@2byte.es>
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
 * or see http://www.gnu.org/
 */

/**
 *	\file       htdocs/core/modules/facture/mod_facture_mars.php
 *	\ingroup    facture
 *	\brief      File containing class for numbering module Mars
 */
require_once DOL_DOCUMENT_ROOT .'/core/modules/facture/modules_facture.php';

/**
 * 	Classe du modele de numerotation de reference de facture Mars
 */
class mod_facture_volvo extends ModeleNumRefFactures
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $prefixinvoice='';
	var $prefixreplacement='';
	var $prefixdeposit='';
	var $prefixcreditnote='';
	var $error='';

	/**
	 *  Renvoi la description du modele de numerotation
	 *
	 *  @return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		return "Module de numérotation spécifique Volvo Théobald";
	}

	/**
	 *  Renvoi un exemple de numerotation
	 *
	 *  @return     string      Example
	 */
	function getExample()
	{
		return $this->prefixinvoice."VOL-FAC-210001";
	}

	/**
	 *  Test si les numeros deja en vigueur dans la base ne provoquent pas de
	 *  de conflits qui empechera cette numerotation de fonctionner.
	 *
	 *  @return     boolean     false si conflit, true si ok
	 */
	function canBeActivated()
	{
		global $langs,$conf,$db;

		$langs->load("bills");

		// Check invoice num
		$fayymm=''; $max='';

		$posindice=8;
		$sql = "SELECT MAX(CAST(SUBSTRING(facnumber FROM ".$posindice.") AS SIGNED) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."facture";
		$sql.= " WHERE facnumber LIKE '".$this->prefixinvoice."____-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		if ($resql)
		{
			$row = $db->fetch_row($resql);
			if ($row) { $fayymm = substr($row[0],0,6); $max=$row[0]; }
		}
		if ($fayymm && ! preg_match('/'.$this->prefixinvoice.'[0-9][0-9][0-9][0-9]/i',$fayymm))
		{
			$langs->load("errors");
			$this->error=$langs->trans('ErrorNumRefModel',$max);
			return false;
		}

		// Check credit note num
		$fayymm='';

		$posindice=8;
		$sql = "SELECT MAX(SUBSTRING(facnumber FROM ".$posindice.")) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."facture";
		$sql.= " WHERE facnumber LIKE '".$this->prefixcreditnote."____-%'";
		$sql.= " AND entity = ".$conf->entity;

		$resql=$db->query($sql);
		if ($resql)
		{
			$row = $db->fetch_row($resql);
			if ($row) { $fayymm = substr($row[0],0,6); $max=$row[0]; }
		}
		if ($fayymm && ! preg_match('/'.$this->prefixcreditnote.'[0-9][0-9][0-9][0-9]/i',$fayymm))
		{
			$this->error=$langs->trans('ErrorNumRefModel',$max);
			return false;
		}

		return true;
	}

	/**
	 * Return next value not used or last value used
	 *
	 * @param	Societe		$objsoc		Object third party
	 * @param   Facture		$facture	Object invoice
     * @param   string		$mode       'next' for next value or 'last' for last value
	 * @return  string       			Value
	 */
	function getNextValue($objsoc,$facture,$mode='next')
	{
		global $db;

		$prefix=$this->prefixinvoice;
		$date=$facture->date;
		$yy = strftime("%y",$date);

		if ($facture->type == 1){
		    if ($facture->array_options['options_canal'] == 1) $prefix="VOL-REP-";
		    else if ($facture->array_options['options_canal'] == 2) $prefix="NIS-REP-";
		    else if ($facture->array_options['options_canal'] == 3) $prefix="SCH-REP-";
		    else if ($facture->array_options['options_canal'] == 4) $prefix="VOC-REP-";
		    else if ($facture->array_options['options_canal'] == 5) $prefix="DIV-REP-";
		}else if ($facture->type == 2){
		    if ($facture->array_options['options_canal'] == 1) $prefix="VOL-AVO-";
		    else if ($facture->array_options['options_canal'] == 2) $prefix="NIS-AVO-";
		    else if ($facture->array_options['options_canal'] == 3) $prefix="SCH-AVO-";
		    else if ($facture->array_options['options_canal'] == 4) $prefix="VOC-AVO-";
		    else if ($facture->array_options['options_canal'] == 5) $prefix="DIV-AVO-";
		}else if ($facture->type == 3){
		    if ($facture->array_options['options_canal'] == 1) $prefix="VOL-ACC-";
		    else if ($facture->array_options['options_canal'] == 2) $prefix="NIS-ACC-";
		    else if ($facture->array_options['options_canal'] == 3) $prefix="SCH-ACC-";
		    else if ($facture->array_options['options_canal'] == 4) $prefix="VOC-ACC-";
		    else if ($facture->array_options['options_canal'] == 5) $prefix="DIV-ACC-";
		}else {
		    if ($facture->array_options['options_canal'] == 1) $prefix="VOL-FAC-";
		    else if ($facture->array_options['options_canal'] == 2) $prefix="NIS-FAC-";
		    else if ($facture->array_options['options_canal'] == 3) $prefix="SCH-FAC-";
		    else if ($facture->array_options['options_canal'] == 4) $prefix="VOC-FAC-";
		    else if ($facture->array_options['options_canal'] == 5) $prefix="DIV-FAC-";
		}

		// D'abord on recupere la valeur max
		$posindice=8;
		$sql = "SELECT MAX(CAST(SUBSTRING(facnumber FROM ".$posindice.") AS SIGNED)) as max";	// This is standard SQL
		$sql.= " FROM ".MAIN_DB_PREFIX."facture";
		$sql.= " WHERE facnumber LIKE '".$prefix.$yy."-____%'";
		$sql.= " AND entity IN (".getEntity('facture', 1).")";
        
		var_dump($sql);
		exit;
		
		$resql=$db->query($sql);
		dol_syslog(get_class($this)."::getNextValue", LOG_DEBUG);
		if ($resql)
		{
			$obj = $db->fetch_object($resql);
			if ($obj) $max = intval($obj->max);
			else $max=0;
		}
		else
		{
			return -1;
		}

		if ($mode == 'last')
		{
    		if ($max >= (pow(10, 4) - 1)) $num=$max;	// If counter > 9999, we do not format on 4 chars, we take number as it is
    		else $num = sprintf("%04s",$max);

            $ref='';
            $sql = "SELECT facnumber as ref";
            $sql.= " FROM ".MAIN_DB_PREFIX."facture";
            $sql.= " WHERE facnumber LIKE '".$prefix.$yy."____-%'";
            $sql.= " AND entity IN (".getEntity('facture', 1).")";

            dol_syslog(get_class($this)."::getNextValue", LOG_DEBUG);
            $resql=$db->query($sql);
            if ($resql)
            {
                $obj = $db->fetch_object($resql);
                if ($obj) $ref = $obj->ref;
            }
            else dol_print_error($db);

            return $ref;
		}
		else if ($mode == 'next')
		{
    		$date=$facture->date;	// This is invoice date (not creation date)
    		$yymm = strftime("%y",$date);

    		if ($max >= (pow(10, 4) - 1)) $num=$max+1;	// If counter > 9999, we do not format on 4 chars, we take number as it is
    		else $num = sprintf("%04s",$max+1);

    		dol_syslog(get_class($this)."::getNextValue return ".$prefix.$yymm."-".$num);
    		return $prefix.$yymm."-".$num;
		}
		else dol_print_error('','Bad parameter for getNextValue');
	}

	/**
	 * Return next free value
	 *
     * @param	Societe		$objsoc     	Object third party
     * @param	string		$objforref		Object for number to search
     * @param   string		$mode       	'next' for next value or 'last' for last value
     * @return  string      				Next free value
	 */
	function getNumRef($objsoc,$objforref,$mode='next')
	{
		return $this->getNextValue($objsoc,$objforref,$mode);
	}

}






