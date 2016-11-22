<?php

/**
 * Used to send the final email to client where the option is selected.
 * This uses mail() for now but might need to look at using phpmailer if getting bouncebacks
 * 
 */


// Gat and Format the date   
    $date = DateTime::createFromFormat('d-m-Y', $surveyDate);      
    $formatedDate = $date->format('j F Y'); // Format: 21 January 2015


// Inspection Details
$emailbody = "<h2>Inspection Details</h2>";
$emailbody .= "<table width='40%' style='border-top:1px solid #cfcfcf; border-left:1px solid #cfcfcf; '>";

// ID
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;' width='50%'><strong>Survey ID:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;' width='50%'>"       		. $surveyID 		."</td>
</tr>";

// Type
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Vehicle Type:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>"     	. $vehicleType 		."</td>
</tr>";

// Reg
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Vehicle Registration:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>" .  $vehicleReg 		."</td>
</tr>";

// Make/Model
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Make/Model:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>"   	. $makeModel 		."</td>
</tr>";

// Date
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Inspection Date:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>"      . $surveyDate 		."</td>
</tr>";

// Odo Meter Reading
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Odometer Reading:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>"  	. $odoReading 		. " " . $odoType . "</td>
</tr>";

// Pre service remarks
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Pre-service Remarks:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>"  . $preServiceRemarks   ."</td>
</tr>";

// Notes/Parts List
$emailbody .= "<tr>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Notes/Part:</strong></td>
<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>"     		. nl2br($notesPartsList)   ."</td>
</tr>";

$emailbody .= "</table>";


/* ============================================================*/
// Axles (Brake Performance)
/* ============================================================*/

$emailbody .= "<h2>Brake Performance</h2>";
$emailbody .= "<table width='40%' style='border-top:1px solid #cfcfcf; border-left:1px solid #cfcfcf; '>";
$bkpf_counter = 0;

// var_dump($axle_array);
foreach($axle_array as $key => $value){

	if((strpos($key,"_service_bk_")!==false) || (strpos($key,"_parking_bk_")!==false)) {

			$bkpf_counter++;

          	$label = create_axle_label($key); 
        
         	$emailbody .= "<tr>
          					<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;' width='50%'> $label </td>
          					<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;' width='50%'>  $value</td></tr>
          				</tr>";

    	}// End if string match

} // end foreach 

if($bkpf_counter == 0){
	$emailbody .= "<tr><td colspan=2 style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>";
	$emailbody .= "No Brake Performance Recorded.";
	$emailbody .= "</td></tr>";
} 

$emailbody .= "<tr></table>";

// Axles (Tyre Thread)
$emailbody .= "<h2>Tyre Thread</h2>";
$emailbody .= "<table width='40%' style='border-top:1px solid #cfcfcf; border-left:1px solid #cfcfcf; '>";
$tyth_counter = 0;

foreach($axle_array as $key => $value){

	if((strpos($key,"_inner_")!==false) || (strpos($key,"_outer_")!==false)) {

			$tyth_counter++;

          	$label = create_axle_label($key); 
        
         	$emailbody .= "<tr>
          					<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;' width='25%'> $label </td>
          					<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>  $value</td></tr>
          				</tr>";

    	}// End if string match

} // end foreach  

if($tyth_counter == 0){
	$emailbody .= "<tr><td colspan=2 style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>";
	$emailbody .= "No Tyre Thread Recorded.";
	$emailbody .= "</td></tr>";
}

$emailbody .= "<tr></table>";


  // ============================================
  // Get questions from db for lube section
  // ============================================
  
    $ques_query = "    
        SELECT * 
        FROM tbl_questions
        WHERE section_ID = 4
    ";      

  try 
  { 
      // These two statements run the query against your database table. 
      $stmt = $db->prepare($ques_query); 
      $stmt->execute(); 
  } 
  catch(PDOException $ex) 
  { 
      // Note: On a production website, you should not output $ex->getMessage(). 
      // It may provide an attacker with helpful information about your code.  
      die("Failed to run query: " . $ex->getMessage()); 
  } 

  // Save db results
  $ques_rows = $stmt->fetchAll(); 

  // Parse array questions 
  $ques_final = array();

  foreach($ques_rows as $row => $array){

    $question_id = $array["question_ID"];    
    $question_text = $array["question_text"];        

    if($response != "ok"){        
        $ques_final[$question_id] = $question_text;
      }
  } // end foreach 

  /* ======================================= */
  /* Render the output */
  /* ======================================= */

	$emailbody .= "<h2>Lubrication</h2>";

	$emailbody .= "<table width='100%' style='border-top:1px solid #cfcfcf; border-left:1px solid #cfcfcf; '>";
	$emailbody .= '<thead>
			          <tr>
			            <td width="20%" style="border-bottom:1px solid #242424; border-right:1px solid #242424; background-color:#242424; color:white; padding:3px 2px">Item Inspected</td>
			            <td width="20%" style="border-bottom:1px solid #242424; border-right:1px solid #242424; background-color:#242424; color:white; padding:3px 2px">Condition</td>
			            <td width="20%" style="border-bottom:1px solid #242424; border-right:1px solid #242424; background-color:#242424; color:white; padding:3px 2px">Details</td>            
			          </tr>
			        </thead>';

	foreach($ques_final as $key =>$question){

		$emailbody .= "<tr>";

		$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>$question</td>";

			// Build keys
            $key_resp = "veh_lub_" . $key;
            $key_dets = "veh_lub_" . $key . "_details";

            // Is there a fault in the array for this key?
            if(array_key_exists($key_resp, $lube_array)) {

            	$emailbody .= '<td style="border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;"><strong style ="color:red">Not OK:</strong></td>';

            	// As an issue has been found, output recorded details
            	if(array_key_exists($key_dets, $lube_array)){

            		$emailbody .= '<td style="border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;">' . $lube_array[$key_dets] . '</td>';

            	} else {

            		$emailbody .= '<td>&nbsp;</td>';

            	}
            } else {
            	$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong style='color:green'>OK</strong></td>";
                $emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>N/A</td>";
            } 

		$emailbody .= "</tr>";

	}// end foreach

$emailbody .= "</table>";


/* ========================================================= */
/* BUILD THE STANDARD REPORT SECTIONS                        */ 
/* ========================================================= */

$emailbody .= "<h2>Lights</h2>";
$section_id = 5;
$section_class = "veh_lights_";
$emailbody .= email_std_results($section_class, $surveyID, $section_id, $db);

$emailbody .= "<h2>Tachograph</h2>";
$section_id = 6;
  $section_class = "veh_tacho_";
$emailbody .= email_std_results($section_class, $surveyID, $section_id, $db);

$emailbody .= "<h2>Inside Cab</h2>";
$section_id = 7;
$section_class = "veh_insidecab_";
$emailbody .= email_std_results($section_class, $surveyID, $section_id, $db);

$emailbody .= "<h2>Ground Level</h2>";
$section_id = 8;
$section_class = "veh_glevel_";
$emailbody .= email_std_results($section_class, $surveyID, $section_id, $db);

$emailbody .= "<h2>Small Service</h2>";
$section_id = 9;
$section_class = "veh_smallservice_";
$emailbody .= email_std_results($section_class, $surveyID, $section_id, $db);

$emailbody .= "<h2>Additional</h2>";
$section_id = 10;
$section_class = "veh_additional_";
$emailbody .= email_std_results($section_class, $surveyID, $section_id, $db);

$emailbody .= "<p style='text-align:center;'>********* REPORT ENDS **********</p>";

// echo $emailbody; die;

$headers = "Content-Type: text/html; charset=UTF-8\n";
$headers .= "From:enquiries@corrbrothers.com\n";
$headers .= "X-Mailer: PHP/" . phpversion();



// SUBJECT: Inspection Details for KN 02 D 85738 inspected on 21 January 2015 – 20104 kms
$subject = "Inspection Details for " . $vehicleReg . " inspected on " . $formatedDate . " - " . $odoReading . " " . $odoType ;

// $to = "accounts@corrbrothers.co.uk";
// $to = "gareth@websiteni.com, accounts@corrbrothers.co.uk";
//$to = "gareth@websiteni.com";

$to = getEmailAddrs($companyID, $db);


mail($to, $subject, $emailbody, $headers);


/* ========================================================== */
/* FUNCTIONS FOR SENDING EMAILS
/* ========================================================== */


// The only function required for this page - 
function email_std_results($section_class, $survey_id, $section_id, $db){

	$emailbody = "";

	$search_string = $section_class . "%";
	// echo $search_string;

	// Get responses  
	$resp_query = "    
	    SELECT question_ID, question_response 
	    FROM tbl_survey_responses
	    WHERE question_ID 
	    LIKE '$search_string'
	    and survey_ID = $survey_id
	";  
	// echo $resp_query;
	
	try 
    {         
        $stmt = $db->prepare($resp_query); 
        $stmt->execute(); 
    } 
    catch(PDOException $ex) 
    {       
        die("Failed to run query: " . $ex->getMessage()); 
    } 

    // Responses queried from db
    $resp_rows = $stmt->fetchAll(); 

    // Create empty array for parsed responses  
    $resp_final = array(); 

    // If results are found
    if($resp_rows){

          // Parse the resp_rows array
          $resp_final = array();

          foreach($resp_rows as $row => $array){

            $question = $array["question_ID"]; // NB: This is the ID of the element
            $response = $array["question_response"]; 
            
            // If a result is not ok (which is all we want)
            if($response != "satisfactory"){        
                $resp_final[$question] = $response;                   
              }// end if != ok

          } // end foreach()

    } // end if $resp_rows 

    // ============================================
    // Get questions from db for section
    // ============================================
    
    $ques_query = "    
        SELECT * 
        FROM tbl_questions
        WHERE section_ID = $section_id
    ";      

    try 
    { 
        // These two statements run the query against your database table. 
        $stmt = $db->prepare($ques_query); 
        $stmt->execute(); 
    } 
    catch(PDOException $ex) 
    { 
        // Note: On a production website, you should not output $ex->getMessage(). 
        // It may provide an attacker with helpful information about your code.  
        die("Failed to run query: " . $ex->getMessage()); 
    } 

    // Save db results
    $ques_rows = $stmt->fetchAll(); 
    // var_dump($ques_rows);
    
    // Parse array questions 
    $ques_final = array();

    foreach($ques_rows as $row => $array){

      $question_id = $array["question_ID"];    
      $question_text = $array["question_text"];        

      if($response != "satisfactory"){        
          $ques_final[$question_id] = $question_text;
        }
    } // end foreach

    /* ======================================= */
    /* Render the output */
    /* ======================================= */
    
    $emailbody .= "<table width='100%' style='border-top:1px solid #cfcfcf; border-left:1px solid #cfcfcf; '>";

	$emailbody .= '<thead>
			          <tr>
			            <td width="20%" style="border-bottom:1px solid #242424; border-right:1px solid #242424; background-color:#242424; color:white; padding:3px 2px">Item Inspected</td>
			            <td width="20%" style="border-bottom:1px solid #242424; border-right:1px solid #242424; background-color:#242424; color:white; padding:3px 2px">Condition</td>
			            <td width="20%" style="border-bottom:1px solid #242424; border-right:1px solid #242424; background-color:#242424; color:white; padding:3px 2px">Details</td>
			            <td width="20%" style="border-bottom:1px solid #242424; border-right:1px solid #242424; background-color:#242424; color:white; padding:3px 2px">Rectified By</td>
			          </tr>
			        </thead>';

	foreach($ques_final as $key =>$question){

		$emailbody .= "<tr>";
		$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'> $question </td>";

		// Build keys
        $key_resp     = $section_class . $key;                      // e.g. veh_lights_44
        $key_rect_by  = $section_class . "rectified_by_" . $key;   // e.g. veh_lights_rectified_by_44
        $key_dets     = $section_class . "details_" . $key;         // e.g. veh_lights_details_44

        // Check if the key_exists in the array - this means a fault has been recorded
        if(array_key_exists($key_resp, $resp_final)){        	
        	
        	$emailbody .= emailStdSectionResponse($resp_final[$key_resp]);        	

        	$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>";
        	$emailbody .= $resp_final[$key_dets];
        	$emailbody .= "</td>";

        	if(array_key_exists($key_rect_by, $resp_final)){

        		$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>";
	        	$emailbody .= get_user_username($resp_final[$key_rect_by], $db);
	        	$emailbody .= "</td>";

	        } else {
	        	$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>&nbsp;</td>";
			} // Close array_key_exists: $key_rect_by, $resp_final

        } else {

        	$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong style='color:green'>Satisfactory</strong></td>";
        	$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>&nbsp;</td>";
        	$emailbody .= "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'>&nbsp;</td>";

        } // Close array_key_exists: $key_resp, $resp_final

		$emailbody .= "</tr>";

	} // Close foreach    
    
    $emailbody .= '</table>';

    return $emailbody;

} // End function: email_std_results()

function emailStdSectionResponse($value){

  $html = "";

  switch($value){

    case "significant_defect":
      $html = "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong style='color:red'>Significant Defect</strong></td>";      
      break;

    case "slight_defect":
      $html = "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong style='color:FF9900'>Slight Defect</strong></td>";      
      break;

    case "not_applicable":
      $html = "<td style='border-bottom:1px solid #cfcfcf; border-right:1px solid #cfcfcf;'><strong>Not Applicable</strong></td>";      
      break;
  }

  return $html;

} // End function: showStdSectionResponse()


// This will return a string of email addresses in the correct format
function getEmailAddrs($companyID, $db){

  $to_string = "accounts@corrbrothers.co.uk, enquiries@corrbrothers.co.uk, ";

  $query = "
      SELECT
        email, email_2, email_3, email_4, email_5,
        email_6, email_7, email_8, email_9, email_10        
      FROM 
        tbl_companies
      WHERE 
        company_ID = $companyID
      ";
   
  try 
  { 
      // Run the query against your database table. 
      $stmt = $db->prepare($query); 
      $stmt->execute(); 
      // echo "success on THIS query! ";
  } 
  catch(PDOException $ex) 
  { 
      // Note: On a production website, you should not output $ex->getMessage(). 
      // It may provide an attacker with helpful information about your code.  
      //echo " no success on query!";
      die("Failed to run query: " . $ex->getMessage()); 
  } 

  $row = $stmt->fetch();    

  foreach($row as $email){
    $to_string .= "$email, ";
  }    

  // Tidy the string
  $to_string = rtrim($to_string, ', ');
  return $to_string;

  die;

}



?>