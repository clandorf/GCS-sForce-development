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
$inputdata = '~/GCS-sForce-devolpemnt/testData/rbaa_';

// SalesForce login details        ---------------------------------- 
$sflogin = 'demo@gcsiweb.com';
$sfwsdl = 'partner.wsdl.xml';
$sfpassword = 'Gcsi4562';
$sftoken = 'I8YA9ecWzvoV7zocGox736Q1';


// Owner and Account constants     ----------------------------------
$sfOwnerID = '00537000000QcYd';
$sfAccount = '00137000003rwua';
//  
//  // Missing buyer/seller info for vehicle load   ---------------------
$sfNoSeller = '00137000003rwuk';
$sfNoRepID = 'a0537000000dEXv';

// Indicate whether to make SF calls (test feature)   --------------- 
$store = 1;

?>	
