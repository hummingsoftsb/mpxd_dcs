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

	    $("#exporttoexcel").click(function () {
		  var htmltable= document.getElementById('myTable');
			var html = htmltable.outerHTML;
       window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
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
		<div class="col-md-4">
			<ul class="breadcrumb">
            	<li><a href="<?php echo base_url(); ?>home">Home</a></li>
                <li><?php echo $labelgroup; ?></li>
                <li class="active"><?php echo $labelobject; ?></li>
       		</ul>
		</div>
	</div>
<!-- BUAT CODING DALAM WRAP-->


<!-- INPUT HERE-->


<!-- ---------------------- -->


<table id="journal_list" class="table table-striped table-hover ">
    <thead>
        <tr>
            <th>No</th>
            <th><?php echo $labelname[0]; ?></th>
            <th><?php echo $labelname[1]; ?></th>
            <th><?php echo $labelname[2]; ?></th>
			<th><?php echo $labelname[3]; ?></th>
			<th><?php echo $labelname[4]; ?></th>
        </tr>
    </thead>
    <tbody>
    			<?php
                //print_r($records);
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
							$is_progressive = true;
							if (isset($audlog[$aulog->data_entry_no])) {
								$audlogdat=$audlog[$aulog->data_entry_no];
							} else {
								$is_progressive = false;
							}
							

							if ($is_progressive) {?>
				            <a href="#" data-toggle="modal" class="modaledit" data-target=".bs-example-modal-lg2" data-pname="<?php echo $aulog->project_name; ?>" data-jname="<?php echo $aulog->journal_name; ?>" data-fname="<?php echo $aulog->frequency_detail_name; ?>" data-lname="<?php echo $aulog->validate_level_no; ?>" data-auditlog="<?php echo $audlogdat; ?>"><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
							<?php } else { ?>
							<a href="<?php echo base_url(); ?>index.php/ilyasaudit?jid=<?php echo $aulog->journal_no;?>"><span class="glyphicon glyphicon-edit">&nbsp;</span></a>
							
							<?php } ?>
				            </td>
        </tr>
         <?php
								$sno=$sno+1;
								endforeach;
//								if($totalrows==0)
//								{
									//echo '<tr><td class="row text-center text-danger" colspan="6"> No Record Found</td></tr></tbody></table>';
//								}
			?>
</tbody>
</table>


</div>

<!-- -------------------------------------------- -->
<!-- pop-up -->
<div class="modal fade bs-example-modal-lg2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo $labelobject; ?></h4>
      </div>
      <div class="modal-body">

      <div class="row" style="width: 70%; margin: auto;">
		  <div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[5]; ?></b></div>
		  <div class="col-xs-8" id="fname" name="fname" style="color: blue; margin-bottom: 8px;"></div>
		  <div class="col-xs-4"  style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[6]; ?></b></div>
		  <div class="col-xs-8"  id="pname" name="pname" style="color: blue; margin-bottom: 8px;"></div>
		  </br>
		  <div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[7]; ?></b></div>
		  <div class="col-xs-8" id="jname" name="jname" style="color: blue; margin-bottom: 8px;"></div>
		  </br>
		  <div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[3]; ?></b></div>
		  <div class="col-xs-8" id="lname" name="lname" style="color: blue; margin-bottom: 8px;"></div>
		  </br>
		  <a href="javascript:window.print()")><img src="<?php echo base_url(); ?>img/print.png" class="img-responsive" alt="Responsive image" style="width: 50px; height: 50px; float: left;"></a>

		  <a><img src="<?php echo base_url(); ?>img/excel.ico" id="exporttoexcel" name="exporttoexcel" class="img-responsive"  alt="Responsive image" style="width: 40px; height: 40px; float: right;"></a>

			<table class="table table-striped table-hover" id="myTable" name="myTable" style="width: 100%;">
					<thead>
						<tr>
							<th>No</th>
							<th><?php echo $labelname[8]; ?></th>
							<th><?php echo $labelname[9]; ?></th>
							<th><?php echo $labelname[10]; ?></th>
							<th><?php echo $labelname[11]; ?></th>
							<th><?php echo $labelname[12]; ?></th>
							<th><?php echo $labelname[13]; ?></th>
							<th><?php echo $labelname[14]; ?></th>
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
<script>
$(document).ready(function() {
    var oTable = $('#journal_list').dataTable({
		"order": [[ 0, "asc" ]],
		"columnDefs": [ {
		  "targets"  : 'no-sort',
		  "orderable": false
		}]
	});
	
	$('div.dataTables_filter input').attr('placeholder', 'Enter the text here');
	<?php if ($search != "") { ?> 
		var search = <?php echo json_encode($search); ?>;
		oTable.fnFilter(search); 
		$('td:contains('+search+')').parents('tr').addClass('highlight');
	<?php } ?>
});

</script>