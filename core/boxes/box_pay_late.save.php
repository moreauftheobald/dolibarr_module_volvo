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
class box_pay_late extends ModeleBoxes
{
    var $boxcode="pay_late";
    var $boximg="lead@lead";
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
        $langs->load("pay_late");
        $this->boxlabel="Affaire en attente de paiement";
        $this->db = $db;
    }

	/**
	 *  Load data for box to show them later
	 *
	 *  @param  int     $max        Maximum number of records to load
	 *  @return void
	 */
	function loadBox($max=5)
	{
		global $conf, $user, $langs, $db;

		$this->max=$max;

		$totalMnt = 0;
		$totalnb = 0;
		$totalDuree=0;
		dol_include_once('/lead/class/lead.class.php');
		$lead=new lead($db);


		$textHead = "Affaire en attente de paiement";
		$this->info_box_head = array('text' => $textHead, 'limit'=> dol_strlen($textHead));

		$sql = "SELECT l.rowid as lead, DATEDIFF(NOW(), sa.dt_fact + INTERVAL t.del_rg DAY) as delais, t.genre as genre, IF(DATEDIFF(NOW(), sa.dt_fact + INTERVAL t.del_rg DAY)< -10,1,IF(DATEDIFF(NOW(), sa.dt_fact + INTERVAL t.del_rg DAY)<0,2,3)) AS statut";
		$sql.= " FROM ".MAIN_DB_PREFIX."lead AS l INNER JOIN ".MAIN_DB_PREFIX."cust_suiviadmin_extrafields AS sa ON l.rowid = sa.fk_object";
		$sql.= " INNER JOIN ".MAIN_DB_PREFIX."lead_extrafields AS ef ON l.rowid = ef.fk_object INNER JOIN ".MAIN_DB_PREFIX."cust_genre_extrafields as t on ef.type = t.rowid";
		$sql.= " WHERE sa.dt_fact IS NOT NULL ";
		$sql.= " AND l.fk_c_status = 6";
		$sql.= " AND sa.dt_rg is not null ";
		$sql.= " AND sa.dt_paye IS NULL ";
		if (empty($user->rights->societe->client->voir)) {
			$sql.= " AND l.fk_user_resp =" . $user->id ;
		}
		$sql.= " ORDER BY DATEDIFF(NOW(), sa.dt_fact + INTERVAL t.del_rg DAY) DESC";
		$sql.= $db->plimit($max, 0);
		$result = $db->query($sql);

		if ($result) {
			$num = $db->num_rows($result);
			$i = 0;
			while ($i < $num) {
				$objp = $db->fetch_object($result);
				$lead->fetch($objp->lead);
				$lead->fetch_thirdparty();

				if ($objp->statut == 1) {
					$imgst ='statut4@lead';
				} elseif ($objp->statut == 2){
					$imgst ='statut1@lead';
				} else {
					$imgst ='statut8@lead';
				}

				$this->info_box_contents[$i][0] = array(
				'td' => 'align="left" width="16"',
				'logo' => $this->boximg,
				'url' => dol_buildpath('/lead/lead/card.php', 1) . '?id=' . $lead->id
				);

				$this->info_box_contents[$i][1] = array(
				'td' => 'align="left"',
				'text' => $lead->ref,
				'url' => dol_buildpath('/lead/lead/card.php', 1) . '?id=' . $lead->id
				);
				$this->info_box_contents[$i][2] = array(
				'td' => 'align="left" width="16"',
				'logo' => 'company',
				'url' => DOL_URL_ROOT . "/societe/soc.php?socid=" . $lead->fk_soc
				);

				$this->info_box_contents[$i][3] = array(
				'td' => 'align="left"',
				'text' => dol_trunc($lead->thirdparty->name, 30),
				'url' => DOL_URL_ROOT . "/societe/soc.php?socid=" . $lead->fk_soc
				);

				$this->info_box_contents[$i][4] = array(
				'td' => 'align="left"',
				'text' => dol_trunc($objp->genre,8). ' ' . $objp->delais. ' jours'
				);

				$this->info_box_contents[$i][5] = array(
				'td' => 'align="left"',
				'logo' => $imgst
				);

				$i++;
			}

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
