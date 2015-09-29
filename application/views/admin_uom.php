<script>
	$(document).ready(function()
	{
		$(document).on("click", ".modaladd", function ()
		{
			var empty = "";
			$(".modal-body #uom").val( empty );
			$(".modal-body #uomdesc").val( empty );
			$("#erroruom").html( empty );
			$("#erroruomdesc").html( empty );
		});

		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
					$('#erroruom').html(data.msg);
					$('#erroruomdesc').html(data.msg1);
				}
				else if(data.st == 1)
				{
					location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>";
				}

			}, 'json');
			return false;
   		});

		$(document).on("click", ".modaledit", function ()
		{
			var uomname = $(this).data('uomname');
			var uomdesc = $(this).data('uomdesc');
			var uomid = $(this).data('uomid');
			var empty ="";
			$(".modal-body #uom1").val( uomname );
			$(".modal-body #uomdesc1").val( uomdesc );
			$(".modal-body #uomid").val( uomid );
			$("#erroruom").html( empty );
			$("#erroruomdesc").html( empty );
		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#erroruom1').html(data.msg);
		 			$('#erroruomdesc1').html(data.msg1);
				}
				else if(data.st == 1)
				{
		  			location.reload();
				}

			}, 'json');
			return false;
   		});

		$(document).on("click", ".modaldelete", function ()
		{
			if(confirm("Do you want to delete?"))
			{
				var uomid = $(this).data('id');
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/delete",{id:uomid}, function( data ) {
					location.reload();
				});
			}
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
			var patt = new RegExp(/^[A-Za-z0-9 _\-\(\)\.\%]+$/);
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
	<!--SEARCH-->
	<div class="form-group">
		<label for="search" class="col-sm-1 control-label">Search</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" id="search" name="search" value="<?php echo $searchrecord; ?>" placeholder="Enter the text here">
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
		<button type="button" class="btn btn-success btn-sm pull-right modaladd" data-toggle="modal" data-target="#myModalAdd" <?php if($addperm==0) echo 'disabled="true"'; ?>>Add New</button>
	</div>
	<!-- -------------------------------------------- -->
	<!-- pop-up for Add -->
	<div class="modal fade" id="myModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Add New <?php echo $labelobject; ?></h4>
				</div>
				<form method=post id=addrecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
					<div class="modal-body">
						<div class="form-group">
							<label for="select" class="col-md-3 control-label"></label>
							<div class="col-md-8">
								<label id="erroruom" class="text-danger"></label>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="form-group">
								<label for="uom" class="col-sm-3 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
								<div class="col-sm-4">
									<input type="text" class="form-control" name="uom"  id="uom" placeholder="UOM" maxlength="60">
								</div>
							</div>
						</div>
						<br>
						<div class="form-group">
							<label for="select" class="col-md-3 control-label"></label>
							<div class="col-md-8">
								<label id="erroruomdesc" class="text-danger"></label>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="form-group">
								<label for="description" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" name="uomdesc" id="uomdesc"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type=submit value="Add New" class="btn btn-primary btn-sm" onclick="showloader();">
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- close pop-up-->
	<!-- pop-up for Edit -->
	<div class="modal fade" id="myModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Edit <?php echo $labelobject; ?></h4>
				</div>
				<form method=post id=updaterecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update/">
					<div class="modal-body">
						<div class="row">
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"></label>
								<div class="col-md-8">
									<label id="erroruom1" name="erroruom1" class="text-danger" ></label>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="form-group">
								<label for="uom1" class="col-sm-3 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
								<div class="col-sm-4">
									<input type="text" class="form-control" name="uom1"  id="uom1" value="" maxlength="60">
									<input type="hidden" class="form-control" name="uomid"  id="uomid">
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="form-group">
								<label for="select" class="col-md-3 control-label"></label>
								<div class="col-md-8">
									<label id="erroruomdesc1" name="erroruomdesc1" class="text-danger"></label>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="form-group">
								<label for="description" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" name="uomdesc1" id="uomdesc1"></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type=submit value="Update" class="btn btn-primary btn-sm" onclick="showloader();">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- close pop-up-->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
	<div class="row">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>No</th>
					<th><?php echo $labelname[0]; ?></th>
					<th><?php echo $labelname[1]; ?></th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sno=1;
					foreach ($records as $uom):
				?>
						<tr>
							<td><?php echo $sno; ?></td>
							<td><?php echo $uom->uom_name; ?></td>
							<td><?php echo $uom->uom_desc; ?></td>
							<td>
								<?php
									if($editperm==1)
									{
								?>
										<a href="#" data-toggle="modal" class="modaledit" data-target="#myModalEdit" data-uomname="<?php echo $uom->uom_name; ?>" data-uomdesc="<?php echo $uom->uom_desc; ?>" data-uomid="<?php echo $uom->uom_id; ?>"><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
								<?php
									}
									else
									{
										echo '<span class="glyphicon glyphicon-edit">&nbsp;</span>';
									}
								?>
				  			</td>
		          			<td>
		          				<?php
									if($delperm==1)
									{
								?>
										<a href="#" data-toggle="modal" class="modaldelete" data-id="<?php echo $uom->uom_id; ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
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
						echo '<tr><td class="row text-center text-danger" colspan="5"> No Record Found</td></tr></tbody></table>';
					}
					else
					{
				?>
			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-4">
				<ul class="pagination">
					<?php echo $this->pagination->create_links(); ?>
				</ul>
			</div>
			<div class="col-md-4 col-md-offset-1" >
      			<div class="form-group">
    				<label for="search" class="col-sm-2 control-label" style="padding-top: 15px; padding-bottom: 5px;">Show</label>
    				<div class="col-sm-3" style="padding-top: 15px; padding-bottom: 5px;">
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
			<div class="col-md-3" style="padding-top: 22px;"> Showing <?php echo $page; ?> to <?php echo $end; ?> of <?php echo $totalrows; ?> rows</div>
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