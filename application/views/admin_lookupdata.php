<script>
	$(document).ready(function()
	{
//        $('#lookup').DataTable();
        var oTable = $('#lookup').dataTable({
        "order": [[ 0, "asc" ]],
        "columnDefs": [ {
            "targets"  : 'no-sort',
            "orderable": false
        }]
    });

        $('div.dataTables_filter input').attr('placeholder', 'Enter the text here');

		$("#modaladd").click(function ()
		{
			var empty = "";
			$(".modal-body #code").val( empty );
			$(".modal-body #data").val( empty );
			$(".modal-body #value").val( empty );
			$(".modal-body #errorcode").html( empty );
			$(".modal-body #errordata").html( empty );
			$(".modal-body #errorvalue").html( empty );
		});

		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#errorcode').html(data.msg);
		 			$('#errordata').html(data.msg1);
					$('#errorvalue').html(data.msg2);
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
			var datasetid = $(this).data('datasetid');
			var datadetailid = $(this).data('datadetailid');
		    var code = $(this).data('code');
			var data = $(this).data('data');
			var value = $(this).data('value');
			var empty="";
			$(".modal-body #datasetid").val( datasetid );
			$(".modal-body #datadetailid").val( datadetailid );
			$(".modal-body #code1").val( code );
			$(".modal-body #data1").val( data );
			$(".modal-body #value1").val( value );
			$(".modal-body #errorcode1").html( empty );
			$(".modal-body #errordata1").html( empty );
			$(".modal-body #errorvalue1").html( empty );
		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#errorcode1').html(data.msg);
		 			$('#errordata1').html(data.msg1);
					$('#errorvalue1').html(data.msg2);
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
				var datasetid = $(this).data('datasetid');
				var datadetailid = $(this).data('datadetailid');
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/delete",{id:datasetid,id1:datadetailid}, function( data ) {
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
<!--		<label for="search" class="col-sm-1 control-label">Search</label>-->
<!--		<div class="col-sm-4">-->
<!--			<input type="text" class="form-control" id="search" name="search" value="--><?php //echo $searchrecord; ?><!--" placeholder="Enter the text here">-->
<!--		</div>-->
<!--		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />-->
<!--		<a href="--><?php //echo base_url(); ?><!----><?php //echo $cpagename; ?><!--" class="btn btn-danger btn-sm">Clear</a>-->
		<button type="button" class="btn btn-success btn-sm pull-right" id="modaladd" name="modaladd" data-toggle="modal" data-target="#myModal" <?php if($addperm==0) echo 'disabled="true"'; ?>>Add New</button>
	</div>
	<!-- -------------------------------------------- -->
	<!-- pop-up -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method=post id=addrecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Add New <?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorcode" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-5">
						    		<input type="text" class="form-control" id="code" name="code" placeholder="Code" maxlength="10">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errordata" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="data" name="data" placeholder="Data" maxlength="60">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorvalue" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="value" name="value" placeholder="Value" maxlength="10">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Add New" onclick="showloader();" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--close pop-up-->
	<!-- pop-up -->
	<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method=post id=updaterecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update/">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Edit <?php echo $labelobject; ?></h4>
					</div>
					<div class="modal-body">
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-8">
						    		<label id="errorcode1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-5">
									<input type="hidden" class="form-control" id="datasetid" name="datasetid">
									<input type="hidden" class="form-control" id="datadetailid" name="datadetailid">
						    		<input type="text" class="form-control" id="code1" name="code1" placeholder="Code" maxlength="10">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errordata1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="data1" name="data1" placeholder="Data" maxlength="60">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="errorvalue1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-3 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="value1" name="value1" placeholder="Value" maxlength="10">
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" onclick="showloader();" />
					</div>
				</form>
			</div>
		</div>
	</div>
	<!--close pop-up-->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
    <div>&nbsp;</div>
	<div class="row">
<!--        --><?php
//        echo '<pre>';
//        print_r($records);
//        echo '<pre>';
//        ?>
		<table class="table table-striped table-hover" id="lookup">
	        <thead>
    			<tr>
      				<th>No</th>
			        <th><?php echo $labelname[0]; ?></th>
			        <th><?php echo $labelname[1]; ?></th>
			        <th  style="text-align: center;"><?php echo $labelname[2]; ?></th>
			        <th>Edit</th>
			        <th>Delete</th>
    			</tr>
    		</thead>
    		<tbody>
    			<?php
					$sno=$page;
					foreach ($records as $record):
				?>
						<tr>
				  			<td><?php echo $sno; ?></td>
				  			<td><?php echo $record->lk_code; ?></td>
				  			<td><?php echo $record->lk_data; ?></td>
<!--                            Modified by agaile as per requirements on 20/11/2015 task #9605-->
				  			<td style="text-align: center;"><?php $a = explode(".",$record->lk_value); echo $a[0]; ?></td>
<!--				  			<td  style="text-align: center;">--><?php //echo $record->lk_value; ?><!--</td>-->
	      					<td>
								<?php
									if($editperm==1)
									{
								?>
										<a href="#" data-toggle="modal" data-target="#myModal1" class="modaledit" data-datasetid="<?php echo $record->data_set_id; ?>" data-datadetailid="<?php echo $record->data_set_detail_id; ?>" data-code="<?php echo $record->lk_code; ?>" data-value="<?php echo $record->lk_value; ?>" data-data="<?php echo $record->lk_data; ?>" ><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
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
										<a href="#" data-toggle="modal" class="modaldelete" data-datasetid="<?php echo $record->data_set_id; ?>" data-datadetailid="<?php echo $record->data_set_detail_id; ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
								<?php
									}
									else
									{
										echo '<span class="glyphicon glyphicon-trash">&nbsp;</span>';
									}
								?>
							</td>
						</tr>
				<?php
					$sno=$sno+1;
					endforeach;
					if($totalrows==0)
					{
						echo '<tr><td class="row text-center text-danger" colspan="6"> No Record Found</td></tr></tbody></table>';
					}
					else
					{
				?>
  			</tbody>
		</table>
	</div>
	<div class="row">
<!--		<div class="col-md-12">-->
<!--			<div class="col-md-4">-->
<!--				<ul class="pagination">-->
<!--                	--><?php //echo $this->pagination->create_links(); ?>
<!--				</ul>-->
<!--			</div>-->
<!--			<div class="col-md-4 col-md-offset-1" >-->
<!--				<div class="form-group">-->
<!--					<label for="search" class="col-sm-2 control-label" style="padding-top: 15px; padding-bottom: 5px;">Show</label>-->
<!--					<div class="col-sm-3" style="padding-top: 15px; padding-bottom: 5px;">-->
<!--						<select class="form-control" id="recordselect" name="recordselect" >-->
<!--						</select>-->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
<!--			--><?php
//				// Display the number of records in a page
//				$end=$mpage+$page-1;
//				if($totalrows<$end) $end=$totalrows;
//			?>
<!--			<div class="col-md-3" style="padding-top: 22px;"> Showing --><?php //echo $page; ?><!-- to --><?php //echo $end; ?><!-- of --><?php //echo $totalrows; ?><!-- rows  </div>-->
<!--		</div>-->
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