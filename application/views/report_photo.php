<script>
    $(document).ready(function ()
    {
        $("#recordselect").change(function ()
        {
            var recordselect = $(this).val();
            $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/selectrecord", {recordselect: recordselect}, function (data) {
                location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/select";
            });
        });

        $("#recordsearch").click(function ()
        {
            var search = $('#search').val();
            var patt = new RegExp(/^[A-Za-z0-9 _\-\(\)\.]+$/);
            if (patt.test(search) || search == '')
            {
                var search = $('#search').val();
                $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord", {search: search}, function (data) {
                    location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
                });
            }
            else
            {
                alert('The Search field may only contain alpha-numeric characters, underscores, dashes and bracket.');
            }
        });

        $(document).on("click", ".modalentry", function ()
        {
            var count = $(this).data('count');
            var id = $(this).data('id');
            if (count == 0)
            {
                if (confirm("Current Data Attributes will be assigned to this Week Journal?"))
                {
                    $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/dataentry", {id: id}, function (data) {
                        location.href = "<?php echo base_url(); ?>journaldataentryaddnonp?jid=" + id;
                    });
                }
            }
            else
            {
                $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/dataentry", {id: id}, function (data) {
                    location.href = "<?php echo base_url(); ?>journaldataentryaddnonp?jid=" + id;
                });
            }
        });

        $(document).on("click", ".recordsort", function ()
        {
            var search = $(this).data('rsort');

            $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord", {search: search}, function (data) {
                location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
            });

        });

    });
</script>
<style>
	.project-indent-1{
		margin-left: 30px;
	}
	.project-indent-2{
		margin-left: 60px;
	}
</style>
<?php
$labelnames = '';
foreach ($labels as $label):
    $labelnames .= ',' . $label->sec_label_desc;
endforeach;
$labelnames = substr($labelnames, 1);
$labelname = explode(",", $labelnames);
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

    <div class="row">
        <form method="get" class="form-horizontal">

            <div class="form-group">
                <label class="col-sm-2 control-label">Report Date:</label>
                <div class="col-sm-3">
					<input placeholder="Select Report Date" id="proj_date" class="form-control" type="text" name="date" required/>
                    <select style="display: none;" class="form-control"  name="freq">
                        <?php foreach ($freqs as $freq) : ?>
                            <option value="<?php echo $freq->frequency_detail_no; ?>"><?php echo date("d-M-Y", strtotime($freq->start_date)); ?> to <?php echo date("d-M-Y", strtotime($freq->end_date)); ?></option>
                        <?php endforeach; ?>			
                    </select>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-3">
					<a id="check-all-project" href="#">Select all</a> | 
					<a id="clear-all-project" href="#">Clear all</a>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-2 control-label">Packages:</label>
                <div class="col-sm-6">
					<div id="project-list" style="max-height: 400px; border: 1px solid #dddddd; overflow: auto; padding: 5px 10px;">
						<?php foreach($projects as $project): ?>
							<div class="checkbox">
							  <label>
								<input type="checkbox" name="project[]" value="<?php echo $project->project_no; ?>">
								<span class="project-indent-<?php echo $project->indent;?>"><?php echo $project->project_name; ?></span>
							  </label>
							</div>
						<?php endforeach; ?>
					</div>
					
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input class="btn btn-primary btn-sm" type="submit" value="Download"/>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$("#check-all-project").on("click", function(){
	$("#project-list input:checkbox").prop('checked', true);
})
$("#clear-all-project").on("click", function(){
	$("#project-list input:checkbox").prop('checked', false);
})

$( "#proj_date" ).datepicker({
	showOn: "button",
	buttonImage: "<?php echo base_url(); ?>img/calendar.gif",
	buttonImageOnly: true,
	buttonText: "Select date",
	dateFormat: "yy-mm-dd",
	beforeShowDay: available

});

var availableDates = [
<?php foreach ($freqs as $freq) : ?>
"<?php echo date("j-n-Y", strtotime($freq->end_date)); ?>",
<?php endforeach; ?>
];

function available(date) {
  dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
  if ($.inArray(dmy, availableDates) != -1) {
    return [true, "","Available"];
  } else {
    return [false,"","unAvailable"];
  }
}

</script>














