<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
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


// If form is submitted process it
if(!empty($_POST)){

    // If user selects cancel bounce them back to index
    if(isset($_POST['cancel']) && !empty($_POST['cancel'])){
        header("Location: vehicle-management.php");
        die("Redirecting to vehicle-management.php");
    }

    // If user selects delete then deal with this
    if(isset($_POST['delete']) && !empty($_POST['delete'])) {
        if (!isset($error_check)) {
            $query = "
                DELETE FROM tbl_vehicles
                WHERE
                    id = :id
            ";
            $query_params = array(
                ':id' => $vehicle['id'],
            );

            try {
                // Execute the query to create the user
                $stmt = $db->prepare($query);
                $result = $stmt->execute($query_params);
            } catch (PDOException $ex) {
                // Note: On a production website, you should not output $ex->getMessage().
                // It may provide an attacker with helpful information about your code.
                die("Failed to run THIS query: " . $ex->getMessage());
            }
            header("Location: vehicle-management.php");
            die("Redirecting to vehicle-management.php");
        }
    }
}

?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>


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
                <h2>Delete Vehicle: <?php echo $vehicle['reg']; ?></h2>

                <div class="section-questions">
                    <?php if (isset($success_message) && !empty($success_message)){?>
                        <div class="alert alert-success" role="alert">
                            <strong><?php echo $success_message; ?></strong>
                        </div>
                    <?php }?>

                    <div class="question_row cf form-group">
                        <p class="col-sm-12">Are you sure you wish to delete Vehicle: <?php echo $vehicle['reg'];?>?</p>
                    </div>
            </fieldset>
            <button id="submit" type="submit" class="btn btn-danger" name = "delete" value = "delete"/>Delete Vehicle</button>
            <button id="cancel" type="submit" class="btn btn-primary" name = "cancel" value = "cancel"/>Cancel</button>
        </form>
    </div><!-- app-col-main -->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>