<script>

	$(document).ready(function()
	{
		$(document).on("click", ".modaledit", function ()
		{
		     var objid = $(this).data('objid');
		     var objgroup = $(this).data('objgroup');
		     var objdesc = $(this).data('objdesc');
			 var objtype = $(this).data('objtype');
			 var error='&nbsp;';
			 $(".modal-body #objid").val( objid );
		     $(".modal-body #objgroup").val( objgroup );
		     $(".modal-body #objdesc").val( objdesc );
		     $(".modal-body #objtype").val( objtype );
		     $('#error').html( error );
		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#error').html(data.msg);
				}
				else if(data.st == 1)
				{
		  			location.reload();
				}

			}, 'json');
			return false;
   		});

   		$("#recordselect").change(function()
		{
			var recordselect = $(this).val();
			$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/selectrecord",{recordselect:recordselect}, function( data ) {
				location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/select";
			});
	    });

	    $("#recordsearch").click(function ()
	    {
			var search = $('#search').val();
			var patt = new RegExp(/^[A-Za-z0-9 _\-\(\)\.]+$/);
			if(patt.test(search) || search=='')
			{
				var search = $('#search').val();
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord",{search:search}, function( data ) {
					location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
				});
			}
			else
			{
				alert('The Search field may only contain alpha-numeric characters, underscores, dashes and bracket.');
			}
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
<div id="after_header">
<div class="container">
	<!-- BUAT CODING DALAM WRAP-->
	<!-- INPUT HERE-->
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
	<!-- ---------------------- -->
	<div class="form-group">
		<label for="search" class="col-sm-1 control-label">Search</label>
    	<div class="col-sm-4">
    		<input type="text" class="form-control" id="search" name="search" value="<?php echo $searchrecord; ?>" placeholder="Enter the text here">
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
	</div>
	<!-- small modal -->
	<div class="modal fade" id="myModalEdit"  tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-content">
					<form method=post id=updaterecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update/">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="myModalLabel">Edit <?php echo $labelobject; ?></h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"></label>
								<div class="col-md-8">
									<label id="error" class="text-danger"></label>
								</div>
							</div>
							<br>
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"><?php echo $labelname[0]; ?> <red>*</red></label>
								<div class="col-md-8">
									<input type="hidden"  class="form-control"  id="objid" name="objid"/>
									<select class="form-control" id="objgroup" name="objgroup">
										<?php
											foreach ($groups as $group):
										?>
												<option value="<?php echo $group->sec_group_id; ?>"><?php echo $group->sec_group_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
							<br>
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"><?php echo $labelname[1]; ?> <red>*</red></label>
								<div class="col-md-8">
									<input type="text" class="form-control" id="objdesc" name="objdesc" maxlength="80">
								</div>
							</div>
							<br>
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"><?php echo $labelname[2]; ?> <red>*</red></label>
								<div class="col-md-8">
									<select class="form-control" id="objtype" name="objtype">
										<option value="1">Screen</option>
										<option value="2">Report</option>
									</select>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
							<input type="submit" class="btn btn-primary btn-sm" value="Save changes" onclick="showloader();" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- ENOF Large modal -->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
	<table class="table table-striped table-hover ">
		<thead>
			<tr>
				<th>No</th>
				<th><?php echo $labelname[0]; ?></th>
				<th><?php echo $labelname[1]; ?></th>
				<th><?php echo $labelname[2]; ?></th>
				<th>Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$sno=$page;
				foreach ($records as $record):
			?>
					<tr>
						<td><?php echo $sno; ?></td>
						<td><?php echo $record->sec_group; ?></td>
						<td><?php echo $record->sec_obj_desc; ?></td>
						<td><?php if($record->sec_obj_type=="1") echo 'Screen'; else echo 'Report'; ?></td>
						<td>
							<?php
								if($editperm==1)
								{
							?>
									<a href="#" data-toggle="modal" class="modaledit" data-target="#myModalEdit" data-objgroup="<?php echo $record->sec_group_id; ?>" data-objdesc="<?php echo $record->sec_obj_desc; ?>" data-objid="<?php echo $record->sec_obj_id; ?>" data-objtype="<?php echo $record->sec_obj_type; ?>"><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
							<?php
								}
								else
								{
									echo '<span class="glyphicon glyphicon-edit">&nbsp;</span>';
								}
							?>
						</td>
					</tr>
			<?php
				$sno=$sno+1;
				endforeach;
				if($totalrows==0)
				{
					echo "<tr><td colspan='5' class='row text-center text-danger'> No Record Found</td></tr></tbody></table>";
				}
				else
				{
			?>
		</tbody>
	</table>
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-4">
				<ul class="pagination">
					<?php echo $this->pagination->create_links(); ?>
				</ul>
			</div>
			<div class="col-md-4 col-md-offset-1">
				<div class="form-group">
					<label for="search" class="col-sm-2 control-label" style="padding-top: 22px;">Show</label>
					<div class="col-sm-3" style="padding-top: 14px;">
						<select class="form-control" id="recordselect" name="recordselect">
							<option <?php if($selectrecord=="10") echo "selected=selected"; ?>>10</option>
							<option <?php if($selectrecord=="20") echo "selected=selected"; ?>>20</option>
							<option <?php if($selectrecord=="40") echo "selected=selected"; ?>>40</option>
						</select>
					</div>
				</div>
			</div>
			<?php
				// Display the number of records in a page
				$end=$mpage+$page-1;
				if($totalrows<$end) $end=$totalrows;

			?>
			<div class="col-md-3" style="padding-top: 22px;"> Showing <?php echo $page; ?> to <?php echo $end; ?> of <?php echo $totalrows; ?> rows  </div>
		</div>
		<?php }?>
	</div>
</div>
<script type="text/javascript">
	function showloader() {
		$('#after_header').loader('show');
	}

	function hideloader() {
		setTimeout(function(){$('#after_header').loader('hide')},200);
	}
	$.post("<?php echo $this->config->base_url().'index.php/'.$cpagename; ?>/validate?jid=<?php echo $details->journal_no; ?>", data).always(function(data){
		console.log(data);
		hideloader();
		disallow();
		location.href='<?php echo $this->config->base_url() ?>/index.php/journalvalidationnonp';
		if (typeof callback == "function") callback();
	});
</script>
</div>