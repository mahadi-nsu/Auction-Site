
<title>Settings</title>
<?php include '../partials/navigations.php';?>

<div class="settings">

  <?php 
  if(isset($_GET['error_mode'])){
    $e = htmlspecialchars($_GET['error_mode']);
    if($e == "noerror") echo "<p style=\"color:green;text-transform:capitalize;\">Password Successfully Updated &nbsp; <i class=\"fa fa-check\"></i></p>";
    if($e == "unknown") echo "<p style=\"color:red;text-transform:capitalize;\">Sorry, There were some problems updating your password &nbsp; <i class=\"fa fa-times\"></i></p>";
    if($e == "invalidpasswordformatexception") echo "<p style=\"color:red;text-transform:capitalize;\">Your password must contain at least a letter, a digit and a special character &nbsp; <i class=\"fa fa-times\"></i></p>";
    if($e == "passwordmismatchexception") echo "<p style=\"color:red;text-transform:capitalize;\">new passwords don't match &nbsp; <i class=\"fa fa-times\"></i></p>";
    if($e == "wrongpasswordexception") echo "<p style=\"color:red;text-transform:capitalize;\">current password don't match &nbsp; <i class=\"fa fa-times\"></i></p>";
    if($e == "userdontexistexception") echo "<p style=\"color:red;text-transform:capitalize;\">No such user is registerd &nbsp; <i class=\"fa fa-times\"></i></p>";
    if($e == "toosmallexception") echo "<p style=\"color:red;text-transform:capitalize;\">Password must contain at least 6 characters &nbsp; <i class=\"fa fa-times\"></i></p>";
  }
  ?>





  <h2>Settings</h2>
  <form action="../../functions/process-forms.php" method="post">
    <ul>
      <li>
        <label>Username</label>
        <input type="text" name="mail" placeholder="user name"> 
      </li>
      <li>
        <label>Password</label>
        <input type="password" name="current-password" placeholder="Current Password">
        
      </li>
    </ul>


    <div class="password-inputs">
      <section>
        <input type="password" name="new-password"  placeholder="Minimum 6 Characters (0-9,a-z,@#$)"> 
        <p>New Password</p>
      </section>
      
      <section>
        <input type="password" name="confirm-password" placeholder="Repeat Password"> 
        <p>Confirm Password</p>
      </section>
      
      <input type="submit" name="edit-member-password" value="Update">
    </div>

  </form>


</div> <!--/settings -->



<?php include '../partials/footer.php';?>