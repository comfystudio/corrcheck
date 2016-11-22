<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php $user->redirect_customer(); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php    

    // Determine current edit 'state' and build config_company object accordingly
    if(empty($_POST) && isset($_GET['company_id'])) 
    {
        // If edit form is being loaded for the first time
        $company_args = array(
                "edit_mode" => "load_edit",
                "company_id" => $_GET['company_id']
        );           
    }
    else
    {   

        // If $_POST has been submitted
        // Pass the $_POST to the object
        $company_args = array(
                "edit_mode" => "post_edit",
                "company_id" => $_POST['company_id'],
                "post_array" => $_POST
        );

    }  

    // Create new Config_Co object
    $this_co = new Config_Company($db, $company_args); 

    // Assign user vars
    $companyVarArray = $this_co->companyVarArray;   

    // Build a new user form object
    $company_form = new Company_Form($db, $companyVarArray, "edit");

    // If form has been submitted then process it
    if(!empty($_POST)) 
    {

        $company_form->processCompanyForm();

    }

    // Assuming have made it this far output the physical form
    $company_form->renderCompanyForm();


?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>