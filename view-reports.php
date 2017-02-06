<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?><?php  	// If we make it this far we must be a logged in user but ensure customer role does not proceed	$user->redirect_customer();    	if (empty($_GET)) {  		// redirect  	    	header("Location: report-management.php");     	  		  	die("Redirecting to report-management.php");  // critical! 	}	// Only use filter if looking for final reports  	include($_SERVER["DOCUMENT_ROOT"] . "/inc/search-filter-head.php"); 	  	 ?><?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?><!-- app main column --><div class="app-col-main grid_12 alpha">	<h2 style="text-transform:uppercase"><?php echo $status; ?> INSPECTIONS</h2>	<?php if($_GET["status"] == "final"): ?>		<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/search-filter-form.php"); 	 ?>	<?php endif; ?>	<table class="table table-bordered table-striped">		<thead>			<tr>				<td>					Inspection No.				</td>				<td>					Date				</td>				<td>					Company				</td>				<td>					Vehicle Registration				</td>				<td>					Inspection By				</td>				<td>					Status				</td>				<td>					&nbsp;				</td>			</tr>		</thead>		<tbody>			<?php foreach($rep_rows as $rep_row): ?> 				<tr>					<td>						<?php echo $rep_row["survey_ID"]; ?>					</td>					<td>						<?php  echo $rep_row["survey_date"]; ?>					</td>					<td>						<?php echo $rep_row["company_name"]; ?>					</td>					<td>						<?php echo $rep_row["vehicle_reg"]; ?>					</td>					<td>						<?php echo $rep_row["username"]; ?>					</td>					<td>						<?php echo $rep_row["survey_status"]; ?>					</td>					<td>						<?php if(($status=="pending") || ($status=="draft")): ?>							<a href="<?php echo BASE_URL; ?>edit-report.php?surveyid=<?php echo $rep_row["survey_ID"] ?>" class="btn btn-primary">Edit</a>						<?php else: ?>							<a href="<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $rep_row["survey_ID"] ?>" class="btn btn-primary">View</a>						<?php endif; ?>					</td>			<?php endforeach; ?>			<?php				if(isset($no_recs_msg) && $no_recs_msg != ""): ?>					<tr><td colspan="7"><p class="has-error message-error"><?php echo $no_recs_msg; ?></p></td></tr>				<?php endif; ?>					</tbody>	</table>    <?php if(!empty($page_links)){ ?>        <div class="dataTables_paginate paging_bootstrap">            <?php echo $page_links; ?>        </div>    <?php } ?></div><?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>