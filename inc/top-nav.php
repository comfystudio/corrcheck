<?php    $thisPage = basename($_SERVER['PHP_SELF']);    $thisPage = preg_replace('/\\.[^.\\s]{3,4}$/', '', $thisPage);?><div class="top-nav">    <ul>        <?php if(($user->user_role == "Manager") || ($_SESSION['user']['dashboard_permission'] == 1)){?>            <li>                <a href="<?php echo BASE_URL ?>vehicle-dashboard.php"                   class="<?php setCurrent($thisPage, 'dashboard'); ?>">Dashboard                </a>            </li>        <?php } ?>        <?php //if(($user->user_role == "Manager") || ($_SESSION['user']['vehicle_permission'] == true)){?>            <li>                <a href="<?php echo BASE_URL ?>vehicle-management.php"                   class="<?php setCurrent($thisPage, 'vehicles'); ?>">Vehicles                </a>            </li>        <?php //} ?>        <?php //if (($user->user_role == "Manager") || ($user->user_role == "Garage")) {?>            <li>                <a href="<?php echo BASE_URL ?>report-management.php"                   class="<?php setCurrent($thisPage, 'inspections'); ?>">Inspections                </a>            </li>        <?php //}?>        <?php if (($user->user_role == "Manager") || ($user->user_role == "Garage")) {?>            <li>                <a href="<?php echo BASE_URL ?>company-management.php"                   class="<?php setCurrent($thisPage, 'companies'); ?>">Company Management                </a>            </li>        <?php } ?>        <?php if ($user->user_role == "Manager" || $_SESSION['user']['user_permission'] == 1){?>            <li>                <a href="<?php echo BASE_URL ?>user-management.php"                   class="<?php setCurrent($thisPage, 'users'); ?>">User Management                </a>            </li>        <?php } ?>            <li>                <a href="https://vehicleenquiry.service.gov.uk/" target="_blank">                   Check My PSV                </a>            </li>    </ul></div>