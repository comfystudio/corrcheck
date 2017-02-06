<?php
/**
 * This script is intended to be used once in order to move vehicles from the inspections (surveys) and populate the tbl_vehicles table
 */
include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php");
$user->redirect_customer();

$query = "
            SET foreign_key_checks = 0
        ";
try {
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
}catch (PDOException $ex) {
    die("Failed to run query: " . $ex->getMessage());
}

// Getting all unique inspections and reg
$query = "
            SELECT t1.vehicle_reg, t1.vehicle_type, t1.company_ID, t1.make_model
            FROM tbl_surveys t1
            GROUP BY t1.vehicle_reg
        ";
try {
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
} catch (PDOException $ex) {
    die("Failed to run query: " . $ex->getMessage());
}
$inspections = $stmt->fetchAll();

//$data = array();
$data = "";
$total = count($inspections);
$count = 1;
foreach($inspections as $inspection){
    $data .= "('".$inspection['vehicle_reg']."', ".$inspection['company_ID'].", '".$inspection['vehicle_type']."', '".$inspection['make_model']."', '".date("Y-m-d")."', 10)";
    if($count != $total){
        $data .= ",";
    }
    $count++;
}

// Inserting new vehicles into tbl_vehicles
$query = "
            INSERT INTO tbl_vehicles
              (reg, company_id, type, make, psv_date, service_interval)
            VALUES
              $data
        ";
try {
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
} catch (PDOException $ex) {
    die("Failed to run query: " . $ex->getMessage());
}

echo "Added ".$total." vehicles to tbl_vehicles";
echo "redirecting in 10 seconds";
$query = "
            SET foreign_key_checks = 1
        ";
try {
    $stmt = $db->prepare($query);
    $result = $stmt->execute();
}catch (PDOException $ex) {
    die("Failed to run query: " . $ex->getMessage());
}
header("Location: report-listing.php");
die("Redirecting to report-listing.php");

?>