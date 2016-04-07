<?php  ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>MPXD Data Capture System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/bootstrap/bootstrap.css" media="screen">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/bootstrap/style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/bootstrap/bootswatch.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/bootstrap/font.css" media="screen">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/customcss/custom.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/ilyas/css/bootstrap-multiselect.css">
    <script src="<?php echo base_url(); ?>/bootstrap/jquery-1.10.2.min.js"></script>
    <script src="<?php echo base_url(); ?>/ilyas/jquery-migrate-1.2.1.min.js"></script>
    <script src="<?php echo base_url(); ?>/ilyas/polyfills.js"></script>
    <!--<script type="text/javascript" src="<?php echo base_url(); ?>/bootstrap/bootstrap-2.0.2.js"></script>-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url(); ?>/bower_components/html5shiv/dist/html5shiv.js"></script>
    <script src="<?php echo base_url(); ?>/bower_components/respond/dest/respond.min.js"></script>
    <![endif]-->
    <script src="<?php echo base_url(); ?>/bootstrap/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>/ilyas/bootstrap-multiselect.js"></script>
    <script src="<?php echo base_url(); ?>/bootstrap/bootswatch.js"></script>
    <script src="<?php echo base_url(); ?>/bootstrap/jquery.confirm.js"></script>
    <script src="<?php echo base_url(); ?>/bootstrap/jquery-ui.js"></script>
    <!--<script src="<?php echo base_url(); ?>/ilyas/jquery.mjs.nestedSortable.js"></script>-->
    <script src="<?php echo base_url(); ?>/ilyas/jquery.loader.js"></script>
    <script src="<?php echo base_url(); ?>/ilyas/numeral.js"></script>
    <link rel="stylesheet" href="<?php echo base_url(); ?>/ilyas/loader.css"/>
    <link rel="stylesheet" href="<?php echo base_url(); ?>/bootstrap/jquery-ui.css">


    <script src="<?php echo base_url(); ?>/ilyas/datatables/jquery.dataTables.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
    <!--link rel="stylesheet" href="<?php echo base_url(); ?>/ilyas/datatables/jquery.dataTables.min.css"-->


    <script>

        // Temporarily to mitigate annoying datatables alert
        var oldalert = window.alert;
        window.alert = function (a) {
            if (a.indexOf('DataTables warning') != -1) console.log(a); else oldalert(a)
        };

        function showloader(timer) {
            $('body').loader('show');
            if (timer > 1) {
                setTimeout(function () {
                    alert('Server is not responding. Please try again.');
                    location.reload();
                }, timer)
            }
        }

        function hideloader(cb) {
            setTimeout(function () {
                $('body').loader('hide');
                if (typeof cb == "function") cb();
            }, 200);
        }
        $(document).ready(function () {
            $(document).on("click", ".alerthide", function () {
                if (confirm("Do you want to delete?")) {
                    var id = $(this).data('id');
                    $.post("<?php echo base_url(); ?>home/hidealert", {id: id}, function (data) {
                        location.reload();
                    });
                }
            });
            $(document).on("click", ".reminderhide", function () {
                if (confirm("Do you want to delete?")) {
                    var id = $(this).data('id');
                    $.post("<?php echo base_url(); ?>home/hidereminder", {id: id}, function (data) {
                        location.reload();
                    });
                }
            });
        });
    </script>
    <!--        Multi delete: Start-->
    <!--        Added by Agaile on 27/10/2015 for Multi deleting Alert with Accepted Status-->
    <!--    Function to multi delete the 'accepted' Journals-->
    <script>
        function sdelete() {
            // get the count of checked checkbox just to show alert
            //Modified By Sebin
            var count = document.querySelectorAll('input[type="checkbox"]:checked').length;
            if (count > 0) {
                if(confirm("Do you want to delete selected entry(s)?")){
                    if (document.getElementById('chk_chk[]').checked) {
                        var val = [];
                        $(':checkbox:checked').each(function (i) {
                            // insert the values to array
                            val[i] = $(this).val();
                        });
                        // loop to get the value and pass to the function to update the status
                        var i;
                        for (i = 0; i < val.length; i++) {
                            //alert('double inside');
    //                        if (confirm("Do you want to delete?")) {
                            var id = val[i];
                            $.post("<?php echo base_url(); ?>home/hidealert", {id: id}, function (data) {
                                // reload the page to show the updated data
                                location.reload();
                            });
    //                        }
                        }
                    }
                    else {
                        alert('Select atleast One Journal to Delete!');
                    }
                }
            }
            else {
                alert('Select atleast One Journal to Delete!');
            }


        }
    </script>
    <!--        Multi delete: End-->
</head>
<body>
<?php

$ses_data = $this->session->userdata('logged_in');
$ses_data1 = $this->session->userdata('cpass');
$chpass = $ses_data1['cpass'];
$umenu = $ses_data['datap'];
$umenuobj = explode(",777,", $umenu);
$cnt = count($umenuobj) - 1;

//Alert Label
$alabelnames = '';
foreach ($alabels as $label):
    $alabelnames .= ',' . $label->sec_label_desc;
endforeach;
$alabelnames = substr($alabelnames, 1);
$alabelname = explode(",", $alabelnames);

//Reminders Label
$rlabelnames = '';
foreach ($rlabels as $label):
    $rlabelnames .= ',' . $label->sec_label_desc;
endforeach;
$rlabelnames = substr($rlabelnames, 1);
$rlabelname = explode(",", $rlabelnames);
?>
<div id="wrap">
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <!--HEADER-->
        <div class="headermenu">
            <div class="row">
                <div class="col-md-12">
                    <div class="header_top">
                        <div class="row">
                            <div class="col-md-8">

                                <a href="#" class="navbar-brand"><img src="<?php echo base_url(); ?>/img/logo_1.png"
                                                                      width="70">&nbsp;MPXD Data Capture System</a>
                            </div>
                            <div class="col-md-4">
                            </div>
                        </div>
                    </div>
                    <div class="header_bottom">
                        <div class="row">
                            <div class="col-md-8">
                                <?php if ($chpass != 1) { ?>
                                    <ul class="nav navbar-nav">
                                        <?php
                                        $gname = "";
                                        for ($i = 0;
                                        $i < $cnt;
                                        $i++)
                                        {
                                        $umenuobj1 = explode(",", $umenuobj[$i]);
                                        $url = base_url() . $umenuobj1[2];
                                        if ($gname == "" || $gname != $umenuobj1[0]) {
                                        if ($gname != "") {
                                            echo "</ul><li>";
                                        }
                                        ?>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span
                                                    class="<?php echo $umenuobj1[3]; ?>">&nbsp;</span><?php echo $umenuobj1[0]; ?>
                                                <b class="caret"></b></a>
                                            <ul class="dropdown-menu">
                                                <li><a href='<?php echo $url; ?>'><?php echo $umenuobj1[1]; ?></a></li>
                                                <?php
                                                $gname = $umenuobj1[0];
                                                } else {
                                                    ?>
                                                    <li><a href='<?php echo $url; ?>'><?php echo $umenuobj1[1]; ?></a>
                                                    </li>
                                                <?php
                                                }
                                                }
                                                ?>

                                            </ul>
                                        </li>
                                        <!-- <li><a href="#"><span class="glyphicon glyphicon-info-sign">&nbsp;</span><i class="fa fa-cloud"></i>About</a></li> -->
                                    </ul>
                                <?php } ?>
                            </div>
                            <div class="col-md-4">
                                <ul class="nav navbar-nav navbar-right">
                                    <!-- alert -->
                                    <li class="header_alert">
                                        <a href="javascript:;" data-toggle="modal" data-target=".bs-example-modal-md_alert" id="notification_alert">
                                            <span class="glyphicon glyphicon-warning-sign">&nbsp;</span><i
                                                class="fa fa-cloud"></i><span id="aCount"
                                                                              class="badge pull-right"><?php echo $alertcount; ?></span>
                                        </a>
                                    </li>
                                    <!--  -->
                                    <!-- reminder -->
                                    <li class="header_reminder">
                                        <a href="#" data-toggle="modal" data-target=".bs-example-modal-md_reminders">
                                            <span class="glyphicon glyphicon-bullhorn">&nbsp;</span><i
                                                class="fa fa-cloud"></i><span
                                                class="badge pull-right"><?php echo $remindercount; ?></span>
                                        </a>
                                    </li>
                                    <!--  -->
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Welcome,
                                            <br><?php function truncate($string, $length, $dots = "...")
                                            {
                                                return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
                                            }

                                            ;
                                            echo truncate($username, 15); ?><b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="<?php echo base_url(); ?>login/logout">Logout</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--TUTUP HEADER-->
<!-- alert ---->
<div class="modal fade bs-example-modal-md_alert" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                        class="sr-only">Close</span></button>

                <h4 class="modal-title" id="myModalLabel"><?php echo $alabelobject; ?></h4>
            </div>
            <div class="modal-body">


                <!-- ------ -->


                <?php $href = "#"; ?>
                <script type="text/javascript">
                    $(document).ready(function () {
                        $.fn.dataTableExt.sErrMode = 'throw';
                        $('#table_notification1').DataTable({
                            "bFilter": false,
                            "bLengthChange": false,
                            "bSort": false,
                            "language": {"loadingRecords": "No notification."}
                        });
                        var oTable = $('#table_notification').dataTable({
                            "ajax": '<?php echo base_url(); ?>api/getlatestnotification',
                            "bFilter": false,
                            "bLengthChange": false,
                            "bSort": false,
                            "language": {"loadingRecords": "No notification."}
                        });

                       setInterval(function () {
                            oTable.api().ajax.reload(null, false); // user paging is not reset on reload
                            aCount = oTable.count;
                            $('#aCount').text(aCount);
                           $('#checkboxes input:checkbox').each(function(){
                               var time = 500;
                               setTimeout(function() {
                                   $('.not_seen').closest('tr').css('background-color', '#e6f3f7');
                               }, time);
                           });
                        }, 30000);
                        $('#notification_alert').click(function(){
                            $('#checkboxes input:checkbox').each(function(){
                                var time = 500;
                                setTimeout(function() {
                                    $('.not_seen').closest('tr').css('background-color', '#e6f3f7');
                                }, time);
                         });
                        });
                    });
                    function confirm_fn(i){
                        $('#tempid').val($('#reminder_no_hid'+i).val());
                        $(".bs-example-modal-confirm").modal("show");
                    }
                    function redirect_fn() {
                        var reminder_no_hid="";
                        reminder_no_hid = $("#tempid").val();
                        $.post("<?php echo base_url(); ?>reminders/resend_reminder/", {reminder_no: reminder_no_hid}, function (data) {
                            location.reload();
                        });
                    }
                    /*function to assign data attributes to the data entry. done by jane*/
                    function assign_attributes(id){
                        /*if(confirm("Current Data Attributes will be assigned to this Week Journal?"))
                        {*/
                            showloader(25000);
                            $.post( "<?php echo base_url(); ?>journaldataentry/dataentry",{id:id}, function( data ) {
                                location.href="<?php echo base_url(); ?>journaldataentryadd?jid="+id;
                            }).always(function(d){console.log(d);});

                        /*}*/
                    }
                </script>

                <style type="text/css">
                    /*.override_style_1 {width: 10px !important;}*/
                    .override_style_2 {
                        width: 300px !important;
                    }

                    .override_style_3 {
                        width: 70px !important;
                    }

                    .dataTables_filter input {
                        width: 250px;
                    }

                    /*.override_style_4 {width: 10px !important;}*/
                    /*.override_style_5 {width: 10px !important;}*/

                    /*table notification1*/
                    .dataTables_empty {
                        color: #f00 !important;
                    }
                </style>
                <table id="table_notification" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><a href="javascript:sdelete()" id="adelete"><span title='Delete'
                                                                              class='glyphicon glyphicon-trash'></span></a>
                        </th>
                        <th class="override_style_1">No</th>
                        <th class="override_style_2"><?php echo $alabelname[0]; ?></th>
                        <th class="override_style_3"><?php echo $alabelname[1]; ?></th>
                        <th class="override_style_4"><?php echo $alabelname[2]; ?></th>
                        <th class="override_style_5"><?php echo $alabelname[3]; ?></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th>No</th>
                        <th><?php echo $alabelname[0]; ?></th>
                        <th><?php echo $alabelname[1]; ?></th>
                        <th><?php echo $alabelname[2]; ?></th>
                        <th><?php echo $alabelname[3]; ?></th>
                    </tr>
                    </tfoot>
                    <tbody id="checkboxes">
                    </tbody>
                </table>


                <!-- ------ -->

            </div>
        </div>
    </div>
</div>
<!-- alert ---->
<!--Confirm --->
<div class="modal fade bs-example-modal-confirm" tabindex="-1" role="dialog" aria-labelledby="myConfirmationLabel" aria-hidden="true" style="z-index: 1060 !important;margin-top: 7%">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure to resend the reminder again?</p>
                <div class="row">
                    <div class="col-md-8" style="margin-left: 60px;">
                        <input class="btn btn-success btn-sm" value="No" type="button" data-dismiss="modal">
                        <span>&nbsp;&nbsp;</span>
                        <input class="btn btn-primary btn-sm" value="Yes" onclick="redirect_fn()" type="button">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Confirm End--->
<!-- Reminders ---->
<div class="modal fade bs-example-modal-md_reminders" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $rlabelobject; ?></h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>

                        <th>No</th>
                        <th><?php echo $rlabelname[0]; ?></th>
                        <?php if($ses_data['roleid']==1){ ?>
                        <th>Assignee</th>
                        <?php } ?>
                        <th><?php echo $rlabelname[1]; ?></th>
                        <th><?php echo $rlabelname[2]; ?></th>
                        <!--th><?php echo $rlabelname[3]; ?></th-->
                        <?php if($ses_data['roleid']==1){ ?>
                        <th><?php echo $alabelname[3]; ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sno = 1;
                    $i = 0;
                    foreach ($reminders as $record):
                        $i++;
                        //to disable resend button for 12 hours. Done by jane
                        $current_timestamp = date('Y-m-d H:i:s');
                        $class = 'glyphicon glyphicon-send';
                        if(!empty($record->maxt)){
                            $reminder_timestamp = date_format(date_create($record->maxt), 'Y-m-d H:i:s');
                            $hours_diff = (strtotime($current_timestamp)-strtotime($reminder_timestamp))/3600;
                            if($hours_diff<12) {
                                $class = '';
                            }
                        }
                        ?>

                        <tr>
                            <td><?php echo $sno; ?></td>
                            <?php if($ses_data['roleid']==1){ ?>
                            <?php if($record->reminder_status_id == 1 && (!empty($record->data_entry_no))){ ?>
                                <td><a href="<?php echo base_url(); ?>journaldataentry"><?php echo $record->reminder_message; ?></a></td>
                            <?php } elseif($record->reminder_status_id == 2 && (!empty($record->data_entry_no))) {?>
                                <td><a href="<?php echo base_url(); ?>journalvalidation"><?php echo $record->reminder_message; ?></a></td>
                            <?php } elseif($record->reminder_status_id == 1 && (!empty($record->nonp_journal_id))) {?>
                                <td><a href="<?php echo base_url(); ?>journaldataentry"><?php echo $record->reminder_message; ?></a></td>
                            <?php } else { ?>
                                <td><a href="<?php echo base_url(); ?>journalvalidationnonp"><?php echo $record->reminder_message; ?></a></td>
                            <?php } ?>
                            <td><?php echo $record->user_full_name.' ('.$record->sec_role_name.')'; ?></td>
                            <?php } else { ?>
                                <?php if($record->reminder_status_id == 1 && (!empty($record->data_entry_no))){ ?>
                                    <td><a href="<?php echo base_url(); ?>journaldataentryadd?jid=<?php echo $record->data_entry_no; ?>" onclick="assign_attributes('<?php echo $record->data_entry_no;?>');"><?php echo $record->reminder_message; ?></a></td>
                                <?php } elseif($record->reminder_status_id == 2 && (!empty($record->data_entry_no)) && (!empty($record->data_validate_no))) {?>
                                    <td><a href="<?php echo base_url(); ?>journalvalidationview?id=<?php echo $record->data_validate_no; ?>"><?php echo $record->reminder_message; ?></a></td>
                                <?php } elseif($record->reminder_status_id == 1 && (!empty($record->nonp_journal_id))) {?>
                                    <td><a href="<?php echo base_url(); ?>index.php/ilyas?jid=<?php echo $record->nonp_journal_id; ?>"><?php echo $record->reminder_message; ?></a></td>
                                <?php } else { ?>
                                    <td><a href="<?php echo base_url(); ?>/index/ilyasvalidate?jid=<?php echo $record->nonp_journal_id; ?>"><?php echo $record->reminder_message; ?></a></td>
                                <?php } ?>
                            <?php } ?>
                            <td><?php echo $record->reminder_date; ?></td>
                            <?php if(!empty($record->nonp_journal_id)) {?>
                                <td><?php echo $record->reminder_frequency; ?></td>
                            <?php }else { ?>
                                <td><?php echo $record->frequency_period; ?></td>
                            <?php } ?>
                            <?php if($ses_data['roleid']==1){ ?>
                                <input type="hidden" id="reminder_no_hid<?php echo $i;?>" name="reminder_no_hid" value="<?php echo $record->reminder_no; ?>">
                            <td><a id="resend-location" href='javascript:;' onclick="confirm_fn('<?php echo $i;?>');"><span title='Resend' class='<?php echo $class; ?>'></span></a></td>
                            <?php } ?>
                            <input type="hidden" name="data_entry_hid" id="data_entry_hid" value="<?php if(!empty($record->data_entry_no)){echo $record->data_entry_no;}?>">
                            <input type="hidden" name="nonp_journal_hid" id="nonp_journal_hid" value="<?php if(!empty($record->nonp_journal_id)){echo $record->nonp_journal_id;}?>">
                        </tr>
                        <?php
                        $sno = $sno + 1;
                    endforeach;
                    if ($sno == 1) {
                        echo "<tr><td colspan='5' class='row text-center text-danger'> No Reminder Found</td></tr>";
                    }
                    ?>
                    <input type="hidden" id="tempid"/>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Reminders ---->