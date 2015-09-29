<script>
	$(document).ready(function() 
	{
		$('#resetrecord').submit(function()
		{
			$.post($('#resetrecord').attr('action'), $('#resetrecord').serialize(), function( data ) 
			{
				if(data.st == 0)
				{
		 			$('#errorpassword').html(data.msg);
		 			$('#errorcpass').html(data.msg1);
				}
				if(data.st == 1)
				{
		  			location.href="<?php echo base_url(); ?>login";
				}
				
			}, 'json');
			return false;			
   		});
	});

</script>

<div class="form-wrap">
                <h3>Reset New Password</h3>
                <?php if($id=="invalid") { ?>
                <label class="text-danger"><?php echo "Invalid verifcation code.Please click <a href='forgotpassword'>Forgot Password</a> to reset your password";?></label>
                <?php } else { ?>
                    <form method=post action='/resetpassword/update' id="resetrecord">
    					<div class="form-group">
					    	<label id="errorpassword" class="text-danger"></label>
						</div>
                        <div class="form-group">
                            <label for="email" class="sr-only">New Password</label>
                            <input type="password" name="newpass" id="newpass" class="form-control" placeholder="New Password">
                        </div>
    					<div class="form-group">
						   <label id="errorcpass" class="text-danger"></label>
						</div>
                       <div class="form-group">
                            <label for="email" class="sr-only">Retype New Password</label>
                            <input type="password" name="retypepass" id="retypepass" class="form-control" placeholder="Retype New Password">
                        </div>
						<input type=hidden name="uid" id="uid" value="<?php echo $id; ?>">
                        <input type=submit class="btn btn-info btn-lg " value="Submit">
                        <a href="login" class="btn btn-warning btn-lg">Cancel</a>
                    </form>
<?php } ?>
                    <hr>
        	    </div>