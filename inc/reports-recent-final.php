<div class="report-group report-section">

	<div class="report-section-hdr">

		<h2>Recent Final Inspections</h2>

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
					<?php foreach($final_rows as $final_row): ?> 
						<tr>
							<td>
								<?php echo $final_row["survey_ID"]; ?>
							</td>
							<td>
								<?php  echo $final_row["survey_date"]; ?>
							</td>
							<td>
								<?php echo $final_row["company_name"]; ?>
							</td>
							<td>
								<?php echo $final_row["vehicle_reg"]; ?>
							</td>
							<td>
								<?php echo $final_row["username"]; ?>
							</td>
							<td>
								<?php echo $final_row["survey_status"]; ?>
							</td>
							<td>
								<a href="<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $final_row["survey_ID"] ?>" class="btn btn-primary">View</a>
							</td>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div class="btn-ctrl">	

			<?php if($user->user_role == "Customer"): // if user is customer ?>

				<a href="<?php echo BASE_URL; ?>report-listing-all.php" class="btn btn-primary">View All Final Reports >></a>

			<?php else: ?>

				<a href="<?php echo BASE_URL; ?>view-reports.php?status=final&limit=all" class="btn btn-primary">View All Final Reports >></a>

			<?php endif; ?>


			</div>

		</div><!-- eport-section-body -->

	</div><!-- report-group -->