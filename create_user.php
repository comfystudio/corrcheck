<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php $user->redirect_customer(); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php

    // If we make it this far we must be a logged in user but ensure customer role does not proceed
    $user->redirect_customer();

    // If create form is being loaded for the first time user vars will be blank
    // Else if a form has been submitted then get the vars from $_POST
    if(empty($_POST)) {          
        $user_args = array("edit_mode" => "load_create" );
    }
    else
    {   
        $user_args = array(
                        "edit_mode" => "post_create",
                        "post_array" => $_POST 
                    );
    }
    

    // Create the config_user object    
    $this_user = new Config_User($db, $user_args);

    // Assign user vars
    $userVarArray = $this_user->userVarArray;
	
    // Create a new user form object
    $user_form = new User_Form($db, $userVarArray, "new");

    // If form has been submitted then process it
    if(!empty($_POST)) 
    {
        $user_form->processUserForm();

        // Now that a user's E-Mail address has changed, the data stored in the $_SESSION 
        // array might be stale if this is the current logged in user; if so we need to update it so that it is accurate. 
        // <-- GW: won't always be current logged in user that is changing own email, etc
        // Might need some additional code here depending on if user_id here matches user_id in session
        $_SESSION['user']['email'] = $_POST['email']; 

    }

    // Assuming have made it this far output the physical form
    $user_form->renderUserForm();

?>




<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>