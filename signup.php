<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="signup.css">
</head>
<body>
  <section class="vh-100 bg-image">
    <div class="mask d-flex align-items-center h-100 gradient-custom-3">
      <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-12 col-md-9 col-lg-7 col-xl-6">
            <div class="card">
              <div class="card-body">
                <h2 class="text-uppercase text-center mb-5">Sign Up</h2>

                <form action="process_signup.php" method="POST">
                  <div class="form-outline mb-4">
                    <input type="text" id="name" name="name" class="form-control form-control-lg" required />
                    <label class="form-label" for="name">Your Name</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="email" id="email" name="email" class="form-control form-control-lg" required />
                    <label class="form-label" for="email">Your Email</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                    <label class="form-label" for="password">Password</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control form-control-lg" required />
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                  </div>

                  <div class="form-check d-flex justify-content-center mb-5">
                    <input class="form-check-input me-2" type="checkbox" id="terms" name="terms" required />
                    <label class="form-check-label" for="terms">
                      I agree to the <a href="#" class="text-body"><u>Terms of Service</u></a>
                    </label>
                  </div>

                  <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">
                      Register
                    </button>
                  </div>

                  <p class="text-center text-muted mt-5 mb-0">Already have an account? <a href="login.html" class="fw-bold text-body"><u>Login here</u></a></p>
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
