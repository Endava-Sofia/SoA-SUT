<?php
ob_start();
session_start();
require_once 'api_client.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
// select logged in users detail

$rest_response = CallAPI("GET","http://rest/users/".$_SESSION['user']);
$loged_in_user = json_decode($rest_response);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $cities = isset($_POST["selectedCities"]) ? $_POST["selectedCities"] : [];
    $countries = isset($_POST["selectedCountries"]) ? $_POST["selectedCountries"] : [];
    $skills = isset($_POST["skillsToSearch"]) ? $_POST["skillsToSearch"] : [];

    $payload = [
        "cities" => $cities,
        "countries" => $countries,
        "skills" => $skills,
    ];

    $json_payload =  json_encode($payload);
    $api_result = CallAPI("POST", "http://rest/search/users", $json_payload);
    $search_result = json_decode($api_result);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Results</title>
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
                        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        <li><a class="dropdown-item" href="user_profile.php"><i class="bi bi-person"></i> Profile</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Search Results</h3>
                </div>
                <div class="card-body">
                    <?php if ($search_result): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <th>Surname</th>
                                        <th>Email</th>
                                        <th>Country</th>
                                        <th>City</th>
                                        <th>Skill</th>
                                        <th>Skill Category</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $index = 0;
                                        foreach ($search_result as $result){
                                            $index++;
                                            echo "<tr>\n";
                                            echo "<th scope=\"row\">$index</th>\n";
                                            echo "<td>$result->first_name</td>\n";
                                            echo "<td>$result->sir_name</td>\n";
                                            echo "<td>$result->email</td>\n";
                                            echo "<td>$result->country</td>\n";
                                            echo "<td>$result->city</td>\n";
                                            echo "<td>$result->skill_name</td>\n";
                                            echo "<td>$result->skill_category</td>\n";
                                            echo "</tr>\n";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No users found matching your search criteria.
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <a href="search.php" class="btn btn-primary">
                            <i class="bi bi-search"></i> New Search
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>