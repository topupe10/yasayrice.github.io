<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a SQL statement to check user credentials
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $connect->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Authentication successful
            $role = $user['role'];
        
            // Set the session variables for user authentication and role
            session_start(); // Start the session (if not already started)
            $_SESSION['user_authenticated'] = true;
            $_SESSION['role'] = $role;
        
            if ($role === 'admin') {
                header("Location: dashboard.php"); // Redirect admin to the admin dashboard
            } elseif ($role === 'stockman') {
                header("Location: stockman_dashboard.php"); // Redirect stockman to the stockman dashboard
            } else {
                // Handle other roles or invalid roles
                echo "Invalid user role";
            }
            exit; // Important: Terminate the script after redirection
        } else {
            // Authentication failed
            echo "<script>document.getElementById('loginError').innerHTML = 'Invalid username or password';</script>";
        }

        $stmt->close();
    } else {
        echo "Error: " . $connect->error;
    }

    // Close the database connection
    $connect->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Yasay Rice & Corn Milling Management Information System</title>
    <link rel="stylesheet" type="text/css" href="stylesheet/loginstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha384-..." crossorigin="anonymous">
</head>
<body>
    <div class="login-container">
        <div class="company-logo">
            <img src="logo.png" alt="Company Logo">
        </div>
        <br>
        <p>Company Staff Login</p>
        <hr>
        <br>
        <form method="post" action="login.php">
            <div class="input-container">
                <input type="text" id="username" name="username" required autocomplete="username" style="background: none; outline: none; background-color: none;">
                <label for="username"><i class="fas fa-user"></i> Username</label>
            </div>
            <br>
            <div class="input-container">
                <input type="password" id="password" name="password" required autocomplete="current-password" style="background: none; outline: none; background-color: none;">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
            </div>
            <br>
            <input type="submit" value="LOGIN">
            <br>
            <br>
            <div class="alert loginError"></div>
        </form>
    </div>
    <script>
<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Check if the form was submitted and authentication failed
    echo "document.addEventListener('DOMContentLoaded', function () {
        var loginError = document.querySelector('.alert.loginError');
        loginError.style.display = 'block';
        loginError.innerHTML = 'Invalid username or password. Please try again.';
    });";
}
?>
</script>
</body>
</html>
