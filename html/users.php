<?php
    ob_start();
    session_start();
    require_once 'api_client.php';

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    // Handle Add User POST request
    if (isset($_POST['ajax_add_user'])) {
        ob_clean(); // Clear any previous output
        header('Content-Type: application/json');
        
        $email = $_POST['email'];
        $upass = $_POST['pass'];
        $firstName = $_POST['first_name'];
        $sirName = $_POST['sir_name'];
        $title = $_POST['title'];
        $country = $_POST['country'];
        $city = $_POST['city'];
        $isAdmin = isset($_POST['is_admin']) ? true : false;
        $password = hash('sha256', $upass);

        $payload = [
            "email" => $email,
            "password" => $password,
            "first_name" => $firstName,
            "sir_name" => $sirName,
            "title" => $title,
            "country" => $country,
            "city" => $city,
            "is_admin" => $isAdmin
        ];

        $json_payload = json_encode($payload);
        $api_result = CallAPI("POST", "/users", $json_payload);
        $response = json_decode($api_result);

        $result = ["status" => "error", "message" => "Unable to connect to the user service. Please try again later."];
        if (!is_null($response)) {
            if (property_exists($response, "message")) {
                $result = ["status" => "error", "message" => $response->message];
            } else {
                $result = ["status" => "success", "message" => "User added successfully."];
            }
        }
        echo json_encode($result);
        exit;
    }

    if (isset($_POST['get_users_table'])) {
        ob_clean(); // Clear any previous output
        header('Content-Type: application/json');
        
        // Initialize logged in user for the AJAX request
        $rest_response = CallAPI("GET", "/users/".$_SESSION['user']);
        $loged_in_user = json_decode($rest_response);
        
        $url_params = [
            "order_by" => isset($_POST['column']) ? $_POST['column'] : 'title',
            "orientation" => isset($_POST['order']) ? $_POST['order'] : 'ASC'
        ];

        $rest_response = CallAPI("GET", "/users", $url_params);
        $users = json_decode($rest_response);
        
        $html = '';
        foreach ($users as $user) {
            $html .= "<tr>";
            $html .= "<td>{$user->title}</td>";
            $html .= "<td>{$user->first_name}</td>";
            $html .= "<td>{$user->sir_name}</td>";
            $html .= "<td>{$user->country}</td>";
            $html .= "<td>{$user->city}</td>";
            $html .= "<td>{$user->email}</td>";
            if ($loged_in_user->is_admin) {
                $html .= "<td><a href='delete_user.php?id={$user->id}' onclick=\"return confirm('Are you SURE you want to delete {$user->first_name} {$user->sir_name}? ')\">delete</a></td>";
            }
            $html .= "</tr>";
        }
        
        echo json_encode(["html" => $html]);
        exit;
    }

    // select logged in users detail
    $rest_response = CallAPI("GET", "/users/".$_SESSION['user']);
    $loged_in_user = json_decode($rest_response);
    // Table sorting mechanism

    // For extra protection these are the columns of which the user can sort by (in your database table).
    $columns = array('title', 'first_name', 'sir_name', 'country', 'city', 'email');
    
    // Only get the column if it exists in the above columns array, if it doesn't exist the database table will be sorted by the first item in the columns array.
    $column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];

    // Get the sort order for the column, ascending or descending, default is ascending.
    $sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

    // Get the result...
    $url_params = [
        "order_by" => $column,
        "orientation" => $sort_order
    ];

    if  ($rest_response = CallAPI("GET","/users", $url_params)) {
        // Some variables we need for the table.
        $available_users = json_decode($rest_response);
        $up_or_down = str_replace(array('ASC','DESC'), array('by-attributes','by-attributes-alt'), $sort_order); 
        $asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
        $add_class = ' class="highlight"';
    
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users - Test Automation Training</title>
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
                        <a class="nav-link active" href="users.php">Users</a>
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
            <div class="col-md-8 offset-md-2">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Available Users</h2>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus"></i> Add User
                        </button>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table id="users_list" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="users.php?column=title&order=<?php echo $asc_or_desc; ?>" class="text-decoration-none text-dark">
                                        Title
                                        <i class="bi bi-sort-<?php echo $column == 'title' ? ($up_or_down == 'asc' ? 'up' : 'down') : 'alpha-down'; ?>"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="users.php?column=first_name&order=<?php echo $asc_or_desc; ?>" class="text-decoration-none text-dark">
                                        First Name
                                        <i class="bi bi-sort-<?php echo $column == 'first_name' ? ($up_or_down == 'asc' ? 'up' : 'down') : 'alpha-down'; ?>"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="users.php?column=sir_name&order=<?php echo $asc_or_desc; ?>" class="text-decoration-none text-dark">
                                        Surname
                                        <i class="bi bi-sort-<?php echo $column == 'sir_name' ? ($up_or_down == 'asc' ? 'up' : 'down') : 'alpha-down'; ?>"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="users.php?column=country&order=<?php echo $asc_or_desc; ?>" class="text-decoration-none text-dark">
                                        Country
                                        <i class="bi bi-sort-<?php echo $column == 'country' ? ($up_or_down == 'asc' ? 'up' : 'down') : 'alpha-down'; ?>"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="users.php?column=city&order=<?php echo $asc_or_desc; ?>" class="text-decoration-none text-dark">
                                        City
                                        <i class="bi bi-sort-<?php echo $column == 'city' ? ($up_or_down == 'asc' ? 'up' : 'down') : 'alpha-down'; ?>"></i>
                                    </a>
                                </th>
                                <th>
                                    <a href="users.php?column=email&order=<?php echo $asc_or_desc; ?>" class="text-decoration-none text-dark">
                                        Email
                                        <i class="bi bi-sort-<?php echo $column == 'email' ? ($up_or_down == 'asc' ? 'up' : 'down') : 'alpha-down'; ?>"></i>
                                    </a>
                                </th>
                                <?php if ($loged_in_user->is_admin): ?>
                                    <th>Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($available_users as &$user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user->title); ?></td>
                                    <td><?php echo htmlspecialchars($user->first_name); ?></td>
                                    <td><?php echo htmlspecialchars($user->sir_name); ?></td>
                                    <td><?php echo htmlspecialchars($user->country); ?></td>
                                    <td><?php echo htmlspecialchars($user->city); ?></td>
                                    <td><?php echo htmlspecialchars($user->email); ?></td>
                                    <?php if ($loged_in_user->is_admin): ?>
                                        <td>
                                            <a href="delete_user.php?id=<?php echo $user->id; ?>" 
                                               class="text-danger"
                                               onclick="return confirm('Are you SURE you want to delete <?php echo htmlspecialchars($user->first_name . ' ' . $user->sir_name); ?>?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addUserForm" method="post" autocomplete="off" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <div id="formAlert" class="alert d-none"></div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <select name="title" id="title" class="form-select" required>
                                <option value="">Select Title</option>
                                <option value="Mr.">Mr.</option>
                                <option value="Mrs.">Mrs.</option>
                            </select>
                            <div class="invalid-feedback">Please select a title.</div>
                        </div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
                            <div class="invalid-feedback">Please enter a first name.</div>
                        </div>

                        <div class="mb-3">
                            <label for="sir_name" class="form-label">Surname</label>
                            <input type="text" name="sir_name" id="sir_name" class="form-control" required>
                            <div class="invalid-feedback">Please enter a surname.</div>
                        </div>

                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select name="country" id="country" class="form-select" required>
                                <option value="">Select Country</option>
                                <option value="Bulgaria">Bulgaria</option>
                                <option value="Romania">Romania</option>
                                <option value="Greece">Greece</option>
                                <option value="Germany">Germany</option>
                                <option value="UK">UK</option>
                                <option value="USA">ASU</option>
                            </select>
                            <div class="invalid-feedback">Please select a country.</div>
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" id="city" class="form-control" required>
                            <div class="invalid-feedback">Please enter a city.</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <div class="mb-3">
                            <label for="pass" class="form-label">Password</label>
                            <input type="password" name="pass" id="pass" class="form-control" required>
                            <div class="invalid-feedback">Please enter a password.</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_admin" id="is_admin" class="form-check-input">
                            <label class="form-check-label" for="is_admin">Is Admin User</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Handle form submission
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            if (!this.checkValidity()) {
                return;
            }
            
            $.ajax({
                url: 'users.php',
                type: 'POST',
                data: $(this).serialize() + '&ajax_add_user=1',
                dataType: 'json',
                success: function(response) {
                    const alertDiv = $('#formAlert');
                    alertDiv.removeClass('alert-success alert-danger d-none')
                           .addClass(response.status === 'success' ? 'alert-success' : 'alert-danger')
                           .html(response.message)
                           .show();
                    
                    if (response.status === 'success') {
                        $('#addUserForm').removeClass('was-validated')[0].reset();
                        refreshUsersTable();
                        
                        setTimeout(function() {
                            alertDiv.addClass('d-none');
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                            modal.hide();
                        }, 2000);
                    }
                },
                error: function() {
                    $('#formAlert')
                        .removeClass('alert-success d-none')
                        .addClass('alert-danger')
                        .html('An error occurred. Please try again.')
                        .show();
                }
            });
        });
        
        function refreshUsersTable() {
            $.ajax({
                url: 'users.php',
                type: 'POST',
                data: {
                    get_users_table: 1,
                    column: '<?php echo $column; ?>',
                    order: '<?php echo $sort_order; ?>'
                },
                dataType: 'json',
                success: function(response) {
                    $('#users_list tbody').html(response.html);
                }
            });
        }
    });
    </script>
</body>
</html>