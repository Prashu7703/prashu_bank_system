<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'BankingSystem');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO Users (Username, Password, FullName, Email) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $username, $password, $fullname, $email);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Banking System</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        /* Sign-Up Form Container */
        .signup-container {
            background: white;
            color: #333;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            transition: transform 0.2s ease;
        }

        /* Form Animation */
        .signup-container:hover {
            transform: translateY(-5px);
        }

        /* Heading */
        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8em;
        }

        h2 {
            margin-bottom: 15px;
            font-size: 1.1em;
            color: #555;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
            text-align: left;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #aaa;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            border-color: #6a11cb;
            outline: none;
        }

        /* Button Styling */
        button {
            padding: 10px;
            background-color: #6a11cb;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2575fc;
        }

        /* Error Message */
        .error-msg {
            color: red;
            margin-bottom: 10px;
            font-size: 0.9em;
        }

        /* Link to Login */
        p.login-link {
            margin-top: 15px;
            font-size: 0.9em;
            color: #555;
        }

        p.login-link a {
            text-decoration: none;
            color: #6a11cb;
            font-weight: bold;
            transition: text-decoration 0.2s ease;
        }

        p.login-link a:hover {
            text-decoration: underline;
        }

        /* Media Query for Responsiveness */
        @media (max-width: 480px) {
            .signup-container {
                padding: 20px;
                max-width: 90%;
            }

        }
    </style>
</head>

<body>
    <div class="signup-container">
        <h2>Create Your Account</h2>
        <?php if (isset($error)) echo "<p class='error-msg'>$error</p>"; ?>
        <form method="POST" action="">
            <label for="fullname">Full Name</label>
            <input type="text" name="fullname" id="fullname" required>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>
