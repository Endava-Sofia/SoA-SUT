<?php
ob_start();
session_start();

if (isset($_SESSION['user']) != "") {
    header("Location: index.php");
}

require_once 'api_client.php';

if (isset($_POST['signup'])) {

    $title = trim($_POST['title']);
    $first_name = trim($_POST['first_name']); // get posted data and remove whitespace
    $sir_name = trim($_POST['sir_name']);
    $email = trim($_POST['email']);
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $upass = trim($_POST['pass']);

    // hash password with SHA256;
    $password = hash('sha256', $upass);

    $payload = [
        "title" => $title,
        "first_name" => $first_name,
        "sir_name" => $sir_name,
        "email" => $email,
        "password" => $password,
        "country" => $country,
        "city" => $city,
        "is_admin" => false
    ];

    $json_payload =  json_encode($payload);
    $api_result = CallAPI("POST", "http://rest:5000/users", $json_payload);
    $response = json_decode($api_result);

    if (is_null($response)) {
        $errTyp = "danger";
        $errMSG = "Someting really bad has happened";
    } if (property_exists( $response, "message" )) {
        $errTyp = "warning";
        $errMSG = $response->message;
    } elseif (property_exists( $response, "id" )) {
        $_SESSION['user'] = $response->id;
        header("Location: index.php");
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
    <title>Registration</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="assets/css/style.css" type="text/css"/>
</head>

<script>
function setCityRequired(value) {
    if (value){
        document.getElementById("city").required = true;
    } else{
        document.getElementById("city").required = false;
    }
}
</script>
<body>

<div class="container">

    <div id="login-form">
        <form method="post" autocomplete="off">

            <div class="col-md-12">

                <div class="form-group">
                    <h2 class="">Register for our School of Automation</h2>
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

                <div class="form-group">
                    <div class="input-group">
                        <span>  Set Title: &nbsp; </span> 
                        <label class="radio-inline"><input type="radio" name="title" value="Mr." checked onclick="setCityRequired(true)">Mr.</label>
                        <label class="radio-inline"><input type="radio" name="title" value="Mrs." onclick="setCityRequired(false)">Mrs.</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                        <input type="text" name="first_name" class="form-control" placeholder="Enter First Name" pattern="[a-zA-Z]{1,15}" required/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                        <input type="text" name="sir_name" class="form-control" placeholder="Enter Sir Name" pattern="[^0-9]{2,15}" required/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter Email" required/>
                    </div>
                </div>

                
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                        <input type="password" name="pass" class="form-control" placeholder="Enter Password"
                               required="false"/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-globe"></span></span>
                        <input type="text" name="country" class="form-control" placeholder="Enter Country" required=/>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-globe"></span></span>
                        <input type="text" name="city" id="city" class="form-control" placeholder="Enter City" required/>
                    </div>
                </div>

                <div class="checkbox">
                    <label><input type="checkbox" id="TOS" value="This"><a href="#">I agree with
                            terms of service</a></label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-primary" name="signup" id="reg">Register</button>
                </div>

                <div class="form-group">
                    <hr/>
                </div>

                <div class="form-group">
                    <a href="login.php" type="button" class="btn btn-block btn-success" name="btn-login">Login</a>
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
