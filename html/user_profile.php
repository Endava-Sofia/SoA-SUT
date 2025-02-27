<?php
ob_start();
session_start();
require_once 'api_client.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$rest_response = CallAPI("GET", "http://rest/users/" . $_SESSION['user']);
$loged_in_user = json_decode($rest_response);

if (isset($_POST['Update'])) {
    $user_id = trim($_POST['id']);
    
    // Validate title
    $valid_titles = ['Mr.', 'Mrs.'];
    $title = trim($_POST['title']);
    if (!in_array($title, $valid_titles)) {
        $errTyp = "danger";
        $errMSG = "Invalid title selected.";
    } else {
        $email = trim($_POST['email']);
        $first_name = trim($_POST['first_name']);
        $sir_name = trim($_POST['sir_name']);
        $country = trim($_POST['country']);
        $city = trim($_POST['city']);

        $payload = array(
            "title" => $title,
            "first_name" => $first_name,
            "sir_name" => $sir_name,
            "email" => $email,
            "country" => $country,
            "city" => $city
        );

        $json_payload = json_encode($payload);
        $rest_response = CallAPI("PUT", "http://rest/users/" . $user_id, $json_payload);
        
        if ($rest_response === false) {
            $errTyp = "danger";
            $errMSG = "Failed to connect to the server. Please try again later.";
        } else {
            $response = json_decode($rest_response, true);
            
            if (is_null($response)) {
                $errTyp = "danger";
                $errMSG = "Invalid response from server. Please try again.";
            } elseif (isset($response['error'])) {
                $errTyp = "danger";
                $errMSG = "Server error: " . $response['error'];
            } elseif (isset($response['message'])) {
                $errTyp = "warning";
                $errMSG = $response['message'];
            } elseif (isset($response['id'])) {
                header("Location: user_profile.php?updated=true");
                exit;
            } else {
                $errTyp = "danger";
                $errMSG = "Server returned an unexpected response. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Profile</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS for profile cards -->
    <style>
        .profile-card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            background: #fff;
            border: none;
            position: relative;
            overflow: hidden;
        }
        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
            z-index: 1;
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        .profile-card:hover::before {
            opacity: 1;
        }
        .profile-card .card-body {
            padding: 2rem;
            position: relative;
            z-index: 2;
        }
        .profile-icon {
            font-size: 4rem;
            color: #0d6efd;
            position: relative;
            display: inline-block;
        }
        .profile-icon::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: radial-gradient(circle, rgba(13,110,253,0.1) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            z-index: -1;
        }
        .profile-info {
            margin-top: 1.5rem;
            position: relative;
        }
        .profile-info i {
            margin-right: 0.5rem;
            color: #6c757d;
            transition: color 0.3s;
        }
        .profile-info p:hover i {
            color: #0d6efd;
        }
        .profile-card .card-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .profile-card .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            border-bottom: none;
            position: relative;
            z-index: 2;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.15);
            border-color: #86b7fe;
        }
        .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.15);
            border-color: #86b7fe;
        }
    </style>
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
                    <a class="nav-link" href="users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search.php">Search</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person"></i> Logged in: <?php echo $loged_in_user->email; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item active" href="user_profile.php"><i class="bi bi-person-circle"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php?logout=true"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-4">
    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true') { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> Profile updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <div class="row">
        <!-- Profile Info Card -->
        <div class="col-md-4 mb-4">
            <div class="card profile-card">
                <div class="card-body text-center">
                    <div class="profile-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h4 class="card-title mt-3"><?php echo htmlspecialchars($loged_in_user->title . ' ' . $loged_in_user->first_name . ' ' . $loged_in_user->sir_name); ?></h4>
                    <div class="profile-info">
                        <p class="mb-1">
                            <i class="bi bi-envelope"></i>
                            <?php echo htmlspecialchars($loged_in_user->email); ?>
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-geo-alt"></i>
                            <?php echo htmlspecialchars($loged_in_user->city . ', ' . $loged_in_user->country); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="col-md-8">
            <div class="card profile-card">
                <div class="card-header bg-transparent border-0">
                    <h3 class="mb-0">Edit Profile</h3>
                </div>
                <div class="card-body">
                    <profile-edit>
                        <form method="post" action="user_profile.php" autocomplete="off" class="needs-validation" novalidate>
                            <?php if (isset($errMSG)) { ?>
                                <div class="alert alert-<?php echo $errTyp; ?> alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $errMSG; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php } ?>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title</label>
                                    <select name="title" class="form-select" id="title" required>
                                        <option value="Mr." <?php echo ($loged_in_user->title == "Mr.") ? "selected" : ""; ?>>Mr.</option>
                                        <option value="Mrs." <?php echo ($loged_in_user->title == "Mrs.") ? "selected" : ""; ?>>Mrs.</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a title.</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" id="first_name" value="<?php echo htmlspecialchars($loged_in_user->first_name); ?>" required>
                                    <div class="invalid-feedback">Please enter your first name.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="sir_name" class="form-label">Surname</label>
                                    <input type="text" name="sir_name" class="form-control" id="sir_name" value="<?php echo htmlspecialchars($loged_in_user->sir_name); ?>" required>
                                    <div class="invalid-feedback">Please enter your surname.</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" disabled class="form-control" id="email_display" value="<?php echo htmlspecialchars($loged_in_user->email); ?>" />
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($loged_in_user->email); ?>" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control" id="country" value="<?php echo htmlspecialchars($loged_in_user->country); ?>" required>
                                    <div class="invalid-feedback">Please enter your country.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" id="city" value="<?php echo htmlspecialchars($loged_in_user->city); ?>" required>
                                    <div class="invalid-feedback">Please enter your city.</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <input type="hidden" name="id" value="<?php echo $loged_in_user->id ?>" />
                                    <button type="submit" name="Update" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update Profile
                                    </button>
                                </div>
                            </div>
                        </form>
                    </profile-edit>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/profile-cards.js"></script>
<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
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