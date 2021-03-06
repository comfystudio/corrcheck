<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>

<?php
// If no $_GET vars are passed
if (empty($_GET)) {
    // redirect
    header("Location: vehicle-management");
    die("Redirecting to vehicle-management");  // critical!
}


// Get ID of vehicle
$vehicle_id = $_GET ["vehicle_id"];

//Bounce user back if they don't have permission to view
$user->check_vehicle_premission();

// Get main vehicle details
// Do DB Query
$query = "
            SELECT
                t1.*, t2.company_name, t3.username, GROUP_CONCAT(DISTINCT t4.date separator ', ') as late_dates
            FROM tbl_vehicles t1
            LEFT JOIN tbl_companies t2 ON t1.company_id = t2.company_ID
            LEFT JOIN tbl_users t3 ON t1.user_id = t3.user_id
            LEFT JOIN tbl_late_vehicles t4 ON t1.id = t4.vehicle_id
            WHERE t1.id = ".$vehicle_id."
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
$vehicle = $stmt->fetch();

// We need to get surveys for the vehicle
$where = "";
if($user->user_role != "Manager"){
    $where = " AND t1.company_ID = ".$user->company_id."";
}
$query = "
            SELECT
                t1.*, t2.username, t3.status_name
            FROM tbl_surveys t1
                LEFT JOIN tbl_users t2 ON t1.completed_by_user_ID = t2.user_id
                LEFT JOIN tbl_survey_statuses t3 ON t1.status_id = t3.status_id
            WHERE t1.vehicle_reg = '".$vehicle['reg']."' ".$where."
            ORDER BY t1.survey_date DESC
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
$surveys = $stmt->fetchAll();

//We need to work out if oil change / filter change and tachograph has been changed....Have to loop through cause of mad Database
foreach($surveys as $key => $survey){
    $query = "
            SELECT
                t1.question_response
            FROM tbl_survey_responses t1
            WHERE t1.survey_ID = '".$survey['survey_ID']."' AND t1.question_ID = 'veh_smallservice_140'
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }

    $oil = $stmt->fetch();
    $surveys[$key]['oil'] = $oil;

    $query = "
            SELECT
                t1.question_response
            FROM tbl_survey_responses t1
            WHERE t1.survey_ID = '".$survey['survey_ID']."' AND t1.question_ID = 'veh_smallservice_141'
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }

    $filter = $stmt->fetch();
    $surveys[$key]['filter'] = $filter;

    $query = "
            SELECT
                GROUP_CONCAT(t1.question_response) as response
            FROM tbl_survey_responses t1
            WHERE t1.survey_ID = '".$survey['survey_ID']."' AND (t1.question_ID = 'veh_tacho_58' OR t1.question_ID = 'veh_tacho_59' OR t1.question_ID = 'veh_tacho_60')
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }

    $tacho = $stmt->fetch();
    if(isset($tacho['response']) && !empty($tacho['response'])){
        $tacho['response'] = explode(',',$tacho['response']);
        foreach($tacho['response'] as $tac){
            if($tac == 'significant_defect' || $tac == 'slight_defect'){
                $surveys[$key]['tacho'] = true;
                break;
            }
        }
    }else{
        $surveys[$key]['tacho'] = false;
    }
}
?>

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>




<!-- app main column -->
<div class="app-col-main grid_12">

    <div class="section-header">
        <div class="btn-ctrl">
        <?php if($user->check_vehicle_premission()){?>
            <a href="<?php echo BASE_URL; ?>edit_vehicle.php?vehicle_id=<?php echo $vehicle["id"] ?>" class="btn btn-primary">Edit This Vehicle >></a>
        <?php } ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Vehicle Details</h3>
        </div>

        <div class="panel-body">
            <table class="survey-results">
                <tr>
                    <td>Reg</strong></td>
                    <td> <?php echo $vehicle["reg"]; ?></td>
                </tr>

                <tr>
                    <td>Company Name</strong></td>
                    <td> <?php echo $vehicle["company_name"] ?></td>
                </tr>

                <tr>
                    <td>User</strong></td>
                    <td> <?php echo $vehicle["username"] ?></td>
                </tr>

                <tr>
                    <td>Type</strong></td>
                    <td> <?php echo $vehicle["type"] ?></td>
                </tr>

                <tr>
                    <td>Make</strong></td>
                    <td> <?php echo $vehicle["make"] ?></td>
                </tr>

                <tr>
                    <td>PSV Date</strong></td>
                    <td> <?php echo date('j F, Y', strtotime($vehicle['psv_date'])) ?></td>
                </tr>

                <tr>
                    <td>Service Interval (In Weeks)</strong></td>
                    <td> <?php echo $vehicle["service_interval"]; ?></td>
                </tr>

                <?php if($vehicle['user_start'] == 1){ ?>
                    <tr>
                        <td>Custom Interval Date</strong></td>
                        <td> <?php echo date('j F, Y', strtotime($vehicle['start_time'])) ?></td>
                    </tr>
                <?php }?>

                <tr>
                    <td>Created </strong></td>
                    <td> <?php echo date('j F, Y', strtotime($vehicle['created']))  ?></td>
                </tr>

                <tr>
                    <td>Active?</strong></td>
                    <td>
                        <?php
                        if($vehicle['is_active'] == 1){
                            echo 'Yes';
                        }else{
                            echo "No";
                        }
                        ?>
                    </td>
                </tr>

            </table>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Inspections</h3>
        </div>
        <div class="panel-body">
            <table class="survey-results lube-results">
                <thead>
                    <tr>
                        <td class="hdr-insp-lube">Inspection No.</td>
                        <td class="hdr-cond-lube">Date</td>
                        <td class="hdr-dets-lube">Inspection By</td>
                        <td class="hdr-dets-lube">Oil Changed?</td>
                        <td class="hdr-dets-lube">Filter Changed?</td>
                        <td class="hdr-dets-lube">Tachograph Calibrated?</td>
                        <td class="hdr-dets-lube">Status</td>
                        <td class="text-center"><i class="fa fa-flash"></i></td>
                    </tr>
                </thead>

                <?php foreach($surveys as $key => $survey){ ?>
                    <tr>
                        <td><?php echo $survey['survey_ID'];?></td>
                        <td><?php echo date('j F, Y', strtotime($survey['survey_date']));?></td>
                        <td><?php echo $survey['username'];?></td>

                        <td class="text-center">
                            <?php
                                if(isset($survey['oil']) && ($survey['oil']['question_response'] == 'significant_defect' || $survey['oil']['question_response'] == 'slight_defect')){
                                    echo '<a class="btn btn-effect-ripple btn-sm btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>';
                                }else{
                                    echo '<a class="btn btn-effect-ripple btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';
                                }
                            ?>
                        </td>

                        <td class="text-center">
                            <?php
                            if(isset($survey['filter']) && ($survey['filter']['question_response'] == 'significant_defect' || $survey['filter']['question_response'] == 'slight_defect')){
                                echo '<a class="btn btn-effect-ripple btn-sm btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>';
                            }else{
                                echo '<a class="btn btn-effect-ripple btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';
                            }
                            ?>
                        </td>

                        <td class="text-center">
                            <?php
                            if(isset($survey['tacho']) && !empty($survey['tacho'])){
                                echo '<a class="btn btn-effect-ripple btn-sm btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>';
                            }else{
                                echo '<a class="btn btn-effect-ripple btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';
                            }
                            ?>
                        </td>

                        <td><?php echo $survey['status_name'];?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $survey["survey_ID"] ?>"
                               class="btn btn-primary">view
                            </a>
                        </td>
                    </tr>
                <?php }?>
            </table>
        </div>
    </div>

    <?php if(isset($vehicle['late_dates']) && !empty($vehicle['late_dates'])){?>
        <?php $late_dates = explode(',', $vehicle['late_dates']);?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Missed Inspections</h3>
            </div>
            <div class="panel-body">
                <table class="survey-results lube-results">
                    <thead>
                    <tr>
                        <td colspan="2" class="hdr-cond-lube text-center">Date</td>
                    </tr>
                    </thead>

                    <?php foreach($late_dates as $late_date){ ?>
                        <tr>
                            <td class = "text-center"><a class = "btn btn-effect-ripple btn-sm btn-danger"><i class="fa fa-exclamation" aria-hidden="true"></i></a> <?php echo date('j F, Y', strtotime($late_date));?></td>
                        </tr>
                    <?php }?>
                </table>
            </div>
        </div>
    <?php }?>



<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>