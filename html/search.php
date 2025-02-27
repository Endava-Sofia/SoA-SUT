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

if  ($rest_response = CallAPI("GET","http://rest/countries")) {
    // Some variables we need for the table.
    $available_countries = json_decode($rest_response);
}

if  ($rest_response = CallAPI("GET","http://rest/skills")) {
    // Some variables we need for the table.
    $available_skills = json_decode($rest_response);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search for skillful users</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        table#availableSkills tbody tr:nth-child(n+11) {
            display: none;
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
                    <a class="nav-link active" href="#">Search</a>
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
        <div class="col-lg-6 container text-center"><h2>Countries</h2></div>
        <div class="col-lg-6 container text-center"><h2>Cities</h2></div>
    </div>

    <div class="row">
        <p>&nbsp</p>
    </div>

    <div class="row">
        <div class="col-lg-2"> <label for="availableCountries">Available Countries</label></div>
        <div class="col-lg-1">&nbsp</div>
        <div class="col-lg-2"><label>Countries for search</label></div>
        <div class="col-lg-3"><label>Available Cities</label></div>
        <div class="col-lg-1">&nbsp</div>
        <div class="col-lg-3"><label>Cities for search</label></div>
    </div>
    
    <form method="post" action="search_result.php">
    <div class="row">
        <div class="col-lg-2">
            <?php
                if (!$available_countries) {
                    echo "<select class=\"form-select form-control\" disabled id=\"availableCountries\" title=\"Available Countries\">";
                    echo "<option selected>No countries are available :(.....</option>";
                    echo "</select>";
                }else {
                    echo "<select class=\"form-select form-control\" id=\"availableCountries\" name=\"availableCountries\" title=\"County Selection\">";
                    foreach ($available_countries as &$country){
                        echo "<option value=\"$country\">$country</option>";
                    }
                    echo "</select>";
                }
            ?>
        </div>

        <div class="col-lg-1 container text-center">
            <button type="button" class="btn btn-primary" id="addCountryToSearch" onclick="addSelectedItems('countries')">Add</button>
        </div>

        <div class="col-lg-2">
            <ul class="list-group min-h-200 container-border" id="searchedCountries">
            <?php
                if (!$available_countries) {
                    echo "<li class=\"list-group-item\">";
                    echo "<label class=\"form-check-label\">Nothing to be added</label>";
                    echo "</li>";
                }
            ?>
            </ul>
        </div>

        <div class="col-lg-3 container text-left">
            <select class="form-select form-control min-h-200 container-border list-group" multiple disabled=true id="availableCities" name="availableCities"></select>
        </div>

        <div class="col-lg-1 container text-center">
            <button type="button" class="btn btn-primary" id="addCitiesToSearch" onclick="addSelectedItems('cities')">Add</button>

        </div>

        <div class="col-lg-3">
            <ul class="list-group min-h-200 container-border" id="searchedCities">
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h2>Skills</h2></div>
                <div class="panel-body">
                    <table class="table table-striped" id="availableSkills">
                        <thead>
                            <tr>
                            <th scope="col">Search</th>
                            <th scope="col">Skill Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Description</th>
                            </tr>
                        </thead>
                        <?php 
                            if ($available_skills) {
                                echo "<tbody>\n";
                                foreach ($available_skills as &$skill){
                                    echo "<tr>\n";
                                    echo "<td><input type=\"checkBox\" name=\"skillsToSearch[]\" value=\"$skill->skill_name\"></td>\n";
                                    echo "<td>$skill->skill_name</td>\n";
                                    echo "<td>$skill->skill_category</td>\n";
                                    echo "<td>$skill->skill_description</td>\n";
                                    echo "</tr>\n";
                                }
                                echo "</tbody>\n";
                            }
                        
                        ?>
                    </table>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php 
                                    if ($available_skills) {
                                        
                                        $pages = ceil(sizeof($available_skills) / 10);
                                        $page = 1; 
                                        echo "<li class=\"page-item active\"><a class=\"page-link\" href=\"#\">$page</a></li>";
                                        for($page=2; $page <= $pages;  $page++){
                                            echo "<li class=\"page-item\"><a class=\"page-link\" href=\"#\">$page</a></li>";
                                        }
                                    }
                                ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">&nbsp</div>
            <div class="col-lg-4 container text-center">
                <button type="submit" class="btn btn-primary" name="search" id="search">
                    Search
                </button>
            </div>
        <div class="col-lg-4">&nbsp</div>
    </div>
    </form>
</div>

<script src="assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
 $(document).ready(function () {

    $('#availableCountries').change(function () {
        var selectedCountry = $(this).val();
        console.log("Selected - " + selectedCountry);
        $.ajax({
            url: 'api_client.php',
            method: 'POST',
            data: {country: selectedCountry},
            success: function (response) {
                var citiesSelect = $('#availableCities');
                citiesSelect.empty();
                console.log(response)
                var data  = JSON.parse(response);
                console.log("data - " + response)
                $.each(data, function (index, city) {
                    citiesSelect.append('<option class="list-group-item" value="' + city + '">' + city + '</option>');
                });
                citiesSelect.prop('disabled', false);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

        function switchPage(page) {
            $('table#availableSkills tbody tr').hide();
            var startIndex = (page - 1) * 10;
            var endIndex = startIndex + 10;
            $('table tbody tr').slice(startIndex, endIndex).show();
        }

        $('.pagination li').click(function () {
            $('.pagination li').removeClass('active');
            $(this).addClass('active');
            var page = parseInt($(this).text());
            switchPage(page);
        });

            switchPage(1);

    });

    function addSelectedItems(el) {
        var sourceElementId;
        var destinationElementId;
        var addedItemName;

        if (el === "countries"){
            sourceElementId = "availableCountries";
            destinationElementId = "searchedCountries";
            addedItemName = "selectedCountries[]";
        }else if(el === "cities"){
            sourceElementId = "availableCities";
            destinationElementId = "searchedCities";
            addedItemName = "selectedCities[]";
        }else{
            alert("Adding items is Implemented for: " + el);
            return;
        }

        var sourceSelection = document.getElementById(sourceElementId);
        var selecteItemsTosearch = sourceSelection.selectedOptions;
        var addedItemsToSearch = document.getElementById(destinationElementId);

        for (var i = 0; i < selecteItemsTosearch.length; i++) {
            var item = selecteItemsTosearch[i].value;

            var isItemsAlreadyAdded = false;
            var existingItems = addedItemsToSearch.getElementsByTagName("li");
            for (var j = 0; j < existingItems.length; j++) {
                var existingItem= existingItems[j].getElementsByTagName("label")[0].textContent;
                if (existingItem === item) {
                    isItemsAlreadyAdded = true;
                    break;
                }
            }

            if (!isItemsAlreadyAdded) {
                var listItem = document.createElement("li");
                listItem.className = "list-group-item";

                var checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.className = "form-check-input me-1";
                checkbox.value = item;
                checkbox.name = addedItemName;
                checkbox.checked = true;

                var label = document.createElement("label");
                label.className = "form-check-label";
                label.textContent = item;

                listItem.appendChild(checkbox);
                listItem.appendChild(label);

                addedItemsToSearch.appendChild(listItem);
            }
        }
    }
    

</script>
</body>
</html>