<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php
    // If we make it this far we must be a logged in user but ensure customer role does not proceed
    $user->check_vehicle_premission();

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
            $_POST['company_id'] = $user->company_id;
        }
        if(!isset($_POST['user_id']) || empty($_POST['user_id'])){
            $_POST['user_id'] = $user->user_id;
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
        ";
        $query_params = array(
            ':reg' => $_POST['reg']
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

        // If no errors then create new vehicle
        if(!isset($error_check)){

            ksort($_POST);
            if(isset($_POST['psv_date']) && !empty($_POST['psv_date'])){
                $_POST['psv_date'] = date('Y-m-d', strtotime($_POST['psv_date']));
            }

            if(isset($_POST['start_time']) && !empty($_POST['start_time'])){
                $_POST['start_time'] = date('Y-m-d', strtotime($_POST['start_time']));
            }

            $data = array();
            foreach ($_POST as $key => $value) {
                if (!is_null($value) && !empty($value)) {
                    $data[$key] = $value;
                }
            }

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

            $success_message = "New Vehicle successfully created.";
        }
    }

    $cameraScript = true;

?>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Take a good clear picture of vehicle license plate.</h4>
                </div>
                <div class="modal-body">
                    <div id="step1">
                        <figure class="not-ready">
                            <video autoplay></video>
                        </figure>

                        <button class="btn btn-lg btn-success" disabled id="takePicture">Take a picture</button>
                    </div>

                    <div id="step2">
                        <h1><i class="glyphicon glyphicon-pencil"></i></h1>

                        <p class="lead">
                            Crop the picture and adjust it so that text is clearly visible.
                            <i class="glyphicon glyphicon-question-sign help" data-placement="bottom"
                               data-content="<img src='img/step2.png' />" data-html="true"></i>
                        </p>

                        <figure>
                            <canvas style="display:none"></canvas>
                            <img src=""/>
                        </figure>

                        <p>Brightness: <input type="range" min="0" max="100" id="brightness" value="20"></p>

                        <p>Contrast: <input type="range" min="0" max="100" id="contrast" value="90"></p>

                        <button class="btn btn-lg btn-success" id="adjust" disabled>Done</button>
                    </div>

                    <div id="step3">
                        <h1><i class="glyphicon glyphicon-text-height"></i></h1>

                        <p class="lead">You'll find the recognized text below.</p>

                        <figure>
                            <canvas></canvas>
                        </figure>

                        <blockquote>
                            <p id="result"></p>
                            <footer></footer>
                        </blockquote>

                        <button class="btn btn-lg btn-default" id="go-back">Go back</button>
                        <button class="btn btn-lg btn-default" id="start-over">Start over</button>
                    </div>
                </div>
                <div class="modal-footer">
<!--                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
<!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
                </div>
            </div>
        </div>
    </div>
    <!-- END of Modal -->


<!-- app main column -->
<div class="app-col-main col-md-10">

    <h1>Vehicle Management</h1>

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

                <div class="question_row cf form-group">
                    <label for="reg" class="col-sm-3 control-label">Vehicle Registration:</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control" name="reg" id = "input-reg" value="<?php if (!empty($errorArray)) { echo htmlspecialchars($_POST['reg']);}?>" required/>
                    </div>
<!--                    <div class = "col-sm-1">-->
<!--                        <i class="fa fa-camera" aria-hidden="true" id = "take-picture" data-toggle="modal" data-target="#myModal"></i>-->
<!--                    </div>-->
                </div>

                <?php if($user->user_role == "Manager"){ ?>
                    <div class="question_row cf form-group">
                        <label for="username" class="col-sm-3 control-label">Belongs To Company:</label>
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
                <?php } ?>

                <?php if($user->user_role == "Manager"){ ?>
                    <div class="question_row cf form-group">
                        <label for="user_id" class="col-sm-3 control-label">Belongs To User:</label>
                        <div class="col-sm-4">
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="" disabled selected> -- Select User --</option>
                                <?php foreach ($users as $key => $user){ ?>
                                    <option value="<?php echo $user['user_id']; ?>"
                                        <?php if ((!empty($errorArray)) && isset($_POST['user_id']) && ($user['user_id'] == $_POST['user_id'])) {echo 'selected="selected"';} ?>
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
                            <option value="lorry"<?php if ((!empty($errorArray)) && isset($_POST['type']) && ($_POST['type'] == "lorry")) {echo 'selected="selected"';} ?>>Lorry</option>
                            <option value="trailer" <?php if ((!empty($errorArray)) && isset($_POST['type']) && ($_POST['type'] == "trailer")) {echo 'selected="selected"';} ?>>Trailer</option>
                        </select>
                    </div>
                </div>

                <div class="question_row cf form-group">
                    <label for="make" class="col-sm-3 control-label">Make/Model:</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control" name="make" value="<?php if (!empty($errorArray)) { echo htmlspecialchars($_POST['make']);}?>"/>
                    </div>
                </div>

                <div class="question_row cf form-group">
                    <label for="psv" class="col-sm-3 control-label">Date Of Next PSV:</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control date-input form-control datepicker" name="psv_date" data-date-format="dd-mm-yyyy" value="<?php if (!empty($errorArray)) { echo htmlspecialchars(date('d-m-Y', strtotime($_POST['psv_date'])));}?>"/>
                    </div>
                </div>

                <div class="question_row cf form-group">
                    <label for="service_interval" class="col-sm-3 control-label">Service Interval (Weeks):</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control" name="service_interval" value="<?php if (!empty($errorArray)) { echo htmlspecialchars($_POST['service_interval']);}?>"/>
                    </div>
                </div>

                <input type="hidden" class="form-control form-control" name="user_start" id="user_start" value="0"/>
                <div class="question_row cf form-group">
                    <label for="vehicle_permission" class="col-sm-3 control-label">Use Custom Inspection Start Date?:</label>
                    <div class="col-sm-4">
                        <input type="checkbox" class="form-control form-control" name="user_start" id="user_start" value="1" <?php if ((!empty($errorArray)) && isset($_POST['user_start']) && ($_POST['user_start'] == 1)) {echo 'checked="checked"';} ?>/>
                    </div>
                </div>

                <div class="question_row cf form-group">
                    <label for="start_time" class="col-sm-3 control-label">Custom Start Time:</label>

                    <div class="col-sm-4">
                        <input type="text" class="form-control date-input form-control datepicker" name="start_time" data-date-format="dd-mm-yyyy" value="<?php if (!empty($errorArray)) { echo htmlspecialchars(date('d-m-Y', strtotime($_POST['start_time'])));}?>"/>
                    </div>
                </div>

        </fieldset>
        <button id="submit" type="submit" class="btn btn-primary"/>Create Vehicle</button>
    </form>
</div><!-- app-col-main -->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>