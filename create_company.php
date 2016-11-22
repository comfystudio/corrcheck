<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php $user->redirect_customer(); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php  

    // If create form is being loaded for the first time user vars will be blank
    // Else if a form has been submitted then get the vars from $_POST
    if(empty($_POST)) {          
        $company_args = array("edit_mode" => "load_create" );
    }
    else
    {   
        $company_args = array(
                        "edit_mode" => "post_create",
                        "post_array" => $_POST 
                    );
    }  

    // Create new Config_Co object
    $this_co = new Config_Company($db, $company_args); 

    // Assign user vars
    $companyVarArray = $this_co->companyVarArray; 

    // Build a new user form object
    $company_form = new Company_Form($db, $companyVarArray, "new");

    // If form has been submitted then process it
    if(!empty($_POST)) 
    {

        $company_form->processCompanyForm();

    }

    // Assuming have made it this far output the physical form
    $company_form->renderCompanyForm();


?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>