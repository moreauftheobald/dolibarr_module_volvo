<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2014 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2010-2016 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2011      Jean Heimburger      <jean@tiaris.info>
 * Copyright (C) 2012-2014 Christophe Battarel  <christophe.battarel@altairis.fr>
 * Copyright (C) 2012      Cedric Salvador      <csalvador@gpcsolutions.fr>
 * Copyright (C) 2013      Florian Henry		<florian.henry@open-concept.pro>
 * Copyright (C) 2014-2015 Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2016      Ferran Marcet        <fmarcet@2byte.es>
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
 *  \file       htdocs/commande/class/commande.class.php
 *  \ingroup    commande
 *  \brief      Fichier des classes de commandes
 */
include_once DOL_DOCUMENT_ROOT .'/core/class/commonorder.class.php';
require_once DOL_DOCUMENT_ROOT .'/core/class/commonobjectline.class.php';
require_once DOL_DOCUMENT_ROOT .'/product/class/product.class.php';
require_once NUSOAP_PATH.'/nusoap.php';     // Include SOAP

/**
 *  Class to manage customers orders
 */
class CommandeTrans extends CommonOrder
{
    public $element='commande';
    public $table_element='commande';
    public $table_element_line = 'commandedet';
    public $class_element_line = 'OrderLine';
    public $fk_element = 'fk_commande';
    protected $ismultientitymanaged = 1;	// 0=No test on entity, 1=Test with field entity, 2=Test with link by societe

    /**
     * ERR Not enough stock
     */
    const STOCK_NOT_ENOUGH_FOR_ORDER = -3;

	/**
	 * Canceled status
	 */
	const STATUS_CANCELED = -1;
	/**
	 * Draft status
	 */
	const STATUS_DRAFT = 0;
	/**
	 * Validated status
	 */
	const STATUS_VALIDATED = 1;
	/**
	 * Accepted (supplier orders)
	 */
	const STATUS_ACCEPTED = 2;
	/**
	 * Shipment on process (customer orders)
	 */
	const STATUS_SHIPMENTONPROCESS = 2;
	/**
	 * Closed (Sent/Received, billed or not)
	 */
	const STATUS_CLOSED = 3;


    /**
     *	Constructor
     *
     *  @param		DoliDB		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;

    }

    function fetch($object){

    	$object->fetch_thirdparty();

    	$ws_url      = $object->thirdparty->webservices_url;
    	$ws_key      = $object->thirdparty->webservices_key;
    	$ws_user     = $object->thirdparty->array_options['options_edi_user'];
    	$ws_password = $object->thirdparty->array_options['options_edi_pw'];

    	$ws_ns = 'http://www.dolibarr.org/ns/';
    	$ws_authentication = array(
    			'dolibarrkey'=>$ws_key,
    			'sourceapplication'=>'DolibarrWebServiceClient',
    			'login'=>$ws_user,
    			'password'=>$ws_password,
    			'entity'=>''
    	);

    	//Create SOAP client and connect it to user
    	$soapclient_user = new nusoap_client($ws_url."/webservices/server_user.php");
    	$soapclient_user->soap_defencoding='UTF-8';
    	$soapclient_user->decodeUTF8(false);

    	//Get the thirdparty associated to user
    	$ws_parameters = array('authentication'=>$ws_authentication, 'id' => '', 'ref'=>$ws_user);
    	$result_user = $soapclient_user->call("getUser", $ws_parameters, $ws_ns, '');
    	$user_status_code = $result_user["result"]["result_code"];

    	if ($user_status_code == "OK"){
    		$ws_entity = $result_user["user"]["entity"];
    		$ws_authentication['entity'] = $ws_entity;
    		$ws_thirdparty = $result_user["user"]["fk_thirdparty"];

    		$soapclient_order = new nusoap_client($ws_url."/webservices/server_order.php");
    		$soapclient_order->soap_defencoding='UTF-8';
    		$soapclient_order->decodeUTF8(false);
    		$ws_parameters = array('authentication'=>$ws_authentication,'idthirdparty'=>$ws_thirdparty);
    		$result_orders = $soapclient_order->call("getOrdersForThirdParty",$ws_parameters,$ws_ns,'');
    		$orders = $result_orders['orders'];
    		$cmd_found =array();
    		foreach ($orders as $order){
    			if($order['ref_client'] == $object->ref_supplier){
    				$this->cmd_found[] = $order;
    			}
    		}
			if(count($this->cmd_found) == 0){
				$this->msg = 'Pas de commande transmise';
			}else{
    			$this->msg='cmd found';
			}
    	}else{
    		$this->msg=$user_status_code;
    	}
    }

	function create($object){

		$object->fetch_thirdparty();

		$ws_url      = $object->thirdparty->webservices_url;
		$ws_key      = $object->thirdparty->webservices_key;
		$ws_user     = $object->thirdparty->array_options['options_edi_user'];
		$ws_password = $object->thirdparty->array_options['options_edi_pw'];

		$ws_ns = 'http://www.dolibarr.org/ns/';
		$ws_authentication = array(
				'dolibarrkey'=>$ws_key,
				'sourceapplication'=>'DolibarrWebServiceClient',
				'login'=>$ws_user,
				'password'=>$ws_password,
				'entity'=>''
		);

		//Create SOAP client and connect it to user
		$soapclient_user = new nusoap_client($ws_url."/webservices/server_user.php");
		$soapclient_user->soap_defencoding='UTF-8';
		$soapclient_user->decodeUTF8(false);

		//Get the thirdparty associated to user
		$ws_parameters = array('authentication'=>$ws_authentication, 'id' => '', 'ref'=>$ws_user);
		$result_user = $soapclient_user->call("getUser", $ws_parameters, $ws_ns, '');
		$user_status_code = $result_user["result"]["result_code"];

		if ($user_status_code == "OK"){
			$ws_entity = $result_user["user"]["entity"];
			$ws_authentication['entity'] = $ws_entity;
			$ws_thirdparty = $result_user["user"]["fk_thirdparty"];

			$soapclient_product = new nusoap_client($ws_url."/webservices/server_productorservice.php");
            $soapclient_product->soap_defencoding='UTF-8';
            $soapclient_product->decodeUTF8(false);
            $fournref = array();
            foreach ($object->lines as $line){
            	$ref_supplier = $line->ref;
            	if (empty($ref_supplier) || $line->type == 9) {
            		continue;
            	}
            	$ws_parameters = array('authentication' => $ws_authentication, 'id' => '', 'ref' => $ref_supplier);
                $result_product = $soapclient_product->call("getProductOrService", $ws_parameters, $ws_ns, '');
                $status_code = $result_product["result"]["result_code"];
                if($status_code =='OK'){
                	$fournref[] = array(
                			'desc'=>$line->desc,
                			'product_id'=>$result_product['product']['id'],
                			'qty'=>$line->qty,
                			'unitprice'=>$line->subprice);
                }else{
                	$this->msg.= 'Ref non valide:'. $line->ref . '</br>';
                	$this->error+=1;
                }

            }
            if(count($fournref) >0 && $this->error ==0){
            	$cmd_fourn =array(
            			'thirdparty_id'=>$ws_thirdparty,
            			'ref_ext' => $object->ref_supplier,
            			'date'=>dol_now(),
            			'note_public' => $object->note_public,
            			'arrayoflines'=>$fournref
            	);
            	$soapclient_order = new nusoap_client($ws_url."/webservices/server_order.php");
            	$soapclient_order->soap_defencoding='UTF-8';
            	$soapclient_order->decodeUTF8(false);
            	$ws_parameters = array('authentication'=>$ws_authentication,'order'=>$cmd_fourn);
            	$result_orders = $soapclient_order->call("createOrder",$ws_parameters,$ws_ns,'');
            	$this->msg = $result_orders['result_code'];
            }else{
            	$this->msg.='no product match';
            }

		}else{
			$this->msg=$user_status_code;
		}

	}


    /**
     *	Return status label of Order
     *
     *	@param      int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *	@return     string      		Libelle
     */
    function getLibStatut($mode,$statut)
    {
        if ($this->facturee && empty($this->billed)) $this->billed=$this->facturee; // For backward compatibility
        return $this->LibStatut($statut,$this->billed,$mode);
    }

    /**
     *	Return label of status
     *
     *	@param		int		$statut      	  Id statut
     *  @param      int		$billed    		  If invoiced
     *	@param      int		$mode        	  0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *  @param      int     $donotshowbilled  Do not show billed status after order status
     *  @return     string					  Label of status
     */
    function LibStatut($statut,$billed,$mode,$donotshowbilled=0)
    {
        global $langs, $conf;

        $billedtext = '';
        if (empty($donotshowbilled)) $billedtext .= ($billed?' - '.$langs->trans("Billed"):'');

        //print 'x'.$statut.'-'.$billed;
        if ($mode == 0)
        {
            if ($statut==self::STATUS_CANCELED) return $langs->trans('StatusOrderCanceled');
            if ($statut==self::STATUS_DRAFT) return $langs->trans('StatusOrderDraft');
            if ($statut==self::STATUS_VALIDATED) return $langs->trans('StatusOrderValidated').$billedtext;
            if ($statut==self::STATUS_ACCEPTED) return $langs->trans('StatusOrderSentShort').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! $billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBill');
            if ($statut==self::STATUS_CLOSED && ($billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderDelivered');
        }
        elseif ($mode == 1)
        {
            if ($statut==self::STATUS_CANCELED) return $langs->trans('StatusOrderCanceledShort');
            if ($statut==self::STATUS_DRAFT) return $langs->trans('StatusOrderDraftShort');
            if ($statut==self::STATUS_VALIDATED) return $langs->trans('StatusOrderValidatedShort').$billedtext;
            if ($statut==self::STATUS_ACCEPTED) return $langs->trans('StatusOrderSentShort').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! $billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderToBillShort');
            if ($statut==self::STATUS_CLOSED && ($billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderProcessed').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return $langs->trans('StatusOrderDelivered');
        }
        elseif ($mode == 2)
        {
            if ($statut==self::STATUS_CANCELED) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceledShort');
            if ($statut==self::STATUS_DRAFT) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraftShort');
            if ($statut==self::STATUS_VALIDATED) return img_picto($langs->trans('StatusOrderValidated'),'statut1').' '.$langs->trans('StatusOrderValidatedShort').$billedtext;
            if ($statut==self::STATUS_ACCEPTED) return img_picto($langs->trans('StatusOrderSent'),'statut3').' '.$langs->trans('StatusOrderSentShort').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! $billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBillShort');
            if ($statut==self::STATUS_CLOSED && ($billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed').$billedtext,'statut6').' '.$langs->trans('StatusOrderProcessed').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderDelivered'),'statut6').' '.$langs->trans('StatusOrderDeliveredShort');
        }
        elseif ($mode == 3)
        {
            if ($statut==self::STATUS_CANCELED) return img_picto($langs->trans('StatusOrderCanceled'),'statut5');
            if ($statut==self::STATUS_DRAFT) return img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==self::STATUS_VALIDATED) return img_picto($langs->trans('StatusOrderValidated').$billedtext,'statut1');
            if ($statut==self::STATUS_ACCEPTED) return img_picto($langs->trans('StatusOrderSentShort').$billedtext,'statut3');
            if ($statut==self::STATUS_CLOSED && (! $billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7');
            if ($statut==self::STATUS_CLOSED && ($billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessed').$billedtext,'statut6');
            if ($statut==self::STATUS_CLOSED && (! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderDelivered'),'statut6');
        }
        elseif ($mode == 4)
        {
            if ($statut==self::STATUS_CANCELED) return img_picto($langs->trans('StatusOrderCanceled'),'statut5').' '.$langs->trans('StatusOrderCanceled');
            if ($statut==self::STATUS_DRAFT) return img_picto($langs->trans('StatusOrderDraft'),'statut0').' '.$langs->trans('StatusOrderDraft');
            if ($statut==self::STATUS_VALIDATED) return img_picto($langs->trans('StatusOrderValidated').$billedtext,'statut1').' '.$langs->trans('StatusOrderValidated').$billedtext;
            if ($statut==self::STATUS_ACCEPTED) return img_picto($langs->trans('StatusOrderSentShort').$billedtext,'statut3').' '.$langs->trans('StatusOrderSent').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! $billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderToBill'),'statut7').' '.$langs->trans('StatusOrderToBill');
            if ($statut==self::STATUS_CLOSED && ($billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderProcessedShort').$billedtext,'statut6').' '.$langs->trans('StatusOrderProcessed').$billedtext;
            if ($statut==self::STATUS_CLOSED && (! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return img_picto($langs->trans('StatusOrderDelivered'),'statut6').' '.$langs->trans('StatusOrderDelivered');
        }
        elseif ($mode == 5)
        {
            if ($statut==self::STATUS_CANCELED) return '<span class="hideonsmartphone">'.$langs->trans('StatusOrderCanceledShort').' </span>'.img_picto($langs->trans('StatusOrderCanceled'),'statut5');
            if ($statut==self::STATUS_DRAFT) return '<span class="hideonsmartphone">'.$langs->trans('StatusOrderDraftShort').' </span>'.img_picto($langs->trans('StatusOrderDraft'),'statut0');
            if ($statut==self::STATUS_VALIDATED) return '<span class="hideonsmartphone">'.$langs->trans('StatusOrderValidatedShort').$billedtext.' </span>'.img_picto($langs->trans('StatusOrderValidated').$billedtext,'statut1');
            if ($statut==self::STATUS_ACCEPTED) return '<span class="hideonsmartphone">'.$langs->trans('StatusOrderSentShort').$billedtext.' </span>'.img_picto($langs->trans('StatusOrderSent').$billedtext,'statut3');
            if ($statut==self::STATUS_CLOSED && (! $billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return '<span class="hideonsmartphone">'.$langs->trans('StatusOrderToBillShort').' </span>'.img_picto($langs->trans('StatusOrderToBill'),'statut7');
            if ($statut==self::STATUS_CLOSED && ($billed && empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return '<span class="hideonsmartphone">'.$langs->trans('StatusOrderProcessedShort').$billedtext.' </span>'.img_picto($langs->trans('StatusOrderProcessed').$billedtext,'statut6');
            if ($statut==self::STATUS_CLOSED && (! empty($conf->global->WORKFLOW_BILL_ON_SHIPMENT))) return '<span class="hideonsmartphone">'.$langs->trans('StatusOrderDeliveredShort').' </span>'.img_picto($langs->trans('StatusOrderDelivered'),'statut6');
        }
    }

}

