<?php
include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php");
// If form is submitted process it
if(!empty($_POST)){
    $query = "
            SELECT
                t1.*
            FROM tbl_vehicles t1
            WHERE t1.company_id = :id
            ";
    try {
        // Execute the query to create the user
        $stmt = $db->prepare($query);
        $stmt->bindValue(":id", $_POST['id'], PDO::PARAM_INT);

        $stmt->execute();
    } catch (PDOException $ex) {
        // Note: On a production website, you should not output $ex->getMessage().
        // It may provide an attacker with helpful information about your code.
        die("Failed to run THIS query: " . $ex->getMessage());
    }
    $rows = $stmt->fetchAll();
    foreach($rows as $key => $vehicle) {
        echo '<option value="'. $vehicle['reg'] .'" data-make="'.$vehicle['make'].'" data-type="'.$vehicle['type'].'"> ' . $vehicle['reg'] . '</option>';
    }
}else{
    return false;
}


?>