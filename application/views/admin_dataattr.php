<script>
	$(document).ready(function()
	{
//        $('#prg_da').DataTable();
        var oTable = $('#prg_da').dataTable({
            "order": [[ 0, "asc" ]],
            "columnDefs": [ {
                "targets"  : 'no-sort',
                "orderable": false
            }]
        });

        $('div.dataTables_filter input').attr('placeholder', 'Enter the text here');

		$(".modal-body #inputtype").change(function()
		{
            if($(this).val() ==1)
            {
            	$(".modal-body #lookup").attr('disabled','disabled');
				$(".modal-body #datatype").removeAttr('disabled');
				$(".modal-body #datatype").val( '1' )
				$(".modal-body #decimaldigits").attr('disabled','disabled');
				$(".modal-body #decimaldigits").val( '' );
				$(".modal-body #uom").removeAttr('disabled');
				$(".modal-body #uom").val( '1' );
				$(".modal-body #fieldlock").prop('checked', false);
				$(".modal-body #fieldlock").attr('disabled','disabled');
            }
			else if($(this).val() ==2)
            {
				$(".modal-body #lookup").prop('selectedIndex',0);
                $(".modal-body #lookup").removeAttr('disabled');
				$(".modal-body #datatype").attr('disabled','disabled');
				$(".modal-body #decimaldigits").attr('disabled','disabled');
				$(".modal-body #decimaldigits").val( '' );
				$(".modal-body #uom").removeAttr('disabled');
				$(".modal-body #uom").val( '1' );
				$(".modal-body #fieldlock").prop('checked', false);
				$(".modal-body #fieldlock").attr('disabled','disabled');
            }
			else if($(this).val() ==3)
            {
                $(".modal-body #lookup").attr('disabled','disabled');
				$(".modal-body #lookup").val('');
				$(".modal-body #datatype").val( '1' )
				$(".modal-body #datatype").attr('disabled','disabled');
				$(".modal-body #decimaldigits").attr('disabled','disabled');
				$(".modal-body #decimaldigits").val( '' );
				$(".modal-body #uom").removeAttr('disabled');
				$(".modal-body #uom").val( '1' );
				$(".modal-body #fieldlock").removeAttr('disabled');
            }
            else if($(this).val() ==4)
            {
                $(".modal-body #lookup").attr('disabled','disabled');
				$(".modal-body #datatype").attr('disabled','disabled');
				$(".modal-body #decimaldigits").attr('disabled','disabled');
				$(".modal-body #decimaldigits").val( '' );
				$(".modal-body #lookup").val('');
				$(".modal-body #datatype").val('');
				$(".modal-body #uom").val('');
				$(".modal-body #uom").attr('disabled','disabled');
				$(".modal-body #fieldlock").removeAttr('disabled');
            }
     	});

     	$(".modal-body #datatype").change(function()
		{
            if($(this).val() ==3)
            {
				$(".modal-body #decimaldigits").removeAttr('disabled');
				$(".modal-body #decimaldigits").val( '' );
            }
            else
            {
				$(".modal-body #decimaldigits").attr('disabled','disabled');
				$(".modal-body #decimaldigits").val( '' );
            }
     	});

     	$(".modal-body #inputtype1").change(function()
		{
			if($(this).val() ==1)
            {
            	$(".modal-body #lookup1").attr('disabled','disabled');
				$(".modal-body #datatype1").removeAttr('disabled');
				$(".modal-body #datatype1").val( '1' )
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").val( '' );
				$(".modal-body #uom1").removeAttr('disabled');
				$(".modal-body #fieldlock1").prop('checked', false);
				$(".modal-body #fieldlock1").attr('disabled','disabled');
            }
			else if($(this).val() ==2)
            {
                $(".modal-body #lookup1").removeAttr('disabled');
				$(".modal-body #datatype1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").val( '' );
				$(".modal-body #uom1").removeAttr('disabled');
				$(".modal-body #fieldlock1").prop('checked', false);
				$(".modal-body #fieldlock1").attr('disabled','disabled');
            }
			else if($(this).val() ==3)
            {
                $(".modal-body #lookup1").attr('disabled','disabled');
				$(".modal-body #datatype1").val( '1' )
				$(".modal-body #datatype1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").val( '' );
				$(".modal-body #uom1").removeAttr('disabled');
				$(".modal-body #uom1").val( '1' );
				$(".modal-body #fieldlock1").removeAttr('disabled');
            }
            else if($(this).val() ==4)
            {
                $(".modal-body #lookup1").attr('disabled','disabled');
				$(".modal-body #datatype1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").val( '' );
				$(".modal-body #uom1").attr('disabled','disabled');
				$(".modal-body #fieldlock1").removeAttr('disabled');
            }
     	});

     	$(".modal-body #datatype1").change(function()
		{
            if($(this).val() ==3)
            {
				$(".modal-body #decimaldigits1").removeAttr('disabled');
				$(".modal-body #decimaldigits").val( '' );
            }
            else
            {
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #decimaldigits").val( '' );
            }
     	});

		$("#modaladd").click(function ()
		{
			var empty="";
			$(".modal-body #label").val( empty );
			$(".modal-body #inputtype").val( '1' );
			$(".modal-body #datatype").val( '1' );
			$(".modal-body #lookup").attr('disabled','disabled');
			$(".modal-body #datatype").removeAttr('disabled');
			$(".modal-body #decimaldigits").attr('disabled','disabled');
			$(".modal-body #decimaldigits").val( empty );
			$(".modal-body #fieldlock").prop('checked', false);
			$(".modal-body #errorlabel").html( empty );
			$(".modal-body #errorinputtype").html( empty );
			$(".modal-body #errorlookup").html( empty );
			$(".modal-body #errordatatype").html( empty );
			$(".modal-body #errordecimal").html( empty );
			$(".modal-body #erroruom").html( empty );
			
		});

		$('#addrecord').submit(function()
		{
			$.post($('#addrecord').attr('action'), $('#addrecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					hideloader();
		 			$('#errorlabel').html(data.msg);
		 			$('#errordecimal').html(data.msg1);
		 			$('#errorlookup').html(data.msg2);
		 			$('#errordatatype').html(data.msg3);
		 			$('#erroruom').html(data.msg4);
					$('#errorattbgroup').html(data.msg5);
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
			var labelname = $(this).data('label');
		    var inputtype = $(this).data('attbtype');
			var attbgroup = $(this).data('attbgroup');
			var lookup = $(this).data('lookup');
			var datatype = $(this).data('attbdatatype');
			var decimal = $(this).data('digit');
			var uom = $(this).data('uom');
			var datasetid = $(this).data('attbid');
			var fieldlock = $(this).data('fieldlock');

			$(".modal-body #decimaldigits1").val( decimal );
			if(inputtype==1)
			{
				$(".modal-body #lookup1").attr('disabled','disabled');
				$(".modal-body #datatype1").removeAttr('disabled');
				if(datatype!=3)
				{
					$(".modal-body #decimaldigits1").attr('disabled','disabled');
					$(".modal-body #decimaldigits1").val( '' );
				}
				else
				{
					$(".modal-body #decimaldigits1").removeAttr('disabled');
				}
				$(".modal-body #fieldlock1").prop('checked', false);
				$(".modal-body #fieldlock1").attr('disabled','disabled');
			} 
			else if(inputtype==2)
			{
				$(".modal-body #datatype1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #lookup1").removeAttr('disabled');
				$(".modal-body #decimaldigits1").val( '' );
				$(".modal-body #fieldlock1").prop('checked', false);
				$(".modal-body #fieldlock1").attr('disabled','disabled');
			}
			else if (inputtype==3)
			{
				$(".modal-body #datatype1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").val( '' );
				$(".modal-body #lookup1").attr('disabled','disabled');
				$(".modal-body #uom1").removeAttr('disabled');
				$(".modal-body #fieldlock1").removeAttr('disabled');
			}
			else
			{
				$(".modal-body #datatype1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").attr('disabled','disabled');
				$(".modal-body #decimaldigits1").val( '' );
				$(".modal-body #lookup1").attr('disabled','disabled');
				$(".modal-body #uom1").attr('disabled','disabled');
				$(".modal-body #fieldlock1").removeAttr('disabled');
			}
			var empty="";
			$(".modal-body #label1").val( labelname );
			$(".modal-body #inputtype1").val( inputtype );
			$(".modal-body #attbgroup1").val( attbgroup );
			$(".modal-body #lookup1").val( lookup );
			$(".modal-body #datatype1").val( datatype );
			$(".modal-body #uom1").val( uom );
			$(".modal-body #datasetid").val(datasetid);
			if(fieldlock==0)
			{
				$(".modal-body #fieldlock1").prop('checked', false);
			}
			else
			{
				$(".modal-body #fieldlock1").prop('checked', true);
			}
			$(".modal-body #errorlabel1").html( empty );
			$(".modal-body #errorinputtype1").html( empty );
			$(".modal-body #errorlookup1").html( empty );
			$(".modal-body #errordatatype1").html( empty );
			$(".modal-body #errordecimal1").html( empty );
			$(".modal-body #erroruom1").html( empty );
			$(".modal-body #errorattbgroup1").html( empty );

		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
					// hideloader();
		 			$('#errorlabel1').html(data.msg);
		 			$('#errordecimal1').html(data.msg1);
		 			$('#errorlookup1').html(data.msg2);
		 			$('#errordatatype1').html(data.msg3);
		 			$('#erroruom1').html(data.msg4);
					$('#errorattbgroup1').html(data.msg5);
				}
				else if(data.st == 1)
				{
					// hideloader();
		  			location.reload();
				}

			}, 'json');
			return false;
   		});

   		$(document).on("click", ".modaldelete", function ()
		{
			if(confirm("Do you want to delete?"))
			{
				var id = $(this).data('attbid');
				$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/delete",{id:id}, function( data ) {
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
		<div class="col-md-4">
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
						    	<div class="col-sm-6">
						    		<label id="errorlabel" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-4 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-6">
						    		<input type="text" class="form-control" id="label" name="label" placeholder="Enter the Label name" maxlength="60">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorinputtype" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="inputtype" name="inputtype">
                        				<?php
											foreach ($inputtypes as $inputtype):
										?>
												<option value="<?php echo $inputtype->data_attb_type_id; ?>"><?php echo $inputtype->data_attb_type_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorattbgroup" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="attbgroup" name="attbgroup">
                        				<?php
											foreach ($attbgroups as $attbgroup):
										?>
												<option value="<?php echo $attbgroup->data_attribute_group_id; ?>"><?php echo $attbgroup->data_attribute_group_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorlookup" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[3]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="lookup" name="lookup">
										<?php
											foreach ($lookups as $lookup):
										?>
												<option value="<?php echo $lookup->data_set_id; ?>"><?php echo $lookup->lk_code; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errordatatype" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[4]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="datatype" name="datatype">
                        				<?php
											foreach ($datatypes as $datatype):
										?>
												<option value="<?php echo $datatype->data_attb_data_type_id; ?>"><?php echo $datatype->data_attb_data_type_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errordecimal" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[5]; ?><red>*</red></label>
								<div class="col-sm-3">
									<input type="text" class="form-control" id="decimaldigits" name="decimaldigits" placeholder="" maxlength="1">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroruom" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[6]; ?><red>*</red></label>
								<div class="col-sm-5">
									<select class="form-control" id="uom" name="uom">
										<?php
											foreach ($uoms as $uom):
										?>
												<option value="<?php echo $uom->uom_id; ?>"><?php echo $uom->uom_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
<!--                        Hidden by : Agaile as suggested by ZUL on 02/06/2016-->
<!--						<div class="row">-->
<!--							<div class="form-group">-->
<!--								<label for="search" class="col-sm-4 control-label">--><?php //echo $labelname[7]; ?><!--</label>-->
<!--								<div class="col-sm-3">-->
<!--									<input type="checkbox" id="fieldlock" name="fieldlock" disabled="true" />-->
<!--								</div>-->
<!--							</div>-->
<!--						</div>-->
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
						    	<div class="col-sm-6">
						    		<label id="errorlabel1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-4 control-label"><?php echo $labelname[0]; ?><red>*</red></label>
						    	<div class="col-sm-6">
						    		<input type="hidden" class="form-control" id="datasetid" name="datasetid">
						    		<input type="text" class="form-control" id="label1" name="label1" placeholder="Enter the Span Value" maxlength="60">
						        </div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorinputtype1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[1]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="inputtype1" name="inputtype1">
                        				<?php
											foreach ($inputtypes as $inputtype):
										?>
												<option value="<?php echo $inputtype->data_attb_type_id; ?>"><?php echo $inputtype->data_attb_type_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorattbgroup1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[2]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="attbgroup1" name="attbgroup1">
                        				<?php
											foreach ($attbgroups as $attbgroup):
										?>
												<option value="<?php echo $attbgroup->data_attribute_group_id; ?>"><?php echo $attbgroup->data_attribute_group_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorlookup1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[3]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="lookup1" name="lookup1">
										<?php
											foreach ($lookups as $lookup):
										?>
												<option value="<?php echo $lookup->data_set_id; ?>"><?php echo $lookup->lk_code; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errordatatype1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[4]; ?><red>*</red></label>
								<div class="col-sm-6">
									<select class="form-control" id="datatype1" name="datatype1">
                        				<?php
											foreach ($datatypes as $datatype):
										?>
												<option value="<?php echo $datatype->data_attb_data_type_id; ?>"><?php echo $datatype->data_attb_data_type_desc; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errordecimal1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[5]; ?><red>*</red></label>
								<div class="col-sm-3">
									<input type="text" class="form-control" id="decimaldigits1" name="decimaldigits1" maxlength="1">
								</div>
							</div>
						</div>
						<div class="row">
    						<div class="form-group">
    							<label for="search" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-5">
						    		<label id="erroruom1" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[6]; ?><red>*</red></label>
								<div class="col-sm-5">
									<select class="form-control" id="uom1" name="uom1">
										<?php
											foreach ($uoms as $uom):
										?>
												<option value="<?php echo $uom->uom_id; ?>"><?php echo $uom->uom_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="form-group">
								<label for="search" class="col-sm-4 control-label"><?php echo $labelname[7]; ?></label>
								<div class="col-sm-3">
									<input type="checkbox"  id="fieldlock1" name="fieldlock1" />
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<input type="submit" class="btn btn-primary btn-sm" value="Save Changes" />
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
		<table class="table table-striped table-hover" id="prg_da">
	        <thead>
    			<tr>
      				<th>No</th>
			        <th><?php echo $labelname[0]; ?></th>
				  	<th><?php echo $labelname[1]; ?></th>
					<th><?php echo $labelname[2]; ?></th>
					<th><?php echo $labelname[3]; ?></th>
					<th><?php echo $labelname[4]; ?></th>
					<th><?php echo $labelname[5]; ?></th>
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
				  			<td><?php echo html_escape($record->data_attb_label); ?></td>
				  			<td><?php echo html_escape($record->data_attb_type_desc); ?></td>
							<td><?php echo html_escape($record->data_attribute_group_desc); ?></td>
				  			<td><?php echo html_escape($record->lk_code); ?></td>
				  			<td><?php echo html_escape($record->data_attb_data_type_desc); ?></td>
				  			<td><?php echo html_escape($record->uom_name); ?></td>
	      					<td>
								<?php
									if($editperm==1)
									{
								?>
										<a href="#" data-toggle="modal" data-target="#myModal1" class="modaledit" data-attbid="<?php echo html_escape($record->data_attb_id); ?>" data-label="<?php echo html_escape($record->data_attb_label); ?>" data-attbtype="<?php echo html_escape($record->data_attb_type_id); ?>" data-lookup="<?php echo html_escape($record->data_set_id); ?>" data-attbdatatype="<?php echo html_escape($record->data_attb_data_type_id); ?>" data-digit="<?php echo html_escape($record->data_attb_digits); ?>" data-attbgroup="<?php echo html_escape($record->data_attribute_group_id); ?>" data-uom="<?php echo html_escape($record->uom_id); ?>" data-fieldlock="<?php echo html_escape($record->field_lock); ?>" ><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
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
										<a href="#" data-toggle="modal" class="modaldelete" data-attbid="<?php echo html_escape($record->data_attb_id); ?>"><span class="glyphicon glyphicon-trash">&nbsp;</span></a>
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
						echo '<tr><td class="row text-center text-danger" colspan="8"> No Record Found</td></tr></tbody></table>';
					}
					else
					{
				?>
  			</tbody>
		</table>
	</div>
	<div class="row">
		<div class="col-md-12">
<!--			<div class="col-md-4">-->
<!--				<ul class="pagination">-->
<!--                	--><?php //echo $this->pagination->create_links(); ?>
<!--				</ul>-->
<!--			</div>-->
<!--			<div class="col-md-4 col-md-offset-1" >-->
<!--				<div class="form-group">-->
<!--					<label for="search" class="col-sm-2 control-label" style="padding-top: 15px; padding-bottom: 5px;">Show</label>-->
<!--					<div class="col-sm-3" style="padding-top: 15px; padding-bottom: 5px;">-->
<!--						<select class="form-control" id="recordselect" name="recordselect">-->
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