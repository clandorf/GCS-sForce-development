<?php

require_once('SforcePartnerClient.php');
require_once('SforceHeaderOptions.php');
require_once("devConstants.php");
require_once('a_util.php');
require_once('globalcommutil.php');

$mySforceConnection = new SforcePartnerClient();
$mySoapClient = $mySforceConnection->createConnection($sfwsdl);
$mylogin = $mySforceConnection->login($sflogin, $sfpassword . $sftoken);

$deaown = '00537000000QcYdAAK';
$slsdt = '2015-07-21';
$lotnum = 'F60006';
$color = 'silver';
$vin = '3030202';
$lane = '3';
$slsstat = 'sold';
$sellertype = 'unhappy';
$miles = '100';
$slsamt = '100';
$deaseller = '00137000002jWHg';
$deabuyer = '00137000002jWHg';
$repbuyerid = 'a0537000000cU9k';
$year = '2000';
$make = 'Buick';
$model = 'Lesabre';
$condslsflg = 'flagged';
$onlineslsflg = 'flagged';

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


$sObjects=array();
$sObject = new stdclass();
$sObject->fields = $fields;
$sObject->type = 'Sale_Activity__c';
$querySource = "Sale_Activity__c";
$queryFields = "Id, OwnerID";
//$sObject->fields['Id'] = "MYIDgo";


$queryMatch="OwnerID = '$deaown' AND Sale_Date__c = $slsdt AND Lot_Number__c = '$lotnum'";


$sfConn = $mySforceConnection;

$foundStuff = query_first($sfConn, $queryFields, $querySource, $queryMatch);

$records = $foundStuff[0]->records;
$firstObject = $records[0];
$theId = $firstObject->Id;
echo "Found a record with that date... its id is ... " . $theId;

$sObject->Id = $theId;

echo "the new constructed sObject is " .print_r($sObject, True);

array_push($sObjects, $sObject);

$results = $sfConn->update($sObjects);
echo "the results of the update are " . print_r($results, True);

//$resultSObject = $foundStuff->records;
//echo "theh id is " .$resultSObject['Id'];




?>
