<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php $user->redirect_customer(); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>



<!-- app main column -->

<div class="app-col-main col-xs-10">



	<h1>Dashboard</h1>



	<?php $user->userGreating(); ?>



</div><!-- app-col-main -->



<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>