<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking System Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px;
            height: 15%;
            position: relative;
        }
        .header a {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            color: white;
            text-decoration: none;
            background-color: #3498db;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .header a:hover {
            background-color: #2980b9;
        }
        .container {
            display: flex;
            flex: 1;
        }
        .left-menu {
            background-color: #34495e;
            color: white;
            width: 25%;
            padding: 20px;
        }
        .left-menu a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            background-color: #2c3e50;
            text-align: center;
            border-radius: 5px;
        }
        .left-menu a:hover {
            background-color: #1abc9c;
        }
        .right-content {
            background-color: #ecf0f1;
            color: #2c3e50;
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Banking System Dashboard</h1>
        <a href="logout.php">logout</a>
    </div>
    <div class="container">
        <div class="left-menu">
            <h2>Menu</h2>
            <a href="create_account.php">Create Account</a>
            <a href="deposit.php">Deposit Money</a>
            <a href="withdraw.php">Withdraw Money</a>
            <a href="money_transfer.php">Money Transfer</a>
            <a href="transaction_history.php">Transaction History</a>
            
            
        </div>
        <div class="right-content">
            <h2>Welcome, User!</h2>
            <p>Here you can manage your banking activities conveniently.</p>
        </div>
    </div>
</body>
</html>
