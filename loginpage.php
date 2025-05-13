<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>loginPage</title>
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

	<style type="text/css">
		    /* Basic Reset */
		*{
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			font-family: "poppins", sans-serif;

		}
		     /* Body and main container */
		body{
			display: flex;
			justify-content: center;
			margin-top: 200px;
			background: url(wow.jpg);
		}
		/* positioning and sizing sa main container content */
		.login{
			width: 420px;
			background: transparent;
			color: white;			
		}
		/* para sa kanang text nga login sa taas */
		.login h1{
			font-size: 36px;
			text-align: center;
		}
		/* positionining and sizing sa inputs username, password and icon nga naa sa kilid */
		.login .inputbox{
			position: relative;
			width: 100%;
			height: 50px;
			margin: 30px 0;
		}
		/* position, size mismo sa input box kanang username and password etc.. */
		.inputbox input{
			height: 100%;
			width: 100%;
			background: transparent;
			border: none;
			outline: none;
			border: 2px solid rgba(255, 255, 255, 2);
			border-radius: 40px;
			font-size: 16px;
			color: white;
			padding: 20px 45px 20px 20px;
		}
		/* color sa anang text nga sulod sa username, password placeholder*/
		.inputbox input::placeholder{
			color: white;
		}
		/* position og size anang duha ka icons*/
		.inputbox i{
			position: absolute;
			right:25px;
			top: 15px;
			font-size: 20px;
		}
		/* position sa Remember me, and Forgotpassword*/
		.remember-forget{
			text-align: center;
		}
		/* login button size, color, backgroundcolor, margin, position,borders etc..*/
		.login .btn{
			width: 100%;
			height: 45px;
			background: white;
			border: none;
			outline: none;
			border-radius: 40px;
			cursor: pointer;
			font-size: 100%;
			color: black;
			font-weight: 600;
		}
		/* position and margin sa Dont have account? register*/
		.login .register-link{
			text-align: center;
			margin-top: 20px;
		}
	</style>
</head>

<body>


	<div class="login">
		<form action="">
			<h1 class="Logintext" style="color: black;">Login</h1>
			<div class="inputbox">
				<input type="text" placeholder="username" required>
				<i class='bx bxs-user'></i>
			</div>

	<div class="inputbox">
		<input type="password" placeholder="password" required>
		<i class='bx bxs-lock-alt' ></i>
	</div>

	<div class="remember-forget">
		<label><input type="checkbox" class="box_check" style="scale:1.5"> Remeber me</label>
		<a href="#">Forgot password?<a/>
	</div>

	<button type="submit=" class="btn"><a href="Dashboard.html">Login</a></button>
	
	
	<div class="register-link">
		<p>Don't have an account?<a href="#"> Register<a/></p>
	</div>
	</form>
</div>
</body>
</html>