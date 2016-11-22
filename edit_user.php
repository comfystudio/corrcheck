<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php $user->redirect_customer(); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php    

    // If we make it this far we must be a logged in user but ensure customer role does not proceed
    $user->redirect_customer();
    
    // Determine current edit 'state' and build config_user object accordingly
    if(empty($_POST) && isset($_GET['user_id'])) 
    {
        // If edit form is being loaded for the first time
        $user_args = array(
                "edit_mode" => "load_edit",
                "user_id" => $_GET['user_id']
        );    
    }
    else
    {
        // If $_POST has been submitted
        // Pass the $_POST to the object
        $user_args = array(
                "edit_mode" => "post_edit",
                "user_id" => $_POST['user_id'],
                "post_array" => $_POST
        );

    }  

    // Create new Config_User object
    $this_user = new Config_User($db, $user_args);

    // Assign user vars
    $userVarArray = $this_user->userVarArray;

    // Build a new user form object
    $user_form = new User_Form($db, $userVarArray, "edit");
    
    // If form has been submitted then process it
    if(!empty($_POST)) 
    {

        $user_form->processUserForm();

        // Now that the user's E-Mail address has changed, the data stored in the $_SESSION 
        // array is stale; we need to update it so that it is accurate. 
        // <-- GW: won't always be current logged in user that is changing own email, etc
        // Might need some additional code here depending on if user_id here matches user_id in session
        $_SESSION['user']['email'] = $_POST['email']; 

    }

    // Assuming have made it this far output the physical form
    $user_form->renderUserForm();

?>

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>