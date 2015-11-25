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

	    $(document).on("click", ".recordsort", function ()
		{
			var search=$(this).data('rsort');

			$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord",{search:search}, function( data ) {
			location.href="<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
			});

		});

	});
</script>
<?php
//echo '<pre>';
//print_r($records);
//echo '</pre>';
//?>
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

<div class="form-group">
		<label for="search" class="col-sm-1 control-label">Search</label>
		<div class="col-sm-4">
			<?php if($searchrecord=="project_name asc" || $searchrecord=="project_name desc" || $searchrecord=="journal_name asc" || $searchrecord=="journal_name desc" || $searchrecord=="user_full_name desc" || $searchrecord=="user_full_name asc" || $searchrecord=="publish_date asc" || $searchrecord=="publish_date desc" || $searchrecord=="frequency_detail_name desc" || $searchrecord=="frequency_detail_name asc" || $searchrecord=="validate_level_no asc" || $searchrecord=="validate_level_no desc") { ?>
			<input type="text" class="form-control" id="search" name="search" value="" placeholder="Enter the text here">
			<?php } else { ?>
			<input type="text" class="form-control" id="search" name="search" value="<?php echo $searchrecord; ?>" placeholder="Enter the text here">
			<?php } ?>
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
	</div>
<div class="row text-center text-success"><?php echo $message; ?> </div>
<table class="table table-striped table-hover ">
    <thead>
        <tr>
            <th>No</th>
            <th>
			<?php if ($searchrecord=="project_name asc")  { ?>
			<a href="javascript:void(0)" data-rsort="project_name desc" class="recordsort"><span class="dropup"><?php echo $labelname[0]; ?><span class="caret"></span></span></a>
			<?php } else { ?>
			<a href="javascript:void(0)" data-rsort="project_name asc" class="recordsort"><?php echo $labelname[0]; ?><span class="caret"></span></a>
			<?php } ?>
			</th>
			<th>
			<?php if ($searchrecord=="journal_name asc")  { ?>
			<a href="javascript:void(0)" data-rsort="journal_name desc" class="recordsort"><span class="dropup"><?php echo $labelname[1]; ?><span class="caret"></span></span></a>
			<?php } else { ?>
			<a href="javascript:void(0)" data-rsort="journal_name asc" class="recordsort"><?php echo $labelname[1]; ?><span class="caret"></span></a>
			<?php } ?>
	            </th>
			<th>
			<?php if ($searchrecord=="frequency_detail_name asc")  { ?>
			<a href="javascript:void(0)" data-rsort="frequency_detail_name desc" class="recordsort"><span class="dropup"><?php echo $labelname[2]; ?><span class="caret"></span></span></a>
			<?php } else { ?>
			<a href="javascript:void(0)" data-rsort="frequency_detail_name asc" class="recordsort"><?php echo $labelname[2]; ?><span class="caret"></span></a>
			<?php } ?>
	        </th>
			<th>
			<?php if ($searchrecord=="user_full_name asc")  { ?>
			<a href="javascript:void(0)" data-rsort="user_full_name desc" class="recordsort"><span class="dropup"><?php echo $labelname[3]; ?><span class="caret"></span></span></a>
			<?php } else { ?>
			<a href="javascript:void(0)" data-rsort="user_full_name asc" class="recordsort"><?php echo $labelname[3]; ?><span class="caret"></span></a>
			<?php } ?>
	        </th>
	        <th>
			<?php if ($searchrecord=="publish_date asc")  { ?>
			<a href="javascript:void(0)" data-rsort="publish_date desc" class="recordsort"><span class="dropup"><?php echo $labelname[4]; ?><span class="caret"></span></span></a>
			<?php } else { ?>
			<a href="javascript:void(0)" data-rsort="publish_date asc" class="recordsort"><?php echo $labelname[4]; ?><span class="caret"></span></a>
			<?php } ?>
	        </th>
			<th>
			<?php if ($searchrecord=="validate_level_no asc")  { ?>
			<a href="javascript:void(0)" data-rsort="validate_level_no desc" class="recordsort"><span class="dropup"><?php echo $labelname[5]; ?><span class="caret"></span></span></a>
			<?php } else { ?>
			<a href="javascript:void(0)" data-rsort="validate_level_no asc" class="recordsort"><?php echo $labelname[5]; ?><span class="caret"></span></a>
			<?php } ?>
	        </th>
			<th><?php echo $labelname[6]; ?></th>
        </tr>
    </thead>
    <tbody>

        <?php
						$sno=1;
						foreach ($records as $valde):
						$publishdate=date("d-m-Y", strtotime($valde->publish_date));
			?>
			<tr>
			            <td><?php echo $sno; ?></td>
						<td><?php echo $valde->project_name; ?></td>
			            <td><?php echo $valde->journal_name; ?></td>
			            <td><?php echo $valde->frequency_detail_name; ?></td>
						<td><?php echo $valde->user_full_name; ?></td>
			            <td><?php echo $publishdate; ?></td>
			            <td>Level <?php echo $valde->validate_level_no; ?></td>
			            <td><?php echo "<a href=".base_url()."journalvalidationview?id=".$valde->data_validate_no.">"; ?><span class="glyphicon glyphicon-edit">&nbsp;</span></a></td>
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
















