<?php

/**
 * =====================
 * Class: Config_Company
 * =====================
 * 
 * This is used in the creation & modification of companies.
 * A config user object can have 1 of 4 states which are set when the object is created:
 * 1: load_create - a new user account is being created and the form has been loaded for the first time
 * 2: post_create - a new user account is being crated and the details have been submitted at least once via $_POST
 * 3: load_edit - an existing account is being edited and the edit form is being loaded for the first time (this is an important state)
 * 4: post_edit - an existing account is being edited and the details have been submitted at least once via $_POST
 * 
 */

class Config_Company {

	// The database object
	protected $db;

	// Company properties
	var $company_id;
	var $company_name;
	var $addr_1;
	var $addr_2;
	var $addr_3;
	var $postcode;
	var $email;
	var $telno;
	var $faxno;
    var $service_interval;
	var $user_start;
	var $start_time;
    var $vehicle_permission;
    var $is_active;

	// Additional Email Addresses
	var $add_email_2;
	var $add_email_3;
	var $add_email_4;
	var $add_email_5;
	var $add_email_6;
	var $add_email_7;
	var $add_email_8;
	var $add_email_9;
	var $add_email_10;
	
	var $status;

	// Required for pages
	var $edit_mode;
	//var $success_message;
	
	// Allows an array to be built that holds the assigned vars
	var $companyVarArray = array();

	// Constructor
	public function __construct($db, $company_args) {

		// Database object
   		$this->db=$db;

   		$this->edit_mode = $company_args["edit_mode"];  

   		// Assign the individual vars from the database
   		if($this->edit_mode == "load_edit"){

   			// Set current user ID
   			$this->company_id = $company_args["company_id"]; 

   			// Query db to get fields for form
	        $company_dets = $this->assignVarsDB(); 

   		}elseif($this->edit_mode == "post_edit"){

   			// Set current user ID
   			$this->company_id = $company_args["company_id"];

   			// Get passed array, assign it and pass to method assignVarsPost()
   			$post_array = $company_args["post_array"];
   			$company_dets = $this->assignVarsPost($post_array);

   		}elseif($this->edit_mode == "load_create"){

   			// echo "we are creating a new company - everything blank";

   			$this->company_id 	= "";
			$this->company_name = "";
			$this->addr_1 		= "";
			$this->addr_2 		= "";
			$this->addr_3 		= "";
			$this->postcode 	= "";
			$this->email 		= "";
			$this->telno 		= "";
			$this->faxno 		= "";
            $this->service_interval = "";
			$this->user_start	= "";
			$this->start_time	= "";
            $this->vehicle_permission = "";
            $this->is_active    = "";

			$this->add_email_2 		= "";
			$this->add_email_3 		= "";
			$this->add_email_4 		= "";
			$this->add_email_5 		= "";
			$this->add_email_6 		= "";
			$this->add_email_7 		= "";
			$this->add_email_8 		= "";
			$this->add_email_9 		= "";
			$this->add_email_10 	= "";


   		}elseif($this->edit_mode == "post_create"){

   			  

   			$post_array = $company_args["post_array"];
   			$this->assignVarsPost($post_array);

   		}

   		$this->buildVarsArray(); // Build the array from assigned vars   		

   	} // close constructor 

   	// Assign object vars via call to db
   	function assignVarsDB()
   	{

   		$query = "
	    SELECT *
	    FROM tbl_companies
	    WHERE company_id = " . $this->company_id . "
	  ";

	  try 
	  { 
	      // Run the query against your database table. 
	      $stmt = $this->db->prepare($query); 
	      $stmt->execute(); 
	      // echo "success on THIS query! ";
	  } 
	  catch(PDOException $ex) 
	  { 
	      // Note: On a production website, you should not output $ex->getMessage(). 
	      // It may provide an attacker with helpful information about your code.  
	      //echo " no success on query!";
	      die("Failed to run THIS query: " . $ex->getMessage()); 
	  } 

	  $row = $stmt->fetch();
	  
	  $this->company_name = $row["company_name"];
	  $this->addr_1 = $row["addr_1"];
	  $this->addr_2 = $row["addr_2"];
	  $this->addr_3 = $row["addr_3"];	  
	  $this->postcode = $row["postcode"];
	  $this->email =  $row["email"];
	  $this->telno = $row["telno"];        
	  $this->faxno = $row["faxno"];
      $this->service_interval = $row['service_interval'];
	  $this->user_start = $row['user_start'];
	  $this->start_time = $row['start_time'];

      $this->vehicle_permission = $row['vehicle_permission'];
      $this->is_active = $row['is_active'];

	  $this->add_email_2 =  $row["email_2"];
	  $this->add_email_3 =  $row["email_3"];
	  $this->add_email_4 =  $row["email_4"];
	  $this->add_email_5 =  $row["email_5"];
	  $this->add_email_6 =  $row["email_6"];
	  $this->add_email_7 =  $row["email_7"];
	  $this->add_email_8 =  $row["email_8"];
	  $this->add_email_9 =  $row["email_9"];
	  $this->add_email_10 =  $row["email_10"];

   	}


   	// Assign object properties based on passed array
   	function assignVarsPost($post_array){
		if (($this->edit_mode == "load_edit") || ($this->edit_mode == "post_edit")){
			$this->company_id = $post_array["company_id"];
		}
		$this->company_name = $post_array["company_name"];
	  	$this->addr_1 = $post_array["addr_1"];
	  	$this->addr_2 = $post_array["addr_2"];
	  	$this->addr_3 = $post_array["addr_3"];
	  	$this->postcode =  $post_array["postcode"];
	  	$this->email = $post_array["email"];        
	  	$this->telno = $post_array["telno"];        
	  	$this->faxno = $post_array["faxno"];
        $this->service_interval = $post_array['service_interval'];
		$this->user_start = $post_array['user_start'];
		$this->start_time = $post_array['start_time'];
        $this->vehicle_permission = $post_array['vehicle_permission'];
        $this->is_active = $post_array['is_active'];

        $this->add_email_2 = $post_array["add_email_2"];
	  	$this->add_email_3 = $post_array["add_email_3"];        
	  	$this->add_email_4 = $post_array["add_email_4"];        
	  	$this->add_email_5 = $post_array["add_email_5"];        
	  	$this->add_email_6 = $post_array["add_email_6"];        
	  	$this->add_email_7 = $post_array["add_email_7"];        
	  	$this->add_email_8 = $post_array["add_email_8"];        
	  	$this->add_email_9 = $post_array["add_email_9"];        
	  	$this->add_email_10 = $post_array["add_email_10"];  

	}


   	// This will build an array containing this objects properties
   	function buildVarsArray(){

		$this->companyVarArray["company_id"] = $this->company_id;
		$this->companyVarArray["company_name"] = $this->company_name;
		$this->companyVarArray["addr_1"] = $this->addr_1;
		$this->companyVarArray["addr_2"] = $this->addr_2;
		$this->companyVarArray["addr_3"] = $this->addr_3;
		$this->companyVarArray["postcode"] = $this->postcode;				
		$this->companyVarArray["email"] = $this->email;
		$this->companyVarArray["telno"] = $this->telno;
        $this->companyVarArray["faxno"] = $this->faxno;
        $this->companyVarArray['service_interval'] = $this->service_interval;
		$this->companyVarArray['user_start'] = $this->user_start;
		$this->companyVarArray['start_time'] = $this->start_time;
        $this->companyVarArray['vehicle_permission'] = $this->vehicle_permission;
        $this->companyVarArray['is_active'] = $this->is_active;


        $this->companyVarArray["add_email_2"] = $this->add_email_2;
		$this->companyVarArray["add_email_3"] = $this->add_email_3;
		$this->companyVarArray["add_email_4"] = $this->add_email_4;
		$this->companyVarArray["add_email_5"] = $this->add_email_5;
		$this->companyVarArray["add_email_6"] = $this->add_email_6;
		$this->companyVarArray["add_email_7"] = $this->add_email_7;
		$this->companyVarArray["add_email_8"] = $this->add_email_8;
		$this->companyVarArray["add_email_9"] = $this->add_email_9;
		$this->companyVarArray["add_email_10"] = $this->add_email_10;

	}
	

} // close class