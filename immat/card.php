<?php
/* Copyright (C) 2007-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *   	\file       immat/immat_card.php
 *		\ingroup    immat
 *		\brief      This file is an example of a php page
 *					Initialy built by build_class_from_table on 2016-07-18 21:17
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');			// Do not check anti CSRF attack test
//if (! defined('NOSTYLECHECK'))   define('NOSTYLECHECK','1');			// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');		// Do not check anti POST attack test
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');			// If there is no need to load and show top and left menu
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');			// If we don't need to load the html.form.class.php
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');				// If this page is public (can be called outside logged session)

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
if (! $res) die("Include of main fails");
// Change this following line to use the correct relative path from htdocs
include_once(DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php');
dol_include_once('/immat/class/immat.class.php');

// Load traductions files requiredby by page
$langs->load("immat");
$langs->load("other");

// Get parameters
$id			= GETPOST('id','int');
$action		= GETPOST('action','alpha');
$backtopage = GETPOST('backtopage');
$myparam	= GETPOST('myparam','alpha');


$search_genre=GETPOST('search_genre','int');
$search_marque=GETPOST('search_marque','int');
$search_type_veh=GETPOST('search_type_veh','alpha');
$search_energie=GETPOST('search_energie','alpha');
$search_carrosserie=GETPOST('search_carrosserie','int');
$search_const_dist=GETPOST('search_const_dist','alpha');
$search_ptr=GETPOST('search_ptr','int');
$search_gvw=GETPOST('search_gvw','alpha');
$search_charutpl=GETPOST('search_charutpl','int');
$search_puissfisc=GETPOST('search_puissfisc','int');
$search_fk_soc=GETPOST('search_fk_soc','int');
$search_status=GETPOST('search_status','alpha');
$search_csp_prop=GETPOST('search_csp_prop','alpha');
$search_immat=GETPOST('search_immat','alpha');
$search_vin=GETPOST('search_vin','alpha');
$search_num_serie=GETPOST('search_num_serie','alpha');
$search_modele=GETPOST('search_modele','alpha');
$search_volume=GETPOST('search_volume','alpha');
$search_county=GETPOST('search_county','alpha');
$search_fk_user=GETPOST('search_fk_user','int');
$search_fk_lead=GETPOST('search_fk_lead','int');
$search_fk_order=GETPOST('search_fk_order','int');
$search_import_key=GETPOST('search_import_key','alpha');



// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if (empty($action) && empty($id) && empty($ref)) $action='list';

// Load object if id or ref is provided as parameter
$object=new Immat($db);
if (($id > 0 || ! empty($ref)) && $action != 'add')
{
	$result=$object->fetch($id,$ref);
	if ($result < 0) dol_print_error($db);
}

// Initialize technical object to manage hooks of modules. Note that conf->hooks_modules contains array array
$hookmanager->initHooks(array('immat'));
$extrafields = new ExtraFields($db);



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions',$parameters,$object,$action);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (empty($reshook))
{
	// Action to add record
	if ($action == 'add')
	{
		if (GETPOST('cancel'))
		{
			$urltogo=$backtopage?$backtopage:dol_buildpath('/immat/list.php',1);
			header("Location: ".$urltogo);
			exit;
		}

		$error=0;

		/* object_prop_getpost_prop */
		
	$object->genre=GETPOST('genre','int');
	$object->marque=GETPOST('marque','int');
	$object->type_veh=GETPOST('type_veh','alpha');
	$object->energie=GETPOST('energie','alpha');
	$object->carrosserie=GETPOST('carrosserie','int');
	$object->const_dist=GETPOST('const_dist','alpha');
	$object->ptr=GETPOST('ptr','int');
	$object->gvw=GETPOST('gvw','alpha');
	$object->charutpl=GETPOST('charutpl','int');
	$object->puissfisc=GETPOST('puissfisc','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->status=GETPOST('status','alpha');
	$object->csp_prop=GETPOST('csp_prop','alpha');
	$object->immat=GETPOST('immat','alpha');
	$object->vin=GETPOST('vin','alpha');
	$object->num_serie=GETPOST('num_serie','alpha');
	$object->modele=GETPOST('modele','alpha');
	$object->volume=GETPOST('volume','alpha');
	$object->county=GETPOST('county','alpha');
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_lead=GETPOST('fk_lead','int');
	$object->fk_order=GETPOST('fk_order','int');
	$object->import_key=GETPOST('import_key','alpha');

		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->create($user);
			if ($result > 0)
			{
				// Creation OK
				$urltogo=$backtopage?$backtopage:dol_buildpath('/immat/list.php',1);
				header("Location: ".$urltogo);
				exit;
			}
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else  setEventMessages($object->error, null, 'errors');
				$action='create';
			}
		}
		else
		{
			$action='create';
		}
	}

	// Cancel
	if ($action == 'update' && GETPOST('cancel')) $action='view';

	// Action to update record
	if ($action == 'update' && ! GETPOST('cancel'))
	{
		$error=0;

		
	$object->genre=GETPOST('genre','int');
	$object->marque=GETPOST('marque','int');
	$object->type_veh=GETPOST('type_veh','alpha');
	$object->energie=GETPOST('energie','alpha');
	$object->carrosserie=GETPOST('carrosserie','int');
	$object->const_dist=GETPOST('const_dist','alpha');
	$object->ptr=GETPOST('ptr','int');
	$object->gvw=GETPOST('gvw','alpha');
	$object->charutpl=GETPOST('charutpl','int');
	$object->puissfisc=GETPOST('puissfisc','int');
	$object->fk_soc=GETPOST('fk_soc','int');
	$object->status=GETPOST('status','alpha');
	$object->csp_prop=GETPOST('csp_prop','alpha');
	$object->immat=GETPOST('immat','alpha');
	$object->vin=GETPOST('vin','alpha');
	$object->num_serie=GETPOST('num_serie','alpha');
	$object->modele=GETPOST('modele','alpha');
	$object->volume=GETPOST('volume','alpha');
	$object->county=GETPOST('county','alpha');
	$object->fk_user=GETPOST('fk_user','int');
	$object->fk_lead=GETPOST('fk_lead','int');
	$object->fk_order=GETPOST('fk_order','int');
	$object->import_key=GETPOST('import_key','alpha');

		

		if (empty($object->ref))
		{
			$error++;
			setEventMessages($langs->transnoentitiesnoconv("ErrorFieldRequired",$langs->transnoentitiesnoconv("Ref")), null, 'errors');
		}

		if (! $error)
		{
			$result=$object->update($user);
			if ($result > 0)
			{
				$action='view';
			}
			else
			{
				// Creation KO
				if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
				else setEventMessages($object->error, null, 'errors');
				$action='edit';
			}
		}
		else
		{
			$action='edit';
		}
	}

	// Action to delete
	if ($action == 'confirm_delete')
	{
		$result=$object->delete($user);
		if ($result > 0)
		{
			// Delete OK
			setEventMessages("RecordDeleted", null, 'mesgs');
			header("Location: ".dol_buildpath('/immat/list.php',1));
			exit;
		}
		else
		{
			if (! empty($object->errors)) setEventMessages(null, $object->errors, 'errors');
			else setEventMessages($object->error, null, 'errors');
		}
	}
}




/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','MyPageName','');

$form=new Form($db);


// Put here content of your page

// Example : Adding jquery code
print '<script type="text/javascript" language="javascript">
jQuery(document).ready(function() {
	function init_myfunc()
	{
		jQuery("#myid").removeAttr(\'disabled\');
		jQuery("#myid").attr(\'disabled\',\'disabled\');
	}
	init_myfunc();
	jQuery("#mybutton").click(function() {
		init_myfunc();
	});
});
</script>';


// Part to create
if ($action == 'create')
{
	print load_fiche_titre($langs->trans("NewMyModule"));

	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="add">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';

	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgenre").'</td><td><input class="flat" type="text" name="genre" value="'.GETPOST('genre').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmarque").'</td><td><input class="flat" type="text" name="marque" value="'.GETPOST('marque').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_veh").'</td><td><input class="flat" type="text" name="type_veh" value="'.GETPOST('type_veh').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldenergie").'</td><td><input class="flat" type="text" name="energie" value="'.GETPOST('energie').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcarrosserie").'</td><td><input class="flat" type="text" name="carrosserie" value="'.GETPOST('carrosserie').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldconst_dist").'</td><td><input class="flat" type="text" name="const_dist" value="'.GETPOST('const_dist').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldptr").'</td><td><input class="flat" type="text" name="ptr" value="'.GETPOST('ptr').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgvw").'</td><td><input class="flat" type="text" name="gvw" value="'.GETPOST('gvw').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcharutpl").'</td><td><input class="flat" type="text" name="charutpl" value="'.GETPOST('charutpl').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpuissfisc").'</td><td><input class="flat" type="text" name="puissfisc" value="'.GETPOST('puissfisc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.GETPOST('fk_soc').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.GETPOST('status').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcsp_prop").'</td><td><input class="flat" type="text" name="csp_prop" value="'.GETPOST('csp_prop').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimmat").'</td><td><input class="flat" type="text" name="immat" value="'.GETPOST('immat').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvin").'</td><td><input class="flat" type="text" name="vin" value="'.GETPOST('vin').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_serie").'</td><td><input class="flat" type="text" name="num_serie" value="'.GETPOST('num_serie').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodele").'</td><td><input class="flat" type="text" name="modele" value="'.GETPOST('modele').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvolume").'</td><td><input class="flat" type="text" name="volume" value="'.GETPOST('volume').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcounty").'</td><td><input class="flat" type="text" name="county" value="'.GETPOST('county').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.GETPOST('fk_user').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_lead").'</td><td><input class="flat" type="text" name="fk_lead" value="'.GETPOST('fk_lead').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_order").'</td><td><input class="flat" type="text" name="fk_order" value="'.GETPOST('fk_order').'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td><input class="flat" type="text" name="import_key" value="'.GETPOST('import_key').'"></td></tr>';

	print '</table>'."\n";

	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="add" value="'.$langs->trans("Create").'"> &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></div>';

	print '</form>';
}



// Part to edit record
if (($id || $ref) && $action == 'edit')
{
	print load_fiche_titre($langs->trans("MyModule"));
    
	print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
	print '<input type="hidden" name="id" value="'.$object->id.'">';
	
	dol_fiche_head();

	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgenre").'</td><td><input class="flat" type="text" name="genre" value="'.$object->genre.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmarque").'</td><td><input class="flat" type="text" name="marque" value="'.$object->marque.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_veh").'</td><td><input class="flat" type="text" name="type_veh" value="'.$object->type_veh.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldenergie").'</td><td><input class="flat" type="text" name="energie" value="'.$object->energie.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcarrosserie").'</td><td><input class="flat" type="text" name="carrosserie" value="'.$object->carrosserie.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldconst_dist").'</td><td><input class="flat" type="text" name="const_dist" value="'.$object->const_dist.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldptr").'</td><td><input class="flat" type="text" name="ptr" value="'.$object->ptr.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgvw").'</td><td><input class="flat" type="text" name="gvw" value="'.$object->gvw.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcharutpl").'</td><td><input class="flat" type="text" name="charutpl" value="'.$object->charutpl.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpuissfisc").'</td><td><input class="flat" type="text" name="puissfisc" value="'.$object->puissfisc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td><input class="flat" type="text" name="fk_soc" value="'.$object->fk_soc.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td><input class="flat" type="text" name="status" value="'.$object->status.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcsp_prop").'</td><td><input class="flat" type="text" name="csp_prop" value="'.$object->csp_prop.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimmat").'</td><td><input class="flat" type="text" name="immat" value="'.$object->immat.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvin").'</td><td><input class="flat" type="text" name="vin" value="'.$object->vin.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_serie").'</td><td><input class="flat" type="text" name="num_serie" value="'.$object->num_serie.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodele").'</td><td><input class="flat" type="text" name="modele" value="'.$object->modele.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvolume").'</td><td><input class="flat" type="text" name="volume" value="'.$object->volume.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcounty").'</td><td><input class="flat" type="text" name="county" value="'.$object->county.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td><input class="flat" type="text" name="fk_user" value="'.$object->fk_user.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_lead").'</td><td><input class="flat" type="text" name="fk_lead" value="'.$object->fk_lead.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_order").'</td><td><input class="flat" type="text" name="fk_order" value="'.$object->fk_order.'"></td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td><input class="flat" type="text" name="import_key" value="'.$object->import_key.'"></td></tr>';

	print '</table>';
	
	dol_fiche_end();

	print '<div class="center"><input type="submit" class="button" name="save" value="'.$langs->trans("Save").'">';
	print ' &nbsp; <input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
	print '</div>';

	print '</form>';
}



// Part to show record
if ($id && (empty($action) || $action == 'view' || $action == 'delete'))
{
	print load_fiche_titre($langs->trans("MyModule"));
    
	dol_fiche_head();

	if ($action == 'delete') {
		$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $object->id, $langs->trans('DeleteMyOjbect'), $langs->trans('ConfirmDeleteMyObject'), 'confirm_delete', '', 0, 1);
		print $formconfirm;
	}
	
	print '<table class="border centpercent">'."\n";
	// print '<tr><td class="fieldrequired">'.$langs->trans("Label").'</td><td><input class="flat" type="text" size="36" name="label" value="'.$label.'"></td></tr>';
	// 
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgenre").'</td><td>$object->genre</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmarque").'</td><td>$object->marque</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldtype_veh").'</td><td>$object->type_veh</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldenergie").'</td><td>$object->energie</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcarrosserie").'</td><td>$object->carrosserie</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldconst_dist").'</td><td>$object->const_dist</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldptr").'</td><td>$object->ptr</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldgvw").'</td><td>$object->gvw</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcharutpl").'</td><td>$object->charutpl</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldpuissfisc").'</td><td>$object->puissfisc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_soc").'</td><td>$object->fk_soc</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldstatus").'</td><td>$object->status</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcsp_prop").'</td><td>$object->csp_prop</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimmat").'</td><td>$object->immat</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvin").'</td><td>$object->vin</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldnum_serie").'</td><td>$object->num_serie</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldmodele").'</td><td>$object->modele</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldvolume").'</td><td>$object->volume</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldcounty").'</td><td>$object->county</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_user").'</td><td>$object->fk_user</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_lead").'</td><td>$object->fk_lead</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldfk_order").'</td><td>$object->fk_order</td></tr>';
print '<tr><td class="fieldrequired">'.$langs->trans("Fieldimport_key").'</td><td>$object->import_key</td></tr>';

	print '</table>';
	
	dol_fiche_end();


	// Buttons
	print '<div class="tabsAction">'."\n";
	$parameters=array();
	$reshook=$hookmanager->executeHooks('addMoreActionsButtons',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
	if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

	if (empty($reshook))
	{
		if ($user->rights->immat->write)
		{
			print '<div class="inline-block divButAction"><a class="butAction" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=edit">'.$langs->trans("Modify").'</a></div>'."\n";
		}

		if ($user->rights->immat->delete)
		{
			print '<div class="inline-block divButAction"><a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'&amp;action=delete">'.$langs->trans('Delete').'</a></div>'."\n";
		}
	}
	print '</div>'."\n";


	// Example 2 : Adding links to objects
	//$somethingshown=$form->showLinkedObjectBlock($object);
	//$linktoelem = $form->showLinkToObjectBlock($object);
	//if ($linktoelem) print '<br>'.$linktoelem;

}


// End of page
llxFooter();
$db->close();
