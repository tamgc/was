<?php
if(isset($_POST['send']))
{
  if($_POST['name'] == '')
  {
    $to = "tamgc@tamgc.net";
    $subject = "[tamgc.net]" . $_POST['subject'];
    //$name_field = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $headers = 'From: ' . "$email";

    //$body = "From: $name_field\nE-mail: $email\nMessage:\n $message";
    //$body = "E-mail: $email\nMessage:\n $message";
    $body = "$message";
    echo '<h2>Message has been sent!</h2>';
    mail($to, $subject, $body, $headers);
  } else {
    header( 'Location: http://www.tamgc.net' );
  }
}
include("./inc/header.inc");
?>
  <form method="POST" action="contact.php">
    <div id="form0">
      <input type="text" name="name" size="30"><br />
    </div>
    Email:<br />
    <input type="text" name="email" size="40">
    <p>Subject:<br />
    <input type="text" name="subject" size="40">
    </p>
    Message:<br />
    <textarea rows="10" name="message" cols="40"></textarea><br /><br />
    <input type="submit" value="Send" name="send">
  </form>
<?php include("./inc/footer.inc"); ?>
