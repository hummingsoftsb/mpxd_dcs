d<script>
	$(document).ready(function()
	{
		$("#modaladd").click(function ()
		{
			var empty="";
			$(".modal-body #imagefile").val( empty );
			$(".modal-body #imagedesc").val( empty );
			$('#errorimage').html(empty);
			$('#errordesc').html(empty);
			$('#diverrormsg').html(empty);
			
		});

		$('#addRecord').submit(function()
		{
			var empty="";
			$('#diverrormsg').html(empty);
			$.post($('#addRecord').attr('action'), $('#addRecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$('#errordata').html(data.msg);
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
			if(confirm("Do you want to delete the image?"))
			{
				var imgid = $(this).data('imgid');
				var dataid = $(this).data('dataid');
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/deleteimage",{id:imgid,dataid:dataid}, function( data ) 
				{
					var imagevalue=data.imgval;
					var imagevalue1 = imagevalue.split(',777,');
					$("#tableimage").find("tr:gt(0)").remove();
					for (var i = 0; i < imagevalue1.length; i++)
					{
						if(imagevalue1[i]!="")
						{
							var content='';
							var imagevalue2 = imagevalue1[i].split(',');
							content += '<tr>';
							content += '<td>'+imagevalue2[0]+'</td>';
							content += '<td> <img src="'+imagevalue2[1]+imagevalue2[2]+'" class="img-responsive" alt="" style="width: 200px; height: 137px;"> </td>';
							content += '<td> '+imagevalue2[3]+' </td>';
							content += '<td> <a href="#" data-toggle="modal" class="modaldelete" data-imgid="'+imagevalue2[4]+'" data-dataid="'+imagevalue2[5]+'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td>';
							content += '</tr>';
							$("#tableimage").append(content);	
						}
						
					}
					$("#diverrormsg").html("Picture Attachment Deleted Successfully");
					
				}, 'json');
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
<div class="container">
	<div class="page-header">
		<h1 id="nav"><?php echo $labelobject; ?></h1>
	</div>
	<!-- BUAT CODING DALAM WRAP-->
	<?php 
		foreach($details as $row):
			$week=$row->frequency_period;
			$pname=$row->project_name;
			$jname=$row->journal_name;
			$owner=$row->user_full_name;
		endforeach;
	?>
	<!-- INPUT HERE-->
	<div class="row" style="width: 70%; margin: auto;">
		<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[3]; ?></b></div>
  		<div class="col-xs-9" style="color: blue; margin-bottom: 8px;">Week <?php echo $week; ?></div>
  		<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[0]; ?></b></div>
  		<div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $pname; ?></div>
  		</br>
  		<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[1]; ?></b></div>
  		<div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $jname; ?></div>
  		</br>
  		<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[4]; ?></b></div>
		<div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $owner; ?></div>
		</br>
	<div class="row text-center text-danger"><?php echo $message; ?> </div>
	<form id="addRecord" method="POST" action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
		<fieldset>
	    	<legend><?php echo $labelname[5]; ?></legend>
			<table class="table table-striped table-hover ">
				<tr>
					<td colspan="7" align="center"><div id="errordata" class="text-danger"></div><input type="hidden" id="dataentryno" name="dataentryno" value="<?php echo $dataentryno; ?>" />
					</td>
				</tr>
				<thead>
					<tr>
						<th><?php echo $labelname[6]; ?></th>
						<th><?php echo $labelname[7]; ?></th>
						<th><?php echo $labelname[8]; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sno=1;
						
						foreach($dataentryattbs as $dataentryattb):
						
							echo "<tr>";
							echo '<td>'.$sno.'</td>';
							echo '<td>'.$dataentryattb->data_attb_label.'</td>';
							echo '<td><input type="hidden" id="dataattbid'.$sno.'" name="dataattbid'.$sno.'" value="'.$dataentryattb->data_attb_id.'" />';
							if($dataentryattb->data_attb_type_id==1)
							{
								echo '<textarea type="text" id="dataattb'.$sno.'" name="dataattb'.$sno.'" class="form-control">'.$dataentryattb->actual_value.'</textarea>';
								echo '<input type="hidden" id="dataattbtype'.$sno.'" name="dataattbtype'.$sno.'" value="1" />';
							}
							else
							{
								echo '<input type="text" id="dataattb'.$sno.'" name="dataattb'.$sno.'" class="form-control" value="'.$dataentryattb->actual_value.'" maxlength=10 style="width:150px;" onpaste="return false"/>';								
								echo '<script>$(document).ready(function(){$( "#dataattb'.$sno.'" ).datepicker({showOn: "button",buttonImage: "/img/calendar.gif",	buttonImageOnly: true,buttonText: "Select date",dateFormat: "dd-mm-yy"});});</script>';
								echo '<input type="hidden" id="dataattbtype'.$sno.'" name="dataattbtype'.$sno.'" value="2" />';
							}
							echo '<input type="hidden" id="datalabel'.$sno.'" name="datalabel'.$sno.'" value="'.$dataentryattb->data_attb_label.'" />';
							echo '</td>';
							echo "</tr>";
							$sno++;
						endforeach;
					?>
					<input type="hidden" id="dataattbcount" name="dataattbcount" value="<?php echo $sno-1; ?>" />
				</tbody>
			</table>
		</fieldset>
		</br>
		<div class="row text-center text-danger" id="diverrormsg"></div>
		<fieldset>
			<legend><?php echo $labelname[9]; ?></legend>
			<p style="text-align: right;"><?php echo $labelname[10]; ?> &nbsp &nbsp &nbsp <a href="javascript:void(0)" data-toggle="modal" data-target=".bs-example-modal-lg2"><button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#myModal" id="modaladd" name="modaladd">Upload</button></a></p>
			<?php 	
				if(count($dataimages)!=0) 
				{
			 ?>
					<table class="table table-striped table-hover" style="margin-top: 30px;" id="tableimage" name="tableimage">
						<thead>
							<tr>
								<th><?php echo $labelname[6]; ?></th>
								<th><?php echo $labelname[13]; ?></th>
								<th><?php echo $labelname[12]; ?></th>
								<th>Delete</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($dataimages as $dataimage):
									echo '<tr>';
									echo '<td>'.$dataimage->pict_seq_no.'</td>';
									echo '<td> <img src="'.$dataimage->pict_file_path.$dataimage->pict_file_name.'" class="img-responsive" alt="" style="width: 200px; height: 137px;"> </td>';
									echo '<td> '.$dataimage->pict_definition.' </td>';
									echo '<td> <a href="#" data-toggle="modal" class="modaldelete" data-imgid="'.$dataimage->data_entry_pict_no.'" data-dataid="'.$dataimage->data_entry_no.'"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td>';
									echo '</tr>';
								endforeach;
							?>
						</tbody>
					</table>
			<?php
				}
			?>
		</fieldset>
		
		<div class="form-group" style="text-align: center;">
		    <input type="submit" class="btn btn-primary btn-sm" value="Save" />
		    <a href="/journaldataentrynonp" class="btn btn-danger btn-sm">Cancel</a>
			
		</div>
	</form>
	<!-- -------------------------------------------- -->
	<!-- pop-up -->
	<div class="modal fade bs-example-modal-lg2" id="MyModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	  	<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
			    	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
			    	<h4 class="modal-title" id="myModalLabel"><?php echo $labelname[11]; ?></h4>
			  	</div>
			  	<div class="modal-body">
			  		<form method=post id=addimage action="<?php echo base_url(); ?><?php echo $cpagename; ?>/addimage/" enctype="multipart/form-data">
			  			<div class="form-group">
							<div class="col-xs-4" style="text-align: right;"></div>
							<div class="col-xs-5"><label id="errorimage" class="text-danger"></label></div>
					  	</div>
				  		<br>
						<div class="form-group">
							<div class="col-xs-4" style="padding-left: 210px; text-align: right;"><label for="exampleInputFile" class="col-lg-3 control-label"><?php echo $labelname[11]; ?></label> <red>*</red></div>
							<div class="col-xs-5"><input type="file" id="imagefile" name="imagefile"><input type="hidden" id="dataentryno1" name="dataentryno1" value="<?php echo $dataentryno; ?>" /></div>
					  	</div>
				  		<br>
				  		<div class="form-group">
							<div class="col-xs-4" style="text-align: right;"></div>
							<div class="col-xs-5"><label id="errordesc" class="text-danger"></label></div>
					  	</div>
				  		<br>
					  	<div class="form-group">
							<div class="col-xs-4" style="text-align: right;"><label for="exampleInputEmail1" class="control-label"><?php echo $labelname[12]; ?></label> <red>*</red></div>
							<div class="col-xs-5"><textarea class="form-control" rows="3" id="imagedesc" name="imagedesc"></textarea></div>
					  	</div>
					  	<br>
					  	<br>
					  	<br>
					  	<br>
				  		<div class="modal-footer">
				    		<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
				    		<input type="submit" class="btn btn-primary btn-sm" value="Save" />
				  		</div>
				  	</form>
				</div>
			</div>
		</div>
	</div>
	<!-- close pop-up-->
</div>
