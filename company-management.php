<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php $user->redirect_customer(); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
	<!-- app main column -->
	<div class="app-col-main grid_12 alpha">  

		<h2>Company Management</h2>		

		<?php

		// Do DB Query
		 $query = " 
	                SELECT 
	                	tbl_companies.company_ID,
			        	tbl_companies.company_name,
			        	tbl_companies.addr_1,			        				        	
			        	tbl_companies.email,
		                tbl_companies.telno
			        FROM tbl_companies            			        
		            ORDER BY company_name    
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

	    // Finally, we can retrieve all of the found rows into an array using fetchAll 
    	$rows = $stmt->fetchAll(); 

	?>

	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<td>
					Company Name
				</td>
				<td>
					Address Line 1
				</td>
				<td>
					Email
				</td>
				<td>
					Contact Number
				</td>				
				<td>
					&nbsp;
				</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rows as $row): ?> 				
				<tr>
					<td>
						<?php echo $row["company_name"]; ?>
					</td>
					<td>
						<?php  echo $row["addr_1"]; ?>
					</td>
					<td>
						<?php echo $row["email"]; ?>
					</td>
					<td>
						<?php echo $row["telno"]; ?>
					</td>					
					<td>
						<a href="<?php echo BASE_URL; ?>edit_company.php?company_id=<?php echo $row["company_ID"] ?>" class="btn btn-primary">edit</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<a href="<?php echo BASE_URL; ?>create_company.php" class="btn btn-success">Create New Company</a>



	</div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>