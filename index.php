<?php
include 'connection.php';

// creates table if there isn't one
$sql = "SELECT id_number FROM pineapple";
$result = mysqli_query($connection, $sql);

if(empty($result)) {
  $sql = "CREATE TABLE pineapple (
  id_number INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL,
  date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";
  $result = mysqli_query($connection, $sql);
}

// validation
$errors = array('email'=>'', 'terms'=>'');
$email = '';
$terms = isset($_POST['terms']) ? 'checked' : '';

if(isset($_POST['submit'])){
    // checks email
    if(empty($_POST['email'])){
        $errors['email'] = 'Email address is required';
    }else{
      $email = $_POST['email'];
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $errors['email'] = 'Please provide a valid e-mail address';
    }else if(preg_match('/\.(co)$/i', $email)){
      $errors['email'] = 'We are not accepting subscriptions from Colombia emails';
    }

    // checks terms
    if(!isset($_POST['terms'])){
      $errors['terms'] = 'You must accept the terms and conditions';
    }

    if(!array_filter($errors)){
        $email = mysqli_real_escape_string($connection, $_POST['email']);

        $sql = "INSERT INTO pineapple(email) VALUES ('$email')";
 
        if(mysqli_query($connection, $sql)){
            header("Location: success.html");
        }else{
            echo 'query error: ' . mysqli_error($connection);
        }    
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"
      integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="./styles.css" />
    <script src="./main.js" defer></script>
    <title>Pineapple glasses</title>
  </head>
  <body>
    <main>
      <!-- navigation -->
      <nav>
        <div class="logo">
          <img src="./pictures/logo/logo_pineapple.svg" alt="pineapple logo" />
          <img src="./pictures/logo/pineapple-text.png" alt="pineapple text" />
        </div>
        <ul>
          <li><a href="#">About</a></li>
          <li><a href="#">How it works</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
      </nav>

      <!-- content -->
      <section>
        <img src="./pictures/Union.svg" alt="Trophy" id="union" />
        <h1 id="title-text">Subscribe to newsletter</h1>
        <p id="subtitle-text">
          Subscribe to our newsletter and get 10% discount on pineapple glasses.
        </p>

        <form action="index.php" method="POST" novalidate>
          <div class="email">
            <input
              type="email"
              name="email"
              id="email"
              value="<?php echo $email ?>"
              placeholder="Type your email address here..."
            />
            <input type="submit" name="submit"/>
            <div class="email-error error"><?php echo $errors['email']; ?></div>
          </div>
          <div class="check-box">
            <input type="checkbox" name="terms" id="terms" <?php echo $terms ?>/>
            <p>I agree to <a href="#">terms of service</a></p>
            <div class="terms-error error"><?php echo $errors['terms']; ?></div>
          </div>
        </form>

        <!-- footer -->
        <footer>
          <div class="social-icons">
            <a href="#"
              ><div class="facebook"><i class="fab fa-facebook-f"></i></div
            ></a>
            <a href="#"
              ><div class="instagram"><i class="fab fa-instagram"></i></div
            ></a>
            <a href="#"
              ><div class="twitter"><i class="fab fa-twitter"></i></div
            ></a>
            <a href="#"
              ><div class="youtube"><i class="fab fa-youtube"></i></div
            ></a>
          </div>
        </footer>
      </section>
    </main>

    <div class="summer-background"></div>
  </body>
</html>
