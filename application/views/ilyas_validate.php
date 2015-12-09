<script src="<?php echo base_url(); ?>ilyas/handsontable.full.min.js"></script>
<script src="<?php echo base_url(); ?>ilyas/moment.js"></script>
<script src="<?php echo base_url(); ?>ilyas/pikaday.js"></script>
<script src="<?php echo base_url(); ?>ilyas/jquery.json.js"></script>
<script src="<?php echo base_url(); ?>ilyas/ilyas.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/handsontable.full.min.css"></link>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/css/pikaday.css"></link>


<?php
//echo "LOLOLOL";
//var_dump($details);
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
		<h1 id="nav">Journal Validation Non Progressive<?php //echo $labelobject; ?></h1>
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
				</br><?php if ($data_date) { ?>
				<div class="col-xs-3" style="text-align: right; margin-bottom: 12px;"><b>Data date</b></div>
				<div class="col-xs-9" style="color: blue; margin-bottom: 12px;"><?php echo $data_date; ?></div>
				</br><?php } ?>
			</div>
		</div>
		<div class="clearfix"></div>
	<div class="row"><div class="col-md-12">
	<div id="scroll_container" style="overflow:hidden;">
	<div id="hottable_container" style="margin-bottom:15px"></div>
	<!--<div><p style="color:red; font-size:11px; font-style:italic">* You can add comments by right-clicking the above cells</p></div>-->
	</div>
	</div>
	</div>
	
	<fieldset>
				<legend>Validation</legend>
					<div class="row" style="width: 70%; margin: auto;">
					  <div class="col-xs-2" style="margin-bottom: 8px;">
						<div class="radio">
							<label>
								<input type="radio" id="optradio" name="optradio" value="Approve" onclick="enable_save_yn()">Approve
							</label>
						</div>
					  </div>
					 <!--<div class="col-xs-2" style="margin-bottom: 8px;">
						  <div class="radio">
								<label>
									<input type="radio" id="optradio" name="optradio" value="Close">Close
								</label>
							</div>
					  </div>-->
					  <div class="col-xs-3" style="margin-bottom: 8px;">
						<div class="radio">
							<label>
								<input type="radio" id="optradio" name="optradio" value="Reject" onclick="enable_save_yn()">Reject
							</label>
						</div>
					  </div><!--
					  <div class="col-xs-5" style="font-size: 14px; color: blue; margin-bottom: 12px;">
						<select class="form-control">
							<option>Administrator</option>
						</select>
					  </div>-->
					</div>
					<div class="row" style="width: 70%; margin: auto;">
					  <div class="col-xs-4" style="margin-bottom: 8px;">
						
					  </div>
					  <!--<div class="col-xs-3" style="margin-bottom: 8px;">Reject notes</div>
					  <div class="col-xs-5" style="color: blue; margin-bottom: 40px;">
						<textarea class="form-control" rows="5" id="comment" name="comment"></textarea>
					  </div>-->
					</div>
			</fieldset>
	<div class="row">
		<div class="col-md-12">
		<script type="text/javascript">
			function enable_save_yn(){
				document.getElementById("save").disabled = false; 
			}
		</script>
		<input type="button" class="btn btn-primary btn-sm" id="save" disabled="true" name="save" value="Save"/>
		<input type="button" class="btn btn-danger btn-sm" id="cancel" onClick="location.href = '<?php echo base_url(); ?>journalvalidationnonp'" name="cancel" value="Cancel"/>
		</div>
	</div>
</div>
</div>

<div id="comment_modal" style="display:hidden" class="modal fade in">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">Add a comment</div>
			<div>
			<div class="modal-body">
			<textarea maxlength="500" style="width:100%; min-height:100px;" id="comment_area"></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary btn-sm" onclick="add_comment()">Save</button>
				<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
			</div>
			</div>
		</div>
	</div>
</div>
	  
	  
<script>


function disablesubmit(){
	if ($('input[value=Reject]:checked').length > 0) {
		$('input[id="save"]').attr('disabled','disabled');
	} else if ($('input[value=Reject]:checked').length = 0) {
		$('input[id="save"]').removeAttr('disabled');
	};
}
function enablesubmit(){
	$('input[id="save"]').removeAttr('disabled');
}

function checkTextField() {
	if ($('input[value=Reject]:checked').length > 0) {
		var commentCount = document.getElementById("comment_area").value;
		var combine_id  = [];
		var combine = [];
		combine_id.push("comment"+i);
		combine.push("fromtext"+i);
		var arrCombineId = combine_id;
		var commentValue = "";
		commentValue = document.getElementById(arrCombineId[i]).value;
		console.log(commentValue);
		if (commentValue == '') {
			$('input[id="save"]').attr('disabled','disabled');
		}else{
			$('input[id="save"]').removeAttr('disabled');
		};
	}
}

/*
function showloader() {
	$('#after_header').loader('show');
}

function hideloader() {
	setTimeout(function(){$('#after_header').loader('hide')},200);
}*/

var current_comment = null;

function get_comments() {
	return $('.ht_clone_right td:last-child').map(function(idx,i){return $(i).text().trim()}).toArray();
	//return $('.comment_text').map(function(idx, i){var d = $.data(i,'comment'); return (typeof d != "undefined") ? d : ""}).toArray();
}

function show_comment(a) {
	var $c = $(a).parent().siblings('.comment_text');
	$('#comment_modal').modal('show');
	current_comment = $c;
	$('#comment_area').val($c.text());
}
function add_comment() {
	if (current_comment == null) return;
	
	var text = $('#comment_area').val();
	
	//current_comment.text(truncText(text, 22, '...'));
	//$.data(current_comment[0],'comment',text);
	current_comment.text(text);
	$('input[value="Reject"]').attr('checked','checked');
	var comments = get_comments();
	synchronize_comments(comments, true);
	$('#comment_modal').modal('hide');
	enablesubmit();
}


function validate() {

	// Change comment into configno 
	var configs = cols_to_config_no(hot_object.raw_config);
	var c = hot_object.hot_serialize_comment();
	for (var i = 0; i < c.length; i++) {
		c[i]['config_no'] = configs[c[i]['col']];
	}
	//console.log(c);
	var comments = get_comments();
	var opt = $("#optradio:checked").val();
	if ((opt == "Reject") && (comments.join("") == "")) {
		alert("Please write at least 1 comment");
		return;
	}
	var data = {
		"optradio": $("#optradio:checked").val(),
		"reject_notes": $("#comment").val(),
		"comments": get_comments()// change this to "c" if you want cell-level comment
	}
	showloader();
	
	$.post("<?php echo $this->config->base_url().'index.php/'.$cpagename; ?>/validate?jid=<?php echo $details->journal_no; ?>", data).always(function(data){
		console.log(data);
		hideloader();
		disallow();
		location.href='<?php echo $this->config->base_url() ?>/index.php/journalvalidationnonp';
		if (typeof callback == "function") callback();
	});
}

function disallow() {
	$('#save, #optradio, #comment').attr('disabled','disabled');
}

$(function(){

	$('#save').on('click', function(){
		
		if ($('input[value="Approve"]:checked').length > 0){
			var msg = "Confirm Approve?";
		}
		if ($('input[value="Reject"]:checked').length > 0){
			var msg = "Confirm Reject?";
		}
		
		var a = confirm(msg);
		if (a) {
			validate();
			return true;
		} else {
			return false;
		}
	});
	
	
	
	raw_config = <?php echo json_encode($hot_config); ?>;
	//hot_config = hot_build_custom_config(raw_config);
	data = <?php echo json_encode($hot_data); ?>;
	lookupdata = <?php echo json_encode($lookups); ?>;
	hot_lock = <?php echo (($hot_lock == 1) ? "true" : "false" ); ?>;
	
	// Function to populate lookup codes from database
	$.each(lookupdata, function(idx,i){
		j = transpose(i.data);
		//console.log(i.meta.id);
		addLookupCode(i.meta.id, j[0], j[1]);
	
	});
	
	
	var container = document.getElementById("hottable_container");
	var lastChange = null;
	
 
 
	hot_object = new HOT(Handsontable, raw_config, data, 'validate');
	if (!hot_lock) { disallow(); }
	else draw_comments([],"validation");
	
/*
	hot.updateSettings({
		beforeKeyDown: function (e) {
		  var selection = hot.getSelected();

		  // BACKSPACE or DELETE
		  if (e.keyCode === 8 || e.keyCode === 46) {
			e.stopImmediatePropagation();
			// remove data at cell, shift up
			hot.spliceCol(selection[1], selection[0], 1);
			e.preventDefault();
		  }
		  // ENTER
		  else if (e.keyCode === 13) {
			// if last change affected a single cell and did not change it's values
			if (lastChange && lastChange.length === 1 && lastChange[0][2] == lastChange[0][3]) {
			  e.stopImmediatePropagation();
			  hot.spliceCol(selection[1], selection[0], 0, ''); // add new cell
			  hot.selectCell(selection[0], selection[1]); // select new cell
			}
		  }

		  lastChange = null;
		}
	  }
	);*/

});
</script>


