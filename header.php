<?php
/* are we showing activity or testbed? */
/* check the name of the file: stats, monika or drawgantt are for activity */
$is_activity = ((strpos($_SERVER['PHP_SELF'], "/stats.php") !== FALSE) || (strpos($_SERVER['PHP_SELF'], "/drawgantt.php") !== FALSE) || (strpos($_SERVER['PHP_SELF'], "/monika.php") !== FALSE));

$body_padding_top = 62;
if (isset($_SESSION['is_auth']) && $_SESSION['is_auth'] && !$is_activity) $body_padding_top = 112;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FIT/IoT-LAB &#8226; Very large scale open wireless sensor network testbed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/wp-content/themes/alienship-1.2.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="/wp-content/themes/alienship-1.2.5/style.css" rel="stylesheet">
    <link href="/wp-content/themes/alienship-1.2.5-child/style.css" rel="stylesheet">
    <link href="css/portal.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="js/html5.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="img/favicon.png"/>

    <style type="text/css">
        body {
            padding-top: <?php echo $body_padding_top; ?>px;
        }
    </style>

    <!--<script src='/wp-includes/js/jquery/jquery.js'></script>-->
    <script src='js/jquery-1.10.2.min.js'></script>
    <script src="js/utils.js"></script>
    <script type='text/javascript' src='/wp-content/themes/alienship-1.2.5/js/bootstrap.min.js'></script>
    <script type='text/javascript'
            src='/wp-content/themes/alienship-1.2.5-child/js/bootstrap-hover-dropdown.min.js'></script>

</head>

<body>

<!--  NAV BAR  -->

<header class="navbar navbar-default navbar-onelab navbar-fixed-top" role="banner">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="/"> 
                <img src="/wp-content/themes/alienship-1.2.5-child/templates/parts/fit-iotlab3.png">
            </a>
        </div>


        <nav class="navbar-collapse collapse navbar-ex1-collapse" role="navigation">

            <?php include('./wp-menu/wp-menu.php'); ?>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown <?php echo($is_activity ? 'active' : ''); ?>">
                    <a href="./stats.php" title="Testbed activity" data-toggle="dropdown" data-hover="dropdown"><span
                            class="glyphicon glyphicon-info"></span> Activity <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li id="stats"><a href="./stats.php">Statistics</a></li>
                        <li id="monika"><a href="./monika.php">View nodes status</a></li>
                        <li id="drawgantt"><a href="./drawgantt.php">View gantt chart</a></li>
                    </ul>
                </li>
                <?php if (isset($_SESSION['is_auth']) && $_SESSION['is_auth']) { ?>
                    <li class="dropdown <?php echo($is_activity ? '' : 'active'); ?>">
                        <a href="./" title="Testbed" data-toggle="dropdown" data-hover="dropdown"><span
                                class="glyphicon glyphicon-wrench"></span> Testbed <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li id="dashboard"><a href="./dashboard.php"><span class="glyphicon glyphicon-list"></span>
                                    Dashboard</a></li>
                            <li id="exp_new"><a href="./exp_new.php"><span class="glyphicon glyphicon-file"></span> New
                                    Experiment</a></li>
                            <li id="profiles"><a href="./profiles.php"><span class="glyphicon glyphicon-cog"></span>
                                    Manage Profiles</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header"><span
                                    class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['login']; ?></li>
                            <li id="user_profile"><a href="./user_profile.php">Edit My Profile</a></li>
                            <li><a href="./logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li id="login"><span><a href="./" class="btn btn-primary testbed" title="Login">Access the tesbed</a></span></li>
                <?php } ?>
            </ul>
        </nav>
        <!--/.nav-collapse -->
    </div>
    <!--/.container -->
</header>

<!--  END NAV BAR  -->

<!--  LOGGED IN NAV BAR  -->

<?php if (isset($_SESSION['is_auth']) && $_SESSION['is_auth'] && !$is_activity) { ?>
    <div class="navbar navbar-default navbar-fixed-top navbar-grey" role="banner">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <nav class="navbar-collapse collapse navbar-ex2-collapse" role="navigation">
                <ul class="nav navbar-nav">
                    <li class="divider-vertical"></li>
                    <li id="dashboard2"><a href="./dashboard.php">Dashboard</a></li>
                    <li id="exp_new2"><a href="./exp_new.php">New Experiment</a></li>
                    <!--<li><a id='profilesModalLink' data-toggle="modal" data-target="#profiles_modal" style="cursor:pointer">Manage Profiles</a></li>-->
                    <li id="profiles2"><a href="./profiles.php">Manage Profiles</a></li>
                    <li class="divider-vertical"></li>
                </ul>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) { ?>
                    <ul class="nav navbar-nav pull-right">
                        <li class="dropdown" id="admin">
                            <a href="#" data-toggle="dropdown" data-hover="dropdown">Admin <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li id="admin_users"><a href="./admin_users.php">Users</a></li>
                                <li id="admin_exps"><a href="./admin_exps.php">Experiments</a></li>
                                <li id="admin_nodes"><a href="./admin_nodes.php">Nodes</a></li>
                                <li id="admin_stats"><a href="./admin_stats.php">Statistics</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php } ?>
            </nav>
            <!--/.nav-collapse -->
        </div>
        <!--/.container -->
    </div><!--/.navbar -->
<?php } ?>

<!--  END LOGGED IN NAV BAR  -->

<!-- ------------------------------------- -->
<!--            END HEADER                 -->
<!-- ------------------------------------- -->


