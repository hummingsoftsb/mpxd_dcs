<script src="<?php echo base_url(); ?>ilyas/handsontable.full.min.js"></script>
<script src="<?php echo base_url(); ?>ilyas/moment.js"></script>
<script src="<?php echo base_url(); ?>ilyas/pikaday.js"></script>
<script src="<?php echo base_url(); ?>ilyas/jquery.json.js"></script>
<script src="<?php echo base_url(); ?>ilyas/ruleJS.all.full.js"></script>
<script src="<?php echo base_url(); ?>ilyas/handsontable.formula.js"></script>
<script src="<?php echo base_url(); ?>ilyas/ilyas.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/handsontable.full.min.css"></link>
<link rel="stylesheet" href="<?php echo base_url(); ?>ilyas/css/pikaday.css"></link>


<?php
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
	<div class="row">
		<div class="col-md-4">
			<ul class="breadcrumb">
				<li><a href="<?php echo base_url(); ?>home">Home</a></li>
                <li>Design</li>
                <li class="active">Non Progressive Journal</li>
			</ul>
		</div>
	</div>
	<div id="after_header">
	<div class="form-group">
			<button type="button" class="btn btn-success btn-sm pull-right" id="modaladd" data-toggle="modal" data-target="#myModal">Add New Column</button>
		</div>
<div class="row">
	<div class="col-md-12">
	<!--<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php //echo $labelname[3]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 8px;">Week <?php //echo $week; ?></div>-->
	<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[0]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $pname; ?></div>
	</br>
	<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[1]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $jname; ?></div>
	</br>
	<div class="col-xs-3" style="text-align: right; margin-bottom: 8px;"><b><?php echo $labelname[4]; ?></b></div>
	<div class="col-xs-9" style="color: blue; margin-bottom: 8px;"><?php echo $owner; ?></div>
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
			<input type="button" class="btn btn-default btn-sm" id="cancel" value="Back">
			<input type="button" class="btn btn-primary btn-sm" id="savedata" name="savedata" value="Save">
		</div>
	</div>
</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<form id=addrecord>
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Add New Column</h4>
					</div>
					<div class="modal-body">
						<div class="row">
    						<div class="form-group">
    							<label for="" class="col-sm-3 control-label"></label>
						    	<div class="col-sm-6">
						    		<label id="errorlabel" class="text-danger"></label>
						        </div>
							</div>
						</div>
						<div class="row" style="margin-bottom:15px">
    						<div class="form-group">
    							<label for="column_title" class="col-sm-4 control-label">Column title <red>*</red></label>
						    	<div class="col-sm-6">
						    		<input type="text" class="form-control" id="column_title" name="column_title" maxlength="60">
						        </div>
							</div>
						</div>
						<div class="row" style="margin-bottom:15px">
    						<div class="form-group">
    							<label for="column_type" class="col-sm-4 control-label">Column type <red>*</red></label>
						    	<div class="col-sm-5">
						    		<select class="form-control" id="column_type" name="column_type">
												<option value="text">Text</option>
												<option value="date">Date</option>
												<option value="percentround2">Percent</option>
												<option value="price_myr">Ringgit</option>
												<option value="numeric">Number</option>
												<option value="decimal2">Decimal (2)</option>
												<option value="lookup">Lookup</option>
												<option value="progressive_link">Progressive Link</option>
												<option value="non_progressive_link">Non-Progressive Link</option>
												<option value="formula">Formula</option>
												<!--<option value="checkbox" disabled=true>Checkbox</option>-->
									</select>
						        </div>
							</div>
						</div>
						
						<div id="lookup_container"></div>
						<div id="link_container"></div>
						<div id="nonp_link_container"></div>
						<div id="formula_container"></div>
						
						
						<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Unit of Measurement </label>
								<div class="col-sm-5">
									<select class="form-control" id="uom" name="uom">
										<?php
											foreach ($uoms as $uom):
										?>
												<option value="<?php echo $uom->uom_id; ?>" <?php if ($uom->uom_id == 12) echo "SELECTED"?>><?php echo $uom->uom_name; ?></option>
										<?php
											endforeach;
										?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row" style="margin-bottom:15px">
							<div class="form-group">
								<label for="readonly" class="col-sm-4 control-label">Read only </label>
								<div class="col-sm-5">
									<input type="checkbox" id="readonly" name="readonly" style="font-size:20px"/>
								</div>
							</div>
						</div>
						
						<div style="display:none" id="hiddenstuff">
							<div id="lookup_content" class="row" style="margin-bottom:15px">
								<div class="form-group">
									<label for="column_type" class="col-sm-4 control-label">Lookup <red>*</red></label>
									<div class="col-sm-5">
										<select class="form-control" id="lookup" name="lookup">
													
										</select>
									</div>
								</div>
							</div>
							
							<div id="link_content" class="row" style="margin-bottom:15px">
								<div class="form-group">
									<label for="column_type" class="col-sm-4 control-label">Journal <red>*</red></label>
									<div class="col-sm-5">
										<select class="form-control" id="link_jid" name="link_jid">
											<?php foreach ($journal_list as $k=>$v):
												echo "<option value='". $v->journal_no ."'>". $v->journal_name ."</option>";
											endforeach;
											?>
										</select>
									</div>
								</div>
							</div>
							
							
							<div id="nonp_link_content" class="row" style="margin-bottom:15px">
								<div class="form-group">
									<label for="column_type" class="col-sm-4 control-label">Journal <red>*</red></label>
									<div class="col-sm-5">
										<select class="form-control" id="nonp_link_jid" name="nonp_link_jid">
											<option SELECTED></option>
											<?php foreach ($nonp_journal_list as $k=>$v):
												echo "<option value='". $v->journal_no ."'>". $v->journal_name ."</option>";
											endforeach;
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="column_type" class="col-sm-4 control-label" style="margin-top:15px">Column <red>*</red></label>
									<div class="col-sm-5" style="margin-top:15px">
										<select class="form-control" id="nonp_link_column" name="nonp_link_column">
											
										</select>
									</div>
								</div>
							</div>
							
							<div id="formula_content" class="row" style="margin-bottom:15px">
								<div class="form-group">
									<label for="column_type" class="col-sm-4 control-label">Formula <red>*</red></label>
									<div class="col-sm-5">
										<input class="form-control" id="formula" name="formula" type="text">											
									</div>
								</div>
							</div>
							
							
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary btn-sm" id="add_new_column"/>Add New</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	
	  
<script>


function showloader() {
	$('#after_header').loader('show');
}

function hideloader(cb) {
	setTimeout(function(){
	$('#after_header').loader('hide'); 
	if (typeof cb == "function") cb();
	},200);
}


function hot_save_config(callback) {
	var c = hot_object.raw_config;
	var isReadonlyExist = false;
	for (var i = 0; i < c.length; i++) {
		if (c[i]["readonly"]) { isReadonlyExist = true; break; }
	}
	
	// Invalid data detected. Dont save
	if (isReadonlyExist) {
		if ($('.htInvalid').length > 0) { alert("Data is invalid. Please rectify the highlighted cells"); return false; }
	}
	showloader();
	var raw_config = hot_object.raw_config;
	
	console.log("Saving config..");
	$.post("<?php echo $this->config->base_url().'index.php/'.$cpagename; ?>/save_config?jid=<?php echo $dataentryno; ?>", {config:$.toJSON(raw_config)}).always(function(data){
		console.log(data);
		//if (isReadonlyExist) {
			var d = hot_object.hot_serialize_data();
			var data = $.toJSON(d);
			//console.log(data);
			console.log("Saving read only data..");
			$.post("<?php echo $this->config->base_url().'index.php/'.$cpagename; ?>/save_data?jid=<?php echo $details->journal_no; ?>&publish=false", {data:data}).always(function(data){
				console.log(data);
				hideloader();
				// location.href="<?php echo base_url(); ?>designjournalnonp_ilyas";
			});
		/*} else {
			hideloader();
		}*/
		if (typeof callback == "function") { callback(); }
		
	});
}

function initialize_hot() {
	if ((typeof hot_object == "undefined") || (hot_object == null)) {
		hot_object = new HOT(Handsontable, raw_config, [[]], 'design');
		hot_object.register_edit_callback(edit_callback);
		refresh_progressive_links();
		refresh_non_progressive_links();
	}
	
}

function add_new_column(title,type,uom,extra) {
	initialize_hot();
	hot_object.add_new_column(title,type,uom,extra);
	if (type == "progressive_link") {
		load_progressive_link(hot_object.raw_config[hot_object.raw_config.length-1]);
		//refresh_progressive_links();
	} else if (type == "non_progressive_link") {
		load_non_progressive_link(hot_object.raw_config[hot_object.raw_config.length-1]);
	}
}

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

function edit_column_click() {
	var originaltitle = $edit_modal.children('#original_title').val();
	var title = $edit_modal.find('#column_title_edit').val();
	var type = $edit_modal.find('#column_type_edit').val();
	var uom = $edit_modal.find('#uom_edit').val();
	var extra = {};
	if (type == "lookup") { extra["lookup_id"] = $edit_modal.find("#lookup_edit").val() }
	if (type == "progressive_link") { extra["progressive_link"] = $edit_modal.find("#link_jid_edit").val() }
	if (type == "non_progressive_link") { console.log("NONPROG@!!", $edit_modal.find("#nonp_link_jid_edit").val() + "|" + $edit_modal.find("#nonp_link_column_edit").val()); extra["non_progressive_link"] = $edit_modal.find("#nonp_link_jid_edit").val() + "|" + $edit_modal.find("#nonp_link_column_edit").val() }
	if (type == "formula") { extra["formula"] = $edit_modal.find("#formula_edit").val(); console.log(extra); }
	extra["readonly"] = $edit_modal.find('#readonly_edit').prop('checked');
	
	hot_object.edit_column(originaltitle,title,type,uom,extra);
	console.log('wut',hot_object.get_config_by_column_header(title));
	if (type == "progressive_link") {
		load_progressive_link(hot_object.get_config_by_column_header(title));
	} else if (type == "non_progressive_link") {
		load_non_progressive_link(hot_object.get_config_by_column_header(title));
	}
	$edit_modal.modal('hide');
	
}

function edit_callback(object) {
	console.log(object);
	$edit_modal.modal('show');
	$edit_modal.find('#column_title_edit').val(object.header);
	$edit_modal.find('#column_type_edit').val(object.type);
	$edit_modal.find('#uom_edit').val(object.uom);
	$edit_modal.find('#formula_edit').val(object.formula);
	
	$edit_modal.children('#original_title').val(object.header);
	
	if (object.type == "date") {
		$edit_modal.find('#uom_edit').val('3').attr('disabled','true'); /* Date */
	} else {
		$edit_modal.find('#uom_edit').val('12').removeAttr('disabled'); /* Not selected */
	}
	
	if ((object.type == 'lookup') && (object.lookup_id != null)) {
		$edit_modal.find('#lookup_content_edit').appendTo($edit_modal.find('#lookup_container_edit'));
		$edit_modal.find('#lookup_edit').val(object.lookup_id);
	} else {
		//console.log('hiding it',object)
		$edit_modal.find('#lookup_content_edit').appendTo($('#hiddenstuff_edit'));
	}
	
	
	if ((object.type == 'progressive_link') && (object.progressive_link != null)) {
		$edit_modal.find('#link_content_edit').appendTo($edit_modal.find('#link_container_edit'));
		$edit_modal.find('#link_jid_edit').val(object.progressive_link);
		$edit_modal.find('#uom_edit').attr('disabled','disabled');
		$edit_modal.find('#readonly_edit').attr('disabled','disabled');
	} else {
		//console.log('hiding it 2',object)
		$edit_modal.find('#link_content_edit').appendTo($('#hiddenstuff_edit'));
		$edit_modal.find('#uom_edit').removeAttr('disabled');
		$edit_modal.find('#readonly_edit').removeAttr('disabled');
	}
	
	if ((object.type == 'non_progressive_link') && (object.non_progressive_link != null)) {
		var nonp_link = object.non_progressive_link.split("|");
		
		$edit_modal.find('#nonp_link_content_edit').appendTo($edit_modal.find('#nonp_link_container_edit'));
		//console.log('WOTT',object.non_progressive_link.split("|"));
		$edit_modal.find('#nonp_link_jid_edit').val(nonp_link[0]);
		
		populate_nonp_columns($edit_modal.find('#nonp_link_column_edit'), nonp_link[0], function(){ console.log("CALLED",nonp_link[1]); $edit_modal.find('#nonp_link_column_edit').val(nonp_link[1]); })
		
		$edit_modal.find('#uom_edit').attr('disabled','disabled');
		$edit_modal.find('#readonly_edit').attr('disabled','disabled');
	} else {
		//console.log('hiding it 2',object)
		$edit_modal.find('#nonp_link_content_edit').appendTo($('#hiddenstuff_edit'));
		$edit_modal.find('#uom_edit').removeAttr('disabled');
		$edit_modal.find('#readonly_edit').removeAttr('disabled');
	}
	
	if ((object.type == 'formula')) {
		$edit_modal.find('#formula_content_edit').appendTo($edit_modal.find('#formula_container_edit'));
	} else {
		$edit_modal.find('#formula_content_edit').appendTo($('#hiddenstuff_edit'));
	}
	
	$edit_modal.find('#readonly_edit').prop('checked',object.readonly);
}

// Function to generate edit modal on the fly, so that hopefully you dont have to maintain 2 modals
function generate_edit_modal() {
	$edit_modal = $('#myModal').clone();
	
	$edit_modal.find('#column_title').attr('id','column_title_edit').attr('name','column_title_edit');
	$edit_modal.find('#column_type').attr('id','column_type_edit').attr('name','column_type_edit').on('change',function(e){
		//console.log('change',e);
		$t = $(this);
		var val = $t.val();
		
		if (val != "date") {
			$edit_modal.find('#uom_edit').val('12').removeAttr('disabled');
		}
		if (val != "lookup") {
			$edit_modal.find('#lookup_content_edit').appendTo($('#hiddenstuff_edit'));
		}
		if (val != "progressive_link") {
			$edit_modal.find('#link_content_edit').appendTo($('#hiddenstuff_edit'));
			$edit_modal.find('#uom_edit').removeAttr('disabled');
			$edit_modal.find('#readonly_edit').removeAttr('disabled').attr('checked',false);
		}
		if (val != "non_progressive_link") {
			$edit_modal.find('#nonp_link_content_edit').appendTo($('#hiddenstuff_edit'));
			$edit_modal.find('#uom_edit').removeAttr('disabled');
			$edit_modal.find('#readonly_edit').removeAttr('disabled').attr('checked',false);
		}
		if (val != "formula") {
			$edit_modal.find('#formula_content_edit').appendTo($('#hiddenstuff_edit'));
			//$edit_modal.find('#uom_edit').removeAttr('disabled');
			//$edit_modal.find('#readonly_edit').removeAttr('disabled').attr('checked',false);
		}
		
		
		
		switch(val) {
			case "date":
				$edit_modal.find('#uom_edit').val('3').attr('disabled','true');
				break;
			case "lookup":
				$edit_modal.find('#lookup_content_edit').appendTo($('#lookup_container_edit'));
				break;
			case "progressive_link":
				$edit_modal.find('#link_content_edit').appendTo($('#link_container_edit'));
				$edit_modal.find('#uom_edit').attr('disabled','disabled');
				$edit_modal.find('#readonly_edit').attr('disabled','disabled').attr('checked',true);
				break;
			case "non_progressive_link":
				$edit_modal.find('#nonp_link_content_edit').appendTo($('#nonp_link_container_edit'));
				$edit_modal.find('#uom_edit').attr('disabled','disabled');
				$edit_modal.find('#readonly_edit').attr('disabled','disabled').attr('checked',true);
				break;
			case "formula":
				$edit_modal.find('#formula_content_edit').appendTo($('#formula_container_edit'));
				//$edit_modal.find('#uom_edit').attr('disabled','disabled');
				//$edit_modal.find('#readonly_edit').attr('disabled','disabled').attr('checked',true);
				break;
				
		}
		
		/*
		if (val == "date") {
			$edit_modal.find('#uom_edit').val('3').attr('disabled','true'); /* Date *
		} else {
			$edit_modal.find('#uom_edit').val('12').removeAttr('disabled'); /* Not selected *
		}
		
		if (val == 'lookup') {
			$edit_modal.find('#lookup_content_edit').appendTo($('#lookup_container_edit'));
		} else {
			$edit_modal.find('#lookup_content_edit').appendTo($('#hiddenstuff_edit'));
		}
		
		if (val == 'progressive_link') {
			$edit_modal.find('#link_content_edit').appendTo($('#link_container_edit'));
			$edit_modal.find('#uom_edit').attr('disabled','disabled');
			$edit_modal.find('#readonly_edit').attr('disabled','disabled');
		} else {
			$edit_modal.find('#link_content_edit').appendTo($('#hiddenstuff_edit'));
			$edit_modal.find('#uom_edit').removeAttr('disabled');
			$edit_modal.find('#readonly_edit').removeAttr('disabled');
		}*/
		
		$edit_modal.find('#column_type').data('previous',val);
	});
	$edit_modal.find('#uom').attr('id','uom_edit').attr('name','uom_edit');
	$edit_modal.find('#readonly').attr('id','readonly_edit').attr('name','readonly_edit');
	$edit_modal.find('#hiddenstuff').attr('id','hiddenstuff_edit').attr('name','hiddenstuff_edit');
	$edit_modal.find('#lookup').attr('id','lookup_edit').attr('name','lookup_edit');
	$edit_modal.find('#lookup_content').attr('id','lookup_content_edit').attr('name','lookup_content_edit');
	$edit_modal.find('#lookup_container').attr('id','lookup_container_edit').attr('name','lookup_container_edit');
	
	$edit_modal.find('#link_jid').attr('id','link_jid_edit').attr('name','link_jid_edit');
	$edit_modal.find('#link_content').attr('id','link_content_edit').attr('name','link_content_edit');
	$edit_modal.find('#link_container').attr('id','link_container_edit').attr('name','link_container_edit');
	
	$edit_modal.find('#nonp_link_jid').attr('id','nonp_link_jid_edit').attr('name','nonp_link_jid_edit');
	$edit_modal.find('#nonp_link_content').attr('id','nonp_link_content_edit').attr('name','nonp_link_content_edit');
	$edit_modal.find('#nonp_link_container').attr('id','nonp_link_container_edit').attr('name','nonp_link_container_edit');
	$edit_modal.find('#nonp_link_column').attr('id','nonp_link_column_edit').attr('name','nonp_link_column_edit');
	
	$edit_modal.find('#formula_content').attr('id','formula_content_edit').attr('name','formula_content_edit');
	$edit_modal.find('#formula_container').attr('id','formula_container_edit').attr('name','formula_container_edit');
	$edit_modal.find('#formula').attr('id','formula_edit').attr('name','formula_edit');
	
	
	$edit_modal.find('.modal-title').text('Edit Column');
	$edit_modal.find('#add_new_column').attr('id','edit_column').text('Confirm Edit').on('click', function(){
		edit_column_click();
	})
	
	$edit_modal.append('<input type="hidden" id="original_title" value=""/>')
	
	$edit_modal.find('#nonp_link_jid_edit').on('change',function() {
		var $t = $(this);
		var val = $t.val();
		populate_nonp_columns($('#nonp_link_column_edit'), val);
	});
}

/*
function toggleLookup(b) {
	if (b) $('#lookup_content').appendTo($('#lookup_container'));
	else $('#lookup_content').appendTo($('#hiddenstuff'));
}

function toggleLink(b) {
	if (b) { 
		$('#link_content').appendTo($('#link_container'));
		$('#readonly').attr('disabled','disabled').attr('checked',true);
		$('#uom').attr('disabled','disabled');
	}
	else  {
		$('#link_content').appendTo($('#hiddenstuff'));
		$('#readonly').removeAttr('disabled').attr('checked',true);
		$('#uom').removeAttr('disabled');
	}
}*/


function populate_nonp_columns($e, jid, callback) {
	$.getJSON('<?php echo base_url(); ?>api/get_nonp_columns?jid='+jid, function(data){
		var opts = [];
		for (var i = 0; i < data.length; i++) {
			opts.push("<option value='"+data[i].config_no+"'>"+data[i].col_header+"</option>");
		}
		$e.empty().append($(opts.join("")));
		if (typeof callback == "function") callback();
	});
}

$(function(){
	
	var formhandler = function() {
		var title = $.trim($('#column_title').val());
		var type = $('#column_type').val();
		var uom = $('#uom').val();
		var readonly = $("#readonly").is(":checked");
		
		var extra = {};
		
		if (type == "lookup") { extra["lookup_id"] = $("#lookup").val()}
		if (type == "progressive_link") { extra["progressive_link"] = $("#link_jid").val() }
		if (type == "non_progressive_link") { extra["non_progressive_link"] = $("#nonp_link_jid").val() +"|"+$("#nonp_link_column").val() }
		if (type == "formula") { extra["formula"] = $("#formula").val() }
		
		if (readonly != "undefined") { extra["readonly"] = readonly; }
		
		console.log(extra);
		add_new_column(title,type,uom,extra);
		$('#myModal').modal('toggle');
		$('#column_title').val('');
	}
	$('#savedata').on('click', function(){
		hot_save_config();
	});
	$('#cancel').on('click', function(){
		location.href="<?php echo base_url(); ?>designjournalnonp_ilyas";
	});
	
	$('#addrecord').on('submit', function(e) {
		formhandler();
		e.preventDefault();
		return false;
	})
	
	$('#add_new_column').on('click', function(){
		formhandler();
	});
	
	$('#column_type').on('change', function(){
		$t = $(this);
		var val = $t.val();
		var $myModal = $('#myModal');
		
		if (val != "date") {
			$myModal.find('#uom').val('12').removeAttr('disabled');
		}
		if (val != "lookup") {
			$myModal.find('#lookup_content').appendTo($('#hiddenstuff'));
		}
		if (val != "progressive_link") {
			$myModal.find('#link_content').appendTo($('#hiddenstuff'));
			$myModal.find('#uom').removeAttr('disabled');
			$myModal.find('#readonly').removeAttr('disabled').attr('checked',false);
		}
		
		if (val != "non_progressive_link") {
			$myModal.find('#nonp_link_content').appendTo($('#hiddenstuff'));
			$myModal.find('#uom').removeAttr('disabled');
			$myModal.find('#readonly').removeAttr('disabled').attr('checked',false);
		}
		
		if (val != 'formula') {
			$myModal.find('#formula_content').appendTo($('#hiddenstuff'));
			//$myModal.find('#uom').removeAttr('disabled');
			//$myModal.find('#readonly').removeAttr('disabled').attr('checked',false);
		}
		
		switch (val) {
			case "date":
				$myModal.find('#uom').val('3').attr('disabled','true');
				break;
			case "lookup":
				$myModal.find('#lookup_content').appendTo($('#lookup_container'));
				break;
			case "progressive_link":
				$myModal.find('#link_content').appendTo($('#link_container'));
				$myModal.find('#uom').attr('disabled','disabled');
				$myModal.find('#readonly').attr('disabled','disabled').attr('checked',true);
				break;
			case "non_progressive_link":
				$myModal.find('#nonp_link_content').appendTo($('#nonp_link_container'));
				$myModal.find('#uom').attr('disabled','disabled');
				$myModal.find('#readonly').attr('disabled','disabled').attr('checked',true);
				break;
			case "formula":
				$myModal.find('#formula_content').appendTo($('#formula_container'));
				//$myModal.find('#uom').attr('disabled','disabled');
				//$myModal.find('#readonly').attr('disabled','disabled').attr('checked',true);
				break;
		}
		
		$('#nonp_link_jid').on('change',function() {
			var $t = $(this);
			var val = $t.val();
			populate_nonp_columns($('#nonp_link_column'), val);
		});
		
		
		
		
		/*
		if (val == "date") {
			$('#uom').val('3').attr('disabled','true'); /* Date *
		} else {
			$('#uom').val('12').removeAttr('disabled'); /* Not selected *
		}
		
		if (val == "lookup") {
			toggleLookup(true);
		} else {
			toggleLookup(false);
		}
		
		if (val == "progressive_link") {
			toggleLink(true);
		} else {
			toggleLink(false);
		}*/
	});
	
	
	raw_config = <?php echo json_encode($hot_config); ?>;
	///kikiki = _.cloneDeep(raw_config);
	data = <?php echo json_encode($hot_data); ?>;
	lookupdata = <?php echo json_encode($lookups); ?>;
	hot_lock = <?php echo (($hot_lock == 1) ? "true" : "false" ); ?>;
	hot_object = null;
	
	
	
	$.each(lookupdata, function(idx,i){$('#lookup').append($("<option value='"+i.meta.id+"'>"+idx+"</option>"));})
	
	// Function to populate lookup codes from database
	$.each(lookupdata, function(idx,i){
		j = transpose(i.data);
		//console.log(i.meta.id);
		addLookupCode(i.meta.id, j[0], j[1]);
	
	})
	
	
	//hot_config = hot_build_custom_config(raw_config);
	
	if (raw_config.length > 0) {
		hot_object = new HOT(Handsontable, raw_config,data,'design');
		if (hot_lock) { console.log(hot_lock); $('#modaladd').attr('disabled','disabled'); $('#savedata').attr('disabled','disabled'); }
		hot_object.register_edit_callback(edit_callback)
		refresh_progressive_links();
		refresh_non_progressive_links();
	}
	else { 
		//initialize_hot(); 
	}
	
	generate_edit_modal();
	

});
</script>


