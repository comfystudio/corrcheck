<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header.php"); ?>
<?php
	
 /**
  * This page is the customer version of view-reports.php only here we do not use GET variables
  * and simply use the company id of the current logged in user to list all reports
  */  
 
 include($_SERVER["DOCUMENT_ROOT"] . "/inc/search-filter-head.php"); 	  


  // Query for 5 most recent pending reports by ALL users
  // $rep_args = array(
  // 		"limit"			=> all,
  // 		"status"		=> "final",
  // 		"role"			=> $user->user_role_id,
  // 		"company_id"	=> $user->company_id
  // 	); 


  //$rep_rows = get_cust_reports($rep_args, $db);

 
?>
 
<!-- app main column -->
<div class="app-col-main grid_12 alpha">

	<h2 style="text-transform:uppercase"><?php echo $status; ?> INSPECTIONS</h2>

	<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/search-filter-form.php"); 	 ?>

	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<td>
					Inspection No.
				</td>
				<td>
					Date
				</td>
				<td>
					Company
				</td>
				<td>
					Vehicle Registration
				</td>
				<td>
					Inspection By
				</td>
				<td>
					Status
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rep_rows as $rep_row): ?> 
				<tr>
					<td>
						<?php echo $rep_row["survey_ID"]; ?>
					</td>
					<td>
						<?php  echo $rep_row["survey_date"]; ?>
					</td>
					<td>
						<?php echo $rep_row["company_name"]; ?>
					</td>
					<td>
						<?php echo $rep_row["vehicle_reg"]; ?>
					</td>
					<td>
						<?php echo $rep_row["username"]; ?>
					</td>
					<td>
						<?php echo $rep_row["survey_status"]; ?>
					</td>
					<td>
						
							<a href="<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $rep_row["survey_ID"] ?>" class="btn btn-primary">View</a>
						
					</td>
			<?php endforeach; ?>
			<?php if($no_recs_msg != ""): ?>
				<tr>
					<td colspan="7">
						<p class="has-error message-error"><?php echo $no_recs_msg; ?></p>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

</div>

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>