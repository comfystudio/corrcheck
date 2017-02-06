<?php
include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php");
// If form is submitted process it
if(!empty($_POST)){
    $query = "
                UPDATE tbl_users t1
                SET
                    dashboard_permission = :dashboard_permission
                WHERE
                    user_id = :id
            ";
    try {
        // Execute the query to create the user
        $stmt = $db->prepare($query);
        $stmt->bindValue(":id", $_POST['id'], PDO::PARAM_INT);
        $stmt->bindValue(":dashboard_permission", $_POST['state'], PDO::PARAM_INT);

        $result = $stmt->execute();
    } catch (PDOException $ex) {
        // Note: On a production website, you should not output $ex->getMessage().
        // It may provide an attacker with helpful information about your code.
        die("Failed to run THIS query: " . $ex->getMessage());
    }
}
?>