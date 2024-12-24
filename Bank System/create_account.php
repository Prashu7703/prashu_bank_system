<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'BankingSystem');

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a unique 10-digit account number
function generateAccountNumber($conn) {
    do {
        $accountNumber = substr(str_shuffle("0123456789"), 0, 10);
        $stmt = $conn->prepare("SELECT * FROM Accounts WHERE AccountNumber = ?");
        $stmt->bind_param('s', $accountNumber);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0); // Ensure uniqueness
    return $accountNumber;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $aadhaar = $_POST['aadhaar'];
    $phone = $_POST['phone'];

    // Validate inputs
    if (strlen($aadhaar) != 12 || !ctype_digit($aadhaar)) {
        die("Invalid Aadhaar number. It must be a 12-digit number.");
    }
    if (strlen($phone) != 10 || !ctype_digit($phone)) {
        die("Invalid phone number. It must be a 10-digit number.");
    }

    // Generate unique account number
    $accountNumber = generateAccountNumber($conn);

    // Insert user details into the database
    $stmt = $conn->prepare("INSERT INTO Accounts (Name, Aadhaar, Phone, AccountNumber, Balance) VALUES (?, ?, ?, ?, 0.00)");
    $stmt->bind_param('ssss', $name, $aadhaar, $phone, $accountNumber);

    if ($stmt->execute()) {
        echo "Account successfully created!<br>";
        echo "Your Account Number is: $accountNumber<br>";
    } else {
        echo "Error creating account: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bank Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
        }
        input {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .back-to-dashboard {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .back-to-dashboard a {
            text-decoration: none;
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
        }
        .back-to-dashboard a:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="back-to-dashboard">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
    <div class="container">
        <h2>Create Bank Account</h2>
        <form method="POST" action="">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="aadhaar">Aadhaar Number (12 digits):</label>
            <input type="text" id="aadhaar" name="aadhaar" maxlength="12" required>

            <label for="phone">Phone Number (10 digits):</label>
            <input type="text" id="phone" name="phone" maxlength="10" required>

            <button type="submit">Create Account</button>
        </form>
    </div>
</body>
</html>
