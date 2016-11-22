<?php include("/inc/all-includes.php"); 
 /**
  * This script will be used to process form submissions
  * The db survey will be broken into 3 tables
  * 1. tbl_surveys - to record vehicle, survey and user details
  * 2. tbl_answers - to record the standard question results
  * 3. tbl_axle_responses to record the axle based responses
  */
 
   
    // Get initial Survey Details [Vehicle Details Section]
    $vehicleType 					= $_POST['veh_dets_11']; //echo "Vehicle Type: $vehicleType <br>";
    $vehicleReg	 					= $_POST['veh_dets_12']; //echo "Vehicle Reg: $vehicleReg <br>";
    $companyID 						=	$_POST['veh_dets_13']; //echo "Company ID: $companyID <br>";
    $makeModel 						=	$_POST['veh_dets_14']; //echo "Make/Model: $makeModel <br>";
    $surveyDate 					=	$_POST['veh_dets_15']; //echo "Survey Date: $surveyDate <br>";
    $odoReading						= $_POST['veh_dets_16']; //echo "Odometer Reading: $odoReading <br>";
    $odoType 							= $_POST['veh_dets_17']; //echo "Odometer Type: $odoType <br>";
    $preServiceRemarks 		= $_POST['veh_dets_18']; //echo "Pre Service Remarks: $preServiceRemarks <br>";
    // Get initial Survey Details [Report Details]
    $notesPartsList			= $_POST['rep_dets_notes'];
    // Get other details necessary for report
    $completedByID 			= $_SESSION["user"]["user_id"]; // The current logged in user
    $supervisedByID 		= ""; // This should be NULL due to the fact that this is a pending report
    if(isset($_POST['save'])) {
      $status_id          = "1"; // If this script is running and save has been clicked set the status to draft
    }
    if(isset($_POST['submit'])) {
      $status_id          = "2"; // If this script is running and submit has been clicked then this is a pending report so status should be '2'
    }
    $dateLastUpdate 		= date('Y-m-d H:i:s'); // Always stamp this with the current date
    $userIDLastUpdate   = $user->user_id; // the ID of the current logged in user regardless if a survey is being created or edited
    // Anything else...
    $formatdate = date('Y-m-d H:i:s', strtotime($surveyDate));  // Format the date for MySQL
    try {
    		$dets_sql = "INSERT INTO `tbl_surveys` (
    								`survey_ID`, 
    								`vehicle_type`,
    								`vehicle_reg`, 
    								`company_ID`, 
    								`make_model`, 
    								`odo_reading`, 
    								`odo_type`, 
    								`pre_service_remarks`, 
    								`notes_parts_list`, 
    								`completed_by_user_ID`, 
    								`supervised_by_user_ID`, 
    								`survey_date`, 
                    `status_id`, 
    								`date_last_update`, 
    								`user_last_update`) 
    							VALUES (
    								:surveyID,
    								:vehicleType,
    								:vehicleReg, 			
    								:companyID, 			
    								:makeModel, 		
    								:odoReading, 		
    								:odoType, 			
    								:preServiceRemarks,
    								:notesPartsList, 	
    								:completedByID, 	
    								:supervisedByID,  
    								:surveyDate,
                    :statusID,   
    								:dateLastUpdate,
    								:userIDLastUpdate)";     
    			
    			// echo $sql;
    			 $query = $db->prepare($dets_sql);
    			 // Bind Values
    			 $query->bindValue(":surveyID", null, PDO::PARAM_NULL); 						// Survey ID
    			 $query->bindValue(":vehicleType", $vehicleType, PDO::PARAM_STR); 	// Vehicle Registration
    			 $query->bindValue(":vehicleReg", $vehicleReg, PDO::PARAM_STR); 		// Vehicle Registration
    			 $query->bindValue(":companyID", $companyID, PDO::PARAM_INT); 			// Company ID
    			 $query->bindValue(":makeModel", $makeModel, PDO::PARAM_STR); 			// Make/Model
    			 $query->bindValue(":odoReading", $odoReading, PDO::PARAM_INT); 		// Odometer Reading
    			 $query->bindValue(":odoType", $odoType, PDO::PARAM_STR); 					// Odometer Type
    			 $query->bindValue(":preServiceRemarks", $preServiceRemarks, PDO::PARAM_STR); // Pre service
    			 
    			 $query->bindValue(":notesPartsList", $notesPartsList, PDO::PARAM_STR); // Notes Parts
    			 $query->bindValue(":completedByID", $completedByID, PDO::PARAM_INT); 	// Completed By			 
           $query->bindValue(":supervisedByID", null, PDO::PARAM_NULL);   // Supervised By <-- NULL!!
    			 $query->bindValue(":surveyDate", $formatdate, PDO::PARAM_STR); 				// Survey Date
           $query->bindValue(":statusID", $status_id, PDO::PARAM_STR);         // Survey Date
    			 $query->bindValue(":dateLastUpdate", $dateLastUpdate, PDO::PARAM_STR); // Date Last Update
    			 $query->bindValue(":userIDLastUpdate", $userIDLastUpdate, PDO::PARAM_INT); // Date Last Update			 
    			 $query->execute();
    			 $newId = $db->lastInsertId(); // Gets the latest primary key after execution		
    			 // echo "<p><strong>Vehicles details successfully recorded!</strong></p>"	 ;
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database.";
            echo $e->getMessage();
            die;
        }
      /** ===============================================================
       * Handle the Brake Performance Section and the Tyre Thread Section
       * Only record a row for values that have been passed
       * =================================================================
       */
      
      $axle_array = array(); // Declare array
      
      // Loop through $_POST and build a new array of required responses
      foreach($_POST as $key=>$response):
      	// if a key begins with 'axle'
      	if (0 === strpos($key, 'axle')) {
       
       		//if the array element is not empty
       		if(!empty($response)){
       			
       			// Build a new array containing only response
       			$axle_array[$key] = $response;
       		}
    		}
      	
      endforeach;  
      try {
      	// The SQL Query
      	$axle_sql= "INSERT INTO `tbl_survey_axle_responses` (
      								`response_ID`,
      								`survey_ID`, 
      								`question_ID`, 
      								`question_response`) 
      							VALUES (
      								:responseID,
      								:surveyID, 
      								:questionID, 
      								:questionResponse)";
    		
    		// Prepare the statement
    		$axle_query = $db->prepare($axle_sql);
      	// Loop through the new array of axle responses
      	foreach($axle_array as $key=>$response){
      		// Bind values
      		$axle_query->bindValue(":responseID", null, PDO::PARAM_NULL); 	
      		$axle_query->bindValue(":surveyID", $newId, PDO::PARAM_INT); // Survey ID
      		$axle_query->bindValue(":questionID", $key, PDO::PARAM_STR); // Question ID
      		$axle_query->bindValue(":questionResponse", $response, PDO::PARAM_INT); // Question ID
      		// Execute the query
      		$axle_query->execute();			
      		
      	}
    		// echo "<p><strong>Axle responses successfully recorded!</strong></p>"	 ;
      } catch (Exception $e) {
            //echo "Data could not be retrieved from the database.";
            echo $e->getMessage();
            die;
      }


      /** ===============================================================
       *  Handle the Lubrication Question responses    
       *  ================================================================
       */
      
      $lube_array = array(); // Declare array
      
      // Loop through $_POST and build a new array of required responses
      // This excludes everything already captured
      foreach($_POST as $key=>$response):
        if (0 === strpos($key, 'veh_lub')){
          //if the array element is not empty
          if(!empty($response)){
            if($response!="ok"){ 
              echo "<p>key: $key | resp: $response";
              // Build a new array containing only responses
              $lube_array[$key] = $response;
            } // end response not empty
          } // end if !- ok    
        } // end if begins "veh_lub"
      endforeach; // end foreach  
       try {
        // The SQL Query
        $lube_sql = "INSERT INTO `tbl_survey_responses` (
                      `response_ID`, 
                      `survey_ID`, 
                      `question_ID`, 
                      `question_response`) 
                    VALUES (
                      :responseID,
                      :surveyID, 
                      :questionID, 
                      :questionResponse)";
        
        // Prepare the statement
        $response_query = $db->prepare($lube_sql);
        // Loop through the new array of axle responses
        foreach($lube_array as $key=>$response){      
          // Bind values
          $response_query->bindValue(":responseID", null, PDO::PARAM_NULL);   
          $response_query->bindValue(":surveyID", $newId, PDO::PARAM_INT); // Survey ID
          $response_query->bindValue(":questionID", $key, PDO::PARAM_STR); // Question ID
          $response_query->bindValue(":questionResponse", $response, PDO::PARAM_STR); // Question ID
          // Execute the query
          $response_query->execute();     
          
        }// end foreach
        // echo "<p><strong>All lubrication responses successfully recorded!</strong></p>"  ;
      } catch (Exception $e) {
            // echo "Data could not be retrieved from the database.";
            echo $e->getMessage();
            die;
      }
      
      /** ===============================================================
       *  Handle the Main Question responses    
       *  ================================================================
       */
      
      $response_array = array();
      // Loop through $_POST and build a new array of required responses
      // This excludes everything already captured
      foreach($_POST as $key=>$response):
       
      	if (
            (0 === strpos($key, 'veh_lights')) || 
      		  (0 === strpos($key, 'veh_tacho')) || 
      		  (0 === strpos($key, 'veh_insidecab')) ||
      		  (0 === strpos($key, 'veh_glevel')) ||
      		  (0 === strpos($key, 'veh_smallservice')) ||
            (0 === strpos($key, 'veh_additional'))
          )
        {
       
       		//if the array element is not empty
       		if(!empty($response)){
       			/* ============================================================= */ 
       			// We only really want to record stuff that is not satisfactory.
       			// This is to reduce database resource usage and overheads.
       			// Therefore only record responses where it is != satisfactory
       			// - this works because at this stage only filled-in responses
       			// remain. 'Details' and 'rectified by' are only filled in if
       			// there is a status other than satisfactory.
       			/* ============================================================= */
       			
       			if($response!="satisfactory"){   			
       			
    	   			// Build a new array containing only responses
    	   			$response_array[$key] = $response;
    	   		} // close !"satisfactory"
       		} // close !empty
    		} // close if
      endforeach;
      // echo "<pre>";
      // var_dump($response_array);
      
      try {
      	// The SQL Query
      	$response_sql = "INSERT INTO `tbl_survey_responses` (
      								`response_ID`, 
      								`survey_ID`, 
      								`question_ID`, 
      								`question_response`) 
      							VALUES (
      								:responseID,
      								:surveyID, 
      								:questionID, 
      								:questionResponse)";
    		
    		// Prepare the statement
    		$response_query = $db->prepare($response_sql);
    		// Loop through the new array of axle responses
    		foreach($response_array as $key=>$response){			
    			// Bind values
    			$response_query->bindValue(":responseID", null, PDO::PARAM_NULL); 	
    			$response_query->bindValue(":surveyID", $newId, PDO::PARAM_INT); // Survey ID
    			$response_query->bindValue(":questionID", $key, PDO::PARAM_STR); // Question ID
    			$response_query->bindValue(":questionResponse", $response, PDO::PARAM_STR); // Question ID
    			// Execute the query
    			$response_query->execute();			
    			
    		}// end foreach
    		// echo "<p><strong>All other responses successfully recorded!</strong></p>"	 ;
      } catch (Exception $e) {
            // echo "Data could not be retrieved from the database.";
            echo $e->getMessage();
            die;
      }

    // May also need to submit results via email
    // 
    if(isset($_POST['submit'])){
      // if we make it this far assume all is OK and redirect to report-management
       header("Location: report-management.php?message=success"); 
             
      // Remember that this die statement is absolutely critical.  Without it, 
      // people can view your members-only content without logging in. 
     die("Redirecting to report-management.php"); 
    }

    if(isset($_POST['save'])) {
      // if we make it this far assume all is OK and redirect to report-management
       header("Location: edit-report.php?surveyid=$newId&message=success"); 
             
      // Remember that this die statement is absolutely critical.  Without it, 
      // people can view your members-only content without logging in. 
     die("Redirecting to report-management.php"); 
    }