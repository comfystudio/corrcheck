<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php
    //check we should be here.
    if(!$user->check_is_manager()){
        header("Location: vehicle-management.php");
        die("Redirecting to vehicle-management.php");
    }

    // Check if we got a Get vehicle_id var
//    if(!isset($_GET) || empty($_GET['vehicle_id'])){
//        header("Location: vehicle-management.php");
//        die("Redirecting to vehicle-management.php");
//    }

//    $_GET['vehicle_id'] = format_string($_GET['vehicle_id']);

    // Need to get vehicles for Vehicle reg field
    $query = "
                SELECT
                    t1.id, t1.reg, t1.make, t1.type
                FROM tbl_vehicles t1
                WHERE t1.is_active = 1
                ORDER BY t1.reg ASC
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
    $vehicles = $stmt->fetchAll();

    // We need to get companies
    $query = "
                    SELECT t1.company_ID, t1.company_name
                      FROM tbl_companies t1
                    WHERE t1.is_active = 1
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


    // If form is submitted process it
    if(!empty($_POST)){
        // If user selects cancel bounce them back to index
        if (isset($_POST['cancel']) && !empty($_POST['cancel'])) {
            header("Location: vehicle-management.php");
            die("Redirecting to vehicle-management.php");
        }

        // Validation
        foreach($_POST as $key => $data){
            $_POST[$key] = format_string($data);
        }
        $errorArray = array();
        if(!isset($_POST['company_id']) || empty($_POST['company_id'])){
            $errorArray[] = 'Please select a company';
        }

        if(!isset($_POST['vehicle_id']) || empty($_POST['vehicle_id'])){
            $errorArray[] = 'Please select a vehicle';
        }

        if(empty($errorArray)) {
            // Assuming we now have vehicle_id check if there's a related vehicle
            $query = "
                    SELECT
                        t1.*, t2.company_name
                    FROM tbl_vehicles t1
                      LEFT JOIN tbl_companies t2 ON t1.company_id = t2.company_ID
                    WHERE
                        t1.id = :id
                    LIMIT 1
                ";
            $query_params = array(
                ':id' => $_POST['vehicle_id']
            );
            try {
                $stmt = $db->prepare($query);
                $result = $stmt->execute($query_params);
            } catch (PDOException $ex) {
                die("Failed to run query: " . $ex->getMessage());
            }
            $vehicle = $stmt->fetch();

            // if no related vehicle is found bounce user back to index page
            if (!isset($vehicle) || empty($vehicle)) {
                header("Location: vehicle-management.php");
                die("Redirecting to vehicle-management.php");
            }

            //We need to check vehicle selected with the company selected so the user doesn't select the vehicles current company
            if($vehicle['company_id'] == $_POST['company_id']){
                $errorArray[] = 'You have selected the same company that already belongs to this vehicle';
            }

            //if no errors then proceed with creating clone of vehicle.
            if (empty($errorArray)) {
                //We need to update the previous vehicle with a new reg
                $new_reg = $vehicle['reg'] . '_' . $_POST['company_id'];

                $query = "
                    UPDATE tbl_vehicles
                    SET
                        `reg` = '".$new_reg."'
                    WHERE
                        id = :id
                ";
                try {
                    // Execute the query to create the user
                    $stmt = $db->prepare($query);
                    $stmt->bindValue(":id", $vehicle['id'], PDO::PARAM_INT);
                    $result = $stmt->execute();
                } catch (PDOException $ex) {
                    // Note: On a production website, you should not output $ex->getMessage().
                    // It may provide an attacker with helpful information about your code.
                    die("Failed to run THIS query: " . $ex->getMessage());
                }

                $data = array();
                foreach ($vehicle as $key => $value) {
                    if (!is_null($value) && !empty($value)) {
                        if ($key == 'company_id') {
                            $data[$key] = $_POST['company_id'];
                        } else {
                            $data[$key] = $value;
                        }
                    }
                }
                unset($data['id']);
                unset($data['created']);
                unset($data['is_active']);
                unset($data['company_name']);

                $fieldNames = implode('`, `', array_keys($data));
                $fieldValues = ':' . implode(', :', array_keys($data));

                $query = "
                    INSERT INTO tbl_vehicles (
                        `" . $fieldNames . "`
                    ) VALUES (
                        " . $fieldValues . "
                    )
                ";

                try {
                    // Execute the query to create the user
                    $stmt = $db->prepare($query);
                    foreach ($data as $key => $value) {
                        if (!is_null($value) && !empty($value)) {
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

                // I believe we now need to change all previous inspections vehicle reg to the new reg.
                $query = "
                    SELECT
                        GROUP_CONCAT(t1.survey_ID) as id
                    FROM tbl_surveys t1
                    WHERE
                        t1.vehicle_reg = '".$vehicle['reg']."'
                ";
                try {
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                } catch (PDOException $ex) {
                    die("Failed to run query: " . $ex->getMessage());
                }
                $surveys = $stmt->fetch();

                if(isset($surveys) && !empty($surveys)){
                    $query = "
                        UPDATE tbl_surveys
                        SET
                            `vehicle_reg` = '".$new_reg."'
                        WHERE
                            survey_ID IN (".$surveys['id'].")
                    ";
                    try {
                        // Execute the query to create the user
                        $stmt = $db->prepare($query);
                        $result = $stmt->execute();
                    } catch (PDOException $ex) {
                        // Note: On a production website, you should not output $ex->getMessage().
                        // It may provide an attacker with helpful information about your code.
                        die("Failed to run THIS query: " . $ex->getMessage());
                    }
                }

                $_SESSION['flash_message'] = "Vehicle transferred successfully.";
                header("Location: vehicle-management.php");
                die("Redirecting to vehicle-management.php");
            }
        }
    }
?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">';?>


<!-- app main column -->
<div class="app-col-main col-md-10">

    <h1>Transfer Vehicle</h1>

    <?php if (!empty($_POST) && isset($errorArray)) {
        foreach ($errorArray as $error_key => $error_reason){ ?>
            <p style="color:red; font-weight: bold" ><?php echo $error_reason; ?></p>
        <?php } ?>
    <?php } ?>

    <form class="corrCheck_form form-horizontal" role="form" method="post"
          action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>'>

        <fieldset class="create_user rep-section">
            <div class="section-questions">
                <?php if (isset($success_message) && !empty($success_message)){?>
                    <div class="alert alert-success" role="alert">
                        <strong><?php echo $success_message; ?></strong>
                    </div>
                <?php }?>

                <div class = "question_row cf form-group">
                    <label for="vehicle_id" class="col-sm-3 control-label">Vehicle Reg:</label>
                    <div class="col-sm-4">
                        <select name="vehicle_id" id="vehicle_id" class="selectpicker" data-live-search="true" data-width="180px">
                            <?php foreach($vehicles as $key => $vehicle){?>
                                <option value="<?php echo $vehicle['id'] ?>"
                                    <?php if ((!empty($errorArray)) && isset($_POST['vehicle_id']) && ($vehicle['id'] == $_POST['vehicle_id'])) {echo 'selected="selected"';}?>
                                    data-make="<?php echo $vehicle['make']?>" data-type="<?php echo $vehicle['type']?>">
                                    <?php echo $vehicle['reg']?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="question_row cf form-group">
                    <label for="username" class="col-sm-3 control-label">Transfer To Company:</label>
                    <div class="col-sm-4">
                        <select name="company_id" id="company_id" class="selectpicker" data-live-search="true" data-width="180px">
                            <option value="" disabled selected> -- Select Company --</option>
                            <?php foreach ($companies as $key => $company){ ?>
                                <option value="<?php echo $company['company_ID']; ?>"
                                    <?php if ((!empty($errorArray)) && isset($_POST['company_id']) && ($company['company_ID'] == $_POST['company_id'])) {echo 'selected="selected"';} ?>
                                    >
                                    <?php echo $company['company_name'];?>
                                </option>
                            <?php }?>
                        </select>
                    </div>
                </div>
        </fieldset>

        <button id="submit" type="submit" class="btn btn-primary"/>Transfer Vehicle</button>
        <button id="cancel" type="submit" class="btn btn-danger" name = "cancel" value = "cancel"/>Cancel</button>
    </form>
</div><!-- app-col-main -->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>