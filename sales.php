<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$where_clause = "WHERE 1";
if (!empty($product_name)) {
    $product_name = mysqli_real_escape_string($conn, $product_name);
    $where_clause .= " AND p.name LIKE '%$product_name%'";
}

if (!empty($start_date) && !empty($end_date)) {
    $where_clause .= " AND s.date BETWEEN '$start_date' AND '$end_date'";
}

$salesQuery = "SELECT p.name AS product_name, s.qty, s.price, s.date
               FROM sales s
               JOIN products p ON s.product_id = p.id
               $where_clause
               ORDER BY s.date DESC";

$salesResult = $conn->query($salesQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="css/admin_style.css">
    <style>
      main {
         margin-left: 250px;
   
         margin-right: 0px;
         padding: 20px;
         width: calc(100% - 250px);
         background-color: #f9f9f9;
         min-height: 100vh;
      }
      body {
         font-family: Arial, sans-serif;
         background-color: #f9f9f9;
      } html,
      body {
         margin: 0;
         padding: 0;
         height: 100%;
         overflow: hidden;
      }

      body {
         font-family: 'Roboto', sans-serif;
         background-color: #f9fafc;
         color: #333;
         padding: 20px;
         display: flex;
         flex-direction: column;
      }

      main {
         flex: 1 1 auto;
         overflow-y: auto;
         margin-top: 80px;
         margin-left: 250px;
         padding: 20px;
         width: calc(100% - 250px);
         background-color: #fff;
         box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
         border-radius: 8px;
      }
      .orders .heading {
         font-size: 24px;
         margin-bottom: 20px;
         text-align: center;
         color: #333;
      }

      .table-container {
         background-color: #fff;
         padding: 15px;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
         border-radius: 8px;
         overflow-x: auto;
         margin-bottom: 60px;
      }

      .table-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 15px;
      }

      .table-title {
         font-size: 18px;
         color: #333;
         font-weight: bold;
      }

   
      table.custom-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 10px;
      
      }

      table.custom-table th,
      table.custom-table td {
         padding: 12px;
         text-align: left;
         border-bottom: 1px solid #ddd;
      }

      table.custom-table th {
         background-color: #4CAF50;
         color: white;
      }

      table.custom-table tr:nth-child(even) {
         background-color: #f2f2f2;
      }

      table.custom-table tr:hover {
         background-color: #ddd;
      }

      table.custom-table td.price-cell {
         color: #4CAF50;
         font-weight: bold;
      }

      .select {
         padding: 5px;
         border-radius: 5px;
         border: 1px solid #ccc;
      }

      .actions-cell {
         display: flex;
         justify-content: space-between;
      }



      .alert-info {
         background-color: #d9edf7;
         color: #31708f;
         padding: 10px;
         border-radius: 5px;
         margin-top: 15px;
         text-align: center;
      }
      .filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    background-color: #f9f9f9;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    flex: 1 1 calc(33.333% - 1rem);
    min-width: 200px;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #333;
}

.form-group input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
}

.filter-btn {
    background-color: #007bff;
    color: white;
    font-size: 1rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    align-self: flex-start;
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn:hover {
    background-color: #0056b3;
}

.filter-btn i {
    font-size: 1.2rem;
}

   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>
<main>
<section class="sales-report">

<h1 class="heading">Sales Summary Report</h1>

<form method="POST" action="" class="filter-form">
    <div class="form-group">
        <label for="product_name">Product Name</label>
        <input type="text" id="product_name" name="product_name" placeholder="Enter Product Name" value="<?php echo htmlspecialchars($product_name); ?>" />
    </div>

    <div class="form-group">
        <label for="start_date">Start Date</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" />
    </div>

    <div class="form-group">
        <label for="end_date">End Date</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" />
    </div>

    <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Apply Filters</button>
</form>


<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Sales Report</h2>
    </div>

    <?php
    if ($salesResult && mysqli_num_rows($salesResult) > 0) {
        echo "<table class='custom-table'>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";

        while ($sale = mysqli_fetch_assoc($salesResult)) {
            echo "<tr>
                <td>" . htmlspecialchars($sale['product_name']) . "</td>
                <td>" . htmlspecialchars($sale['qty']) . "</td>
                <td class='price-cell'>₱ " . number_format($sale['price'], 2) . "</td>
                <td>" . htmlspecialchars($sale['date']) . "</td>
            </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<div class='alert-info'>No sales data available!</div>";
    }
    ?>
</div>

<script src="js/admin_script.js"></script>
</main>
</body>
</html>
