<script src="<?php echo base_url(); ?>ilyas/handsontable.full.min.js"></script>
<script src="<?php echo base_url(); ?>ilyas/moment.js"></script>
<script src="<?php echo base_url(); ?>ilyas/pikaday.js"></script>
<script src="<?php echo base_url(); ?>ilyas/jquery.json.js"></script>
<script src="<?php echo base_url(); ?>ilyas/ruleJS.all.full.js"></script>
<script src="<?php echo base_url(); ?>ilyas/handsontable.formula.js"></script>
<script src="<?php echo base_url(); ?>ilyas/ilyas.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/handsontable/handsontable.full.min.css"></link>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/css/pikaday.css"></link>

<?php
$j_id=$details->journal_no;
$pname=$details->project_name;
$jname=$details->journal_name;
$owner=$details->user_full_name;

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
	
	<!-- INPUT HERE-->
	<div id="after_header">
<div class="row">
	<div class="col-md-12">
	<!--<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b><?php //echo $labelname[3]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;">Week <?php //echo $week; ?></div>-->
	<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b><?php echo $labelname[0]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;"><?php echo $pname; ?></div>
	</br>
	<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b><?php echo $labelname[1]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;"><?php echo $jname; ?></div>
	</br>
	<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b><?php echo $labelname[4]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;"><?php echo $owner; ?></div>
	</br>
	<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b><?php echo $labelname[14]; ?></b></div>
<!--	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;">--><?php //echo $validator->user_full_name; ?><!--</div>-->
	<div id="vali_name" class="col-xs-9" style="color: blue; margin-bottom: 12px;"></div>
	</br>
	<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b>User</b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;" id="user_full_name">&nbsp</div>
	</br><!--
	<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b>Data date</b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;"><div class="input-group" style="width:145px;"><input type="text" id="data_date" style="height:30px; width:105px;border: 1px solid #aaa;border-right-width: 0px;"/><span class="input-group-btn"><button type="button" id="data_date_button" class="btn btn-search" style="height: 30px;padding: 5px 14px;border: 1px solid #aaa;border-left-width: 0px;"><span class="glyphicon glyphicon-calendar" style="color:black"></span></button></span><span class="input-group-btn"><button type="button" id="today_button" class="btn btn-search" style="height: 30px;padding: 5px 14px;border: 1px solid #aaa;border-left-width: 1px; color:black">Today</button></span></div></div>
	</br>-->
	<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b>History</b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 12px;"><select class="input" id="revision" onchange="fetch_vali_name(this.value)">
		<?php foreach($hot_revisions as $k=>$v): ?><option value="<?php echo $v->revision?>"><?php echo $v->timestamp.' (Rev. '.$v->revision.')'.' (by '.$v->user_full_name.')'; ?></option><?php endforeach; ?>
	</select></div>
	</br>
</div>
</div>
<div class="clearfix"></div>
	<div class="row"><div class="col-md-12">
	<div id="scroll_container" style="overflow:hidden;">
	<div id="hottable_container" style="margin-bottom:15px"></div>

	</div>
	</div>
	</div>
	<div class="row">
		<div class="col-md-12">
		<div id="notification">
		
    </div>
	</div>
	</div>
	
</div>
</div>

<!--Added by agaile to get the validator for non - progressive journal based on dropdownlist selected value-->
<!--Start : Here-->

<script>
    $( document ).ready(function() {
        var fval = $('#revision').val();
        var jno = " <?php echo $j_id ?> ";
        getvaliname(jno, fval); // function to get the selected value from dropdownlist on pageload
    });

    function getvaliname(jno,fval){
//      alert('journal_no: ' +jno+ ' Revision: ' +fval);
        $.ajax({
            type:'POST',
            url: "<?php echo base_url('index.php/ilyas/get_validator_details');?>",
            data:{jno : jno, rev : fval},
            dataType: "json",
            success:function (data) {
                if(data.status=="success"){
//                    console.log(data.name.user_full_name);
                    $('#vali_name').html((data.name.user_full_name)?data.name.user_full_name:'-');
                }else{
                    console.log(data.status);
                }
            },
            failure : function () {
                console.log(' Ajax Failure');
            },
            complete: function () {
                console.log("complete");
            }
        })
    }
</script>


<script>
    function fetch_vali_name(str) {
        var val = " <?php echo $j_id ?> ";
       // alert('Journal No: '+val +' Revision: '+ str);
        $.ajax({
            type:'POST',
            url: "<?php echo base_url('index.php/ilyas/get_validator_details');?>",
            data:{jno : val, rev : str},
            dataType: "json",
            success:function (data) {
                if(data.status=="success"){
//                    console.log(data.name.user_full_name);
                    $('#vali_name').html((data.name.user_full_name)?data.name.user_full_name:'-');
                }else{
                    console.log(data.status);
                }
            },
            failure : function () {
                console.log(' Ajax Failure');
            },
            complete: function () {
                console.log("complete");
            }
        })
    }
</script>

<!--End : Here-->
	  
<script>


function load_progressive_link(obj) {
	$.getJSON("<?php echo $this->config->base_url().'index.php/api/get_progressive_attributes?jid='; ?>"+obj.progressive_link, function(data){
		hot_object.fill_column(obj.order, data);
		//console.log('object',obj);
	})
}

function refresh_progressive_links() {
	for (var i = 0; i < hot_object.raw_config.length; i++) {
		var obj = hot_object.raw_config[i];
		
		if (obj.type == "progressive_link") {
			load_progressive_link(obj);
			//(function(saved_obj){})(obj);
		}
	}
}

function load_non_progressive_link(obj) {
	var link = obj.non_progressive_link.split('|');
	var jid = link[0];
	var config_no = link[1];
	$.getJSON("<?php echo $this->config->base_url().'index.php/api/get_nonp_column_value?jid='; ?>"+jid+"&config_no="+config_no, function(data){
		hot_object.fill_column(obj.order, data);
		console.log('object',obj);
	})
}

function refresh_non_progressive_links() {
	for (var i = 0; i < hot_object.raw_config.length; i++) {
		var obj = hot_object.raw_config[i];
		
		if (obj.type == "non_progressive_link") {
			load_non_progressive_link(obj);
		}
	}
}

$("#recordsearch").click(function () {
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
function showloader() {
	$('#after_header').loader('show');
}

function hideloader() {
	setTimeout(function(){$('#after_header').loader('hide');},200);
}

function notify(v) {
	var $div = $('<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">�</span></button>'+v+'</div>');
	$div.appendTo($('#notification'));
}

function lockTable() {
	hot_object.lock_table();
}

function lockButton() {
	$('#publishdata').attr('disabled','disabled');
	$('#savedata').attr('disabled','disabled');
	$('#data_date').attr('disabled','disabled').css('border-color','#ccc');
	$('#data_date_button').attr('disabled','disabled');
	$('#today_button').attr('disabled','disabled');
}

function remove_read_only(data){
	
	for(i = 0; i < hot_object.hot_serialize_data().length; i++){
		//remove read only data from the last index.
		for(j = read_only_rows.length-2; j >= 0; j--){
			if(read_only_rows[j] === 1){
				data[i].splice(j,1);
			}
		}
	}
	return data;
}

function hot_save_data(publish, callback) {

	// Invalid data detected. Dont save
	if ($('.htInvalid').length > 0) { alert("Data is invalid. Please rectify the highlighted cells"); return false; }
	if ($('#data_date').val() == "") { alert("Data date is invalid. Please select a correct date"); if(p_data_date) p_data_date.show(); return false; }
	var d = hot_object.hot_serialize_data();
	var d = remove_read_only(d);
	var p = ((typeof publish == "boolean") && (publish))

	var data = $.toJSON(d);
	
	console.log(data);
	showloader()
	//console.log(<?php echo $details->journal_no; ?>);
	$.post("<?php echo $this->config->base_url().'index.php/'.$cpagename; ?>/save_data?jid=<?php echo $details->journal_no; ?>&publish="+p.toString(), {data:data, data_date:p_data_date.toString()}).always(function(data){
		console.log(data);
		if ((data == 1) && (p)) {
			// Published! Now we should lock the table
			lockTable();
			lockButton();
			//location.reload();
			location.href="<?php echo $this->config->base_url(); ?>journaldataentry";
		}
		else {
			hideloader();
		}
		if (typeof callback == "function") callback();
	});
	/*var form = $('<form action="'+location.href+'" method="post"style="display:none;"><input type="hidden" name="data" id="data"/></form>')
	form.children('input#data').attr('value',data);
	form.appendTo($('body'));
	console.log(form.html());
	form.submit();*/
}

function hot_publish_data() {
	if (confirm("Confirm publish?")) hot_save_data(true);
}

function show_comments() {
	if (comments.length == 0) return;
	total = comments.reduce(function(c,n,a,s) {
		return c+n;
	}, "");
	if (total != "") {
		draw_comments(comments, 'dataentry');
	}
}

function load_data(rev) {
	
	showloader()
  
	$.getJSON("<?php echo $this->config->base_url().'index.php/'.$cpagename; ?>/get_data?jid=<?php echo $details->journal_no; ?>&rev="+rev, function(d){

		if (typeof d == 'undefined') return false;
		hot_object.hot_load_config(d.data.headers);
		hot_object.hot_load_data(d.data.data);
		
		var user = d.user;
		$('#user_full_name').text(user.user_full_name);
		hideloader();
	})
}

$(function(){

	p_data_date = new Pikaday({
		field:$('#data_date')[0],
		format: 'DD-MMM-YYYY',
        onSelect: function() {
            //console.log(this.getMoment().format('Do MMMM YYYY'));
        }
	})
	
	
	
	
	$('#today_button').on('click', function() {
		if (p_data_date) p_data_date.setDate(new Date());
	});
	
	$('#data_date_button').on('click', function() {
		/*if (p_data_date.isVisible()) {
			p.data_date
		}*/
		if (p_data_date) p_data_date.show();
	});
	
	$('#publishdata').on('click', function(){
		hot_publish_data();
	});
	
	$('#savedata').on('click', function(){
		hot_save_data();
	});
	
	$('#cancel').on('click', function(){
		location.href='<?php echo base_url(); ?>journaldataentry';
	});
	
	
	
	
	load_data($('#revision').val());
	
	$('#revision').on('change', function() {
		load_data($(this).val());
	});
	lookupdata = <?php echo json_encode($lookups); ?>;

	// Function to populate lookup codes from database
	$.each(lookupdata, function(idx,i){
		j = transpose(i.data);
		//console.log(i.meta.id);
		addLookupCode(i.meta.id, j[0], j[1]);
		//console.log(j);
	})
	
	raw_config = <?php echo json_encode($hot_config); ?>;
	//hot_config = hot_build_config(raw_config);
	hot_lock = <?php echo (($hot_lock == 1) ? "true" : "false" ); ?>;
	read_only_rows = <?php echo json_encode($hot_read_only_rows); ?>;
	data = <?php echo json_encode($hot_data); ?>;
	//comments = <?php echo json_encode($hot_comments); ?>;
	comments = <?php echo json_encode($new_comments); ?>;
	comments = $.map(comments,function(i,idx){return i.validate_comment_row});
	
	
	data_date = <?php echo json_encode($data_date); ?>;
	
	if (data_date != false) {  p_data_date.setDate(data_date); }
	else { p_data_date.setDate(new Date()); }
	
	var container = document.getElementById("hottable_container");
	var lastChange = null;
	
 
	hot_object = new HOT(Handsontable, raw_config, data, 'audit');
	/*if ((typeof comments == "object") && (comments.length > 0)) {
		for (var i = 0; i < comments.length; i++) {
			hot_object.hot_add_comment(comments[i]['row'], comments[i]['col'], comments[i]['comment']);
		}
	}*/
	refresh_progressive_links();
	refresh_non_progressive_links();
	
	//hot_object.hot_render_comment();
	
	hot_object.hot_instance.updateSettings({
		afterCreateRow: function(idx,amt){
			var a = [idx, 0];
			for (var i = 0; i < amt; i++) { a.push(""); }
			[].splice.apply(comments, a);
			show_comments()
		},
		afterRemoveRow: function(idx,amt){
			comments.splice(idx,amt);
			show_comments()
		},
		cells: function (row, col, prop) {
			var cellProperties = {};
			if(read_only_rows[row] === 1){
				cellProperties.readOnly = true;
			}
			return cellProperties;
		}
	})
	
	
	show_comments();
	
	lockTable();
	/*if (hot_lock) {
		lockTable();
		lockButton();
		notify("<strong>Validation for this journal is pending</strong>");
	}*/

});
</script>


