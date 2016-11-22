<?php

/**
 * This must be included at the top of any pages that have the search form otherwise the form will not work
 */

	// If search criteria has been passed via $_POST
  	if(isset($_POST["btn-filter"])){

  		// var_dump($_POST);
  		$search_check = false;

  		// Set company ID
  		if($user->user_role == "Customer"){

  			$company_id = $user->company_id; // If user is a customer use their Co. ID
			$search_check = true;

  		}elseif(isset($_POST["company-filter"])){    			

  			if($_POST["company-filter"] == "search-all"){
  						
  				$company_id = "*"; // else select all
  				$search_check = true;
  			}else{	  	

				$company_id = $_POST["company-filter"]; // else use Co. ID from filter
				$search_check = true;
			}

		}else{

			$company_id = "*"; // else select all
			$search_check = true;

		} // End if/else for company ID


		// Set the search string
  		if(($_POST["reg-search-input"] != "")){
  			$search_string = htmlspecialchars($_POST["reg-search-input"]); 
  			$search_check = true;

  			// echo "We will search for: " . $search_string;
  		}

  		if($search_check === false){

  			$search_message="You have not entered any search criteria";

  		}else{

  			$rep_args = array(		  		
		  		"status"		=> "final",
		  		"company_id" 	=> $company_id
		  	); 

		  	if($search_string!=""){
		  		$rep_args["search_string"] = $search_string;
		  	}
  		
  			$rep_rows = get_search_results($rep_args, $db);
  		}
  
  	}else{  

  		//  If we make it this far then this is the page first load so set defauls
  		//  Do customer defaults first
  		if($user->user_role == "Customer"){

  			$rep_args = array(
		  		"limit"			=> all,
		  		"status"		=> "final",		  		
		  		"company_id"	=> $user->company_id
		  	); 
		  	$rep_rows = get_cust_reports($rep_args, $db);
  		}else{

				// Set vars
			  	$limit=$_GET["limit"]; // should these be hardcoded?? Somebody could be check and try searching for pending reports
			  	$status=$_GET["status"];

			  	// Query for 5 most recent pending reports by ALL users
			  	$rep_args = array(
			  		"limit"=>$limit,
			  		"status"=>$status,
			  		"role"=>$user->user_role_id
			  	); 
			  	$rep_rows = get_reports($rep_args, $db);
	  	}

	} // end if/else for btn-filter

	$row_count = count($rep_rows);
	if($row_count == 0){
		$no_recs_msg = "No results were found for your search criteria";
	}