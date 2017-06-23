<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php
    //If no $_GET vars are passed
    if (empty($_GET)) {
        // redirect
        header("Location: report-management.php");
        die("Redirecting to report-management.php");  // critical!
    }

    // Get ID of survey
    $survey_id = $_GET ["surveyid"];

//     Customer Checks
//     1st we need to check to see if the current user is a customer. If so we must then ensure that the survey id being passed
//     belongs to the company ID of the customer
//     If it does then we will allow the page to load else we will redirect to report-listing.php

    if($user->user_role == "Customer"){
        $survey_check = survey_check($survey_id, $user->company_id, $db);;
        if(!survey_check($survey_id, $user->company_id, $db)){
            header("Location: report-management.php");
            die("Redirecting to report-management.php");  // critical!
        }
    } // end if user = customer

    //We now need to get our survey data
    $survey_dets = get_survey_main_dets($survey_id, $db);
    $where = "";
    if($user->user_role != "Manager"){
        $where = " AND t1.company_ID = ".$user->company_id."";
    }
    // We need to get surveys for the vehicle
    $query = "
                SELECT
                    t1.*, t2.username, t2.signature as inspector_signature, t3.status_name, t4.signature as foreman_signature, t4.username as foreman_username
                FROM tbl_surveys t1
                    LEFT JOIN tbl_users t2 ON t1.completed_by_user_ID = t2.user_id
                    LEFT JOIN tbl_survey_statuses t3 ON t1.status_id = t3.status_id
                    LEFT JOIN tbl_users t4 ON t1.supervised_by_user_ID = t4.user_id
                WHERE t1.vehicle_reg = '".$survey_dets["vehicle_reg"]."' ".$where." AND t1.survey_ID = ".$survey_id."
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

//    //We need to work out if oil change / filter change and tachograph has been changed....Have to loop through cause of mad Database
    foreach($surveys as $key => $survey) {
        $query = "
                SELECT
                    t1.question_response
                FROM tbl_survey_responses t1
                WHERE t1.survey_ID = '" . $survey['survey_ID'] . "' AND t1.question_ID = 'veh_smallservice_140'
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
                WHERE t1.survey_ID = '" . $survey['survey_ID'] . "' AND t1.question_ID = 'veh_smallservice_141'
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
                WHERE t1.survey_ID = '" . $survey['survey_ID'] . "' AND (t1.question_ID = 'veh_tacho_58' OR t1.question_ID = 'veh_tacho_59' OR t1.question_ID = 'veh_tacho_60')
            ";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
        } catch (PDOException $ex) {
            die("Failed to run query: " . $ex->getMessage());
        }

        $tacho = $stmt->fetch();
        if (isset($tacho['response']) && !empty($tacho['response'])) {
            $tacho['response'] = explode(',', $tacho['response']);
            foreach ($tacho['response'] as $tac) {
                if ($tac == 'significant_defect' || $tac == 'slight_defect') {
                    $surveys[$key]['tacho'] = true;
                    break;
                }
            }
        } else {
            $surveys[$key]['tacho'] = false;
        }
    }

    //We now want to get the axle details
    $query = "
        SELECT
            tbl_survey_axle_responses.question_ID,
            tbl_survey_axle_responses.question_response
        FROM
            tbl_survey_axle_responses
        WHERE
            tbl_survey_axle_responses.survey_ID = $survey_id
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $axles = $stmt->fetchAll();

    $temp = array();
    foreach($axles as $key => $axle){
        $temp[$axle['question_ID']] = $axle['question_response'];
    }
    $axles = $temp;

    //We now want to get the LUBRICATION details
    $query = "
            SELECT t1.question_text, t1.im, t1.question_ID AS question_question_id, t2.question_ID, t2.question_response, t3.question_response as detail_response, t4.question_response as rectified_response
            FROM tbl_questions t1
                LEFT JOIN tbl_survey_responses t2 ON CONCAT('veh_lub_', t1.question_ID ) = t2.question_ID  AND t2.survey_ID = $survey_id
                LEFT JOIN tbl_survey_responses t3 ON CONCAT('veh_lights_details_', t1.question_ID ) = t3.question_ID AND t3.survey_ID = $survey_id
                LEFT JOIN tbl_survey_responses t4 ON CONCAT('veh_lights_rectified_by_', t1.question_ID ) = t4.question_ID AND t4.survey_ID = $survey_id
            WHERE t1.section_ID = 4
            ORDER BY t1.question_ID ASC
            ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $lubrications = $stmt->fetchAll();

    //We now want to get lights details
    $query = "
        SELECT t1.question_text, t1.im, t1.question_ID AS question_question_id, t2.question_ID, t2.question_response, t3.question_response as detail_response, t4.question_response as rectified_response
        FROM tbl_questions t1
            LEFT JOIN tbl_survey_responses t2 ON CONCAT('veh_lights_', t1.question_ID ) = t2.question_ID AND t2.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t3 ON CONCAT('veh_lights_details_', t1.question_ID ) = t3.question_ID AND t3.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t4 ON CONCAT('veh_lights_rectified_by_', t1.question_ID ) = t4.question_ID AND t4.survey_ID = $survey_id
        WHERE t1.section_ID = 5
        ORDER BY t1.question_ID ASC
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $lights = $stmt->fetchAll();

    //We now want to get veh_tacho_ details
    $query = "
        SELECT t1.question_text, t1.im, t1.question_ID AS question_question_id, t2.question_ID, t2.question_response, t3.question_response as detail_response, t4.question_response as rectified_response
        FROM tbl_questions t1
            LEFT JOIN tbl_survey_responses t2 ON CONCAT('veh_tacho_', t1.question_ID ) = t2.question_ID AND t2.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t3 ON CONCAT('veh_tacho_details_', t1.question_ID ) = t3.question_ID AND t3.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t4 ON CONCAT('veh_tacho_rectified_by_', t1.question_ID ) = t4.question_ID AND t4.survey_ID = $survey_id
        WHERE t1.section_ID = 6
        ORDER BY t1.question_ID ASC
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $tachos = $stmt->fetchAll();


    //We now want to get veh_insidecab_ details
    $query = "
        SELECT t1.question_text, t1.im, t1.question_ID AS question_question_id, t2.question_ID, t2.question_response, t3.question_response as detail_response, t4.question_response as rectified_response
        FROM tbl_questions t1
            LEFT JOIN tbl_survey_responses t2 ON CONCAT('veh_insidecab_', t1.question_ID ) = t2.question_ID AND t2.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t3 ON CONCAT('veh_insidecab_details_', t1.question_ID ) = t3.question_ID AND t3.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t4 ON CONCAT('veh_insidecab_rectified_by_', t1.question_ID ) = t4.question_ID AND t4.survey_ID = $survey_id
        WHERE t1.section_ID = 7
        ORDER BY t1.question_ID ASC
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $insideCabs = $stmt->fetchAll();

    //We now want to get veh_glevel_ details
    $query = "
        SELECT t1.question_text, t1.im, t1.question_ID AS question_question_id, t2.question_ID, t2.question_response, t3.question_response as detail_response, t4.question_response as rectified_response
        FROM tbl_questions t1
            LEFT JOIN tbl_survey_responses t2 ON CONCAT('veh_glevel_', t1.question_ID ) = t2.question_ID AND t2.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t3 ON CONCAT('veh_glevel_details_', t1.question_ID ) = t3.question_ID AND t3.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t4 ON CONCAT('veh_glevel_rectified_by_', t1.question_ID ) = t4.question_ID AND t4.survey_ID = $survey_id
        WHERE t1.section_ID = 8
        ORDER BY t1.question_ID ASC
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $groundLevels = $stmt->fetchAll();

    //We now want to get veh_smallservice_ details
    $query = "
        SELECT t1.question_text, t1.im, t1.question_ID AS question_question_id, t2.question_ID, t2.question_response, t3.question_response as detail_response, t4.question_response as rectified_response
        FROM tbl_questions t1
            LEFT JOIN tbl_survey_responses t2 ON CONCAT('veh_smallservice_', t1.question_ID ) = t2.question_ID AND t2.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t3 ON CONCAT('veh_smallservice_details_', t1.question_ID ) = t3.question_ID AND t3.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t4 ON CONCAT('veh_smallservice_rectified_by_', t1.question_ID ) = t4.question_ID AND t4.survey_ID = $survey_id
        WHERE t1.section_ID = 9
        ORDER BY t1.question_ID ASC
        ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $smallServices = $stmt->fetchAll();

    //We now want to get veh_additional_ details
    $query = "
        SELECT t1.question_text, t1.im, t1.question_ID AS question_question_id, t2.question_ID, t2.question_response, t3.question_response as detail_response, t4.question_response as rectified_response
        FROM tbl_questions t1
            LEFT JOIN tbl_survey_responses t2 ON CONCAT('veh_additional_', t1.question_ID ) = t2.question_ID AND t2.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t3 ON CONCAT('veh_additional_details_', t1.question_ID ) = t3.question_ID AND t3.survey_ID = $survey_id
            LEFT JOIN tbl_survey_responses t4 ON CONCAT('veh_additional_rectified_by_', t1.question_ID ) = t4.question_ID AND t4.survey_ID = $survey_id
        WHERE t1.section_ID = 10
        ORDER BY t1.question_ID ASC
            ";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Failed to run query: " . $ex->getMessage());
    }
    $additionals = $stmt->fetchAll();


    //need to convert some booleans into relevent strings
    if($surveys[0]['scheduled']){
        $surveys[0]['scheduled'] = "Scheduled";
    }else{
        $surveys[0]['scheduled'] = "Not Scheduled";
    }

    if($surveys[0]['psv']){
        $surveys[0]['psv'] = "Yes";
    }else{
        $surveys[0]['psv'] = "No";
    }
    //Now we build our PDF output
    require_once($_SERVER["DOCUMENT_ROOT"].'/inc/mpdf/mpdf.php');
    $mpdf = new mPDF();
    $mpdf->showImageErrors = true;
    $mpdf->defaultfooterline = 0;
    $mpdf->defaultheaderline = 0;
    $mpdf->setAutoTopMargin = 'stretch';
    $stylesheet = file_get_contents($_SERVER["DOCUMENT_ROOT"].'/css/pdf.css');
    $mpdf->WriteHTML($stylesheet,1);
    $mpdf->setHeader(
        '
        <div style="overflow: hidden; min-height: 400px; display: block;">
            <table style="overflow: hidden; min-height: 400px; display: block;">
                <tr>
                    <td><img src = "'.$_SERVER["DOCUMENT_ROOT"].'/img/corr-brothers-logo.png" width="80px"></td>
                    <td class = "bold text-right" style = "vertical-align:bottom;">Page {PAGENO} of {nb}</td>
                </tr>
            </table>

            <div class = "underline" style="clear:both; margin-top:12px;"></div>

            <table class = "header" style="min-height: 400px; max-height: 400px;">
                <tr>
                    <td class = "bold">Survey ID</td>
                    <td>'.$surveys[0]['survey_ID'].'</td>

                    <td class = "bold">Vehicle Reg</td>
                    <td>'.$surveys[0]['vehicle_reg'].'</td>

                    <td class = "bold">Completed By</td>
                    <td>'.$surveys[0]['username'].'</td>
                </tr>

                <tr>
                    <td class = "bold">Invoice Number</td>
                    <td>'.$surveys[0]['invoice_num'].'</td>

                    <td class = "bold">Vehicle Type</td>
                    <td>'.$surveys[0]['vehicle_type'].'</td>

                    <td class = "bold">Survey Date</td>
                    <td>'.$surveys[0]['survey_date'].'</td>
                </tr>

                <tr>
                    <td class = "bold">Scheduled?</td>
                    <td>'.$surveys[0]['scheduled'].'</td>

                    <td class = "bold">Make Model</td>
                    <td>'.$surveys[0]['make_model'].'</td>

                    <td class = "bold">Company Name</td>
                    <td>'.$survey_dets['company_name'].'</td>
                </tr>

                <tr>
                    <td class = "bold">PSV?</td>
                    <td>'.$surveys[0]['psv'].'</td>

                    <td class = "bold">Odometer Reading</td>
                    <td>'.$surveys[0]['odo_reading'].' km</td>
                </tr>

                <tr>
                    <td class = "bold">Pre-service Remarks</td>
                    <td colspan = "5">'.$surveys[0]['pre_service_remarks'].'</td>
                </tr>

                <tr>
                    <td class = "bold">Report Details/Notes</td>
                    <td colspan = "5">'.$surveys[0]['notes_parts_list'].'</td>
                </tr>

            </table>
        </div>

        <div class = "underline clear" style="clear:both;"></div>
        '
    );
    //Break Preformance AND Tyre Thread
    $mpdf->WriteHTML(' <div class = "clear"></div>');
    $mpdf->SetColumns(2);
    $mpdf->WriteHTML('<h4 class = "text-centre">Brake Performance</h4>');
    $mpdf->WriteHTML('<table class = "border" style="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td>&nbsp;</td>');
            $mpdf->WriteHTML('<td colspan="2" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10 bold">Service Break</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td colspan="2" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10 bold">Parking Break</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td>IM: 71, 72</td>');
            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">DEC(%)</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">IMB(%)</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">DEC(%)</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">IMB(%)</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        //Need to work out how many lines we need so we don't have empty rows
        $maxBreak = 0;
        $maxTyre = 0;
        foreach($axles as $key => $value){
            if((strpos($key,"_service_bk_")!==false) || (strpos($key,"_parking_bk_")!==false)) {
                $position = explode('axle_', $key);
                if($position[1] > $maxBreak){
                    $maxBreak = $position[1];
                }
            }

            if((strpos($key,"_inner_")!==false) || (strpos($key,"_outer_")!==false)) {
                $position2 = explode('axle_', $key);
                if($position2[1] > $maxTyre){
                    $maxTyre = $position2[1];
                }
            }
        }

        for($count = 1; $count<=$maxBreak; $count++){
            $mpdf->WriteHTML('<tr>');
                $mpdf->WriteHTML('<td>');
                    $mpdf->WriteHTML('Axle '.$count);
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                    if(array_key_exists('axle_'.$count.'_service_bk_dec', $axles)){
                        $mpdf->WriteHTML($axles['axle_'.$count.'_service_bk_dec']);
                    }else{
                        $mpdf->WriteHTML('&nbsp;');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                    if(array_key_exists('axle_'.$count.'_service_bk_imb', $axles)){
                        $mpdf->WriteHTML($axles['axle_'.$count.'_service_bk_imb']);
                    }else{
                        $mpdf->WriteHTML('&nbsp;');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                    if(array_key_exists('axle_'.$count.'_parking_bk_dec', $axles)){
                        $mpdf->WriteHTML($axles['axle_'.$count.'_parking_bk_dec']);
                    }else{
                        $mpdf->WriteHTML('&nbsp;');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                    if(array_key_exists('axle_'.$count.'_parking_bk_imb', $axles)){
                        $mpdf->WriteHTML($axles['axle_'.$count.'_parking_bk_imb']);
                    }else{
                        $mpdf->WriteHTML('&nbsp;');
                    }
                $mpdf->WriteHTML('</td>');
            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    $mpdf->WriteHTML('<h4 class = "text-centre">Tyre Thread</h4>');
    $mpdf->WriteHTML('<table class = "border" style ="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td>&nbsp;</td>');
            $mpdf->WriteHTML('<td colspan="2" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10 bold">Near Side</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td colspan="2" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10 bold">Off Side</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td>IM: 8</td>');
            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">Outside</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">Inside</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">Inside</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-10">Outside</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        for($count = 1; $count <= $maxTyre; $count++){
            $mpdf->WriteHTML('<tr>');
                $mpdf->WriteHTML('<td>');
                    $mpdf->WriteHTML( 'Axle '.$count);
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                    if(array_key_exists('axle_'.$count.'_outer_near', $axles)){
                        $mpdf->WriteHTML($axles['axle_'.$count.'_outer_near']);
                    }else{
                        $mpdf->WriteHTML('&nbsp;');
                    }
                $mpdf->WriteHTML( '</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                    if(array_key_exists('axle_'.$count.'_inner_near', $axles)){
                        $mpdf->WriteHTML($axles['axle_'.$count.'_inner_near']);
                    }else{
                        $mpdf->WriteHTML('&nbsp;');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                    if(array_key_exists('axle_'.$count.'_inner_off', $axles)){
                        $mpdf->WriteHTML($axles['axle_'.$count.'_inner_off']);
                    }else{
                        $mpdf->WriteHTML('&nbsp;');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "text-centre">');
                if(array_key_exists('axle_'.$count.'_outer_off', $axles)){
                    $mpdf->WriteHTML($axles['axle_'.$count.'_outer_off']);
                }else{
                    $mpdf->WriteHTML('&nbsp;');
                }
                $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    $mpdf->SetColumns(1);
    $mpdf->WriteHTML('<div class = "underline clear" style="padding-top:12px; clear:both; margin-top:12px;"></div>');

    //Key / Legend
    $mpdf->WriteHTML('<table class = "subheader">');
        $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td style = "font-weight: bold;">');
                $mpdf->WriteHTML('<span class = "font-10 bold">Condition Key</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td>');
                $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class = "font-10"> = Satisfactory</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td>');
                $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class = "font-10"> = Slight Defect</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td>');
                $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class = "font-10"> = Not Applicable</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td>');
                $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span> <span class = "font-10"> = Significant Defect</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');
    $mpdf->WriteHTML('</table>');

    $mpdf->WriteHTML('<div class = "underline clear" style="padding-top:12px; clear:both; margin-top:12px;"></div>');
    $mpdf->SetColumns(2);

    //Lubrication Block
    $mpdf->WriteHTML('<table class = "border small-table" style ="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr class = "bold font-8 background-grey">');
            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">IM</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Condition</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Item Inspected</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Details</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Rectified By</div>');
            $mpdf->WriteHTML('</th>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr class = "bold font-8 background-darkgrey">');
            $mpdf->WriteHTML('<td colspan="5" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-8 bold">Lubrication</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        foreach($lubrications as $key => $lubrication){
            $mpdf->WriteHTML('<tr class = "font-8">');
                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$lubrication['im'].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    if(isset($lubrication['question_response']) && !empty($lubrication['question_response']) && $lubrication['question_response'] == 'significant_defect') {
                        $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($lubrication['question_response']) && !empty($lubrication['question_response']) && $lubrication['question_response'] == 'slight_defect'){
                        $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($lubrication['question_response']) && !empty($lubrication['question_response']) && $lubrication['question_response'] == 'not_applicable'){
                        $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }else{
                        $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$lubrication["question_text"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$lubrication["detail_response"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$lubrication["rectified_response"].'</span>');
                $mpdf->WriteHTML('</td>');
            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    //lights Block
    $mpdf->WriteHTML('<table class = "border small-table" style ="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr class = "bold font-8 background-grey">');
            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">IM</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Condition</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Item Inspected</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Details</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Rectified By</div>');
            $mpdf->WriteHTML('</th>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr class = "bold font-8 background-darkgrey">');
            $mpdf->WriteHTML('<td colspan="5" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-8 bold">Lights</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        foreach($lights as $key => $value){
            $mpdf->WriteHTML('<tr class = "font-8">');
                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value['im'].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    if(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'significant_defect') {
                        $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'slight_defect'){
                        $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'not_applicable'){
                        $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }else{
                        $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["question_text"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["detail_response"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["rectified_response"].'</span>');
                $mpdf->WriteHTML('</td>');
            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    //tachos Block
    $mpdf->WriteHTML('<table class = "border small-table" style ="border-collapse:collapse;">');
    $mpdf->WriteHTML('<tr class = "bold font-8 background-grey">');
        $mpdf->WriteHTML('<th class = "bold font-8">');
            $mpdf->WriteHTML('<div class = "bold font-8">IM</div>');
        $mpdf->WriteHTML('</th>');

        $mpdf->WriteHTML('<th class = "bold font-8">');
            $mpdf->WriteHTML('<div class = "bold font-8">Condition</div>');
        $mpdf->WriteHTML('</th>');

        $mpdf->WriteHTML('<th class = "bold font-8">');
            $mpdf->WriteHTML('<div class = "bold font-8">Item Inspected</div>');
        $mpdf->WriteHTML('</th>');

        $mpdf->WriteHTML('<th class = "bold font-8">');
            $mpdf->WriteHTML('<div class = "bold font-8">Details</div>');
        $mpdf->WriteHTML('</th>');

        $mpdf->WriteHTML('<th class = "bold font-8">');
            $mpdf->WriteHTML('<div class = "bold font-8">Rectified By</div>');
        $mpdf->WriteHTML('</th>');
    $mpdf->WriteHTML('</tr>');

    $mpdf->WriteHTML('<tr class = "bold font-8 background-darkgrey">');
        $mpdf->WriteHTML('<td colspan="5" class = "text-centre">');
            $mpdf->WriteHTML('<span class = "font-8 bold">Tachograph</span>');
        $mpdf->WriteHTML('</td>');
    $mpdf->WriteHTML('</tr>');

    foreach($tachos as $key => $value){
        $mpdf->WriteHTML('<tr class = "font-8">');
            $mpdf->WriteHTML('<td class = "font-8">');
                $mpdf->WriteHTML('<span class = "font-8">'.$value['im'].'</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "font-8">');
                if(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'significant_defect') {
                    $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'slight_defect'){
                    $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'not_applicable'){
                    $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }else{
                    $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "font-8">');
                $mpdf->WriteHTML('<span class = "font-8">'.$value["question_text"].'</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "font-8">');
                $mpdf->WriteHTML('<span class = "font-8">'.$value["detail_response"].'</span>');
            $mpdf->WriteHTML('</td>');

            $mpdf->WriteHTML('<td class = "font-8">');
                $mpdf->WriteHTML('<span class = "font-8">'.$value["rectified_response"].'</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');
    }
    $mpdf->WriteHTML('</table>');

    //Inside Block
    $mpdf->WriteHTML('<table class = "border small-table" style ="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr class = "bold font-8 background-grey">');
            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">IM</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Condition</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Item Inspected</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Details</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Rectified By</div>');
            $mpdf->WriteHTML('</th>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr class = "bold font-8 background-darkgrey">');
            $mpdf->WriteHTML('<td colspan="5" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-8 bold">Inside Cab</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        foreach($insideCabs as $key => $value){
            $mpdf->WriteHTML('<tr class = "font-8">');
                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value['im'].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    if(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'significant_defect') {
                        $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'slight_defect'){
                        $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'not_applicable'){
                        $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }else{
                        $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["question_text"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["detail_response"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["rectified_response"].'</span>');
                $mpdf->WriteHTML('</td>');
            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    //Ground Level Block
    $mpdf->WriteHTML('<table class = "border small-table" style ="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr class = "bold font-8 background-grey">');
            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">IM</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Condition</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Item Inspected</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Details</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Rectified By</div>');
            $mpdf->WriteHTML('</th>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr class = "bold font-8 background-darkgrey">');
            $mpdf->WriteHTML('<td colspan="5" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-8 bold">Ground Level</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        foreach($groundLevels as $key => $value){
            $mpdf->WriteHTML('<tr class = "font-8">');
                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value['im'].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    if(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'significant_defect') {
                        $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'slight_defect'){
                        $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'not_applicable'){
                        $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }else{
                        $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                    }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["question_text"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["detail_response"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["rectified_response"].'</span>');
                $mpdf->WriteHTML('</td>');
            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    //smallServices Block
    $mpdf->WriteHTML('<table class = "border small-table" style ="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr class = "bold font-8 background-grey">');
            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">IM</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Condition</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Item Inspected</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Details</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Rectified By</div>');
            $mpdf->WriteHTML('</th>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr class = "bold font-8 background-darkgrey">');
            $mpdf->WriteHTML('<td colspan="5" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-8 bold">Small Service</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        foreach($smallServices as $key => $value){
            $mpdf->WriteHTML('<tr class = "font-8">');
                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value['im'].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                if(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'significant_defect') {
                    $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'slight_defect'){
                    $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'not_applicable'){
                    $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }else{
                    $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["question_text"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["detail_response"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["rectified_response"].'</span>');
                $mpdf->WriteHTML('</td>');
            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    //additionals Block
    $mpdf->WriteHTML('<table class = "border small-table" style ="border-collapse:collapse;">');
        $mpdf->WriteHTML('<tr class = "bold font-8 background-grey">');
            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">IM</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Condition</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Item Inspected</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Details</div>');
            $mpdf->WriteHTML('</th>');

            $mpdf->WriteHTML('<th class = "bold font-8">');
                $mpdf->WriteHTML('<div class = "bold font-8">Rectified By</div>');
            $mpdf->WriteHTML('</th>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr class = "bold font-8 background-darkgrey">');
            $mpdf->WriteHTML('<td colspan="5" class = "text-centre">');
                $mpdf->WriteHTML('<span class = "font-8 bold">Additional</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        foreach($additionals as $key => $value){
            $mpdf->WriteHTML('<tr class = "font-8">');
                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value['im'].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                if(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'significant_defect') {
                    $mpdf->WriteHTML('<span class = "block-red bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'slight_defect'){
                    $mpdf->WriteHTML('<span class = "block-yellow bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }elseif(isset($value['question_response']) && !empty($value['question_response']) && $value['question_response'] == 'not_applicable'){
                    $mpdf->WriteHTML('<span class = "block-grey bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }else{
                    $mpdf->WriteHTML('<span class = "block-green bold">&nbsp;&nbsp;&nbsp;&nbsp;</span>');
                }
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["question_text"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["detail_response"].'</span>');
                $mpdf->WriteHTML('</td>');

                $mpdf->WriteHTML('<td class = "font-8">');
                    $mpdf->WriteHTML('<span class = "font-8">'.$value["rectified_response"].'</span>');
                $mpdf->WriteHTML('</td>');
            $mpdf->WriteHTML('</tr>');
        }
    $mpdf->WriteHTML('</table>');

    $mpdf->SetColumns(1);
        $mpdf->WriteHTML('<div class = "underline clear" style="clear:both;"></div>');
    $mpdf->SetColumns(2);
//    $mpdf->WriteHTML('<pagebreak />');

    //Signature tables
    $mpdf->WriteHTML('<table style ="border-collapse:collapse; border: 1px solid #000000; margin-top: -4px; padding-top: -4px;">');
        $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td class = "bold text-center" style ="border-bottom: 1px solid black;">');
                $mpdf->WriteHTML('<span class = "bold">Inspected by: '.$surveys[0]['username'].' on '.date("d/m/Y", strtotime($surveys[0]["survey_date"])).'</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr>');
            if(isset($surveys[0]['inspector_signature']) && !empty($surveys[0]['inspector_signature'])){
                $mpdf->WriteHTML('<td align="center" class = "bold text-center" style ="width:90px; height:80px;">');
                    $mpdf->WriteHTML('<img src = "'.ROOT_PATH.'/img/signatures/'.$surveys[0]['inspector_signature'].'" width="100px">');
                $mpdf->WriteHTML('</td>');
            }else{
                $mpdf->WriteHTML('<td align="center" class = "bold text-center" style ="width:90px; height:80px;">');
                    $mpdf->WriteHTML('<div class ="fake-box">&nbsp;</div>');
                $mpdf->WriteHTML('</td>');
            }
        $mpdf->WriteHTML('</tr>');
    $mpdf->WriteHTML('</table>');

    $mpdf->WriteHTML('<table style ="border-collapse:collapse; border: 1px solid #000000;">');
        $mpdf->WriteHTML('<tr>');
            $mpdf->WriteHTML('<td class = "bold text-center" style ="border-bottom: 1px solid black;">');
                $mpdf->WriteHTML('<span class = "bold">Approved by  '.$surveys[0]['foreman_username'].' on '.date("d/m/Y", strtotime($surveys[0]["date_last_update"])).'</span>');
            $mpdf->WriteHTML('</td>');
        $mpdf->WriteHTML('</tr>');

        $mpdf->WriteHTML('<tr>');
            if(isset($surveys[0]['foreman_signature']) && !empty($surveys[0]['foreman_signature'])){
                $mpdf->WriteHTML('<td align="center" class = "bold text-center" style ="width:90px; height:80px;">');
                    $mpdf->WriteHTML('<img src = "'.ROOT_PATH.'/img/signatures/'.$surveys[0]['foreman_signature'].'" width="100px">');
                $mpdf->WriteHTML('</td>');
            }else{
                $mpdf->WriteHTML('<td align="center" class = "bold text-center" style ="width:90px; height:80px;">');
                    $mpdf->WriteHTML('<div class ="fake-box">&nbsp;</div>');
                $mpdf->WriteHTML('</td>');
            }
        $mpdf->WriteHTML('</tr>');
    $mpdf->WriteHTML('</table>');

    $mpdf->debug = true;
    $mpdf->Output();
    exit;


?>