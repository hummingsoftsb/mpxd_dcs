<script>
	$(document).ready(function()
	{
		$('#addRecord').submit(function()
		{
			$.post($('#addRecord').attr('action'), $('#addRecord').serialize(), function( data )
			{
				if(data.st == 0)
				{
		 			$('#errordata').html(data.msg);
		 			$('#errordata1').html(data.msg1);
				}
				else if(data.st == 1)
				{
					location.href="<?php echo base_url(); ?>journalvalidation";
				}

			}, 'json');
			return false;
   		});
	});
</script>

<?php 

	foreach($details as $row):
		$week=$row->frequency_period;
		$pname=$row->project_name;
		$jname=$row->journal_name;
		$level=$row->validate_level_no ;
		$publishdate=$row->publish_date;
		$publishname=$row->publishname;
		$dataentryno=$row->data_entry_no;
	endforeach;
?>
<div class="container">
	<div class="page-header">
		<h1 id="nav">Project Journal Data Validation</h1>
	</div>
	<!-- BUAT CODING DALAM WRAP-->
	<!-- INPUT HERE-->
	<div class="row" style="width: 70%; margin: auto;">
		<div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Data Entry For</b></div>
		<div class="col-xs-8" style="color: blue; margin-bottom: 8px;">Week <?php echo $week; ?></div>
		<div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Project Name</b></div>
		<div class="col-xs-8" style="color: blue; margin-bottom: 8px;"><?php echo $pname; ?></div>
		</br>
		<div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Journal Name</b></div>
		<div class="col-xs-8" style="color: blue; margin-bottom: 8px;"><?php echo $jname; ?></div>
		</br>
		<div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Level</b></div>
		<div class="col-xs-8" style="color: blue; margin-bottom: 8px;">Level <?php echo $level; ?></div>
		</br>
		<div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Publish By</b></div>
		<div class="col-xs-8" style="color: blue; margin-bottom: 8px;"><?php echo $publishname; ?></div>
		</br>
		<div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Publish On</b></div>
		<div class="col-xs-8" style="color: blue; margin-bottom: 8px;"><?php echo date("d-m-Y",strtotime($publishdate)); ?></div>
		</br>
		<?php
			if($validatorcount!=0)
			{
		?>
				<div class="col-xs-4" style="text-align: right; margin-bottom: 8px;"><b>Other Validation By</b></div>
				<div class="col-xs-8" style="margin-bottom: 8px;">
					<table class="table table-striped table-hover ">
						<thead>
							<tr>
								<th>Name</th>
								<th>level</th>
								<th>Publish Date</th>
							</tr>
						</thead>
						<thead>
							<?php
									foreach($validators as $validator):
										echo '<tr>';
										echo '<td>'.$validator->user_full_name.'</td>';
										echo '<td>Level '.$validator->validate_level_no.'</td>';
										echo '<td>Level '.date("d-m-Y",strtotime($validator->accept_date)).'</td>';
										echo '</tr>';
									endforeach;
								?>
						</tbody>
					</table>
				</div>
		<?php
			}
		?>
	</div>
	<form id="addRecord" method="POST" action="<?php echo base_url(); ?><?php echo $cpagename; ?>/add/">
		<!-- ---------------------- -->
		<fieldset>
			<legend>Data Attributes for Journal</legend>
				<table class="table table-striped table-hover ">
					<thead>
						<tr>
							<th>No</th>
							<th>Data Attributes</th>
							<th>Previous Value</th>
							<!--<th>Start</th>
							<th>End</th>
							<th>Weekly Max</th>-->
							<th>New Value</th>
							<!--<th>Variance</th>-->
							
							<th>UOM</th>
							<th>Comments<input type="hidden" id="validateid" name="validateid" value="<?php echo $validatorid; ?>" /><input type="hidden" id="validateid" name="validateid" value="<?php echo $validatorid; ?>" /><input type="hidden" id="dataentryid" name="dataentryid" value="<?php echo $dataentryno; ?>" /></th>
						</tr>
					</thead>
					<tbody>
						
						<?php
							$sno=1;
							foreach($dataentryattbs as $dataentryattb):
								echo "<tr>";
								echo '<td>'.$sno.'</td>';
								echo '<td>'.$dataentryattb->data_attb_label.'</td>';
								echo '<td>'.$dataentryattb->prev_actual_value.'</td>';
								//echo '<td>'.intval($dataentryattb->start_value).'</td>';
								//echo '<td>'.intval($dataentryattb->end_value).'</td>';
								//echo '<td>'.intval($dataentryattb->frequency_max_value).'</td>';
								echo '<td><input type="hidden" id="dataattbid'.$sno.'" name="dataattbid'.$sno.'" value="'.$dataentryattb->data_attb_id.'" />';
								echo $dataentryattb->actual_value;
								echo '</td>';
								//echo '<td>'.intval($dataentryattb->frequency_max_opt).'</td>';
								
								echo '<td>'.$dataentryattb->uom_name.'</td>';
								echo '<td><input type="text" id="comment'.$sno.'" name="comment'.$sno.'" value="" /></td>';
								echo "</tr>";
								$sno++;
							endforeach;
						?>
						<input type="hidden" id="dataattbcount" name="dataattbcount" value="<?php echo $sno-1; ?>" />
					</tbody>
				</table>
		</fieldset>
		</br>
		<?php 
			if(count($dataimages)!=0) 
			{
		 ?>
			<fieldset>
				<legend>Picture Attachment</legend>
					<table class="table table-striped table-hover" style="margin-top: 30px;">
					<table class="table table-striped table-hover ">
						<thead>
							<tr>
								<th>No</th>
								<th>Picture</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($dataimages as $dataimage):
									echo '<tr>';
									echo '<td>'.$dataimage->pict_seq_no.'</td>';
									echo '<td> <img src="'.$dataimage->pict_file_path.$dataimage->pict_file_name.'" class="img-responsive" alt="" style="width: 200px; height: 137px;"> </td>';
									echo '<td> '.$dataimage->pict_definition.' </td>';
									echo '</tr>';
								endforeach;
							?>
						</tbody>
					</table>
			</fieldset>
			</br>
		<?php 
			}
		 ?>
		<div class="row text-center text-danger" id="errordata"></div>
		<div class="row text-center text-danger" id="errordata1"></div>
		<fieldset>
			<legend>Validation</legend>
				<div class="row" style="width: 70%; margin: auto;">
				  <div class="col-xs-3" style="margin-bottom: 8px;">
					<div class="radio">
						<label>
							<input type="radio" id="optradio" name="optradio" value="Approve">Approve
						</label>
					</div>
				  </div>
				  <div class="col-xs-4" style="margin-bottom: 8px;">
					<div class="radio">
						<label>
							<input type="radio" id="optradio" name="optradio" value="Reject">Reject to
						</label>
					</div>
				  </div>
				  <div class="col-xs-5" style="font-size: 14px; color: blue; margin-bottom: 8px;">
					<select class="form-control">
						<option><?php echo $publishname; ?></option>
					</select>
				  </div>
				</div>
				<div class="row" style="width: 70%; margin: auto;">
				  <div class="col-xs-3" style="margin-bottom: 8px;">
				  </div>
				  <div class="col-xs-4" style="margin-bottom: 8px;">Reject notes</div>
				  <div class="col-xs-5" style="color: blue; margin-bottom: 40px;">
					<textarea class="form-control" rows="5" id="comment" name="comment"></textarea>
				  </div>
				</div>
		</fieldset>

		<div class="form-group" style="text-align: center;">
			<input type="submit" class="btn btn-primary btn-sm" value="Save" />
			<a href="/journalvalidation" class="btn btn-danger btn-sm">Cancel</a>
		</div>
</form>
</div>

