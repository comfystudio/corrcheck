<div class="search-filter cf">
		
		<?php if($search_message!=""): ?>

			<p class="has-error message-error"><?php echo $search_message; ?></p>

		<?php endif; ?>

		<form method="post">

			<?php if(($user->user_role == "Manager") || ($user->user_role == "Garage")) : ?>


			<div class="sf-company-filter search-group">

				<label for="company-filter">
					Select Company:
				</label>

			<select name="company-filter" id="company-filter" class="form-control">
				<option value="search-all" <?php if($_POST["company-filter"]=="search-all") echo selected; ?>>Search All</option>
				<?php 

	            // Get companies
	            $companies = get_company_names($db);  

	            foreach($companies as $company_id => $company):              
	              $company_name = $company["company_name"]; ?>

	              <option value="<?php echo $company_id; ?>"
	              	<?php 

	              		if(isset($_POST["company-filter"])){

	              			if($_POST["company-filter"] == $company_id){
	              				echo "selected";
	              			}
	              		}

	              	?>
	              	><?php echo $company_name; ?></option>              

	            <?php endforeach; ?>
	            

          	</select>

          </div><!-- sf-company-filter -->

      	<?php endif; ?>



          <div class="sf-search-box search-group">
          	<label for="reg-search-input">Registration Search: </label>
          	<input type="text" name="reg-search-input" id="reg-search-input" class="form-control" value="<?php echo $_POST['reg-search-input']; ?>">


          </div>

          <button id="btn-filter" name="btn-filter" type="submit" class="btn btn-success">Apply Filter</button>

		</form>
	</div><!-- .search-filter -->