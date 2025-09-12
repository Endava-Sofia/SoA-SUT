<?php
ob_start();
session_start();
require_once 'api_client.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$rest_response = CallAPI("GET","/users/".$_SESSION['user']);
$loged_in_user = json_decode($rest_response);

// Handle profile picture upload
if (isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    
    // Debug logging
    error_log("Profile picture upload attempt:");
    error_log("File data: " . print_r($file, true));
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $errTyp = "danger";
        $errMSG = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
        error_log("Invalid file type: " . $file['type']);
    } elseif ($file['size'] > $max_size) {
        $errTyp = "danger";
        $errMSG = "File size too large. Maximum size is 5MB.";
        error_log("File too large: " . $file['size'] . " bytes");
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $errTyp = "danger";
        $errMSG = "Error uploading file. Please try again.";
        error_log("File upload error code: " . $file['error']);
    } else {
        // Upload file using API
        error_log("Calling API to upload profile picture...");
        $rest_response = CallAPI("POST", "/users/" . $_SESSION['user'] . "/profile-picture", null, $file);
        error_log("API Response: " . print_r($rest_response, true));
        $response = json_decode($rest_response, true);
        
        if (isset($response['message']) && $response['message'] === 'Profile picture uploaded successfully') {
            error_log("Profile picture uploaded successfully. New picture URL: " . $response['profile_picture']);
            // Update session user data
            $user_response = CallAPI("GET", "/users/" . $_SESSION['user']);
            $updated_user = json_decode($user_response);
            if ($updated_user && isset($updated_user->profile_picture)) {
                $_SESSION['profile_picture'] = $updated_user->profile_picture;
            }
            header("Location: user_profile.php?updated=true");
            exit;
        } else {
            $errTyp = "danger";
            $errMSG = isset($response['message']) ? $response['message'] : "Error uploading profile picture";
            error_log("Upload failed: " . $errMSG);
        }
    }
}

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
        $rest_response = CallAPI("PUT", "/users/" . $user_id, $json_payload);
        
        if ($rest_response === false) {
            $errTyp = "danger";
            $errMSG = "Failed to connect to the server. Please try again later.";
        } else {
            $response = json_decode($rest_response, true);
            
            if (is_null($response)) {
                $errTyp = "danger";
                $errMSG = "Invalid response from server. Please try again.";
                // Log the invalid response to console
                echo "<script>console.error('Invalid JSON Response:', " . json_encode($rest_response) . ");</script>";
            } elseif (isset($response['error'])) {
                $errTyp = "danger";
                $errMSG = "Server error: " . $response['error'];
                // Log the error response to console
                echo "<script>console.error('Server Error Response:', " . json_encode($response) . ");</script>";
            } elseif (isset($response['message'])) {
                $errTyp = "warning";
                $errMSG = $response['message'];
                // Log the message response to console
                echo "<script>console.warn('Server Message:', " . json_encode($response) . ");</script>";
            } elseif (isset($response['id'])) {
                header("Location: user_profile.php?updated=true");
                exit;
            } else {
                $errTyp = "danger";
                $errMSG = "Server returned an unexpected response. Please try again.";
                // Log the unexpected response and payload to console
                echo "<script>console.error('Unexpected Server Response:', " . json_encode($rest_response) . ");</script>";
                echo "<script>console.log('JSON Payload Sent:', " . json_encode($payload) . ");</script>";
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
        .navbar-profile-picture {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
            border: 2px solid #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
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
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .profile-picture-upload {
            position: relative;
            display: inline-block;
        }
        .profile-picture-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .profile-picture-upload .btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                        <?php if (isset($loged_in_user->profile_picture) && $loged_in_user->profile_picture): ?>
                            <img src="<?php echo htmlspecialchars($loged_in_user->profile_picture); ?>" 
                                 class="navbar-profile-picture" alt="Profile Picture">
                        <?php else: ?>
                            <img src="uploads/profile_pictures/default_profile.png" 
                                 class="navbar-profile-picture" alt="Default Profile Picture">
                        <?php endif; ?>
                        <?php echo $loged_in_user->email; ?>
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

    <?php if (isset($errMSG)) { ?>
        <div class="alert alert-<?php echo $errTyp; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $errMSG; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <div class="row">
        <!-- Profile Info Card -->
        <div class="col-md-4 mb-4">
            <div class="card profile-card">
                <div class="card-body text-center">
                    <div class="profile-picture-upload mb-3">
                        <?php if (isset($loged_in_user->profile_picture) && $loged_in_user->profile_picture): ?>
                            <img src="<?php echo htmlspecialchars($loged_in_user->profile_picture); ?>" 
                                 class="profile-picture" alt="Profile Picture">
                        <?php else: ?>
                            <img src="uploads/profile_pictures/default_profile.png" 
                                 class="profile-picture" alt="Default Profile Picture">
                        <?php endif; ?>
                        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>
                    <h3 class="card-title mb-3">
                        <?php echo htmlspecialchars($loged_in_user->title . ' ' . $loged_in_user->first_name . ' ' . $loged_in_user->sir_name); ?>
                    </h3>
                    <div class="profile-info">
                        <p class="mb-2">
                            <i class="bi bi-envelope"></i>
                            <?php echo htmlspecialchars($loged_in_user->email); ?>
                        </p>
                        <p class="mb-2">
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

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="user_profile.php" method="post" enctype="multipart/form-data" id="uploadForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" 
                               accept=".jpg,.jpeg,.png" required>
                        <div class="form-text">
                            Maximum file size: 5MB<br>
                            Allowed formats: JPG, JPEG, PNG<br>
                            Recommended size: 200x200 pixels
                        </div>
                    </div>
                    <div id="imagePreview" class="text-center mt-3" style="display: none;">
                        <img src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 50%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
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

// Image preview
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = preview.querySelector('img');
    
    if (file) {
        // Validate file type
        if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
            alert('Please select a valid image file (JPG, JPEG, or PNG)');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});
</script>
</body>
</html>