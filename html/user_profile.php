<?php
ob_start();
session_start();
require_once 'api_client.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$rest_response = CallAPI("GET","http://rest:5000/users/".$_SESSION['user']);
$loged_in_user = json_decode($rest_response);

if (isset($_POST['Update'])) {

    $user_id = trim($_POST['id']);
    $title = trim($_POST['title']);
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $sir_name = trim($_POST['sir_name']);
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);

    $rest_response = CallAPI("PUT","http://rest:5000/users/".$user_id);
    $loged_in_user = json_decode($rest_response);

    $payload = [
        "title" => $title,
        "first_name" => $first_name,
        "sir_name" => $sir_name,
        "email" => $email,
        "country" => $country,
        "city" => $city
    ];

    $json_payload = json_encode($payload);
    $rest_response = CallAPI("PUT","http://rest:5000/users/".$user_id, $json_payload);
    $response = json_decode($rest_response);

    if (is_null($response)) {
        $errTyp = "danger";
        $errMSG = "Someting really bad has happened";
    } elseif (property_exists( $response, "message" )) {
        $errTyp = "warning";
        $errMSG = $response->message;
    } elseif (property_exists( $response, "id" )) {
        $_SESSION['user'] = $response->id;
        header("Location: user_profile.php?updated=true");
        exit;
    } else {
        $errTyp = "danger";
        $errMSG = "Unexpected responce";
    }
}

?>

<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Profile</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="assets/css/index.css" type="text/css"/>
</head>

<body>
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
                    <li><a href="users.php">Users</a></li>
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
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<p>&nbsp;</p>

<div class="container">

    <div id="update_user_form">
        <form method="post" autocomplete="off">

            <div class="col-md-12">

                <div class="form-group">
                <?php 
                    $header_title = (isset($_GET['updated']) && $_GET['updated'] == 'true') ? "Profile Updated" : "Update Profile"
                ?>
                    <h2 class=""><?php echo $header_title ?></h2>
                </div>

                <div class="form-group">
                    <hr/>
                </div>

                <?php
                    if (isset($errMSG)) {

                        ?>
                        <div class="form-group">
                            <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                                <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                            </div>
                        </div>
                        <?php
                    }
                ?>
                
                <input type="hidden" name="id" value = "<?php echo $loged_in_user->id ?>" />
                <div class="form-group">
                    <div class="input-group">
                        <span>  Set Title: &nbsp; </span> 
                        <label class="radio-inline"><input type="radio" name="title" value="Mr."  <?php echo $loged_in_user->title == 'Mr.' ? 'checked' : '' ?> >Mr.</label>
                        <label class="radio-inline"><input type="radio" name="title" value="Mrs." <?php echo $loged_in_user->title == 'Mrs.' ? 'checked' : '' ?> >Mrs.</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                        <input type="text" name="first_name" class="form-control" value="<?php echo $loged_in_user->first_name ?>" required />
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                        <input type="text" name="sir_name" class="form-control" value="<?php echo $loged_in_user->sir_name ?>" required />
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                        <input type="email" disabled class="form-control" value="<?php echo $loged_in_user->email ?>" />
                        <input type="hidden" name="email" class="form-control" value="<?php echo $loged_in_user->email ?>" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-globe"></span></span>
                        <input type="text" name="country" class="form-control" value="<?php echo $loged_in_user->country ?>" required />
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-globe"></span></span>
                        <input type="text" name="city" class="form-control" value="<?php echo $loged_in_user->city ?>" required />
                    </div>
                </div>


                <div class="form-group">
                    <button type="submit" class="btn  btn-block btn-primary" name="Update" id="Update">Update</button>
                </div>

            </div>

        </form>
    </div>

</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/tos.js"></script>

</body>
</html>
