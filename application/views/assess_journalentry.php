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

	    $(document).on("click", ".modalentry", function ()
		{
			var count=$(this).data('count');
			var id = $(this).data('id');
			if(count==0)
			{
				if(confirm("Current Data Attributes will be assigned to this Week Journal?"))
				{
					$.post( "<?php echo base_url(); ?><?php echo $cpagename; ?>/dataentry",{id:id}, function( data ) {
						location.href="<?php echo base_url(); ?>journaldataentryadd?jid="+id;
					}).always(function(d){console.log(d);});

				}
			}
			else
			{
				location.href="<?php echo base_url(); ?>journaldataentryadd?jid="+id;
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
		<?php if($searchrecord=="project_name asc" || $searchrecord=="project_name desc" || $searchrecord=="journal_name asc" || $searchrecord=="journal_name desc") { ?>
			<input type="text" class="form-control" id="search" name="search" value="" placeholder="Enter the text here">
			<?php } else { ?>
			<input type="text" class="form-control" id="search" name="search" value="<?php echo $searchrecord; ?>" placeholder="Enter the text here">
			<?php } ?>
		</div>
		<input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search" />
		<a href="<?php echo base_url(); ?><?php echo $cpagename; ?>" class="btn btn-danger btn-sm">Clear</a>
	</div>
	<div class="row text-center text-danger"><?php echo $message; ?> </div>
	<table class="table table-striped table-hover ">
    	<thead>
        	<tr>
            	<th><?php echo $labelname[9]?></th>
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
	            <th><?php echo $labelname[2]; ?></th>
	        </tr>
	    </thead>
    	<tbody>
    		<?php
				$sno=0;
				foreach ($records as $pjde):
				$index = $page+$sno;
			?>
        			<tr>
			        	<td><?php echo $index; ?></td>
			            <td><?php echo $pjde->project_name; ?></td>
			            <td><?php echo $pjde->journal_name; ?></td>
			            <?php  $pjdefdat=$pjdefreq[$pjde->journal_no]; ?>
			            <td><?php echo $pjdefdat; ?></td>
			        </tr>


    		<?php
				$sno=$sno+1;
				endforeach;
				if($totalrows==0)
				{
					echo '<tr><td class="row text-center text-danger" colspan="4"> No Record Found</td></tr></tbody></table>';
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














