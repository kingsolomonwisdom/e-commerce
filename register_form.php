<?php session_start();
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>RRMS Sign Up</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      display: flex;
      justify-content: center;
      margin-top: 200px;
      background: url(wow.jpg);
    }
      * {
          padding: 0;
          margin: 0;
          box-sizing: border-box;
      }
      .header-space {
          letter-spacing: 4px;
      }
      .conword-space {
          letter-spacing: 2px;
      }
      .form-control {
          background-color: #fffdf2;
      }
      .header-shadow {
          box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.8);
          position: relative;
          z-index: 10;
      }
      .signup-container {
          width: 50%;
      }
      @media (max-width: 992px) {
          .signup-container {
              width: 75%;
          }
      }
      @media (max-width: 768px) {
          .signup-container {
              width: 90%;
          }
          .form-check {
              flex-direction: column;
              text-align: center;
          }
      }

   .signup-form {
    background: transparent;
    padding: 30px;
    border: 2px solid white; /* white border around form */
    border-radius: 15px;
    color: white;
}

.signup-form input,
.signup-form .form-check-label {
    background: transparent !important;
    color: white !important;
    border: 2px solid white !important;
}

.signup-form input::placeholder {
    color: white;
    opacity: 2;

}

.signup-form .form-check-input {
    background-color: transparent;
    border: 2px solid white;
}

</style>
</head>
<body>
  <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
      <div class="text-center">
          <h2 class="header-space p-1 fw-bold">RAPIDREX</h2>
      </div>
          <h4 class="conword-space text-center fw-bold">Create a new account</h4>
  
          <form method="POST" action="registration.php" class="signup-form">
              <div class="row g-2">
                  <div class="col-md-6">
                      <input type="text" name="fname" class="form-control border-2 border-dark rounded-4" placeholder="First Name">
                  </div>
                  <div class="col-md-6">
                      <input type="text" name="lname" class="form-control border-2 border-dark rounded-4" placeholder="Last Name">
                  </div>
              </div>
              <div class="mt-2">
                  <input type="text" name="uname" class="form-control border-2 border-dark rounded-4" placeholder="Username">
              </div>
              <div class="mt-2">
                  <input type="email" name="email" class="form-control border-2 border-dark rounded-4" placeholder="Email Address">
              </div>
              <div class="mt-2">
                  <input type="tel" name="phonenumber" class="form-control border-2 border-dark rounded-4" placeholder="Phone Number">
              </div>
              <div class="mt-2">
                  <input type="password" name="password" class="form-control border-2 border-dark rounded-4" placeholder="Password">
              </div>
              <div class="mt-2">
                  <input type="password" name="confirm_password" class="form-control border-2 border-dark rounded-4" placeholder="Confirm Password">
              </div>
              <div class="mt-2">
                  <input type="text" name="address" class="form-control border-2 border-dark rounded-4" placeholder="Address">
              </div>
              <div class="form-check d-flex justify-content-center align-items-center mt-2">
                  <!-- <label class="form-check-label fw-bold text-dark" > I agree to the Terms and Privacy Policy</label> -->
                    <label><input type="checkbox" class="check_box" style="scale: 1.5;">I agree to the Terms and Privacy Policy</label>

              </div>
              <div class="d-flex justify-content-center mt-3">
                  <button type="submit" name="submit" class="btn btn-danger border-dark border-2 rounded-5 w-50 fw-bold fs-5">Sign Up</button>
              </div>
          </form>
      </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
