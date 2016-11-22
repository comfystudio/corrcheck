<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php

  	/**
   * When we land on this page we must be either a garager user or a manager user
   * If we are a manager we can see all 'pending' inspections that require approval and can click a button to view all
   * We can also view recently completed reports
   * We can edit pending inspections
   *
   * If we are a garage user we can see only those reports that we have created
   */
  
  // If we make it this far we must be a logged in user but ensure customer role does not proceed
  $user->redirect_customer();  

  $success_check = "";
  $success_message = "";
  // Check if success message has been passed
  if (!empty($_GET)) {
  	if($_GET["message"]!=""){
  		$success_check = $_GET["message"];
  	}
  }

  if($success_check=="success"){
  	$success_message = "Vehicle report successfully submitted for review";
  }
  if($success_check=="final"){
    $success_message = "Vehicle report successfully marked as final!";
  }


  // Query for 5 most recent pending reports by ALL users
  $pending_args = array(
  		"limit"=>5,
  		"status"=>"pending",
  		"role"=>$user->user_role_id

  	);  
  $pending_rows = get_reports($pending_args, $db);

  // Query for 5 most recent final reports by ALL users
  $final_args = array(
  		"limit"=>5,
  		"status"=>"final",
  		"role"=>$user->user_role_id
  	);  
  $final_rows = get_reports($final_args, $db);

  // Query for 5 most recent pending reports by ALL users
  $saved_args = array(
      "limit"=>5,
      "status"=>"draft",
      "role"=>$user->user_role_id

    );  
  $saved_rows = get_reports($saved_args, $db);
 
?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<!-- app main column -->
<div class="app-col-main grid_12 alpha">

	<div class="section-header">

		<?php if($success_message!=""): ?>
			<div class="alert alert-success" role="alert"><strong><?php echo $success_message; ?></strong></div>
		<?php endif; ?>

		<div class="btn-ctrl">		
			<a href="<?php echo BASE_URL; ?>create-report.php" class="btn btn-success">Create New Inspection >></a>
		</div>

	</div>	

	<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/reports-recent-pending.php"); ?>
	<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/reports-recent-final.php"); ?>	
  <?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/reports-recent-saved.php"); ?>  

	

</div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>