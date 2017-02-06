<?php

/**
 * ===================
 * Class: User Form
 * ===================
 *
 * This is used to build the form used in the creation & modification of user accounts.
 *
 */
class User_Form
{

    // The database object
    protected $db;

    // User variables
    var $user_id;
    var $username;
    var $first_name;
    var $last_name;
    var $title;
    var $email;
    var $tel_no;
    var $user_role_id;
    var $user_role;
    var $company_id;
    var $company_name;
    var $vehicle_permission;
    var $dashboard_permission;
    var $error_check;

    var $form_type; // options are "new" or "edit" only!

    // Arrays
    var $userVarArray;
    var $userRolesArray;
    var $companyNamesArray;
    var $errorArray;

    var $success_message;


    /* ========= CONSTRUCTOR ========= */
    public function __construct($db, $user_vars, $form_type)
    {

        $this->db = $db;     // Database object
        $this->userVarArray = $user_vars;
        $this->form_type = $form_type;

        $this->success_message = "";

        $this->user_id = $this->userVarArray["user_id"];

        $this->username = $this->userVarArray["username"];
        $this->first_name = $this->userVarArray["first_name"];
        $this->last_name = $this->userVarArray["last_name"];
        $this->title = $this->userVarArray["title"];
        $this->email = $this->userVarArray["email"];
        $this->tel_no = $this->userVarArray["tel_no"];
        $this->user_role_id = $this->userVarArray["user_role_id"];
        $this->company_id = $this->userVarArray["company_id"];
        $this->company_name = $this->userVarArray["company_name"];
        $this->user_role = $this->userVarArray["user_role"];
        $this->vehicle_permission = $this->userVarArray['vehicle_permission'];
        $this->dashboard_permission = $this->userVarArray['dashboard_permission'];

        $this->userRolesArray = $this->getUserRoles();
        $this->companyNamesArray = $this->getCompanyNames();

        // Initialise error handling vars
        $this->error_check = false;
        $this->errorArray = array();

    }

    function getUserRoles()
    {

        try {
            $results = $this->db->query("
	      SELECT
	        user_role_id,
	        role
	      FROM tbl_user_roles
	      ;               
	      ");
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database.";
            exit;
        }

        $roles = array();

        while ($row = $results->fetch(PDO::FETCH_ASSOC)):

            $role_id = $row["user_role_id"];
            $role = $row["role"];

            $roles[$role_id]["user_role"] = $role;

        endwhile;
        return $roles;

    }

    function getCompanyNames()
    {

        try {
            $results = $this->db->query("
        SELECT
          company_ID,
          company_name
        FROM tbl_companies
        ;               
        ");
        } catch (Exception $e) {
            echo "Data could not be retrieved from the database.";
            exit;
        }

        $companies = array();

        while ($row = $results->fetch(PDO::FETCH_ASSOC)):

            $company_id = $row["company_ID"];
            $company_name = $row["company_name"];

            $companies[$company_id]["company_name"] = $company_name;

        endwhile;
        return $companies;

    }


    function isSelected($type_id, $user_id)
    {
        if ($type_id == $user_id) {
            echo "selected";
        }
    }

    function processUserForm()
    {

        if ($this->form_type == "new") {
            $this->processNewUserForm();
        }

        if ($this->form_type == "edit") {
            $this->processEditUserForm();
        }

    }

// Process the $_POST submission where a new user is being created
    function processNewUserForm()
    {

        /* =========================================== */
        // STAGE 1: Simple 'empty' Checks
        /* =========================================== */

        // Username
        if (empty($_POST['username'])) {
            $this->errorArray['name_error'] = "Please enter a username.";
            $this->error_check = true;

        } else {
            $this->username = format_string($_POST['username']);
        }

        // First Name
        if (empty($_POST['first_name'])) {
            $this->errorArray['firstname_error'] = "Please enter a first name.";
            $this->error_check = true;
        } else {
            $this->firstname = format_string($_POST['first_name']);
        }

        // Last Name | Not Required
        if (!empty($_POST['last_name'])) {
            $this->lastname = format_string($_POST['last_name']);
        }

        // Title | Not Required
        if (!empty($_POST['title'])) {
            $this->title = format_string($_POST['title']);
        }

        // Email
        if (empty($_POST['email'])) {
            $this->errorArray['email_error'] = "Please enter an email.";
            $this->error_check = true;
        } else {
            $this->email = format_string($_POST['email']);
        }

        // Contact | Not Required
        if (!empty($_POST['tel_no'])) {
            $this->tel_no = format_string($_POST['tel_no']);
        }

        // Password
        if (empty($_POST['password'])) {
            $this->errorArray['password_error'] = "Please enter a password.";
            $this->error_check = true;
        }

        // Password Confirmation
        if (empty($_POST['password_confirm'])) {
            $this->errorArray['password_confirm_error'] = "Please confirm password.";
            $this->error_check = true;
        }

        // User Type
        if (!isset($_POST['usertype'])) {
            $this->errorArray['usertype_error'] = "Please select a User Type.";
            $this->error_check = true;
        } else {
            $this->user_role_id = $_POST['usertype'];
        }

        // Company
        if (!isset($_POST['company'])) {
            $this->errorArray['company_error'] = "Please select a Company.";
            $this->error_check = true;
        } else {
            $this->company_id = $_POST['company'];
        }

        //vehicle_permission
        if (isset($_POST['vehicle_permission'])) {
            $this->vehicle_permission = $_POST['vehicle_permission'];
        }

        //dashboard_permission
        if (isset($_POST['dashboard_permission'])) {
            $this->dashboard_permission = $_POST['dashboard_permission'];
        }

        /* ============================================================ */
        // STAGE 2: Secondary Checks 
        // NOTE - these are repeated in processEditUserForm()
        // so here is a good area to focus if time for code refactoring
        // -- All these checks should be individual functions
        /* ============================================================ */

        // ==================== Check passwords
        if (!empty($_POST['password']) && !empty($_POST['password_confirm'])) {
            if ($_POST['password'] != $_POST['password_confirm']) {

                $this->errorArray['password_dup_check'] = "Passwords do not match!";
                $this->error_check = true;

            }
        }

        // ================== Check that email is valid         
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errorArray['email_invalid_error'] = "Invalid E-Mail Address";
            $this->error_check = true;
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

        try {
            // Run the query against your database table. 
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($query_params);
            // echo "success on THIS query! ";
        } catch (PDOException $ex) {
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
        if ($row) {
            // NOTE: REPLACE THIS WITH PROPER ERROR HANDLING
            // die("This username is already in use"); 
            $this->errorArray['username_dup_check'] = "This username is already in use";
            $this->error_check = true;

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

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($query_params);
        } catch (PDOException $ex) {
            die("Failed to run query: " . $ex->getMessage());
        }

        $row = $stmt->fetch();

        if ($row) {
            // NOTE: REPLACE THIS WITH PROPER ERROR HANDLING
            //die("THIS email address is already registered"); 
            $this->errorArray['email_dup_check'] = "This email address is already registered";
            $this->error_check = true;

        }

        if ($this->error_check === false) {
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
                    salt,
                    vehicle_permission,
                    dashboard_permission
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
                    :salt,
                    :vehicle_permission,
                    :dashboard_permission
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
            for ($round = 0; $round < 65536; $round++) {
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
                ':salt' => $salt,
                ':vehicle_permission' => $_POST['vehicle_permission'],
                ':dashboard_permission' => $_POST['dashboard_permission']
            );

            try {
                // Execute the query to create the user 
                $stmt = $this->db->prepare($query);
                $result = $stmt->execute($query_params);
            } catch (PDOException $ex) {
                // Note: On a production website, you should not output $ex->getMessage(). 
                // It may provide an attacker with helpful information about your code.  
                die("Failed to run THIS query: " . $ex->getMessage());
            }

            $this->success_message = "New user successfully created.";

            // This redirects the user back to the login page after they register 

            // header("Location: login.php");

            // Calling die or exit after performing a redirect using the header function 
            // is critical.  The rest of your PHP script will continue to execute and 
            // will be sent to the user if you do not die or exit. 

            // die("Redirecting to login.php");

        }

    }

// Process the $_POST submission where an existing user is being edited
    function processEditUserForm()
    {
        // Get the stored email for the current user
        $user_email = get_user_email($this->user_id, $this->db);

        // Check passwords
        if (!empty($_POST['password']) && !empty($_POST['password_confirm'])) {
            if ($_POST['password'] != $_POST['password_confirm']) {
                $this->errorArray['password_dup_check'] = "Passwords do not match!";
                $this->error_check = true;
            }
        }

        // Make sure the user entered a valid E-Mail address
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errorArray['valid_email_check'] = "Not a valid email address";
            $this->error_check = true;
        }

        // If the email address in the form submission does not match the one in the database
        // This means that the email address is being changed therefore make sure that the
        // new value does not conflict with a value that is already in the system.
        // If the user is not changing their E-Mail address this check is not needed.
        if ($_POST['email'] != $user_email) {
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

            try {
                // Execute the query
                $stmt = $this->db->prepare($query);
                $result = $stmt->execute($query_params);
            } catch (PDOException $ex) {
                // Note: On a production website, you should not output $ex->getMessage().
                // It may provide an attacker with helpful information about your code.
                die("Failed to run query 2: " . $ex->getMessage());
            }

            // Retrieve results (if any)
            $row = $stmt->fetch();
            if ($row) {
                //die("This E-Mail address is already in use");
                $this->errorArray['email_exists'] = "This email address is already registered";
                $this->error_check = true;
            }
        } // end if

        // If the user entered a new password, we need to hash it and generate a fresh salt
        // for good measure.
        if (!empty($_POST['password'])) {

            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));
            $password = hash('sha256', $_POST['password'] . $salt);
            for ($round = 0; $round < 65536; $round++) {
                $password = hash('sha256', $password . $salt);
            }
        } else {
            // If the user did not enter a new password we will not update their old one.
            $password = null;
            $salt = null;
        }

        if ($this->error_check === false) {

            // Initial query parameter values
            $query_params = array(
                ':first_name' => $this->first_name,
                ':last_name' => $this->last_name,
                ':title' => $this->title,
                ':email' => $this->email,
                ':tel_no' => $this->tel_no,
                ':user_role_id' => $this->user_role_id,
                ':company_id' => $this->company_id,
                ':user_id' => $this->user_id,
                ':vehicle_permission' => $this->vehicle_permission,
                ':dashboard_permission' => $this->dashboard_permission
            );

            // If the user is changing their password, then we need parameter values
            // for the new password hash and salt too.
            if ($password !== null) {
                $query_params[':password'] = $password;
                $query_params[':salt'] = $salt;
            }

            // Note how this is only first half of the necessary update query.
            // We will dynamically construct the rest of it depending on whether
            //  or not the user is changing their password.
            $query = "
          UPDATE tbl_users 
          SET 
                first_name = :first_name,
                last_name = :last_name,
                title = :title,
                email = :email,
                tel_no = :tel_no,
                user_role_id = :user_role_id,
                company_id = :company_id,
                vehicle_permission = :vehicle_permission,
                dashboard_permission = :dashboard_permission
      ";

            // If the user is changing their password, then we extend the SQL query
            // to include the password and salt columns and parameter tokens too.
            if ($password !== null) {
                $query .= "
              ,password = :password 
              , salt = :salt 
          ";
            }

            // Finally we finish the update query by specifying that we only wish
            // to update the one record with for the current user.
            $query .= "
          WHERE 
              user_id = :user_id 
      ";

            try {
                // Execute the query
                $stmt = $this->db->prepare($query);
                $result = $stmt->execute($query_params);
            } catch (PDOException $ex) {
                // Note: On a production website, you should not output $ex->getMessage().
                // It may provide an attacker with helpful information about your code.
                die("Failed to run query 3: " . $ex->getMessage());
            }

            $this->success_message = "User updated successfully.";


        } // end error_check === false

    } // close function processUserForm()


    function renderUserForm()
    {
        ?>

        <!-- app main column -->
        <div class="app-col-main col-md-10">

            <h1>User Management</h1>

            <?php

            if (!empty($_POST)) {

                // var_dump($_POST);

                // TESTING ERRORS - LIST ERROR ARRAY
                foreach ($this->errorArray as $error_key => $error_reason): ?>

                    <p><?php echo $error_key; ?>: <?php echo $error_reason; ?></p>

                <?php endforeach; ?>

            <?php } ?>

            <form class="corrCheck_form form-horizontal" role="form" method="post"
                  action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'>

                <fieldset class="create_user rep-section">

                    <?php if ($this->form_type == "edit"): ?>

                        <h2>Edit User: <?php echo $this->username; ?></h2>

                    <?php endif; ?>

                    <div class="section-questions">

                        <?php if ($this->success_message != ""): ?>
                            <div class="alert alert-success" role="alert">
                                <strong><?php echo $this->success_message; ?></strong></div>
                        <?php endif; ?>

                        <?php if ($this->form_type == "new"): ?>

                            <div class="question_row cf form-group">
                                <label for="username" class="col-sm-3 control-label">Username:</label>

                                <div class="col-sm-4">
                                    <input type="text" class="form-control form-control" name="username" id="username"
                                           value="<?php echo $this->username; ?>"/>
                                </div>
                            </div>

                        <?php endif; ?>

                        <div class="question_row cf form-group">
                            <label for="username" class="col-sm-3 control-label">First Name:</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control" name="first_name"
                                       value="<?php echo $this->first_name; ?>"/>
                            </div>
                        </div>

                        <div class="question_row cf form-group">
                            <label for="username" class="col-sm-3 control-label">Last Name:</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control" name="last_name"
                                       value="<?php echo $this->last_name; ?>"/>
                            </div>
                        </div>

                        <div class="question_row cf form-group">
                            <label for="username" class="col-sm-3 control-label">Title:</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control" name="title"
                                       value="<?php echo $this->title; ?>"/>
                            </div>
                        </div>

                        <div class="question_row cf form-group">
                            <label for="username" class="col-sm-3 control-label">Email Address:</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control" name="email"
                                       value="<?php echo $this->email; ?>"/>
                            </div>
                        </div>

                        <div class="question_row cf form-group">
                            <label for="username" class="col-sm-3 control-label">Contact Number:</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control" name="tel_no"
                                       value="<?php echo $this->tel_no; ?>"/>
                            </div>
                        </div>

                        <div class="question_row cf form-group">
                            <label for="password" class="col-sm-3 control-label">Password:</label>

                            <div class="col-sm-4">
                                <input type="password" class="form-control form-control" name="password" value=""/>
                            </div>
                        </div>

                        <div class="question_row cf form-group">
                            <label for="password" class="col-sm-3 control-label">Confirm Password:</label>

                            <div class="col-sm-4">
                                <input type="password" class="form-control form-control" name="password_confirm"
                                       value=""/>
                            </div>
                        </div>


                        <div class="question_row cf form-group">
                            <label for="password" class="col-sm-3 control-label">User Role:</label>

                            <div class="col-sm-4">

                                <select name="usertype" id="usertype" class="form-control">
                                    <option value="" disabled selected> -- Select User Type --</option>
                                    <?php

                                    foreach ($this->userRolesArray as $role_id => $role):
                                        $role_name = $role["user_role"];

                                        ?>

                                        <option
                                            value="<?php echo $role_id; ?>" <?php $this->isSelected($role_id, $this->user_role_id); ?>><?php echo $role_name; ?></option>

                                    <?php endforeach; ?>

                                </select>

                            </div>
                        </div>


                        <div class="question_row cf form-group">
                            <label for="password" class="col-sm-3 control-label">Company Name:</label>

                            <div class="col-sm-4">

                                <select name="company" id="company" class="form-control">
                                    <option value="" disabled selected> -- Select Company --</option>
                                    <?php

                                    foreach ($this->companyNamesArray as $company_id => $company):
                                        $company_name = $company["company_name"]; ?>

                                        <option
                                            value="<?php echo $company_id; ?>" <?php $this->isSelected($company_id, $this->company_id); ?>><?php echo $company_name; ?></option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>
                        <!-- close section-questions -->

                        <?php if ($this->form_type == "edit"): ?>

                            <input type="hidden" name="user_id" value="<?php echo $this->user_id; ?>"/>
                            <input type="hidden" name="username" value="<?php echo $this->username; ?>"/>

                        <?php endif; ?>

                        <input type="hidden" class="form-control form-control" name="vehicle_permission" id="vehicle_permission"
                               value="0"/>

                        <div class="question_row cf form-group">
                            <label for="vehicle_permission" class="col-sm-3 control-label">Vehicle Permission:</label>

                            <div class="col-sm-4">
                                <input type="checkbox" class="form-control form-control" name="vehicle_permission"
                                       id="vehicle_permission"
                                       value="1" <?php if (isset($this->vehicle_permission) && $this->vehicle_permission == 1) {
                                    echo 'checked';
                                } ?>/>
                            </div>
                        </div>

                        <input type="hidden" class="form-control form-control" name="dashboard_permission" id="dashboard_permission"
                               value="2"/>

                        <div class="question_row cf form-group">
                            <label for="dashboard_permission" class="col-sm-3 control-label">Dashboard Permission:</label>

                            <div class="col-sm-4">
                                <input type="checkbox" class="form-control form-control" name="dashboard_permission"
                                       id="dashboard_permission"
                                       value="1" <?php if (isset($this->dashboard_permission) && $this->dashboard_permission == 1) {
                                    echo 'checked';
                                } ?>/>
                            </div>
                        </div>
                </fieldset>
                <button id="submit" type="submit" value="Register" name="submit" class="btn btn-primary"/>Update User</button>

            </form>

        </div><!-- app-col-main -->

    <?php
    }


} //close class