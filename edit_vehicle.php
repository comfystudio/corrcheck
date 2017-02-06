<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php
// If we make it this far we must be a logged in user but ensure customer role does not proceed
if(!$user->check_vehicle_premission()){
    header("Location: report-listing.php");
    die("Redirecting to report-listing.php");
}

// Check if we got a Get vehicle_id var
if(!isset($_GET) || empty($_GET['vehicle_id'])){
    header("Location: vehicle-management.php");
    die("Redirecting to vehicle-management.php");
}

$_GET['vehicle_id'] = format_string($_GET['vehicle_id']);
// Assuming we now have vehicle_id check if there's a related vehicle
$query = "
            SELECT
                t1.*
            FROM tbl_vehicles t1
            WHERE
                t1.id = :id
            LIMIT 1
        ";
$query_params = array(
    ':id' => $_GET['vehicle_id']
);
try {
    $stmt = $db->prepare($query);
    $result = $stmt->execute($query_params);
} catch (PDOException $ex) {
    die("Failed to run query: " . $ex->getMessage());
}
$vehicle = $stmt->fetch();

// if no related vehicle is found bounce user back to index page
if(!isset($vehicle) || empty($vehicle)){
    header("Location: vehicle-management.php");
    die("Redirecting to vehicle-management.php");
}

// We need to get companies
$query = "
            SELECT t1.company_ID, t1.company_name
              FROM tbl_companies t1
        ";
try {
    // These two statements run the query against your database table.
    $stmt = $db->prepare($query);
    $stmt->execute();
} catch (PDOException $ex) {
    // Note: On a production website, you should not output $ex->getMessage().
    // It may provide an attacker with helpful information about your code.
    die("Failed to run query: " . $ex->getMessage());
}
// Finally, we can retrieve all of the found rows into an array using fetchAll
$companies = $stmt->fetchAll();

// We need to get users
$query = "
                SELECT t1.user_id, t1.username
                  FROM tbl_users t1
            ";
try {
    // These two statements run the query against your database table.
    $stmt = $db->prepare($query);
    $stmt->execute();
} catch (PDOException $ex) {
    // Note: On a production website, you should not output $ex->getMessage().
    // It may provide an attacker with helpful information about your code.
    die("Failed to run query: " . $ex->getMessage());
}
// Finally, we can retrieve all of the found rows into an array using fetchAll
$users = $stmt->fetchAll();


// If form is submitted process it
if(!empty($_POST)){
    // adding company_id and user_id if not in post
    if(!isset($_POST['company_id']) || empty($_POST['company_id'])){
        if(isset($vehicle['company_id']) && !empty($vehicle['company_id'])){
            $_POST['company_id'] = $vehicle['company_id'];
        }
    }
    if(!isset($_POST['user_id']) || empty($_POST['user_id'])){
        if(isset($vehicle['user_id']) && !empty($vehicle['user_id'])){
            $_POST['user_id'] = $vehicle['user_id'];
        }
    }

    // Validation
    foreach($_POST as $key => $data){
        $_POST[$key] = format_string($data);
    }

    // reg
    if (empty($_POST['reg'])) {
        $errorArray['reg'] = "Please enter a Vehicle Registration.";
        $error_check = true;
    }

    // check reg is unique
    $query = "
            SELECT
                t1.id
            FROM tbl_vehicles t1
            WHERE
                reg = :reg
                AND t1.id != :id
        ";
    $query_params = array(
        ':reg' => $_POST['reg'],
        ':id' => $vehicle['id']
    );
    try {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $row = $stmt->fetch();
    if ($row) {
        $errorArray['reg'] = "This vehicle registration is already in system";
        $error_check = true;
    }

    // If no errors then edit vehicle
    if(!isset($error_check)){

        ksort($_POST);

        if(isset($_POST['psv_date']) && !empty($_POST['psv_date'])){
            $_POST['psv_date'] = date('Y-m-d', strtotime($_POST['psv_date']));
        }

        if(isset($_POST['start_time']) && !empty($_POST['start_time'])){
            $_POST['start_time'] = date('Y-m-d', strtotime($_POST['start_time']));
        }
        $fieldDetails = NULL;
        foreach($_POST as $key => $value) {
            if(!is_null($value) && !empty($value)) {
                $fieldDetails .= "`$key`=:$key,";
            }
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $query = "
                UPDATE tbl_vehicles
                SET
                    ".$fieldDetails."
                WHERE
                    id = :id
            ";
        try {
            // Execute the query to create the user
            $stmt = $db->prepare($query);
            $stmt->bindValue(":id", $vehicle['id'], PDO::PARAM_INT);

            foreach ($_POST as $key => $value) {
                if(!is_null($value) && !empty($value)) {
                    if (is_int($value)) {
                        $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
                    }
                }
            }
            $result = $stmt->execute();
        } catch (PDOException $ex) {
            // Note: On a production website, you should not output $ex->getMessage().
            // It may provide an attacker with helpful information about your code.
            die("Failed to run THIS query: " . $ex->getMessage());
        }

        $vehicle = $_POST;
        $vehicle['id'] = $_GET['vehicle_id'];
        $success_message = "Vehicle successfully updated.";
    }
}

?>

    <!-- app main column -->
    <div class="app-col-main col-md-10">

        <h1>Vehicle Management</h1>

        <?php if (!empty($_POST) && isset($errorArray)) {
            foreach ($errorArray as $error_key => $error_reason){ ?>
                <p style="color:red; font-weight: bold" ><?php echo $error_reason; ?></p>
            <?php } ?>
        <?php } ?>

        <form class="corrCheck_form form-horizontal" role="form" method="post"
              action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?vehicle_id='.$vehicle['id']; ?>'>

            <fieldset class="create_user rep-section">
                <h2>Edit Vehicle: <?php echo $vehicle['reg']; ?></h2>

                <div class="section-questions">
                    <?php if (isset($success_message) && !empty($success_message)){?>
                        <div class="alert alert-success" role="alert">
                            <strong><?php echo $success_message; ?></strong>
                        </div>
                    <?php }?>

                    <div class="question_row cf form-group">
                        <label for="reg" class="col-sm-3 control-label">Vehicle Registration:</label>

                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control" name="reg" value="<?php if (!empty($errorArray)) { echo htmlspecialchars($_POST['reg']);}elseif(!empty($vehicle['reg'])){echo $vehicle['reg'];}?>" required/>
                        </div>
                    </div>

                    <?php if($user->user_role == "Manager"){ ?>
                        <div class="question_row cf form-group">
                            <label for="username" class="col-sm-3 control-label">Belongs To Company:</label>
                            <div class="col-sm-4">
                                <select name="company_id" id="company_id" class="form-control">
                                    <option value="" disabled selected> -- Select Company --</option>
                                    <?php foreach ($companies as $key => $company){ ?>
                                        <option value="<?php echo $company['company_ID']; ?>"
                                            <?php if ((!empty($errorArray)) && isset($_POST['company_id']) && ($company['company_ID'] == $_POST['company_id'])) {echo 'selected="selected"';}
                                                 elseif (!empty($vehicle['company_id']) && ($company['company_ID'] == $vehicle['company_id'])) {echo 'selected="selected"';} ?>
                                            >
                                            <?php echo $company['company_name'];?>
                                        </option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if($user->user_role == "Manager"){ ?>
                        <div class="question_row cf form-group">
                            <label for="user_id" class="col-sm-3 control-label">Belongs To User:</label>
                            <div class="col-sm-4">
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="" disabled selected> -- Select User --</option>
                                    <?php foreach ($users as $key => $user){ ?>
                                        <option value="<?php echo $user['user_id']; ?>"
                                            <?php if ((!empty($errorArray)) && isset($_POST['user_id']) && ($user['user_id'] == $_POST['user_id'])) {echo 'selected="selected"';}
                                                elseif (!empty($vehicle['user_id']) && ($user['user_id'] == $vehicle['user_id'])) {echo 'selected="selected"';} ?>
                                            >
                                            <?php echo $user['username']; ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>


                    <div class="question_row cf form-group">
                        <label for="type" class="col-sm-3 control-label">Vehicle Type:</label>
                        <div class="col-sm-4">
                            <select name="type" id="type" class="form-control">
                                <option value="lorry"<?php if ((!empty($errorArray)) && isset($_POST['type']) && ($_POST['type'] == "lorry")) {echo 'selected="selected"';}elseif(!empty($vehicle['type']) && $vehicle['type'] == 'lorry'){echo 'selected="selected"';} ?>>Lorry</option>
                                <option value="trailer" <?php if ((!empty($errorArray)) && isset($_POST['type']) && ($_POST['type'] == "trailer")) {echo 'selected="selected"';}elseif(!empty($vehicle['type']) && $vehicle['type'] == 'trailer'){echo 'selected="selected"';} ?>>Trailer</option>
                            </select>
                        </div>
                    </div>

                    <div class="question_row cf form-group">
                        <label for="make" class="col-sm-3 control-label">Make/Model:</label>

                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control" name="make" value="<?php if (!empty($errorArray)) { echo htmlspecialchars($_POST['make']);}elseif(!empty($vehicle['make'])){echo $vehicle['make'];}?>"/>
                        </div>
                    </div>

                    <div class="question_row cf form-group">
                        <label for="psv" class="col-sm-3 control-label">Date Of Next PSV:</label>

                        <div class="col-sm-4">
                            <input type="text" class="form-control date-input form-control datepicker" name="psv_date" data-date-format="dd-mm-yyyy" value="<?php if (!empty($errorArray)) { echo htmlspecialchars(date('d-m-Y', strtotime($_POST['psv_date'])));}elseif(!empty($vehicle['psv_date'])){echo date('d-m-Y', strtotime($vehicle['psv_date']));}?>"/>
                        </div>
                    </div>

                    <div class="question_row cf form-group">
                        <label for="service_interval" class="col-sm-3 control-label">Service Interval (Weeks):</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control" name="service_interval" value="<?php if (!empty($errorArray)) { echo htmlspecialchars($_POST['service_interval']);}elseif(!empty($vehicle['service_interval'])){echo $vehicle['service_interval'];}?>"/>
                        </div>
                    </div>

                    <input type="hidden" class="form-control form-control" name="user_start" id="user_start" value="0"/>
                    <div class="question_row cf form-group">
                        <label for="vehicle_permission" class="col-sm-3 control-label">Use Custom Inspection Start Date?:</label>
                        <div class="col-sm-4">
                            <input type="checkbox" class="form-control form-control" name="user_start" id="user_start" value="1" <?php if ((!empty($errorArray)) && isset($_POST['user_start']) && ($_POST['user_start'] == 1)) {echo 'checked="checked"';}elseif(!empty($vehicle['user_start']) && $vehicle['user_start'] == 1){echo 'checked="checked"';} ?>/>
                        </div>
                    </div>

                    <div class="question_row cf form-group">
                        <label for="start_time" class="col-sm-3 control-label">Custom Start Time:</label>

                        <div class="col-sm-4">
                            <input type="text" class="form-control date-input form-control datepicker" name="start_time" data-date-format="dd-mm-yyyy" value="<?php if (!empty($errorArray)) { echo htmlspecialchars(date('d-m-Y', strtotime($_POST['start_time'])));}elseif(!empty($vehicle['start_time'])){echo date('d-m-Y', strtotime($vehicle['start_time']));}?>"/>
                        </div>
                    </div>

                    <input type="hidden" class="form-control form-control" name="is_active" id="is_active" value="2"/>
                    <div class="question_row cf form-group">
                        <label for="is_active" class="col-sm-3 control-label">Is Active?</label>
                        <div class="col-sm-4">
                            <input type="checkbox" class="form-control form-control" name="is_active" id="is_active" value="1" <?php if ((!empty($errorArray)) && isset($_POST['is_active']) && ($_POST['is_active'] == 1)) {echo 'checked="checked"';}elseif(!empty($vehicle['is_active']) && $vehicle['is_active'] == 1){echo 'checked="checked"';} ?>/>
                        </div>
                    </div>

            </fieldset>
            <button id="submit" type="submit" class="btn btn-primary"/>Update Vehicle</button>
        </form>
    </div><!-- app-col-main -->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>