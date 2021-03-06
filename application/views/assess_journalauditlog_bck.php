<script>
	$(document).ready(function()
	{
		$(document).on("click", ".modaledit", function ()
				{
					var pname = $(this).data('pname');
					var jname = $(this).data('jname');
					var fname = $(this).data('fname');
					var lname = "Level " + $(this).data('lname');
					var audlog = $(this).data('auditlog');

					$(".modal-body #pname").html( pname );
					$(".modal-body #jname").html( jname );
					$(".modal-body #fname").html( fname );
					$(".modal-body #lname").html( lname );
					$("#myTable tbody").empty();
			var userdat1 = audlog.split(',777,');

			for (var i = 0; i < userdat1.length-1; i++)
			{
				var content="";
				var userdat2 = userdat1[i].split(',');
				 sno = i+1;
				content += '<tr>';
										content += '<td>'+sno+'</td>';
										content += '<td>'+userdat2[0]+'</td>';
										content += '<td>'+userdat2[1]+'</td>';
										content += '<td>'+userdat2[2]+'</td>';
										content += '<td>'+userdat2[3]+'</td>';
										content += '<td>'+userdat2[4]+'</td>';
										content += '<td>'+userdat2[5]+'</td>';
										content += '<td>'+userdat2[6]+'</td>';
										content += '</tr>';
										$("#myTable").append(content);



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
			$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord",{search:search}, function( data ) {
				location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
			});
	    });

	    $("#exporttoexcel").click(function () {
		  var htmltable= document.getElementById('myTable');
			var html = htmltable.outerHTML;
       window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
   });
	});
</script>
<div class="container">

<div class="page-header">
              <h1 id="nav">Project Journal Audit Log</h1>
</div>
<div class="row">
		<div class="col-md-4">
			<ul class="breadcrumb">
            	<li><a href="<?php echo base_url(); ?>home">Home</a></li>
                <li>Assessment</li>
                <li class="active">Project Journal Audit Log</li>
       		</ul>
		</div>
	</div>
<!-- BUAT CODING DALAM WRAP-->


<!-- INPUT HERE-->


<!-- ---------------------- -->

<div class="form-group">
		<label for="search" class="col-sm-1 control-label">Search</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" id="search" name="search" value="<?php echo $searchrecord; ?>" placeholder="Enter the text here">
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
	</div>

<table class="table table-striped table-hover ">
    <thead>
        <tr>
            <th>No</th>
            <th>Project Template</th>
            <th>Journal</th>
            <th>Week</th>
			<th>Level</th>
			<th>View</th>
        </tr>
    </thead>
    <tbody>
    			<?php
							$sno=1;
							foreach ($records as $aulog):

				?>
				<tr>
				            <td><?php echo $sno; ?></td>
							<td><?php echo $aulog->project_name; ?></td>
				            <td><?php echo $aulog->journal_name; ?></td>
				            <td><?php echo $aulog->frequency_detail_name; ?></td>
							<td>Level <?php echo $aulog->validate_level_no; ?></td>
							<td>
							<?php
							//split the array as comma seperated value
							$audlogdat=$audlog[$aulog->data_entry_no];

							?>
				            <a href="#" data-toggle="modal" class="modaledit" data-target=".bs-example-modal-lg2" data-pname="<?php echo $aulog->project_name; ?>" data-jname="<?php echo $aulog->journal_name; ?>" data-fname="<?php echo $aulog->frequency_detail_name; ?>" data-lname="<?php echo $aulog->validate_level_no; ?>" data-auditlog="<?php echo $audlogdat; ?>"><span class="glyphicon glyphicon-edit">&nbsp;</span></a>

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
			<div class="col-md-3" style="padding-top: 22px;"> Showing <?php echo $page; ?> to <?php echo $end; ?> of <?php echo $totalrows; ?> rows</div>
		</div>
		<?php }?>
</div>


</div>

<!-- -------------------------------------------- -->
<!-- pop-up -->
<div class="modal fade bs-example-modal-lg2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Project Journal Data Audit Log</h4>
      </div>
      <div class="modal-body">

      <div class="row" style="width: 70%; margin: auto;">
		  <div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Data Log For</b></div>
		  <div class="col-xs-8" id="fname" name=fname" style="color: blue; margin-bottom: 8px;"></div>
		  <div class="col-xs-4"  style="text-align: right; margin-bottom: 8px;"><b>Project Name</b></div>
		  <div class="col-xs-8"  id="pname" name="pname" style="color: blue; margin-bottom: 8px;"></div>
		  </br>
		  <div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Journal Name</b></div>
		  <div class="col-xs-8" id="jname" name="jname" style="color: blue; margin-bottom: 8px;"></div>
		  </br>
		  <div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Level</b></div>
		  <div class="col-xs-8" id="lname" name="lname" style="color: blue; margin-bottom: 8px;"></div>
		  </br>
		  <a href="javascript:window.print()")><img src="<?php echo base_url(); ?>img/print.png" class="img-responsive" alt="Responsive image" style="width: 50px; height: 50px; float: left;"></a>

		  <a><img src="<?php echo base_url(); ?>img/excel.ico" id="exporttoexcel" name="exporttoexcel" class="img-responsive"  alt="Responsive image" style="width: 40px; height: 40px; float: right;"></a>
		  <a><img src="<?php echo base_url(); ?>img/pdf.png" class="img-responsive" alt="Responsive image" style="width: 50px; height: 40px; float: right; padding-right: 10px;"></a>
			<table class="table table-striped table-hover" id="myTable" name="myTable" style="width: 100%;">
					<thead>
						<tr>
							<th>No</th>
							<th>Data Attributes</th>
							<th>Current Value</th>
							<th>Current User</th>
							<th>Current Date</th>
							<th>Previous Value</th>
							<th>Previous User</th>
							<th>Previous Date</th>
						</tr>
					</thead>
<tbody>
</tbody>
				</table>
		  </div>
		</div>

      <br>

      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>

    </div>
  </div>
</div>
<!-- close pop-up-->
<!-- -------------------------------------------- -->

</div>


