<script>
	$(document).ready(function()
	{
		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$('#erroroldpass').html(data.msg);
		 			$('#errornewpass').html(data.msg1);
					$('#errorrenewpass').html(data.msg2);
				}
				if(data.st == 1)
				{
		  			location.href="<?php echo base_url(); ?>home";
				}

			}, 'json');
			return false;
		});
	});
</script>
<?php
	$labelnames='';
	foreach ($labels as $label): 
		$labelnames .= ','.$label->sec_label_desc;
	endforeach;
	$labelnames=substr($labelnames,1);
	$labelname=explode(",",$labelnames);
?>
<div class="container">
	<div class="page-header">
		<h1 id="nav"><?php echo $labelobject; ?></h1>
	</div>
	<div class="row">
		<div class="col-md-3">
			<ul class="breadcrumb">
			<li><a href="<?php echo base_url(); ?>home">Home</a></li>
				<li><?php echo $labelgroup; ?></li>
                <li class="active"><?php echo $labelobject; ?></li>
			</ul>
		</div>
	</div>
	<div class="row" style="width: 50%; margin: auto;">
		<form method=post id="addrecord" action='<?php echo base_url(); ?><?php echo $cpagename; ?>/update'>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<label id="erroroldpass" class="text-danger"></label>
			</div>
			<div class="row">
			<div class="form-group">
				<label class="col-sm-4 control-label"><?php echo $labelname[0]; ?> <red>*</red></label>
				<div class="col-sm-5">
				<input type="hidden" name="username" id="username" class="form-control" value="<?php echo $username; ?>">
				<input type="password" name="oldpass" id="oldpass" class="form-control" placeholder="Old Password" maxlength="20">
			</div>
			</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<label id="errornewpass" class="text-danger"></label>
			</div>
			<div class="row">
			<div class="form-group">
				<label class="col-sm-4 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
				<div class="col-sm-5">
				<input type="password" name="newpass" id="newpass" class="form-control" placeholder="New Password" maxlength="20">
				</div>
			</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<label id="errorrenewpass" class="text-danger"></label>
			</div>
			<div class="row">
			<div class="form-group">
				<label class="col-sm-4 control-label"><?php echo $labelname[2]; ?> <red>*</red></label>
				<div class="col-sm-5">
				<input type="password" name="renewpass" id="renewpass" class="form-control" placeholder="Retype New Password" maxlength="20">
				</div>
			</div>
			</div>
			<div style="text-align:center;padding-right:20px;padding-top:20px;">
				<input type=submit class="btn btn-primary btn-sm" value="Submit">
				<!-- <a href="home" class="btn btn-primary btn-sm">Cancel</a> -->
			</div>
		</form>
	</div>
</div>