<script>
	$(document).ready(function()
	{
        var oTable = $('#change_deo').dataTable({

        });

		$(document).on("click", ".modaledit", function ()
		{
			var pname = $(this).data('pname');
			var jname = $(this).data('jname');
			var uname = $(this).data('uname');
			var jid = $(this).data('jid');
			var type1 = $(this).data('type1');
			var empty ="";
			$(".modal-body #pname").html( pname );
			$(".modal-body #jname").html( jname );
			$(".modal-body #owner").html( uname );
			$(".modal-body #jid").val( jid );
			$(".modal-body #type1").val( type1 );

			var	userdat = $(this).data('juserid');
			var userdat1 = userdat.split(',777,');
			$('.modal-body #selowner').empty();
			for (var i = 0; i < userdat1.length-1; i++)
			{
				var userdat2 = userdat1[i].split(',');
				$(".modal-body #selowner").append($("<option>").attr("value", userdat2[0]).text(userdat2[1]));

			}

		});

		$('#updaterecord').submit(function()
		{
			$.post($('#updaterecord').attr('action'), $('#updaterecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$('#erroruom1').html(data.msg);
		 			$('#erroruomdesc1').html(data.msg1);
				}
				if(data.st == 1)
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
	<!--<div class="form-group">
		<label for="search" class="col-sm-1 control-label">Search</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" id="search" name="search" value="<?php /*echo $searchrecord; */?>" placeholder="Enter the text here">
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php /*echo base_url(); */?><?php /*echo $cpagename; */?>" class="btn btn-danger btn-sm">Clear</a>
		</div>-->


<!-- pop-up -->
<div class="modal fade bs-example-modal-md3" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $labelobject; ?></h4>
      </div>
      <form method=post id=updaterecord action="<?php echo base_url(); ?><?php echo $cpagename; ?>/update/">
      <div class="modal-body">

      <div class="form-group">
        <label for="select" class="col-lg-4 control-label"><?php echo $labelname[4]; ?></label>
        <div class="col-lg-8" id="pname" name="pname">

        </div>
      </div>

      <br>

      <div class="form-group">
        <label for="select" class="col-lg-4 control-label"><?php echo $labelname[5]; ?></label>
        <div class="col-lg-8" id="jname" name="jname">

        </div>
      </div>

	  <br>

      <div class="form-group">
        <label for="select" class="col-lg-4 control-label"><?php echo $labelname[6]; ?></label>
        <div class="col-lg-8" id="owner" name="owner">

        </div>
      </div>

	  <br>

      <div class="form-group">
        <label for="select" class="col-lg-4 control-label"><?php echo $labelname[7]; ?> <red>*</red></label>
          <div class="col-lg-8">
              <select class="form-control" id="selowner" name="selowner">
              </select>
        </div>
      </div>

      <br>


      <div class="modal-footer" style=text-align:center;>
		<input type="hidden" id='jid' name='jid' value="">
		<input type="hidden" id='type1' name='type1' value="">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <input type="submit" class="btn btn-primary btn-sm" value="Save" />
      </div>

    </div>
</form>
    </div>
  </div>
</div>
</div>

<!-- ----- -->



<div class="row text-center text-success"><?php echo $message; ?> </div>
<div class="row">
<table class="table table-striped table-hover" id="change_deo">
    <thead>
	<tr>
		<th>No</th>
		<th><?php echo $labelname[0]; ?></th>
		<th><?php echo $labelname[1]; ?></th>
		<th><?php echo $labelname[2]; ?></th>
		<th><?php echo $labelname[3]; ?></th>
	</tr>
    </thead>
	<?php
						$sno=1;

						foreach ($records as $chdeo):
					?>
							<tr>
								<td><?php echo $sno; ?></td>
								<td><?php echo $chdeo->project_name; ?></td>
								<td><?php echo $chdeo->journal_name; ?></td>
								<td><?php echo $chdeo->user_full_name; ?></td>
								<td>
									<?php
									//split the array as comma seperated value
									$chdeodat=$chdeousr[$chdeo->journal_no];

										if($editperm==1 && $chdeodat!='')
										{
									?>
											<a href="#" data-toggle="modal" class="modaledit" data-target=".bs-example-modal-md3" data-pname="<?php echo $chdeo->project_name; ?>" data-jname="<?php echo $chdeo->journal_name; ?>" data-uname="<?php echo $chdeo->user_full_name; ?>" data-jid="<?php echo $chdeo->journal_no; ?>" data-type="<?php echo $chdeo->type; ?>" data-juserid="<?php echo $chdeodat;?>"><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
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
							echo '<tr><td class="row text-center text-success" colspan="5"> No Record Found</td></tr></tbody></table>';
						}
						else
						{
				?>



</table>

<div class="row">

<!--<div class="col-md-12">
<div class="col-md-4">
<ul class="pagination">
                <?php /*echo $this->pagination->create_links();*/ ?>
</ul>
</div>
<div class="col-md-4 col-md-offset-1" >
	    <div class="form-group">
        <label for="search" class="col-sm-2 control-label" style="padding-top: 22px;">Show</label>
        <div class="col-sm-3" style="padding-top: 14px;">
        <select class="form-control" id="recordselect" name="recordselect">
		                    		<option <?php /*if($selectrecord=="10") echo "selected=selected";*/ ?>>10</option>
			                        <option <?php /*if($selectrecord=="20") echo "selected=selected";*/ ?>>20</option>
			                        <option <?php /*if($selectrecord=="40") echo "selected=selected";*/ ?>>40</option>
                  		</select>
        </div>

      </div>
      </div>
      <?php
	  				 // Display the number of records in a page
	  				/* $end=$mpage+$page-1;
	  				if($totalrows<$end) $end=$totalrows;*/
			?>
<div class="col-md-3" style="padding-top: 22px;"> Showing <?php /*echo $page; */?> to <?php /*echo $end;*/ ?> of <?php /*echo $totalrows; */?> rows</div>
</div>-->
<?php }?>
<!--</div>-->
</div>


</div>
