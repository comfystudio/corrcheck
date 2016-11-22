<?php 

function report_details($survey_id, $db){

  // This will need to be build in sections
  // Similar to how the report itself is built
  
  // Axles
  get_axle_results($survey_id, $db); 

  // Lubrication
  get_lube_results($survey_id, $db);

  // Lights
  get_lights_results($survey_id, $db);

  // Tacho
  get_tacho_results($survey_id, $db);

  // Inside Cab
  get_insidecab_results($survey_id, $db);

  // Ground Level
  get_groundlevel_results($survey_id, $db);

  // Ground Level
  get_smallservice_results($survey_id, $db);

  // Ground Level
  get_additional_results($survey_id, $db);


}



function get_axle_results($survey_id, $db){

  $query = "    
        SELECT
          tbl_survey_axle_responses.question_ID,
          tbl_survey_axle_responses.question_response
        FROM 
          tbl_survey_axle_responses
        WHERE 
          tbl_survey_axle_responses.survey_ID = $survey_id            
    ";      

  try 
  { 
      // These two statements run the query against your database table. 
      $stmt = $db->prepare($query); 
      $stmt->execute(); 
  } 
  catch(PDOException $ex) 
  { 
      // Note: On a production website, you should not output $ex->getMessage(). 
      // It may provide an attacker with helpful information about your code.  
      die("Failed to run query: " . $ex->getMessage()); 
  } 
  
  $rows = $stmt->fetchAll();  

  // PROCESS ROWS
  $refined_array = array();

  // Refine the return rows
  foreach ($rows as $row) {

    $question_id = $row["question_ID"];
    $question_resp = $row["question_response"];

    $refined_array[$question_id] = $question_resp;    

  } ?>

  <div class="results-section cf">

    <div class="results-axles-brakes results-axles">

      <h3>Brake Performance</h3>

      <table class="survey-results axle-results">

      <?php // Get just the brake performance results
      
      foreach($refined_array as $key => $value){

        if((strpos($key,"_service_bk_")!==false) || (strpos($key,"_parking_bk_")!==false)) {

           $label = create_axle_label($key); ?>
        
          <tr><td><?php echo $label; ?></td><td><?php echo $value; ?></td></tr>

        <?php }// End if string match

        }// end foreach  ?>

      </table>

    </div>

    <div class="results-axles-brakes results-axles">


        <h3>Tyre Thread</h3>

        <table class="survey-results axle-results">

        <?php // Get just the tyre thread results
        
        foreach($refined_array as $key => $value){

          if((strpos($key,"_inner_")!==false) || (strpos($key,"_outer_")!==false)) {

             $label = create_axle_label($key); ?>
          
            <tr><td><?php echo $label; ?></td><td><?php echo $value; ?></td></tr>

          <?php }// End if string match

        }// end foreach ?>

      </table>

    </div><!-- results-axles-brakes results-axles -->

</div>

<?php }// end function get_axle_results



// Create a label from a question ID - used in axle output
function create_axle_label($key){  

  // Split the string
  $pieces = explode('_', $key);    

  $label = ""; // To store new label

  foreach ($pieces as $counter => $piece) {
    
    // echo "Current word at $counter: $piece <br>";

    switch($piece){

      case("axle"):
        // echo " 'axle' found so replace ";
        $pieces[$counter] = "Axle";        
        break;

      case("service"):
        // echo " 'service' found so replace ";
        $pieces[$counter] = "Service";
        break;
      case("bk"):
        // echo " 'bk' found so replace ";
        $pieces[$counter] = "Brake";
        break;
      case("dec"):
        // echo " 'dec' found so replace ";
        $pieces[$counter] = "(DEC)";
        break;
      case("imb"):
        // echo " 'dec' found so replace ";
        $pieces[$counter] = "(IMB)";
        break;
      case("inner"):
        // echo " 'dec' found so replace ";
        $pieces[$counter] = "Inner";
        break;
      case("outer"):
        // echo " 'dec' found so replace ";
        $pieces[$counter] = "Outer";
        break;
      case("near"):
        // echo " 'dec' found so replace ";
        $pieces[$counter] = "Near";
        break;
      case("off"):
        // echo " 'dec' found so replace ";
        $pieces[$counter] = "Off";
        break;

    }// end switch

  }// end foreach

  foreach ($pieces as $piece) {
    $label .= $piece . " ";    
  }

  return $label;

} // End create_axle_label


// GET & OUTPUT LUBRICATION RESULTS
function get_lube_results($survey_id, $db){

  // Get responses
  $resp_query = "    
        SELECT question_ID, question_response 
        FROM tbl_survey_responses
        WHERE question_ID 
        LIKE 'veh_lub_%'
        and survey_ID = $survey_id
    ";      

  try 
  { 
      // These two statements run the query against your database table. 
      $stmt = $db->prepare($resp_query); 
      $stmt->execute(); 
  } 
  catch(PDOException $ex) 
  { 
      // Note: On a production website, you should not output $ex->getMessage(). 
      // It may provide an attacker with helpful information about your code.  
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
            if($response != "ok"){        
                $resp_final[$question] = $response;                   
              }// end if != ok

          } // end foreach()

    } // end if $resp_rows 


 //    echo "<pre>Original Array direct from DB";
	// var_dump($resp_rows);
	// echo "</pre>";

	// echo "<pre>Parsed Array";
	// var_dump($resp_final);
	// echo "</pre>";  
    

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

  ?>  

    <h2>Lubrication</h2>

      <table class="survey-results lube-results">

        <thead>
          <tr>
            <td class="hdr-insp-lube">Item Inspected</td>
            <td class="hdr-cond-lube">Condition</td>
            <td class="hdr-dets-lube">Details</td>            
          </tr>
        </thead>

        <?php foreach($ques_final as $key =>$question): ?>

          <tr>
            <td><?php echo $question ?></td>
            

              <?php 
                
                $q_id = $key;       
                
                // Build keys
                $key_resp = "veh_lub_" . $key;
                $key_dets = "veh_lub_" . $key . "_details";

                // Is there a fault in the array for this key?
                if(array_key_exists($key_resp, $resp_final)): ?>
                 
                  <td><strong class="issue-found significant-issue">Not OK:</strong></td>                                

                  <?php if(array_key_exists($key_dets, $resp_final)): ?>                      

                    <td><?php echo $resp_final[$key_dets]; ?></td>

                  <?php else: ?>                    

                    <td>&nbsp;</td>

                  <?php endif; ?>

                <?php else: ?>                                  

                  <td><strong class="issue-ok">OK</strong></td>
                  <td>&nbsp;</td>

                <?php endif; ?>
            
          </tr>

        <?php endforeach; ?>

      </table>


<?php
} // end function get_lube_results


/* ========================================================= */
/* BUILD THE STANDARD REPORT SECTIONS                        */ 
/* ========================================================= */


// Lights
function get_lights_results($survey_id, $db) { ?>

  <h2>Lights</h2>

<?php 

  $section_id = 5;
  $section_class = "veh_lights_";
  get_std_results($section_class, $survey_id, $section_id, $db);

}// close get_lights_results()

// Tachograph
function get_tacho_results($survey_id, $db) { ?>

  <h2>Tachograph</h2>

<?php 

  $section_id = 6;
  $section_class = "veh_tacho_";
  get_std_results($section_class, $survey_id, $section_id, $db);

}// close 


// Inside Cab
function get_insidecab_results($survey_id, $db) { ?>

  <h2>Inside Cab</h2>

<?php 

  $section_id = 7;
  $section_class = "veh_insidecab_";
  get_std_results($section_class, $survey_id, $section_id, $db);

}// close

// Ground Level
function get_groundlevel_results($survey_id, $db) { ?>

  <h2>Ground Level</h2>

<?php 

  $section_id = 8;
  $section_class = "veh_glevel_";
  get_std_results($section_class, $survey_id, $section_id, $db);

}// close

// Small Service
function get_smallservice_results($survey_id, $db) { ?>

  <h2>Small Service</h2>

<?php 

  $section_id = 9;
  $section_class = "veh_smallservice_";
  get_std_results($section_class, $survey_id, $section_id, $db);

}// close

// Additional
function get_additional_results($survey_id, $db) { ?>

  <h2>Additional</h2>

<?php 

  $section_id = 10;
  $section_class = "veh_additional_";
  get_std_results($section_class, $survey_id, $section_id, $db);

}// close




function get_std_results($section_class, $survey_id, $section_id, $db){

  $search_string = $section_class . "%";

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

    // echo "<pre>Original Array direct from DB";
    // var_dump($resp_rows);
    // echo "</pre>";

    // echo "<pre>Parsed Array";
    // var_dump($resp_final);
    // echo "</pre>";  

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

    ?>      

      <table class="survey-results <?php echo $section_class; ?>results">
        
        <thead>
          <tr>
            <td class="hdr-insp">Item Inspected</td>
            <td class="hdr-cond">Condition</td>
            <td class="hdr-dets">Details</td>
            <td class="hdr-rect">Rectified By</td>
          </tr>
        </thead>

        <?php foreach($ques_final as $key =>$question): ?>

          <tr>
            <td><?php echo $question ?></td>            

              <?php 
                
                $q_id = $key;       
                
                // Build keys
                $key_resp     = $section_class . $key;                      // e.g. veh_lights_44
                $key_rect_by  = $section_class . "rectified_by_" . $key;   // e.g. veh_lights_rectified_by_44
                $key_dets     = $section_class . "details_" . $key;         // e.g. veh_lights_details_44

                // Check if the key_exists in the array - this means a fault has been recorded
                if(array_key_exists($key_resp, $resp_final)): ?>

                  <?php echo showStdSectionResponse($resp_final[$key_resp]); ?>

                  <?php

                    // If details are in the array
                    if(array_key_exists($key_resp, $resp_final)): ?>

                      <td><?php echo $resp_final[$key_dets]; ?></td>

                    <?php else: ?>
                      <td>&nbsp;</td>
                    <?php endif;                     

                    // If details for the fault exist in the array then out put them
                    if(array_key_exists($key_rect_by, $resp_final)): ?>

                      <?php // need some code here to return the id of the user; ?>

                      <td><?php echo get_user_username($resp_final[$key_rect_by], $db); ?></td>

                    <?php else: ?>
                      <td>&nbsp;</td>
                    <?php endif;                     

                  ?>

                <?php else: ?>

                  <td><strong class="issue-ok">Satisfactory</strong></td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>                  

                <?php endif; // close that initial if array_key_found ?>   
            
          </tr>

        <?php endforeach; ?>

      </table>


<?php

} // close standard section



function getIntFromString($string){

  $int = filter_var($string, FILTER_SANITIZE_NUMBER_INT);
  return $int;   
 
} // Close function getIntFromString

// Take the response key and return human readable string for value
function showStdSectionResponse($value){

  $html = "";

  switch($value){

    case "significant_defect":
      $html = "<td><strong class='significant-issue'>Significant Defect</strong></td>";      
      break;

    case "slight_defect":
      $html = "<td><strong class='slight-issue'>Slight Defect</strong></td>";      
      break;

    case "not_applicable":
      $html = "<td><strong class='issue-not-applicable'>Not Applicable</strong></td>";      
      break;
  }

  return $html;

} // End function: showStdSectionResponse()




