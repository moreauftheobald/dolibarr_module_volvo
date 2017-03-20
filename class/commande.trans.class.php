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

    function fetch($ref,$object){
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
    		$msg=$user_status_code;
    	}else{
    		$msg=$user_status_code;
    	}

    	return $user_status_code;

    }


    /**
     * 	Cancel an order
     * 	If stock is decremented on order validation, we must reincrement it
     *
     *	@param	int		$idwarehouse	Id warehouse to use for stock change.
     *	@return	int						<0 if KO, >0 if OK
     */
	function create($mode, $object){

	if ($mode != "send" && ! GETPOST('cancel')){
		$ws_url      = $object->thirdparty->webservices_url;
		$ws_key      = $object->thirdparty->webservices_key;
		$ws_user     = GETPOST('ws_user','alpha');
		$ws_password = GETPOST('ws_password','alpha');

        // NS and Authentication parameters
        $ws_ns = 'http://www.dolibarr.org/ns/';
        $ws_authentication = array(
            'dolibarrkey'=>$ws_key,
            'sourceapplication'=>'DolibarrWebServiceClient',
            'login'=>$ws_user,
            'password'=>$ws_password,
            'entity'=>''
        );

        print load_fiche_titre($langs->trans('CreateRemoteOrder'),'');

        //Is everything filled?
        if (empty($ws_url) || empty($ws_key)) {
            setEventMessages($langs->trans("ErrorWebServicesFieldsRequired"), null, 'errors');
            $mode = "init";
            $error_occurred = true; //Don't allow to set the user/pass if thirdparty fields are not filled
        } else if ($mode != "init" && (empty($ws_user) || empty($ws_password))) {
            setEventMessages($langs->trans("ErrorFieldsRequired"), null, 'errors');
            $mode = "init";
        }

        if ($mode == "init")
        {
            //Table/form header
            print '<table class="border" width="100%">';
            print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="action" value="webservice">';
            print '<input type="hidden" name="mode" value="check">';

            if ($error_occurred)
            {
                print "<br>".$langs->trans("ErrorOccurredReviseAndRetry")."<br>";
                print '<input class="button" type="submit" id="cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
            }
            else
            {
                $textinput_size = "50";
                // Webservice url
                print '<tr><td>'.$langs->trans("WebServiceURL").'</td><td colspan="3">'.dol_print_url($ws_url).'</td></tr>';
                //Remote User
                print '<tr><td>'.$langs->trans("User").'</td><td><input size="'.$textinput_size.'" type="text" name="ws_user"></td></tr>';
                //Remote Password
                print '<tr><td>'.$langs->trans("Password").'</td><td><input size="'.$textinput_size.'" type="text" name="ws_password"></td></tr>';
                //Submit button
                print '<tr><td align="center" colspan="2">';
                print '<input class="button" type="submit" id="ws_submit" name="ws_submit" value="'.$langs->trans("CreateRemoteOrder").'">';
                print ' &nbsp; &nbsp; ';
                //Cancel button
                print '<input class="button" type="submit" id="cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
                print '</td></tr>';
            }

            //End table/form
            print '</form>';
            print '</table>';
        }
        elseif ($mode == "check")
        {
            $ws_entity = '';
            $ws_thirdparty = '';
            $error_occurred = false;

            //Create SOAP client and connect it to user
            $soapclient_user = new nusoap_client($ws_url."/webservices/server_user.php");
            $soapclient_user->soap_defencoding='UTF-8';
            $soapclient_user->decodeUTF8(false);

            //Get the thirdparty associated to user
            $ws_parameters = array('authentication'=>$ws_authentication, 'id' => '', 'ref'=>$ws_user);
            $result_user = $soapclient_user->call("getUser", $ws_parameters, $ws_ns, '');
            $user_status_code = $result_user["result"]["result_code"];

            if ($user_status_code == "OK")
            {
                //Fill the variables
                $ws_entity = $result_user["user"]["entity"];
                $ws_authentication['entity'] = $ws_entity;
                $ws_thirdparty = $result_user["user"]["fk_thirdparty"];
                if (empty($ws_thirdparty))
                {
                    setEventMessages($langs->trans("RemoteUserMissingAssociatedSoc"), null, 'errors');
                    $error_occurred = true;
                }
                else
                {
                    //Create SOAP client and connect it to product/service
                    $soapclient_product = new nusoap_client($ws_url."/webservices/server_productorservice.php");
                    $soapclient_product->soap_defencoding='UTF-8';
                    $soapclient_product->decodeUTF8(false);

                    // Iterate each line and get the reference that uses the supplier of that product/service
                    $i = 0;
                    foreach ($object->lines as $line) {
                        $i = $i + 1;
                        $ref_supplier = $line->ref_supplier;
                        $line_id = $i."º) ".$line->product_ref.": ";
                        if (empty($ref_supplier)) {
                            continue;
                        }
                        $ws_parameters = array('authentication' => $ws_authentication, 'id' => '', 'ref' => $ref_supplier);
                        $result_product = $soapclient_product->call("getProductOrService", $ws_parameters, $ws_ns, '');
                        if (!$result_product)
                        {
                            setEventMessages($line_id.$langs->trans("SOAPError")." ".$soapclient_product->error_str." - ".$soapclient_product->response, null, 'errors');
                            $error_occurred = true;
                            break;
                        }

                        // Check the result code
                        $status_code = $result_product["result"]["result_code"];
                        if (empty($status_code)) //No result, check error str
                        {
                            setEventMessages($langs->trans("SOAPError")." '".$soapclient_order->error_str."'", null, 'errors');
                        }
                        else if ($status_code != "OK") //Something went wrong
                        {
                            if ($status_code == "NOT_FOUND")
                            {
                                setEventMessages($line_id.$langs->trans("SupplierMissingRef")." '".$ref_supplier."'", null, 'warnings');
                            }
                            else
                            {
                                setEventMessages($line_id.$langs->trans("ResponseNonOK")." '".$status_code."' - '".$result_product["result"]["result_label"]."'", null, 'errors');
                                $error_occurred = true;
                                break;
                            }
                        }


                        // Ensure that price is equal and warn user if it's not
                        $supplier_price = price($result_product["product"]["price_net"]); //Price of client tab in supplier dolibarr
                        $local_price = NULL; //Price of supplier as stated in product suppliers tab on this dolibarr, NULL if not found

                        $product_fourn = new ProductFournisseur($db);
                        $product_fourn_list = $product_fourn->list_product_fournisseur_price($line->fk_product);
                        if (count($product_fourn_list)>0)
                        {
                            foreach($product_fourn_list as $product_fourn_line)
                            {
                                //Only accept the line where the supplier is the same at this order and has the same ref
                                if ($product_fourn_line->fourn_id == $object->socid && $product_fourn_line->fourn_ref == $ref_supplier) {
                                    $local_price = price($product_fourn_line->fourn_price);
                                }
                            }
                        }

                        if ($local_price != NULL && $local_price != $supplier_price) {
                            setEventMessages($line_id.$langs->trans("RemotePriceMismatch")." ".$supplier_price." - ".$local_price, null, 'warnings');
                        }

                        // Check if is in sale
                        if (empty($result_product["product"]["status_tosell"])) {
                            setEventMessages($line_id.$langs->trans("ProductStatusNotOnSellShort")." '".$ref_supplier."'", null, 'warnings');
                        }
                    }
                }

            }
            elseif ($user_status_code == "PERMISSION_DENIED")
            {
                setEventMessages($langs->trans("RemoteUserNotPermission"), null, 'errors');
                $error_occurred = true;
            }
            elseif ($user_status_code == "BAD_CREDENTIALS")
            {
                setEventMessages($langs->trans("RemoteUserBadCredentials"), null, 'errors');
                $error_occurred = true;
            }
            else
            {
                setEventMessages($langs->trans("ResponseNonOK")." '".$user_status_code."'", null, 'errors');
                $error_occurred = true;
            }

            //Form
            print '<form action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.'" method="post">';
            print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="action" value="webservice">';
            print '<input type="hidden" name="mode" value="send">';
            print '<input type="hidden" name="ws_user" value="'.$ws_user.'">';
            print '<input type="hidden" name="ws_password" value="'.$ws_password.'">';
            print '<input type="hidden" name="ws_entity" value="'.$ws_entity.'">';
            print '<input type="hidden" name="ws_thirdparty" value="'.$ws_thirdparty.'">';
            if ($error_occurred)
            {
                print "<br>".$langs->trans("ErrorOccurredReviseAndRetry")."<br>";
            }
            else
            {
                print '<input class="button" type="submit" id="ws_submit" name="ws_submit" value="'.$langs->trans("Confirm").'">';
                print ' &nbsp; &nbsp; ';
            }
            print '<input class="button" type="submit" id="cancel" name="cancel" value="'.$langs->trans("Cancel").'">';
            print '</form>';
        }
	}

	}

    /**
     *	Create order
     *	Note that this->ref can be set or empty. If empty, we will use "(PROV)"
     *
     *	@param		User	$user 		Objet user that make creation
     *	@param		int	$notrigger	Disable all triggers
     *	@return 	int			<0 if KO, >0 if OK
     */

    /**
     *	Return status label of Order
     *
     *	@param      int		$mode       0=libelle long, 1=libelle court, 2=Picto + Libelle court, 3=Picto, 4=Picto + Libelle long, 5=Libelle court + Picto
     *	@return     string      		Libelle
     */
    function getLibStatut($mode)
    {
        if ($this->facturee && empty($this->billed)) $this->billed=$this->facturee; // For backward compatibility
        return $this->LibStatut($this->statut,$this->billed,$mode);
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

