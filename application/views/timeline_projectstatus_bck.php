<script>
	$(document).ready(function()
	{
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

	});
</script>
<div class="container">

<!-- INPUT HERE-->

<div class="page-header">
              <h1 id="nav">Project Status</h1>
</div>


<div class="row">


<div class="col-md-3">
<ul class="breadcrumb" style=" text-align: center; ">
                <li><a href="<?php echo base_url(); ?>home">Home</a></li>
                <li>Timeline</li>
                <li class="active">Project Status</li>
              </ul>
</div>


</div>

<div class="form-group">
		<label for="search" class="col-sm-1 control-label">Search</label>
		<div class="col-sm-4">
			<input type="text" class="form-control" id="search" name="search" value="<?php echo $searchrecord; ?>" placeholder="Enter the text here">
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
		</div>


<table class="table table-striped table-hover" id="status_table">
	<tr>
		<th>No</th>
		<th>Project Template</th>
		<th>Start Date</th>
		<th>End Date</th>
		<th>No of Journals</th>
		<th>Status</th>
	</tr>
	<tbody>

		<?php
					$sno=1;

					foreach ($records as $pstat):
					$startdate=date("d-m-Y", strtotime($pstat->start_date));
					$enddate=date("d-m-Y", strtotime($pstat->end_date));
					//split the array as comma seperated value
					$pstat1=explode(",777,",$projstat[$pstat->project_no]);
					$pstat2=explode(",",$pstat1[0]);
					$perc=round($pstat2[1]);

							?>
									<tr>
										<td><?php echo $sno; ?></td>
										<td><?php echo $pstat->project_name; ?></td>
										<td><?php echo $startdate; ?></td>
								<td><?php echo $enddate; ?></td>
								<td><?php echo $pstat2[0]; ?></td>
								<td><div class="progress progress-striped active">
								<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $perc; ?>%;">
								<div class="progresstext"><?php echo $perc; ?>%</div></div></div>
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
<div class="col-md-4 col-md-offset-1" >
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


</div>



