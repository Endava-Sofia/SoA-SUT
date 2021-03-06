<?php
    ob_start();
    session_start();
    require_once 'api_client.php';

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }
    // select logged in users detail

    $rest_response = CallAPI("GET","http://rest:5000/users/".$_SESSION['user']);
    $loged_in_user = json_decode($rest_response);
    // Table sorting mechanism

    // For extra protection these are the columns of which the user can sort by (in your database table).
    $columns = array('title', 'first_name', 'sir_name', 'country', 'city', 'email');
    
    // Only get the column if it exists in the above columns array, if it doesn't exist the database table will be sorted by the first item in the columns array.
    $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];

    // Get the sort order for the column, ascending or descending, default is ascending.
    $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

    // Get the result...
    $url_params = [
        "order_by" => $column,
        "orientation" => $sort_order
    ];

    if  ($rest_response = CallAPI("GET","http://rest:5000/users", $url_params)) {
        // Some variables we need for the table.
        $available_users = json_decode($rest_response);
        $up_or_down = str_replace(array('ASC','DESC'), array('by-attributes','by-attributes-alt'), $sort_order); 
        $asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
        $add_class = ' class="highlight"';
    
    }
?>


<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hello,<?php echo $loged_in_user->email; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="assets/css/index.css" type="text/css"/>
</head>
<body>

    <!-- Navigation Bar-->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Endava SoA SUT</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><a href="#">Users</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                        aria-expanded="false">
                            <span
                                class="glyphicon glyphicon-user"></span>&nbsp;Logged
                            in: <?php echo $loged_in_user->email; ?>
                            &nbsp;<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="logout.php?logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;Logout</a>
                            </li>
                            <li><a href="user_profile.php"><span class="glyphicon glyphicon-user"></span>&nbsp;Profile</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<p>&nbsp;</p>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
    <h2>Available users</h2>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table class="table table-striped table-bordered" id="users_list" class="col-md-8">
        <thead>
            <tr>
                <th scope="col">
                    <a href="users.php?column=title&order=<?php echo $asc_or_desc; ?>">
                    Title
                    <i class="glyphicon glyphicon-sort<?php echo $column == 'title' ? '-' . $up_or_down : ''; ?>"></i>
                    </a>
                </th>

                <th scope="col">
                    <a href="users.php?column=first_name&order=<?php echo $asc_or_desc; ?>">
                    First Name
                    <i class="glyphicon glyphicon-sort<?php echo $column == 'first_name' ? '-' . $up_or_down : ''; ?>"></i>
                    </a>
                </th>

                <th scope="col">
                    <a href="users.php?column=sir_name&order=<?php echo $asc_or_desc; ?>">
                    Sir Name
                    <i class="glyphicon glyphicon-sort<?php echo $column == 'sir_name' ? '-' . $up_or_down : ''; ?>"></i>
                    </a>
                </th>

                <th scope="col">
                    <a href="users.php?column=country&order=<?php echo $asc_or_desc; ?>">
                    Country
                    <i class="glyphicon glyphicon-sort<?php echo $column == 'country' ? '-' . $up_or_down : ''; ?>"></i>
                    </a>
                </th>

                <th scope="col">
                    <a href="users.php?column=city&order=<?php echo $asc_or_desc; ?>">
                    City
                    <i class="glyphicon glyphicon-sort<?php echo $column == 'city' ? '-' . $up_or_down : ''; ?>"></i>
                    </a>
                </th>

                
                <th scope="col">
                    <a href="users.php?column=email&order=<?php echo $asc_or_desc; ?>">
                    Email
                    <i class="glyphicon glyphicon-sort<?php echo $column == 'email' ? '-' . $up_or_down : ''; ?>"></i>
                    </a>
                </th>

            <?php
                if ($loged_in_user->is_admin){
                    echo "<th scope='col'>Action</th>";
                }
            ?>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach ($available_users as &$user){
                    echo "<tr>";
                    echo "<td> $user->title </td>";
                    echo "<td> $user->first_name </td>";
                    echo "<td> $user->sir_name </td>";
                    echo "<td> $user->country </td>";
                    echo "<td> $user->city </td>";
                    echo "<td> $user->email </td>";
                    if ($loged_in_user->is_admin){
                        echo "<td><a href='delete_user.php?id={$user->id}' onclick=\"return confirm('Are you SURE you want to delete {$user->first_name} {$user->sir_name}? ')\">delete</a> </td>";
                    }
                    echo "</tr>";
                }
            ?>
        </tbody>
    </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>

