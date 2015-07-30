<?php

require_once('SforcePartnerClient.php');
require_once('SforceHeaderOptions.php');
require_once("devConstants.php");
$mySforceConnection = new SforcePartnerClient();
$mySoapClient = $mySforceConnection->createConnection($sfwsdl);
$mylogin = $mySforceConnection->login($sflogin, $sfpassword . $sftoken);

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
array_push($sObjects, $sObject);
$queryFields = "Id";
$querySource = "Sale_Activity__c";


$queryMatch="Sale_Date__c = 2015-07-24 AND Lot_Number__c = 'F60006'";

require_once('a_util.php');

$sfConn = $mySforceConnection;

$foundStuff = query_first($sfConn, $queryFields, $querySource, $queryMatch);




?>
