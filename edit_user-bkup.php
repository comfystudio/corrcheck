<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header.php"); ?>
<?php

    /* ==================== CONTROLLER CODE ==================== */     

    // Get roles
    $roles = get_user_roles($db);  
    $companies = get_company_names($db);   
    $success_message = "";

    // If this page is reached via link on user-management then get user_id from $_GET
    // Else get it from the hidden field in the submitted edit form
    if(empty($_POST) && isset($_GET['user_id'])) {
        $user_id = $_GET['user_id'];

        // Query db to get fields for form
        $user_dets = assign_vars($user_id, $db); // See functions
        $username = $user_dets["username"];
        $firstname =$user_dets["first_name"];
        $lastname = $user_dets["last_name"];
        $title = $user_dets["title"];
        $email = $user_dets["email"];
        $telno = $user_dets["tel_no"];
        $usertype = $user_dets["user_role_id"];
        $companyid = $user_dets["company_id"];

    }else{

        $user_id = $_POST["user_id"];       

        // Get fields from $_POST        
        $username = $_POST["username"];
        $firstname =$_POST["first_name"];
        $lastname = $_POST["last_name"];
        $title = $_POST["title"];
        $email = $_POST["email"];
        $telno = $_POST["tel_no"];
        $usertype = $_POST["usertype"];
        $companyid = $_POST["company"];
    }  


    // This if statement checks to determine whether the edit form has been submitted 
    // If it has, then the account updating code is run, otherwise the form is displayed 
    if(!empty($_POST)) 
    {
        // If we have made it here the edit form has been submitted so get everyting from $_POST

        // echo "Form has been submitted - let's do some simple error checking!";
        // else the edit form has been submitted 
        $user_id = $_POST["user_id"];
        $user_email = get_user_email($user_id, $db);     
        $username = $_POST["username"];


        $error_check = false;   
        $error_array = array();

        /* =========================================== */
        // STAGE 2: Secondary Checks
        /* =========================================== */

        // ==================== Check passwords
        // 
        if(!empty($_POST['password']) && !empty($_POST['password_confirm'])) {
            if($_POST['password'] != $_POST['password_confirm']){

                $error_array['password_dup_check'] = "Passwords do not match!";
                $error = true;

            }
        }

        // Make sure the user entered a valid E-Mail address 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            die("Invalid E-Mail Address"); 
        } 

        // If the email address in the form submission does not match the one in the database
        // This means that the email address is being changed therefore make sure that the 
        // new value does not conflict with a value that is already in the system. 
        // If the user is not changing their E-Mail address this check is not needed. 
        if($_POST['email'] != $user_email) 
        { 
            // Define our SQL query 
            $query = " 
                SELECT 
                    1 
                FROM tbl_users 
                WHERE 
                    email = :email 
            "; 
             
            // Define our query parameter values 
            $query_params = array( 
                ':email' => $_POST['email'] 
            ); 
             
            try 
            { 
                // Execute the query 
                $stmt = $db->prepare($query); 
                $result = $stmt->execute($query_params); 
            } 
            catch(PDOException $ex) 
            { 
                // Note: On a production website, you should not output $ex->getMessage(). 
                // It may provide an attacker with helpful information about your code.  
                die("Failed to run query 2: " . $ex->getMessage()); 
            } 
             
            // Retrieve results (if any) 
            $row = $stmt->fetch(); 
            if($row) 
            { 
                //die("This E-Mail address is already in use"); 
                $error_array['email_exists'] = "This email address is already registered";
                $error_check = true;
            } 
        } 

        // If the user entered a new password, we need to hash it and generate a fresh salt 
        // for good measure. 
        if(!empty($_POST['password'])) 
        { 
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
            $password = hash('sha256', $_POST['password'] . $salt); 
            for($round = 0; $round < 65536; $round++) 
            { 
                $password = hash('sha256', $password . $salt); 
            } 
        } 
        else 
        { 
            // If the user did not enter a new password we will not update their old one. 
            $password = null; 
            $salt = null;             
        } 

            if($error_check === false)
            {    

                // Initial query parameter values 
                $query_params = array(             
                    ':first_name' => $firstname, 
                    ':last_name' => $lastname, 
                    ':title' => $title, 
                    ':email' => $email, 
                    ':tel_no' => $telno, 
                    ':user_role_id' => $usertype,
                    ':company_id' => $companyid,
                    ':user_id' =>  $user_id          
                ); 

                // If the user is changing their password, then we need parameter values 
                // for the new password hash and salt too. 
                if($password !== null) 
                { 
                    $query_params[':password'] = $password; 
                    $query_params[':salt'] = $salt; 
                } 

                // Note how this is only first half of the necessary update query.  We will dynamically 
                // construct the rest of it depending on whether or not the user is changing 
                // their password. 
                $query = " 
                    UPDATE tbl_users 
                    SET 
                          first_name = :first_name,
                          last_name = :last_name,
                          title = :title,
                          email = :email,
                          tel_no = :tel_no,
                          user_role_id = :user_role_id,
                          company_id = :company_id
                ";         

                // If the user is changing their password, then we extend the SQL query 
                // to include the password and salt columns and parameter tokens too. 
                if($password !== null) 
                { 
                    $query .= " 
                          password = :password 
                        , salt = :salt 
                    ";             
                }         

                // Finally we finish the update query by specifying that we only wish 
                // to update the one record with for the current user. 
                $query .= " 
                    WHERE 
                        user_id = :user_id 
                ";         

                try 
                { 
                    // Execute the query 
                    $stmt = $db->prepare($query); 
                    $result = $stmt->execute($query_params); 
                } 
                catch(PDOException $ex) 
                { 
                    // Note: On a production website, you should not output $ex->getMessage(). 
                    // It may provide an attacker with helpful information about your code.  
                    die("Failed to run query 3: " . $ex->getMessage()); 
                } 


                // Now that the user's E-Mail address has changed, the data stored in the $_SESSION 
                // array is stale; we need to update it so that it is accurate. 
                // <-- GW: won't always be current logged in user that is changing own email, etc
                // Might need some additional code here depending on if user_id here matches user_id in session
                $_SESSION['user']['email'] = $_POST['email']; 

                // This redirects the user back to the members-only page after they register 
                // header("Location: private.php"); 
                 
                // Calling die or exit after performing a redirect using the header function 
                // is critical.  The rest of your PHP script will continue to execute and 
                // will be sent to the user if you do not die or exit. 
                // die("Redirecting to private.php"); 

                $success_message = "User updated successfully.";
        }

    }

?>

<!-- app main column -->
<div class="app-col-main col-md-10">

    <h1>User Management</h1>

    <?php 

    if(!empty($_POST)) {  

      // TESTING ERRORS - LIST ERROR ARRAY
      foreach($error_array as $error_key => $error_reason): ?>

        <p><?php echo $error_key; ?>: <?php echo $error_reason; ?>

        <?php endforeach; ?>

    <?php } ?>

    <form class="corrCheck_form form-horizontal" role="form" method="post" action="edit_user.php"> 

        <fieldset class="create_user rep-section">

          <h2>Edit User: <?php echo $username; ?></h2>

          <div class="section-questions">

            <?php echo $success_message; ?>

            <div class="question_row cf form-group">
                <label for="username" class="col-sm-3 control-label">First Name:</label>
                <div class="col-sm-4">                
                    <input type="text" class="form-control form-control" name="first_name" value="<?php echo $firstname; ?>" /> 
                </div>
            </div>

            <div class="question_row cf form-group">
                <label for="username" class="col-sm-3 control-label">Last Name:</label>
                <div class="col-sm-4">                                    
                    <input type="text" class="form-control form-control" name="last_name" value="<?php echo $lastname; ?>" />
                </div>
            </div>

            <div class="question_row cf form-group">
                <label for="username" class="col-sm-3 control-label">Title:</label>
                <div class="col-sm-4">                                                        
                    <input type="text" class="form-control form-control"  name="title" value="<?php echo $title; ?>" /> 
                </div>
            </div>

            <div class="question_row cf form-group">
                <label for="username" class="col-sm-3 control-label">Email Address:</label>
                <div class="col-sm-4">   
                    <input type="text" class="form-control form-control" name="email" value="<?php echo $email; ?>" />
                </div>
            </div>

            <div class="question_row cf form-group">
                <label for="username" class="col-sm-3 control-label">Contact Number:</label>
                <div class="col-sm-4">   
                    <input type="text" class="form-control form-control" name="tel_no" value="<?php echo $telno; ?>" />                     
                </div>
            </div>       

            <div class="question_row cf form-group">
                <label for="password" class="col-sm-3 control-label">Password:</label>
                <div class="col-sm-4">                       
                    <input type="password" class="form-control form-control" name="password" value="" /> 
                </div>
            </div> 

            <div class="question_row cf form-group">
                <label for="password" class="col-sm-3 control-label">Confirm Password:</label>
                <div class="col-sm-4">                                           
                    <input type="password" class="form-control form-control" name="password_confirm" value="" /> 
                </div>
            </div>


            <div class="question_row cf form-group">
                <label for="password" class="col-sm-3 control-label">User Role:</label>
               
                <div class="col-sm-4">    

                    <select name="usertype" id="" class="form-control">
                        <option value="" disabled selected> -- Select User Type -- </option>
              <?php 

                  foreach($roles as $role_id => $role):              
                  $role_name = $role["user_role"]; 

              ?>

                  <option value="<?php echo $role_id; ?>" <?php is_selected($role_id, $usertype); ?>><?php echo $role_name; ?></option>              

                <?php endforeach; ?>

                    </select>                    

                </div>
            </div>     


            <div class="question_row cf form-group">
                <label for="password" class="col-sm-3 control-label">Company Name:</label>   

                <div class="col-sm-4">            

             <select name="company" id="" class="form-control">
                <option value="" disabled selected> -- Select Company -- </option>
                  <?php 

                      foreach($companies as $company_id => $company):              
                      $company_name = $company["company_name"]; ?>

                      <option value="<?php echo $company_id; ?>" <?php is_selected($company_id, $companyid); ?>><?php echo $company_name; ?></option>              

                    <?php endforeach; ?>

                  </select>

                </div>
              
              </div><!-- close section-questions -->

              <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" /> 
              <input type="hidden" name="username" value="<?php echo $username; ?>" /> 

            </fieldset>


        <button id="submit" type="submit" value="Register" name="submit" class="btn btn-primary"/>Update User</button> 

    </form>

    </div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>

