<?php

/**
 * ===================
 * Class: Company Form
 * ===================
 * 
 * This is used to build the form used in the creation & modification of companies. 
 * 
 */

class Company_Form {

	// The database object
	protected $db;

	// Company properties
	var $company_id;
	var $company_name;
	var $addr_1;
	var $addr_2;
	var $addr_3;
	var $postcode;
	var $email; // Primary email address
	var $telno;
	var $faxno;
    var $service_interval;
	var $user_start;
	var $start_time;
    var $vehicle_permission;
    var $is_active;
    var $error_check;
	var $success_message;
	var $form_type; // options are "new" or "edit" only!	

	var $add_email_2;
	var $add_email_3;
	var $add_email_4;
	var $add_email_5;
	var $add_email_6;
	var $add_email_7;
	var $add_email_8;
	var $add_email_9;
	var $add_email_10;

	var $btn_text;

	// Arrays
	var $companyVarArray;
	var $errorArray;	

	

	/* ========= CONSTRUCTOR ========= */
   	public function __construct($db, $company_vars, $form_type)
   	{
   		$this->db=$db;	 // Database object     		 	
        $this->form_type = $form_type;	        

        $this->companyVarArray = $company_vars;

        $this->company_id 	= $company_vars["company_id"];
        $this->company_name = $company_vars["company_name"];
        $this->addr_1 		= $company_vars["addr_1"];
        $this->addr_2 		= $company_vars["addr_2"];
        $this->addr_3 		= $company_vars["addr_3"];
        $this->postcode 	= $company_vars["postcode"];
        $this->email 		= $company_vars["email"];
        $this->telno 		= $company_vars["telno"];
        $this->faxno        = $company_vars['faxno'];
        $this->service_interval = $company_vars['service_interval'];
		$this->user_start	= $company_vars['user_start'];
		$this->start_time	= $company_vars['start_time'];
        $this->vehicle_permission = $company_vars['vehicle_permission'];
        $this->is_active   = $company_vars['is_active'];

        $this->add_email_2 		= $company_vars["add_email_2"];   
        $this->add_email_3 		= $company_vars["add_email_3"];   
        $this->add_email_4 		= $company_vars["add_email_4"];   
        $this->add_email_5 		= $company_vars["add_email_5"];   
        $this->add_email_6 		= $company_vars["add_email_6"];   
        $this->add_email_7 		= $company_vars["add_email_7"];   
        $this->add_email_8 		= $company_vars["add_email_8"];   
        $this->add_email_9 		= $company_vars["add_email_9"];   
        $this->add_email_10 	= $company_vars["add_email_10"];   

        // Initialise error handling vars
      	$this->error_check = false;   
      	$this->errorArray = array(); 	

      	// Set btn text
      	if($this->form_type=="edit"){
      		$this->btn_text = "Update Company";
      	}elseif($this->form_type=="new"){
      		$this->btn_text = "Create Company";
      	}else{
      		$this->btn_text = "Blah Company";
      	}


   	}// close constructor


	// Process the edited details of existing company
	function processCompanyForm()
	{

		/* =========================================== */
        // STAGE 1: Simple 'empty' Checks
        /* =========================================== */

        // echo " *checks* ";

        // Company Name
        if(empty($_POST['company_name'])) 
        {               
            $this->errorArray['name_error'] =  "Company Name cannot be blank.";
            $this->error_check = true;            

        } else {
            $this->company_name = format_string($_POST['company_name']);                      
        }

        // Address Line 1
        if(empty($_POST['addr_1'])) 
        {               
            $this->errorArray['address_1_error'] =  "Must have at least one address line";
            $this->error_check = true;            

        } else {
            $this->addr_1 = format_string($_POST['addr_1']);                      
        }

        // Address Line 2 | Not Required
        if(!empty($_POST['addr_2'])){        
            $this->addr_2 = format_string($_POST['addr_2']);         
        }

        // Address Line 3 | Not Required
        if(!empty($_POST['addr_3'])){        
            $this->addr_3 = format_string($_POST['addr_3']);         
        }

        // Postcode | Not Required
        if(!empty($_POST['postcode'])){        
            $this->postcode = format_string($_POST['postcode']);         
        }

        // Email Address
        if(empty($_POST['email'])) 
        {               
            $this->errorArray['email_error'] =  "Email address cannot be blank";
            $this->error_check = true;            

        } else {
            $this->email = format_string($_POST['email']);                      
        }

        // Telephone number
        if(empty($_POST['telno'])) 
        {               
            $this->errorArray['telno_error'] =  "Telephone Number cannot be blank";
            $this->error_check = true;            

        } else {
            $this->telno = format_string($_POST['telno']);                      
        }

        // Faxno | Not Required
        if(!empty($_POST['faxno'])){        
            $this->faxno = format_string($_POST['faxno']);         
        }

        if(!empty($_POST['service_interval'])){
            $this->service_interval = format_string($_POST['service_interval']);
        }

		//Need to reformat the date to a more sql friendly format
		if(!empty($_POST['start_time'])){
			$this->start_time = date('Y-m-d', strtotime($_POST['start_time']));
		}

		if(!empty($_POST['user_start'])){
			$this->user_start = format_string($_POST['user_start']);
		}

        if(!empty($_POST['vehicle_permission'])){
            $this->vehicle_permission = format_string($_POST['vehicle_permission']);
        }

        /* ============================================================ */
        // STAGE 2: Secondary Checks 
        // NOTE - these are repeated in processEditUserForm()
        // so here is a good area to focus if time for code refactoring
        // -- All these checks should be individual functions
        /* ============================================================ */

        // ================== Check that email is valid         
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            $this->errorArray['email_invalid_error'] = "Invalid E-Mail Address"; 
            $this->error_check = true;
        } 

        /* ============================================================ */
        // STAGE 3: DB Query        
        /* ============================================================ */                

        if($this->error_check === false)
        {
        	// No errors were encountered - process form                      
        	// If creating editing an existing compant THEN need UPDATE query
        	// If adding a new company THEN need INSERT query
        	
        	if($this->form_type=="edit"){

        		// echo "we are editing an existing company";

	            $query = "
	            		UPDATE tbl_companies
	                	SET                     
		                    company_name = :company_name, 
		                    addr_1 = :addr_1,
		                    addr_2 = :addr_2,
		                    addr_3 = :addr_3,
		                    postcode = :postcode,
		                    email = :email,
		                    telno = :telno,
		                    faxno = :faxno,
		                    service_interval = :service_interval,
		                    user_start = :user_start,
		                    start_time = :start_time,
		                    vehicle_permission = :vehicle_permission,
		                    is_active = :is_active,
		                    email_2 = :email_2,
		                    email_3 = :email_3,
		                    email_4 = :email_4,
		                    email_5 = :email_5,
		                    email_6 = :email_6,
		                    email_7 = :email_7,
		                    email_8 = :email_8,
		                    email_9 = :email_9,
		                    email_10 = :email_10
	                    WHERE 
	              			company_ID = :company_id 
	            "; 

	            $query_params = array( 
	                ':company_id' => $_POST['company_id'], 
	                ':company_name' => $_POST['company_name'], 
	                ':addr_1' => $_POST['addr_1'], 
	                ':addr_2' => $_POST['addr_2'], 
	                ':addr_3' => $_POST['addr_3'], 
	                ':postcode' => $_POST['postcode'], 
	                ':email' => $_POST['email'], 
	                ':telno' => $_POST['telno'], 
	                ':faxno' => $_POST['faxno'],
                    ':service_interval' => $_POST['service_interval'],
					':user_start' => $_POST['user_start'],
					':start_time' => $this->start_time,
                    ':vehicle_permission' => $_POST['vehicle_permission'],
                    ':is_active' => $_POST['is_active'],
	                ':email_2' 		=>$_POST["add_email_2"],
	                ':email_3' 		=>$_POST["add_email_3"],
	                ':email_4' 		=>$_POST["add_email_4"],
	                ':email_5' 		=>$_POST["add_email_5"],
	                ':email_6' 		=>$_POST["add_email_6"],
	                ':email_7' 		=>$_POST["add_email_7"],
	                ':email_8' 		=>$_POST["add_email_8"],
	                ':email_9' 		=>$_POST["add_email_9"],
	                ':email_10' 	=>$_POST["add_email_10"],
	            ); 

	        }elseif($this->form_type="new"){

	        	// echo "we are creating a new company";

	        	$query = " 
	                INSERT INTO tbl_companies ( 
	                    company_name,
	                    addr_1, 
	                    addr_2,
	                    addr_3,
	                    postcode,
	                    email,
	                    telno,
	                    faxno,
	                    service_interval,
	                    user_start,
	                    start_time,
	                    vehicle_permission,
	                    is_active,
	                    email_2,
						email_3, 
						email_4, 
						email_5,
						email_6,
						email_7,
						email_8,
						email_9,
						email_10                   
	                ) VALUES ( 
	                    :company_name,
	                    :addr_1,
	                    :addr_2,
	                    :addr_3,
	                    :postcode,
	                    :email,
	                    :telno,
	                    :faxno,
	                    :service_interval,
	                    :user_start,
	                    :start_time,
	                    :vehicle_permission,
	                    :is_active,
	                    :email_2,
						:email_3, 
						:email_4, 
						:email_5,
						:email_6,
						:email_7,
						:email_8,
						:email_9,
						:email_10
	                ) 
	            "; 

	            $query_params = array( 
	               	':company_name' => $_POST['company_name'], 
	                ':addr_1' 		=> $_POST['addr_1'], 
	                ':addr_2' 		=> $_POST['addr_2'], 
	                ':addr_3' 		=> $_POST['addr_3'], 
	                ':postcode' 	=> $_POST['postcode'], 
	                ':email' 		=> $_POST['email'], 
	                ':telno' 		=> $_POST['telno'], 
	                ':faxno' 		=> $_POST['faxno'],
                    ':service_interval' => $_POST['service_interval'],
					':user_start' 	=> $_POST['user_start'],
					':start_time' 	=> $this->start_time,
                    ':vehicle_permission' => $_POST['vehicle_permission'],
                    ':is_active'    => $_POST['is_active'],
	                ':email_2' 		=>$_POST["add_email_2"],
	                ':email_3' 		=>$_POST["add_email_3"],
	                ':email_4' 		=>$_POST["add_email_4"],
	                ':email_5' 		=>$_POST["add_email_5"],
	                ':email_6' 		=>$_POST["add_email_6"],
	                ':email_7' 		=>$_POST["add_email_7"],
	                ':email_8' 		=>$_POST["add_email_8"],
	                ':email_9' 		=>$_POST["add_email_9"],
	                ':email_10' 	=>$_POST["add_email_10"],
	            );

	        }

	        // $this->insertEmails();	        

            try 
            {
                // Execute the query to create the user 
                $stmt = $this->db->prepare($query);
                $result = $stmt->execute($query_params);
            } 
            catch(PDOException $ex) 
            { 
                // Note: On a production website, you should not output $ex->getMessage(). 
                // It may provide an attacker with helpful information about your code.  
                die("Failed to run THIS query: " . $ex->getMessage()); 
            }



            if($this->form_type=="edit")
            {
            	$this->success_message = "Company details updated successfully.";
            }
            elseif($this->form_type=="new")
            {
            	$this->success_message = "Company created successfully.";
            }

        } // close error_check

	} // close processEditCompanyForm()	


	function renderCompanyForm(){ ?>

		<!-- app main column -->
		<div class="app-col-main grid_12 alpha">  

			<h2>Company Management</h2>

			    <?php 

			    if(!empty($this->errorArray)) {  

			    // var_dump($_POST);

			      // TESTING ERRORS - LIST ERROR ARRAY
			      foreach($this->errorArray as $error_key => $error_reason): ?>

			        <p style="color:red; font-weight: bold"><?php echo $error_reason; ?></p>

			        <?php endforeach; ?>

			    <?php } ?>

			<form class="corrCheck_form form-horizontal" role="form" method="post" action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'> 

				<fieldset class="create_company rep-section">

					<div class="section-questions">		

						<?php if($this->success_message!=""): ?>
                    		<div class="alert alert-success" role="alert"><strong><?php echo $this->success_message; ?></strong></div>
                		<?php endif; ?>  			

		                <div class="question_row cf form-group">
		                    <label for="company_name" class="col-sm-3 control-label">Company Name*:</label>
		                    <div class="col-sm-4">
		                        <input type="text" class="form-control form-control" name="company_name" id="company_name" value="<?php echo $this->company_name; ?>" /> 
		                    </div>
		                </div>            			

		                <div class="question_row cf form-group">
		                    <label for="addr_1" class="col-sm-3 control-label">Address Line 1*:</label>
		                    <div class="col-sm-4">
		                        <input type="text" class="form-control form-control" name="addr_1" id="addr_1" value="<?php echo $this->addr_1; ?>" /> 
		                    </div>
		                </div> 

		                <div class="question_row cf form-group">
		                    <label for="addr_2" class="col-sm-3 control-label">Address Line 2:</label>
		                    <div class="col-sm-4">
		                        <input type="text" class="form-control form-control" name="addr_2" id="addr_2" value="<?php echo $this->addr_2; ?>" /> 
		                    </div>
		                </div>

		                <div class="question_row cf form-group">
		                    <label for="addr_3" class="col-sm-3 control-label">Address Line 3:</label>
		                    <div class="col-sm-4">
		                        <input type="text" class="form-control form-control" name="addr_3" id="addr_3" value="<?php echo $this->addr_3; ?>" /> 
		                    </div>
		                </div>

		                <div class="question_row cf form-group">
		                    <label for="postcode" class="col-sm-3 control-label">Postcode:</label>
		                    <div class="col-sm-4">
		                        <input type="text" class="form-control form-control" name="postcode" id="postcode" value="<?php echo $this->postcode; ?>" /> 
		                    </div>
		                </div>

		                <div class="question_row cf form-group">
		                    <label for="email" class="col-sm-3 control-label">Primary Email Address*:</label>
		                    <div class="col-sm-4">
		                        <input type="text" class="form-control form-control" name="email" id="email" value="<?php echo $this->email; ?>" /> 
		                    </div>
		                </div>

		                <div class="question_row cf form-group">
		                    <label for="telno" class="col-sm-3 control-label">Telephone No.*:</label>
		                    <div class="col-sm-4">
		                        <input type="text" class="form-control form-control" name="telno" id="telno" value="<?php echo $this->telno; ?>" /> 
		                    </div>
		                </div>

		                <div class="question_row cf form-group">
                            <label for="faxno" class="col-sm-3 control-label">Fax No.:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control" name="faxno" id="faxno" value="<?php echo $this->faxno; ?>" />
                            </div>
                        </div>

                        <div class="question_row cf form-group">
                            <label for="service_interval" class="col-sm-3 control-label">Service Interval (weeks):</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control" name="service_interval" id="service_interval" value="<?php echo $this->service_interval; ?>" />
                            </div>
                        </div>

                        <input type="hidden" class="form-control form-control" name="vehicle_permission" id="vehicle_permission" value="0"/>
<!--                        <div class="question_row cf form-group">-->
<!--                            <label for="vehicle_permission" class="col-sm-3 control-label">Vehicle Permission:</label>-->
<!--                            <div class="col-sm-4">-->
<!--                                <input type="checkbox" class="form-control form-control" name="vehicle_permission" id="vehicle_permission" value="1" --><?php //if(isset($this->vehicle_permission) && $this->vehicle_permission == 1){echo 'checked';}?><!--/>-->
<!--                            </div>-->
<!--                        </div>-->

						<input type="hidden" class="form-control form-control" name="user_start" id="user_start" value="0"/>
						<div class="question_row cf form-group">
							<label for="vehicle_permission" class="col-sm-3 control-label">Use Fixed Schedule?:</label>
							<div class="col-sm-4">
								<input type="checkbox" class="form-control form-control" name="user_start" id="user_start" value="1" <?php if(isset($this->user_start) && $this->user_start == 1){echo 'checked';}?>/>
							</div>
						</div>

						<div class="question_row cf form-group">
							<label for="start_time" class="col-sm-3 control-label">Custom Start Date:</label>

							<div class="col-sm-4">
								<input type="text" class="form-control date-input form-control datepicker" name="start_time" data-date-format="dd-mm-yyyy" value="<?php if(isset($this->start_time) && !empty($this->start_time)){echo date('d-m-Y', strtotime($this->start_time));}?>"/>
							</div>
						</div>

                        <input type="hidden" class="form-control form-control" name="is_active" id="is_active" value="2"/>
                        <div class="question_row cf form-group">
                            <label for="is_active" class="col-sm-3 control-label">Is Active?</label>
                            <div class="col-sm-4">
                                <input type="checkbox" class="form-control form-control" name="is_active" id="is_active" value="1" <?php if(isset($this->is_active) && $this->is_active == 1){echo 'checked="checked"';} ?>/>
                            </div>
                        </div>

					</div><!-- close section-questions -->

					<?php if($this->form_type=="edit"): ?>

		                  <input type="hidden" name="company_id" value="<?php echo $this->company_id; ?>" /> 
		                  
		              <?php endif; ?>

				</fieldset>

				<a href="" class="btn btn-success email-ctrl">Show/Hide Additional Email Addresses</a>

				<fieldset class="create_company rep-section hide-this extra-emails">

					<div class="section-questions section-add-emails">	

						<?php $counter = 2; ?>
						<?php for ($i = 1; $i <= 9; $i++): ?>
							<?php 

								// Get the value for the current email address from the array...								
								$curr_email = $this->{"add_email_$counter"};	// this will give $this->add_email_($counter)			

							?>

							<div class="question_row cf form-group">
			                    <label for="email_<?php echo $counter; ?>" class="col-sm-3 control-label">Email Address (<?php echo $counter; ?>):</label>
			                    <div class="col-sm-4">
			                        <input type="text" class="form-control" name="add_email_<?php echo $counter; ?>" id="add_email_<?php echo $counter; ?>" value="<?php echo $curr_email; ?>" /> 
			                    </div>
			                </div>						

							<?php $counter++; ?>
						<?php endfor; ?>

					</div>

				</fieldset>

				<button id="submit" type="submit" value="update" name="submit" class="btn btn-primary"/><?php echo $this->btn_text; ?></button> 

			</form>

		</div>

	<?php } // close renderCompanyForm


} // close class