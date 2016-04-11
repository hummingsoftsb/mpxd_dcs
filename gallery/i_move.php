				<?php
				/**
				 * @agaile
				 * date:23/02/16
				 * Parameter:host, user, password, db, port
				 * Return type:boolean
				 * Description:for db access
				 */
				 
				$dcs_root = 'mpxd_dcs';
				//database access information
				$host = "192.168.1.52";
				$user = "postgres";
				$pass = "mrt@mpxd!@#123";
				$db = "pilot_db_new";
				$port = "5432";

				//open a connection to the database server
				$connection = pg_connect("host=$host dbname=$db user=$user password=$pass port=$port");
				if (!$connection) {
					die("Could not open connection to database server");
				} else {
				//    echo("Connection Succeeded");
				}

				/**
				 * @agaile
				 * date:23/02/16
				 * Parameter:none
				 * Return type:array
				 * Description:for fetching the details of images
				 */

				// get the filepath of images which are not moved to local repository

				
				$query ="select  a.journal_no,b.data_entry_no,c.data_entry_pict_no,c.pict_file_name,c.pict_file_path||c.pict_file_name as fullpath ,c.pict_definition,
				e.frequency_period as wk,e.frequency_year as yr,'Week'||e.frequency_period||'_'||e.frequency_year as wks, e.start_date, e.end_date,
				f.slug, d.validate_level_no,d.data_validate_no,d.validate_status
				from journal_master a join journal_data_entry_master b on a.journal_no = b.journal_no join journal_data_entry_picture c on b.data_entry_no = c.data_entry_no
				join journal_data_validate_master d on c.data_entry_no = d.data_entry_no join frequency_detail e on b.frequency_detail_no = e.frequency_detail_no 
				join tbl_slug_dictionary f on a.journal_name = f.slug_description where  d.validate_status = 2 and c.is_moved = 0 ";
				$result = pg_query($query);
				$val = pg_fetch_all($result);

				$query2 = "SELECT data_entry_no, max(validate_level_no) FROM journal_data_validate_master where validate_status=2 group by data_entry_no";
				$result2 = pg_query($query2);
				$val2 = pg_fetch_all($result2);



				echo '<pre>';
				print_r($val);
				echo '</pre>';

				echo '<pre>';
				print_r($val2);
				echo '</pre>';
				
				// remove duplicate entry logic

				$temp = array();
				$final = array();
				if(!empty ($val2) && !empty ($val))
				{
					foreach($val2 as $rslt2)
					{
						$data_entry_no = $rslt2['data_entry_no'];
						$level_no = $rslt2['max'];

						foreach($val as $rslt){
							if($data_entry_no == $rslt['data_entry_no'] && $level_no == $rslt['validate_level_no'] )
							{
								$temp['journal_no'] = $rslt['journal_no'];
								$temp['data_entry_no'] =  $rslt['data_entry_no'];
								$temp['data_entry_pict_no'] =  $rslt['data_entry_pict_no'];
								$temp['pict_file_name'] = $rslt['pict_file_name'];
								$temp['fullpath'] =  $rslt['fullpath'];
								$temp['pict_definition'] =  $rslt['pict_definition'];
								$temp['wk'] = $rslt['wk'];
								$temp['yr'] =  $rslt['yr'];
								$temp['wks'] =  $rslt['wks'];
								$temp['start_date'] = $rslt['start_date'];
								$temp['end_date'] =  $rslt['end_date'];
								$temp['slug'] =  $rslt['slug'];
								$temp['validate_level_no'] = $rslt['validate_level_no'];
								$temp['data_validate_no'] =  $rslt['data_validate_no'];
								$temp['validate_status'] =  $rslt['validate_status'];
								$final[] = $temp;
							}
						}
					}


				echo '<pre>';
				print_r($final);
				echo '</pre>';


				//Image moving logic : Start

					foreach ($final as $rslt3) {
					$chk = 1;  // checker variable 0 - failure , 1 - success
					$fcount = 0;
					$scount = 0;

					//$frm = $_SERVER['DOCUMENT_ROOT'].'/'.$dcs_root.'/'.$rslt3['fullpath'];
					$frm = $_SERVER['DOCUMENT_ROOT'].'/'.$rslt3['fullpath'];
					$id = $rslt3['data_entry_pict_no'];
					$alb_name = $rslt3['slug'].' Project '.$rslt3['start_date'].' to '.$rslt3['end_date'];

					// spliting the week and customising otherwise '/' will treat as new folder creation

					/*
					$wk = $rslt['week'];
					$wks = explode("/", $wk);
					$fwk = $wks[0].'_'.$wks[1];
					*/


				    // check whether there is a folder in the document root if not create
					if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.'gallery')) 
					{
						mkdir($_SERVER['DOCUMENT_ROOT'].'/'.'gallery', 0777, true);
					}
					// check whether there is a folder inside gallery for slug if not create
					if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.'gallery'.'/'.$rslt3['slug'])) 
					{
						mkdir($_SERVER['DOCUMENT_ROOT'].'/'.'gallery'.'/'.$rslt3['slug'], 0777, true);
					}

					// check whether there is a folder inside slug for week if not create
					if (!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.'gallery'.'/'.$rslt3['slug'].'/'.$rslt3['wks'])) 
					{
						mkdir($_SERVER['DOCUMENT_ROOT'].'/'.'gallery'.'/'.$rslt3['slug'].'/'.$rslt3['wks'], 0777, true);
					}

					$r_path = 'gallery'.'/'.$rslt3['slug'].'/'.$rslt3['wks'].'/'.$rslt3['pict_file_name'];
					$to = $_SERVER['DOCUMENT_ROOT'].'/'.'gallery'.'/'.$rslt3['slug'].'/'.$rslt3['wks'].'/'.$rslt3['pict_file_name'];

					// copy image to folder

					if(!copy($frm,$to))
					{
						$chk = 0;
						$fcount++;
					}
					if($chk == 1){

				    // update table journal_data_entry_picture column is_moved to 1 

						$query_update ="UPDATE journal_data_entry_picture set is_moved=1 WHERE data_entry_pict_no = $id";
						$rs = pg_query($query_update);
						if(!$rs)
						{
							$fcount++;
						}
						else{
					 		// insert into table gallery

							$query_ig = "insert into gallery (slug,year,week,album_name,image_name,image_description,end_date,uploaded_path) VALUES ('$rslt3[slug]','$rslt3[yr]','$rslt3[wk]','$alb_name','$rslt3[pict_file_name]','$rslt3[pict_definition]','$rslt3[end_date]','$r_path')";
							$result_ig = pg_query($query_ig);
							if(!$result_ig)
							{
								$fcount++;
							}
							else{

								$scount++;

							}
						}
					}

				}

				//Image moving logic : End
				
				// show success or failure message
				echo '<script type="text/javascript">alert("Successfully moved ' . $scount . ' Image(s) & Failed to move ' . $fcount . ' Image(s)");</script>';
			}
			else{
					// show success or failure message
				echo '<script type="text/javascript">alert("No image(s) available to move");</script>';
			}
			?>