<?php
//================================================================================================
//  Global Communication Services
//  www.gcsiweb.com
//  Telephone: 607-786-6205
//  Toll Free: 866-463-6292  
//  Fax:       607-786-1231
//  e-Mail:    info@gcsiweb.com
//  Utility File to support Saleforce Indiana Auto Auction Nultiple Transaction management routines.
//  Author:  Jeff Howard  jeff.howard2@yahoo.com
//  Create Date:  May 2010
//================================================================================================

//================================================================================================
// SF Create Multiple 
//================================================================================================

  function create_multiple($sfConn, $sObjects)
  {
		echo("<P> create_multiple <P>");
        $accounts_created = 0;
        $accounts_updated = 0;
        $accounts_failed = 0;
        try
        {
            // The create process
            $results = $sfConn->create($sObjects);
            foreach ($results as $result)
            {
                if ($result->success)
                {
                    if ($result->created)
                    {
                        $accounts_created++;
                    }
                    else
                    {
                        $accounts_updated++;
                    }
                }
                else
                {
				$error1 = $result->errors;
				echo("<P> Error message: " . $error1->message . "<P>");
                $accounts_failed++;
                }
            }
            // Put the result counts into an array to pass back as the result.
            $success = array();
            array_push($success, $accounts_created, $accounts_updated, $accounts_failed, $results);
            return $success;
            exit;
        }

        catch (exception $e)
        {
            // This is reached if there is a major problem in the data or with
            // the salesforce.com connection. Normal data errors are caught by
            // salesforce.com
			echo("<P> Major SalesForce error <P>");
            echo $e->GetMessage();
            return false;
            exit;
        }
    
    }
   
//================================================================================================
// SF Create Contact staging
//================================================================================================

      function createContact($sfConn, $dataArray){
		echo("<P> createContact <P>");
        $sObjects = array();
        foreach ($dataArray as $fieldset)
            {
		echo("<P> Set records to Contact <P>");
                $sObject = new sObject();
                $sObject->type = 'Contact'; 
                $sObject->fields = $fieldset;
                array_push($sObjects, $sObject);
            }
		echo("<P> Ready to roll <P>");
        $success = create_multiple($sfConn, $sObjects);
        return  $success;  
   }         
   
//================================================================================================
// SF Create Account (Dealer) staging
//================================================================================================

      function createAccount($sfConn, $dataArray){
		echo("<P> createAccount <P>");
        $sObjects = array();
        foreach ($dataArray as $fieldset)
            {
		echo("<P> Set records to Account <P>");
                $sObject = new sObject();
                $sObject->type = 'Account'; 
                $sObject->fields = $fieldset;
                array_push($sObjects, $sObject);
				
echo("<P> ACCOUNT DETAILS- type: " . $sObject->type . 
		" Dealer: " . $fieldset['Dealer_Number__c'] . 
		" Name: " . $fieldset['Name'] . 
							" Owner ID: " . $fieldset['OwnerId'] .
							" Phone " . $fieldset['Phone'] .
							" Dealer Auction Access: " . $fieldset['Dealer_Auction_Access_Number__c'] .
							" Dealer_Status__c: " . $fieldset['Dealer_Status__c'] .
							" IAA_Territory__c: " . $fieldset['IAA_Territory__c'] .
							" ID: " . $sObject->Id .
		"");
		
            }
		echo("<P> Ready to roll <P>");
        $success = create_multiple($sfConn, $sObjects);
        return  $success;  
   }         

//================================================================================================
// SF Create Rep_ID_c staging
//================================================================================================

      function createRepID($sfConn, $dataArray){
		echo("<P> createRepID <P>");
        $sObjects = array();
        foreach ($dataArray as $fieldset)
            {
		echo("<P> Set records to Rep_ID_c <P>");
                $sObject = new sObject();
                $sObject->type = 'Rep_ID_c'; 
                $sObject->fields = $fieldset;
                array_push($sObjects, $sObject);
            }
		echo("<P> Ready to roll <P>");
        $success = create_multiple($sfConn, $sObjects);
        return  $success;  
   }         
   
//================================================================================================
// SF Update Multiple 
//================================================================================================

  function update_objects($sfConn, $sObjects)
  {
		echo("<P> update_objects <P>");
        $accounts_created = 0;
        $accounts_updated = 0;
        $accounts_failed = 0;
        try
        {
            // The update process
            $results = $sfConn->update($sObjects);
            foreach ($results as $result)
            {
                if ($result->success)
                {
                    if ($result->created)
                    {
                        $accounts_created++;
                    }
                    else
                    {
                        $accounts_updated++;
                    }
                }
                else
                {
				$error1 = $result->errors;
				echo("<P> Error message: " . $error1->message . "<P>");
                $accounts_failed++;
                }
            }

            // Put the result counts into an array to pass back as the result.
            $success = array();
            array_push($success, $accounts_created, $accounts_updated, $accounts_failed);
            return $success;
            exit;
        }

        catch (exception $e)
        {
            // This is reached if there is a major problem in the data or with
            // the salesforce.com connection. Normal data errors are caught by
            // salesforce.com
			echo("<P> Major SalesForce error <P>");
            echo $e->GetMessage();
            return false;
            exit;
        }
    
    }
   
//================================================================================================
// SF Update Account staging
//================================================================================================

      function updateAccount($sfConn, $dataArray){
		echo("<P> updateAccount ");
        $sObjects = array();
        foreach ($dataArray as $fieldset)
            {
				echo("<P> Set records to Account ");
                $sObject = new sObject();
                $sObject->type = 'Account'; 
                $sObject->fields = $fieldset;
                array_push($sObjects, $sObject);
            }
		echo("<P> Ready to roll ");
        $success = update_objects($sfConn, $sObjects);
        return  $success;  
   }         
   
   
//================================================================================================
// SF Update Contact staging
//================================================================================================

      function updateContact($sfConn, $dataArray){
		echo("<P> updateContact ");
        $sObjects = array();
        foreach ($dataArray as $fieldset)
            {
				echo("<P> Set records to Contact ");
                $sObject = new sObject();
                $sObject->type = 'Contact'; 
                $sObject->fields = $fieldset;
              	$sObject->Id = $fieldset[iaa_id];
                array_push($sObjects, $sObject);
            }
		echo("<P> Ready to roll ");
        $success = update_objects($sfConn, $sObjects);
        return  $success;  
   }         
   
   
//================================================================================================
// SF Query routine
//================================================================================================

  function query_first($sfConn, $queryFields, $querySource, $queryMatch)
  {
		echo("<P> query_first: " . $queryFields . " Source: " . $querySource . " Match?: " . $queryMatch . "<P>");
        try
        {
            // The query process
            if (empty($queryMatch))
				{ 
				$querysf = "select " . $queryFields . " FROM " . $querySource;
				echo("<P> No WHERE: <P> " . $querysf . "<P>");
				$queryResults = $sfConn->query($querysf);
				}
				else
				{
				$querysf = "select " . $queryFields . " FROM " . $querySource . " WHERE " . $queryMatch;
				echo("<P> WHERE: <P> " . $querysf . "<P>");
				$queryResults = $sfConn->query($querysf);
				}

		     $queryResultsf = new QueryResult($queryResults);
		     $recordsf = $queryResultsf->records[0];
			 $recnums = $queryResultsf->size;

			if ($queryResultsf->size > 0)
				{
				echo("<P> Query results size: " . $queryResultsf->size . " <P>");
				}
				else
				{
					echo("<P> Query results size = 0 " . $queryResultsf->size . " <P>");
				}
			 gprint();
			 gprint();
			 gprint ("the contents of queryResult are " . print_r($queryResults));
				gprint ("the contents of queryResultsf are " . print_r($queryResultsf));

            // Put the result counts into an array to pass back as the result.
            $successful = array();
            array_push($successful, $queryResultsf);
            return $successful;
            exit;
        }

        catch (exception $e)
        {
            // This is reached if there is a major problem in the data or with
            // the salesforce.com connection. Normal data errors are caught by
            // salesforce.com
			echo("<P> Major SalesForce error <P>");
            echo $e->GetMessage();
            return false;
            exit;
        }
    
   }
   
//================================================================================================
// SF Query Next routine
//================================================================================================

  function query_more($sfConn, $queryLocator)
  {
		echo("<P> query_more <P>");
        try
        {
            // The query process
		echo("<P> Query more <P>");
echo("<P> QUERYLOCATOR, call!!! " . 	$queryLocator . " !! " . "<P>");
		$queryResults = $sfConn->queryMore($queryLocator);
			
        // Put the result counts into an array to pass back as the result.
        $successful = array();
        array_push($successful, $queryResults);
        return $successful;
        exit;
        }

        catch (exception $e)
        {
            // This is reached if there is a major problem in the data or with
            // the salesforce.com connection. Normal data errors are caught by
            // salesforce.com
			echo("<P> Major SalesForce error <P>");
            echo $e->GetMessage();
            return false;
            exit;
        }
    
  }

function gprint($string) { 
	print "$string\n";
}
?>	


