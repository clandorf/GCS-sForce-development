<?php

//Following line added for use with cronjob

#!/usr/local/bin/php -q

// Set general debug flag
$debugFlow = FALSE;

// Set details debug flag
$debugDetails = FALSE;

// Set data debug flag
$debugData = TRUE;

$maxRecords = 195;

echo( "<P>iaa dealer add/update" .  "to salesforce:</P>" );

//displayed comments
//------------------
echo( "<P>iaa  - First php script" . "- connecting to the server, and selecting a database:</P>" );

// Connect to mysql database
$dbcnx = @mysql_connect("www.globalcommdev.com", "mike", "mikep102954", false, 128);
  if (!$dbcnx) 
    {    
     echo( "<P>Unable to connect to the " . "database server at this time.</P>" );    
     exit();  
    }

 
echo( "<P> able to connect to" . "the server</P>");


if (! @mysql_select_db("mike") ) 
  {    
  echo( "<P>Unable to locate mike " . "database at this time.</P>" );      
  exit();  
  }

echo( "<P> able to" . "select database</P>");

?>


<?php 

// Connect to SF
echo("<P> iaa  - SalesForce add/update</P>");

//------required files to work with salesforce----------------------------------------------------------
		require_once ('SforcePartnerClient.php');
		require_once ('SforceHeaderOptions.php');	

//------Start of try construct--------------------------------------------------------------------------
		try
		  {
//--------Authentication--------------------------------------------------------------------------------
			$mySforceConnection = new SforcePartnerClient();
			$mySoapClient = $mySforceConnection->createConnection($sfwsdl);
			$mylogin = $mySforceConnection->login($sflogin,                  
											 $sfpassword . $sftoken);
      
//--------vehicle input----------------------------------------------------------------------------------
          echo( "<P>iaa  - script 2 - query table , prepare for upload to SF:</P>" );
          $querysql = 'SELECT * FROM iaavehicle_sftmp1';
          $resultsql = mysql_query($querysql); 
          if (!$resultsql) 
            {
            echo("<P>Error performing query from  iaa vehicles table: " .  mysql_error() . "</P>");    
            exit();  
            }
          else
            {
            if (mysql_num_rows($resultsql) != 0)
              {
              echo( "<P>  iaa vehicles file has data, continue run" . "</P>");
              } 
            else
              {
              echo( "<P>  iaa vehicles file has no data, exiting run" . "</P>");
              exit();
              }
            }
          echo( "<P>iaa vehicles table has data" . " coninue processing of SF upload</P>");


//---------first, set up for next tmp table to process those rows not handled here--------------------------------
//---------delelte, and create, new table-------------------------------------------------------------------------
           $query = "DROP TABLE IF EXISTS iaavehicle_sftmp2";
           $result = mysql_query($query); 
           if (!$result) 
             {
             echo("<P>Error deleting  iaa vehicle sf tmp2 sql table " .  mysql_error() . "</P>");    
             exit();  
             }
           echo( "<P>iaa vehicles sf tmp2 sql table deleted</P>");
		   		   
           $query = "CREATE TABLE iaavehicle_sftmp2
		   (iaa_deaown VARCHAR(30),	
		    iaa_slsdt VARCHAR(30),	
		    iaa_vin VARCHAR(30),	
		    iaa_lane VARCHAR(30),	
		    iaa_lotnum VARCHAR(30),	
		    iaa_slsstat VARCHAR(30),	
		    iaa_sellertype VARCHAR(30),	
		    iaa_miles VARCHAR(30),	
		    iaa_slsamt FLOAT(7,2),	
		    iaa_deaseller VARCHAR(30),	
		    iaa_deabuyer VARCHAR(30),	
		    iaa_repbuyerid VARCHAR(30),	
		    iaa_year VARCHAR(30),
			iaa_make VARCHAR(30),	
			iaa_model VARCHAR(40),	
			iaa_color VARCHAR(30),	
			iaa_condslsflg VARCHAR(30),	
			iaa_onlineslsflg VARCHAR(30),
			iaa_delete VARCHAR(30))";

           $result = mysql_query($query); 
           if (!$result) 
             {
             echo("<P>Error creating iaa vehicle sq2 sf tmp1 table " .  mysql_error() . "</P>");    
             exit();  
             }
           echo( "<P>iaa vehicle sf tmp2 sql table created</P>");


//-------- Call first setup routine: ----------------------------------------------------------------------------- 
          echo("<P> Call to queryVehicle </P>");
			$success = queryVehicle($mySforceConnection, $sfOwnerID);

$query = 'DELETE FROM iaavehicle_sftmp1
			WHERE (iaa_delete = "TRUE")'; 

$result = mysql_query($query); 

if (!$result) 
 {
  echo("<P>Error on iaa sftmp1 table update  " .  mysql_error() . "</P>");    
  exit();  
  }
	echo("<P> How big is sftmp1 after queryVehicle? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp1';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");
//--------- Updated based on RepID, now move the rest forward --------------------------------

echo("<P>Read iaa vehicle import table, write formatted sf update table - tmp1 first pass</P>");

$query = "INSERT INTO iaavehicle_sftmp2
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
          SELECT
            iaa_deaown,			
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
			'".$sfNoRepID."',
			iaa_year ,
			iaa_make ,
			iaa_model ,
			iaa_color ,
			iaa_condslsflg ,
			iaa_onlineslsflg
          FROM iaavehicle_sftmp1";
// a07Q0000000zI9p     a0780000007lFvz
$result = mysql_query($query); 

if (!$result) 
  {
  echo("<P>Error on iaa sf tmp2 update table: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>sf iaa sf tmp2 update table has been updated" );

	echo("<P> How big is sftmp2 after initial load? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp2';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");
//---------first, set up for next tmp table to process those rows not handled here--------------------------------
//---------delete, and create, new table-------------------------------------------------------------------------
           $query = "DROP TABLE IF EXISTS iaavehicle_sftmp3";
           $result = mysql_query($query); 
           if (!$result) 
             {
             echo("<P>Error deleting  iaa vehicle sf tmp3 sql table " .  mysql_error() . "</P>");    
             exit();  
             }
           echo( "<P>iaa vehicles sf tmp3 sql table deleted</P>");
		   		   
           $query = "CREATE TABLE iaavehicle_sftmp3
		   (iaa_deaown VARCHAR(30),	
		    iaa_slsdt VARCHAR(30),	
		    iaa_vin VARCHAR(30),	
		    iaa_lane VARCHAR(30),	
		    iaa_lotnum VARCHAR(30),	
		    iaa_slsstat VARCHAR(30),	
		    iaa_sellertype VARCHAR(30),	
		    iaa_miles VARCHAR(30),	
		    iaa_slsamt FLOAT(7,2),	
		    iaa_deaseller VARCHAR(30),	
		    iaa_deabuyer VARCHAR(30),	
		    iaa_repbuyerid VARCHAR(30),	
		    iaa_year VARCHAR(30),
			iaa_make VARCHAR(30),	
			iaa_model VARCHAR(40),	
			iaa_color VARCHAR(30),	
			iaa_condslsflg VARCHAR(30),	
			iaa_onlineslsflg VARCHAR(30),
			iaa_delete VARCHAR(30))";

           $result = mysql_query($query); 
           if (!$result) 
             {
             echo("<P>Error creating iaa vehicle sq3 sf tmp1 table " .  mysql_error() . "</P>");    
             exit();  
             }
           echo( "<P>iaa vehicle sf tmp3 sql table created</P>");

//-------- vehicle routine 2------------------------------------------------------------------------------
          echo( "<P>iaa  query table vehicle tmp3 prepare </P>" );
		  $success = queryVehicle2($mySforceConnection, $sfOwnerID);

$query = 'DELETE FROM iaavehicle_sftmp2
			WHERE (iaa_delete = "TRUE")'; 

$result = mysql_query($query); 

if (!$result) 
 {
  echo("<P>Error on iaa sftmp1 table update  " .  mysql_error() . "</P>");    
  exit();  
  }
	echo("<P> How big is sftmp2 after queryVehicle2? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp2';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");
//--------- Updated based on RepID, now move the rest forward --------------------------------

echo("<P>Read iaa vehicle import table, write formatted sf update table - tmp1 first pass</P>");

$query = "INSERT INTO iaavehicle_sftmp3
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
          SELECT
            iaa_deaown,			
            iaa_slsdt ,
		   	iaa_vin ,	
			iaa_lane ,
			iaa_lotnum ,
			iaa_slsstat ,
			iaa_sellertype ,	
			iaa_miles ,	
			iaa_slsamt ,	
			'".$sfNoSeller."' ,	
			iaa_deabuyer ,
			iaa_repbuyerid,
			iaa_year ,
			iaa_make ,
			iaa_model ,
			iaa_color ,
			iaa_condslsflg ,
			iaa_onlineslsflg
          FROM iaavehicle_sftmp2";
// a07Q0000000zI9p     0018000000edlwr

$result = mysql_query($query); 

if (!$result) 
  {
  echo("<P>Error on iaa sf tmp2 update table: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>sf iaa sf tmp2 update table has been updated" );

	echo("<P> How big is sftmp3 after initial load? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp3';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");

//---------first, set up for next tmp table to process those rows not handled here--------------------------------
//---------delete, and create, new table-------------------------------------------------------------------------
           $query = "DROP TABLE IF EXISTS iaavehicle_sftmp4";
           $result = mysql_query($query); 
           if (!$result) 
             {
             echo("<P>Error deleting  iaa vehicle sf tmp4 sql table " .  mysql_error() . "</P>");    
             exit();  
             }
           echo( "<P>iaa vehicles sf tmp4 sql table deleted</P>");
		   		   
           $query = "CREATE TABLE iaavehicle_sftmp4
		   (iaa_deaown VARCHAR(30),	
		    iaa_slsdt VARCHAR(30),	
		    iaa_vin VARCHAR(30),	
		    iaa_lane VARCHAR(30),	
		    iaa_lotnum VARCHAR(30),	
		    iaa_slsstat VARCHAR(30),	
		    iaa_sellertype VARCHAR(30),	
		    iaa_miles VARCHAR(30),	
		    iaa_slsamt FLOAT(7,2),	
		    iaa_deaseller VARCHAR(30),	
		    iaa_deabuyer VARCHAR(30),	
		    iaa_repbuyerid VARCHAR(30),	
		    iaa_year VARCHAR(30),
			iaa_make VARCHAR(30),	
			iaa_model VARCHAR(40),	
			iaa_color VARCHAR(30),	
			iaa_condslsflg VARCHAR(30),	
			iaa_onlineslsflg VARCHAR(30),
			iaa_delete VARCHAR(30))";

           $result = mysql_query($query); 
           if (!$result) 
             {
             echo("<P>Error creating iaa vehicle sq4 sf tmp1 table " .  mysql_error() . "</P>");    
             exit();  
             }
           echo( "<P>iaa vehicle sf tmp4 sql table created</P>");

//-------- vehicle routine 3------------------------------------------------------------------------------
          echo( "<P>iaa  query table vehicle tmp4 prepare </P>" );
		  $success = queryVehicle3($mySforceConnection, $sfOwnerID);

$query = 'DELETE FROM iaavehicle_sftmp3
			WHERE (iaa_delete = "TRUE")'; 

$result = mysql_query($query); 

if (!$result) 
 {
  echo("<P>Error on iaa sftmp1 table update  " .  mysql_error() . "</P>");    
  exit();  
  }
	echo("<P> How big is sftmp3 after queryVehicle3? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp3';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");
//--------- Updated based on RepID, now move the rest forward --------------------------------

	echo("<P> How big is sftmp3 now? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp3';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");


	echo("<P> How big is sftmp4 now? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp4';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");

echo("<P>Read iaa vehicle import table, write formatted sf update table - tmp1 first pass</P>");

$query = "INSERT INTO iaavehicle_sftmp4
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
          SELECT
            iaa_deaown,			
            iaa_slsdt ,
		   	iaa_vin ,	
			iaa_lane ,
			iaa_lotnum ,
			iaa_slsstat ,
			iaa_sellertype ,	
			iaa_miles ,	
			iaa_slsamt ,	
			iaa_deaseller ,
			'".$sfNoSeller."' ,	
			iaa_repbuyerid,
			iaa_year ,
			iaa_make ,
			iaa_model ,
			iaa_color ,
			iaa_condslsflg ,
			iaa_onlineslsflg
          FROM iaavehicle_sftmp3";
// a07Q0000000zI9p     0018000000edlwr

$result = mysql_query($query); 

if (!$result) 
  {
  echo("<P>Error on iaa sf tmp2 update table: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>sf iaa sf tmp2 update table has been updated" );

	echo("<P> How big is sftmp4 after initial load? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp4';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");
//--------start of Creation while loop:----------------------------------------------------------------------------- 
	echo("<P> last while loop starts </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp4';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");
	$sObjects = array ();
	$numCreateVehicles = 0;
	$numUpdateVehicles = 0;
	while ($row = mysql_fetch_array($resultsql))
      	{
//----------set sql cells to varible and display-----------------------------------------------------------------
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
		if ($slsstat == "No sale")
			{
			$deabuyer = "";
			$repbuyerid = "";
			}
		echo("<P> SF CALL deaown = " . $deaown  . 	
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
						 
//-----------modify from here down-------------------------------------------------------------------------------	
//----------create sf vehicle object record--------------------------------------------------------------------
		$fields = array('OwnerID'=>$deaown,	
		'Sale_Date__c'=>$slsdt,
		'VIN__c'=>$vin,
		'Lane__c'=>$lane,
		'Lot_Number__c'=>$lotnum,	
		'Sale_Status__c'=>$slsstat,	
		'Seller_Type__c'=>$sellertype,	
		'Mileage__c'=>$miles,	
		'Sale_Amount__c'=>$slsamt,	
		'Dealer_Seller__c'=>$deaseller,	
		'Dealer_Buyer__c'=>$deabuyer,	
		'Rep_Buyer_ID__c'=>$repbuyerid,	
		'Year__c'=>$year,	
		'Make__c'=>$make,	
		'Model__c'=>$model,	
		'Color__c'=>$color,	
		'Conditional_Sale__c'=>$condslsflg,	
		'Online_Purchase__c'=>$onlineslsflg);
echo("<P> !!! FIELDS !!!   " .
			" Owner ID: " . $deaown . 
			" DealSell: " . $deaseller .
			" DealBuy: " . $deabuyer .
			" RepBuyer: " . $repbuyerid .
			" ");		 
		$sObject = new stdclass();
		$sObject->fields = $fields;
		$sObject->type = 'Sale_Activity__c';    

		$theId = null;

		//checks if there is a vehicle with that owner, date, lotnumber. 
		$theId = checkIfVehicleExists($deaown, $slsdt, $lotnum);

		if( $theId != null){
			$sObject->Id = $theId;
			array_push($updateVehicleObjects, $sObject);
			echo("<P> Increment number of Vehicles To Update: " . $numUpdateVehicles . "<P>");
			$numUpdateVehicles++;
		}
		else{
			array_push($sObjects, $sObject);
			echo("<P> Increment number of Vehicles to Create: " . $numCreateVehicles . "<P>");
			$numCreateVehicles++;
		}


		if ($numCreateVehicles>$maxRecords)
			{
			if ($store = 1)
				{
				$success = create_multiple($mySforceConnection, $sObjects);
				}
			if (is_array($success))
				{
				$vehicle_created = $vehicle_created + $success[0];
				$vehicle_updated = $vehicle_updated + $success[1];
				$vehicle_failed =  $vehicle_failed + $success[2];
				echo " Created=$vehicle_created <br>";
				echo " Updated=$vehicle_updated <br>";
				echo " Failed=$attends_failed <br>";
				}
			echo "Updated $numCreateVehicles records, inside <br>";
			$sObjects = array ();
			$numCreateVehicles = 0;
			}
		} // end while loop, processing each mysql row. 
	if ($numCreateVehicles>0)
		{
		echo "Updating $numCreateVehicles RepID records: " . $numCreateVehicles . " <br>";
		if ($store = 1)
			{
			$success = create_multiple($mySforceConnection, $sObjects);
			}
		if (is_array($success))
			{
			$vehicle_created = $vehicle_created + $success[0];
			$vehicle_updated = $vehicle_updated + $success[1];
			$vehicle_failed =  $vehicle_failed + $success[2];
			echo " Created=$vehicle_created <br>";
			echo " Updated=$vehicle_updated <br>";
			echo " Failed=$attends_failed <br>";
			}
		echo "Updated $numCreateVehicles records: " . $numCreateVehicles . ", outside <br>";
		}

		if ($numUpdateVehicles>$maxRecords)
			{
			if ($store = 1)
				{
				$success = $sfConn->update($mySforceConnection, $updateVehicleObjects);
				}
			if (is_array($success))
				{
				$vehicle_created = $vehicle_created + $success[0];
				$vehicle_updated = $vehicle_updated + $success[1];
				$vehicle_failed =  $vehicle_failed + $success[2];
				echo " ... believed updated =$vehicle_created <br>";
				echo " Updated=$vehicle_updated <br>";
				echo " Failed=$attends_failed <br>";
				}
			echo "Updated $numUpdateVehicles records, inside <br>";
			$updateVehicleObjects = array ();
			$numUpdateVehicles = 0;
			}
		} // end while loop, processing each mysql row. 
	if ($numUpdateVehicles>0)
		{
		echo "Updating $numUpdateVehicles RepID records: " . $numUpdateVehicles . " <br>";
			$success = $sfConn->update($mySforceConnection, $updateVehicleObjects);
		if (is_array($success))
			{
			$vehicle_created = $vehicle_created + $success[0];
			$vehicle_updated = $vehicle_updated + $success[1];
			$vehicle_failed =  $vehicle_failed + $success[2];
			echo " ... believed updated =$vehicle_created <br>";
			echo " Updated=$vehicle_updated <br>";
			echo " Failed=$attends_failed <br>";
			}
		echo "Updated $numUpdateVehicles records: " . $numUpdateVehicles . ", outside <br>";
		}




	echo("<P> Vehicle processing done. <P>");  
//-------end of processing of vehicle input---------------------------------------------------------------------	
	      
//-----end of try construct		
	}  						 
//-----catch construct
	 catch (Exception $e)
       {
	   print_r($mySforceConnection->getLastRequest());
	   echo $e->faultstring;
       $ex = $e->faultstring;
	   }

	 function checkIfVehicleExists( $deaown, $slsdt, $lotnum){ 
			
		$querySource = "Sale_Activity__c";
		$queryFields = "Id, OwnerID";
		$queryMatch="OwnerID = '$deaown' AND Sale_Date__c = $slsdt AND Lot_Number__c = '$lotnum'";
		
		$foundStuff = query_first($sfConn, $queryFields, $querySource, $queryMatch);

		$records = $foundStuff[0]->records;
		$firstObject = $records[0];
		$theId = $firstObject->Id;
		echo "Found a record with that date... its id is ... " . $theId;

	 	return $theId;
	 }

	   	
?>
