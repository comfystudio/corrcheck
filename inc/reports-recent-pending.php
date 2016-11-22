<div class="report-group report-section">

	<div class="report-section-hdr">

		<h2>Recent Pending Inspections</h2>

	</div>

	<div class="report-section-body">

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
				<?php foreach($pending_rows as $pending_row): ?> 
					<tr>
						<td>
							<?php $survey_id = $pending_row["survey_ID"];
								echo  $survey_id;
							?>
						</td>
						<td>
							<?php  echo $pending_row["survey_date"]; ?>
						</td>
						<td>
							<?php echo $pending_row["company_name"]; ?>
						</td>
						<td>
							<?php echo $pending_row["vehicle_reg"]; ?>
						</td>
						<td>
							<?php echo $pending_row["username"]; ?>
						</td>
						<td>
							<?php echo $pending_row["survey_status"]; ?>
						</td>
						<td>
							<!--<a href="<?php echo BASE_URL; ?>edit_user.php?user_id=<?php echo $row["user_id"] ?>" style="color:blue">edit</a>-->
							<a href="<?php echo BASE_URL; ?>edit-report.php?surveyid=<?php echo $survey_id; ?>" class="btn btn-primary" >Edit</a>
						</td>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="btn-ctrl">	
			<a href="<?php echo BASE_URL; ?>view-reports.php?status=pending&limit=all" class="btn btn-primary">View All Pending Reports >></a>
		</div>

	</div><!-- eport-section-body -->

</div><!-- report-group -->