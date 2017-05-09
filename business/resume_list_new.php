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

require_once DOL_DOCUMENT_ROOT . '/volvo/class/table_template.class.php';

$table = new Dyntable($db);

$table->title = 'Suivis d\'activité VN volvo - detail';
$table->default_sortfield = 'dt_sortie';
$table->export_name = 'suivi_activité-detail_new';
$table->context = 'suivi_activite_detail';
$table->export_button = 1;
$table->select_fields_button = 1;
$table->mode = 'function_methode';
$table->include = '/volvo/lib/volvo.lib.php';
$table->function = 'stat_sell';
$table->limit = 0;
$table->param0 = 'filter';


$field= new Dyntable_fields($db);
$field->name='dossier';
$field->label = 'Dossier';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->post_traitement = array('link', '/commande/card.php','?id=','id');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='ca_total';
$field->label = 'C.A. Total HT';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->post_traitement = array('price', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='ca_volvo';
$field->label = 'C.A. Fac. Volvo';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='nb_trt';
$field->label = 'Nb Tracteurs';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='nb_port';
$field->label = 'Nb Porteurs';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='vcm';
$field->label = 'VCM';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->post_traitement = array('num', '0');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dfol';
$field->label = 'DFOL';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->post_traitement = array('num', '0');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dded';
$field->label = 'DDED';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->post_traitement = array('num', '0');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='vfs';
$field->label = 'VFS';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='lixbail';
$field->label = 'Lixbail';
$field->checked = 1;
$field->sub_title = 1;
$field->align = 'center';
$field->post_traitement = array('num', 0);
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_tot';
$field->label = 'Marge totale';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_tot_r';
$field->label = 'Marge Totale Réélle';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='m_tot_e';
$field->label = 'Marge totale - Ecart';
$field->checked = 1;
$field->sub_title = 0;
$field->unit = '€';
$field->align = 'center';
$field->post_traitement = array('price', '2');
$table->arrayfields[$field->name] = $field;

$tools =array();

$table->extra_tools =$tools;

$table->sub_title = array(1=>'Soft Offers');

$table->post();

$table->data_array();

$table->header();

$table->draw_tool_bar();

$table->draw_table_head();

$table->draw_data_table();

$table->end_table();