<?php 

/**
 * ===================
 * Class: Survey
 * ===================
 * 
 * This is used to build the survey form and to populate it with content if it is being edited
 * 
 */

class Survey {

	// The database object
	protected $db;

	// Initial Survey  Details
	var $surveyID;
	var $vehicleType;
	var $vehicleReg;
	var $companyID;
	var $makeModel;
	var $surveyDate;
	var $odoReading;
	var $odoType;
	var $preServiceRemarks;
	var $notesPartsList;
	var $surveyStatus;
    var $scheduled;
    var $psv;
    var $psvPresented;
    var $psvNotes;
    var $invoiceNum;

	// Other Details
	var $completedByID;
	var $completedByUsername;
	var $completedByFirstName;
	var $completedByLastName;
	var $supervisedByID;

	// Survey state
	var $surveyState;

	// Arrays
	var $surveyDetailsArray;
	var $surveyAxleResponses;
	var $surveyResponses;

	var $newArray; // Used on state:reload to hold revised array of _post items





	
	/* ========= CONSTRUCTOR ========= */
   	public function __construct($db, $args)
   	{	

		$this->db=$db;	 // Database object

		// Passed Args
		$this->surveyID = $args["surveyID"];
		$this->surveyState = $args["surveyStatus"];		

		if($this->surveyState == "load"){	

			// echo "State is $this->surveyState";

			// Get the initial survey details
			$this->surveyDetailsArray = $this->getSurveyDetails();			

			//var $vehicleType;
			$this->vehicleReg 		 = $this->surveyDetailsArray["vehicle_reg"];
			$this->companyID 		 = $this->surveyDetailsArray["company_ID"];
			$this->makeModel 		 = $this->surveyDetailsArray["make_model"];
			//$this->surveyDate 		 = $this->surveyDetailsArray["survey_date"];
			$this->odoReading 		 = $this->surveyDetailsArray["odo_reading"];
			$this->odoType 			 = $this->surveyDetailsArray["odo_type"];
			$this->preServiceRemarks = $this->surveyDetailsArray["pre_service_remarks"];
			$this->notesPartsList 	 = $this->surveyDetailsArray["notes_parts_list"];
			$this->surveyStatus 	 = $this->surveyDetailsArray["survey_status"];
            $this->scheduled         = $this->surveyDetailsArray["scheduled"];
            $this->psv               = $this->surveyDetailsArray["psv"];
            $this->psvPresented      = $this->surveyDetailsArray["psv_presented"];
            $this->psvNotes          = $this->surveyDetailsArray["psv_notes"];
            $this->invoiceNum        = $this->surveyDetailsArray["invoice_num"];

			// Gat and Format the date
			$date = DateTime::createFromFormat('Y-m-d', $this->surveyDetailsArray["survey_date"]);			
			$this->surveyDate = $date->format('d-m-Y'); //day-month-year

			// Other Details
			$this->completedByID = $this->surveyDetailsArray["completed_by_user_ID"];				
			$this->completedByUsername = $this->surveyDetailsArray["username"];				
			$this->completedByFirstName = $this->surveyDetailsArray["first_name"];	
			$this->completedByLastName = $this->surveyDetailsArray["last_name"];				
			
			$this->supervisedByID = $this->surveyDetailsArray["supervised_by_user_ID"];	

			// echo "<pre>";
			// var_dump($this->surveyDetailsArray);
			// echo "</pre>";

			// Get the axle responses for this survey
			$this->surveyAxleResponses = $this->getAxleResponses();			

			// Get all other responses
			$this->surveyResponses = $this->getSurveyResponses();	

			// var_dump($this->surveyResponses);

		} // close if load

		if($this->surveyState == "reload"){				

			$postedArray = $args["posted"];
			$this->newArray = $this->rebuildPostArray($postedArray);					

			//var $vehicleType;
			$this->vehicleReg 		 = $this->printSurveyDetails("veh_dets_12"); 
			$this->companyID 		 = $this->printSurveyDetails("veh_dets_11");  
			$this->makeModel 		 = $this->printSurveyDetails("veh_dets_14");  
			$this->surveyDate 		 = $this->printSurveyDetails("veh_dets_15");  
			$this->odoReading 		 = $this->printSurveyDetails("veh_dets_16");  
			$this->odoType 			 = $this->printSurveyDetails("veh_dets_17");  
			$this->preServiceRemarks = $this->printSurveyDetails("veh_dets_18");  
			$this->notesPartsList 	 = $this->printSurveyDetails("rep_dets_notes");
			$this->surveyStatus 	 = $this->printSurveyDetails("surveystatus");
            $this->scheduled         = $this->printSurveyDetails("scheduled");
            $this->psv               = $this->printSurveyDetails("psv");
            $this->psvPresented      = $this->printSurveyDetails("psv_presented");
            $this->psvNotes          = $this->printSurveyDetails("psv_notes");
            $this->invoiceNum        = $this->printSurveyDetails("invoice_num");

			// Get the axle responses for this survey
			$this->surveyAxleResponses = $this->getAxleResponses();

			// Get all other responses
			$this->surveyResponses = $this->getSurveyResponses();

		} // end if

   	} // close constructor

   	
   	/**
   	 * Is used to get details for the current survey object
   	 * @return [type] [description]
   	 */
   	function getSurveyDetails(){
		
		$query = "
		    SELECT 
				surveys.*,
			    companies.company_name as company_name,
			    user1.first_name,
			    user1.last_name,
			    user1.username,    
			    statuses.status_name as survey_status,
                user2.username as user_last_update                
				
			FROM 
				tbl_surveys AS surveys	

			LEFT OUTER JOIN tbl_companies as companies
				ON surveys.company_ID = companies.company_ID

			LEFT OUTER JOIN tbl_users as user1
				ON surveys.completed_by_user_ID = user1.user_id

			LEFT OUTER JOIN tbl_survey_statuses as statuses
				ON surveys.status_id= statuses.status_id
            
            LEFT OUTER JOIN tbl_users as user2
            	ON surveys.user_last_update = user2.user_id			  

			WHERE surveys.survey_ID = $this->surveyID	                    
		    ";

		try
		{
			$stmt = $this->db->prepare($query); 
	        $stmt->execute(); 

		} catch (Exception $e) {
		    echo "Data could not be retrieved from the database.";
		    exit;
		}

		// Finally, we can retrieve all of the found rows into an array using fetchAll 
    	$row = $stmt->fetch(); 
    	return $row;

   	} // close getSurveyDetails

   	// Query tbl_axle_responses
   	function getAxleResponses(){

   		$axleResponses = array();

   			if($this->surveyState=="load"){   				

		   		$query = "
		   			Select *
						FROM tbl_survey_axle_responses as axles
					WHERE axles.survey_ID = $this->surveyID	                    
		   		";

		   		// echo $query;

		   		try
				{
					$stmt = $this->db->prepare($query); 
			        $stmt->execute(); 

				} catch (Exception $e) {
				    echo "Data could not be retrieved from the database.";
				    exit;
				}
				
		    	$rows = $stmt->fetchAll(); 		    	

	    		// Loop through each row to build the axle response array
	    		foreach($rows as $row){  

	    			$questionID = $row["question_ID"];
	    			$questionResponse = $row["question_response"];

	    			$axleResponses[$questionID] = $questionResponse;
	    		}	    		

		    }elseif($this->surveyState=="reload"){

		    	// loop through the newArray and 
		    	foreach($this->newArray as $key => $value){   			    			

	    			if (0 === strpos($key, 'axle')) {

	    				$axleResponses[$key] = $value;

	    			}// close if begins with axle
	    		} // end foreach

		    }// end elseif		    

    	// Return this array
    	return $axleResponses;

   	} // close getAxleResponses

   	/**
   	 * Get all other survey responses
   	 * @return [type] [description]
   	 */
   	function getSurveyResponses(){

   		$surveyResponses = array();

   			if($this->surveyState=="load"){

		   		$query = "
		   			Select responses.*
						FROM tbl_survey_responses as responses
						WHERE responses.survey_ID = $this->surveyID	                    
		   		";

		   		try
				{
					$stmt = $this->db->prepare($query); 
			        $stmt->execute(); 

				} catch (Exception $e) {
				    echo "Data could not be retrieved from the database.";
				    exit;
				}

				$rows = $stmt->fetchAll(); 

				// echo "<pre>";
				// var_dump($rows);

				// Loop through each row to build the axle response array
	    		foreach($rows as $row){   			    			

	    			$questionID = $row["question_ID"];
	    			$questionResponse = $row["question_response"];

	    			$surveyResponses[$questionID] = $questionResponse;    	
	    		}

		    }elseif($this->surveyState=="reload"){

		    	// loop through the newArray and 
		    	foreach($this->newArray as $key => $value){   			    			

	    			if ((0 === strpos($key, 'veh_lub')) || 
			  		  (0 === strpos($key, 'veh_lights')) || 
			  		  (0 === strpos($key, 'veh_tacho')) || 
			  		  (0 === strpos($key, 'veh_insidecab')) ||
			  		  (0 === strpos($key, 'veh_glevel')) ||
			  		  (0 === strpos($key, 'veh_smallservice'))
			  		  ) {
	    				
	    				$surveyResponses[$key] = $value;

	    			}// close if begins with axle
	    		} // end foreach



		    }

    	return $surveyResponses;

   	}// close getSurveyResponses

   	/**
   	 * Not sure if this is needed in the long term but for now
   	 * it takes the post array and rebuilds it by stripping 
   	 * out blank responses
   	 * @return [type] [description]
   	 */
   	function rebuildPostArray($array){

   		$new_array = array();   		

   		// Pass 1: remove blanks
   		foreach($array as $key => $value){
   			
   			if($value!=""){

   				//echo "<li> $key : $value </li>" ;
   				$new_array[$key] = $value;
   			}  			
   		}

   		// Pass 2: remove redundant response
   		foreach($new_array as $key => $value){    			
   			if($value=="satisfactory"){      				
   				unset($new_array[$key]);
   			}  			
   		}   

   		return $new_array;		 		

   	} // rebuildPostArray




   	/**
   	 * =======================================================================
   	 * BUILD THE ACTUAL SURVEY FORM
   	 * =======================================================================
   	 */



   	function buildSurveyForm(){ ?>

   		<form class="corrCheck_form form-horizontal" role="form" method="post" action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'>

   			<?php // Build Individual sections

   				// Build section 1 of form: Vehicle Details
   				$this->create_corrCheck_Vehicle_Details();

   				// Build section 2 of form: Brake Performance
  				$this->create_corrCheck_Brake_Perf(); 

  				// Build section 3 of form: Brake Tyre Thread Remaining
  				$this->create_corrCheck_Tyre_Thread();

  				// Build section 4 of form: Lubrication
				$this->create_corrCheck_Lub();
				  
			  	// Build section 5 of form: Lights (standard section)
			  	$this->create_corrCheck_Lights();

			  	// Build section 6 of form: Tachograph / Speed Limiter (standard section)
			  	$this->create_corrCheck_Tachograph();

			  	// Build section 7 of form: Inside Cab (standard section)
			  	$this->create_corrCheck_InsideCab();

			  	// Build section 8 of form: Ground Level (standard section)
			  	$this->create_corrCheck_GroundLevel();    

			  	// Build section 9 of form: Small Service (standard section)
			  	$this->create_corrCheck_SmallService();    

			  	// Build section 10 of form: Additional (Road test)
			  	$this->create_corrCheck_Additional();

			  	// Build section 11 of form: Inspection Report Details
			  	$this->create_corrCheck_Rep_Details(); 

			  	// Build section 12 of form: Survey Summary
  				$this->create_corrCheck_Summary();

   			?>   			

   			<?php // Hidden fields: survey ID | survey status | completed by | current logged in userid ?>
            <input type="hidden" name="surveyid" value="<?php echo $this->surveyID; ?>" />
            <input type="hidden" name="surveystatus" value="<?php echo $this->surveyStatus; ?>" />
            <input type="hidden" name="completedByID" value="<?php echo $this->completedByID; ?>" />
            <input type="hidden" name="completedByID" value="<?php echo $this->completedByID; ?>" />

            <?php  

            	$current_user_role = $_SESSION["user"]["user_role_id"];
            	if($current_user_role == 1):
            		if(($this->surveyStatus == "pending") || ($this->surveyStatus == "final")): ?>	
  				
		  				<div class="email-check">  				
		  					<input type="checkbox" name="survey_send_email" id="survey_send_email" value="yes">
		  					<label for="survey_send_email">Send Email of Report?</label>
		  				</div>
		  				
		  			<?php endif; ?>
  				<?php endif; ?>


   		<button id="save" name="save" type="submit" class="btn btn-warning">Save Report As Draft</button>
   		<button id="submit" name="submit" type="submit" class="btn btn-success">Mark Report As Pending</button>  		

  		<?php   		  			

  			if(
  				($current_user_role == 1) && 
  				(($this->surveyStatus == "pending") || ($this->surveyStatus == "final"))
  			): ?>	
  				<button id="final" name="final" type="submit" class="btn btn-danger">Mark Report As Final</button>
  			<?php endif; ?>

  		<form> <?php   		

   	} // close buildSurveyForm()


   	/* ********************************************** */
	/* SECTION 1 VEHICLE DETAILS                      */
	/* ********************************************** */ 

	/**
	 * Builds the vehicle details section of the vehicle check.
	 * Has a few more hard coded options than other sections.
	 * @return [type] [description]
	 */
   	function create_corrCheck_Vehicle_Details(){ ?>

   		<fieldset class="veh_dets rep-section">

		  <h2>Vehicle Details</h2>

		  <div class="section-questions">

		  	<?php
                // Need to get vehicles for Vehicle reg field
                $query = "
                SELECT
                    t1.reg, t1.make, t1.type
                FROM tbl_vehicles t1
                WHERE t1.is_active = 1 AND t1.company_id = $this->companyID
                ORDER BY t1.reg ASC
            ";
                try {
                    // These two statements run the query against your database table.
                    $stmt = $this->db->prepare($query);
                    $stmt->execute();
                } catch (PDOException $ex) {
                    // Note: On a production website, you should not output $ex->getMessage().
                    // It may provide an attacker with helpful information about your code.
                    die("Failed to run query: " . $ex->getMessage());
                }
                // Finally, we can retrieve all of the found rows into an array using fetchAll
                $vehicles = $stmt->fetchAll();

			  	// where section_ID = 1 
				try {
				  $results = $this->db->query("
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
  			while ($row = $results->fetch(PDO::FETCH_ASSOC)) :

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
			            <option value="lorry" <?php $this->isDetailSelected($question_name, "lorry"); ?>>Lorry</option>
			            <option value="trailer" <?php $this->isDetailSelected($question_name, "trailer"); ?>>Trailer</option>
			          </select>

			        <?php }
			        elseif($question_id==13) { // If question is: ?>  
			          <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control">
			          <?php 

			            // Get companies
			            $companies = $this->get_company_names();  

			            foreach($companies as $company_id => $company):              
			              $company_name = $company["company_name"]; ?>

			              <option value="<?php echo $company_id; ?>"
			              	<?php $this->isDetailSelected($question_name, $company_id); ?>
			              	><?php echo $company_name; ?></option>              

			            <?php endforeach; ?>

			          </select>

                    <?php } // Vehicle Registration
                    elseif($question_id == 12){?>
                        <select id="<?php echo $question_name; ?>" name="<?php echo $question_name; ?>" class="selectpicker" data-live-search="true" data-width="180px">
                            <?php foreach($vehicles as $key => $vehicle){?>
                                <option value="<?php echo $vehicle['reg'] ?>" <?php $this->isDetailSelected($question_name, $vehicle['reg']); ?> data-make="<?php echo $vehicle['make']?>" data-type="<?php echo $vehicle['type']?>"> <?php echo $vehicle['reg']?></option>
                            <?php } ?>
                        </select>
                        <a href="<?php echo BASE_URL; ?>create_vehicle.php" class="btn btn-success" title = "add a vehicle"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    <?php } // PSV Inspection?
                    elseif($question_id == 175){?>
                        <label class="switch" for="<?php echo $question_name?>">
                            <input type="hidden" name="<?php echo $question_name ?>" value="0">
                            <input type="checkbox" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" value="1" <?php if(isset($question_name) && $this->getValue($question_name) == 1){echo "checked";}?>>
                            <div class="slider round"></div>
                        </label>
                        <span class = "right-hand-label">PSV</span>
                    <?php }
                    elseif($question_id == 176){ ?>
                        <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control">
                            <option value="customer" <?php $this->isDetailSelected($question_name, "customer"); ?>>Customer</option>
                            <option value="corr brothers" <?php $this->isDetailSelected($question_name, "corr brothers"); ?>>Corr Brothers</option>
                        </select>
                    <?php }

			        elseif($question_type == "Date"){ // If question is a date field ?>   
			          <input type="input" class="form-control date-input form-control datepicker" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" data-date-format="dd-mm-yyyy" value="<?php echo $this->surveyDate; ?>" required value="<?php echo $this->surveyDate; ?>">   
			        <?php }

			        elseif($question_type == "Number"){ // If question is a number field ?>
			          <input type="input" class="form-control number-input form-control" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" required value="<?php $this->getValue($question_name); ?>">                  
			        
			        <?php }

			        elseif($question_type == "Dropdown"){ // If question is a dropdown field ?>  

			          <select name="<?php echo $question_name; ?>" id="<?php echo $question_name; ?>" class="form-control">

			            <option value="km" <?php $this->isDetailSelected($question_name, "km"); ?>>KM</option>
			            <option value="miles" <?php $this->isDetailSelected($question_name, "miles"); ?>>Miles</option>
			            <option value="hours" <?php $this->isDetailSelected($question_name, "hours"); ?>>Hours</option>

			          </select>

			        <?php }

			        elseif($question_type == "Text"){ // Default text input ?>
			          <input type="text" class="form-control form-control" maxlength="50" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" required value="<?php $this->getValue($question_name); ?>">  
			        <?php } 

			        elseif($question_type == "Textarea"){ ?>
                        <textarea name="<?php echo $question_name ?>" id="<?php echo $question_name ?>" rows="6" class="form-control"><?php $this->getValue($question_name); ?></textarea>
			        <?php }elseif($question_type == "Checkbox"){ ?>
                        <!-- We Use a hidden one so we do not get flagged for null value on submit -->
                        <input class="form-control" type="hidden" name="<?php echo $question_name ?>" value="0">
                        <input class="form-control" type="checkbox" name="<?php echo $question_name ?>" id="<?php echo $question_name ?>"  value="1" <?php if(isset($question_name) && $this->getValue($question_name) == 1){echo "checked";}?>>
                    <?php } ?>

			      </div><!-- close col-sm-10 -->

			    </div><!-- close question -->

  			<?php endwhile;	// close while results ?>

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
	function create_corrCheck_Brake_Perf() { ?>

	  <fieldset class="bk_perf rep-section">

	    <h2>Brake Performance</h2>

	    <div class="panel-group" id="accordion">
            <!-- start panel -->
            <div class="panel panel-primary">
                <div class = "row">
                    <div class = "col-md-3 col-md-offset-3">
                        <strong>Service Break</strong>
                    </div>

                    <div class = "col-md-4 col-md-offset-2">
                        <strong>Parking Break</strong>
                    </div>
                </div>

                <div class = "row">
                    <div class = "col-md-2 col-md-offset-2">
                        <p>DEC(%)</p>
                    </div>

                    <div class = "col-md-3">
                        <p>IMB(%)</p>
                    </div>

                    <div class = "col-md-2">
                        <p>DEC(%)</p>
                    </div>

                    <div class = "col-md-3">
                        <p>IMB(%)</p>
                    </div>
                </div>

	    <?php

	    $axle_no = 1;

	    for($count=1;$count<11;$count++): ?>

            <?php

                // Axle No. Service Brake DEC Name
                $service_bk_dec = "axle_" . $axle_no . "_service_bk_dec";
                // Axle No. Service Brake IMB Name
                $service_bk_imb = "axle_" . $axle_no . "_service_bk_imb";
                // Axle No. Parking Brake DEC Name
                $parking_bk_dec = "axle_" . $axle_no . "_parking_bk_dec";
                // Axle No. Parking Brake IMB Name
                $parking_bk_imb = "axle_" . $axle_no . "_parking_bk_imb";

            ?>

            <div class="form-group">
                <label for="axle_<?php echo $axle_no; ?>_service_bk_dec" class="col-md-2 control-label">Axle <?php echo $axle_no ?></label>

                <div class="col-md-2">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_service_bk_dec" id="axle_<?php echo $axle_no; ?>_service_bk_dec"
                    value="<?php $this->getAxleValue($service_bk_dec); ?>">
                </div>

                <div class="col-md-2">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_service_bk_imb" id="axle_<?php echo $axle_no; ?>_service_bk_imb"
                    value="<?php $this->getAxleValue($service_bk_imb); ?>">
                </div>

                <div class="col-md-2 col-md-offset-1">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_parking_bk_dec" id="axle_<?php echo $axle_no; ?>_parking_bk_dec"
                    value="<?php $this->getAxleValue($parking_bk_dec); ?>">
                </div>

                <div class="col-md-2">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_parking_bk_imb" id="axle_<?php echo $axle_no; ?>_parking_bk_imb"
                    value="<?php $this->getAxleValue($parking_bk_imb); ?>">
                </div>
            </div>

		    <?php $axle_no++; ?>

	    <?php endfor; ?>
            <div class="alert alert-info" role="alert">
                <strong>NOTE:</strong> Any items left blank will not be submitted as part of report.
            </div>

		</div><!-- close panel-group -->

	  </fieldset>

	<?php } // close create_corrChech_Brake_Perf

	/* ********************************************** */
	/* SECTION 3 TYRE THREAD REMAINING                */
	/* ********************************************** */

	function create_corrCheck_Tyre_Thread(){ ?>

	   <fieldset class="section_tyre_thread rep-section">

	    <h2>Tyre Thread Remaining</h2>

	    <div class="panel-group" id="accordion">
            <!-- start panel -->
            <div class="panel panel-primary">
                <div class = "row">
                    <div class = "col-md-3 col-md-offset-3">
                        <strong>Near Side</strong>
                    </div>

                    <div class = "col-md-4 col-md-offset-2">
                        <strong>Off Side</strong>
                    </div>
                </div>

                <div class = "row">
                    <div class = "col-md-2 col-md-offset-2">
                        <p>Outside</p>
                    </div>

                    <div class = "col-md-3">
                        <p>Inside</p>
                    </div>

                    <div class = "col-md-2">
                        <p>Inside</p>
                    </div>

                    <div class = "col-md-3">
                        <p>Outside</p>
                    </div>
                </div>

	    <?php

	    $axle_no = 1;

	    for($count=1;$count<11;$count++): ?>

            <?php

                // Axle No. INNER Near Side (mm)
                $inner_near = "axle_" . $axle_no . "_inner_near";
                // Axle No. INNER Off Side (mm)
                $inner_off = "axle_" . $axle_no . "_inner_off";
                // Axle No.  OUTER Near Side (mm)
                $outer_near = "axle_" . $axle_no . "_outer_near";
                // Axle No. OUTER Off Side (mm)
                $outer_off = "axle_" . $axle_no . "_outer_off";

            ?>

            <div class="form-group">
                <label for="axle_<?php echo $axle_no; ?>_service_bk_dec" class="col-md-2 control-label">Axle <?php echo $axle_no ?></label>

                <div class="col-md-2">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_outer_near" id="axle_<?php echo $axle_no; ?>_outer_near"
                           value="<?php $this->getAxleValue($outer_near); ?>">
                </div>

                <div class="col-md-2">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_inner_near" id="axle_<?php echo $axle_no; ?>_inner_near"
                           value="<?php $this->getAxleValue($inner_near); ?>">
                </div>

                <div class="col-md-2 col-md-offset-1">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_inner_off" id="axle_<?php echo $axle_no; ?>_inner_off"
                           value="<?php $this->getAxleValue($inner_off); ?>">
                </div>

                <div class="col-md-2">
                    <input type="input" class="form-control number-input" maxlength="50" name="axle_<?php echo $axle_no; ?>_outer_off" id="axle_<?php echo $axle_no; ?>_outer_off"
                           value="<?php $this->getAxleValue($outer_off); ?>">
                </div>
            </div>

            <?php $axle_no++; ?>
	    <?php endfor; ?>
            <div class="alert alert-info" role="alert">
                <strong>NOTE:</strong> Any items left blank will not be submitted as part of report.
            </div>
	  	</div><!-- close panel-group -->

	  </fieldset>

	<?php }

	/* ********************************************** */
	/* SECTION 4 LUBRICATION                          */
	/* ********************************************** */

	function create_corrCheck_Lub(){ ?>

	  <fieldset class="section_lub rep-section">

	    <h2>Lubrication</h2> 

	    <div class="section-questions">

	    <?php

	      $section_id = 4;
	      $section_class="veh_lub";

	      // Get questions for section
	      // echo "we will get questions for section: " . $section_id . " ";

		    try {
		      $results = $this->db->query("
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

	      $question_details_name =$section_class . "_" . $question_id . "_details"; 

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
	                  
	                  <option value="ok" 	                  
	                  <?php $this->isSelected($question_name, "ok"); ?>
	                  	>OK</option>
	                  
	                  <option value="not-ok" 
	                  <?php $this->isSelected($question_name, "not-ok"); ?>
	                  	>Not OK</option>

	                </select>

	            <?php break;

	          } // end the switch         

	        ?>
	      	</div><!-- close control col -->

	        <!-- the details option for the question -->
	        <div class="group_details">
	          <label for="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" class="col-sm-1 control-label">Details: </label>
	            
	            <div class="col-md-5">
	             <input type="input" class="<?php echo $section_class; ?>_details_<?php echo $question_id; ?> form-control" id="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" length="100" maxlength="100" name="<?php echo $question_name ?>_details" id="<?php echo $question_name ?>"
	             	value="<?php $this->printDetailsResponse($question_details_name); ?>"
	             	>    
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

	function create_corrCheck_Lights(){ ?>

	   <fieldset class="section_lights rep-section section_std">

	    <h2>Lights</h2> 

	    <div class="section-questions">

	    <?php 

	      $section_id = 5;
	      $section_class= "veh_lights";
	      $this->get_standard_section($section_id, $section_class);

	    ?>

	  	</div>

	  </fieldset>

	<?php } // close create_corrCheck_Lights()

	/* ********************************************** */
	/* SECTION 6 TACHHOGRAPH                          */
	/* ********************************************** */

	function create_corrCheck_Tachograph(){ ?>

	   <fieldset class="section_tacho rep-section section_std">

	    <h2>Tachograph / Speed Limiter</h2>

	    <div class="section-questions">     

	    <?php 

	      $section_id = 6;
	      $section_class= "veh_tacho";
	      $this->get_standard_section($section_id, $section_class);

	    ?>

	  	</div>

	  </fieldset>

	<?php } // close create_corrCheck_Tachograph($db)

	/* ********************************************** */
	/* SECTION 7 INSIDE CAB                           */
	/* ********************************************** */

	function create_corrCheck_InsideCab(){ ?>

	   <fieldset class="section_insdecab rep-section section_std">

	    <h2>Inside Cab</h2>     

	    <div class="section-questions">

	    <?php 

	      $section_id = 7;
	      $section_class= "veh_insidecab";
	      $this->get_standard_section($section_id, $section_class);

	    ?>

	  </div>

	  </fieldset>

	<?php } // close create_corrCheck_Tachograph($db)

	/* ********************************************** */
	/* SECTION 8 GROUND LEVEL                           */
	/* ********************************************** */

	function create_corrCheck_GroundLevel(){ ?>

	   <fieldset class="section_groundlevel rep-section section_std">

	    <h2>Ground Level / Under Vehicle</h2> 

	    <div class="section-questions">
	    
	    <?php 

	      $section_id = 8;
	      $section_class= "veh_glevel";
	      $this->get_standard_section($section_id, $section_class);

	    ?>

	  	</div>

	  </fieldset>

	<?php } // close create_corrCheck_Tachograph($db)

	/* ********************************************** */
	/* SECTION 9 SMALL SERVICE                        */
	/* ********************************************** */

	function create_corrCheck_SmallService(){ ?>

	   <fieldset class="section_smallservice rep-section section_std">

	    <h2>Small Service</h2>

	    <div class="section-questions">

	    <?php 

	      $section_id = 9;
	      $section_class= "veh_smallservice";
	      $this->get_standard_section($section_id, $section_class);

	    ?>

	  	</div>

	  </fieldset>

	<?php } // close create_corrCheck_Tachograph($db)

	/* ********************************************** */
	/* SECTION 10 ADDITIONAL                          */
	/* ********************************************** */

	function create_corrCheck_Additional(){ ?>

	   <fieldset class="section_additional rep-section section_std">

	    <h2>Additional (Road Test)</h2>    

	    <div class="section-questions">

	    <?php 

	      $section_id = 10;
	      $section_class= "veh_additional";
	      $this->get_standard_section($section_id, $section_class);

	    ?>

	  </div>

	  </fieldset>

	<?php } // close create_corrCheck_Tachograph($db)

	/* ********************************************** */
	/* SECTION 11 REPORT DETAILS                      */
	/* ********************************************** */

	function create_corrCheck_Rep_Details(){ ?>

	   <fieldset class="section_rep_details rep-section">

	    <h2>Inspection Report Details</h2>

	    <div class="section-questions">

	          <div class="form-group cf">
	            <label for="rep_dets_notes" class="col-sm-3 control-label">Notes / Parts List:</label> 
	            <div class="col-sm-9">            

	              <textarea name="rep_dets_notes" id="rep_dets_notes" rows="6" class="form-control"><?php echo $this->notesPartsList; ?></textarea>

	          	</div><!-- close control col -->
	          </div><!-- close form group -->        

	      </div><!-- close section-questions -->      

	  </fieldset>

	<?php } // close create_corrCheck_Tachograph($db)

	/* ********************************************** */
/* SECTION 12 SUMMARY                             */
/* ********************************************** */

function create_corrCheck_Summary(){ ?>

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


/** =====================================================================================================
 * A standard section has a question with a dropdown then an associated rectied by and details text input
 * ======================================================================================================
 */
function get_standard_section($section_id, $section_class){

  try {
      $results = $this->db->query("
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

      $question_details_name = $section_class . "_details_" . $question_id; 
      $question_rect_name =  $section_class . "_rectified_by_" . $question_id;


      $ind_required = $row["ind_required"]; ?>

        <div class="form-group<?php if($ind_trailers == "N") echo " not_trailers"; ?>">

          <label for="<?php echo $question_name; ?>" class="col-sm-3 control-label"><?php echo $question_text; ?>: </label>
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

		                  <option value="satisfactory" 
		                  	<?php $this->isSelected($question_name, "satisfactory"); ?>
		                  >Satisfactory Condition</option>



		                  <option value="significant_defect" 		                  	
		                  	<?php $this->isSelected($question_name, "significant_defect"); ?>
		                  >Significant Defect Identified</option>

		                  <option value="slight_defect" 		                  		
		                  		<?php $this->isSelected($question_name, "slight_defect"); ?>
		                  >Slight Defect Identified</option>

		                  <option value="not_applicable" 		                  		
		                  		<?php $this->isSelected($question_name, "not_applicable"); ?>
		                  >Not Applicable</option>		                  
		                </select>

		            <?php break;
                    case "Checkbox": ?>
                      <input class="form-control" type="checkbox" name="<?php echo $question_name ?>" value="Yes" <?php if(isset($this->surveyResponses[$question_name]) && $this->surveyResponses[$question_name] == "Yes"){echo "checked";}?>>
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
                  $users = $this->get_all_corr_users();

                  foreach($users as $user_id => $user):
                    $full_name = $user["full_name"]; ?>

                    <option value="<?php echo $user_id; ?>"
                    		<?php $this->isSelected($question_rect_name, $user_id); ?>
                    	><?php echo $full_name; ?></option>              

                  <?php endforeach; ?>

              </select>
             </div><!-- close column -->

            </div><!-- close rect_by -->

        </div><!-- close form group -->

        <!-- extra details if required -->
        <div class="form-group group_details <?php echo $question_details; ?> cf">          

          <label for="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" class="col-sm-3 control-label">Details: </label>

          <div class="col-md-8">
           <input type="input" class="<?php echo $section_class; ?>_details_<?php echo $question_id; ?> form-control" id="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" name="<?php echo $section_class; ?>_details_<?php echo $question_id; ?>" length="100" maxlength="100" 
           value="<?php $this->printDetailsResponse($question_details_name); ?>">     
          </div>

        </div><!-- close form-group -->

    <?php endwhile;

 } // close get_standard_function






   	/**
 * Return array of company names and ids
 * @param  [type] $db connection obect
 * @return [type]     array containing company name
 */
function get_company_names(){  

  try {
    $results = $this->db->query("
      SELECT
        company_ID,
        company_name
      FROM tbl_companies
      ;               
      ");
  } catch (Exception $e) {
      echo "Data could not be retrieved from the database.";
      exit;
  }

  $companies = array();

  while ($row = $results->fetch(PDO::FETCH_ASSOC)):
    
    $company_id = $row["company_ID"]; 
    $company_name = $row["company_name"]; 
    
    $companies[$company_id]["company_name"] = $company_name;    

  endwhile;
  
  return $companies;

}
 // close_get_company_names
 
 /**
 * Return array of Users names and ids for Corr Bros Employees only
 * @param  [type] $db connection obect
 * @return [type]     array containing company name
 */
function get_all_corr_users(){
  //echo "we will do something now with companies";

  try {
    $results = $this->db->query("
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

/**
 * Accepts a string that relates to a quesiotn name/ID
 * Determines what the value should be and echoes it
 * @param  [type] $questionName [description]
 * @return [type]               [description]
 */
function getValue($questionName){
	// echo $questionName;

	// This switch deals with survey details
	switch($questionName){

		case("veh_dets_12"):
			echo $this->vehicleReg;
			break;
		case("veh_dets_14"):
			echo $this->makeModel;
			break;
		case("veh_dets_16"):
			echo $this->odoReading;
			break;
		case("veh_dets_18"):
			echo $this->preServiceRemarks;
			break;
        case("veh_dets_174"):
            return $this->scheduled;
            break;
        case("veh_dets_177"):
            echo $this->psvNotes;
            break;
        case("veh_dets_175"):
            return $this->psv;
            break;
        case("veh_dets_178"):
            echo $this->invoiceNum;
            break;

	} // end switch



} // End getValue()


function getAxleValue($questionName){

	// echo $questionName;	
	
	if (array_key_exists($questionName,$this->surveyAxleResponses)){
		echo $this->surveyAxleResponses[$questionName];
	}else{
		echo "";
	}
} // close getAxleValue()

// GW Echoes "selected" to option if true for suvrey details
function isDetailSelected($inputName, $inputValue){
	
	// The Survey Details array is a little different than the other arrays
	// used in this class, the input names do not match up with the
	// array keys that are used so we will set those here
	
	switch($inputName){
		case "veh_dets_13":
			$surveyResponse = "company_ID";
			break;
		case "veh_dets_17":
			$surveyResponse = "odo_type";
			break;
		case "veh_dets_11":
			$surveyResponse = "vehicle_type";
			break;
        case "veh_dets_12":
            $surveyResponse = "vehicle_reg";
            break;
        case "veh_dets_176":
            $surveyResponse = "psv_presented";
            break;
	}

	// Only do something if the passed array key actually exists
	if( array_key_exists($surveyResponse,$this->surveyDetailsArray) ){		

				if($this->surveyDetailsArray[$surveyResponse] == $inputValue){
					echo "selected";
				}
	}

} // close isLubeSelected()


// Echoes "selected" to option if true for lubrication questions
function isLubeSelected($surveyResponse, $inputValue){

	if($surveyResponse == $inputValue){
		echo "selected";
	}

} // close isLubeSelected()


// Echoes "selected" to option if true for standard questions
function isSelected($surveyResponse, $inputValue){

	echo " [ survey response: $surveyResponse | input value is: $inputValue ] ";	

	// Only do something if the passed array key actually exists
	if( array_key_exists($surveyResponse,$this->surveyResponses) ){

		$storedVal = $this->surveyResponses[$surveyResponse];

		echo " [ we have a valid array key: $storedVal ] ";

		if($storedVal == $inputValue){
			echo "selected";
		}
	}

} // close isLubeSelected()


// Accepts an input name and checks for its existence in the array of responses
function printDetailsResponse($inputName){

	// $this->surveyResponses
	if( array_key_exists($inputName,$this->surveyResponses) ){
		echo $this->surveyResponses[$inputName];
	}else{
		echo"";
	}

} // close printDetailsResponse

// Accepts an input name and checks for its existence in the array of responses
// Used for the Vehicle Details section of the report
function printSurveyDetails($inputName){

	// $this->surveyResponses
	if( array_key_exists($inputName,$this->newArray) ){
		return $this->newArray[$inputName];
	}else{
		return "";
	}

} // close printDetauksResponse


} // close class

