<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Orva CRM | Sign in</title>
<link rel="icon" type="image/png" href="<?php echo base_url();?>systemimages/favicon.png" />
<style type="text/css">
* {
  box-sizing: border-box;
}

html,
body {
  min-height: 100%;
  margin: 0;
}

body {
  font: 14px/1.5 Arial, Tahoma, Verdana, sans-serif;
  color: #0f172a;
  background:
    radial-gradient(circle at top left, rgba(129, 140, 248, 0.32), transparent 34rem),
    linear-gradient(135deg, #0f172a 0%, #1e1b4b 52%, #312e81 100%);
}

.container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}

.login {
  width: 100%;
  max-width: 390px;
  padding: 32px;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(255, 255, 255, 0.42);
  border-radius: 20px;
  box-shadow: 0 24px 70px rgba(15, 23, 42, 0.32);
}

.brand {
  margin-bottom: 28px;
  text-align: center;
}

.brand-name {
  margin: 0;
  color: #6366f1;
  font-size: 52px;
  font-weight: 800;
  letter-spacing: -0.07em;
  line-height: 1;
}

.brand-subtitle {
  margin: 10px 0 0;
  color: #64748b;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
}

h1 {
  margin: 0 0 24px;
  color: #0f172a;
  font-size: 22px;
  font-weight: 700;
  text-align: center;
}

p {
  margin: 16px 0 0;
  color: #334155;
  font-weight: 600;
}

input[type=text],
input[type=password] {
  display: block;
  width: 100%;
  height: 44px;
  margin-top: 6px;
  padding: 0 12px;
  color: #0f172a;
  font-size: 15px;
  background: #ffffff;
  border: 1px solid rgba(15, 23, 42, 0.12);
  border-radius: 12px;
  outline: 0;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

input[type=text]:focus,
input[type=password]:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.14);
}

input[type=checkbox] {
  margin-left: 8px;
  accent-color: #6366f1;
}

input[type=submit] {
  width: 100%;
  height: 44px;
  color: #ffffff;
  font-size: 15px;
  font-weight: 700;
  background: linear-gradient(135deg, #6366f1, #818cf8);
  border: 0;
  border-radius: 999px;
  cursor: pointer;
  box-shadow: 0 12px 26px rgba(99, 102, 241, 0.28);
}

input[type=submit]:hover {
  filter: brightness(1.03);
}

.error,
.validation_errors {
  color: #ef4444;
}
</style>
</head>
<script src="<?php echo base_url();?>ckeditor/ckeditor.js"></script>
<body>
 <section class="container">
    <div class="login">
    <div class="brand" aria-label="Orva CRM">
      <p class="brand-name">Orva</p>
      <p class="brand-subtitle">CRM Demo</p>
    </div>
    <h1>Sign in to Orva</h1>
    <?php
    echo form_open('administrator/login_validation');

    echo validation_errors();
 // if(isset($custom_error))
 //  echo $custom_error;

    echo "<p>Username";
    echo form_input('user');
    echo "</p>";

    echo "<p>Password";
    echo form_password('password');
    echo "</p>";

    echo "<p>Remember Me";
    echo form_checkbox('remember_me', '1', TRUE);
    echo "</p>";

    echo "<p style='text-align:center'>";
    echo form_submit('login_submit', 'Login');
    echo "</p>";

    echo form_close();
    ?>
</div>
  </section>
</body>
</html>
