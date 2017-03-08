<?php

/**
 * ===================
 * Class: Config_User
 * ===================
 * 
 * This is used in the creation & modification of user accounts.
 * A config user object can have 1 of 4 states which are set when the object is created:
 * 1: load_create - a new user account is being created and the form has been loaded for the first time
 * 2: post_create - a new user account is being crated and the details have been submitted at least once via $_POST
 * 3: load_edit - an existing account is being edited and the edit form is being loaded for the first time (this is an important state)
 * 4: post_edit - an existing account is being edited and the details have been submitted at least once via $_POST
 * 
 */


class Config_User {

	// The database object
	protected $db;

	// User variables
	var $user_id;
	var $username;
	var $first_name;
	var $last_name;
	var $title;
	var $email;
	var $tel_no;
	var $user_role_id;
	var $user_role;
	var $company_id;
	var $company_name;
    var $vehicle_permission;
    var $dashboard_permission;
    var $user_permission;

	// Required for pages
	var $edit_mode;
	//var $success_message;

	// Allows an array to be built that holds the assigned vars
	var $userVarArray = array();

   public function __construct($db, $user_args) {

   		// Database object
   		$this->db=$db;

   		$this->edit_mode = $user_args["edit_mode"];   		
   		$this->success_message = "";

   		// Assign the individual vars
   		if($this->edit_mode == "load_edit"){

   			// Set current user ID
   			$this->user_id = $user_args["user_id"]; 

   			// Query db to get fields for form
	        $user_dets = $this->assignVarsDB(); 

   		}elseif($this->edit_mode == "post_edit"){

   			// Set current user ID
   			$this->user_id = $user_args["user_id"];

   			// Get passed array, assign it and pass to method assignVarsPost()
   			$post_array = $user_args["post_array"];
   			$user_dets = $this->assignVarsPost($post_array);

   		}elseif($this->edit_mode == "load_create"){

   			// echo "we are creating a new user - everything blank";

   			$this->user_id = "";
			$this->username = "";
			$this->first_name = "";
			$this->last_name = "";
			$this->title = "";
			$this->email = "";
			$this->tel_no = "";
			$this->user_role_id = "";
			$this->user_role = "";
			$this->company_id = "";
			$this->company_name = "";
            $this->vehicle_permission = "";
            $this->dashboard_permission = "";
            $this->user_permission = "";


   		}elseif($this->edit_mode == "post_create"){  			

   			$post_array = $user_args["post_array"];
   			$user_dets = $this->assignVarsPost($post_array);

   		}

   		if (($this->edit_mode == "load_edit") || ($this->edit_mode == "post_edit"))
   		{

	   		$this->setCompany(); // Set the company name
	   		$this->setUserRole(); // Set the user role name   		

	   	}

	   	$this->buildVarsArray(); // Build the array from assigned vars

   } // End constructor


	function assignVarsDB(){		

	  $query = "
	    SELECT *
	    FROM tbl_users
	    WHERE user_id = " . $this->user_id . "
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

	  $this->user_id = $row["user_id"];
	  $this->username = $row["username"];
	  $this->first_name = $row["first_name"];
	  $this->last_name = $row["last_name"];
	  $this->title = $row["title"];
	  $this->email =  $row["email"];
	  $this->tel_no = $row["tel_no"];        
	  $this->user_role_id = $row["user_role_id"];
	  $this->company_id = $row["company_id"];
      $this->vehicle_permission = $row['vehicle_permission'];
      $this->dashboard_permission = $row['dashboard_permission'];
      $this->user_permission = $row['user_permission'];

	}

	function assignVarsPost($post_array){

		if (($this->edit_mode == "load_edit") || ($this->edit_mode == "post_edit")){
			$this->user_id = $post_array["user_id"];
		}
		$this->username = $post_array["username"];
	  	$this->first_name = $post_array["first_name"];
	  	$this->last_name = $post_array["last_name"];
	  	$this->title = $post_array["title"];
	  	$this->email =  $post_array["email"];
	  	$this->tel_no = $post_array["tel_no"];        
	  	$this->user_role_id = $post_array["usertype"];
	  	$this->company_id = $post_array["company"];
        $this->vehicle_permission = $post_array['vehicle_permission'];
        $this->dashboard_permission = $post_array['dashboard_permission'];
        $this->user_permission = $post_array['user_permission'];

	}


	function buildVarsArray(){

		$this->userVarArray["user_id"] = $this->user_id;
		$this->userVarArray["username"] = $this->username;
		$this->userVarArray["first_name"] = $this->first_name;
		$this->userVarArray["last_name"] = $this->last_name;
		$this->userVarArray["title"] = $this->title;
		$this->userVarArray["email"] = $this->email;
		$this->userVarArray["tel_no"] = $this->tel_no;
		$this->userVarArray["user_role_id"] = $this->user_role_id;
		$this->userVarArray["company_id"] = $this->company_id;
		$this->userVarArray["company_name"] = $this->company_name;
		$this->userVarArray["user_role"] = $this->user_role;
        $this->userVarArray['vehicle_permission'] = $this->vehicle_permission;
        $this->userVarArray['dashboard_permission'] = $this->dashboard_permission;
        $this->userVarArray['user_permission'] = $this->user_permission;

	}


   
    /**
     * Sets the company name property based on the company_id
     */
	function setCompany(){		

	   	try
	   	{
			$results = $this->db->query("
				SELECT company_name 
				FROM tbl_companies 
				WHERE company_ID = " . $this->company_id . "
				;");
		}
		catch (Exception $e) 
		{
			echo "THIS Data could not be retrieved from the database.";
			exit;
		}

	 	$row = $results->fetch();
	 	$this->company_name = $row["company_name"];	 

	}// close set_company

	/**
     * Sets the company name propery based on the company_id
     */
	function setUserrole(){

	   	try
	   	{
			$results = $this->db->query("
				SELECT role 
				FROM tbl_user_roles
				WHERE user_role_id = " . $this->user_role_id . "
				;");
		}
		catch (Exception $e) 
		{
			echo "Data could not be retrieved from the database.";
			exit;
		}

	 	$row = $results->fetch();
	 	$this->user_role = $row["role"];	 	

	}// close set_company

	function userGreating(){

		$welcome = "<h3>Welcome back, " . $this->first_name . " | " . $this->user_role ."</h3>";
		echo $welcome;

	}

} //close class