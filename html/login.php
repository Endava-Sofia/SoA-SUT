<?php
ob_start();
session_start();
require_once 'api_client.php';

// Initialize variables
$email = '';
$emailError = '';
$passError = '';
$errMSG = '';

if (isset($_POST['btn-login'])) {
    $infoMSG = "Entered Login";
    $email = $_POST['email'];
    $upass = $_POST['pass'];

    $password = hash('sha256', $upass); // password hashing using SHA256
    $payload = [
        "email" => $email,
        "password" => $password
    ];

    $json_payload = json_encode($payload);
    $api_result = CallAPI("POST", "http://rest/login", $json_payload);
    $response = json_decode($api_result);

    if ($response === null) {
        $errMSG = "Unable to connect to the login service. Please try again later.";
    } elseif (property_exists($response, "message")) {
        $errMSG = $response->message;
    } else {
        $_SESSION['user'] = $response->id;
        $_SESSION['is_admin'] = $response->is_admin;
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Test Automation Training</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-container">
        <h2 class="text-center mb-4">Login</h2>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
        
            <?php if (isset($errMSG) && !empty($errMSG)): ?>
                <div class="alert alert-danger mb-3"><?php echo $errMSG; ?></div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Your Email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <?php if (!empty($emailError)): ?>
                    <span class="text-danger"><?php echo $emailError; ?></span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="pass" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="pass" class="form-control" placeholder="Your Password" required>
                </div>
                <?php if (!empty($passError)): ?>
                    <span class="text-danger"><?php echo $passError; ?></span>
                <?php endif; ?>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="btn-login" class="btn btn-primary">Sign In</button>
            </div>

            <div class="text-center mt-3">
                <a href="register.php" class="text-decoration-none">Sign Up Here...</a>
            </div>

        </form>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
