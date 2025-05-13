<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and main container */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            display: flex;
        }

        /* Sidebar kanang naay gray sa kilid */
        .sidebar {
            width: 250px;
            background-color: #2f3640;
            color: white;
            padding: 20px;
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

         /* kanang sulod sa sidebar kanang mga home etc..*/
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px 0;
             font-size: 1.1rem;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        /* effects na siya sa a href katong color2 pag i tudlo nimo imong cursor */
        .sidebar a:hover {
            background-color: green;
        }
        /* sulod sa sidebar kanang naay ShopwayDashboard */
        .sidebar .logo {
            text-align: center;
            font-size: 1.5rem;
        }
        /* dapat bold ang "Shopway" pero wala ni gawas*/
        .sidebar .span {
            font-weight: bold;
        }

        /* Main Content */
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: 100%;
        }
        /* header kanang naay text sa pinaka taas kanang naay morag container  */
        .header {
            background-color: white;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* kanang specifically text nga naa sa header */
        .header h1 {
            font-size: 1.8rem;
            color: black;
        }
        /* kanang ngalan beside sa picture profile sa user righttop */
        .header .user {
            display: flex;
            align-items: center;
        }
        /* picture before sa username righttop */
        .header .user img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        /* position and sizing sa main content include header, user, profile, icons etc..*/
        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        /* position and sizing sa sulod sa container anang tulo Sales, Order, product..*/
        .card {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        /* position and sizing sa kanang mas bold nga text sulod anang tulo ka container Sales, Order, and Products */
        .card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }
        /* walay laing number nga naa dira*/
        .card p {
            color: black;
        }
        /* position and sizing sa tulo ka icons nga sulod sa container, reference for icons:https://emojipedia.org/bar-chart*/
        .card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .bold {
    font-weight: bold;
}
  
    </style>
</head>
<body>

    <!--sidebar-->
    <div class="sidebar">
        <div class="logo">
           <span class="bold">Shopway</span>Dashboard
        </div>
        <div class="menu">
            <a href="homepage.html">Home</a>
            <a href="Orders.php">Orders</a>
            <a href="#">Products</a>
            <a href="#">Customers</a>
            <a href="#">Reports</a>
            <a href="loginpage.html">Log out</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Welcome back, User</h1>
            <div class="user">
                <img src="me.jpg">
                <span>John Marvin</span>
            </div>
        </div>

        <!--  Cards -->
        <div class="cards">
            <div class="card">
                <div class="icon">📊</div>
                <h3>Sales</h3>
                <p>₱130,000</p>
            </div>
            <div class="card">
                <div class="icon">📦</div>
                <h3>Orders</h3>
                <p>150</p>
            </div>
            <div class="card">
                <div class="icon">🛒</div>
                <h3>Products</h3>
                <p>300</p>
            </div>
        </div>
    </div>

</body>
</html>
