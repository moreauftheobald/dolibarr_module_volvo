<?php

/* Copyright (C) 2015		Florian HENRY	<florian.henry@atm-consulting.fr>
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
 * \file htdocs/lead/class/actions_lead.class.php
 * \ingroup lead
 * \brief Fichier de la classe des actions/hooks des lead
 */
class ActionsVolvo // extends CommonObject
{

	/**
	 * addMoreActionsButtons Method Hook Call
	 *
	 * @param string[] $parameters parameters
	 * @param CommonObject $object Object to use hooks on
	 * @param string $action Action code on calling page ('create', 'edit', 'view', 'add', 'update', 'delete'...)
	 * @param HookManager $hookmanager class instance
	 * @return int Hook status
	 */
	function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager) {
		global $langs, $conf, $user, $db, $bc;

		$current_context = explode(':', $parameters['context']);
		if (in_array('ordercard', $current_context)) {




		} elseif (in_array('thirdpartycard', $current_context)){
			$out = '<script type="text/javascript">' . "\n";
			$out .= '  	$(document).ready(function() {' . "\n";
			$out .= '		$a = $(\'<a href="javascript:popCalendar()" class="butAction">Créer un calendrier</a>\');' . "\n";
			$out .= '		$(\'div.fiche div.tabsAction\').first().prepend($a);' . "\n";
			$out .= '  	});' . "\n";
			$out .= '' . "\n";
			$out .= '  	function popCalendar() {' . "\n";
			$out .= '  		$div = $(\'<div id="popCalendar"><iframe width="100%" height="100%" frameborder="0" src="' . dol_buildpath('/volvo/event/createcustcalendar.php?socid=' . $object->id, 1) . '"></iframe></div>\');' . "\n";
			$out .= '' . "\n";
			$out .= '  		$div.dialog({' . "\n";
			$out .= '  			modal:true' . "\n";
			$out .= '  			,width:"90%"' . "\n";
			$out .= '  			,height:$(window).height() - 150' . "\n";
			$out .= '  			,close:function() {document.location.reload(true);}' . "\n";
			$out .= '  		});' . "\n";
			$out .= '' . "\n";
			$out .= '  	}' . "\n";
			$out .= '' . "\n";
			$out .= '</script>';
			print $out;
		}

		// Always OK
		return 0;
	}

	/**
	 * formObjectOptions Method Hook Call
	 *
	 * @param string[] $parameters parameters
	 * @param CommonObject $object Object to use hooks on
	 * @param string $action Action code on calling page ('create', 'edit', 'view', 'add', 'update', 'delete'...)
	 * @param HookManager $hookmanager class instance
	 * @return int Hook status
	 */
	function formObjectOptions($parameters, &$object, &$action, $hookmanager) {
		global $langs, $conf, $user, $db, $bc;

		$current_context = explode(':', $parameters['context']);
		if (in_array('ordercard', $current_context)) {


		}

		return 0;
	}

	/**
	 * doActions Method Hook Call
	 *
	 * @param string[] $parameters parameters
	 * @param CommonObject $object Object to use hooks on
	 * @param string $action Action code on calling page ('create', 'edit', 'view', 'add', 'update', 'delete'...)
	 * @param HookManager $hookmanager class instance
	 * @return int Hook status
	 */
	function doActions($parameters, &$object, &$action, $hookmanager) {
		global $langs, $conf, $user, $db, $bc;

		$current_context = explode(':', $parameters['context']);

		if (in_array('ordercard', $current_context)) {
 			header("Location: ".DOL_URL_ROOT.'/volvo/commande/card.php?id=' . $object->id);
 			exit;

		} elseif (in_array('ordersuppliercard', $current_context)) {
			header("Location: ".DOL_URL_ROOT.'/volvo/fourn/commande/card.php?id=' . $object->id);
			exit;

		}

		return 0;
	}


	/**
	 * formConfirm Method Hook Call
	 *
	 * @param string[] $parameters parameters
	 * @param CommonObject $object Object to use hooks on
	 * @param string $action Action code on calling page ('create', 'edit', 'view', 'add', 'update', 'delete'...)
	 * @param HookManager $hookmanager class instance
	 * @return int Hook status
	 */
	function formConfirm($parameters, &$object, &$action, $hookmanager) {
		global $langs, $conf, $user, $db, $bc;

		$current_context = explode(':', $parameters['context']);
		if (in_array('ordercard', $current_context)) {

		}

		return 0;
	}
}
