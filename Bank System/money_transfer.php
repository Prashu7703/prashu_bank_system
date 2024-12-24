<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'BankingSystem');

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for money transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourceAccount = $_POST['source_account'];
    $destinationAccount = $_POST['destination_account'];
    $amount = floatval($_POST['amount']);

    // Validate input
    if (empty($sourceAccount) || empty($destinationAccount) || empty($amount)) {
        $message = "Please provide all the required fields.";
    } elseif ($amount <= 0) {
        $message = "Invalid amount. Please enter a positive value.";
    } elseif ($sourceAccount === $destinationAccount) {
        $message = "Source and Destination accounts cannot be the same.";
    } else {
        // Check if the source account exists
        $stmt = $conn->prepare("SELECT Balance FROM Accounts WHERE AccountNumber = ?");
        $stmt->bind_param('s', $sourceAccount);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $sourceData = $result->fetch_assoc();
            $currentBalance = $sourceData['Balance'];

            if ($currentBalance >= $amount) {
                // Begin transaction
                $conn->begin_transaction();

                try {
                    // Deduct amount from source account
                    $deductStmt = $conn->prepare("UPDATE Accounts SET Balance = Balance - ? WHERE AccountNumber = ?");
                    $deductStmt->bind_param('ds', $amount, $sourceAccount);
                    $deductStmt->execute();

                    // Add amount to destination account
                    $addStmt = $conn->prepare("UPDATE Accounts SET Balance = Balance + ? WHERE AccountNumber = ?");
                    $addStmt->bind_param('ds', $amount, $destinationAccount);
                    $addStmt->execute();

                    // Commit transaction
                    $conn->commit();
                    $message = "Money transfer successful! ₹" . number_format($amount, 2) . " has been transferred.";
                } catch (Exception $e) {
                    // Rollback transaction in case of any error
                    $conn->rollback();
                    $message = "Transaction failed. Please try again.";
                }

                $deductStmt->close();
                $addStmt->close();
            } else {
                $message = "Insufficient funds in the source account.";
            }
        } else {
            $message = "Source account does not exist.";
        }

        $stmt->close();
    }
}

// Fetch all account data to display in a table
$accounts = $conn->query("SELECT * FROM Accounts");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Money</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Background */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #e6e6fa, #f4f4f4);
            color: #333;
        }

        /* Centered Content */
        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Back to Dashboard Button */
        .back-to-dashboard {
            margin-bottom: 10px;
            text-align: right;
        }

        .back-to-dashboard a {
            text-decoration: none;
            color: white;
            background: #4682b4;
            padding: 10px 15px;
            border-radius: 4px;
        }

        .back-to-dashboard a:hover {
            background: #4169e1;
        }

        /* Form Styling */
        h2 {
            text-align: center;
            margin: 10px 0;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
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
            margin: 20px 0;
            padding: 10px;
            background: #4682b4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #4169e1;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #4682b4;
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

        <h2>Money Transfer - Add Money</h2>

        <!-- Form for Money Transfer -->
        <form method="POST" action="">
            <label for="source_account">Source Account Number:</label>
            <input type="text" id="source_account" name="source_account" required>

            <label for="destination_account">Destination Account Number:</label>
            <input type="text" id="destination_account" name="destination_account" required>

            <label for="amount">Amount to Transfer:</label>
            <input type="number" id="amount" name="amount" step="0.01" required>

            <button type="submit">Transfer Money</button>
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
                echo "<tr><td colspan='5'>No account data found.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
