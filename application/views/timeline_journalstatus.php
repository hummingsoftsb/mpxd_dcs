<script>
    $(document).ready(function () {
        $("#recordselect").change(function () {
            var recordselect = $(this).val();
            $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/selectrecord", {recordselect: recordselect}, function (data) {
                location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/select";
            });
        });

        $("#recordsearch").click(function () {
            var search = $('#search').val();
            var patt = new RegExp(/^[A-Za-z0-9 _\-\(\)\.]+$/);
            if (patt.test(search) || search == '') {
                var search = $('#search').val();
                $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord", {search: search}, function (data) {
                    location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
                });
            }
            else {
                alert('The Search field may only contain alpha-numeric characters, underscores, dashes and bracket.');
            }
        });
        $("#excelgenerator").click(function () {
            $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/generateexcel", function (data) {
                location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/generateexcel";
            });

        });

        $("#chkstat input:radio").click(function () {
            var rval = "";
            var search = $("#chkstat input:radio:checked").val();
            if (search == "Completed") {
                $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord", {search: search}, function (data) {
                    location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
                });
            } else if (search == "Pending") {
                $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord", {search: search}, function (data) {
                    location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
                });
            } else {
                var search = "";
                $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/searchrecord", {search: search}, function (data) {
                    location.href = "<?php echo base_url(); ?><?php echo $cpagename; ?>/search";
                });
            }
        });

    });
</script>

<!--Added by Agaile for intense / column search on 26/10/2015-->

<script>
    $(document).ready(function () {

        // DataTable
        var table = $('#status_table').DataTable({
        });
        // Setup - add a text input to each footer cell
        $('#status_table tfoot th').each(function () {
            var title = $('#status_table thead th').eq($(this).index()).text();
            $(this).html('<input type="text" placeholder="Search" style="width: 70px;" />');
        });

//        $('#status_table_wrapper .dataTables_filter input').addClass("form-control input-sm").attr("placeholder", "Search");

        // Apply the search
        table.columns().every(function () {
            var that = this;

            $('input', this.footer()).on('keyup change', function () {
                if (that.search() !== this.value) {
                    that
                        .search(this.value)
                        .draw();
                }
            });
        });
    });
</script>

<?php
$labelnames = '';
foreach ($labels as $label):
    $labelnames .= ',' . $label->sec_label_desc;
endforeach;
$labelnames = substr($labelnames, 1);
$labelname = explode(",", $labelnames);
?>
<div class="container">
    <!-- INPUT HERE-->

    <div class="page-header">
        <h1 id="nav"><?php echo $labelobject; ?></h1>
    </div>


    <div class="row">


        <div class="col-md-4">
            <ul class="breadcrumb" style=" text-align: center; ">
                <li><a href="<?php echo base_url(); ?>home">Home</a></li>
                <li><?php echo $labelgroup; ?></li>
                <li class="active"><?php echo $labelobject; ?></li>
            </ul>
        </div>


    </div>
    <!--    commented by Agaile as advised by Zul on 26/10/2015-->
    <!--    Start: Comment-->
        <div class="form-group">
<!--            <label for="search" class="col-sm-1 control-label">Search</label>-->
<!--    -->
<!--            <div class="col-sm-4">-->
<!--                <input type="text" class="form-control" id="search" name="search" value="-->
<!--    --><?php //echo $searchrecord; ?><!--"-->
<!--                       placeholder="Enter the text here">-->
<!--            </div>-->
<!--            <input type="button" class="btn btn-primary btn-sm" id="recordsearch" name="recordsearch" value="Search"/>-->
<!--            <a href="--><?php //echo base_url(); ?>
<!--    --><?php //echo $cpagename; ?><!--" class="btn btn-danger btn-sm">Clear</a>-->
            <input type="button" class="btn btn-success btn-sm pull-right" id="excelgenerator" name="excelgenerator"value="Generate Excel"/>
            <br/>
            <br/>
        </div>
    <!---->
    <!--    <div class="row">-->
    <!--    <div class="col-md-12" id="chkstat" name="chkstat" style="text-align:center;">-->
    <!--    -->
    <!--    -->
    <!--          <b>Status</b> : &nbsp;<label class="radio-inline">-->
    <!--    -->
    <!--          --><?php //if($searchrecord=="Completed") { ?>
    <!--            <input type="radio" name="jstat" id="jstat" value="Completed" checked>  Completed-->
    <!--            --><?php //} else {?>
    <!--            <input type="radio" name="jstat" id="jstat" value="Completed">  Completed-->
    <!--            --><?php //} ?>
    <!--          </label>-->
    <!--          <label class="radio-inline">-->
    <!--          --><?php //if($searchrecord=="Pending") { ?>
    <!--            <input type="radio" name="jstat" id="jstat" value="Pending" checked> Pending-->
    <!--            --><?php //} else {?>
    <!--            <input type="radio" name="jstat" id="jstat" value="Pending"> Pending-->
    <!--            --><?php //} ?>
    <!--    -->
    <!--          </label>-->
    <!--    -->
    <!--    -->
    <!--    </div>-->
    <!--    </div>-->
    <!--    End: Comment-->

    <style>
        .multicss {
            font-size: 1.1em;
            height: 30px;
        }
    </style>

    <table class="table table-striped table-hover" id="status_table" name="status_table">
        <thead>
        <tr>
            <th>No</th>
            <th><?php echo $labelname[0]; ?></th>
            <th><?php echo $labelname[1]; ?></th>
            <th><?php echo 'Data Entry' ?></th>
            <th><?php echo 'Gate Keeper' ?></th>
            <th><?php echo $labelname[2]; ?></th>
            <th><?php echo $labelname[3]; ?></th>
            <th><?php echo $labelname[4]; ?></th>
            <th><?php echo $labelname[5]; ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th></th>
            <th><?php echo $labelname[0]; ?></th>
            <th><?php echo $labelname[1]; ?></th>
            <th><?php echo 'Data Entry' ?></th>
            <th><?php echo 'Gate Keeper' ?></th>
            <th><?php echo $labelname[2]; ?></th>
            <th><?php echo $labelname[3]; ?></th>
            <th><?php echo $labelname[4]; ?></th>
            <th><?php echo $labelname[5]; ?></th>
        </tr>
        </tfoot>
        <tbody>

<?php
//echo "<pre>";
//	print_r($records);
//	echo "</pre>";
?>
<?php $i=0; foreach($records as $pstat) { $i++;
    $startdate = date("d-m-Y", strtotime($pstat['start_date']));
    if ($pstat['end_date'] != "") {
        $enddate = date("d-m-Y", strtotime($pstat['end_date']));
    } else {
        $enddate = "";
    }
    ?>


            <tr id="<?php echo $pstat['complete_percent']; ?>" name="<?php echo $pstat['complete_percent']; ?>">
                <td><?php echo $i; ?></td>
                <td><?php echo $pstat['project_name']; ?></td>
                <td><?php echo $pstat['journal_name']; ?></td>
                <td><?php echo $pstat['user_full_name']; ?></td>
                <td><?php
                    $ar = $pstat['gate_name'] ;
                    if($ar){
                        echo  implode(',',$ar);
                    }
                    ?></td>
                <td><?php echo $startdate; ?></td>
                <td><?php echo $enddate; ?></td>
                <td><?php echo $pstat['frequency_detail_name']; ?></td>
                <td><!--div class="progress progress-striped active">
									<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $pstat['complete_percent'] ?>%;">
									<div class="progresstext"><?php echo $pstat['complete_percent']; ?>%</div></div></div-->
                    <?php echo $pstat['data_entry_status_desc']; ?>
                </td>
            </tr>
<?php }?>
<!--            --><?php
//            $sno = $sno + 1;
//        endforeach;
//        if ($totalrows == 0)
//        {
//            echo '<tr><td class="row text-center text-danger" colspan="8"> No Record Found</td></tr></tbody></table>';
//        }
//        else
//        {
//        ?>
        </tbody>
    </table>

    <div class="row">

        <div class="col-md-12">
            <div class="col-md-4">
<!--                <ul class="pagination">-->
<!--                    --><?php //echo $this->pagination->create_links(); ?>
<!--                </ul>-->
            </div>
<!--            <div class="col-md-4 col-md-offset-1">-->
<!--                <div class="form-group">-->
<!--                    <label for="search" class="col-sm-2 control-label" style="padding-top: 22px;">Show</label>-->
<!---->
<!--                    <div class="col-sm-3" style="padding-top: 14px;">-->
<!--                        <select class="form-control" id="recordselect" name="recordselect">-->
<!--                            <option --><?php //if ($selectrecord == "10") echo "selected=selected"; ?><!--></option>
<!--                            <option --><?php //if ($selectrecord == "20") echo "selected=selected"; ?><!--></option>
<!--                            <option --><?php //if ($selectrecord == "40") echo "selected=selected"; ?><!--></option>
<!--                        </select>-->
<!--                    </div>-->
<!---->
<!--                </div>-->
            </div>
<!--            --><?php
//            // Display the number of records in a page
//            $end = $mpage + $page - 1;
//            if ($totalrows < $end) $end = $totalrows;
//            ?>
<!--            <div class="col-md-3" style="padding-top: 22px;"> Showing --><?php //echo $page; ?><!-- to --><?php //echo $end; ?>
<!--                of --><?php //echo $totalrows; ?><!-- rows-->
<!--            </div>-->
<!--        </div>-->
<!--        --><?php //} ?>
    </div>


</div>


</div>



