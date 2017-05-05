<?php
/*
 * Copyright (C) 2014 Florian HENRY <florian.henry@open-concept.pro>
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

$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

if (! $user->rights->volvo->activite)
	accessforbidden();


require_once DOL_DOCUMENT_ROOT . '/volvo/lib/volvo.lib.php';
require_once DOL_DOCUMENT_ROOT . '/volvo/class/table_template.class.php';

$table = new Dyntable($db);

$table->title = 'Suivis d\'activité VN volvo';
$table->default_sortfield = 'dt_sortie';
$table->export_name = 'suivi_activité_new';
$table->context = 'suivi_activite';
$table->search_button = 1;
$table->remove_filter_button = 1;
$table->export_button = 1;
$table->select_fields_button = 1;
$table->mode = 'function_methode';
$table->include = '/volvo/lib/volvo.lib.php';
$table->function = 'stat_sell';
$table->limit = 0;
$table->param0 = 'year';
$table->param1 = 'search_commercial';
$table->param2 = 'monthlist';

$field= new Dyntable_fields($db);
$field->name='mois';
$field->label = 'Mois';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->alias = 'comm';
$field->post_traitement = array('link', '/user/card.php','?id=','commercial');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='nb_facture';
$field->label = 'Nb Factures';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->alias = 'commande';
$field->post_traitement = array('num',0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='nb_portfeuille';
$field->label = 'Nb portefeuille';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->alias = 'numom';
$field->post_traitement = array('num',0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='ca_total';
$field->label = 'C.A. Total HT';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'socnom';
$field->post_traitement = array('price', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='ca_volvo';
$field->label = 'C.A. Fac. Volvo';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'dt_env_usi';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='nb_trt';
$field->label = 'Nb Tracteurs';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->alias = 'dt_liv_dem_cli';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='nb_port';
$field->label = 'Nb Porteurs';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->alias = 'dt_sortie';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='precent_trt';
$field->label = '% Tracteurs';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '%';
$field->align = 'center';
$field->alias = 'vin';
$field->post_traitement = array('num', 2);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='percent_prt';
$field->label = '% Porteurs';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '%';
$field->align = 'center';
$field->alias = 'dt_sortie';
$field->post_traitement = array('num', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='vcm';
$field->label = 'VCM';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->alias = 'genre';
$field->post_traitement = array('num', '0');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dfol';
$field->label = 'DFOL';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->alias = 'gamme';
$field->post_traitement = array('num', '0');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dded';
$field->label = 'DDED';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->alias = 'silouhette';
$field->post_traitement = array('num', '0');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='vfs';
$field->label = 'VFS';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->alias = 'pv';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='lixbail';
$field->label = 'Lixbail';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->alias = 'pv';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_tot';
$field->label = 'Marge totale';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'dt_env_usi';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_moy';
$field->label = 'Marge moyenne';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'dt_env_usi';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_tot_r';
$field->label = 'Marge Totale Réélle';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'dt_env_usi';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_moy_r';
$field->label = 'Marge Moyenne Réélle';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'dt_env_usi';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_tot_e';
$field->label = 'Marge totale - Ecart';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'dt_env_usi';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_moy_e';
$field->label = 'Marge Moyenne - Ecart';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->alias = 'dt_env_usi';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$tools =array();

$tool = new Dyntable_tools($db);
$tool->type = 'select_year';
$tool->title = 'Année: ';
$tool->html_name = 'year';
$tool->use_empty = 0;
$tool->min_year = 5;
$tool->max_year = 0;
$tool->default = dol_print_date(dol_now(),'%Y');
$tools['1'] = $tool;

$tool = new Dyntable_tools($db);
$tool->type = 'select_user';
$tool->title = 'Commercial: ';
$tool->html_name = 'search_commercial';
$tool->use_empty = 1;
$tool->see_all = $user->rights->volvo->stat_all;
$tool->limit_to_group = '1';
$tools['2'] = $tool;

$periodarray= array(
		'1,2,3'=>'1er Trimestre',
		'4,5,6'=> '2eme Trimestre',
		'7,8,9'=>'3eme Trimestre',
		'10,11,12'=>'4eme Trimestre',
		'1,2,3,4,5,6'=>'1er Semestre',
		'7,8,9,10,11,12'=>'2eme Semestre'
);

$tool = new Dyntable_tools($db);
$tool->type = 'select_array';
$tool->title = 'Periode: ';
$tool->html_name = 'search_periode';
$tool->use_empty = 1;
$tool->array = $periodarray;
$tools['3'] = $tool;

$table->extra_tools =$tools;

$table->sub_title = array(1=>'Soft Offers');

$month = array(
		1=>'Janvier',
		2=>'Fevrier',
		3=>'Mars',
		4=>'Avril',
		5=>'Mai',
		6=>'Juin',
		7=>'Juillet',
		8=>'Aout',
		9=>'Septembre',
		10=>'Octobre',
		11=>'Novembre',
		12=>'Décembre'
);

$table->post();

$table->data_array();

$table->header();

$table->draw_tool_bar();

$table->draw_table_head();

$table->draw_data_table();

$table->end_table();










