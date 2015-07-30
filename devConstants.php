<?php
//================================================================================================
//  Global Communication Services
//  www.gcsiweb.com
//  Telephone: 607-786-6205
//  Toll Free: 866-463-6292  
//  Fax:       607-786-1231
//  e-Mail:    info@gcsiweb.com
//  Charleston Auto Auction execution constants file for data upload
//  Author:  Jeff Howard  jeff.howard2@yahoo.com
//  Create Date:  August 2010
//================================================================================================

// File path for input data files  ---------------------------------- 
$inputdata = '/var/www/globalcommdev/webroot/AuctionData/rea/rbaa_';

// SalesForce login details        ---------------------------------- 
$sflogin = 'demo@gcsiweb.com';
$sfwsdl = 'partner.wsdl.xml';
$sfpassword = 'Gcsi4562';
$sftoken = 'I8YA9ecWzvoV7zocGox736Q1';

// Owner and Account constants     ---------------------------------- 
$sfOwnerID = "005j000000BTnZt";
$sfAccount = '001j000000H7y3Z';

// Missing buyer/seller info for vehicle load   --------------------- 
$sfNoSeller = '001j000000H7y3b';
$sfNoRepID = "a04j00000045FwV";

// Indicate whether to make SF calls (test feature)   --------------- 
$store = 1;

?>	
