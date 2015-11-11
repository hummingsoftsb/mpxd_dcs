<?php
header("Content-type:application/octet-stream");
header("Content-Disposition:attachment;filename=Project_Journal_Status.xls");
header("Pragma:no-cache");
header("Expires:0");
?>
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


<!--    <div class="row">


        <div class="col-md-4">
            <ul class="breadcrumb" style=" text-align: center; ">
                <li><a href="<?php /*echo base_url(); */?>home">Home</a></li>
                <li><?php /*echo $labelgroup; */?></li>
                <li class="active"><?php /*echo $labelobject; */?></li>
            </ul>
        </div>


    </div>-->

</div>


<p>
    <style>
        .multicss {
            font-size: 1.1em;
            height: 30px;
        }
    </style>
<div = "table">
<table class="table table-striped table-hover" id="status_table" name="status_table">
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
    <tbody>
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
    </tbody>
</table>