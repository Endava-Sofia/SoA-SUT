<?php
ob_start();
session_start();
require_once 'api_client.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
// select logged in users detail

$rest_response = CallAPI("GET", "/users/".$_SESSION['user']);
$loged_in_user = json_decode($rest_response);

if  ($rest_response = CallAPI("GET", "/countries")) {
    // Some variables we need for the table.
    $available_countries = json_decode($rest_response);
}

?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Search for skillful users</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="assets/css/index.css" type="text/css"/>
</head>
<body>
    <div class="row">
        <div class="col-lg-6">
            <h2>Countries</h2>
            <?php 
                foreach ($available_countries as &$country){
                    echo "<option value=\"country\"> $country </option>";
                }
                echo "</select>";
            ?>
        </div>
        <div class="col-lg-6">
            <h2>Cities</h2>
            <select class="form-select form-select-lg mb-3" multiple disabled=true id="cities" name="cities">"
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?php
            require_once 'dbconnect.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $searchTerm = isset($_POST['searchTerm']) ? trim($_POST['searchTerm']) : '';
                
                if (!empty($searchTerm)) {
                    try {
                        // Prepare the search query
                        $query = "SELECT u.*, GROUP_CONCAT(s.skill_name) as skills 
                                 FROM users u 
                                 LEFT JOIN user_skills us ON u.id = us.user_id 
                                 LEFT JOIN skills s ON us.skill_id = s.id 
                                 WHERE u.first_name LIKE :search 
                                    OR u.sir_name LIKE :search 
                                    OR u.email LIKE :search 
                                    OR u.country LIKE :search 
                                    OR u.city LIKE :search 
                                    OR s.skill_name LIKE :search 
                                 GROUP BY u.id";
                        
                        $stmt = $conn->prepare($query);
                        $searchParam = "%{$searchTerm}%";
                        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
                        $stmt->execute();
                        
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if ($results) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-striped table-hover">';
                            echo '<thead><tr>';
                            echo '<th>Name</th>';
                            echo '<th>Email</th>';
                            echo '<th>Location</th>';
                            echo '<th>Skills</th>';
                            echo '</tr></thead>';
                            echo '<tbody>';
                            
                            foreach ($results as $row) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['title'] . ' ' . $row['first_name'] . ' ' . $row['sir_name']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['city'] . ', ' . $row['country']) . '</td>';
                                echo '<td>';
                                if ($row['skills']) {
                                    $skills = explode(',', $row['skills']);
                                    echo '<ul class="list-unstyled mb-0">';
                                    foreach ($skills as $skill) {
                                        echo '<li><span class="badge bg-secondary">' . htmlspecialchars($skill) . '</span></li>';
                                    }
                                    echo '</ul>';
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</tbody></table>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-info">';
                            echo '<i class="bi bi-info-circle"></i> No results found for "' . htmlspecialchars($searchTerm) . '"';
                            echo '</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">';
                        echo '<i class="bi bi-exclamation-triangle"></i> An error occurred while searching. Please try again.';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert alert-warning">';
                    echo '<i class="bi bi-exclamation-circle"></i> Please enter a search term.';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
