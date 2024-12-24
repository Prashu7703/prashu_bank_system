<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'BankingSystem');

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle withdrawal logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountNumber = $_POST['account_number'];
    $amount = floatval($_POST['amount']);

    if (empty($accountNumber) || empty($amount)) {
        $message = "Please fill in all fields.";
    } elseif ($amount <= 0) {
        $message = "Invalid amount. Please enter a valid positive number.";
    } else {
        $stmt = $conn->prepare("SELECT Balance FROM Accounts WHERE AccountNumber = ?");
        $stmt->bind_param('s', $accountNumber);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $userBalance = $result->fetch_assoc()['Balance'];
            if ($userBalance >= $amount) {
                $updateStmt = $conn->prepare("UPDATE Accounts SET Balance = Balance - ? WHERE AccountNumber = ?");
                $updateStmt->bind_param('ds', $amount, $accountNumber);

                if ($updateStmt->execute()) {
                    $transactionStmt = $conn->prepare("INSERT INTO Transactions (AccountNumber, TransactionType, Amount) VALUES (?, 'Withdrawal', ?)");
                    $transactionStmt->bind_param('sd', $accountNumber, $amount);
                    $transactionStmt->execute();
                    $message = "Withdrawal successful! ₹" . number_format($amount, 2) . " has been deducted.";
                    $transactionStmt->close();
                } else {
                    $message = "Error processing withdrawal.";
                }
                $updateStmt->close();
            } else {
                $message = "Insufficient funds.";
            }
        } else {
            $message = "Account does not exist.";
        }
        $stmt->close();
    }
}

// Fetch account data to show in the table
$accounts = $conn->query("SELECT * FROM Accounts");

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Money</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Page Background */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #f0f4f8, #d9e2ec);
            color: #333;
        }

        /* Container for the entire UI */
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Header with "Back to Dashboard" */
        .back-to-dashboard {
            margin-bottom: 15px;
            text-align: right;
        }

        .back-to-dashboard a {
            text-decoration: none;
            color: white;
            background: #007bff;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        .back-to-dashboard a:hover {
            background: #0056b3;
        }

        /* Title Styling */
        h2 {
            text-align: center;
            margin: 10px 0;
        }

        /* Form Styles */
        form {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input {
            padding: 10px;
            width: 70%;
            margin: 10px 0;
            border: 1px solid #aaa;
            border-radius: 4px;
        }

        button {
            margin: 10px 0;
            padding: 10px 20px;
            color: white;
            background: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        button:hover {
            background: #0056b3;
        }

        /* Table for displaying user accounts */
        table {
            margin: 20px 0;
            border-collapse: collapse;
            width: 100%;
        }

        table th, table td {
            padding: 8px;
            border: 1px solid #aaa;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back to Dashboard Link -->
        <div class="back-to-dashboard">
            <a href="dashboard.php">Back to Dashboard</a>
        </div>

        <h2>Withdraw Money</h2>

        <!-- Withdrawal Form -->
        <form method="POST" action="">
            <label>Account Number:</label>
            <input type="text" name="account_number" required>
            <label>Amount to Withdraw:</label>
            <input type="number" step="0.01" name="amount" required>
            <button type="submit">Withdraw</button>
        </form>

        <!-- Display transaction messages -->
        <?php if (isset($message)): ?>
            <h4 style="color: green; text-align: center;"><?php echo $message; ?></h4>
        <?php endif; ?>

        <!-- Account Table -->
        <h3>Account Details</h3>
        <table>
            <tr>
                <th>Account Number</th>
                <th>Name</th>
                <th>Aadhaar Number</th>
                <th>Phone Number</th>
                <th>Balance</th>
            </tr>
            <?php
            if ($accounts && $accounts->num_rows > 0) {
                while ($row = $accounts->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['AccountNumber']}</td>";
                    echo "<td>{$row['Name']}</td>";
                    echo "<td>{$row['Aadhaar']}</td>";
                    echo "<td>{$row['Phone']}</td>";
                    echo "<td>₹" . number_format($row['Balance'], 2) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No account data available.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
