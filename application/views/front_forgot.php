<div class="form-wrap">
	<h3>Forgot Password</h3>
	<label class="text-danger">
	<?php
	if(form_error('email')!='') {
	echo form_error('email');
	} else {
	echo form_error('username');
	}
	?></label>
	<div class="row text-center text-danger"><?php echo $message; ?> </div>
	<form method=post action='/forgotpassword/verify_user'>
		<div class="form-group">

			<label for="email" class="sr-only">Username ID</label>
			<input type="text" name="username" id="username" class="form-control" placeholder="Username ID">
		</div>
		OR
		<p>
		<div class="form-group">
			<label for="email" class="sr-only">Email Address</label>
			<input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
		</div>
		<input type="submit" class="btn btn-info btn-lg " value="Submit">
		<a href="/login" class="btn btn-warning btn-lg">Cancel</a>
	</form>
	<hr>
</div>