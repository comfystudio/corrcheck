<?php 

/**
 * All functions go here
 * 
 */

// Get vehicle survey specific elements
require_once("build_sections.php");
require_once("build-report-sections.php");

/* ==================== BEGIN FUNCTIONS ==================== */


/**
 * Return array of company names and ids
 * @param  [type] $db connection obect
 * @return [type]     array containing company name
 */
function get_company_names($db){  

  try {
    $results = $db->query("
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
 * Return array of user role and ids
 * @param  [type] $db connection obect
 * @return [type]     array containing roles
 */
function get_user_roles($db){  

  try {
    $results = $db->query("
      SELECT
        user_role_id,
        role
      FROM tbl_user_roles
      ;               
      ");
  } catch (Exception $e) {
      echo "Data could not be retrieved from the database.";
      exit;
  }

  $roles = array();

  while ($row = $results->fetch(PDO::FETCH_ASSOC)):
    
    $role_id = $row["user_role_id"]; 
    $role = $row["role"]; 
    
    $roles[$role_id]["user_role"] = $role;    

  endwhile;
  
  return $roles;

}
 // close_get_company_names

 /**
 * Accept a string and return trimmed version
 * @param  [string] $field 
 * @return [string] $field
 */
function format_string($field) {
	  $field = trim($field);
	  $field = stripslashes($field);
	  $field = htmlspecialchars($field);
	  return $field;
}

 /**
 * Accept two strings from within an <option> and if they match then echo "selected"
 * @param  [string] $role_id
 * @return [string] $usertype
 */
function is_selected($role_id, $usertype){
	if($role_id == $usertype){
		echo "selected";
	}
}

/**
* Accept user ID and return email address
*/
function get_user_email($user_id, $db){
  
  $query = "
    SELECT email
    FROM tbl_users
    WHERE user_id = $user_id
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

  return $row["email"];

}




/**
* Accept user ID and return email address
*/
function get_user_username($user_id, $db){
  
  $query = "
    SELECT username
    FROM tbl_users
    WHERE user_id = $user_id
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

  return $row["username"];

}

/**
* Accept a USER ID and return an array of all variables for that user
*/

function assign_vars($user_id, $db){

  $query = "
    SELECT *
    FROM tbl_users
    WHERE user_id = $user_id
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
      die("Failed to run query 1: " . $ex->getMessage()); 
  } 

  $row = $stmt->fetch(); 

  $user_dets = array();  

  $user_dets["username"] = $row["username"];
  $user_dets["first_name"] = $row["first_name"];
  $user_dets["last_name"] = $row["last_name"];
  $user_dets["title"] = $row["title"];
  $user_dets["email"] =  $row["email"];
  $user_dets["tel_no"] = $row["tel_no"];        
  $user_dets["user_role_id"] = $row["user_role_id"];
  $user_dets["company_id"] = $row["company_id"];  

  return $user_dets;

}

/**
 * Return an array of reports based on passed args
 * @param  [array]  $args array of arguments
 * @param  [object] $db   pdo object
 * @return [array]        return an array of rows matching required args
 */
function get_reports($args, $db){

  // NB: Passing the 'role' allows us to limit to 
  // just the reports of the garage user if this
  // becomes a requirement!
  // -- for now all reports show - just need to ensure only managers can edit all reports
  // -- and garage users to be limited to editing their own report

  $limit = $args["limit"];
  $status = $args["status"];
  $role = $args["role"];

  switch($status){

    case "draft":
      $status_id = "1"; // draft
      break;
    case "pending":
      $status_id = "2"; // pending
      break;
    case "final":
      $status_id = "3"; // final
      break;
    case "archive":
      $status_id = "4"; // archive
      break;

  }

  $query = " 
    SELECT
      tbl_surveys.*,
      tbl_companies.company_name as company_name,
      tbl_users.username as username,
      tbl_survey_statuses.status_name as survey_status
    FROM tbl_surveys
    LEFT OUTER JOIN tbl_companies
      ON tbl_surveys.company_id = tbl_companies.company_id
    LEFT OUTER JOIN tbl_users
      ON tbl_surveys.completed_by_user_ID = tbl_users.user_id
    LEFT OUTER JOIN tbl_survey_statuses
      ON tbl_surveys.status_id = tbl_survey_statuses.status_id
    WHERE tbl_surveys.status_id = $status_id
    ORDER BY survey_ID desc    
  "; 

  if($limit!="all"){
    $query .= "LIMIT $limit";
  }

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

  // Finally, we can retrieve all of the found rows into an array using fetchAll 
  $pending_rows = $stmt->fetchAll(); 

  return $pending_rows;

}


/**
 * A modified version of the previous function that ensure we just return 
 * the correct sruveys
 * @param  [array]  $args array of arguments
 * @param  [object] $db   pdo object
 * @return [array]        return an array of rows matching required args
 */
function get_cust_reports($args, $db){

  // NB: Passing the 'role' allows us to limit to 
  // just the reports of the garage user if this
  // becomes a requirement!
  // -- for now all reports show - just need to ensure only managers can edit all reports
  // -- and garage users to be limited to editing their own report

  $limit = $args["limit"];
  $status = $args["status"];
  $role = $args["role"];
  $company_id = $args["company_id"];


  switch($status){

    case "draft":
      $status_id = "1"; // draft
      break;
    case "pending":
      $status_id = "2"; // pending
      break;
    case "final":
      $status_id = "3"; // final
      break;
    case "archive":
      $status_id = "4"; // archive
      break;

  }

  $query = " 
    SELECT
      tbl_surveys.*,
      tbl_companies.company_name as company_name,
      tbl_users.username as username,
      tbl_survey_statuses.status_name as survey_status
    FROM tbl_surveys
    LEFT OUTER JOIN tbl_companies
      ON tbl_surveys.company_id = tbl_companies.company_id
    LEFT OUTER JOIN tbl_users
      ON tbl_surveys.completed_by_user_ID = tbl_users.user_id
    LEFT OUTER JOIN tbl_survey_statuses
      ON tbl_surveys.status_id = tbl_survey_statuses.status_id
    WHERE tbl_surveys.status_id = $status_id
    AND tbl_surveys.company_ID = $company_id    
    ORDER BY survey_ID desc    
  "; 

  if($limit!="all"){
    $query .= "LIMIT $limit";
  }

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

  // Finally, we can retrieve all of the found rows into an array using fetchAll 
  $pending_rows = $stmt->fetchAll(); 

  return $pending_rows;

}

// Returns rows for the search filter
function get_search_results($args, $db){  

  // echo "<pre>";
  // var_dump($args);
  // echo "</pre>";

  // Set vars
  $limit = "all";
  $status = "final";  // Should always be for final reports
  $status_id = "3";    // Should always be for final reports
  $company_id = $args["company_id"];

  // Check for a search_string
  if(array_key_exists("search_string", $args)){

    $search_string = $args["search_string"];    

  }
  

  $query = " 
    SELECT
      tbl_surveys.*,
      tbl_companies.company_name as company_name,
      tbl_users.username as username,
      tbl_survey_statuses.status_name as survey_status
    FROM tbl_surveys
    LEFT OUTER JOIN tbl_companies
      ON tbl_surveys.company_id = tbl_companies.company_id
    LEFT OUTER JOIN tbl_users
      ON tbl_surveys.completed_by_user_ID = tbl_users.user_id
    LEFT OUTER JOIN tbl_survey_statuses
      ON tbl_surveys.status_id = tbl_survey_statuses.status_id
    WHERE tbl_surveys.status_id = $status_id
  ";

  // Do company search if not wildcard value
  if($company_id != "*"){
    $query .= " AND tbl_surveys.company_id = $company_id"; 
    // echo "company ID is not a star so add co id to sql";
  }

  // Do string search if not blank
  if($search_string != ""){
    $query .= " AND tbl_surveys.vehicle_reg like '%$search_string%'";
  } 

  // Add the limit
  if($limit!="all"){
    $query .= " LIMIT $limit";
  }

  $query .= " ORDER BY survey_ID desc";  

  // echo $query; die;
  
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

  // Finally, we can retrieve all of the found rows into an array using fetchAll 
  $pending_rows = $stmt->fetchAll(); 

  return $pending_rows;

}






// Set a class in the body based on the filename
function set_css_classes($filename){

  $classes = "";

  if(($filename == "create-report.php") || ($filename == "edit-report.php")){

      switch($filename){

        case "create-report.php":
          $classes = " corr-report create-report ";
          break;
        case "edit-report.php":
          $classes = " corr-report edit-report ";
          break;
      } // end switch

  }// end if

  else{

    $classes = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);

  }

  echo "class='" . $classes . "'";

}



function setCurrent($page, $anchor){  

  echo "page: $page anchor: $anchor ";

  
  // Inspections
  if(($page == "report-management") || ($page =="create-report") || ($page =="edit-report") || ($page =="view-reports")){
    echo " 2 ";
    $page = "inspections";
  }

  // Companies 
  if(($page == "company-management") || ($page =="edit_company") || ($page =="create_company")){
    echo " 2 ";
    $page = "companies";
  }

  // Users 
  if(($page == "user-management") || ($page =="edit_user") || ($page =="create_user")){
    echo " 2 ";
    $page = "users";
  }

  
  if($page == $anchor){
    echo " current";
  }

}


function get_survey_main_dets($survey_id, $db){

  //  $query = " 
  //   SELECT
  //     tbl_surveys.*
  //   FROM 
  //     tbl_surveys    
  //   WHERE
  //     tbl_surveys.survey_ID = $survey_id    
  // ";
  // 
  
  $query = "    
        SELECT
          tbl_surveys.*,
          tbl_companies.company_name as company_name,
          tbl_users.username as username,
          tbl_survey_statuses.status_name as survey_status
        FROM tbl_surveys
        LEFT OUTER JOIN tbl_companies
          ON tbl_surveys.company_id = tbl_companies.company_id
        LEFT OUTER JOIN tbl_users
          ON tbl_surveys.completed_by_user_ID = tbl_users.user_id
        LEFT OUTER JOIN tbl_survey_statuses
          ON tbl_surveys.status_id = tbl_survey_statuses.status_id
        WHERE 
          tbl_surveys.survey_ID = $survey_id            
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

  $row = $stmt->fetch(); 
  return $row;

}

// Ensures that the survey id belongs to the company
function survey_check($survey_id, $company_id, $db) {


   // Get all ID's for company id
   $query = "    
        SELECT
          tbl_surveys.survey_ID
        FROM tbl_surveys        
        WHERE 
          company_ID = $company_id            
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

  $results = $stmt->fetchAll(); 

  $isMatch = false;

  foreach ($results as $result) {

    $curr_survey_id = $result["survey_ID"];   

    if($curr_survey_id == $survey_id){
      $isMatch = true;         
    }         

  } // end for each  

    return $isMatch;  

} // end survey_check

