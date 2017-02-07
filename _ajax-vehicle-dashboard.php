<?php
include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php");

function datediffInWeeks($date1, $date2)
{
    if($date1 > $date2) return datediffInWeeks($date2, $date1);
    $first = DateTime::createFromFormat('m/d/Y', $date1);
    $second = DateTime::createFromFormat('m/d/Y', $date2);
    return ceil($first->diff($second)->days/7);
}

$paramArray = array();
$where = "";

if(isset($_POST['direction']) && !empty($_POST['direction'])){
    if($_POST['direction'] == 'back'){
        if(isset($_POST['start_date']) && !empty($_POST['start_date'])){
            $_POST['start_date'] = strtotime ( '-4 weeks' , strtotime ( $_POST['start_date'] ) ) ;
            $_POST['start_date'] = date("d-m-Y", $_POST['start_date']);
        }else{
            $_POST['start_date'] = date("d-m-Y", strtotime("-8 weeks"));
            $_POST['end_date'] = date("d-m-Y", strtotime("+8 weeks"));
        }
    }elseif($_POST['direction'] == 'forward'){
        if(isset($_POST['end_date']) && !empty($_POST['end_date'])){
            $_POST['end_date'] = strtotime ( '+4 weeks' , strtotime ( $_POST['end_date'] ) ) ;
            $_POST['end_date'] = date("d-m-Y", $_POST['end_date']);
        }else{
            $_POST['start_date'] = date("d-m-Y");
            $_POST['end_date'] = date("d-m-Y", strtotime("+16 weeks"));
        }
    }
}

//Checking if we have $_GET['company-filter'] we do same as with post basically
if(isset($_GET) && !empty($_GET['company-filter']) && $_GET['company-filter'] != 'search-all'){
    $_GET['company-filter'] = format_string($_GET['company-filter']);
    $where .= " AND t1.company_id = :company";
    $paramArray[':company'] = $_GET['company-filter'];
}

if($user->user_role == "Customer"){
    $where = " AND t1.company_id = ".$user->company_id." OR t1.user_id = ".$user->user_id;
}

//Adding filter if user has used search filter
if(isset($_POST) && !empty($_POST['company-filter']) && $_POST['company-filter'] != 'search-all'){
    $_POST['company-filter'] = format_string($_POST['company-filter']);
    $where .= " AND t1.company_id = :company";
    $paramArray[':company'] = $_POST['company-filter'];
}

//Adding filter if user has used search filter
if(isset($_POST) && !empty($_POST['reg-search-input'])){
    $_POST['reg-search-input'] = format_string($_POST['reg-search-input']);
    $where .= " AND t1.reg LIKE :reg";
    $paramArray[':reg'] = '%'.$_POST['reg-search-input'].'%';
}

// If the user has selected start and end date
if(isset($_POST) && !empty($_POST['start_date']) && !empty($_POST['end_date'])){
    $start_date = date("m/d/Y", strtotime($_POST['start_date']));
    $end_date = date("m/d/Y", strtotime($_POST['end_date']));
    $number_weeks = datediffInWeeks($start_date, $end_date);
}else{
    $start_date = date("m/d/Y", strtotime("-1 months"));
    $end_date = date("m/d/Y", strtotime("+3 months"));
    $number_weeks = datediffInWeeks($start_date, $end_date);
}

// ADD filter if last-filter has been selected use that value else default to 2 weeks
if(isset($_POST) && !empty($_POST['last-filter'])){
    if($_POST['last-filter'] != 'all'){
        $where .= " AND t3.survey_date > DATE_SUB(CURDATE(), INTERVAL :last WEEK)";
        $paramArray[':last'] = intval($_POST['last-filter']);
    }
}else{
    $where .= " AND t3.survey_date > DATE_SUB(CURDATE(), INTERVAL 2 WEEK)";
}

// Checking for psv-filter and then only showing vehicles that have had PSV checks done based on all the other criteria
if(isset($_POST) && !empty($_POST['psv-filter'])){
    if($_POST['psv-filter'] == 1) {
        $where .= " AND t3.psv = 1";
    }
}

//We need to work out if current date falls within $start_date and $end_date then work out which week number it is
$today = date("m/d/Y");
if(strtotime($today) >= strtotime($start_date) && strtotime($today) <= strtotime($end_date)){
    $today = datediffInWeeks($start_date, $today);
}else{
    $today = 0;
}

// Do DB Query
$query = "
            SELECT
                t1.*, t2.company_name, t2.service_interval as company_service_interval, GROUP_CONCAT(t3.survey_ID separator ', ') as survey_ids
            FROM tbl_vehicles t1
            LEFT JOIN tbl_companies t2 ON t1.company_id = t2.company_ID
            LEFT JOIN tbl_surveys t3 ON t1.reg = t3.vehicle_reg
            WHERE t1.is_active = 1
              ".$where."
            GROUP BY t1.id
            ORDER BY type DESC
        ";
try {
    // These two statements run the query against your database table.
    $stmt = $db->prepare($query);
    foreach ($paramArray as $key => $value){
        if(is_int($value)){
            $stmt->bindValue("$key", $value, PDO::PARAM_INT);
        }
        else{
            $stmt->bindValue("$key", $value, PDO::PARAM_STR);
        }
    }
    $stmt->execute();
} catch (PDOException $ex) {
    // Note: On a production website, you should not output $ex->getMessage().
    // It may provide an attacker with helpful information about your code.
    die("Failed to run query: " . $ex->getMessage());
}
// Finally, we can retrieve all of the found rows into an array using fetchAll
$rows = $stmt->fetchAll();

// We need to build our survey data and attach it to our rows
foreach($rows as $key => $row){
    if(isset($row['survey_ids']) && !empty($row['survey_ids'])) {
        $query = "
            SELECT
                t1.survey_ID, t1.scheduled, t1.psv, t1.survey_date, t2.question_ID, t2.question_response
            FROM tbl_surveys t1
              LEFT JOIN tbl_survey_responses t2 ON t2.survey_ID = t1.survey_ID
            WHERE t1.survey_ID IN (" . $row['survey_ids'] . ")
                AND (t1.survey_date BETWEEN '" . date('Y-m-d', strtotime($start_date)) . "' AND '" . date('Y-m-d', strtotime($end_date)) . "')
            ORDER BY t1.survey_date DESC
        ";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
        } catch (PDOException $ex) {
            die("Failed to run query: " . $ex->getMessage());
        }
        $surveys = $stmt->fetchAll();

        if(isset($surveys) && !empty($surveys)) {
            $rows[$key]['surveys'] = array();
            // We still need to dig deeper and get the related survey data we need.
            foreach ($surveys as $survey) {
                if(!array_key_exists($survey['survey_ID'], $rows[$key]['surveys'])) {
                    $rows[$key]['surveys'][$survey['survey_ID']]['scheduled'] = $survey['scheduled'];
                    $rows[$key]['surveys'][$survey['survey_ID']]['psv'] = $survey['psv'];
                    $rows[$key]['surveys'][$survey['survey_ID']]['date'] = date('m/d/Y', strtotime($survey['survey_date']));
                    $temp_date =  datediffInWeeks($start_date, date('m/d/Y', strtotime($survey['survey_date'])));
                    if($temp_date == 0){
                        $temp_date = 1;
                    }
                    $rows[$key]['surveys'][$survey['survey_ID']]['date_weeks'] = $temp_date;
                }

                //IF tachograph has taken place based on 6 Year Tachograph
                if ($survey['question_ID'] == 'veh_tacho_58' && ($survey['question_response'] == 'significant_defect' || $survey['question_response'] == 'slight_defect')) {
                    $rows[$key]['surveys'][$survey['survey_ID']]['tachograph'] = $survey['question_response'];
                    //IF tachograph has taken place based on 2 Year Tachograph
                } elseif ($survey['question_ID'] == 'veh_tacho_59' && ($survey['question_response'] == 'significant_defect' || $survey['question_response'] == 'slight_defect')) {
                    $rows[$key]['surveys'][$survey['survey_ID']]['tachograph'] = $survey['question_response'];
                    //IF tachograph has taken place based on Digital Tachograph
                } elseif ($survey['question_ID'] == 'veh_tacho_60' && ($survey['question_response'] == 'significant_defect' || $survey['question_response'] == 'slight_defect')) {
                    $rows[$key]['surveys'][$survey['survey_ID']]['tachograph'] = $survey['question_response'];
                    // IF OIL Change has happened
                } elseif ($survey['question_ID'] == 'veh_smallservice_140' && ($survey['question_response'] == 'significant_defect' || $survey['question_response'] == 'slight_defect')) {
                    $rows[$key]['surveys'][$survey['survey_ID']]['oil'] = $survey['question_response'];
                    // IF Engine Filter Has happened
                } elseif ($survey['question_ID'] == 'veh_smallservice_141' && ($survey['question_response'] == 'significant_defect' || $survey['question_response'] == 'slight_defect')) {
                    $rows[$key]['surveys'][$survey['survey_ID']]['engine'] = $survey['question_response'];
                }
            }
        }
    }

    // We need to construct PSV dates for each vehicle depending on date range.
    if(isset($row['psv_date']) && !empty($row['psv_date'])){
        $start_year = date('Y', strtotime($start_date));
        $psv_date = explode('-', $row['psv_date']);
        $psv_date[0] = $start_year;
        $psv_date = implode('-', $psv_date);
        $count = 0;
        while(strtotime($psv_date) <= strtotime($end_date)) {
            if(strtotime($psv_date) >= strtotime($start_date)) {
                $rows[$key]['psvs'][$count]['date'] = date('m/d/Y', strtotime($psv_date));
                $rows[$key]['psvs'][$count]['date_weeks'] = datediffInWeeks($start_date, date('m/d/Y', strtotime($psv_date)));
            }
            $start_year++;
            $psv_date = explode('-', $row['psv_date']);
            $psv_date[0] = $start_year;
            $psv_date = implode('-', $psv_date);
            $count++;
        }
    }

    // We now need to build a list of scheduled inspections which can be based on many factors.
    $time_interval = 10;
    if(isset($row['service_interval']) && !empty($row['service_interval'])){
        $time_interval = $row['service_interval'];
    }elseif(isset($row['company_service_interval']) && !empty($row['company_service_interval'])){
        $time_interval = $row['company_service_interval'];
    }
    //We now have time interval based on hierarchy of vehicle / Company / default 10
    if(isset($row['user_start']) && $row['user_start'] == 0){
        // If the vehicle is not using a custom start date we try to use most recent inspection
        if(isset($rows[$key]['surveys']) && !empty($rows[$key]['surveys'])) {
            $array_keys = array_keys($rows[$key]['surveys']);
            if (isset($array_keys) && !empty($array_keys)) {
                $origin_date = date('Y-m-d', strtotime($rows[$key]['surveys'][$array_keys[0]]['date']));
            }
        }else{
            // If we don't have recent inspection we shall use psv date...think this is the best fallback
            $origin_date = $row['psv_date'];
        }
        $count = 0;
        // This while loop is working down to start date
        while(strtotime($origin_date) > strtotime($start_date)) {
            $origin_date = date('Y-m-d', strtotime("-".$time_interval." Week", strtotime($origin_date)));
        }

        // This while loop is working up to end date
        while(strtotime($origin_date) <= strtotime($end_date)) {
            if(strtotime($origin_date) >= strtotime($start_date)) {
                $rows[$key]['schedules'][$count]['date'] = date('m/d/Y', strtotime($origin_date));
                $rows[$key]['schedules'][$count]['date_weeks'] = datediffInWeeks($start_date, date('m/d/Y', strtotime($origin_date)));
                if($rows[$key]['schedules'][$count]['date_weeks'] == 0){
                    $rows[$key]['schedules'][$count]['date_weeks'] = 1;
                }
            }
            $origin_date = date('Y-m-d', strtotime("+".$time_interval." Week", strtotime($origin_date)));
            $count++;
        }

    }else{
        // If the vehicle is using a custom start date.
        $count = 0;
        $origin_date = $row['start_time'];
        while(strtotime($origin_date) <= strtotime($end_date)) {
            if(strtotime($origin_date) >= strtotime($start_date)) {
                $rows[$key]['schedules'][$count]['date'] = date('m/d/Y', strtotime($origin_date));
                $rows[$key]['schedules'][$count]['date_weeks'] = datediffInWeeks($start_date, date('m/d/Y', strtotime($origin_date)));
                if($rows[$key]['schedules'][$count]['date_weeks'] == 0){
                    $rows[$key]['schedules'][$count]['date_weeks'] = 1;
                }
            }
            $origin_date = date('Y-m-d', strtotime("+".$time_interval." Week", strtotime($origin_date)));
            $count++;
        }
    }
}
//reverse array so we have lorry above trailers
$rows = array_reverse($rows);


$temp = "";
?>
<div class="dataTables_wrapper form-inline no-footer" id = "ajax-dashboard">
    <div class="row">
        <form class="form-wrap" method="post" action="" id = "vehicle-dashboard-form">
            <?php if ($user->user_role == "Manager"){?>
                <div class="col-lg-3 col-md-6 col-xs-6">
                    <div class="sf-company-filter search-group">
                        <label for="company-filter">
                            Select Company:
                        </label>
                        <select name="company-filter" id="company-filter" class="form-control">
                            <option value="search-all" <?php if(isset($_POST["company-filter"]) && $_POST["company-filter"]=="search-all") echo 'selected'; ?>>Search All</option>
                            <?php
                            // Get companies
                            $companies = get_company_names($db);
                            foreach($companies as $company_id => $company){
                                $company_name = $company["company_name"];
                                ?>
                                <option value="<?php echo $company_id; ?>"
                                    <?php
                                    if(isset($_POST["company-filter"])){
                                        if($_POST["company-filter"] == $company_id){
                                            echo "selected";
                                        }
                                    }
                                    ?>
                                    ><?php echo $company_name; ?></option>
                            <?php } ?>
                        </select>
                    </div><!-- sf-company-filter -->
                </div>

                <div class="col-lg-3 col-md-6 col-xs-6">
                    <div class="sf-search-box search-group">
                        <label for="reg-search-input">Registration Search: </label>
                        <input type="text" name="reg-search-input" id="reg-search-input" class="form-control" value="<?php if(isset($_POST['reg-search-input'])){echo $_POST['reg-search-input'];} ?>">
                    </div>
                </div>
            <?php } ?>

            <div class="col-lg-3 col-md-6 col-xs-6">
                <div class="input-group input-daterange" data-date-format="dd-mm-yyyy" data-date-view-mode="months" data-date-min-view-mode="months">
                    <input type="text" name="start_date" class="form-control datepicker" data-provide="datepicker" data-date-format="dd-mm-yyyy" placeholder="Start Date" value="<?php if(!empty($_POST['start_date'])){echo $_POST['start_date'];}?>">
                    <span class="input-group-addon"><i class="fa fa-chevron-right"></i></span>
                    <input type="text" name="end_date" class="form-control datepicker" data-provide="datepicker" data-date-format="dd-mm-yyyy" placeholder="End Date" value="<?php if(!empty($_POST['end_date'])){echo $_POST['end_date'];}?>">
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-xs-6">
                <span class="input-group-addon" id = "dashboard-back"><i class="fa fa-chevron-left"></i> Back</span>
                <span class="input-group-addon" id = "dashboard-forward">Forward <i class="fa fa-chevron-right"></i></span>
            </div>

            <div class="col-lg-3 col-md-6 col-xs-6">
                <div class="sf-company-filter search-group">
                    <label for="company-filter">
                        Show Vehicles Last Tested:
                    </label>
                    <select name="last-filter" id="last-filter" class="form-control">
                        <option value="all" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="26") echo 'selected'; ?>>Show All</option>
                        <option value="26" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="26") echo 'selected'; ?>>26 Weeks</option>
                        <option value="20" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="20") echo 'selected'; ?>>20 Weeks</option>
                        <option value="16" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="16") echo 'selected'; ?>>16 Weeks</option>
                        <option value="12" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="12") echo 'selected'; ?>>12 Weeks</option>
                        <option value="8" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="8") echo 'selected'; ?>>8 Weeks</option>
                        <option value="6" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="6") echo 'selected'; ?>>6 Weeks</option>
                        <option value="4" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="4") echo 'selected'; ?>>4 Weeks</option>
                        <option value="2" <?php if((isset($_POST["last-filter"]) && $_POST["last-filter"]=="2") || !isset($_POST["last-filter"])) echo 'selected'; ?>>2 Weeks</option>
                        <option value="1" <?php if(isset($_POST["last-filter"]) && $_POST["last-filter"]=="1") echo 'selected'; ?>>1 Weeks</option>
                    </select>
                </div><!-- sf-company-filter -->
            </div>

            <!-- FILTER FOR PSV -->
            <div class="col-lg-3 col-md-6 col-xs-6">
                <div class="sf-company-filter search-group">
                    <label for="psv-filter">Show Only PSVs?</label>
                    <input class="form-control" type="checkbox" name="psv-filter" value="1" <?php if(isset($_POST['psv-filter']) && $_POST['psv-filter'] == 1) { echo 'checked';}?>>
                </div>
            </div>
            <!-- END OF FILTER FOR PSV -->

            <div class="col-lg-1 col-lg-offset-2 col-md-1 col-xs-6">
                <button type="submit" class="btn btn-success">Search</button>
            </div>

            <div class="col-lg-1 col-md-1 col-xs-6">
                <a href = "<?php echo BASE_URL; ?>vehicle-dashboard.php" class="btn btn-primary">Reset</a>
            </div>

            <div class="pull-right"></div>
        </form>
    </div>
    <?php if(!empty($rows)) {?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-vcenter table-hover no-margin">
                <thead>
                <tr>
                    <th style = "width:20px;">Vehicle ID / Reg</th>
                    <!-- For loop to put weeks in header -->
                    <?php for($i = 1; $i <= $number_weeks; $i++){?>
                        <?php
                        if($i == $today) {
                            $class = "today";
                        }elseif(($i % 2) == 1){
                            $class = "odd";
                        }else{
                            $class = "";
                        }
                        ?>
                        <th class = "dashboard-table-row text-center"><?php echo str_replace('-', '<br/>', date('Y-M-d', strtotime($start_date."+".($i-1)." week")));?></th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach($rows as $data) {?>
                    <?php if($temp != $data['type']){?>
                        <tr>
                            <?php
                                if($data['type'] == 'lorry'){
                                    $string = 'Motor Vehicle';
                                }elseif($data['type'] == 'trailer'){
                                    $string = 'trailer';
                                }
                            ?>
                            <td colspan="<?php echo 2+$number_weeks?>" class = "table-divide"><strong><?php echo strtoupper($string);?></strong></td>
                        <tr>
                        <?php $temp = $data['type'];?>
                    <?php } ?>
                    <tr>
                        <td>
                            <a href="<?php echo BASE_URL; ?>vehicle-view.php?vehicle_id=<?php echo $data["id"] ?>" style = "color:#428bca;">
                                <?php echo $data['reg'];?>
                            </a>
                            <?php
                            if(isset($data['company_name']) && !empty($data['company_name'])){
                                echo '<br/>'.'<a href = "/vehicle-dashboard.php?company-filter='.$data['company_id'].'" style = "color:#428bca;">'.$data["company_name"].'</a>';
                            }
                            ?>
                        </td>
                        <!-- For loop to put weeks in TDs -->
                        <?php for($i = 1; $i <= $number_weeks; $i++){?>
                            <?php if($i == $today) {
                                $class = "today";
                            }elseif(($i % 2) == 1){
                                $class = "odd";
                            }else{
                                $class = "";
                            }?>
                            <td class = "<?php echo $class;?>">
                                <!-- START BLOCK OF SCHEDULED AND UNSCHEDULED INSPECTIONS -->
                                <?php if(isset($data['surveys']) && !empty($data['surveys'])){?>
                                    <?php foreach($data['surveys'] as $key2 => $survey){?>
                                        <?php if($survey['date_weeks'] == $i){?>
                                            <!-- If its a PSV survey-->
                                            <?php if($survey['psv'] == 1){?>
                                                <?php
                                                    $tachoIcon = '';
                                                    $oilIcon = '';
                                                    $engineIcon = '';
                                                    $title_text = 'PSV Inspection: '.$survey['date'];
                                                    if(isset($survey['tachograph']) && !empty($survey['tachograph'])){
                                                        $title_text .= '<br/>Tachograph Preformed';
                                                        $tachoIcon = '<div class ="tacho-icon"><span>T</span></div>';
                                                    }
                                                    if(isset($survey['oil']) && !empty($survey['oil'])) {
                                                        $title_text .= '<br/>Oil Changed';
                                                        $oilIcon = '<div class ="oil-icon"><span>O</span></div>';
                                                    }
                                                    if(isset($survey['engine']) && !empty($survey['engine'])) {
                                                        $title_text .= '<br/>Engine Filter Changed';
                                                        $engineIcon = '<div class ="engine-icon"><span>E</span></div>';
                                                    }
                                                ?>
                                                <a data-toggle="tooltip" title="<?php echo $title_text?>" class = "btn btn-effect-ripple btn-sm btn-danger" href = "<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $key2?>">
                                                    <span class="fa-stack fa-1x">
                                                        <i class="fa fa-wrench" aria-hidden="true"></i>
                                                        <?php echo $tachoIcon;?>
                                                        <?php echo $oilIcon;?>
                                                        <?php echo $engineIcon;?>
                                                    </span>
                                                </a>
                                            <!-- If its a scheduled survey-->
                                            <?php }elseif($survey['scheduled'] == 1){?>
                                                <?php
                                                    $tachoIcon = '';
                                                    $oilIcon = '';
                                                    $engineIcon = '';
                                                    $title_text = 'Scheduled Inspection: '.$survey['date'];
                                                    if(isset($survey['tachograph']) && !empty($survey['tachograph'])){
                                                        $title_text .= '<br/>Tachograph Preformed';
                                                        $tachoIcon = '<div class ="tacho-icon"><span>T</span></div>';
                                                    }
                                                    if(isset($survey['oil']) && !empty($survey['oil'])) {
                                                        $title_text .= '<br/>Oil Changed';
                                                        $oilIcon = '<div class ="oil-icon"><span>O</span></div>';
                                                    }
                                                    if(isset($survey['engine']) && !empty($survey['engine'])) {
                                                        $title_text .= '<br/>Engine Filter Changed';
                                                        $engineIcon = '<div class ="engine-icon"><span>E</span></div>';
                                                    }
                                                ?>
                                                <a data-toggle="tooltip" title="<?php echo $title_text?>" class = "btn btn-effect-ripple btn-sm btn-success" href = "<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $key2?>">
                                                    <span class="fa-stack fa-1x">
                                                        <i class="fa fa-wrench" aria-hidden="true"></i>
                                                        <?php echo $tachoIcon;?>
                                                        <?php echo $oilIcon;?>
                                                        <?php echo $engineIcon;?>
                                                    </span>
                                                </a>
                                            <?php }else{?>
                                                <?php
                                                    $tachoIcon = '';
                                                    $oilIcon = '';
                                                    $engineIcon = '';
                                                    $title_text = 'Unscheduled Inspection: '.$survey['date'];
                                                    if(isset($survey['tachograph']) && !empty($survey['tachograph'])){
                                                        $title_text .= '<br/>Tachograph Preformed';
                                                        $tachoIcon = '<div class ="tacho-icon"><span>T</span></div>';
                                                    }
                                                    if(isset($survey['oil']) && !empty($survey['oil'])) {
                                                        $title_text .= '<br/>Oil Changed';
                                                        $oilIcon = '<div class ="oil-icon"><span>O</span></div>';
                                                    }
                                                    if(isset($survey['engine']) && !empty($survey['engine'])) {
                                                        $title_text .= '<br/>Engine Filter Changed';
                                                        $engineIcon = '<div class ="engine-icon"><span>E</span></div>';
                                                    }
                                                ?>
                                                <a data-toggle="tooltip" title="<?php echo $title_text?>" class = "btn btn-effect-ripple btn-sm btn-warning" href = "<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $key2?>">
                                                    <span class="fa-stack fa-1x">
                                                        <i class="fa fa-wrench" aria-hidden="true"></i>
                                                        <?php echo $tachoIcon;?>
                                                        <?php echo $oilIcon;?>
                                                        <?php echo $engineIcon;?>
                                                    </span>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                                <!-- END OF SCHEDULE UNSCHEDULED BLOCK -->

                                <!-- START OF PSV BLOCK -->
                                <?php if(isset($data['psvs']) && !empty($data['psvs'])){?>
                                    <?php foreach($data['psvs'] as $key3 => $psv){?>
                                        <?php if($psv['date_weeks'] == $i && $psv['date_weeks'] > $today){?>
                                            <?php
                                            $title_text = 'PSV FOR: '.$psv['date'];
                                            ?>
                                            <a data-toggle="tooltip" title="<?php echo $title_text?>" class = "btn btn-effect-ripple btn-sm btn-info"><i class="fa fa-cog" aria-hidden="true"></i></a>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                                <!-- END OF PSV BLOCK-->

                                <!-- START OF SCHEDULES BLOCK -->
                                <?php if(isset($data['schedules']) && !empty($data['schedules'])){?>
                                    <?php foreach($data['schedules'] as $key4 => $schedule){?>
                                        <?php if($schedule['date_weeks'] == $i && $schedule['date_weeks'] > $today){?>
                                            <?php
                                            $title_text = 'SCHEDULE FOR: '.$schedule['date'];
                                            ?>
                                            <a data-toggle="tooltip" title="<?php echo $title_text?>" class = "btn btn-effect-ripple btn-sm btn-primary"><i class="fa fa-calendar" aria-hidden="true"></i></a>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                                <!-- END OF SCHEDULES BLOCK-->
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="row no-result">
            <div class="col-xs-12">
                <p>No vehicles matched.</p>
            </div>
        </div>
    <?php } ?>
</div>