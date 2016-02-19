<script src="<?php echo base_url(); ?>ilyas/fancybox/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url(); ?>ilyas/multiupload.js"></script>
<script src="<?php echo base_url(); ?>agaile/jquery.fileupload.js"></script>
<script>var uploadUrl = '<?php echo base_url(); ?><?php echo $cpagename; ?>/addimage/'</script>
<script>var uploadUrl2 = '<?php echo base_url(); ?><?php echo $cpagename; ?>/replaceimage/'</script>
<script src="<?php echo base_url(); ?>ilyas/multiupload/custom.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css"
      media="screen"/>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/css/multiupload.css" type="text/css" media="screen"/>
<style>
    p.set-w {
        word-wrap: break-word;
        width: 425px;
        overflow: auto;
    }
</style>
<script>
var pcid;
var datenno;
var desc;
var rejnote;
var pic_val_comment;
var datavalno;
var j=0;
$(document).ready(function () {
    $("#modaladd").click(function () {
        var empty = "";
        $(".modal-body #imagefile").val(empty);
        $(".modal-body #imagedesc").val(empty);
        $('#errorimage').html(empty);
        $('#errordesc').html(empty);
        $('#diverrormsg').html(empty);
        $('#imagePreview').attr("src", "");
    });

    $("#upld").click(function () {
        if (document.getElementById("iddesc"+j).value.trim() == "") {
            document.getElementById("iddesc"+j).value = desc;
        }
        $.ajax({
            type: 'POST',
            url: uploadUrl2,
            data: {
                data_entry_pic_no: pcid,
                data_entry_no: datenno
            },
            async: false,
            cache: false,
            dataType: "json",
            success: function (data) {
                if (data.status == "success") {
                    alert('success');
                }
                else {
                    alert('failed');
                }
            },

            failure: function () {
                console.log(' Ajax Failure');
            },
            complete: function () {
                console.log("complete");
            }
        });
    });

    $(document).on("click", ".modaledit", function () {
        pcid = $(this).attr('data-picid');
        datenno = $(this).attr('data-enno');
        desc = $(this).attr('data-desc');
        rejnote = $(this).attr('data-rejnote');
        pic_val_comment = $(this).attr('data-pic-val-comment');
        pic_seq_no = $(this).attr('data-pic-seq-no');
        document.getElementById('val_comment').value=pic_val_comment;
        document.getElementById('seq_no').value=pic_seq_no;
        datavalno = $(this).attr('data-validate-no');
        var empty = "";
        $(".modal-body #picid").val($(this).data('picid'));
        $(".modal-body #imagedesc1").val($(this).data('desc'));
        $(".modal-body #divimg").html('<img src="' + $(this).data('img') + '" class="img-responsive" alt="" style="width: 200px; height: 137px;">');
        $('#errordesc1').html(empty);
        $('#diverrormsg').html(empty);

    });

    $('#updateimage').submit(function () {
        var empty = "";
        $('#diverrormsg').html(empty);
        $.post($('#updateimage').attr('action'), $('#updateimage').serialize(), function (data) {
            if (data.st == 0) {
                hideloader();
                $('#errordesc1').html(data.msg);
            }
            else if (data.st == 1) {
                hideloader();
                location.reload();
            }

        }, 'json');
        return false;
    });

    // Will only run callback if its successfull
    function save_data(cb) {
        var empty = "";
        $("[id*='dataattb']").removeClass('this-is-error');
        $('#diverrormsg').html(empty);
        var $dis = $('#addRecord *[disabled=disabled]').removeAttr('disabled');
        //console.log($dis);
        var data = $('#addRecord').serialize();
        //console.log(data);
        $dis.attr('disabled', 'disabled');
        $.post($('#addRecord').attr('action'), data, function (data) {
            // console.log(data);
            if (data.st == 0) {
                hideloader();
                $('#errordata').html(data.msg);
                $('#dataattb' + data.id).addClass('this-is-error');//css('border','1px solid red');
            }
            else if (data.st == 1) {
                if (typeof cb == "function") cb();
                //location.reload();
            }

        }, 'json').always(function (data) {
            console.log(data)
        });
    }

    $('#addRecord').submit(function () {
        save_data(function () {
            window.location.reload()
        });
        return false;
    });

    $('#addVarient').submit(function () {
        var empty = "";
        $('#diverrormsg').html(empty);
        $.post($('#addVarient').attr('action'), $('#addVarient').serialize(), function (data) {
            console.log(data);
            if (data.st == 0) {
                hideloader();
                $('#errorvarient').html(data.msg);
            }
            else if (data.st == 1) {
                location.href = "<?php echo base_url(); ?>journaldataentry";
            }
        }, 'json').always(function (data) {
            console.log(data);
        });
        return false;
    });

    $(document).on("click", ".modaldelete", function () {
        if (confirm("Do you want to delete the image?")) {
            var imgid = $(this).data('imgid');
            var dataid = $(this).data('dataid');
            $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/deleteimage", {
                id: imgid,
                dataid: dataid
            }, function (data) {
                var imagevalue = data.imgval;
                var imagevalue1 = imagevalue//.split(',777,');
                $("#tableimage").find("tr:gt(0)").remove();
                for (var i = 0; i < imagevalue1.length; i++) {
                    if (imagevalue1[i] != "") {
                        var content = '';
                        var imagevalue2 = imagevalue1[i]//.split(',');
                        content += '<tr>';
                        content += '<td>' + imagevalue2[0] + '</td>';
                        content += '<td> <img src="' + imagevalue2[1] + imagevalue2[2] + '" class="img-responsive" alt="" style="width: 200px; height: 137px;"> </td>';
                        content += '<td> ' + imagevalue2[3] + ' </td>';
                        content += '<td> <a href="#" data-toggle="modal" class="modaldelete" data-imgid="' + imagevalue2[4] + '" data-dataid="' + imagevalue2[5] + '"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td>';
                        content += '</tr>';
                        $("#tableimage").append(content);
                    }

                }
                location.reload();
                //$("#diverrormsg").html("Picture Attachment Deleted Successfully");

            }, 'json');
        }
    });

    $("#modalpublish").click(function () {
        var empty = "";
        $('#diverrormsg').html(empty);
        if (confirm("Confirm publish?")) {
            save_data(function () {
                var dataid = $("#dataentryno").val();
                $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/publish", {id: dataid}, function (data) {
                    var count = data.msg2;
                    if (count == 0) {
                        showloader();
                        location.href = "<?php echo base_url(); ?>journaldataentry";
                    }
                    else {
                        hideloader();
                        $("#varient > tbody > tr").remove();
                        $("#varientcount").val(count);
                        var varientvalue = data.msg1//$.parseJSON(data.msg1);
                        var varientvalue1 = varientvalue;
                        for (var i = 1; i <= varientvalue1.length; i++) {
                            if (varientvalue1[i - 1] != "") {
                                var content = '';
                                var varientvalue2 = varientvalue1[i - 1];
                                content += '<tr>';
                                content += '<td>' + i + '</td>';
                                content += '<td>' + varientvalue2[2] + '</td>';
                                content += '<td>' + varientvalue2[3] + '</td>';
                                content += '<td>' + varientvalue2[4] + '</td>';
                                content += '<td>' + varientvalue2[5] + '</td>';
                                content += '<td>' + varientvalue2[6] + '</td>';
                                content += '<td>' + varientvalue2[7] + '</td>';
                                content += '<td>' + varientvalue2[8] + '</td>';
                                content += '<td>' + varientvalue2[9] + '</td>';
                                content += '<td><input type="checkbox" id="chkvarient' + i + '" name="chkvarient' + i + '" />';
                                content += '<input type="hidden" id="dataentry' + i + '" name="dataentry' + i + '" value="' + varientvalue2[0] + '" />';
                                content += '<input type="hidden" id="dataattb' + i + '" name="dataattb' + i + '" value="' + varientvalue2[1] + '" />';
                                content += '<input type="hidden" id="varientvalue' + i + '" name="varientvalue' + i + '" value="' + varientvalue2[9] + '" />';
                                content += '</td>';
                                content += '</tr>';
                                $("#varient").append(content);
                            }

                        }
                        $('#MyModal2').modal('show');
                    }

                }, 'json').always(function (data) {
                    console.log(data);
                });

            });
        } else {
            hideloader();
        }
        /*calling reminder update function*//*
        $.post("<?php echo base_url(); ?>ilyas/update", {
        });*/
    });

    $('.image-description .text').on("click", function () {
        $(this).hide();
        $(this).parent().find(".edit").show();
        var i = $(this).parent().parent().find('#i_hidden').val();
        img_desc_edit_limit(i);
    });
    $('.image-description .cancel').on("click", function () {
        $(this).parent().hide();
        $(this).parent().parent().find('.text').show();
        desc = $(this).parent().parent().find('a').text();
        $(this).parent().find('textarea').val(desc);
        var i = $(this).parent().parent().find('#i_hidden').val();
        img_desc_edit_limit(i);

    });
    $('.image-description .save').on("click", function () {
        that = $(this);
        dataid = $(this).parent().parent().data('picid');
        datadesc = $(this).parent().find('textarea').val();
        if (datadesc.length > 1) {
            $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/updateimagedesc", {
                picid: dataid,
                imagedesc1: datadesc
            }, function (data) {
                var status = data.st;
                if (status === 1) {
                    that.parent().parent().find('a').text(datadesc);
                    that.parent().hide();
                    that.parent().parent().find('.text').show();
                }
            }, 'json').always(function (data) {
                console.log(data);
            });
        }
        else {
            alert('Image Description is compulsory.');
        }
    });

    var fixHelperModified = function (e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function (index) {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };

    $("#tableimage tbody").sortable({
        helper: fixHelperModified,
        stop: function (event, ui) {
            renumber_table('#tableimage')
        }
    }).disableSelection();

});

function renumber_table(tableID) {
    //update table no
    seqs = '';
    $(tableID + " tbody tr").each(function () {
        count = $(this).parent().children().index($(this)) + 1;
        $(this).find('.tableimgno').html(count);
        picid = $(this).data("rowid");
        seqs += picid + ':' + count + ',';
    });
    showloader();
    $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/updateimgsequence", {seqs: seqs}, function () {
    }).done(function () {
        hideloader()
    });
}

function addplus(maxval, txt) {
    if ($('#dataattb' + txt).val() == '') {
        $('#dataattb' + txt).val(0);
    }
    var values = parseInt($('#dataattb' + txt).val()) + 1;
    if (values <= maxval) {
        $('#dataattb' + txt).val(values);

    } else {
        $('#dataattb' + txt).val(maxval);
    }
    revalidateInputs();

}
function addminus(minval, txt) {
    if ($('#dataattb' + txt).val() == '') {
        $('#dataattb' + txt).val(0);
    }
    var values = parseInt($('#dataattb' + txt).val()) - 1;
    if (values >= minval) {
        $('#dataattb' + txt).val(values);

    } else {
        $('#dataattb' + txt).val(minval);
    }
    revalidateInputs();
}
function resetval(minval, txt) {
    $('#dataattb' + txt).val(minval);
    revalidateInputs();
}
/*function to remove uploaded image. added by jane*/
function remove_img(url) {
    var data_entry_no = url.split('/')[1];
    var pict_user_id = url.split('/')[2];
    var pict_file_name = url.split('/')[3];
    $.post("<?php echo base_url(); ?><?php echo $cpagename; ?>/removeimage", {
        data_entry_no: data_entry_no,
        pict_user_id: pict_user_id,
        pict_file_name: pict_file_name
    });
}
/*function to limit image description characters. added by jane*/
/*function img_desc_limit(){
    var characters = 80;
    $("#counter").append("You have <strong>"+  characters+"</strong> characters remaining");
    $("#iddesc").keyup(function(){
        if($(this).val().length > characters){
            $(this).val($(this).val().substr(0, characters));
        }
        var remaining = characters -  $(this).val().length;
        $("#counter").html("You have <strong>"+  remaining+"</strong> characters remaining");
        if(remaining <= 10)
        {
            $("#counter").css("color","red");
        }
        else
        {
            $("#counter").css("color","black");
        }
    });
}*/
/*function to limit image description characters. added by jane*/
function img_desc_limit(j){
    var characters = 80;
    $("#counter"+j).show();
    var a = $("#i_hidden_upload").val();
    $("#iddesc"+j).keyup(function(){
        if($(this).val().length > characters){
            $(this).val($(this).val().substr(0, characters));
        }
        var remaining = characters -  $(this).val().length;
        //$(".char_class").text(remaining);
        $("#idchar"+j).text(remaining);
        if(remaining <= 10)
        {
            $("#counter"+j).css("color","red");
        }
        else
        {
            $("#counter"+j).css("color","black");
        }
    });
}
/*function to limit image description characters in edit description. added by jane*/
function img_desc_edit_limit(i){
    var characters = 80;
    $("#img_dec_edit"+i).keyup(function(){
        $("#counter_edit"+i).show();
        if($(this).val().length > characters){
            $(this).val($(this).val().substr(0, characters));
        }
        var remaining = characters -  $(this).val().length;
        $(".char_class_edit"+i).text(remaining);
        if(remaining <= 10)
        {
            $("#counter_edit"+i).css("color","red");
        }
        else
        {
            $("#counter_edit"+i).css("color","black");
        }
    });
}
</script>

<?php
$dependency = $details[0]->dependency;
//var_dump($details);
$labelnames = '';
foreach ($labels as $label):
    $labelnames .= ',' . $label->sec_label_desc;
endforeach;
$labelnames = substr($labelnames, 1);
$labelname = explode(",", $labelnames);
	$dependency = $details[0]->dependency;
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
<!-- BUAT CODING DALAM WRAP-->
<?php
foreach ($details as $row):
    $week = $row->frequency_period;
    $pname = $row->project_name;
    $jname = $row->journal_name;
    $owner = $row->user_full_name;
    $jdate = $row->start_date;
    $jend = $row->end_date;
    $is_image = $row->is_image;
    $reject_note = $reject_note;
endforeach;
?>
<!-- INPUT HERE-->
<div id="after_header">
<div class="row" style="width: 70%; margin: auto;">
    <div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[3]; ?></b></div>
    <div class="col-xs-9" style="color: blue; margin-bottom: 8px;">Week <?php echo $week; ?></div>
    <div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[27]; ?></b></div>
    <div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo date("d-M-Y", strtotime($jdate)); ?>
        to <?php echo date("d-M-Y", strtotime($jend)); ?></div>
    <div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[0]; ?></b></div>
    <div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $pname; ?></div>
    </br>
    <div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[1]; ?></b></div>
    <div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $jname; ?></div>
    </br>
    <div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[4]; ?></b></div>
    <div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $owner; ?></div>
    </br>
    <div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[5]; ?></b></div>
    <div class="col-xs-9" style="margin-bottom: 8px;">
        <table class="table table-striped table-hover ">
            <thead>
            <tr>
                <th><?php echo $labelname[6]; ?></th>
                <th><?php echo $labelname[7]; ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($validators as $validator):
                echo '<tr>';
                echo '<td>' . $validator->user_full_name . '</td>';
                echo '<td>Level ' . $validator->validate_level_no . '</td>';
                echo '</tr>';
            endforeach;
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php
//echo '<pre>';
//print_r($dataentryattbs);
//echo '</pre>';
//
//?>
<div
    class="row text-center <?php echo $message_type == 1 ? "text-success" : "text-danger"; ?>"><?php echo $message; ?></div>
<!--	 --><?php //var_dump($message_type); ?><!-- 2 -->
<form id="addRecord" method="POST" action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
<?php echo $is_image == 1 ? '<input type="hidden" name="isimage" value="1"/>' : '' ?>
<!-- Changed the condition to show the fieldset when the data entry type is both :Agaile -->
<fieldset style="<?php echo $is_image == 2 || $is_image == 0 ? '': 'display: none;'?>">
    <legend><?php echo $labelname[8]; ?></legend>
    <table class="table table-striped table-hover ">
        <tr>
            <td colspan="7" align="center">
                <div id="errordata" class="text-danger"></div>
                <input type="hidden" id="dataentryno" name="dataentryno" value="<?php echo $dataentryno; ?>"/>
            </td>
        </tr>
        <thead>
        <tr>
            <th><?php echo $labelname[9]; ?></th>
            <th><?php echo $labelname[10]; ?></th>
            <th><?php echo $labelname[11]; ?></th>
            <th><?php echo $labelname[12]; ?></th>
            <th><?php echo $labelname[13]; ?></th>
            <!--<th>Start</th>-
            <th>Baseline</th>
            <th>KPI</th>-->
        </tr>
        </thead>

        <tbody>
        <style type="text/css">.highlight_error {
                background: #ff5;
            }

            .alertstriphidden {
                visibility: hidden;
            }</style>
        <?php
        $sno = 1;
        $disabled = "0";//var_dump($dataentryattbs);

        if ($is_image != 1):
            foreach ($dataentryattbs as $dataentryattb):
                echo "<tr>";
                echo '<td>' . $sno . '</td>';
                echo '<td>' . $dataentryattb->data_attb_label . '</td>';
                echo '<td><input type="hidden" id="dataattbid' . $sno . '" name="dataattbid' . $sno . '" value="' . $dataentryattb->data_attb_id . '" />';
                if ($dataentryattb->data_attb_type_id == 1) {
                    echo '	<script type="text/javascript">
												function datacomparethis' . $sno . '(){
													var a = parseFloat($("#dataattb' . $sno . '").val());
													var b = parseFloat($("#datacompare' . $sno . '").val());
													var c = "' . $sno . ' - ' . $dataentryattb->data_attb_label . '";
													var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;
													var str = $("#dataattb' . $sno . '").val();
													if(numberRegex.test(str)) {
														var d = true;
													}
													if ((a < b) || (d != true)){
														$("#dataattb' . $sno . '").addClass("highlight_error");
														$("#alertstrip' . $sno . '").removeClass("alertstriphidden");
													}else{
														$("#dataattb' . $sno . '").removeClass("highlight_error");
														$("#alertstrip' . $sno . '").addClass("alertstriphidden");
													}
												}
											</script>';
                    echo '<input type="text" id="dataattb' . $sno . '" name="dataattb' . $sno . '" value="' . $dataentryattb->actual_value . '" maxlength="60" class="form-control data-attribute-input-number"';
                    //if($disabled=="1")
                    //	echo 'disabled="true"';
                    echo ' style="width:30%;" onfocusout="datacomparethis' . $sno . '()" onkeypress="datacomparethis' . $sno . '()" onkeyup="datacomparethis' . $sno . '()"/>';
                    echo '<p id="alertstrip' . $sno . '" style="color:red" class="alertstriphidden">the value must be same or higher than previous</p>';
                    echo '<input type="hidden" id="datacompare' . $sno . '" name="datacompare' . $sno . '" value="' . $dataentryattb->actual_value . '" />';
                    echo '<input type="hidden" id="dataattbvalidate' . $sno . '" name="dataattbvalidate' . $sno . '" value="' . $dataentryattb->data_attb_data_type_id . '" />';
                    echo '<input type="hidden" id="dataattbvalidatedigit' . $sno . '" name="dataattbvalidatedigit' . $sno . '" value="' . $dataentryattb->data_attb_data_type_id . '" />';
                    echo '<input type="hidden" id="dataattbtype' . $sno . '" name="dataattbtype' . $sno . '" value="1" />';
                    //echo '<input type="hidden" id="datadisable'.$sno.'" name="datadisable'.$sno.'" value="'.$disabled.'" />';
                } else if ($dataentryattb->data_attb_type_id == 2) {
                    echo '<select id="dataattb' . $sno . '" name="dataattb' . $sno . '"';
                    //if($disabled=="1")
                    //	echo 'disabled="true"';
                    echo '>';
                    foreach ($lookupdetail as $lookupdata):
                        if ($lookupdata->data_set_id == $dataentryattb->data_set_id) {
                            if ($dataentryattb->actual_value == $lookupdata->lk_value)
                                echo '<option value="' . $lookupdata->lk_value . '" selected="selected">' . $lookupdata->lk_data . '</option>';
                            else
                                echo '<option value="' . $lookupdata->lk_value . '">' . $lookupdata->lk_data . '</option>';
                        }
                    endforeach;
                    echo '</select>';
                    echo '<input type="hidden" id="dataattbtype' . $sno . '" name="dataattbtype' . $sno . '" value="2" />';
                    //echo '<input type="hidden" id="datadisable'.$sno.'" name="datadisable'.$sno.'" value="'.$disabled.'" />';

                } else if ($dataentryattb->data_attb_type_id == 3) {

                    $pvalue = $dataentryattb->prev_actual_value;
                    $evalue = $dataentryattb->end_value;
                    if ($pvalue == '') {
                        $pvalue = '0';
                    }
                    if ($evalue == '') {
                        $evalue = '0';
                    }
                    /*if($disabled=="1")
                    {
                        echo '<div class="input-group" style="width:225px;">';
                        echo '<span class="input-group-addon"><img src="img/minus.png"/></span>';
                        echo '<input type="text" id="dataattb'.$sno.'" name="dataattb'.$sno.'" value="'.$dataentryattb->actual_value.'" maxlength="10" class="form-control"';
                        if($disabled=="1")
                            echo 'disabled="true"';
                        echo ' />'; // onpaste="return false" onkeypress="return false"
                        echo '<span class="input-group-addon"><img src="img/plus.png"/></span>';
                        echo '<span class="input-group-addon"><img src="img/reset.png"/></span></div>';
                    }
                    else
                    {*/
                    echo '<div class="input-group" style="width:225px;">';
                    echo '<span class="input-group-addon"><a class="minusbutton" href="javascript:void(0)" onclick="addminus(' . $pvalue . ',' . $sno . ')"><img src="img/minus.png"/></a></span>';
                    echo '<input type="text" id="dataattb' . $sno . '" name="dataattb' . $sno . '" value="' . $dataentryattb->actual_value . '" maxlength="10" class="form-control"';
                    //if($disabled=="1")
                    //echo 'disabled="true"';
                    echo ' />'; // onpaste="return false" onkeypress="return false"
                    echo '<span class="input-group-addon"><a class="addbutton" href="javascript:void(0)" onclick="addplus(' . $evalue . ',' . $sno . ')"><img src="img/plus.png"/></a></span>';
                    echo '<span class="input-group-addon"><a class="resetbutton" href="javascript:void(0)" onclick="resetval(' . $pvalue . ',' . $sno . ')"><img src="img/reset.png"/></a></span></div>';
                    //}

                    /*if($dataentryattb->field_lock=="1")
                    {
                        if($dataentryattb->end_value==$dataentryattb->actual_value)
                        {
                            $disabled="0";
                        }
                        else
                        {
                            $disabled="1";
                        }
                    }
                    */

                    echo '<input type="hidden" id="minvalue' . $sno . '" name="minvalue' . $sno . '" value="' . $pvalue . '" />';
                    echo '<input type="hidden" id="maxvalue' . $sno . '" name="maxvalue' . $sno . '" value="' . $dataentryattb->end_value . '" />';
                    echo '<input type="hidden" id="dataattbtype' . $sno . '" name="dataattbtype' . $sno . '" value="3" />';
                    //echo '<input type="hidden" id="datadisable'.$sno.'" name="datadisable'.$sno.'" value="'.$disabled.'" />';

                    /*if($dataentryattb->field_lock=="1")
                    {
                        $disabled="1";
                    }
                    else
                    {
                        $disabled="0";
                    }*/
                } else if ($dataentryattb->data_attb_type_id == 4) {
                    if ($dataentryattb->actual_value == "Yes") {
                        /*if($dataentryattb->field_lock=="1")
                        {
                            $disabled="0";
                        }*/
                        echo '<input type="radio" name="dataattb' . $sno . '" id="dataattb' . $sno . '" value="Yes" checked>Yes&nbsp;&nbsp;&nbsp;&nbsp;';
                        echo '<input type="radio" name="dataattb' . $sno . '" id="dataattb' . $sno . '" value="No">No';
                        echo '<input type="hidden" id="previousvalue' . $sno . '" name="previousvalue' . $sno . '" value="' . $dataentryattb->actual_value . '" />';
                        //echo '<input type="hidden" id="datadisable'.$sno.'" name="datadisable'.$sno.'" value="'.$disabled.'" />';
                    } else {
                        echo '<input type="radio" name="dataattb' . $sno . '" id="dataattb' . $sno . '" value="Yes"';
                        //if($disabled=="1")
                        //echo 'disabled="true"';
                        echo '/>Yes&nbsp;&nbsp;&nbsp;&nbsp;';
                        echo '<input type="radio" name="dataattb' . $sno . '" id="dataattb' . $sno . '" value="No" checked ';
                        //if($disabled=="1")
                        //echo 'disabled="true"';
                        echo '/>No';
                        //echo '<input type="hidden" id="datadisable'.$sno.'" name="datadisable'.$sno.'" value="'.$disabled.'" />';
                        /*if($dataentryattb->field_lock=="1")
                        {
                            $disabled="1";
                        }*/
                        echo '<input type="hidden" id="previousvalue' . $sno . '" name="previousvalue' . $sno . '" value="' . $dataentryattb->actual_value . '" />';
                    }

                    echo '<input type="hidden" id="dataattbtype' . $sno . '" name="dataattbtype' . $sno . '" value="4" />';


                }
                echo '<input type="hidden" id="datalabel' . $sno . '" name="datalabel' . $sno . '" value="' . $dataentryattb->data_attb_label . '" />';
                echo '<input type="hidden" id="endval' . $sno . '" name="endval' . $sno . '" value="' . $dataentryattb->end_value . '" />';
                echo '</td>';
                echo '<td>' . $dataentryattb->uom_name . '</td>';
                echo '<td>' . $dataentryattb->comments . '</td>';
                //echo '<td>'.intval($dataentryattb->start_value).'</td>';
                //echo '<td>'.intval($dataentryattb->end_value).'</td>';
                //echo '<td>'.intval($dataentryattb->frequency_max_value).'</td>';
                echo "</tr>";
                $sno++;
            endforeach;
        endif;
        ?>
        <input type="hidden" id="dataattbcount" name="dataattbcount" value="<?php echo $sno - 1; ?>"/>
        </tbody>
    </table>
</fieldset>
</br>

<div class="row text-center text-danger" id="diverrormsg"></div>
<?php
//echo '<pre>';
//print_r($dataimages);
//print_r($reject_note);
//echo '</pre>';
//?>
<!-- Changed the condition to show the fieldset when the data entry type is both :Agaile -->
<fieldset style="<?php echo $is_image == 1 || $is_image == 2 ? '' : 'display: none;' ?>">
    <legend><?php echo $labelname[14]; ?></legend>
    <p style="text-align: right;"><?php echo $labelname[15]; ?> &nbsp &nbsp &nbsp <a href="javascript:void(0)"
                                                                                     data-toggle="modal"
                                                                                     data-target=".bs-example-modal-lg2">
            <button type="button" class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#myModal"
                    id="modaladd" name="modaladd">Upload
            </button>
        </a></p>
    <!--          modified by agaile in the view added a warning sign in front of validator comment   -->
    <?php if(!empty($reject_note->reject_notes)) {?>
    <small><?php echo $reject_note->reject_notes != '' ? '<b class="text-default">' . $labelname[13] . ': <span class="glyphicon glyphicon-warning-sign" style="color:red"> </span> </b>' . $reject_note->reject_notes . '' : '' ?></small>
    <?php } ?>
<!--    --><?php //echo $reject_note->reject_notes ?>
<!--    --><?php //echo $reject_note->data_validate_no ?>
    <?php
    if (count($dataimages) != 0) {
        ?>
        <table class="table table-striped table-hover" style="margin-top: 30px;" id="tableimage" name="tableimage">
            <thead>
            <tr>
                <th style="width: 5%;"><?php echo $labelname[9]; ?></th>
                <th style="width: 20%;"><?php echo $labelname[18]; ?></th>
                <th style="width: 40%;"><?php echo $labelname[17]; ?></th>
                <th style="width: 25%;"><?php echo $labelname[13]; ?></th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(empty($reject_note->reject_notes)) {
                $i = 0;
                foreach ($dataimages as $dataimage):
                    $i++;
                    echo '<tr style="cursor:all-scroll;" data-rowid="' . $dataimage->data_entry_pict_no . '">';
                    echo '<td class="tableimgno">' . $dataimage->pict_seq_no . '</td>';
                    echo '<td><a title="' . $dataimage->pict_definition . '" class="fancybox" rel="group" href="' . base_url() . $dataimage->pict_file_path . $dataimage->pict_file_name . '"><img src="' . base_url() . $dataimage->pict_file_path . $dataimage->pict_file_name . '" class="img-responsive" alt="" style="width: 200px; height: 137px;"></a></td>';
                    echo '<td class="image-description" data-picid="' . $dataimage->data_entry_pict_no . '"> <a style="cursor: pointer" class="text">' . wordwrap($dataimage->pict_definition, 40, "<br />\n", true) . '</a> <div class="edit" style="display:none;"><textarea id="img_dec_edit'.$i.'" name="image_description" class="form-control">' . wordwrap($dataimage->pict_definition, 40, "<br />\n", true) . '</textarea><input type="hidden" id="i_hidden" value="' . $i . '"><div id="counter_edit'.$i.'" style="display:none">You have <strong class="char_class_edit'.$i.'"> 80 </strong> characters remaining</div><input class="btn btn-primary btn-xs save" type="button" value="Save"/><input class="btn btn-xs btn-danger cancel" type="button" value="Cancel"/></div></td>';
                    echo '<td> ' . $dataimage->pict_validate_comment . ' </td>';
                    echo '<td><a href="#" data-toggle="modal" class="modaledit" data-target="#testmodal" data-picid="' . $dataimage->data_entry_pict_no . '" data-enno="' . $dataimage->data_entry_no . '" data-desc="' . $dataimage->pict_definition . '" data-pic-val-comment="' . $dataimage->pict_validate_comment . '" data-pic-seq-no="' . $dataimage->pict_seq_no . '" ><span class="glyphicon glyphicon-edit">&nbsp;</span></a></td>';
                    echo '<td> <a href="#" class="modaldelete" data-imgid="' . $dataimage->data_entry_pict_no . '" data-dataid="' . $dataimage->data_entry_no . '"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td>';
                    echo '</tr>';
                endforeach;
            }
            else{
                $i = 0;
                foreach ($dataimages as $dataimage):
                    $i++;
                    echo '<tr style="cursor:all-scroll;" data-rowid="' . $dataimage->data_entry_pict_no . '">';
                    echo '<td class="tableimgno">' . $dataimage->pict_seq_no . '</td>';
                    echo '<td><a title="' . $dataimage->pict_definition . '" class="fancybox" rel="group" href="' . base_url() . $dataimage->pict_file_path . $dataimage->pict_file_name . '"><img src="' . base_url() . $dataimage->pict_file_path . $dataimage->pict_file_name . '" class="img-responsive" alt="" style="width: 200px; height: 137px;"></a></td>';
                    echo '<td class="image-description" data-picid="' . $dataimage->data_entry_pict_no . '"> <a style="cursor: pointer" class="text">' . wordwrap($dataimage->pict_definition, 40, "<br />\n", true) . '</a> <div class="edit" style="display:none;"><textarea id="img_dec_edit'.$i.'" name="image_description" class="form-control">' . wordwrap($dataimage->pict_definition, 40, "<br />\n", true) . '</textarea><input type="hidden" id="i_hidden" value="' . $i . '"><div id="counter_edit'.$i.'" style="display:none">You have <strong class="char_class_edit'.$i.'"> 80 </strong> characters remaining</div><input class="btn btn-primary btn-xs save" type="button" value="Save"/><input class="btn btn-xs btn-danger cancel" type="button" value="Cancel"/></div></td>';
                    echo '<td> ' . $dataimage->pict_validate_comment . ' </td>';
                    echo '<td><a href="#" data-toggle="modal" class="modaledit" data-target="#testmodal" data-picid="' . $dataimage->data_entry_pict_no . '" data-enno="' . $dataimage->data_entry_no . '" data-desc="' . $dataimage->pict_definition . '" data-pic-val-comment="' . $dataimage->pict_validate_comment . '" data-pic-seq-no="' . $dataimage->pict_seq_no . '" data-rejnote="' .$reject_note->reject_notes.'" data-validate-no="'.$reject_note->data_validate_no.'" ><span class="glyphicon glyphicon-edit">&nbsp;</span></a></td>';
                    echo '<td> <a href="#" class="modaldelete" data-imgid="' . $dataimage->data_entry_pict_no . '" data-dataid="' . $dataimage->data_entry_no . '"><span class="glyphicon glyphicon-trash">&nbsp;</span></a></td>';
                    echo '</tr>';
                endforeach;
            }
            ?>
            </tbody>
        </table>
    <?php
    }
    ?>
</fieldset>

<div class="form-group" style="text-align: center;">
    <a href="javascript:void(0)" class="btn btn-success btn-sm" id="modalpublish" onclick="showloader();">Publish</a>
    &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
    <input type="submit" class="btn btn-primary btn-sm" value="Save"/>
    <a href="<?php echo base_url(); ?>journaldataentry" class="btn btn-danger btn-sm">Cancel</a>
</div>
</form>
<!-- -------------------------------------------- -->
<!-- pop-up -->
<div class="modal fade bs-example-modal-lg2" id="MyModal" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $labelname[14]; ?></h4>
            </div>

            <form method="post" id="addimage" enctype="multipart/form-data" onSubmit="return checkAndSendAllImages();">
                <div class="modal-body">
                    <input type="hidden" id="dataentryno1" name="dataentryno1" value="<?php echo $dataentryno; ?>"/>

                    <div style="text-align:center">
                        <button class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Add files...</span>
                            <input type="file" id="imagefile" name="file[]" multiple>
                        </button>
                        <button type="submit" class="btn btn-primary start">
                            <i class="glyphicon glyphicon-upload"></i>
                            <span>Start upload</span>
                        </button>
                    </div>
                    <table role="presentation" class="table table-striped table-vertical-middle">
                        <thead>
                        <tr>
                            <th>Picture</th>
                            <th>Description</th>
                            <th>Progress</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody class="files"></tbody>
                    </table>
                    <p style="font-size:12px">
                        <span style="text-decoration:underline">Notes:</span><br/>
                        Allowed image types: png, jpeg, jpg<br/>
                        Maximum image size: 10MB
                    </p>
                    <!--
							<div class="form-group">
								<div class="col-xs-4" style="text-align: right;"></div>
								<div class="col-xs-5"><label id="errorimage" class="text-danger"></label></div>
							</div>
							<br>
							<div class="form-group">
								<div class="col-xs-4" style="text-align: right;"><label class="control-label"><?php echo $labelname[16]; ?> <red>*</red></label></div>
								<div class="col-xs-5">
									<!--<img src="" class="img-responsive" id="imagePreview" alt="" style="width: 200px; height: 137px;"/>-->
                    <!--<input type="file" id="imagefile" name="files" onchange="PreviewImage();"/>-

								</div>
							</div>
							<div class="form-group">
								<div class="col-xs-4" style="text-align: right;"></div>
								<div class="col-xs-5"><label id="errordesc" class="text-danger"></label></div>
							</div>
							<div class="form-group">
								<div class="col-xs-4" style="text-align: right;"><label class="control-label"><?php echo $labelname[17]; ?> <red>*</red></label></div>
								<div class="col-xs-5"><textarea maxlength="500" class="form-control" rows="3" id="imagedesc" name="imagedesc"></textarea></div>
							</div>
							<br><br><br><br><br><br><br><br><br><br><br><br>-->

                </div>
                <div class="modal-footer">
                    <button type="button" class="closebutton btn btn-default btn-sm" data-dismiss="modal">Close</button>

                </div>
            </form>
        </div>
    </div>
</div>
<!-- close pop-up-->
<!-- -------------------------------------------- -->


<!-- -------------------- Start :  AGAILE ------------------------ -->

<div class="modal fade bs-example-modal-lg3" id="testmodal" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Update Picture</h4>
            </div>
            <div class="modal-body">
                <form method=post id=updateimage enctype="multipart/form-data"
                      onSubmit="return checkAndSendAllImages();">

                    <div class="modal-body">
                        <input type="hidden" id="dataentryno1" name="dataentryno1" value="<?php echo $dataentryno; ?>"/>
                        <input type="hidden" id="val_comment" name="val_comment"/>
                        <input type="hidden" id="seq_no" name="seq_no"/>
                        <div style="text-align:center">
                            <button class="btn btn-success fileinput-button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span>Add files...</span>
                                <input type="file" id="imagefile" name="file[]" multiple>
                            </button>
                            <button type="submit" class="btn btn-primary start" id="upld">
                                <i class="glyphicon glyphicon-upload"></i>
                                <span>Start upload</span>
                            </button>
                        </div>
                        <table role="presentation" class="table table-striped table-vertical-middle">
                            <thead>
                            <tr>
                                <th>Picture</th>
                                <th>Description</th>
                                <th>Progress</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody class="files"></tbody>
                        </table>
                        <p style="font-size:12px"><span style="text-decoration:underline">Notes:</span><br/>
                            Allowed image types: png, jpg, gif<br/>
                            Maximum image size: 10MB
                        </p>

                    </div>


                    <div class="modal-footer">
                        <button type="button" class="closebutton btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- --------------------End : AGAILE ------------------------ -->


<!-- pop-up -->
<!--	<div class="modal fade bs-example-modal-lg3" id="testmodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">-->
<!--	  	<div class="modal-dialog modal-lg">-->
<!--			<div class="modal-content">-->
<!--				<div class="modal-header">-->
<!--			    	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>-->
<!--			    	<h4 class="modal-title" id="myModalLabel">Update</h4>-->
<!--			  	</div>-->
<!--			  	<div class="modal-body">-->
<!--			  		<form method=post id=updateimage action="--><?php //echo base_url(); ?><!---->
<?php //echo $cpagename; ?><!--/updateimage/" enctype="multipart/form-data">-->
<!--						<div class="form-group">-->
<!--							<div class="col-xs-4" style="text-align: right;"></div>-->
<!--							<div class="col-xs-5"><label id="errorimage1" class="text-danger"></label></div>-->
<!--					  	</div>-->
<!--			  			<input type="hidden" id="picid" name="picid" />-->
<!--						<br/>-->
<!--						<div class="form-group">-->
<!--							<div class="col-xs-4" style="padding-left: 210px; text-align: right;"><label for="exampleInputFile" class="col-lg-3 control-label">-->
<?php //echo $labelname[18]; ?><!--</label></div>-->
<!--							<div class="col-xs-5" id="divimg" name="divimg"></div>-->
<!--					  	</div>-->
<!--				  		<br>-->
<!--				  		<div class="form-group">-->
<!--							<div class="col-xs-4" style="text-align: right;"></div>-->
<!--							<div class="col-xs-5"><label id="errordesc1" class="text-danger"></label></div>-->
<!--					  	</div>-->
<!--				  		<br>-->
<!--					  	<div class="form-group">-->
<!--							<div class="col-xs-4" style="text-align: right;"><label for="exampleInputEmail1" class="control-label">-->
<?php //echo $labelname[17]; ?><!--</label></div>-->
<!--							<div class="col-xs-5"><textarea class="form-control" rows="3" id="imagedesc1" name="imagedesc1"></textarea></div>-->
<!--					  	</div>-->
<!--					  	<br>-->
<!--					  	<br>-->
<!--					  	<br>-->
<!--					  	<br>-->
<!--						<br>-->
<!--					  	<br>-->
<!--					  	<br>-->
<!--					  	<br>-->
<!--				  		<div class="modal-footer">-->
<!--				    		<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>-->
<!--				    		<input type="submit" class="btn btn-primary btn-sm" value="Update" onclick="showloader();" />-->
<!--				  		</div>-->
<!--				  	</form>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
<!-- close pop-up-->
<!-- -------------------------------------------- -->
<!-- -------------------------------------------- -->
<!-- pop-up -->
<div class="modal fade bs-example-modal-lg3" id="MyModal2" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $labelname[19]; ?></h4>
            </div>
            <div class="modal-body">
                <div class="row text-center text-danger" id="errorvarient"></div>
                <form method=post id=addVarient action="<?php echo base_url(); ?><?php echo $cpagename; ?>/varient/">
                    <table class="table table-striped table-hover " id="varient">
                        <thead>
                        <tr>
                            <th><?php echo $labelname[9]; ?></th>
                            <th><?php echo $labelname[10]; ?></th>
                            <th><?php echo $labelname[20]; ?></th>
                            <th><?php echo $labelname[21]; ?></th>
                            <th><?php echo $labelname[13]; ?></th>
                            <th><?php echo $labelname[22]; ?></th>
                            <th><?php echo $labelname[23]; ?></th>
                            <th><?php echo $labelname[24]; ?></th>
                            <th><?php echo $labelname[25]; ?></th>
                            <th><?php echo $labelname[26]; ?><input type="hidden" id="varientcount"
                                                                    name="varientcount"/></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="10" align="center"><input type="submit" class="btn btn-primary btn-sm"
                                                                   value="Save" onclick="showloader();"/>&nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel
                                </button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- close pop-up-->
<!-- -------------------------------------------- -->
</div>
</div>

<script id="template-upload" type="text/x-tmpl">
{% for (var i=0; i < o.files.length; i++) { j++; var file=o.files[i]; var fileId = file.name.replace('.','_')+'_'+file.size; %}
    <tr class="template-upload fade">
        <td style="width:10%">
            <span class="preview"></span>
        </td>

        <td style="width:40%">
			<textarea id="iddesc{%=j%}" name="imagedesc_{%=fileId%}" maxlength="80" class="description-textarea textarea-fill" form="addimage" rows="5" onclick="img_desc_limit('{%=j%}');" ></textarea>
        <input type="hidden" id="i_hidden_upload" value="{%=j%}">
        <div id="counter{%=j%}" style="display:none">You have <strong class="char_class" id="idchar{%=j%}"> 80 </strong> characters remaining</div>
        </td>

        <td style="width:40%">
            <p class="name"><b>{%=file.name%}</b> - <span class="size">Processing...</span></p>
			<strong class="error text-danger"></strong>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
		<td style="width:10%;">
			{% if (!i) { %}
                <button class="btn btn-sm btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
		</td>
    </tr>
{% } %}



</script>


<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name set-w">
                {% if (!file.error) { %}
                    <span >{%=file.description%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } else { %}
				
			{% } %}
        </td>
        <td>
		<p>
			{% if (file.url) { %}<span class="label label-success">Successfull</span> {% } %}
            
		</p>
        </td>
        <td>
			<button class="btn btn-sm btn-warning remove" id="image_remove" onclick="remove_img('{%=file.url%}')">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Remove</span>
</button>
            <!--{% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-sm btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %} -->
        </td>
    </tr>
{% } %}



</script>
<!---->
<script>

    function revalidateInputs() {
        $ids = $('input[name^=dataattbid]').attr("disabled", "disabled");
        items = {};
        var index = 1;
        // Populate object to compare
        $.each($ids, function (idx, i) {
            var $t = $(this);
            var type = $t.siblings("*[name^=dataattbtype]").val();
            var id = $t.val();

            var dep = dependency[id];
            if (typeof dep == "undefined") dep = "";
            items[id] = {};
            items[id].index = index;
            if (type == "1") {
                // Type is textbox
                items[id].type = "1";
                items[id].max = $t.siblings("*[name^=endval]").val();
                items[id].value = $t.siblings("input[name=dataattb" + index + "]").val();
                items[id].reachedMax = parseFloat(items[id].max) == parseFloat(items[id].value);
            }
            else if (type == "2") {
                // Type is select
                items[id].type = "2";
                items[id].max = $t.siblings("*[name^=endval]").val();
                items[id].value = $t.siblings("select[name=dataattb" + index + "]").val();
                items[id].reachedMax = parseFloat(items[id].max) == parseFloat(items[id].value);
            }
            else if (type == "3") {
                // Type is incremental
                items[id].type = "3";
                items[id].min = $t.siblings("*[name^=minval]").val();
                items[id].max = $t.siblings("*[name^=endval]").val();
                items[id].value = $t.siblings(".input-group").children("#dataattb" + index).val();
                items[id].reachedMax = parseFloat(items[id].max) == parseFloat(items[id].value);
            }
            else if (type == "4") {
                // Type is radio
                items[id].type = "4";
                aaa = $t;
                items[id].max = $t.siblings("*[name^=endval]").val();
                items[id].value = $t.siblings("input[type=radio]:checked").val();
                items[id].reachedMax = (items[id].value.toLowerCase() == items[id].max.toLowerCase());//(items[id].value == "Yes");//parseFloat(items[id].max) == parseFloat(items[id].value);
            }
            items[id].dependOn = dep.split("|");
            index++;
        });

        // Loop to start disabling/enabling data attributes
        $.each(items, function (idx, i) {
            // idx is actually data attribute ID
            if (i.dependOn[0] == "") return true;
            if (i.type == "1") {
                // Text box
                var enable = true;
                for (var j = 0; j < i.dependOn.length; j++) {
                    if (!items[i.dependOn[j]].reachedMax) {
                        enable = false;
                        break;
                    }
                }
                if (enable) {
                    $("#dataattb" + i.index).removeAttr("disabled");
                } else {
                    $("#dataattb" + i.index).attr("disabled", "disabled");
                }
            } else if (i.type == "2") {
                // Select
                var enable = true;
                for (var j = 0; j < i.dependOn.length; j++) {
                    if (!items[i.dependOn[j]].reachedMax) {
                        enable = false;
                        break;
                    }
                }
                if (enable) {
                    $("#dataattb" + i.index).removeAttr("disabled");
                } else {
                    $("#dataattb" + i.index).attr("disabled", "disabled");
                }
            } else if (i.type == "3") {
                // Incremental
                var enable = true;
                var $igroup = $("#dataattbid" + i.index).siblings(".input-group");

                var $minus = $igroup.find(".minusbutton");
                var $add = $igroup.find(".addbutton");
                var $reset = $igroup.find(".resetbutton");

                for (var j = 0; j < i.dependOn.length; j++) {
                    console.log(items[i.dependOn[j]], items[i.dependOn[j]].reachedMax);
                    if (!items[i.dependOn[j]].reachedMax) {
                        enable = false;
                        break;
                    }
                }
                if (enable) {
                    $("#dataattb" + i.index).removeAttr("disabled");
                    $minus.attr("onclick", "addminus(" + i.min + "," + i.index + ")");
                    $add.attr("onclick", "addplus(" + i.max + "," + i.index + ")")
                    $reset.attr("onclick", "resetval(" + i.min + "," + i.index + ")");
                } else {
                    console.log("Disabling", i.index);
                    $("#dataattb" + i.index).attr("disabled", "disabled");
                    $minus.attr("onclick", "");
                    $add.attr("onclick", "")
                    $reset.attr("onclick", "");
                }
            } else if (i.type == "4") {
                // Radio
                var enable = true;
                for (var j = 0; j < i.dependOn.length; j++) {
                    if (!items[i.dependOn[j]].reachedMax) {
                        enable = false;
                        break;
                    }
                }
                if (enable) {
                    $("input[type=radio][id=dataattb" + i.index + "]").removeAttr("disabled");
                } else {
                    $("input[type=radio][id=dataattb" + i.index + "]").attr("disabled", "disabled");
                }
            }
        })
    }

    /*
     function PreviewImage(){
     var oFReader = new FileReader();
     oFReader.readAsDataURL(document.getElementById("imagefile").files[0]);

     oFReader.onload = function(oFREvent) {
     document.getElementById("imagePreview").src = oFREvent.target.result;
     }
     }*/

    // Validate inputs
    $(function () {


        window.dependency = $.parseJSON(<?php echo json_encode($dependency); ?>);
        revalidateInputs();
        $('#addRecord').find('input[type=text]').on('change', revalidateInputs);
        $('#addRecord').find('input[type=radio]').on('change', revalidateInputs);
        $('#addRecord').find('select').on('change', revalidateInputs);
        $(".fancybox").fancybox();
        var inputnumber = function () {
            var $t = $(this);
            var end = parseFloat($t.siblings("input[name^='endval']").val());
            var val = parseFloat($t.val());
            var digittype = parseInt($t.siblings("input[name^='dataattbvalidatedigit']").val());
            //console.log(digittype);
            // If it is a number field (must contain integer, not decimal), ignore
            if (digittype != 3) return;

            //console.log(val);
            //console.log(end);
            if (val > end) val = end;

            var filter = numeral(val).format('0.00');

            $t.val(filter);
        }

        $('.data-attribute-input-number').on("change", inputnumber);
        $('.data-attribute-input-number').each(inputnumber);
    })

</script>






