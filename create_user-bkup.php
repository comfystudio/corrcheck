<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header.php"); ?>
<?php

  // If we are editing a user
  if(isset($_GET['user_id'])) {

    $user_id = $_GET['user_id'];

    // get details from the db based on the passed user_id
    $edit_query = "
      SELECT *
      FROM tbl_users
      WHERE user_id = $user_id
    ";

    try 
    { 
        // Run the query against your database table. 
        $stmt = $db->prepare($edit_query); 
        $stmt->execute(); 
        // echo "success on THIS query! ";
    } 
    catch(PDOException $ex) 
    { 
        // Note: On a production website, you should not output $ex->getMessage(). 
        // It may provide an attacker with helpful information about your code.  
        //echo " no success on query!";
        die("Failed to run query: " . $ex->getMessage()); 
    } 

    $row = $stmt->fetch(); 


    var_dump($row);

    $username = $row["username"];
    $firstname = $row["first_name"];
    $lastname = $row["last_name"];
    $title = $row["title"];
    $email =  $row["username"];
    $telno = $row["tel_no"];
    $usertype = $row["user_role_id"];
    $companyid = $row["company_id"];

    
  }else {

    // Else we are creating a new user and therefore set these to blank
    $username = $firstname = $lastname = $title = $email =  $telno = $usertype = $companyid = "";

  }

    /* ==================== CONTROLLER CODE ==================== */     

    // Get roles
    $roles = get_user_roles($db);  
    $companies = get_company_names($db);   
    $success_message = "";

    /* ==================== FORM PROCESS CODE ==================== */    

    // NOTE: PAGE SHOULD ONLY BE SEEN IF USER LOGGED IN AND IS ADMIN

    
    // Is form submitted?
    if(!empty($_POST)) {  

    //  var_dump($_POST);

        // echo "Form has been submitted - let's do some simple error checking!";
        $error_check = false;   
        $error_array = array();
      

        /* =========================================== */
        // STAGE 1: Simple 'empty' Checks
        /* =========================================== */

        // Username
        if(empty($_POST['username'])) 
        {               
            $error_array['name_error'] =  "Please enter a username.";
            $error_check = true;

        } else {
            $username = format_string($_POST['username']);          
        }

        // First Name
        if(empty($_POST['first_name'])) 
        { 
            $error_array['firstname_error'] = "Please enter a first name."; 
            $error_check = true;
        } else {
            $firstname = format_string($_POST['first_name']);           
        }

        // Last Name | Not Required
        if(!empty($_POST['last_name'])){        
            $lastname = format_string($_POST['last_name']);         
        }

        // Title | Not Required
        if(!empty($_POST['title'])){        
            $title = format_string($_POST['title']);            
        }

        // Email
        if(empty($_POST['email'])) 
        { 
            $error_array['email_error'] = "Please enter an email."; 
            $error_check = true;
        } else {
            $email = format_string($_POST['email']);            
        }

        // Contact | Not Required
        if(!empty($_POST['tel_no'])) 
        {             
            $telno = format_string($_POST['tel_no']);           
        }

        // Password
        if(empty($_POST['password'])) 
        { 
            $error_array['password_error'] = "Please enter a password."; 
            $error_check = true;
        }

        // Password Confirmation
        if(empty($_POST['password_confirm'])) 
        { 
            $error_array['password_confirm_error'] = "Please confirm password."; 
            $error_check = true;
        }

        // User Type
        if(!isset($_POST['usertype']))
        { 
            $error_array['usertype_error'] = "Please select a User Type."; 
            $error_check = true;
        }else {
            $usertype = $_POST['usertype'];
        }

        // Company
        if(!isset($_POST['company']))
        { 
            $error_array['company_error'] = "Please select a Company."; 
            $error_check = true;
        }else{
            $companyid = $_POST['company'];
        }



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

        // ================== Check that email is valid 
        
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            $error_array['email_invalid_error'] = "Invalid E-Mail Address"; 
            $error_check = true;
        } 

        // ==================== Check if the username entered by the user is already in use
        $query = " 
            SELECT 
                1 
            FROM tbl_users 
            WHERE 
                username = :username 
        "; 

        // Create array to contain definitions for special tokens in SQL Query
        $query_params = array( 
            ':username' => $_POST['username'] 
        ); 

         try 
        { 
            // Run the query against your database table. 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
            // echo "success on THIS query! ";
        } 
        catch(PDOException $ex) 
        { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            //echo " no success on query!";
            die("Failed to run query: " . $ex->getMessage()); 
        } 

        // The fetch() method returns an array representing the "next" row from 
        // the selected results, or false if there are no more rows to fetch. 
        $row = $stmt->fetch(); 

        // If a row was returned, then we know a matching username was found in 
        // the database already and we should not allow the user to continue. 
        if($row) 
        { 
            // NOTE: REPLACE THIS WITH PROPER ERROR HANDLING
            // die("This username is already in use"); 
            $error_array['username_dup_check'] = "This username is already in use";
            $error_check = true;
        } 

        // ==================== Same check for email
        $query = " 
            SELECT 
                1 
            FROM tbl_users 
            WHERE 
                email = :email 
        "; 

         $query_params = array( 
            ':email' => $_POST['email'] 
        );

        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        }  

         $row = $stmt->fetch(); 

         if($row) 
        { 
            // NOTE: REPLACE THIS WITH PROPER ERROR HANDLING
            //die("THIS email address is already registered"); 
            $error_array['email_dup_check'] = "This email address is already registered";
            $error_check = true;
            
        } 

        // ==================== Check Duplicate Passwords
        
        if($error_check === false)
        {           
            // No errors were encountered - process form
            //die("form will now be processed"); // <-- for testing         

            // An INSERT query is used to add new rows to a database table. 
            // Again, we are using special tokens (technically called parameters) to 
            // protect against SQL injection attacks. 
            // THIS CODE SHOULD ONLY FIRE IF NEITHER THE USERNAME OR EMAIL ALREADY EXISTS!!!!!!!!!
            $query = " 
                INSERT INTO tbl_users ( 
                    username,
                    first_name, 
                    last_name,
                    title,
                    email,
                    tel_no,
                    user_role_id,
                    company_id, 
                    password, 
                    salt                    
                ) VALUES ( 
                    :username,
                    :first_name,
                    :last_name,
                    :title,
                    :email,
                    :tel_no,
                    :user_role_id,
                    :company_id,
                    :password, 
                    :salt
                ) 
            "; 

            // A salt is randomly generated here to protect again brute force attacks and rainbow table attacks. 
            // The following statement generates a hex representation of an 8 byte salt.  
            // Representing this in hex provides no additional security, but makes it easier for humans to read.         
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 

            // This hashes the password with the salt so that it can be stored securely in your database.
            // The output of this next statement is a 64 byte hex string representing the 32 byte sha256 hash of the password.  
            // The original password cannot be recovered from the hash.  
            $password = hash('sha256', $_POST['password'] . $salt); 

            // Next we hash the hash value 65536 more times.  The purpose of this is to protect against brute force attacks.
            // Now an attacker must compute the hash 65537 times for each guess they make against a password
            // I the password were hashed only once the attacker would have been able to make 65537 different 
            // guesses in the same amount of time instead of only one. 
            for($round = 0; $round < 65536; $round++) 
            { 
                $password = hash('sha256', $password . $salt); 
            } 

            // Here we prepare our tokens for insertion into the SQL query.  We do not 
            // store the original password; only the hashed version of it.  We do store 
            // the salt (in its plaintext form; this is not a security risk). 
            $query_params = array( 
                ':username' => $_POST['username'], 
                ':first_name' => $_POST['first_name'], 
                ':last_name' => $_POST['last_name'], 
                ':title' => $_POST['title'], 
                ':email' => $_POST['email'], 
                ':tel_no' => $_POST['tel_no'], 
                ':user_role_id' => $_POST['usertype'],
                ':company_id' => $_POST['company'],
                ':password' => $password, 
                ':salt' => $salt
            ); 



            try 
            { 
                // Execute the query to create the user 
                $stmt = $db->prepare($query); 
                $result = $stmt->execute($query_params); 
            } 
            catch(PDOException $ex) 
            { 
                // Note: On a production website, you should not output $ex->getMessage(). 
                // It may provide an attacker with helpful information about your code.  
                die("Failed to run THIS query: " . $ex->getMessage()); 
            }

          $success_message = "New user successfully created.";

            // This redirects the user back to the login page after they register 
            
          // header("Location: login.php"); 

            // Calling die or exit after performing a redirect using the header function 
            // is critical.  The rest of your PHP script will continue to execute and 
            // will be sent to the user if you do not die or exit. 
            
          // die("Redirecting to login.php"); 

         }      

    }// close !empty($_POST)

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

    <form class="corrCheck_form form-horizontal" role="form" method="post" action="create_user.php"> 

        <fieldset class="create_user rep-section">

          <h2>Create New User</h2>

          <div class="section-questions">

            <?php echo $success_message; ?>

            <div class="question_row cf form-group">
                <label for="username" class="col-sm-3 control-label">Username:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control form-control" name="username" id="username" value="<?php echo $username; ?>" /> 
                </div>
            </div>

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

            </fieldset>


        <button id="submit" type="submit" value="Register" name="submit" class="btn btn-primary"/>Create New User</button> 

    </form>

    </div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>