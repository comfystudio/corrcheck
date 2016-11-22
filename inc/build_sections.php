<?php

/**
 * All functions required to build the actual report
 * 
 */

/**
 * This function builds the actual vehicle survey
 * @param  [object] $user The current logged in user object
 * @return [type]       [description]
 */
function create_corrCheck(){

  require(ROOT_PATH . "inc/conn.php");	?>

  <form class="corrCheck_form form-horizontal" role="form" method="post" action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'> 

	<?php

  // Build section 1 of form: Vehicle Details
	create_corrCheck_Vehicle_Details($db);

  // Build section 2 of form: Brake Performance
  create_corrCheck_Brake_Perf($db);

  // Build section 3 of form: Brake Tyre Thread Remaining
  create_corrCheck_Tyre_Thread($db);
  
  // Build section 4 of form: Lubrication
  create_corrCheck_Lub($db);
  
  // Build section 5 of form: Lights (standard section)
  create_corrCheck_Lights($db);

  // Build section 6 of form: Tachograph / Speed Limiter (standard section)
  create_corrCheck_Tachograph($db);

  // Build section 7 of form: Inside Cab (standard section)
  create_corrCheck_InsideCab($db);

  // Build section 8 of form: Ground Level (standard section)
  create_corrCheck_GroundLevel($db);    

  // Build section 9 of form: Small Service (standard section)
  create_corrCheck_SmallService($db);    

  // Build section 10 of form: Additional (Road test)
  create_corrCheck_Additional($db);

  // Build section 11 of form: Inspection Report Details
  create_corrCheck_Rep_Details($db);              

  // Build section 12 of form: Survey Summary
  create_corrCheck_Summary($db);

  ?>  

  <button id="submit" name="submit" type="submit" class="btn btn-success">Mark Report As Pending</button>
  <button id="save" name="save" type="submit" class="btn btn-warning">Save Report As Draft</button>

  <form>

<?php } // close create_corrCheck()


/* ********************************************** */
/* SECTION 1 VEHICLE DETAILS                      */
/* ********************************************** */ 

/**
 * Builds the vehicle details section of the vehicle check.
 * Has a few more hard coded options than other sections.
 * @return [type] [description]
 */
function create_corrCheck_Vehicle_Details($db){	?>

<fieldset class="veh_dets rep-section">

  <h2>Vehicle Details</h2>

  <div class="section-questions">
	
<?php   

  // where section_ID = 1 
	try {
	  $results = $db->query("
	    SELECT 
        tbl_questions.*, 
        tbl_question_types.type_name as question_type_name
      FROM tbl_questions 
      LEFT OUTER JOIN tbl_question_types
        ON tbl_questions.type_ID=tbl_question_types.type_ID
      WHERE tbl_questions.section_ID = 1
        ORDER BY question_seqno ASC
      ;               
	    ");
	} catch (Exception $e) {
	    echo "Data could not be retrieved from the database.";
	    exit;
	}

  // Loop through result set
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {

    // echo "<pre>";
    // var_dump($row);

    $question_text = $row["question_text"]; 
    $question_id   = $row["question_ID"];  
    $question_name = "veh_dets_".$question_id;
    $question_type = $row["question_type_name"];  
    $ind_required = $row["ind_required"];

    ?>
    
    <div class="question_row cf form-group">
      <label for="<?php echo $question_name; ?>"  class="col-sm-3 control-label"><?php echo $question_text; ?>:</label>
      <div class="col-sm-4">

      	<?php

        // If questions is: vehicle type
        if($question_id==11){ ?>
          <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control">
            <option value="lorry">Lorry</option>
            <option value="trailer">Trailer</option>
          </select>

        <?php }
        elseif($question_id==13) { // If question is: ?>  
          <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control">
          <?php 

            // Get companies
            $companies = get_company_names($db);  

            foreach($companies as $company_id => $company):              
              $company_name = $company["company_name"]; ?>

              <option value="<?php echo $company_id; ?>"><?php echo $company_name; ?></option>              

            <?php endforeach; ?>

          </select>

        <?php }  

        elseif($question_type == "Date"){ // If question is a date field ?>   
          <input type="input" class="form-control date-input form-control datepicker" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" data-date-format="dd-mm-yyyy" value="<?php echo date("d-m-Y"); ?>" required>   
        <?php }

        elseif($question_type == "Number"){ // If question is a number field ?>
          <input type="input" class="form-control number-input form-control" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" required>                  
        
        <?php }

        elseif($question_type == "Dropdown"){ // If question is a dropdown field ?>  

          <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control">

            <option value="km">KM</option>
            <option value="miles">Miles</option>
            <option value="hours">Hours</option>

          </select>

        <?php }

        elseif($question_type == "Text"){ // Default text input ?>
          <input type="text" class="form-control form-control" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" required>  
        <?php } 

        elseif($question_type == "Textarea"){ ?>          
          <textarea name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" rows="6" class="form-control"></textarea>
        <?php } ?>

      </div><!-- close col-sm-10 -->

    </div><!-- close question -->

  <?php  }	// close while results ?>

</div><!-- close section-questions -->

</fieldset>

<?php } // close create_corrCheck_Vehicle_Details



/* ********************************************** */
/* SECTION 2 BRAKE PERFORMANCE                    */
/* ********************************************** */

/*
 * This section requires 40 fields so rather than add these to the database
 * they will be built dynamically here.
 * The answers will be written to the database in a table called tbl_brake_performces.
 * 
 * The table will hold 3 columns: Report ID | Questions Name | Value (can be null)
 * Report ID - obtained when report is created
 * Question Name | Dynamically Generated Here
 * Value | Entered by users
 *
 * The table can then be queried when editing a report
 * 
 */
function create_corrCheck_Brake_Perf($db) { ?>

  <fieldset class="bk_perf rep-section">

    <h2>Brake Performance</h2>

    <div class="panel-group" id="accordion">

    <?php

    $axle_no = 1;

    for($count=1;$count<11;$count++): ?>

    <!-- start panel -->
    <div class="panel panel-primary">
    
    	<!-- panel heading -->
    	<div class="panel-heading">
    		<h2 class="panel-title">
    			<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $count; ?>">
    				Axle <?php echo $count; ?>
    			</a>
    		</h2>
    	</div><!-- end heading -->


	    <div id="collapse<?php echo $count; ?>" class="bk_perf_axle_<?php echo $axle_no; ?> panel-collapse collapse">
	    	<div class="panel-body">	    		

	      <div class="form-group">
	        <label for="axle_<?php echo $axle_no; ?>_service_bk_dec" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> Service Brake DEC(%)</label>
	        <div class="col-sm-4">
	        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_service_bk_dec" id="axle_<?php echo $axle_no; ?>_service_bk_dec"> 
	        </div>
	      </div><!-- row -->

	      <div class="form-group">
	        <label for="axle_<?php echo $axle_no; ?>_service_bk_imb" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> Service Brake IMB(%)</label>
	        <div class="col-sm-4">
	        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_service_bk_imb" id="axle_<?php echo $axle_no; ?>_service_bk_imb">  
	        </div>
	        
          
	      </div>

	      <div class="form-group">
	        <label for="axle_<?php echo $axle_no; ?>_parking_bk_dec" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> Parking Brake DEC(%)</label>
	        <div class="col-sm-4">
	        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_parking_bk_dec" id="axle_<?php echo $axle_no; ?>_parking_bk_dec">
	        </div>
	        
                               
	      </div>

	      <div class="form-group">
	        <label for="axle_<?php echo $axle_no; ?>_parking_bk_imb" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> Parking Brake IMB(%)</label>
	        <div class="col-sm-4">
	        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_parking_bk_imb" id="axle_<?php echo $axle_no; ?>_parking_bk_imb">       
	        </div>
	      </div>

	      <div class="alert alert-info" role="alert">
        	<strong>NOTE:</strong> Any items left blank will not be submitted as part of report.
      	</div>

	    <?php $axle_no++; ?>
	      
	    </div><!-- panel-body -->
	   </div><!-- panel-collapse -->

	</div><!-- close panel -->

    <?php endfor; ?>

	</div><!-- close panel-group -->

  </fieldset>

<?php } // close create_corrChech_Brake_Perf

/* ********************************************** */
/* SECTION 3 TYRE THREAD REMAINING                */
/* ********************************************** */

function create_corrCheck_Tyre_Thread($db){ ?>

   <fieldset class="section_tyre_thread rep-section">

    <h2>Tyre Thread Remaining</h2>

    <div class="panel-group" id="accordion">

    <?php

    $axle_no = 1;

    for($count=1;$count<11;$count++): ?>

    <!-- start panel -->
    <div class="panel panel-primary">

    	<!-- panel heading -->
    	<div class="panel-heading">
    		<h2 class="panel-title">
    			<a data-toggle="collapse" data-parent="#accordion" href="#tyre-thread-collapse-<?php echo $count; ?>">    
    				Axle <?php echo $count; ?>
	    		</a>
    		</h2>
    	</div><!-- close heading -->

    	<div id="tyre-thread-collapse-<?php echo $count; ?>" class="bk_perf_axle_<?php echo $axle_no; ?> panel-collapse collapse">
	    	<div class="panel-body">

			    <div class="tyre_thread_axle_<?php echo $axle_no; ?>">
			      
			      <?php // ********** Axle 1 INNER Near Side (mm) ?>
			      <div class="form-group">
			        
			        <label for="axle_<?php echo $axle_no; ?>_inner_near" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> INNER Near Side (mm)</label>	
			        <div class="col-sm-4">		        
			        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_inner_near" id="axle_<?php echo $axle_no; ?>_inner_near"> 			        
			        </div>
			                
			      </div>

			      <?php // ********** Axle 1 INNER Off Side (mm) ?>
			      <div class="form-group">
			        
			        <label for="axle_<?php echo $axle_no; ?>_inner_off" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> INNER Off Side (mm)</label>
			        <div class="col-sm-4">			        
			        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_inner_off" id="axle_<?php echo $axle_no; ?>_inner_off"> 			        
			        </div>
			           
			      </div>


			      <?php // ********** Axle 1 OUTER Near Side (mm) ?>
			      <div class="form-group">
			        
			        <label for="axle_<?php echo $axle_no; ?>_outer_near" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> OUTER Near Side (mm)</label>
			        <div class="col-sm-4">			        
			        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_outer_near" id="axle_<?php echo $axle_no; ?>_outer_near"> 			        
			        </div>
			      
			      </div>

			      <?php // ********** Axle 1 OUTER Off Side (mm) ?>
			      <div class="form-group">
			        
			        <label for="axle_<?php echo $axle_no; ?>_outer_off" class="col-sm-3 control-label">Axle <?php echo $axle_no ?> OUTER Off Side (mm)</label>
			        <div class="col-sm-4">			        
			        	<input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_outer_off" id="axle_<?php echo $axle_no; ?>_outer_off"> 			        
			        </div>
			       
			      </div>

			      <div class="alert alert-info" role="alert">
		        	<strong>NOTE:</strong> Any items left blank will not be submitted as part of report.
		      	</div>

    			<?php $axle_no++; ?>

    		</div><!-- panel-body -->
	   </div><!-- panel-collapse -->
      
    </div>

    </div><!-- close panel -->

    <?php endfor; ?>

  	</div><!-- close panel-group -->

  </fieldset>

<?php }

/* ********************************************** */
/* SECTION 4 LUBRICATION                          */
/* ********************************************** */

function create_corrCheck_Lub($db){ ?>

  <fieldset class="section_lub rep-section">

    <h2>Lubrication</h2> 

    <div class="section-questions">

    <?php

      $section_id = 4;
      $section_class="veh_lub";

      // Get questions for section
      // echo "we will get questions for section: " . $section_id . " ";

	    try {
	      $results = $db->query("
	        SELECT 
	          tbl_questions.*, 
	          tbl_question_types.type_name as question_type_name
	        FROM tbl_questions 
	        LEFT OUTER JOIN tbl_question_types
	          ON tbl_questions.type_ID=tbl_question_types.type_ID
	        WHERE tbl_questions.section_ID = " . $section_id . "
	          ORDER BY question_seqno ASC
	        ;               
	        ");
	    } catch (Exception $e) {
	      echo "Data could not be retrieved from the database.";
	      exit;
	    }

    // var_dump($results);

    while ($row = $results->fetch(PDO::FETCH_ASSOC)): 

      // echo "<pre>"; var_dump($row); echo "</pre>"; 

      $question_text = $row["question_text"]; 
      $question_id   = $row["question_ID"];  
      $question_name = $section_class."_".$question_id;
      $question_type = $row["question_type_name"];  
      $ind_trailers = $row["ind_trailers"];    

      $ind_required = $row["ind_required"];

      ?>

      <div class="form-group<?php if($ind_trailers == "N") echo " not_trailers"; ?>">
        <label for="<?php echo $question_name; ?>" class="col-sm-3 control-label"><?php echo $question_text; ?></label>

        <div class="col-sm-2">
        <?php

          // Assess and output questions type
          switch($question_type) {
            
            // if Text
            case "Text": ?>
              <input type="text" class="form-control" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" >  
            <?php break;

            case "Dropdown":  ?>    

                <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control">
                  <option value="ok">OK</option>
                  <option value="not-ok">Not OK</option>
                </select>

            <?php break;

          } // end the switch         

        ?>
      	</div><!-- close control col -->

        <!-- the details option for the question -->
        <div class="group_details">
          <label for="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" class="col-sm-1 control-label">Details: </label>
            
            <div class="col-md-5">
             <input type="input" class="<?php echo $section_class; ?>_details_<?php echo $question_id; ?> form-control" id="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" length="100" maxlength="100" name="<?php echo $question_name ?>_details" id="<?php echo $question_name ?>">     
            </div>
        </div>


      </div> <!-- close form-group -->
      

    <?php endwhile; ?>

  </div><!-- section-questions -->

  </fieldset>

<?php } // close create_corrCheck_Lub()

/* ********************************************** */
/* SECTION 5 LIGHTS                               */
/* ********************************************** */

function create_corrCheck_Lights($db){ ?>

   <fieldset class="section_lights rep-section section_std">

    <h2>Lights</h2> 

    <div class="section-questions">

    <?php 

      $section_id = 5;
      $section_class= "veh_lights";
      get_standard_section($db, $section_id, $section_class);

    ?>

  	</div>

  </fieldset>

<?php } // close create_corrCheck_Lights()

/* ********************************************** */
/* SECTION 6 TACHHOGRAPH                          */
/* ********************************************** */

function create_corrCheck_Tachograph($db){ ?>

   <fieldset class="section_tacho rep-section section_std">

    <h2>Tachograph / Speed Limiter</h2>

    <div class="section-questions">     

    <?php 

      $section_id = 6;
      $section_class= "veh_tacho";
      get_standard_section($db, $section_id, $section_class);

    ?>

  	</div>

  </fieldset>

<?php } // close create_corrCheck_Tachograph($db)

/* ********************************************** */
/* SECTION 7 INSIDE CAB                           */
/* ********************************************** */

function create_corrCheck_InsideCab($db){ ?>

   <fieldset class="section_insdecab rep-section section_std">

    <h2>Inside Cab</h2>     

    <div class="section-questions">

    <?php 

      $section_id = 7;
      $section_class= "veh_insidecab";
      get_standard_section($db, $section_id, $section_class);

    ?>

  </div>

  </fieldset>

<?php } // close create_corrCheck_Tachograph($db)

/* ********************************************** */
/* SECTION 8 GROUND LEVEL                           */
/* ********************************************** */

function create_corrCheck_GroundLevel($db){ ?>

   <fieldset class="section_groundlevel rep-section section_std">

    <h2>Ground Level / Under Vehicle</h2> 

    <div class="section-questions">
    
    <?php 

      $section_id = 8;
      $section_class= "veh_glevel";
      get_standard_section($db, $section_id, $section_class);

    ?>

  	</div>

  </fieldset>

<?php } // close create_corrCheck_Tachograph($db)

/* ********************************************** */
/* SECTION 9 SMALL SERVICE                        */
/* ********************************************** */

function create_corrCheck_SmallService($db){ ?>

   <fieldset class="section_smallservice rep-section section_std">

    <h2>Small Service</h2>

    <div class="section-questions">

    <?php 

      $section_id = 9;
      $section_class= "veh_smallservice";
      get_standard_section($db, $section_id, $section_class);

    ?>

  	</div>

  </fieldset>

<?php } // close create_corrCheck_Tachograph($db)

/* ********************************************** */
/* SECTION 10 ADDITIONAL                          */
/* ********************************************** */

function create_corrCheck_Additional($db){ ?>

   <fieldset class="section_additional rep-section section_std">

    <h2>Additional (Road Test)</h2>    

    <div class="section-questions">

    <?php 

      $section_id = 10;
      $section_class= "veh_additional";
      get_standard_section($db, $section_id, $section_class);

    ?>

  </div>

  </fieldset>

<?php } // close create_corrCheck_Tachograph($db)

/* ********************************************** */
/* SECTION 11 REPORT DETAILS                      */
/* ********************************************** */

/**
 * Originally this section included Inspection completed by and inspection supervised by fields but they were dropped for the following reasons
 * 1. The person currently logged in is the user completing the inspection, therefore this does not need to be stated as it can be obtained from the session
 * 2. The person who marks the report as completed will need to log in and do so, therefore this user will also be recorded via the session 
 * As such we only need one question in this final section - the Notes/Parts List
 * 
 * @param  [type] $db   [description]
 * @param  [type] $user [description]
 * @return [type]       [description]
 */
function create_corrCheck_Rep_Details($db){ ?>

  <?php // var_dump($_SESSION["user"]); ?>

   <fieldset class="section_rep_details rep-section">

    <h2>Inspection Report Details</h2>

    <div class="section-questions">

    <?php 

      // // where section_ID = 1 
      // try {
      //   $results = $db->query("
      //     SELECT 
      //       tbl_questions.*, 
      //       tbl_question_types.type_name as question_type_name
      //     FROM tbl_questions 
      //     LEFT OUTER JOIN tbl_question_types
      //       ON tbl_questions.type_ID=tbl_question_types.type_ID
      //     WHERE tbl_questions.section_ID = 11
      //       ORDER BY question_seqno ASC
      //     ;               
      //     ");
      // } catch (Exception $e) {
      //     echo "Data could not be retrieved from the database.";
      //     exit;
      // }

      // Loop through result set
      // while ($row = $results->fetch(PDO::FETCH_ASSOC)):

      //   $question_text = $row["question_text"]; 
      //   $question_id   = $row["question_ID"];  
      //   $question_name = "veh_rep_dets_".$question_id;
      //   $question_type = $row["question_type_name"];

        ?>

          <div class="form-group cf">
            <label for="rep_dets_notes" class="col-sm-3 control-label">Notes / Parts List:</label> 
            <div class="col-sm-9">            

              <textarea name="rep_dets_notes" id="rep_dets_notes" rows="6" class="form-control"></textarea>

          	</div><!-- close control col -->
          </div><!-- close form group -->        

      </div><!-- close section-questions -->      

  </fieldset>

<?php } // close create_corrCheck_Tachograph($db)

/* ********************************************** */
/* SECTION 12 SUMMARY                             */
/* ********************************************** */

function create_corrCheck_Summary($db){ ?>

   <fieldset class="section_summary rep-section">

    <h2>Summary</h2>    

    <div class="section-questions">

    <ul>
      <li>Vehicle Type: <span class="sum_veh_type"></span> </li>
      <li>Vehicle Reg: <span class="sum_veh_reg"></span> </li>
      <li>Company: <span class="sum_co_name"></span> </li>
      <li>Make/Model: <span class="sum_make_model"></span> </li>
      <li>Date: <span class="sum_sur_date"></span> </li>
      <li>Odometer: <span class="sum_odo_rd"></span> <span class="sum_odo_type"></span></li>
      <li>Pre-service: <span class="sum_ps_rmks"></span> </li>
      <li>Notes / Parts List: <span class="notes_pts_list"></span> </li>
    </ul>

    
    <div class="sum_bk_perf"></div>
    <div class="sum_tyre_thread"></div>
    <div class="sum_lubrication"></div>

    <!-- standard sections -->
    <div class="sum_lights"></div>
    <div class="sum_tacho"></div>
    <div class="sum_inside_cab"></div>
    <div class="sum_grd_level"></div>
    <div class="sum_sm_service"></div>
    <div class="sum_additional"></div>

    </div>

   </fieldset>



<?php }


 
 /**
 * Return array of Users names and ids for Corr Bros Employees only
 * @param  [type] $db connection obect
 * @return [type]     array containing company name
 */
function get_all_corr_users($db){
  //echo "we will do something now with companies";

  try {
    $results = $db->query("
      SELECT
        user_id,
        username,
        first_name,
        last_name
      FROM tbl_users
      WHERE company_id = 2
      ;               
      ");
  } catch (Exception $e) {
      echo "Data could not be retrieved from the database.";
      exit;
  }

  $users = array();

  while ($row = $results->fetch(PDO::FETCH_ASSOC)):

    // var_dump($row);
    
    $user_id = $row["user_id"]; 
    $username = $row["username"]; 
    $first_name = $row["first_name"]; 
    $last_name = $row["last_name"]; 
    $full_name = $first_name . " " . $last_name;
    
    $users[$user_id]["username"] = $username;    
    $users[$user_id]["full_name"] = $full_name;

  endwhile;
  
  return $users;

}
 // close_get_company_names
 
/** =====================================================================================================
 * A standard section has a question with a dropdown then an associated rectied by and details text input
 * ======================================================================================================
 */
function get_standard_section($db, $section_id, $section_class){

  try {
      $results = $db->query("
        SELECT 
          tbl_questions.*, 
          tbl_question_types.type_name as question_type_name
        FROM tbl_questions 
        LEFT OUTER JOIN tbl_question_types
          ON tbl_questions.type_ID=tbl_question_types.type_ID
        WHERE tbl_questions.section_ID = " . $section_id . "
          ORDER BY question_seqno ASC
        ;               
        ");
    } catch (Exception $e) {
      echo "Data could not be retrieved from the database.";
    exit; }  

    while ($row = $results->fetch(PDO::FETCH_ASSOC)): 

      //echo "<pre>"; var_dump($row); echo "</pre>"; 

      $question_text = $row["question_text"]; 
      $question_id   = $row["question_ID"];  
      $question_name = $section_class."_".$question_id;
      $question_type = $row["question_type_name"];  
      $ind_trailers = $row["ind_trailers"];    
      
      // Used for hiding/showing additional questions based on choices
      $question_details = $question_name . "_details";
      $question_rect_by = $question_name . "_rect_by";


      $ind_required = $row["ind_required"]; ?>

        <div class="form-group<?php if($ind_trailers == "N") echo " not_trailers"; ?>">

          <label for="<?php echo $question_name; ?>" class="col-sm-3 control-label"><?php echo $question_text; ?>:</label>
          <div class="col-sm-3">
		          <?php

		          // Assess and output questions type
		          switch($question_type) {
		            
		            // if Text
		            case "Text": ?>
		              <input type="text" class="form-control" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" >  
		            <?php break;

		            case "Dropdown":  ?> 

		                <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control display-control" >
		                  <option value="satisfactory">Satisfactory Condition</option>
		                  <option value="significant_defect">Significant Defect Identified</option>
		                  <option value="slight_defect">Slight Defect Identified</option>
		                  <option value="not_applicable">Not Applicable</option>		                  
		                </select>

		            <?php break;

		          } // end the switch         

		        ?>
        	</div><!-- close control col -->

          <div class="<?php echo $question_rect_by; ?> rect_by">

            <label for="<?php echo $section_class; ?>_rectified_by_<?php echo $question_id; ?>" class="col-sm-2 control-label">Rectified By: </label>

            <div class="col-md-3">
              <select name="<?php echo $section_class; ?>_rectified_by_<?php echo $question_id; ?>"
                id = "<?php echo $section_class; ?>_rectified_by_<?php echo $question_id; ?>" class="form-control" >
                <option value="" disabled selected>Select person</option>
                <?php 

                  // Get companies
                  $users = get_all_corr_users($db);

                  foreach($users as $user_id => $user):
                    $full_name = $user["full_name"]; ?>

                    <option value="<?php echo $user_id; ?>"><?php echo $full_name; ?></option>              

                  <?php endforeach; ?>

              </select>
             </div><!-- close column -->

            </div><!-- close rect_by -->

        </div><!-- close form group -->

        <!-- extra details if required -->
        <div class="form-group group_details <?php echo $question_details; ?> cf">          

          <label for="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" class="col-sm-3 control-label">Details: </label>

          <div class="col-md-8">
           <input type="input" class="<?php echo $section_class; ?>_details_<?php echo $question_id; ?> form-control" id="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" name="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" length="100" maxlength="100">     
          </div>

        </div><!-- close form-group -->

    <?php endwhile;

 } // close get_standard_function