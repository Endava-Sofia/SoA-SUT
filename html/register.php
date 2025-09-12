<?php
ob_start();
session_start();
require_once 'api_client.php';
require_once 'security.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['signup'])) {
    // Sanitize and validate input
    $title = sanitize_input($_POST['title']);
    $first_name = sanitize_input($_POST['first_name']);
    $sir_name = sanitize_input($_POST['sir_name']);
    $email = sanitize_input($_POST['email']);
    $country = sanitize_input($_POST['country']);
    $city = sanitize_input($_POST['city']);
    $upass = $_POST['pass']; // Don't sanitize password

    // Validate input
    $errors = [];
    if (!validate_name($first_name)) {
        $errors[] = "First name must contain only letters and be 2-15 characters long";
    }
    if (!validate_name($sir_name)) {
        $errors[] = "Surname must contain only letters and be 2-15 characters long";
    }
    if (!validate_email($email)) {
        $errors[] = "Invalid email format";
    }
    if (!validate_password($upass)) {
        $errors[] = "Password must be at least 6 characters long";
    }

    if (empty($errors)) {
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

        $json_payload = json_encode($payload);
        $api_result = CallAPI("POST", "/users", $json_payload);
        $response = json_decode($api_result);

        if (is_null($response)) {
            $errTyp = "danger";
            $errMSG = "Something went wrong. Please try again later.";
        } elseif (property_exists($response, "message")) {
            $errTyp = "warning";
            $errMSG = $response->message;
        } elseif (property_exists($response, "id")) {
            $_SESSION['user'] = $response->id;
            $_SESSION['is_admin'] = $response->is_admin;
            header("Location: index.php");
            exit();
        } else {
            $errTyp = "danger";
            $errMSG = "Unexpected response";
        }
    } else {
        $errTyp = "danger";
        $errMSG = implode("<br>", $errors);
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
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" autocomplete="off" class="needs-validation" novalidate>
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
                                   value="<?php echo isset($first_name) ? $first_name : ''; ?>"
                                   pattern="[a-zA-Z\s]{2,15}" required>
                            <div class="invalid-feedback">Please enter a valid first name (letters only, 2-15 characters).</div>
                        </div>

                        <div class="mb-3">
                            <label for="sir_name" class="form-label">Surname</label>
                            <input type="text" name="sir_name" class="form-control" id="sir_name" 
                                   value="<?php echo isset($sir_name) ? $sir_name : ''; ?>"
                                   pattern="[a-zA-Z\s]{2,15}" required>
                            <div class="invalid-feedback">Please enter a valid surname (letters only, 2-15 characters).</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" 
                                   value="<?php echo isset($email) ? $email : ''; ?>" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <div class="mb-3">
                            <label for="pass" class="form-label">Password</label>
                            <input type="password" name="pass" class="form-control" id="pass" 
                                   minlength="6" required>
                            <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" id="country" 
                                   value="<?php echo isset($country) ? $country : ''; ?>" required>
                            <div class="invalid-feedback">Please enter your country.</div>
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" class="form-control" id="city" 
                                   value="<?php echo isset($city) ? $city : ''; ?>" required>
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