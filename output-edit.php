<?php include("all-includes.php"); 
 /**
  * Used to process surveys submitted via edit-report.php
  * Here we will update existing survey details and do one of the following
  * 1. Stay on the page if save is clicked - (save as draft is clicked)
  * 2. Take to report management if submit is clicked - (mark as pending is clicked)
  * 3. Take to report management if final is click - (mark report as complete is clicked)
  *
  * NB: This should be amalgamated into output.php
  */
 
 // echo "<pre>";var_dump($_POST);echo "</pre>";

    /* =======================================================*/
    // Get initial Survey Details [Vehicle Details Section]
    /* =======================================================*/
    $surveyID             = $_POST['surveyid'];
    $vehicleType 					= $_POST['veh_dets_11']; //echo "Vehicle Type: $vehicleType <br>";
    $vehicleReg	 					= $_POST['veh_dets_12']; //echo "Vehicle Reg: $vehicleReg <br>";
    $companyID 						=	$_POST['veh_dets_13']; //echo "Company ID: $companyID <br>";
    $makeModel 						=	$_POST['veh_dets_14']; //echo "Make/Model: $makeModel <br>";
    $surveyDate 					=	$_POST['veh_dets_15']; //echo "Survey Date: $surveyDate <br>";
    $odoReading						= $_POST['veh_dets_16']; //echo "Odometer Reading: $odoReading <br>";
    $odoType 							= $_POST['veh_dets_17']; //echo "Odometer Type: $odoType <br>";
    $preServiceRemarks 		= $_POST['veh_dets_18']; //echo "Pre Service Remarks: $preServiceRemarks <br>";

    // Echo to page for troubleshooting - will need to place 'exit; to stop full code from running
    // echo "<br>ID: "       . $surveyID ."<br>" ;
    // echo "<br>type: "     . $vehicleType ."<br>" ;
    // echo "reg: "          .  $vehicleReg ."<br>" ;
    // echo "co id: "        . $companyID ."<br>" ; 
    // echo "make model :"   . $makeModel ."<br>"  ;
    // echo "date : "        . $surveyDate ."<br>" ;
    // echo "odo reading: "  . $odoReading ."<br>"  ;
    // echo "odo type "      . $odoType     ."<br>" ;
    // echo "pre serv: "     . $preServiceRemarks   ."<br>";

    // Get initial Survey Details [Report Details]
    $notesPartsList			= $_POST['rep_dets_notes'];
    // echo "notes: " . $notesPartsList . "<br>";

    // Get other details necessary for report
    $completedByID 			= $_POST['completedByID'];
    // echo "Completed By ID: " . $completedByID;

    // Format the date for the database
    $formatedDate = date('Y-m-d H:i:s', strtotime($surveyDate));  // Format the date for MySQL

    /**
     * NOTE IF WE ARE 'COMPLETING' A REPORT WE WANT THE CURRENT LOGGED IN USER TO BE RECORDED IN THE DB AGAINTS THE SUPERVISEDBY RECORD!!! 
     * SO FAR WE ARE JUSTSAVING AND MARKING AS PENDING
     * 
     * */

    $sendEmail = "";

    if(isset($_POST["survey_send_email"])){
      $sendEmail = $_POST["survey_send_email"];
    }        
    
    // This should be NULL due to the fact that this is a pending report
    // If saving
    if(isset($_POST['save'])) {
      $status_id          = "1"; // If this script is running and save has been clicked set the status to draft
      $supervisedByID = NULL;
    }
    
    // If submitting
    if(isset($_POST['submit'])) {
      $status_id          = "2"; // If this script is running and submit has been clicked then this is a pending report so status should be '2'
      $supervisedByID = NULL;
    }

    // If completing
    if(isset($_POST['final'])) {
      // If this script is running and Mark As Final has been clicked then this is a finished report so status should be '3'
      $status_id          = "3"; 
      // Need to record the current user as the supervised by user
      $supervisedByID     = $_SESSION["user"]["user_id"];
    }


    $dateLastUpdate 		= date('Y-m-d H:i:s'); // Always stamp this with the current date
    $userIDLastUpdate   = $user->user_id; // the ID of the current logged in user regardless if a survey is being created or edited

    // Our update queries
    try {  

      $dets_update_sql = "
            UPDATE tbl_surveys
            SET            
                vehicle_type = :vehicleType,
                vehicle_reg = :vehicleReg, 
                company_ID = :companyID, 
                make_model = :makeModel,
                odo_reading = :odoReading, 
                odo_type = :odoType, 
                pre_service_remarks = :preServiceRemarks, 
                notes_parts_list = :notesPartsList, 
                completed_by_user_ID = :completedByID, 
                supervised_by_user_ID = :supervisedByID, 
                survey_date = :surveyDate, 
                status_id = :statusID, 
                date_last_update = :dateLastUpdate, 
                user_last_update = :userIDLastUpdate
            WHERE        
                survey_ID = :surveyID
      ";
      
      // Prepare the statement
      $query = $db->prepare($dets_update_sql);
      // Bind Values
      $query->bindValue(":surveyID", $surveyID, PDO::PARAM_INT);       
      $query->bindValue(":vehicleType", $vehicleType, PDO::PARAM_STR);   // Vehicle Registration
      $query->bindValue(":vehicleReg", $vehicleReg, PDO::PARAM_STR);     // Vehicle Registration
      $query->bindValue(":companyID", $companyID, PDO::PARAM_INT);       // Company ID
      $query->bindValue(":makeModel", $makeModel, PDO::PARAM_STR);       // Make/Model
      $query->bindValue(":odoReading", $odoReading, PDO::PARAM_INT);     // Odometer Reading
      $query->bindValue(":odoType", $odoType, PDO::PARAM_STR);           // Odometer Type
      $query->bindValue(":preServiceRemarks", $preServiceRemarks, PDO::PARAM_STR); // Pre service
      $query->bindValue(":notesPartsList", $notesPartsList, PDO::PARAM_STR); // Notes Parts
      $query->bindValue(":completedByID", $completedByID, PDO::PARAM_INT);   // Completed By      
      $query->bindValue(":supervisedByID", $supervisedByID, PDO::PARAM_NULL);   // Supervised By <-- NULL!!
      $query->bindValue(":surveyDate", $formatedDate, PDO::PARAM_STR);         // Survey Date
      $query->bindValue(":statusID", $status_id, PDO::PARAM_STR);         // Survey Date
      $query->bindValue(":dateLastUpdate", $dateLastUpdate, PDO::PARAM_STR); // Date Last Update
      $query->bindValue(":userIDLastUpdate", $userIDLastUpdate, PDO::PARAM_INT); // Date Last Update      
      $query->execute();
      // echo "<p><strong>Vehicles details successfully recorded!</strong></p>";
    }
    catch (Exception $e) 
    {
      echo "Data could not be retrieved from the database.";
      echo $e->getMessage();
      die;
    }


  /** ===============================================================
   * Handle the Brake Performance Section and the Tyre Thread Section
   * Only record a row for values that have been passed
   * =================================================================
   */
  
  // First DELETE all axle records in the database for the current survey    

    // echo "<p>Begin delete...</p>";
    
    try {

      $delete_axle_sql = "DELETE FROM tbl_survey_axle_responses WHERE survey_ID = :surveyID";

      // Prepare the statement
      $delete_axle_query = $db->prepare($delete_axle_sql);

      // Bind Values
      $delete_axle_query->bindValue(":surveyID", $surveyID, PDO::PARAM_INT); 

      // Execute
      $delete_axle_query->execute();

      // echo "Records deleted for current survey";

    }catch (Exception $e) {
        // echo "Data could not be retrieved from the database.";
        echo $e->getMessage();
        die;
    }

  // Next create the INSERT SQL to add the updated answers back in
  // -- we do this because it is cleaner than doing an UPDATE on the table
  
  $axle_array = array(); // Declare array
  
  // Loop through $_POST and build a new array of required responses
  foreach($_POST as $key=>$response){

  	// if a key begins with 'axle'
  	if (0 === strpos($key, 'axle')) {
   
   		//if the array element is not empty
   		if(!empty($response)){

   			// Build a new array containing only response
   			$axle_array[$key] = $response;
   		}
		}
  	
  } // end foreach

  // echo "<pre>";var_dump($axle_array);echo "</pre>";

  try {  
    $axle_update_sql = "
        UPDATE tbl_survey_axle_responses
        SET
            question_response = :questionResponse
        WHERE
            survey_ID = :surveyID
        AND
            question_ID = :questionID
        ";

    $axle_insert_sql  = "INSERT INTO `tbl_survey_axle_responses` (
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
    $axle_query = $db->prepare($axle_insert_sql);

    // Loop through the new array of axle responses
    foreach($axle_array as $key=>$response){   

      // Bind values
      $axle_query->bindValue(":responseID", null, PDO::PARAM_NULL);        
      $axle_query->bindValue(":surveyID", $surveyID, PDO::PARAM_INT); // Survey ID
      $axle_query->bindValue(":questionID", $key, PDO::PARAM_STR); // Question ID
      $axle_query->bindValue(":questionResponse", $response, PDO::PARAM_INT); // Question ID



      // Execute the query
      $axle_query->execute();
      // echo "<p><strong>Axle details successfully recorded!</strong></p>";
    } // End foreach
  } catch (Exception $e) {
        //echo "Data could not be retrieved from the database.";
        echo $e->getMessage();
        die;
  } 

  /**
   * ===============================================================
   *  First DELETE all records in the database for the current survey
   * ===============================================================
   */
    

    // echo "<p>Begin delete...</p>";
    
    try {

      $delete_sql = "DELETE FROM tbl_survey_responses WHERE survey_ID = :surveyID";

      // Prepare the statement
      $delete_query = $db->prepare($delete_sql);

      // Bind Values
      $delete_query->bindValue(":surveyID", $surveyID, PDO::PARAM_INT); 

      // Execute
      $delete_query->execute();

      // echo "Records deleted for current survey";

    }catch (Exception $e) {
        // echo "Data could not be retrieved from the database.";
        echo $e->getMessage();
        die;
    }


  /*  
   * ===============================================================
   *  Handle the Lubrication Question responses    
   *  ================================================================
   */
     

    // Loop through $_POST and build a new array of required responses
    // This excludes everything already captured
    foreach($_POST as $key=>$response):
      if (0 === strpos($key, 'veh_lub')){

        //if the array element is not empty
        if(!empty($response)){
          if($response!="ok"){ 

            // echo "<p>key: $key | resp: $response";
            // Build a new array containing only responses
            $lube_array[$key] = $response;

          } // end response not empty
        } // end if !- ok    

      } // end if begins "veh_lub"
    endforeach; // end foreach  

    // Now insert all rows pertaining to lubrication
     try {

      // echo "<p>Begin insert...</p>";

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
          $response_query->bindValue(":surveyID", $surveyID, PDO::PARAM_INT); // Survey ID
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

      // echo "New records inserted for section: lubrication";
  
  
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
      $response_query->bindValue(":surveyID", $surveyID, PDO::PARAM_INT); // Survey ID
      $response_query->bindValue(":questionID", $key, PDO::PARAM_STR); // Question ID
      $response_query->bindValue(":questionResponse", $response, PDO::PARAM_STR); // Question ID
      // Execute the query
      $response_query->execute();     
      
    }// end foreach
    //echo "<p><strong>All other responses successfully recorded!</strong></p>"  ;
  } catch (Exception $e) {
        // echo "Data could not be retrieved from the database.";
        echo $e->getMessage();
        die;
  }


// Process email if required
if($sendEmail == "yes"){

  include($_SERVER["DOCUMENT_ROOT"] . "/inc/send-mail.php");
  // die;

}
 
if(isset($_POST['submit'])){
  // if we make it this far assume all is OK and redirect to report-management
   header("Location: report-management.php?message=success"); 
         
  // Remember that this die statement is absolutely critical.  Without it, 
  // people can view your members-only content without logging in. 
 die("1. Redirecting to report-management.php"); 
}

if(isset($_POST['save'])) {

  // if we make it this far assume all is OK and redirect to report-management
   header("Location: edit-report.php?surveyid=$surveyID&message=success"); 
         
  // Remember that this die statement is absolutely critical.  Without it, 
  // people can view your members-only content without logging in. 
 die("2. Redirecting to report-management.php"); 

}


if(isset($_POST['final'])) {

  // if we make it this far assume all is OK and redirect to report-management
   header("Location: report-management.php?surveyid=$surveyID&message=final"); 
         
  // Remember that this die statement is absolutely critical.  Without it, 
  // people can view your members-only content without logging in. 
 die("3. Redirecting to report-management.php"); 

}