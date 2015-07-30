<?PHP

 include('globalcommutil.php');
 include('a_util.php');
 include('aVehicle_util.php');

// Connect to mysql	
$dbcnx = @mysql_connect("www.globalcommdev.com", "mike", "mikep102954", false, 128);
  if (!$dbcnx) 
    {    
     echo("<P>Unable to connect to the " . "database server at this time.</P>");    
     exit();  
    }
  
echo("<P> able to connect to" . "the server</P>");

if (! @mysql_select_db("mike") ) 
  {    
  echo("<P>Unable to locate mike " . "database at this time.</P>");      
  exit();  
  }

echo("<P> able to" . "select database</P>");

?>


<?PHP

echo( "<P> delete and than create iaa inserted vehicles import, and iaa inserted vehicles sf tmp1 sql tables</P>" );

$query = "DROP TABLE IF EXISTS iaavehicle_imp";

// Delete tables and re-create
$result = mysql_query($query); 

if (!$result) 
  {
  echo("<P>Error deleting  iaa inserted vehicles import sql table " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>iaa inserted vehicles import sql table deleted</P>");

$query = "DROP TABLE IF EXISTS iaavehicle_sftmp1";

$result = mysql_query($query); 

if (!$result) 
  {
  echo("<P>Error deleting  iaa inserted vehicles sf tmp1 sql table " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>iaa inserted vehicles sf tmp1 sql table deleted</P>");

$query = "CREATE TABLE iaavehicle_imp
           (iaa_slsdt VARCHAR(30),
		   	iaa_vin VARCHAR(30),	
			iaa_lane VARCHAR(30),
			iaa_lotnum VARCHAR(30),
			iaa_slsstat VARCHAR(30),
			iaa_sellertype VARCHAR(30),	
			iaa_miles VARCHAR(30),	
			iaa_slsamt FLOAT(7,2),	
			iaa_deaseller VARCHAR(30),	
			iaa_deabuyer VARCHAR(30),
			iaa_repbuyernum VARCHAR(30),
			iaa_year VARCHAR(30),
			iaa_make VARCHAR(30),
			iaa_model VARCHAR(40),
			iaa_color VARCHAR(30),
			iaa_condslsflg VARCHAR(30),
			iaa_onlineslsflg VARCHAR(30))";

$result = mysql_query($query); 

if (!$result) 
  {
  echo("<P>Error creating iaa inserted vehicles sql table " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>iaa inserted vehicles sql table created</P>");

$query = "CREATE TABLE iaavehicle_sftmp1
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
  echo("<P>Error creating iaa inserted vehicles sql sf tmp1 table " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>iaa inserted vehicles sf tmp1 sql table created</P>");

?>

<?PHP

echo( "<P> iaa vehicle import" . " - clear table, then import csv file</P>" );

// Clear yable, then import .csv file
$sql = "TRUNCATE TABLE iaavehicle_imp";

$result = mysql_query($sql); 

if (!$result) 
  {
  echo("<P>Error performing truncate on iaa vehicle import table: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>iaa vehicle import table" . " now cleared</P>" );

echo( "<P>Start of import to iaa  table" . " csv file</P>" );

$inputfile = $inputdata . "vehicle.csv";
		
		echo("<P> Input file: " . $inputfile . " ");

$query = 'LOAD DATA LOCAL INFILE
         "'.$inputfile.'"
         INTO TABLE iaavehicle_imp 
         FIELDS TERMINATED BY "," ENCLOSED BY """" 
         LINES TERMINATED BY "\n"
		 IGNORE 1 LINES';

$result = mysql_query($query); 

if (!$result) 
  {
  echo("<P>Error performing import of iaa vehicleimport file " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>import of iaa vehicleimport file into" . " iaa vehicle table completed</P>");

?>

<?PHP

$query = 'SELECT * FROM iaavehicle_imp';

$result = mysql_query($query);
  
  if (!$result) 
    {
    echo("<P>Error performing query to iaavehicle_imp: " .  mysql_error() . "</P>");    
    exit();  
    }
  else
    {
    if (mysql_num_rows($result) != 0)
      {
      if (mysql_num_rows($result) <= 1)
        {
        $query2 = 'SELECT * FROM iaavehicle_imp';
        $result2 = mysql_query($query2);
        $row = mysql_fetch_row($result2);
        $iaaslsdt =  trim($row[0]);
        $sptype =  ctype_space($iaaslsdt) ? "true" : "false";
        $nultype = is_null($iaaslsdt) ? "true" : "false";
        echo (" this is sls date = " . $iaaslsdt);
        echo ("  this is sptype = " . $sptype);
        echo ("  this is nultype = " . $nultype);
        $wh = (($nultype) xor ($sptype)) ? "true" : "false";
        if (empty($iaaslsdt))
          {
           echo("sls date empty = ");
           exit();
          }
        echo("  this is wh = " . $wh);
        if (($nultype) xor ($sptype))
          { 
          echo( "<P>iaa vehicle import file has 1 rec, blank vehicle number, exiting run" );
          echo("<p>blank space ind = " . $sptype);
          exit(); 
          }
        echo( "<P>iaa vehicle import file has data, continue run" ); 
        }  
      }
    else
      {
      echo( "<P>iaa vehicle import file has no data, exiting run" );
      GoExit();
      }
    } 

echo( "<P>import of iaa vehicle import table completed" );

?>

<?php

echo( "<P> clear sf tmp1 table</P>" );

$sql = "TRUNCATE TABLE iaavehicle_sftmp1";

$result = mysql_query($sql); 

if (!$result) 
  {
  echo("<P>Error performing truncate on sf tmp1 table: " .  mysql_error() . "</P>");    
  exit();  
  }

echo("<P>Read iaa vehicle import table, write formatted sf update table - tmp1 first pass</P>");

//-------- input----------------------------------------------------------------------------------

	$querysftmp1 = 'SELECT * FROM iaavehicle_imp';
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
	echo( "<P>  VEHICLE table has data" . " coninue processing of SF upload ");
	$l = 0;
	while ($row = mysql_fetch_array($resultsql))
		{
//----------set sql cells to variable and display-----------------------------------------------------------------
		  
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
		$repbuyernum = $row[iaa_repbuyernum];
		if($deabuyer == '' or $row[iaa_repbuyernum] == '')
		{
		$repbuyerid = '';
		}
		else
		{
		$repbuyerid = $row[iaa_deabuyer] . "-";	
		$repbuyerid = $repbuyerid . $repbuyernum;
		}
		$year = $row[iaa_year];
		$make = cleanseString($row[iaa_make]);	
		$model = cleanseString($row[iaa_model]);	
		$color = cleanseString($row[iaa_color]);	
		$condslsflg = $row[iaa_condslsflg];
		$onlineslsflg = $row[iaa_onlineslsflg];

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
		" repbuyernum = " . $repbuyernum .	
		" repbuyerid = " . $repbuyerid .	
		" year = " . $year .
		" make = " . $make .	
		" model = " . $model .	
		" color = " . $color .	
		" condslsflg = " . $condslsflg .
		" onlineslsflg = " . $onlineslsflg .
		"<P>");
		}						// end if (FALSE)

		$query = "REPLACE INTO iaavehicle_sftmp1
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
			('".$sfOwnerID."', 	
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
		  echo("<P>Error creating iaa vehicle sq2 sf tmp1 table " .  mysql_error() . " ");    
		  exit();  
		  }
	}		


echo( "<P>sf iaa sf tmp1 update table has been created" );
	echo("<P> How big is sftmp1 now? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp1';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");

?>

<?php

echo("<P>Read sftmp1 table, update table - pass 2</P>");

$query = 'UPDATE iaavehicle_sftmp1 
            SET iaa_slsstat = IF (iaa_slsstat = "NOS", "No sale", (if (iaa_slsstat = "HST", "Sold", ""))),
			    iaa_sellertype = IF (iaa_sellertype = "C", "Consignor", (if (iaa_sellertype = "L", "Lease", ""))),
                iaa_condslsflg = IF (iaa_condslsflg = "Y", "TRUE", 
				                    (if ((iaa_condslsflg = "N" or iaa_condslsflg = ""), "FALSE", ""))),
				iaa_onlineslsflg = IF (iaa_onlineslsflg = "Y", "TRUE", 
				                      (if ((iaa_onlineslsflg = "N" or iaa_onlineslsflg = ""), "FALSE", ""))),
                iaa_model = REPLACE(iaa_model, "&", "&amp;")';

$result = mysql_query($query); 

if (!$result) 
 {
  echo("<P>Error on iaa sf tmp1 update table update - pass 2: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 2" );
 
?>

<?php

echo("<P>Read sftmp1 table, update table - pass 3</P>");

$query = 'DELETE FROM iaavehicle_sftmp1
            WHERE (iaa_onlineslsflg != "TRUE" and
			iaa_onlineslsflg != "FALSE")'; 

$result = mysql_query($query); 

if (!$result) 
 {
  echo("<P>Error on iaa sf tmp1 update table update - pass 3: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 3" );
 
?>

<?php

echo("<P>Read sftmp1 table, update table - pass 4</P>");

$query = 'DELETE FROM iaavehicle_sftmp1
            WHERE (iaa_condslsflg != "TRUE" and
			iaa_condslsflg != "FALSE")'; 

$result = mysql_query($query); 

if (!$result) 
 {
  echo("<P>Error on iaa sf tmp1 update table update - pass 4: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 4" );
 
?>


<?php

echo("<P>Read sftmp1 table, update table - pass 5</P>");

//$query = 'DELETE FROM iaavehicle_sftmp1
//            WHERE (iaa_deabuyer REGEXP "^[0-8]")';
//			
//$result = mysql_query($query); 
//
//if (!$result) 
// {
//  echo("<P>Error on iaa sf tmp1 update table update - pass 5: " .  mysql_error() . "</P>");    
//  exit();  
//  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 5" );
 
?>


<?php

echo("<P>Read sftmp1 table, update table - pass 6</P>");

//$query = 'DELETE FROM iaavehicle_sftmp1
//            WHERE (iaa_deabuyer REGEXP "^[9]" and
//			LENGTH(iaa_deabuyer) < 5)'; 
//
//$result = mysql_query($query); 
//
//if (!$result) 
// {
//  echo("<P>Error on iaa sf tmp1 update table update - pass 6: " .  mysql_error() . "</P>");    
//  exit();  
//  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 6" );

?>

<?php

echo("<P>Read sftmp1 table, update table - pass 5a</P>");

//$query = 'DELETE FROM iaavehicle_sftmp1
//            WHERE (iaa_deaseller REGEXP "^[0-8]")';
//			
//$result = mysql_query($query); 
//
//if (!$result) 
// {
//  echo("<P>Error on iaa sf tmp1 update table update - pass 5a: " .  mysql_error() . "</P>");    
//  exit();  
//  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 5a" );
 
?>


<?php

echo("<P>Read sftmp1 table, update table - pass 5b</P>");

//$query = 'DELETE FROM iaavehicle_sftmp1
//            WHERE (iaa_deaseller REGEXP "^[9]" and
//			LENGTH(iaa_deaseller) < 5)'; 
//
//$result = mysql_query($query); 
//
//if (!$result) 
// {
//  echo("<P>Error on iaa sf tmp1 update table update - pass 5b: " .  mysql_error() . "</P>");    
//  exit();  
//  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 5b" );

?>


<?php

echo("<P>Read sftmp1 table, update table - pass 7</P>");

$query = 'UPDATE iaavehicle_sftmp1 
            SET iaa_slsdt = CONCAT((MID(iaavehicle_sftmp1.iaa_slsdt,
                             (LOCATE("/", iaavehicle_sftmp1.iaa_slsdt, (LOCATE("/", iaavehicle_sftmp1.iaa_slsdt, 1) +1))+1),4)),
                              "-",
                              LPAD((LEFT(iaavehicle_sftmp1.iaa_slsdt, (LOCATE("/", iaavehicle_sftmp1.iaa_slsdt) -1))),2,"0"),
                              "-",
                              LPAD((MID(iaavehicle_sftmp1.iaa_slsdt, (LOCATE("/", iaavehicle_sftmp1.iaa_slsdt, 1) +1), 
                             (LOCATE("/", iaavehicle_sftmp1.iaa_slsdt, (LOCATE("/", iaavehicle_sftmp1.iaa_slsdt, 1) +1))) -
                             (LOCATE("/", iaavehicle_sftmp1.iaa_slsdt, 1) +1))),2,"0"))
              WHERE (iaavehicle_sftmp1.iaa_slsdt != "")';

$result = mysql_query($query); 

if (!$result) 
 {
  echo("<P>Error on iaa sf tmp1 update table update - pass 7: " .  mysql_error() . "</P>");    
  exit();  
  }

echo( "<P>sf iaa sf tmp1 update table has been updated - pass 7" );

	echo("<P> How big is sftmp1 now? </P>");				
	$querysql = 'SELECT * FROM iaavehicle_sftmp1';
	$resultsql = mysql_query($querysql); 				
	echo("<P> Num rows: " . mysql_num_rows($resultsql) . "<P>");


?>