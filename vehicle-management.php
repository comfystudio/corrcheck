<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php
    $where = "";
    if($user->user_role != "Manager"){
        $where = " AND t1.company_id = ".$user->company_id." OR t1.user_id = ".$user->user_id;
    }

    //Checking if we have $_GET['company-filter'] we do same as with post basically
    if(isset($_GET) && !empty($_GET['company-filter']) && $_GET['company-filter'] != 'search-all'){
        $_GET['company-filter'] = format_string($_GET['company-filter']);
        $where .= " AND t1.company_id = ".$_GET['company-filter'];
    }

    //Adding filter if user has used company filter
    if(isset($_POST) && !empty($_POST['company-filter']) && $_POST['company-filter'] != 'search-all'){
        $_POST['company-filter'] = format_string($_POST['company-filter']);
        $where .= " AND t1.company_id = ".$_POST['company-filter'];
    }

    //Adding filter if user has used search filter
    if(isset($_POST) && !empty($_POST['reg-search-input'])){
        $_POST['reg-search-input'] = format_string($_POST['reg-search-input']);
        $where .= " AND t1.reg LIKE '%".$_POST['reg-search-input']."%'";
    }

    //Adding filter if user has used vehicle type filter
    if(isset($_POST) && !empty($_POST['vehicle-filter']) && $_POST['vehicle-filter'] != 'search-all'){
        $_POST['vehicle-filter'] = format_string($_POST['vehicle-filter']);
        $where .= " AND t1.type = '".$_POST['vehicle-filter']."'";
    }

    //Working out if we have sort and then order the data
    $order = 'ORDER BY created DESC';
    if(isset($_GET['sort']) && !empty($_GET['sort'])){
        switch ($_GET['sort']) {
            case "reg":
                $order = 'ORDER BY reg ASC';
                break;
            case "type":
                $order = 'ORDER BY type ASC';
                break;
            case "make":
                $order = 'ORDER BY make ASC';
                break;
            case "service":
                $order = 'ORDER BY service_interval ASC';
                break;
            case "company":
                $order = 'ORDER BY company_id ASC';
                break;
            case "user":
                $order = 'ORDER BY user_id ASC';
                break;
            case "psv":
                $order = 'ORDER BY psv_date ASC';
                break;
            case "inspection":
                $order = 'ORDER BY user_start ASC';
                break;
            default:
                $order = 'ORDER BY created DESC';
        }
    }

    $query = "
        SELECT COUNT(t1.id) AS total
          FROM tbl_vehicles t1
        WHERE 1 = 1
          ".$where."
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
    $total = $stmt->fetchAll();
?>



    <!-- app main column -->

<div class="app-col-main grid_12 alpha">
    <h2>Vehicle Management</h2>
    <?php
        if(isset($_GET['sort']) && !empty($_GET['sort'])) {
            $pages = new Pagination(20, '&sort=' . $_GET['sort'] . '&page', $total[0]['total']);
        }else{
            $pages = new Pagination(20, '&page', $total[0]['total']);
        }

        // Do DB Query
        $query = "
            SELECT
                t1.*, t2.company_name, t3.username
            FROM tbl_vehicles t1
            LEFT JOIN tbl_companies t2 ON t1.company_id = t2.company_ID
            LEFT JOIN tbl_users t3 ON t1.user_id = t3.user_id
            WHERE 1 = 1
              ".$where."
              ".$order."
            LIMIT ".$pages->get_limit()."
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
        $rows = $stmt->fetchAll();

        $page_links = $pages->page_links();

    ?>

    <?php if ($user->user_role == "Manager"){?>
        <div class="search-filter cf">
            <form method="post" action = "/vehicle-management.php">
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

                <div class="sf-search-box search-group">
                    <label for="reg-search-input">Registration Search: </label>
                    <input type="text" name="reg-search-input" id="reg-search-input" class="form-control" value="<?php if(isset($_POST['reg-search-input'])){echo $_POST['reg-search-input'];} ?>">
                </div>

                <div class="sf-vehicle-filter search-group">
                    <label for="vehicle-filter">
                        Select Vehicle Type:
                    </label>
                    <select name="vehicle-filter" id="vehicle-filter" class="form-control">
                        <option value="search-all" <?php if(isset($_POST["vehicle-filter"]) && $_POST["vehicle-filter"]=="search-all") echo 'selected'; ?>>Search All</option>
                        <option value="lorry"<?php if(isset($_POST["vehicle-filter"])){ if($_POST["vehicle-filter"] == "lorry"){ echo "selected";}}?>>Lorry</option>
                        <option value="trailer"<?php if(isset($_POST["vehicle-filter"])){ if($_POST["vehicle-filter"] == "trailer"){ echo "selected";}}?>>Trailer</option>
                    </select>
                </div><!-- sf-company-filter -->
                <button id="btn-filter" name="btn-filter" type="submit" class="btn btn-success">Apply Filter</button>
            </form>
        </div><!-- .search-filter -->
    <?php } ?>


    <table class="table table-bordered table-striped">

        <thead>
        <tr>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=reg">Vehicle Registration <?php if(isset($_GET['sort']) && $_GET['sort'] == 'reg'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=type">Type <?php if(isset($_GET['sort']) && $_GET['sort'] == 'type'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=make">Make <?php if(isset($_GET['sort']) && $_GET['sort'] == 'make'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=service">Service Interval <?php if(isset($_GET['sort']) && $_GET['sort'] == 'service'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=company">Company <?php if(isset($_GET['sort']) && $_GET['sort'] == 'company'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=user">User <?php if(isset($_GET['sort']) && $_GET['sort'] == 'user'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=psv">PSV Date <?php if(isset($_GET['sort']) && $_GET['sort'] == 'psv'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th><a href="<?php echo BASE_URL; ?>vehicle-management.php?sort=inspection">Custom Inspection Date? <?php if(isset($_GET['sort']) && $_GET['sort'] == 'inspection'){echo '<i class="fa fa-chevron-down" aria-hidden="true"></i>';}?></a></th>
            <th class="text-center"><i class="fa fa-flash"></i></th>
        </tr>

        </thead>

        <tbody>

        <?php foreach ($rows as $row): ?>
            <tr <?php if(isset($row['is_active']) && $row['is_active'] == 2){ echo 'class = danger';}?>>
                <td><a href="<?php echo BASE_URL; ?>vehicle-view.php?vehicle_id=<?php echo $row["id"] ?>" style = "color:#428bca;"> <?php echo $row["reg"]; ?></a></td>
                <td><?php echo $row["type"]; ?></td>
                <td><?php echo $row["make"]; ?></td>
                <td><?php echo $row["service_interval"]. " Weeks"; ?></td>
                <td>
                    <?php
                        if(isset($row['company_name']) && !empty($row['company_name'])){
                            echo '<a href = "/vehicle-management.php?company-filter='.$row['company_id'].'" style = "color:#428bca;">'.$row["company_name"].'</a>';
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if(isset($row['username']) && !empty($row['username'])){
                            echo $row["username"];
                        }
                    ?>
                </td>
                <td><?php echo date('j F, Y', strtotime($row['psv_date']));?></td>
                <td>
                    <?php
                        if($row['user_start'] == 1){
                            echo 'Yes - Inspection from '. date('j F, Y', strtotime($row['start_time']));
                        }else{
                            echo "No - based on last inspection.";
                        }
                    ?>
                </td>
                <td>
                    <a href="<?php echo BASE_URL; ?>vehicle-view.php?vehicle_id=<?php echo $row["id"] ?>"
                       class="btn btn-primary">view
                    </a>
                    <?php if($user->check_vehicle_premission()){?>
                        <a href="<?php echo BASE_URL; ?>edit_vehicle.php?vehicle_id=<?php echo $row["id"] ?>"
                           class="btn btn-warning">edit
                        </a>
                        <a href="<?php echo BASE_URL; ?>delete_vehicle.php?vehicle_id=<?php echo $row["id"] ?>"
                           class="btn btn-danger">delete
                        </a>
                    <?php } ?>
                </td>
            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>
    <?php if(!empty($page_links)){ ?>
        <div class="dataTables_paginate paging_bootstrap">
            <?php echo $page_links; ?>
        </div>
    <?php } ?>


    <?php if($user->check_vehicle_premission()){?>
        <a href="<?php echo BASE_URL; ?>create_vehicle.php" class="btn btn-success">Create New Vehicle</a>
    <?php } ?>

</div><!-- app-col-main -->
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>