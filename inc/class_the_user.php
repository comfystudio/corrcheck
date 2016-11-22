<?php

 /* Sets the user variables for the logged in user */
class The_User {

	protected $db;

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

   public function __construct($array, $db) {

   		// Database object
   		$this->db=$db;

   		// Variables from session   	
   		$this->user_id = $array['user_id'];
   		$this->username = $array['username'];
   		$this->first_name = $array['first_name'];
   		$this->last_name = $array['last_name'];
   		$this->title = $array['title'];
   		$this->email = $array['email'];
   		$this->tel_no = $array['tel_no'];
   		$this->user_role_id = $array['user_role_id'];   		
   		$this->company_id = $array['company_id']; 

   		// Variables set by methods
   		$this->setCompany();
   		$this->setUserrole();  		

   }

   
    /**
     * Sets the company name propery based on the company_id
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
			echo "Data could not be retrieved from the database.";
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

	// If current user role is equal ro customer then redirect to dashboard
	function redirect_customer(){

		if($this->user_role == "Customer"){

  			// redirect  	
    		header("Location: report-listing.php"); 
         
    		// Remember that this die statement is absolutely critical.  Without it, 
    		// people can view your members-only content without logging in. 
    		die("Redirecting to report-listing.php"); 

  		} // end if

	}// end redirect_customer


} //close class