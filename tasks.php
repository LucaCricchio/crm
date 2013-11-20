<?php

/**
 * Description of tasks
 *
 * @author francesco
 */
class Tasks {
    
    private $task;
    
    public function __construct($task) {
        $this->task = $task;
    }
    
    
    public function execute() {
        if(method_exists($this, $this->task)) 
            return call_user_func(array($this, $this->task));
        else
            return false;
    }
    
    
    
    function authenticate_user()
    {
        $db = Helper::getDb();
        
        $data = Helper::getData();
        
	$username = $db->quote($data->username);
	$password = $db->quote($data->password);

	//per adesso uso request per i test
	if(isset($_REQUEST["username"]) AND isset($_REQUEST["password"]))
	{$username=$_REQUEST["username"];
	$password=$_REQUEST["password"];
	}
	
	
	$current_date = $db->quote(date("Y-m-d"));
	
	
	$sql = "SELECT id, user_name, user_password,last_login_time, support_start_date, support_end_date
		FROM vtiger_portalinfo
		INNER JOIN vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
		INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_portalinfo.id
		WHERE vtiger_crmentity.deleted=0 
                        AND user_name= $username
                        AND user_password = $password
			AND isactive=1
                        AND vtiger_customerdetails.portal=1
			AND vtiger_customerdetails.support_start_date <= $current_date 
                        AND vtiger_customerdetails.support_end_date >= $current_date";
        $db->setQuery($sql);
        $result = $db->loadObject();

	$errors = $db->getErrors();
        if(!empty($errors))
            var_dump($errors);
        
        
        
        if($db->affectedRows() == 0)
            return Helper::$LOGIN_ERROR;
        else {
            $user_data = new stdClass();
            $user_data->customer_id = $result->id;
            Helper::setUserData($user_data);
            
            return Helper::$LOGIN_OK;
        }
    }
    


/** function used to get the Contact name
 *  @param int $id -Contact id
 * return string $message -Contact name returned
 */
function get_contact_name()
{

	$db = Helper::getDb();
	
	
	$sessiondata = Helper::getUserData();
        
	$contactid = $sessiondata->customer_id;
	
	//$contactid = $db->quote($_REQUEST['contactid']);
	
	$contact_name = '';
	if($contactid != '')
	{
		$sql = "select firstname,lastname from vtiger_contactdetails where contactid = $contactid";	
	}
	
	
	
	$db->setQuery($sql);
        if(!$db->execute()) {
            $errors = $db->getErrors();
            var_dump($errors);
        }
       
		
        if($db->affectedRows() == 0)
            return Helper::$LOGIN_ERROR;
        else
		{
		 //$result = $db->loadResult();
		  $result = $db->loadObject();
		  $firstname = $result->firstname;
		  $lastname = $result->lastname;
		/*$firstname = $result['firstname'];
		$lastname = $result['lastname'];*/
		$contact_name = $firstname." ".$lastname;
		

        return $contact_name;
		}
           // return Helper::$LOGIN_OK;
}






/* Function to get contactid's and account's product details'
 *
 */
function get_product_list_values()
	{
	$db = Helper::getDb();
	
	//$id = $db->quote($_REQUEST['id']);
	$contactid=2;
	$modulename="Products";
	$only_mine='true';
	
	
	
	
	$userid = $contactid; //serve?
	
	
	$entity_ids_list = array();
	



	
	
		$contactquery = "SELECT contactid, accountid FROM vtiger_contactdetails " .
		" INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid" .
		" AND vtiger_crmentity.deleted = 0 " .
		" WHERE (accountid = (SELECT accountid FROM vtiger_contactdetails WHERE contactid = $contactid)  AND accountid != 0) OR contactid = $contactid";
		
		
		
		$db->setQuery($contactquery);
        if(!$db->execute()) {
            $errors = $db->getErrors();
            var_dump($errors);
        }
       
	
		
		
        if($db->affectedRows() == 0)
            return Helper::$LOGIN_ERROR;
   else
	{	 //$result = $db->loadResult();
		  $contactres = $db->loadObject();
		

		
		
		//to continue...
		
		$no_of_cont = $db->affectedRows();
		for($i=0;$i<$no_of_cont;$i++)
		{
			$cont_id = $contactres->contactid;
			$acc_id = $contactres->accountid;
			if(!in_array($cont_id, $entity_ids_list))
			$entity_ids_list[] = $cont_id;
			if(!in_array($acc_id, $entity_ids_list) && $acc_id != '0')
			$entity_ids_list[] = $acc_id;
		}

	/*
	$focus = new Products();
	$focus->filterInactiveFields('Products');
	foreach ($focus->list_fields as $fieldlabel => $values){
		foreach($values as $table => $fieldname){
			$fields_list[$fieldlabel] = $fieldname;
		}
	}
	*/
	
	
	
	
	//DA RIMUOVERE
	
	// Return Question mark
				function _questionify($v){
						return "?";
					}
	
	
	// array_map will call the function specified in the first parameter for every element of the list in second parameter
	if (is_array($entity_ids_list)) {
		$get_entity_ids_list = implode(",", array_map("_questionify", $entity_ids_list));
	} else {
		$get_entity_ids_list = implode(",", array_map("_questionify", explode(",", $entity_ids_list)));
	}
}


	//FINO A QUI
//Da sostituire $get_entity_ids_list
	
	$fields_list['Related To'] = 'entityid';
	$query = array();
	$params = array();
	$query[] = "SELECT vtiger_products.*,vtiger_seproductsrel.crmid as entityid, vtiger_seproductsrel.setype FROM vtiger_products
		INNER JOIN vtiger_crmentity on vtiger_products.productid = vtiger_crmentity.crmid
		LEFT JOIN vtiger_seproductsrel on vtiger_seproductsrel.productid = vtiger_products.productid
		WHERE vtiger_seproductsrel.crmid in (".$get_entity_ids_list.") and vtiger_crmentity.deleted = 0 ";
	$params[] = array($entity_ids_list);

	

	
	//to continue
	
	$db->setQuery($query);
        if(!$db->execute()) {
            $errors = $db->getErrors();
            var_dump($errors);
        }
       

		
		
        if($db->affectedRows() == 0)
            return Helper::$LOGIN_ERROR;
   else
	{	 //$result = $db->loadResult();
		 $query = $db->loadObject();
	
	
	
	for($k=0;$k<$db->affectedRows();$k++)
	{
		//$res[$k] = $adb->pquery($query[$k],$params[$k]);
		$noofdata[$k] = $adb->num_rows($res[$k]);
		if($noofdata[$k] == 0)
		$output[$k][$modulename]['data'] = '';
		for( $j= 0;$j < $noofdata[$k]; $j++)
		{
			$i=0;
			foreach($fields_list as $fieldlabel=> $fieldname) {
				$fieldper = getFieldVisibilityPermission('Products',$current_user->id,$fieldname);
				if($fieldper == '1' && $fieldname != 'entityid'){
					continue;
				}
				$output[$k][$modulename]['head'][0][$i]['fielddata'] = $fieldlabel;
				$fieldvalue = $adb->query_result($res[$k],$j,$fieldname);
				$fieldid = $adb->query_result($res[$k],$j,'productid');

				if($fieldname == 'entityid') {
					$crmid = $fieldvalue;
					$module = $adb->query_result($res[$k],$j,'setype');
					if ($crmid != '' && $module != '') {
						$fieldvalues = getEntityName($module, array($crmid));
						if($module == 'Contacts')
						$fieldvalue = '<a href="index.php?module=Contacts&action=index&id='.$crmid.'">'.$fieldvalues[$crmid].'</a>';
						elseif($module == 'Accounts')
						$fieldvalue = '<a href="index.php?module=Accounts&action=index&id='.$crmid.'">'.$fieldvalues[$crmid].'</a>';
					} else {
						$fieldvalue = '';
					}
				}

				if($fieldname == 'productname')
				$fieldvalue = '<a href="index.php?module=Products&action=index&productid='.$fieldid.'">'.$fieldvalue.'</a>';

				if($fieldname == 'unit_price'){
					$sym = getCurrencySymbol($res[$k],$j,'currency_id');
					$fieldvalue = $sym.$fieldvalue;
				}
				$output[$k][$modulename]['data'][$j][$i]['fielddata'] = $fieldvalue;
				$i++;
			}
		}
	}
	$log->debug("Exiting function get_product_list_values.....");
	
	return $output;
	
	}
	
}





/* Function to get contactid's and account's product details'
 *
 */
	function get_simplified_product_list_values()
	{
	$db = Helper::getDb();
    
	$sessiondata = Helper::getUserData();
        
	$contactid = $sessiondata->customer_id;
	
	
	
	//per ora non servono
	
	//$modulename="Products";
	//$only_mine='true';
	
	
	$query = "SELECT vtiger_products.*,vtiger_seproductsrel.crmid as entityid, vtiger_seproductsrel.setype FROM vtiger_products
		INNER JOIN vtiger_crmentity on vtiger_products.productid = vtiger_crmentity.crmid
		LEFT JOIN vtiger_seproductsrel on vtiger_seproductsrel.productid = vtiger_products.productid
		WHERE vtiger_seproductsrel.crmid = $contactid and vtiger_crmentity.deleted = 0 ";
	

	
	
	//to continue
	
	$db->setQuery($query);
    $result = $db->loadObjectList();
	

	
			foreach($result as $element)
			{
				$products [] = (object) array(
								"productid" => $element->productid,
								"product_no" => $element->product_no,
								"productname" => $element->productname,
								);
			
			}

			
	
	$cacca= json_encode($products);
	$caccadec= json_decode($cacca);
	return var_dump($caccadec);
	//return $contactid;
	}
	
	
	
	
	
	
		/*function used to get details of tickets,quotes,documents,Products,Contacts,Accounts
 *	@param int $id - id of quotes or invoice or notes
 *	return string $message - Account informations will be returned from :Accountdetails table
 *  Cose utili: - vtiger_field.tabid (id modulo)
 */
	function get_simplified_details($id, $module)
	{
	
	$db = Helper::getDb();
	
	
	
	//PER ORA SOLO CON I PRODOTTI PER SEMPLIFICAREEE
	$id = 3;
	$module = 'Products';
	
	
	//In teoria non serve più
	
	$sql = "select tabid from vtiger_tab where name='$module'";
	 $db->setQuery($sql);
        $res = $db->loadObject();
		
	//id modulo dalla tabella vtiger_tab	
	$tabid  = $res->tabid;
	echo $tabid;
	echo "</br></br>";
	
	
	
		$query = "SELECT vtiger_products.*,vtiger_productcf.*,vtiger_crmentity.* " .
		"FROM vtiger_products " .
		"INNER JOIN vtiger_crmentity " .
			"ON vtiger_crmentity.crmid = vtiger_products.productid " .
		"LEFT JOIN vtiger_productcf " .
			"ON vtiger_productcf.productid = vtiger_products.productid " .
		"LEFT JOIN vtiger_vendor
			ON vtiger_vendor.vendorid = vtiger_products.vendor_id " .
		"WHERE vtiger_products.productid = $id AND vtiger_crmentity.deleted = 0";

	//$params = array($id);
	 $db->setQuery($query);
        $res = $db->loadObjectList();
	
	
	
	return json_encode($res);
	}
	

	function get_simplified_product_details()
	{
	
	$data = Helper::getData();
        
	$id = $data->id;
	
	$this::get_simplified_details($id, 'Products');
	}
	
    

// FINE CLASSE
}
