<!DOCTYPE html>
<html>
  <head>
    <title>PHP Captcha Demo</title>
    <style>
    #demo {
      max-width: 320px;
      padding: 15px;
      background: #f2f2f2;
    }
    #demo label, #demo input {
      display: block;
      box-sizing: border-box;
      width: 100%;
      margin-top: 10px;
      padding: 10px;
    }
    </style>
  </head>
  <body>
    <form id="demo" method="post" action="3-submit.php">
      <!-- (A) FORM FIELDS -->
      <label for="name">Name:</label>
      <input name="name" type="text" required/>
      <label for="email">Email:</label>
      <input name="email" type="email" required/>

      <!-- (B) CAPTCHA HERE -->
      <label for="captcha">Are you human?</label>
      <?php
      require "1-captcha.php";
      $PHPCAP->prime();
      $PHPCAP->draw();
      ?>
      <input name="captcha" type="text" required/>

      <!-- (C) GO! -->
      <input type="submit"/>
    </form>
  </body>
</html>