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
    $api_result = CallAPI("POST", "http://rest/users", $json_payload);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- Navigation Bar-->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">Endava SoA SUT</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="register.php">Register</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Register</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" class="needs-validation" novalidate>
                        <?php if (isset($errMSG)) { ?>
                            <div class="alert alert-<?php echo $errTyp; ?>">
                                <?php echo $errMSG; ?>
                            </div>
                        <?php } ?>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <select name="title" class="form-select" id="title" required>
                                <option value="Mr.">Mr.</option>
                                <option value="Mrs.">Mrs.</option>
                            </select>
                            <div class="invalid-feedback">Please select a title.</div>
                        </div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" id="first_name" 
                                   value="<?php echo isset($first_name) ? htmlspecialchars($first_name) : ''; ?>"
                                   pattern="[a-zA-Z]{1,15}" required>
                            <div class="invalid-feedback">Please enter a valid first name (letters only, 1-15 characters).</div>
                        </div>

                        <div class="mb-3">
                            <label for="sir_name" class="form-label">Surname</label>
                            <input type="text" name="sir_name" class="form-control" id="sir_name" 
                                   value="<?php echo isset($sir_name) ? htmlspecialchars($sir_name) : ''; ?>"
                                   pattern="[^0-9]{2,15}" required>
                            <div class="invalid-feedback">Please enter a valid surname (no numbers, 2-15 characters).</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" 
                                   value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <div class="mb-3">
                            <label for="pass" class="form-label">Password</label>
                            <input type="password" name="pass" class="form-control" id="pass" required>
                            <div class="invalid-feedback">Please enter a password.</div>
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" id="country" 
                                   value="<?php echo isset($country) ? htmlspecialchars($country) : ''; ?>" required>
                            <div class="invalid-feedback">Please enter your country.</div>
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" class="form-control" id="city" 
                                   value="<?php echo isset($city) ? htmlspecialchars($city) : ''; ?>" required>
                            <div class="invalid-feedback">Please enter your city.</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="tos" required>
                                <label class="form-check-label" for="tos">I agree with the terms of service</label>
                                <div class="invalid-feedback">You must agree to the terms of service.</div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="signup" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a href="login.php" class="btn btn-link">Already have an account? Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Form validation
(() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

</body>
</html>
