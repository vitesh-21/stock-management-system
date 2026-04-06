<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['role'])) {
    header("Location: login.html");
    exit();
}

// Security: If a Staff member tries to access this Admin Dashboard
if ($_SESSION['role'] === 'Staff') {
    header("Location: sales.php");
    exit();
}

include('db.php');


// ===============================
// FETCH DASHBOARD STATISTICS
// ===============================

// Total Products
$total_products = 0;
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
if($query_total){
    $total_products = mysqli_fetch_assoc($query_total)['total'] ?? 0;
}

// Low Stock
$low_stock = 0;
$query_low = mysqli_query($conn, "SELECT COUNT(*) as low FROM products WHERE quantity < 10");
if($query_low){
    $low_stock = mysqli_fetch_assoc($query_low)['low'] ?? 0;
}

// Default Values
$total_revenue = 0;
$sales_history_query = false;
$recent_activity_query = false;


// ===============================
// CHECK IF SALES TABLE EXISTS
// ===============================

$val_table = mysqli_query($conn, "SHOW TABLES LIKE 'sales'");

if($val_table && mysqli_num_rows($val_table) > 0){

    // Total Revenue
    $query_revenue = mysqli_query($conn, "SELECT SUM(total_amount) as revenue FROM sales");
    if($query_revenue){
        $res_rev = mysqli_fetch_assoc($query_revenue);
        $total_revenue = $res_rev['revenue'] ?? 0;
    }

    // Sales History
    $sales_history_query = mysqli_query($conn,
        "SELECT sales.*, products.name as p_name
         FROM sales
         LEFT JOIN products ON sales.product_id = products.id
         ORDER BY sales.sale_date DESC"
    );

    // Recent Activity
    $recent_activity_query = mysqli_query($conn,
        "SELECT sales.*, products.name as p_name
         FROM sales
         LEFT JOIN products ON sales.product_id = products.id
         ORDER BY sales.sale_date DESC
         LIMIT 5"
    );
}


// ===============================
// FETCH PRODUCTS
// ===============================

$products_query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Stock Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-dark: #2c3e50;
            --sidebar-width: 70px;
            --bg-light: #f4f7f6;
            --accent-blue: #3498db;
            --success-green: #27ae60;
            --warning-orange: #f39c12;
            --danger-red: #e74c3c;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .side-bar {
            width: var(--sidebar-width);
            background: var(--primary-dark);
            height: 100vh;
            position: fixed;
            display: flex;
            flex-direction: column;
            color: white;
            z-index: 100;
        }
        .side-item {
            padding: 20px;
            text-align: center;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: 0.3s;
            cursor: pointer;
        }
        .side-item:hover { color: white; background: rgba(255,255,255,0.1); }

        /* MAIN CONTENT */
        .dashboard-container {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 20px;
        }

        .main-header {
            background: var(--primary-dark);
            color: white;
            padding: 25px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* NAVIGATION */
        .nav-bar {
            background: #fff;
            display: flex;
            justify-content: center;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .nav-item {
            padding: 10px 20px;
            margin: 0 5px;
            text-decoration: none;
            color: #555;
            border-radius: 5px;
            font-weight: 500;
            transition: 0.2s;
        }
        .nav-item:hover { background: #eee; }
        .nav-item.active { background: var(--accent-blue); color: white; }

        /* CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }
        .card h3 { font-size: 0.85rem; color: #7f8c8d; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;}
        .card .value { font-size: 1.8rem; font-weight: bold; color: #2c3e50; }
        .card .icon {
            position: absolute; right: 15px; bottom: 15px; font-size: 2.5rem; opacity: 0.1;
        }

        /* REPORT SPECIFIC COLORS (Restored) */
        .report-card { border-top: 5px solid transparent; text-align: center; }
        .report-card.cash { background: #ebf8ff; border-top-color: var(--accent-blue); }
        .report-card.mpesa { background: #f0fff4; border-top-color: var(--success-green); }
        .report-card.card-pay { background: #fffaf0; border-top-color: var(--warning-orange); }

        /* STATUS BADGES */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; }
        .badge-success { background: #eafaf1; color: #27ae60; }
        .badge-warning { background: #fff3e0; color: #f39c12; }
        .badge-danger { background: #fdeaea; color: #e74c3c; }

        /* TABLES */
        .inventory-table {
            width: 100%; background: white; border-collapse: collapse; border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 20px; overflow: hidden;
        }
        .inventory-table thead tr { background-color: #34495e; color: white; }
        .inventory-table th { padding: 15px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
        .inventory-table td { padding: 15px; border-top: 1px solid #eee; vertical-align: middle; }
        .inventory-table tr:hover { background-color: #fcfcfc; }

        .activity-section { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .activity-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid #f9f9f9; }
        .activity-icon { width: 35px; height: 35px; background: #e8f4fd; color: var(--accent-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 0.9rem; }

        /* MODALS */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(3px); }
        .modal-content { background: white; width: 450px; margin: 50px auto; padding: 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        input, select { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; }
        
        .add-btn { background: var(--success-green); color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .add-btn:hover { background: #219150; transform: translateY(-1px); }

        .alert-toast { position: fixed; top: 20px; right: 20px; background: var(--success-green); color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 1001; display: none; }
    </style>
</head>
<body>

    <?php if(isset($_GET['msg'])): ?>
<div id="toast" class="alert-toast" style="display:block;">
    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
</div>
<script>
setTimeout(() => { document.getElementById('toast').style.display='none'; }, 3000);
</script>
<?php endif; ?>

<!-- LOW STOCK NOTIFICATION -->
<?php if($low_stock > 0): ?>
<div id="lowStockAlert" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #f39c12;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    z-index: 2000;
    font-weight: 500;
">
    <i class="fas fa-exclamation-triangle"></i>
    Warning: <?php echo $low_stock; ?> product(s) are low in stock!
</div>

<script>
var audio = new Audio("https://www.soundjay.com/buttons/sounds/beep-07.mp3");
audio.play();

setTimeout(function(){
    document.getElementById("lowStockAlert").style.display="none";
},6000);
</script>
<?php endif; ?>
    <aside class="side-bar">
        <div class="sidebar-top">
            <i class="fas fa-cubes" style="font-size: 2rem; padding: 20px; color: var(--accent-blue);"></i>
        </div>
        
        <div class="sidebar-links" style="flex: 1; display: flex; flex-direction: column;">
            <a class="side-item" title="Dashboard" onclick="showTab('home', this)"><i class="fas fa-th-large"></i></a>
            <a class="side-item" title="Inventory" onclick="showTab('products', this)"><i class="fas fa-box"></i></a>
            <a class="side-item" title="Reports" onclick="showTab('reports', this)"><i class="fas fa-chart-line"></i></a>
            
            <hr style="width: 40%; align-self: center; border: 0.5px solid rgba(255,255,255,0.1); margin: 10px 0;">
            
            <a class="side-item" title="Settings" onclick="showTab('settings', this)"><i class="fas fa-cog"></i></a>
            <a class="side-item" title="Help" onclick="showTab('help', this)"><i class="fas fa-question-circle"></i></a>
        </div>

        <div class="sidebar-bottom" style="display: flex; flex-direction: column;">
            <a class="side-item" title="Profile" onclick="showTab('profile', this)" style="color: var(--success-green);"><i class="fas fa-user-circle"></i></a>
            <a class="side-item" title="Logout" href="logout.php" style="color: var(--danger-red);"><i class="fas fa-power-off"></i></a>
        </div>
    </aside>

    <div class="dashboard-container">
       <header class="main-header">
    <h1>STOCK MANAGEMENT SYSTEM</h1>
    <p style="font-size: 0.9rem; margin-top: 5px; opacity: 0.8;">
        <i class="fas fa-user-shield"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (Admin)
    </p>
</header>
        <nav class="nav-bar">
            <a href="javascript:void(0)" class="nav-item active" onclick="showTab('home', this)"><i class="fas fa-home"></i> Home</a>
            <a href="javascript:void(0)" class="nav-item" onclick="showTab('products', this)"><i class="fas fa-box"></i> Products</a>
            <a href="javascript:void(0)" class="nav-item" onclick="showTab('sales', this)"><i class="fas fa-clock-rotate-left"></i> Sales History</a>
            <a href="javascript:void(0)" class="nav-item" onclick="showTab('reports', this)"><i class="fas fa-chart-pie"></i> Reports</a>
            <a href="logout.php" class="nav-item" style="color: var(--danger-red); border-left: 1px solid #eee; margin-left: 15px;" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>

        <div id="home" class="tab-content">
            <section class="stats-grid">
                <div class="card" style="border-top: 4px solid var(--accent-blue);">
                    <h3>Total Products</h3>
                    <div class="value"><?php echo $total_products; ?></div>
                    <i class="fas fa-boxes-stacked icon"></i>
                </div>
                <div class="card" style="border-top: 4px solid var(--warning-orange);">
                    <h3>Low Stock Alert</h3>
                    <div class="value" style="color: <?php echo $low_stock > 0 ? '#e67e22' : '#2c3e50'; ?>">
                        <?php echo $low_stock; ?>
                    </div>
                    <i class="fas fa-exclamation-triangle icon"></i>
                </div>
                <div class="card" style="border-top: 4px solid var(--success-green);">
                    <h3>Total Revenue</h3>
                    <div class="value">Ksh <?php echo number_format($total_revenue); ?></div>
                    <i class="fas fa-money-bill-trend-up icon"></i>
                </div>
            </section>
            
            <div class="activity-section">
                <h2 style="margin-bottom: 20px; color: var(--primary-dark); font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 10px;">Recent Activity</h2>
                <div class="activity-list">
                    <?php if($recent_activity_query && mysqli_num_rows($recent_activity_query) > 0): ?>
                        <?php while($act = mysqli_fetch_assoc($recent_activity_query)): ?>
                            <div class="activity-item">
                                <div class="activity-icon"><i class="fas fa-receipt"></i></div>
                                <div style="flex: 1;">
                                    <span style="font-weight: 600; font-size: 0.95rem;">New Sale:</span> 
                                    Sold <?php echo $act['quantity_sold']; ?> units of <strong><?php echo htmlspecialchars($act['p_name'] ?? 'Product'); ?></strong>.
                                    <br><small style="color:#7f8c8d;">Method: <?php echo $act['payment_method']; ?> | Total: Ksh <?php echo number_format($act['total_amount']); ?></small>
                                </div>
                                <div style="color: #95a5a6; font-size: 0.8rem;">
                                    <?php echo date('H:i', strtotime($act['sale_date'])); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #7f8c8d; text-align: center; padding: 20px;">No recent activity recorded.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        

        <div id="products" class="tab-content" style="display:none;">
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--primary-dark); font-weight: 600;">Product Inventory</h2>
                <button class="add-btn" onclick="openModal('productModal')"><i class="fas fa-plus"></i> Add Product</button>
            </div>
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Size</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($products_query && mysqli_num_rows($products_query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($products_query)): 
                            $stock = $row['quantity'];
                            if($stock <= 0) {
                                $status = '<span class="badge badge-danger">Out of Stock</span>';
                            } elseif($stock < 10) {
                                $status = '<span class="badge badge-warning">Low Stock</span>';
                            } else {
                                $status = '<span class="badge badge-success">In Stock</span>';
                            }
                        ?>
                        <tr>
                            <td style="color: #7f8c8d;">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></td>
                            <td><strong style="color: #2c3e50;"><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td style="text-transform: uppercase;"><?php echo htmlspecialchars($row['size'] ?? 'N/A'); ?></td>
                            <td style="font-weight: 600;"><?php echo $stock; ?></td>
                            <td style="font-weight: 600;">Ksh <?php echo number_format($row['price']); ?></td>
                            <td><?php echo $status; ?></td>
                            <td>
                                <button onclick="openSellModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', <?php echo $row['quantity']; ?>)" 
                                        style="color: var(--success-green); border:none; background:none; cursor:pointer; font-size:1.2rem; margin-right:10px;" title="Sell Item">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" style="color: var(--danger-red); font-size: 1.1rem;" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="sales" class="tab-content" style="display:none;">
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: var(--primary-dark);">Sales History</h2>
                <button class="add-btn" style="background: var(--accent-blue);" onclick="window.print()"><i class="fas fa-print"></i> Print Records</button>
            </div>
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Qty Sold</th>
                        <th>Payment Method</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($sales_history_query && mysqli_num_rows($sales_history_query) > 0): 
                        mysqli_data_seek($sales_history_query, 0); 
                        while($sale = mysqli_fetch_assoc($sales_history_query)): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($sale['p_name'] ?? 'Deleted Product'); ?></strong></td>
                            <td><?php echo $sale['quantity_sold']; ?></td>
                            <td><?php echo $sale['payment_method']; ?></td>
                            <td style="font-weight:bold; color:var(--success-green);">Ksh <?php echo number_format($sale['total_amount']); ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>

        <div id="reports" class="tab-content" style="display:none;">
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
                <h2 style="color: var(--primary-dark);">Financial Reports</h2>
                <button class="add-btn" style="background: var(--accent-blue);" onclick="window.print()">
                    <i class="fas fa-file-export"></i> Export Report
                </button>
            </div>

            <div class="stats-grid">
                
                <?php
                $pay_methods = ['Cash' => 'cash', 'M-Pesa' => 'mpesa', 'Card' => 'card-pay'];
                foreach($pay_methods as $label => $colorClass) {
                    $q = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM sales WHERE payment_method = '$label'");
                    $val = mysqli_fetch_assoc($q)['total'] ?? 0;
                    ?>
                    <div class="card report-card <?php echo $colorClass; ?>">
                        <span style="color: #4a5568; font-weight: 600; font-size: 0.9rem; text-transform: uppercase;"><?php echo $label; ?> Revenue</span>
                        <div class="value" style="margin-top:10px;">Ksh <?php echo number_format($val); ?></div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div class="activity-section" style="margin-top: 30px;">
                <h3 style="margin-bottom: 15px; color: var(--primary-dark);">Top Selling Products</h3>
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Units Sold</th>
                            <th>Total Earned</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $top_query = mysqli_query($conn, "SELECT products.name, SUM(sales.quantity_sold) as units, SUM(sales.total_amount) as revenue 
                                                        FROM sales JOIN products ON sales.product_id = products.id 
                                                        GROUP BY sales.product_id ORDER BY units DESC LIMIT 5");
                        while($top = mysqli_fetch_assoc($top_query)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($top['name']); ?></strong></td>
                            <td><?php echo $top['units']; ?></td>
                            <td style="color: var(--success-green); font-weight: bold;">Ksh <?php echo number_format($top['revenue']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="profile" class="tab-content" style="display:none;">
            <div class="activity-section">
                <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fas fa-user"></i> My Profile</h2>
                <p><strong>Role:</strong> Administrator</p>
                <p><strong>Status:</strong> Active</p>
                <button class="add-btn" style="margin-top: 20px; background: var(--accent-blue);">Edit Profile</button>
            </div>
        </div>

        <div id="settings" class="tab-content" style="display:none;">
            <div class="activity-section">
                <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fas fa-cog"></i> Settings</h2>
                <div style="margin-bottom: 15px;">
                    <label>Currency Symbol:</label>
                    <input type="text" value="Ksh" style="width: 100px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Low Stock Threshold:</label>
                    <input type="number" value="10" style="width: 100px;">
                </div>
                <button class="add-btn">Save Settings</button>
            </div>
        </div>

        <div id="help" class="tab-content" style="display:none;">
            <div class="activity-section">
                <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fas fa-question-circle"></i> Help & Support</h2>
                <p>For assistance with the Stock Management System, please contact support.</p>
                <ul style="margin: 15px 0 0 20px;">
                    <li>Adding New Stock</li>
                    <li>Processing Sales</li>
                    <li>Generating Weekly Reports</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <h2>Add New Product</h2>
            <form action="save_product.php" method="POST">
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="text" name="category" placeholder="Category" required>
                <input type="text" name="size" placeholder="Size (e.g. XXL, 500ml)" required>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="number" name="price" placeholder="Price (Ksh)" required>
                <button type="submit" class="add-btn" style="width:100%; margin-top:10px;">Save Product</button>
                <button type="button" onclick="closeModal('productModal')" style="width:100%; background:#95a5a6; margin-top:5px;" class="add-btn">Cancel</button>
            </form>
        </div>
    </div>

    <div id="sellModal" class="modal">
        <div class="modal-content">
            <h2 id="sellTitle">Record Sale</h2>
            <form action="process_sale.php" method="POST">
                <input type="hidden" name="product_id" id="sellProductId">
                <label>Qty to Sell (Max: <span id="maxStock"></span>)</label>
                <input type="number" name="quantity_sold" id="sellQty" min="1" required>
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="Cash">Cash</option>
                    <option value="M-Pesa">M-Pesa</option>
                    <option value="Card">Card</option>
                </select>
                <button type="submit" class="add-btn" style="width:100%; margin-top:10px; background:var(--accent-blue);">Confirm</button>
                <button type="button" onclick="closeModal('sellModal')" style="width:100%; background:#95a5a6; margin-top:5px;" class="add-btn">Cancel</button>
            </form>
        </div>
    </div>
<script>

function showTab(tabId, element) {

    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = "none";
    });

    // Show selected tab
    document.getElementById(tabId).style.display = "block";


    // Remove active class from top navigation
    document.querySelectorAll('.nav-item').forEach(nav => {
        nav.classList.remove("active");
    });

    // Reset sidebar styles
    document.querySelectorAll('.side-item').forEach(side => {
        side.style.color = "rgba(255,255,255,0.6)";
        side.style.background = "transparent";
    });


    // Highlight the clicked sidebar item
    if(element){
        if(element.classList.contains("side-item")){
            element.style.color = "white";
            element.style.background = "rgba(255,255,255,0.1)";
        }

        if(element.classList.contains("nav-item")){
            element.classList.add("active");
        }
    }

}


function openModal(id){
    document.getElementById(id).style.display = "block";
}

function closeModal(id){
    document.getElementById(id).style.display = "none";
}


function openSellModal(id, name, stock){

    document.getElementById("sellProductId").value = id;

    document.getElementById("sellTitle").innerText = "Sell: " + name;

    document.getElementById("maxStock").innerText = stock;

    document.getElementById("sellQty").max = stock;

    openModal("sellModal");

}

</script>
</body>
</html>