
<?php include '../partials/header.php';?>


	
	<div class="sign-form sign-up-form">
		
		<h2>Sign up here</h2>
		<form action="../../functions/process-forms.php" method="post">
			
			<section>
				<label>Mail ID</label>
				<input type="email" name="mail" placeholder="Email Address">
			</section>

			<section>
				<label>Password</label>
				<input type="password" name="password" placeholder="Minimum 6 Characters (0-9,a-z,@#$)">
			</section>

			<section>
				<input type="password" name="confirm-password" placeholder="Confirm Password">
			</section>

			<input type="submit" name="register-user" value="Register">
			
			<br><br><br>
			<?php if(isset($_GET['error_mode'])){
				$e = htmlspecialchars($_GET['error_mode']);
				if($e == "emptyemailexception") echo "<p class=\"pull-right\" style=\"color:red; text-transform:capitalize;\">E-mail address can not be empty</p>";
				if($e == "invalidemailexception") echo "<p class=\"pull-right\" style=\"color:red; text-transform:capitalize;\">The E-mail address is not valid</p>";
				if($e == "toosmallexception") echo "<p class=\"pull-right\" style=\"color:red; text-transform:capitalize;\">Password must contain at least 6 characters</p>";
				if($e == "emptypasswordexception") echo "<p class=\"pull-right\" style=\"color:red; text-transform:capitalize;\">Please provide a password to continue</p>";
				if($e == "passwordmismatchexception") echo "<p class=\"pull-right\" style=\"color:red; text-transform:capitalize;\">Values in the password fields do not match</p>";
				if($e == "invalidpasswordformatexception") echo "<p class=\"pull-right\" style=\"color:red; text-transform:capitalize;\"> Password must contain at least a letter,digit and a special character</p>";
				if($e == "useralreadyexist") echo "<p class=\"pull-right\" style=\"color:red; text-transform:capitalize;\">This e-mail is already taken. Try a different one</p>";
					

			}
			?>


		</form>

	</div> <!-- /sign-up-form -->









	









	<div class="sign-form sign-in-form">
		
		<h2>Already Have an Account? Try Signing in</h2>
		<form action="../../functions/process-forms.php" method="post">
			
			<section>
				<label>E-mail</label>
				<input type="text" name="mail" placeholder="E-mail Address">
			</section>

			<section>
				<label>Password</label>
				<input type="password" name="password" placeholder="Choose Password">
			</section>

			<input type="submit" name="login-user" value="Sign In">


			<br><br><br>
			<section>
				<?php if(isset($_GET['error_mode'])){
				$e = htmlspecialchars($_GET['error_mode']);
				if($e == "wrongpasswordexception") echo "<p style=\"text-align:center; color:red; text-transform:capitalize;\">Wrong Password</p>";
				if($e == "userdontexistexception") echo "<p style=\"text-align:center; color:red; text-transform:capitalize;\">No such user is registered</p>";
			}
			?>
			</section>

		</form>

	</div> <!-- /sign-in-form -->



















	<div id="login-modal">
		
	</div>






	<footer>&copy; All rights reserved. Developed by NSU CSE</footer>
	



<?php include '../partials/footer.php';?>