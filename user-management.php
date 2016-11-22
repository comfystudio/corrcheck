<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php $user->redirect_customer(); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>

<!-- app main column -->
<?php $user->redirect_customer(); ?>

<div class="app-col-main grid_12 alpha">  

	<h2>User Management</h2>

	<?php // $user->userGreating(); ?>

	<?php

		// Do DB Query
		 $query = " 
	                SELECT 
	                	tbl_users.user_id,
			        	tbl_users.username,
			        	tbl_users.first_name,
			        	tbl_users.last_name,
		                tbl_users.email,
			        	tbl_user_roles.role as role_name,
		                tbl_companies.company_name as company_name	            
			        FROM tbl_users             
			        LEFT OUTER JOIN tbl_user_roles
			        	ON tbl_users.user_role_id = tbl_user_roles.user_role_id		                
		            LEFT OUTER JOIN tbl_companies
			        	ON tbl_users.company_id = tbl_companies.company_id
		            ORDER BY company_name, username    
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
					Username
				</td>
				<td>
					Name
				</td>
				<td>
					Email
				</td>
				<td>
					Role Level
				</td>
				<td>
					Company
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
						<?php echo $row["username"]; ?>
					</td>
					<td>
						<?php  echo $row["last_name"] . ", " . $row["first_name"]; ?>
					</td>
					<td>
						<?php echo $row["email"]; ?>
					</td>
					<td>
						<?php echo $row["role_name"]; ?>
					</td>
					<td>
						<?php echo $row["company_name"]; ?>
					</td>
					<td>
						<a href="<?php echo BASE_URL; ?>edit_user.php?user_id=<?php echo $row["user_id"] ?>" class="btn btn-primary">edit</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<a href="<?php echo BASE_URL; ?>create_user.php" class="btn btn-success">Create New User</a>

</div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>