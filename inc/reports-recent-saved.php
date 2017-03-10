<div class="report-group report-section">

	<div class="report-section-hdr">

		<h2>Recent Inspections Drafts</h2>

	</div>

	<div class="report-section-body">

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<td>
						Inspection No.
					</td>
                    <td>
                        Invoice Number
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
				<?php foreach($saved_rows as $saved_row): ?> 
					<tr>
						<td>
							<?php $survey_id = $saved_row["survey_ID"];
                                echo '<a href = "view-report.php?survey_id='.$saved_row["survey_ID"].'" style = "color:#428bca;">'.$saved_row["survey_ID"].'</a>';
							?>
						</td>
                        <td>
                            <?php  echo $saved_row["invoice_num"]; ?>
                        </td>
						<td>
							<?php  echo date('j F, Y', strtotime($saved_row["survey_date"])); ?>
						</td>
						<td>
                            <?php echo '<a href = "edit_company.php?company_id='.$saved_row['company_ID'].'" style = "color:#428bca;">'.$saved_row["company_name"].'</a>'; ?>
						</td>
						<td>
                            <?php echo '<a href = "vehicle-view.php?vehicle_id='.$saved_row["vehicle_id"].'" style = "color:#428bca;">'.$saved_row["vehicle_reg"].'</a>'; ?>
						</td>
						<td>
							<?php echo $saved_row["username"]; ?>
						</td>
						<td>
							<?php echo $saved_row["survey_status"]; ?>
						</td>
						<td>
							<!--<a href="<?php echo BASE_URL; ?>edit_user.php?user_id=<?php echo $row["user_id"] ?>" style="color:blue">edit</a>-->
							<a href="<?php echo BASE_URL; ?>edit-report.php?surveyid=<?php echo $survey_id; ?>" class="btn btn-primary" >Edit</a>
						</td>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div class="btn-ctrl">	
			<a href="<?php echo BASE_URL; ?>view-reports.php?status=draft&limit=all" class="btn btn-primary">View All Report Drafts >></a>
		</div>

	</div><!-- eport-section-body -->

</div><!-- report-group -->