<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?><?php          // If no $_GET vars are passed  if (empty($_GET)) {  		// redirect  	    	header("Location: report-management.php");     	    	die("Redirecting to report-management.php");  // critical!  }    // Get ID of survey    $survey_id = $_GET ["survey_id"];    // Customer Checks    // 1st we need to check to see if the current user is a customer. If so we must then ensure that the survey id being passed    // belongs to the company ID of the customer    // If it does then we will allow the page to load else we will redirect to report-listing.php    if($user->user_role == "Customer"){        $survey_check = survey_check($survey_id, $user->company_id, $db);        //echo $survey_check;        if(!survey_check($survey_id, $user->company_id, $db)){            header("Location: report-management.php");            die("Redirecting to report-management.php");  // critical!        }    } // end if user = customer// Get main survey details$survey_dets = get_survey_main_dets($survey_id, $db);$where = "";if($user->user_role != "Manager"){    $where = " AND t1.company_ID = ".$user->company_id."";}// We need to get surveys for the vehicle$query = "            SELECT                t1.*, t2.username, t3.status_name            FROM tbl_surveys t1            LEFT JOIN tbl_users t2 ON t1.completed_by_user_ID = t2.user_id            LEFT JOIN tbl_survey_statuses t3 ON t1.status_id = t3.status_id            WHERE t1.vehicle_reg = '".$survey_dets["vehicle_reg"]."' ".$where."            ORDER BY t1.survey_date DESC        ";try {    // These two statements run the query against your database table.    $stmt = $db->prepare($query);    $stmt->execute();} catch (PDOException $ex) {    // Note: On a production website, you should not output $ex->getMessage().    // It may provide an attacker with helpful information about your code.    die("Failed to run query: " . $ex->getMessage());}// Finally, we can retrieve all of the found rows into an array using fetchAll$surveys = $stmt->fetchAll();//We need to work out if oil change / filter change and tachograph has been changed....Have to loop through cause of mad Databaseforeach($surveys as $key => $survey){    $query = "            SELECT                t1.question_response            FROM tbl_survey_responses t1            WHERE t1.survey_ID = '".$survey['survey_ID']."' AND t1.question_ID = 'veh_smallservice_140'        ";    try {        $stmt = $db->prepare($query);        $stmt->execute();    } catch (PDOException $ex) {        die("Failed to run query: " . $ex->getMessage());    }    $oil = $stmt->fetch();    $surveys[$key]['oil'] = $oil;    $query = "            SELECT                t1.question_response            FROM tbl_survey_responses t1            WHERE t1.survey_ID = '".$survey['survey_ID']."' AND t1.question_ID = 'veh_smallservice_141'        ";    try {        $stmt = $db->prepare($query);        $stmt->execute();    } catch (PDOException $ex) {        die("Failed to run query: " . $ex->getMessage());    }    $filter = $stmt->fetch();    $surveys[$key]['filter'] = $filter;    $query = "            SELECT                GROUP_CONCAT(t1.question_response) as response            FROM tbl_survey_responses t1            WHERE t1.survey_ID = '".$survey['survey_ID']."' AND (t1.question_ID = 'veh_tacho_58' OR t1.question_ID = 'veh_tacho_59' OR t1.question_ID = 'veh_tacho_60')        ";    try {        $stmt = $db->prepare($query);        $stmt->execute();    } catch (PDOException $ex) {        die("Failed to run query: " . $ex->getMessage());    }    $tacho = $stmt->fetch();    if(isset($tacho['response']) && !empty($tacho['response'])){        $tacho['response'] = explode(',',$tacho['response']);        foreach($tacho['response'] as $tac){            if($tac == 'significant_defect' || $tac == 'slight_defect'){                $surveys[$key]['tacho'] = true;                break;            }        }    }else{        $surveys[$key]['tacho'] = false;    }}?><?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>	<!-- app main column --><div class="app-col-main grid_12">	<div class="section-header hidden-print">	<div class="btn-ctrl">        <a onClick="window.print();return false" class="btn btn-success">Print This Report</a>        <a href="<?php echo BASE_URL; ?>edit-report.php?surveyid=<?php echo $survey_dets["survey_ID"] ?>" class="btn btn-primary">Edit This Report >></a>	</div></div>	<div class="panel panel-default">	  <div class="panel-heading">	    <h3 class="panel-title">Inspection Details</h3>	  </div>	  <div class="panel-body">  	  	<table class="survey-results">	  		<tr><td>Survey ID:</strong></td><td> <?php echo $survey_dets["survey_ID"]; ?></td></tr>            <tr><td>Invoice Number:</strong></td><td> <?php echo $survey_dets["invoice_num"]; ?></td></tr>	  		<tr><td>Vehicle Type:</strong></td><td> <?php echo $survey_dets["vehicle_type"]; ?></td></tr>	  		<tr><td>Vehicle Reg:</strong></td><td> <?php echo $survey_dets["vehicle_reg"]; ?></td></tr>	  		<tr><td>Make Model:</strong></td><td> <?php echo $survey_dets["make_model"]; ?></td></tr>	  		<tr><td>Odometer Reading:</strong></td><td> <?php echo $survey_dets["odo_reading"]; ?> <?php echo $survey_dets["odo_type"]; ?></td></tr>	  		<tr><td>Pre-service Remarks:</strong></td><td> <?php echo $survey_dets["pre_service_remarks"]; ?></td></tr>	  		<tr><td>Notes / Parts List:</strong></td><td> <?php echo nl2br($survey_dets["notes_parts_list"]); ?></td></tr>	  		<tr><td>Survey Date:</strong></td><td> <?php echo $survey_dets["survey_date"]; ?></td></tr>	  		<tr><td>Company Name:</strong></td><td> <?php echo $survey_dets["company_name"]; ?></td></tr>	  		<tr><td>Completed By:</strong></td><td> <?php echo $survey_dets["username"]; ?></td></tr>	  		<tr><td>Survey Status:</strong></td><td> <?php echo $survey_dets["survey_status"]; ?></td></tr>            <tr><td>Scheduled?</strong></td><td> <?php if(isset($survey_dets['scheduled']) && $survey_dets['scheduled']){ echo 'Scheduled';}else{ echo 'Unscheduled';} ?></td></tr>            <tr><td>PSV?</strong></td><td> <?php if(isset($survey_dets['psv']) && $survey_dets['psv']){ echo 'Yes';}else{ echo 'No';} ?></td></tr>            <?php if(isset($survey_dets['psv']) && $survey_dets['psv']){ ?>                <tr><td>PSV Presented By:</strong></td><td> <?php echo $survey_dets["psv_presented"]; ?></td></tr>                <tr><td>PSV Notes:</strong></td><td> <?php echo $survey_dets["psv_notes"]; ?></td></tr>            <?php } ?>        </table>	  </div>	</div>	<div class="panel panel-default">	  <div class="panel-heading">	    <h3 class="panel-title">Fault Details</h3>	  </div>	  <div class="panel-body">	  	<?php report_details($survey_id, $db); ?>	  </div>    </div>    <div class="panel panel-default">        <div class="panel-heading">            <h3 class="panel-title">Previous Inspections Log</h3>        </div>        <div class="panel-body">            <?php //report_details($survey_id, $db); ?>            <table class="survey-results lube-results">                <thead>                <tr>                    <td class="hdr-insp-lube">Inspection No.</td>                    <td class="hdr-cond-lube">Date</td>                    <td class="hdr-dets-lube">Inspection By</td>                    <td class="hdr-dets-lube">Oil Changed?</td>                    <td class="hdr-dets-lube">Filter Changed?</td>                    <td class="hdr-dets-lube">Tachograph Checked?</td>                    <td class="hdr-dets-lube">Status</td>                    <td class="text-center"><i class="fa fa-flash"></i></td>                </tr>                </thead>                <?php foreach($surveys as $key => $survey){ ?>                    <tr>                        <td><?php echo $survey['survey_ID'];?></td>                        <td><?php echo date('j F, Y', strtotime($survey['survey_date']));?></td>                        <td><?php echo $survey['username'];?></td>                        <td class="text-center">                            <?php                            if(isset($survey['oil']) && ($survey['oil']['question_response'] == 'significant_defect' || $survey['oil']['question_response'] == 'slight_defect')){                                echo '<a class="btn btn-effect-ripple btn-sm btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>';                            }else{                                echo '<a class="btn btn-effect-ripple btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';                            }                            ?>                        </td>                        <td class="text-center">                            <?php                            if(isset($survey['filter']) && ($survey['filter']['question_response'] == 'significant_defect' || $survey['filter']['question_response'] == 'slight_defect')){                                echo '<a class="btn btn-effect-ripple btn-sm btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>';                            }else{                                echo '<a class="btn btn-effect-ripple btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';                            }                            ?>                        </td>                        <td class="text-center">                            <?php                            if(isset($survey['tacho']) && !empty($survey['tacho'])){                                echo '<a class="btn btn-effect-ripple btn-sm btn-success"><i class="fa fa-check" aria-hidden="true"></i></a>';                            }else{                                echo '<a class="btn btn-effect-ripple btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';                            }                            ?>                        </td>                        <td><?php echo $survey['status_name'];?></td>                        <td>                            <a href="<?php echo BASE_URL; ?>view-report.php?survey_id=<?php echo $survey["survey_ID"] ?>"                               class="btn btn-primary">view                            </a>                        </td>                    </tr>                <?php }?>            </table>        </div>    </div><?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>