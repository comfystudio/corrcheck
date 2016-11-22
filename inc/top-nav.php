<?php

	$thisPage = basename($_SERVER['PHP_SELF']);  
	$thisPage = preg_replace('/\\.[^.\\s]{3,4}$/', '', $thisPage);

?>
<?php if(($user->user_role == "Manager") || ($user->user_role == "Garage")) : ?>
<div class="top-nav">

	<ul>		
		<li>
			<a href="<?php echo BASE_URL ?>report-management.php" 
				class="<?php setCurrent($thisPage, 'inspections'); ?>">Inspections</a>
		</li>
		<li><a href="<?php echo BASE_URL ?>company-management.php"
			class="<?php setCurrent($thisPage, 'companies'); ?>">Company Management</a>
		</li>
		<li>
			<a href="<?php echo BASE_URL ?>user-management.php"
				class="<?php setCurrent($thisPage, 'users'); ?>">User Management</a>
		</li>
	</ul>

</div>
<?php endif; ?>