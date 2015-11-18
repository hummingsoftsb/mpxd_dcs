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
					showloader(25000);
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
	<!--div class="form-group">
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
	</div-->
	<!-- <div class="row text-center text-danger"><?php echo $message; ?> </div> -->
	<div class="row text-center <?php echo $message_type == 1? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
	<table id="journal_list" class="table table-striped table-hover ">
    	<thead>
        	<tr>
            	<th><?php echo $labelname[9]?></th>
	            <th><a href="javascript:void(0)"><?php echo $labelname[0]; ?></a></th>
	            <th><a href="javascript:void(0)"><?php echo $labelname[1]; ?></a></th>
	            <th class="no-sort">Status</th>
	        </tr>
	    </thead>
    	<tbody>
    		<?php
				$sno=0;
				foreach ($records as $k => $pjde):
			?>
        			<tr>
			        	<td><?php echo $k+1; ?></td>
			            <td><?php echo $pjde->project_name; ?></td>
			            <td><?php echo $pjde->journal_name; ?></td>
			            <?php  $pjdefdat=$pjdefreq[$pjde->journal_no]; ?>
			            <td>Data Entry Pending</td>
			        </tr>
			<?php endforeach; ?>

		</tbody>
	</table>
	
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














