<?php
/* Copyright (C) 2012-2014 Charles-François BENKE <charles.fr@benke.fr>
 * Copyright (C) 2015      Frederic France        <frederic.france@free.fr>
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
 *  \file       htdocs/core/boxes/box_task.php
 *  \ingroup    Projet
 *  \brief      Module to Task activity of the current year
 */

include_once(DOL_DOCUMENT_ROOT."/core/boxes/modules_boxes.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");

/**
 * Class to manage the box to show last task
 */
class box_delaicash_indiv extends ModeleBoxes
{
    var $boxcode="delai_indiv";
    var $boximg="iron02@volvo";
    var $boxlabel;
    //var $depends = array("projet");
    var $db;
    var $param;

    var $info_box_head = array();
    var $info_box_contents = array();

    /**
     *  Constructor
     *
     *  @param  DoliDB  $db         Database handler
     *  @param  string  $param      More parameters
     */
    function __construct($db,$param='')
    {
        global $langs;
        $langs->load("boxes");
        $this->boxlabel="Commande non payées";
        $this->db = $db;
    }

	/**
	 *  Load data for box to show them later
	 *
	 *  @param  int     $max        Maximum number of records to load
	 *  @return void
	 */
	function loadBox()
	{
		global $user,$db;
		dol_include_once('/volvo/class/lead.extend.class.php');
		$lead = new Leadext($db);
		$lead->fetchdelaicash('DESC','diff_cash',15,0,array('commercial'=>$user->id,'dt_pay_isnull'=>1,));

		$this->info_box_head = array('text' => 'Commandes non payées', 'limit'=> 50);

		$i = 0;
		foreach ($lead->business AS $line){
			if($line->diff_cash < 0){
				$img = img_picto('Delai dépassé', 'statut8');
			}elseif($line->diff_cash>=0 && $line->diff_cash <8){
				$img =img_picto('paiment urgent', 'statut1');
			}else{
				$img=img_picto('Délai en cours', 'statut4');
			}

			$this->info_box_contents[$i][0] = array(
				'td' => 'align="center" width="16"',
				'logo' => $img,
			);

			$this->info_box_contents[$i][1] = array(
					'td' => 'align="center"',
					'text' => $line->leadref,
					'url' => DOL_URL_ROOT . "custom/lead/lead/card.php?socid=" . $line->lead
			);

			$i++;
		}
	}
	/**
	 *	Method to show box
	 *
	 *	@param	array	$head       Array with properties of box title
	 *	@param  array	$contents   Array with properties of box lines
	 *	@return	void
	 */
	function showBox($head = null, $contents = null, $nooutput = 0)
	{
		parent::showBox($this->info_box_head, $this->info_box_contents);
	}
}
