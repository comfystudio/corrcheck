<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php

 /**
  * This is the landing page for the customer user. It is very similar to the report-management.php page.
  * The difference is that the view will only show completed reports and it will only show reports
  * belonging to the company of the currently logged in user.
  * - This last point is crucially important!  
  *
  * Note that report-management.php controls the redirect to here.
  * 
  */
 
 // Query for 5 most recent final reports by ALL users
  $final_args = array(
  		"limit"			=> 10,
  		"status"		=> "final",
  		"role"			=> $user->user_role_id,
  		"company_id"	=> $user->company_id
  	);  
  $final_rows = get_cust_reports($final_args, $db);

?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>

<!-- app main column -->
<div class="app-col-main grid_12 alpha">

	
	
	<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/reports-recent-final.php"); ?>		

</div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>


