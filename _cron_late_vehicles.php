<?php
// This script should be run via cron once a day. It will check if a vehicle has missed its scheduled inspection and if so will add this to the tbl_late_vehicles table.
include($_SERVER["DOCUMENT_ROOT"] . "/inc/config.php");
include(ROOT_PATH . "inc/conn.php");

// Getting vehicles
$query = "
            SELECT
                t1.*, t2.company_name, t2.service_interval as company_service_interval, t2.user_start as company_user_start,
                t2.start_time as company_start_time, GROUP_CONCAT(t3.survey_ID separator ', ') as survey_ids,
                MAX(t3.survey_date) as most_recent_survey, MAX(t4.date) as most_recent_late,
                  DATEDIFF(CURDATE(), MAX(t3.survey_date))/7 as DiffDate,
                    CASE
                        WHEN (t1.service_interval IS NOT NULL) THEN t1.service_interval
                        WHEN (t2.service_interval IS NOT NULL) THEN t2.service_interval
                        ELSE 10
                    END true_service_interval
            FROM tbl_vehicles t1
            LEFT JOIN tbl_companies t2 ON t1.company_id = t2.company_ID
            LEFT JOIN tbl_surveys t3 ON t1.reg = t3.vehicle_reg
            LEFT JOIN tbl_late_vehicles t4 ON t1.id = t4.vehicle_id
            WHERE t1.is_active = 1
            GROUP BY t1.id
              HAVING ((most_recent_late < most_recent_survey) OR (most_recent_late IS NULL)) AND DiffDate > true_service_interval
            ORDER BY type DESC
        ";
try {
    $stmt = $db->prepare($query);
    $stmt->execute();
} catch (PDOException $ex) {
    die("Failed to run query: " . $ex->getMessage());
}
$rows = $stmt->fetchAll();

// Now we have our late vehicles (hopefully!) we can now add them to our tbl_late_vehicles
if(isset($rows) && !empty($rows)){
    foreach($rows as $row) {
        $query = "
        INSERT INTO tbl_late_vehicles (
            vehicle_id, type, date
        ) VALUES (
            " . $row['id'] . ", 'Scheduled', '" . date('Y-m-d') . "'
        )
    ";

        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute();
        } catch (PDOException $ex) {
            die("Failed to run THIS query: " . $ex->getMessage());
        }
    }
}

