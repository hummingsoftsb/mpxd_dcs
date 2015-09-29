
<script src="<?php echo base_url(); ?>/bootstrap/jquery-1.10.2.min.js"></script>
<script src="<?php echo base_url(); ?>/ilyas/jquery.loader.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>/ilyas/loader.css"></link>
		
<div id="after_header">
<div class="form-wrap">
	<h4>Log in with your email account</h4>
	<?php echo form_open('login/verify_login', array('id'=>'frm')); ?>
		<div class="form-group">
			<label class="text-danger"><span id="errormsg"></span><?php echo form_error('email'); ?></label>
			<label for="email" class="sr-only">Email</label>
			<input type="text" name="email" id="email" class="form-control" value="<?php echo $emails; ?>" placeholder="Username or Email">
		</div>
		<div class="form-group">
			<label class="text-danger"><?php echo form_error('keypass'); ?></label>
			<label for="key" class="sr-only">Password</label>
			<input type="password" name="keypass" id="keypass" class="form-control" placeholder="Password">
		</div>
		
		<input id="submitbtn" type="submit" class="btn btn-info btn-lg btn-block" value="Login">
	</form>
	<a href="/forgotpassword">Forgot your password?</a>
	<hr>
</div>
</div>
<script>
function showloader() {
	$('#after_header').loader('show');
}
function hideloader() {
	setTimeout(function(){$('#after_header').loader('hide')},200);
}

function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}

function login_enable() {
	$('#email').removeAttr('disabled');
	$('#keypass').removeAttr('disabled');
	$('#submitbtn').removeAttr('disabled');
}

function login_disable() {
	$('#email').attr('disabled','disabled');
	$('#keypass').attr('disabled','disabled');
	$('#submitbtn').attr('disabled','disabled');
}

$(function(){
	var backlink = getURLParameter('back');
	if (backlink != "") {
		$('#frm').append($('<input type="hidden" name="back" id="back" value="'+backlink+'">'));
	}
		
	$('#frm').on('submit',function(e){
		var $t = $(this);
		var data = $t.serialize();
		login_disable();
		$.post($t.attr('action'), data).always(function(d) {
			login_enable();
			d = $.parseJSON(d);
			if (d.st == "1") {
				if ((typeof d.location != "undefined") && (d.location != "")) window.top.location = d.location;
				showloader();
			} else {
				if ((typeof d.message != "undefined") && (d.message != "")) $('#errormsg').html(d.message);
			}
			//console.log(d);
		});
		e.preventDefault();
		return false;
	})
});
</script>