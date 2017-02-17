<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php
    //check we should be here.
    if(!$user->check_is_manager()){
        header("Location: vehicle-management.php");
        die("Redirecting to vehicle-management.php");
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
                    t1.*, t2.company_name
                FROM tbl_vehicles t1
                  LEFT JOIN tbl_companies t2 ON t1.company_id = t2.company_ID
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
                WHERE t1.company_ID <> ".$vehicle['company_id']."
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
        if(isset($_POST['cancel']) && !empty($_POST['cancel'])){
            header("Location: vehicle-management.php");
            die("Redirecting to vehicle-management.php");
        }

        // Validation
        foreach($_POST as $key => $data){
            $_POST[$key] = format_string($data);
        }

        // company_id
        if (empty($_POST['company_id'])) {
            $errorArray['company_id'] = "Please select a company";
            $error_check = true;
        }

        //if no errors then proceed with creating clone of vehicle.
        if(!isset($error_check)){
            $new_reg = $vehicle['reg'].'_'.$_POST['company_id'];

            $data = array();
            foreach ($vehicle as $key => $value) {
                if (!is_null($value) && !empty($value)) {
                    if($key == 'reg'){
                        $data[$key] = $new_reg;
                    }elseif($key == 'company_id'){
                        $data[$key] = $_POST['company_id'];
                    }else{
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
                    `".$fieldNames."`
                ) VALUES (
                    ".$fieldValues."
                )
            ";

            try {
                // Execute the query to create the user
                $stmt = $db->prepare($query);
                foreach ($data as $key => $value) {
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

            $_SESSION['flash_message'] = "Vehicle transferred successfully.";
            header("Location: vehicle-management.php");
            die("Redirecting to vehicle-management.php");
        }

    }
?>

<!-- app main column -->
<div class="app-col-main col-md-10">

    <h1>Transfer Vehicle - <?php echo $vehicle['reg']?></h1>
    <h3>Current Company - <?php echo $vehicle['company_name']?></h3>

    <?php if (!empty($_POST) && isset($errorArray)) {
        foreach ($errorArray as $error_key => $error_reason){ ?>
            <p style="color:red; font-weight: bold" ><?php echo $error_reason; ?></p>
        <?php } ?>
    <?php } ?>

    <form class="corrCheck_form form-horizontal" role="form" method="post"
          action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?vehicle_id='.$vehicle['id']; ?>'>

        <fieldset class="create_user rep-section">
            <div class="section-questions">
                <?php if (isset($success_message) && !empty($success_message)){?>
                    <div class="alert alert-success" role="alert">
                        <strong><?php echo $success_message; ?></strong>
                    </div>
                <?php }?>

                <div class="question_row cf form-group">
                    <label for="username" class="col-sm-3 control-label">Transfer To Company:</label>
                    <div class="col-sm-4">
                        <select name="company_id" id="company_id" class="form-control">
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