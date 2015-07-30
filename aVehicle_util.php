<?php
//================================================================================================
//  Global Communication Services
//  www.gcsiweb.com
//  Telephone: 607-786-6205
//  Toll Free: 866-463-6292  
//  Fax:       607-786-1231
//  e-Mail:    info@gcsiweb.com
//  Utility File to support Saleforce Indiana Auto Auction Vehicle routines.
//  Author:  Jeff Howard  jeff.howard2@yahoo.com
//  Create Date:  May 2010
//================================================================================================
	
//================================================================================================
// Master Query routine for vehicle	
//================================================================================================

function queryVehicle($sfConn, $sfOwnerID)
	{
	echo("<P> queryVehicle <P>");
	$sObjects = array();
	$queryFields = "Id,
		Name,
		Dealer_Name__c,
		Rep_Name__c,
		Rep_Status__c,
		Rep_Auction_Access_Number__c
		 ";
	$querySource = "Rep_ID__c";
	$queryMatch = "Owner__c = '$sfOwnerID'";
		
	echo("<P> Query SF from queryvehicle: " . $queryFields . " Source: " . $querySource . " Match: " . $queryMatch . " ");
	$queries = 0;
	$successful = query_first($sfConn, $queryFields, $querySource, $queryMatch);
	$queryResults = $successful[0];
	$done = $queryResults->done;
	$queryLocator = $queryResults->queryLocator;
	if ($successful)
		{
		$bContinue = TRUE;
		While ($bContinue)
			{
			$attResult = vehicle_setup1($queryResults);
			if (!$queryResults->done)
				{
				echo("<P> Query next, Queries so far: " . $queries . "  ");
				$success = query_more($sfConn, $queryLocator);
				$queryResults = $success[0];
				$done = $queryResults->done;
				$queryLocator = $queryResults->queryLocator;
				$queries++;
				if (!$success)
					{
					$bContinue = FALSE;
					}
				}
				else
				{
				$bContinue = FALSE;
				}     		// end if (!$queryResults->done)
			}				// end While ($bContinue)
		}					// end if ($successful)
	return  $success;  
	}         				// end function queryVehicle($sfConn, $sfOwnerID)
//================================================================================================
	
//================================================================================================
// vehicle Setup	- First
//================================================================================================

function vehicle_setup1($queryResults)
	{
	echo("<P> vehicle_setup  ");

//--------start of first while loop:----------------------------------------------------------------------------- 
	$sqlRecs = 0;
	
//--------repid input----------------------------------------------------------------------------------
	echo( "<P>iaa  - script 2 - query table , prepare for upload to SF: " );
	$querysftmp1 = 'SELECT * FROM iaavehicle_sftmp1';
	$resultsql = mysql_query($querysftmp1); 
	 echo("<P> Num rows: " . mysql_num_rows($resultsql) . " ");
	if (!$resultsql) 
		{
		echo("<P>Error performing query from  iaa vehicle table: " .  mysql_error() . " ");    
		exit();  
		}				// end if (!$resultsql)
	else
		{
		if (mysql_num_rows($resultsql) != 0)
			  {
			  echo( "<P>  iaa vehicle file has data, continue run" . " ");
			  } 
			else
			  {
			  echo( "<P>  iaa vehicle file has no data, exiting run" . " ");
			  exit();
			  }
		}				//  end else
	echo( "<P>iaa vehicle table has data" . " coninue processing of SF upload ");
	$l = 0;
	while ($row = mysql_fetch_array($resultsql))
		{
//----------set sql cells to variable and display-----------------------------------------------------------------
//			echo("<P> Looping l: $l ");
//		echo(" Next: ");
		$l++;
		$id = "";
		$idlit = "";
		$sfrepid = "";
		  
		$deaown = $row[iaa_deaown];	
		$slsdt = $row[iaa_slsdt];	
		$vin = $row[iaa_vin];	
		$lane = $row[iaa_lane];	
		$lotnum = $row[iaa_lotnum];	
		$slsstat = $row[iaa_slsstat];	
		$sellertype = $row[iaa_sellertype];	
		$miles = $row[iaa_miles];	
		$slsamt = $row[iaa_slsamt];	
		$deaseller = $row[iaa_deaseller];	
		$deabuyer = $row[iaa_deabuyer];	
		$repbuyerid = $row[iaa_repbuyerid];	
		$year = $row[iaa_year];
		$make = $row[iaa_make];	
		$model = $row[iaa_model];	
		$color = $row[iaa_color];	
		$condslsflg = $row[iaa_condslsflg];
		$onlineslsflg = $row[iaa_onlineslsflg];
		$deleted = $row[iaa_delete];
			
		if (FALSE)
		{
		echo("<P> deaown = " . $deaown  . 	
		" slsdt = " . $slsdt .	
		" vin = " . $vin .	
		" lane = " . $lane .	
		" lotnum = " . $lotnum .	
		" slsstat = " . $slsstat .	
		" sellertype = " . $sellertype .	
		" miles = " . $miles .	
		" slsamt = " . $slsamt .	
		" deaseller = " . $deaseller .	
		" deabuyer = " . $deabuyer .	
		" repbuyerid = " . $repbuyerid .	
		" year = " . $year .
		" make = " . $make .	
		" model = " . $model .	
		" color = " . $color .	
		" condslsflg = " . $condslsflg .
		" onlineslsflg = " . $onlineslsflg .
		"<P>");
		}						// end if (FALSE)
// Determine whether contact record exists for this buyer
		$m = 0;		
		if (!($deleted == 'TRUE'))
			{   
			foreach ($queryResults->records as $nextResult)
				{
				$m++;
				$nextName = $nextResult->fields->Name;
//					echo("<P> next name  $nextName rep buyerID  $repbuyerid ID  $id ");
				$id = $nextResult->Id;
				if (($nextName == $repbuyerid) AND (!empty($nextName)) )
					{
					echo ("<P> !! Seller MATCH found in Rep buyer: !! " . $repbuyerid . " SF Rep buyer: " . $nextName . " ");												
//					echo(" !!!!!!!!!!!!!!!!!  MATCH  MATCH  MATCH  MATCH  MATCH  MATCH !!!!!!!!!!!!!!!!!!!!! ");
//						echo("<P> !!! FIELDS !!!   " .
//								" Owner ID: " . $deaown . 
//								" DealSell: " . $deaseller .
//								" DealBuy: " . $deabuyer .
//								" RepBuyer: " . $repbuyerid .
//								" ID: " . $id .
//								" ");		 
//	        	        echo("<P>sf record exists for this query to rep_id__c;" .  
//					    " SF ID = " . $id . 
//				    	" SF repid = " . $nextResult->fields->Name . 
//					    " SF dealer name = " . $nextResult->fields->Dealer_Name__c .
//				    	" SF rep name = " . $nextResult->fields->Rep_Name__c .
//					    " SF status = " . $nextResult->fields->Rep_Status__c .
//					    " SF rep auc acc num = " . $nextResult->fields->Rep_Auction_Access_Number__c .
 //       		      " </P>");			   
//----------insert processed rows into next tmp table---------------------------------------------------------
					$query = "REPLACE INTO iaavehicle_sftmp2
						(iaa_deaown ,	
						 iaa_slsdt ,	
						 iaa_vin ,	
						 iaa_lane ,	
						 iaa_lotnum ,	
						 iaa_slsstat ,	
						 iaa_sellertype ,	
						 iaa_miles ,	
						 iaa_slsamt ,	
						 iaa_deaseller ,	
						 iaa_deabuyer ,	
						 iaa_repbuyerid ,	
						 iaa_year ,
						 iaa_make ,	
						 iaa_model ,	
						 iaa_color ,	
						 iaa_condslsflg ,	
						 iaa_onlineslsflg)
						VALUES
						('".$deaown."', 	
						 '".$slsdt."',	
						 '".$vin."',	
						 '".$lane."',	
						 '".$lotnum."',	
						 '".$slsstat."',	
						 '".$sellertype."', 
						 '".$miles."',	
						 '".$slsamt."',	
						 '".$deaseller."',	
						 '".$deabuyer."',	
						 '".$id."',	
						 '".$year."',
						 '".$make."',	
						 '".$model."',	
						 '".$color."',	
						 '".$condslsflg."',
						 '".$onlineslsflg."')";
					$result = mysql_query($query); 
					if (!$result) 
					  {
					  echo("<P>Error creating iaa vehicle sq2 sf tmp1 table " .  mysql_error() . " ");    
					  exit();  
					  }
//				echo( "<P>iaa vehicle sf tmp2 sql table created</P>");   
					$querySQL = "UPDATE iaavehicle_sftmp1 
								SET iaa_delete = 'TRUE'
								WHERE ((iaa_vin = '$vin') AND (iaa_slsdt = '$slsdt'))   ";
					$resultdel = mysql_query($querySQL); 
					if (!$resultdel) 
						{
					  echo("<P> Error deleting from iaa vehicle create sql table " .  mysql_error() . " ");    
					  exit();  
					  }
					}			// end if (($nextName == $repbuyerid) AND (!empty($nextName)) )
				}					// end foreach ($queryResults->records as $nextResult)
			}	  					// end if (!($deleted == 'TRUE')) 
		else
			{	
			echo(" Another deleted:  ");
			}
		}							// end while ($row = mysql_fetch_array($resultsql))
      return  $success;  
   }         						// end function vehicle_setup1($queryResults)
 //================================================================================================
	
//================================================================================================
// Second Query routine for vehicle	
//================================================================================================

      function queryvehicle2($sfConn, $sfOwnerID){
	echo("<P> queryvehicle2 <P>");
        $sObjects = array();
		$queryFields =  " Id, 
			  OwnerId,
			  Name,
			  Dealer_Number__c ";
		$querySource = "Account";
		$queryMatch = "OwnerID = '$sfOwnerID' AND Dealer_Number__c != ''";
		echo("<P> Query SF from queryvehicle2: " . $queryFields . " Source: " . $querySource . " Match: " . $queryMatch . " ");
		$queries = 0;
        $successful = query_first($sfConn, $queryFields, $querySource, $queryMatch);
        $queryResults = $successful[0];
		$done = $queryResults->done;
		$queryLocator = $queryResults->queryLocator;
		if ($successful)
			{
			$bContinue = TRUE;
			While ($bContinue)
				{
				$attResult = vehicle_setup2($queryResults);
				if (!$done)
					{
					echo("<P> Query next, Queries so far: " . $queries . " ");
					$success = query_more($sfConn, $queryLocator);
					$queryResults = $success[0];
					$done = $queryResults->done;
					$queryLocator = $queryResults->queryLocator;
					$queries++;
					if (!$success)
						{
						$bContinue = FALSE;
						}
					}				// end if (!$done)
					else
					{
					$bContinue = FALSE;
					}				// end else
				}					// end While ($bContinue)
			}						// end if ($successful)
        return  $success;  
   }         						// end function queryvehicle2($sfConn, $sfOwnerID)
//================================================================================================
	
//================================================================================================
// vehicle Setup	- Second
//================================================================================================

function vehicle_setup2($queryResults)
	{
	echo("<P> vehicle_setup2 <P>");

//--------start of first while loop:----------------------------------------------------------------------------- 
	$sqlRecs = 0;
	
//--------repid input----------------------------------------------------------------------------------
	echo( "<P>iaa  - vehicle, prepare check SF for account: " );
	$querysql = 'SELECT * FROM iaavehicle_sftmp2';
	$resultsql = mysql_query($querysql); 
	 echo("<P> Num rows: " . mysql_num_rows($resultsql) . " ");
	if (!$resultsql) 
		{
		echo("<P>Error performing query from  iaa vehicle sftmp2 table: " .  mysql_error() . " ");    
		exit();  
		}
	else
		{
		if (mysql_num_rows($resultsql) != 0)
			  {
			  echo( "<P>  iaa repid file has data, continue run" . " ");
			  } 
			else
			  {
			  echo( "<P>  iaa repid file has no data, exiting run" . " ");
      			return  $success;  
			  }
		}
	echo( "<P>iaa repid table has data" . " continue processing of SF upload ");
	$l = 0;
	while ($row = mysql_fetch_array($resultsql))
		{
		echo(" Next: ");
//----------set sql cells to varible and display-----------------------------------------------------------------
		$l++;
//			echo("<P> Looping l: $l ");
		$id = "";
		$idlit = "";
		$sfrepid = "";
		  
		$deaown = $row[iaa_deaown];	
		$slsdt = $row[iaa_slsdt];	
		$vin = $row[iaa_vin];	
		$lane = $row[iaa_lane];	
		$lotnum = $row[iaa_lotnum];	
		$slsstat = $row[iaa_slsstat];	
		$sellertype = $row[iaa_sellertype];	
		$miles = $row[iaa_miles];	
		$slsamt = $row[iaa_slsamt];	
		$deaseller = $row[iaa_deaseller];	
		$deabuyer = $row[iaa_deabuyer];	
		$repbuyerid = $row[iaa_repbuyerid];	
		$year = $row[iaa_year];
		$make = $row[iaa_make];	
		$model = $row[iaa_model];	
		$color = $row[iaa_color];	
		$condslsflg = $row[iaa_condslsflg];
		$onlineslsflg = $row[iaa_onlineslsflg];
		$deleted = $row[iaa_delete];
		
		if (FALSE)
		{
		echo("<P> deaown = " . $deaown  . 	
		" slsdt = " . $slsdt .	
		" vin = " . $vin .	
		" lane = " . $lane .	
		" lotnum = " . $lotnum .	
		" slsstat = " . $slsstat .	
		" sellertype = " . $sellertype .	
		" miles = " . $miles .	
		" slsamt = " . $slsamt .	
		" deaseller = " . $deaseller .	
		" deabuyer = " . $deabuyer .	
		" repbuyerid = " . $repbuyerid .	
		" year = " . $year .
		" make = " . $make .	
		" model = " . $model .	
		" color = " . $color .	
		" condslsflg = " . $condslsflg .
		" onlineslsflg = " . $onlineslsflg .
		" ");
		}
//----------2.query sf account object on input dealer number, return id-----------------------------
		$sfaccid = "";
		$sfowner = "";

// Determine whether contact record exists for this buyer
				   
		$bFound = FALSE;
		if (!($deleted == 'TRUE'))
			{   
			foreach ($queryResults->records as $nextResult)
				{
	//			echo("<P> Looping m: $m ");
				$m++;
				$nextDealSell = $nextResult->fields->Dealer_Number__c;
				$nextName = $nextResult->fields->Name;
				$id = $nextResult->Id;
				if (($nextDealSell == $deaseller) AND (!empty($deaseller)))				
					{
					echo ("<P> !! Seller MATCH found in Dealer seller: !! " . $deaseller . " Dealer Seller: " . $nextDealSell . " ");
	// 				echo("<P>   !!!!!!!!!!!!!!!!!  MATCH  MATCH  MATCH  MATCH  MATCH  MATCH !!!!!!!!!!!!!!!!!!!!! ");
					$bFound = TRUE;
	//echo("<P> !!! FIELDS !!!   " .
	//			" Owner ID: " . $deaown . 
	//			" DealSell: " . $deaseller .
	//			" DealBuy: " . $deabuyer .
	//			" RepBuyer: " . $repbuyerid .
	//			" ");		 
	//                echo("<P>sf record exists for this query to account;" .  
	//			    " SF ID = " . $id . 
	//			    " SF repid = " . $nextResult->fields->Name . 
	//			    " SF dealer name = " . $nextResult->fields->Dealer_Name__c .
	//			    " SF rep name = " . $nextResult->fields->Rep_Name__c .
	//			    " SF status = " . $nextResult->fields->Rep_Status__c .
	//			    " SF rep auc acc num = " . $nextResult->fields->Rep_Auction_Access_Number__c .
	//              " </P>");			   
					   
	//----------insert unprocessed rows into next tmp table---------------------------------------------------------
					$query = "REPLACE INTO iaavehicle_sftmp3
					(iaa_deaown ,	
					 iaa_slsdt ,	
					 iaa_vin ,	
					 iaa_lane ,	
					 iaa_lotnum ,	
					 iaa_slsstat ,	
					 iaa_sellertype ,	
					 iaa_miles ,	
					 iaa_slsamt ,	
					 iaa_deaseller ,	
					 iaa_deabuyer ,	
					 iaa_repbuyerid ,	
					 iaa_year ,
					 iaa_make ,	
					 iaa_model ,	
					 iaa_color ,	
					 iaa_condslsflg ,	
					 iaa_onlineslsflg)
					VALUES
					('".$deaown."', 	
					 '".$slsdt."',	
					 '".$vin."',	
					 '".$lane."',	
					 '".$lotnum."',	
					 '".$slsstat."',	
					 '".$sellertype."', 
					 '".$miles."',	
					 '".$slsamt."',	
					 '".$id."',	
					 '".$deabuyer."',	
					 '".$repbuyerid."',	
					 '".$year."',
					 '".$make."',	
					 '".$model."',	
					 '".$color."',	
					 '".$condslsflg."',
					 '".$onlineslsflg."')";
					$result = mysql_query($query); 
					if (!$result) 
					  {
					  echo("<P>Error creating iaa vehicle sq3 sf tmp1 table " .  mysql_error() . " ");    
					  exit();  
					  }
					}
				if ($bFound)	
					{			
					$querySQL = "UPDATE iaavehicle_sftmp2 
								SET iaa_delete = 'TRUE'
								WHERE ((iaa_vin = '$vin') AND (iaa_slsdt = '$slsdt'))   ";
					$resultdel = mysql_query($querySQL); 
					if (!$resultdel) 
					  {
					  echo("<P> Error deleting from iaa dealer create sql table " .  mysql_error() . " ");    
					  exit();  
					  }
					}			// end if ($bFound)
				}				// end foreach ($queryResults->records as $nextResult)
			}					// end if (!($deleted == 'TRUE'))
		else
			{	
			echo(" Another deleted:  ");
			}
		}						// end while ($row = mysql_fetch_array($resultsql))
      return  $success;  
   }        					// end function vehicle_setup2($queryResults) 
 //================================================================================================

	
//================================================================================================
// Third Query routine for vehicle	
//================================================================================================

      function queryvehicle3($sfConn, $sfOwnerID){
	echo("<P> queryvehicle3  ");
        $sObjects = array();
		$queryFields =  " Id, 
			  OwnerId,
			  Name,
			  Dealer_Number__c ";
		$querySource = "Account";
		$queryMatch = "OwnerID = '$sfOwnerID' AND Dealer_Number__c != ''";
		echo("<P> Query SF from queryvehicle3: " . $queryFields . " Source: " . $querySource . " Match: " . $queryMatch . " ");
		$queries = 0;
        $successful = query_first($sfConn, $queryFields, $querySource, $queryMatch);
        $queryResults = $successful[0];
		$done = $queryResults->done;
		$queryLocator = $queryResults->queryLocator;
		if ($successful)
			{
			$bContinue = TRUE;
			While ($bContinue)
				{
				$attResult = vehicle_setup3($queryResults);
				if (!$done)
					{
					echo("<P> Query next, Queries so far: " . $queries . " ");
					$success = query_more($sfConn, $queryLocator);
					$queryResults = $success[0];
					$done = $queryResults->done;
					$queryLocator = $queryResults->queryLocator;
					$queries++;
					if (!$success)
						{
						$bContinue = FALSE;
						}
					}
					else
					{
					$bContinue = FALSE;
					}
				}
			}
		
        return  $success;  
   }         
//================================================================================================
	
	
//================================================================================================
// vehicle Setup	- Third
//================================================================================================

function vehicle_setup3($queryResults)
	{
	echo("<P> vehicle_setup3  ");

//--------start of first while loop:----------------------------------------------------------------------------- 
	$sqlRecs = 0;
	
//--------repid input----------------------------------------------------------------------------------
	echo( "<P>iaa  - vehicle, prepare check SF for account: " );
	$querysql = 'SELECT * FROM iaavehicle_sftmp3';
	$resultsql = mysql_query($querysql); 
	 echo("<P> Num rows: " . mysql_num_rows($resultsql) . " ");
	if (!$resultsql) 
		{
		echo("<P>Error performing query from  iaa vehicle sftmp2 table: " .  mysql_error() . " ");    
		exit();  
		}
	else
		{
		if (mysql_num_rows($resultsql) != 0)
			  {
			  echo( "<P>  iaa repid file has data, continue run" . " ");
			  } 
			else
			  {
			  echo( "<P>  iaa repid file has no data, exiting run" . " ");
      			return  $success;  
			  }
		}
	echo( "<P>iaa repid table has data" . " continue processing of SF upload ");
	$l = 0;
	while ($row = mysql_fetch_array($resultsql))
		{
		echo(" Next: ");
//----------set sql cells to varible and display-----------------------------------------------------------------
		$l++;
		$id = "";
		$idlit = "";
		$sfrepid = "";
		
		$deaown = $row[iaa_deaown];	
		$slsdt = $row[iaa_slsdt];	
		$vin = $row[iaa_vin];	
		$lane = $row[iaa_lane];	
		$lotnum = $row[iaa_lotnum];	
		$slsstat = $row[iaa_slsstat];	
		$sellertype = $row[iaa_sellertype];	
		$miles = $row[iaa_miles];	
		$slsamt = $row[iaa_slsamt];	
		$deaseller = $row[iaa_deaseller];	
		$deabuyer = $row[iaa_deabuyer];	
		$repbuyerid = $row[iaa_repbuyerid];	
		$year = $row[iaa_year];
		$make = $row[iaa_make];	
		$model = $row[iaa_model];	
		$color = $row[iaa_color];	
		$condslsflg = $row[iaa_condslsflg];
		$onlineslsflg = $row[iaa_onlineslsflg];
		$deleted = $row[iaa_delete];
		
		if (TRUE)
		{
		echo("<P> deaown = " . $deaown  . 	
		" slsdt = " . $slsdt .	
		" vin = " . $vin .	
		" lane = " . $lane .	
		" lotnum = " . $lotnum .	
		" slsstat = " . $slsstat .	
		" sellertype = " . $sellertype .	
		" miles = " . $miles .	
		" slsamt = " . $slsamt .	
		" deaseller = " . $deaseller .	
		" deabuyer = " . $deabuyer .	
		" repbuyerid = " . $repbuyerid .	
		" year = " . $year .
		" make = " . $make .	
		" model = " . $model .	
		" color = " . $color .	
		" condslsflg = " . $condslsflg .
		" onlineslsflg = " . $onlineslsflg .
		"<P>");
		}
//----------2.query sf account object on input dealer number, return id-----------------------------
		$sfaccid = "";
		$sfowner = "";

// Determine whether contact record exists for this buyer
		$m = 0;		   
		$bFound = FALSE;
		if (!($deleted == 'TRUE'))
			{   
			foreach ($queryResults->records as $nextResult)
				{
				$m++;
	//			echo("<P> Looping $m ");
				$nextDealSell = $nextResult->fields->Dealer_Number__c;
				$nextName = $nextResult->fields->Name;
				$id = $nextResult->Id;
				if (($deabuyer == $nextDealSell) AND (!empty($deabuyer)) AND (!$bFound))				
					{
					echo ("<P> !! Seller MATCH found in Dealer buyer: !! " . $deabuyer . " Dealer Seller: " . $nextDealSell . " ");
	//				echo("<P>   !!!!!!!!!!!!!!!!!  MATCH  MATCH  MATCH  MATCH  MATCH  MATCH !!!!!!!!!!!!!!!!!!!!! ");
					$id = $nextResult->Id;
					$bFound = TRUE;
	//				echo("<P> !!! FIELDS !!!   " .
	//							" Owner ID: " . $deaown . 
	//							" DealSell: " . $deaseller .
	//							" DealBuy: " . $deabuyer .
	//							" RepBuyer: " . $repbuyerid .
	//							" ");		 
	//                echo("<P>sf record exists for this query to rep_id__c;" .  
	//			    " SF ID = " . $id . 
	//			    " SF repid = " . $nextResult->fields->Name . 
	//			    " SF dealer name = " . $nextResult->fields->Dealer_Name__c .
	//			    " SF rep name = " . $nextResult->fields->Rep_Name__c .
	//			    " SF status = " . $nextResult->fields->Rep_Status__c .
	//			    " SF rep auc acc num = " . $nextResult->fields->Rep_Auction_Access_Number__c .
	//                " </P>");			   
					   
	//----------insert unprocessed rows into next tmp table---------------------------------------------------------
					$query = "REPLACE INTO iaavehicle_sftmp4
					(iaa_deaown ,	
					 iaa_slsdt ,	
					 iaa_vin ,	
					 iaa_lane ,	
					 iaa_lotnum ,	
					 iaa_slsstat ,	
					 iaa_sellertype ,	
					 iaa_miles ,	
					 iaa_slsamt ,	
					 iaa_deaseller ,	
					 iaa_deabuyer ,	
					 iaa_repbuyerid ,	
					 iaa_year ,
					 iaa_make ,	
					 iaa_model ,	
					 iaa_color ,	
					 iaa_condslsflg ,	
					 iaa_onlineslsflg)
					VALUES
					('".$deaown."', 	
					 '".$slsdt."',	
					 '".$vin."',	
					 '".$lane."',	
					 '".$lotnum."',	
					 '".$slsstat."',	
					 '".$sellertype."', 
					 '".$miles."',	
					 '".$slsamt."',	
					 '".$deaseller."',	
					 '".$id."',	
					 '".$repbuyerid."',	
					 '".$year."',
					 '".$make."',	
					 '".$model."',	
					 '".$color."',	
					 '".$condslsflg."',
					 '".$onlineslsflg."')";
					$result = mysql_query($query); 
					if (!$result) 
						{
						echo("<P>Error creating iaa vehicle sq3 sf tmp1 table " .  mysql_error() . " ");    
						exit();  
						}
					echo( "<P>iaa vehicle sf tmp3 sql table created ");  			  
					}
				}
			if ($bFound)	
				{			
				$querySQL = "UPDATE iaavehicle_sftmp3 
							SET iaa_delete = 'TRUE'
							WHERE ((iaa_vin = '$vin') AND (iaa_slsdt = '$slsdt'))   ";
				$resultdel = mysql_query($querySQL); 
				if (!$resultdel) 
					{
					echo("<P> Error deleting from iaa dealer create sql table " .  mysql_error() . " ");    
					exit();  
				}					// end if ($bFound)
			}						// end foreach ($queryResults->records as $nextResult)
		}							// end if (!($deleted == 'TRUE'))
		else
			{	
			echo(" Another deleted:  ");
			}
	}								// end while ($row = mysql_fetch_array($resultsql))
 echo("<P> $l <P>");
     return  $success;  
  }         						// end function queryvehicle3($sfConn, $sfOwnerID)
 //================================================================================================

?>
