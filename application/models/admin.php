<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Admin extends CI_Model
{
	// Function to fetch total number of Data Attribute Group records
	function totaldataattrgrp($data)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);
		$sql = "SELECT * FROM data_attribute_group";
		if($data!="")
		{
			$sql .=" where lower(data_attribute_group_desc) like '%".$data."%' ";

		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	// Function To Fetch All Data Attribute Group Record
	function show_dataattrgrps()
	{
		$query ="SELECT * FROM  data_attribute_group";		
        $q = $this->db->query($query);
        return $q->result();
	}

	// Function To Fetch All Data Attribute Group Record
	function show_dataattrgrp($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT * FROM  data_attribute_group";
		if($data!="")
		{
			$query .=" where lower(data_attribute_group_desc) like '%".$data."%' ";

		}
		$query .=" Order By data_attribute_group_desc asc OFFSET ".$offset."LIMIT ".$perPage;
        $q = $this->db->query($query);
        return $q->result();
	}

	// Add Check Query For Selected Data attribute Group
	function add_check_dataattrgrp($data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select data_attribute_group_id from data_attribute_group where data_attribute_group_desc='$data'");
		return $query->num_rows();
	}

	//Function to add new Data attribute Group record
	function add_dataattrgrp($data)
	{
		// Inserting in Table data_attribute_group
		$this->db->insert('data_attribute_group', $data);
		return true;
	}

	// Update Check Query For Selected Data attribute Group
	function update_check_dataattrgrp($id,$data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select data_attribute_group_id from data_attribute_group where data_attribute_group_id!=$id and data_attribute_group_desc='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected Data attribute Group
	function update_dataattrgrp($id,$data)
	{
		$this->db->where('data_attribute_group_id', $id);
		$this->db->update('data_attribute_group', $data);
	}

	// Delete Check Query For Selected Data Attribute Group
	function delete_check_dataattrgrp($id)
	{

		$query=$this->db->query("select data_attribute_group_id from data_attribute where data_attribute_group_id=$id");
		return $query->num_rows();
	}

	// Delete the selected record
	function delete_dataattrgrp($id)
	{
		$this->db->where('data_attribute_group_id', $id);
		$this->db->delete('data_attribute_group');
	}

	// Function to fetch all Data Type
	function show_datatype()
	{
		$query = $this->db->get('data_attribute_data_type');
		$query_result = $query->result();
		return $query_result;
	}

	// Function to fetch all Data Type
	function show_attbgroup()
	{
		$query = $this->db->get('data_attribute_group');
		$query_result = $query->result();
		return $query_result;
	}

	// Function to fetch all Data Type
	function show_inputtype()
	{
		$query = $this->db->get('data_attribute_type');
		$query_result = $query->result();
		return $query_result;
	}

	// Function to fetch total number of records
	function totaldataatt($data)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);
		$sql = "SELECT da.*,(select data_attb_data_type_desc from data_attribute_data_type dadt where da.data_attb_data_type_id=dadt.data_attb_data_type_id) as data_attb_data_type_desc,(select lk_code from lookup_data ld where da.data_set_id=ld.data_set_id) as lk_code,(select data_attribute_group_desc from data_attribute_group dag where da.data_attribute_group_id=dag.data_attribute_group_id) as data_attribute_group_desc,(select data_attb_type_desc from data_attribute_type where data_attribute_type.data_attb_type_id=da.data_attb_type_id) as data_attb_type_desc,(select uom_name from unit_measure where unit_measure.uom_id=da.uom_id ) as uom_name FROM data_attribute da where 1=1";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(data_attb_label) like '%".$data."%' ";
			//$sql .=" or lower(data_attb_data_type_desc) like '%".$data."%' ";
			$sql .=" or data_attb_type_id in (select data_attb_type_id from data_attribute_type where lower(data_attb_type_desc) like '%".$data."%') ";
			//$sql .=" or lower(lk_code) like '%".$data."%' ";
			$sql .=" or uom_id in (select uom_id from unit_measure where lower(uom_name) like '%".$data."%') ";
			$sql .=" or data_attribute_group_id in (select data_attribute_group_id from data_attribute_group where lower(data_attribute_group_desc) like '%".$data."%') ";
			$sql .=" ) ";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	//Function to Fetch All Data Attribute Record
	function show_dataatts()
	{
		$sql="SELECT da.data_attb_id,da.data_attb_label,da.data_attribute_group_id,(select data_attb_type_desc from data_attribute_type where data_attribute_type.data_attb_type_id=da.data_attb_type_id) as data_attb_type_desc,(select uom_name from unit_measure where unit_measure.uom_id=da.uom_id ) as uom_name FROM data_attribute da order by da.data_attb_label ";
		$q = $this->db->query($sql);
        return $q->result();
	}

	// Function To Fetch All Data Attribute Record
	function show_dataatt($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$sql = "SELECT da.*,(select data_attb_data_type_desc from data_attribute_data_type dadt where da.data_attb_data_type_id=dadt.data_attb_data_type_id) as data_attb_data_type_desc,(select lk_code from lookup_data ld where da.data_set_id=ld.data_set_id) as lk_code,(select data_attribute_group_desc from data_attribute_group dag where da.data_attribute_group_id=dag.data_attribute_group_id) as data_attribute_group_desc,(select data_attb_type_desc from data_attribute_type where data_attribute_type.data_attb_type_id=da.data_attb_type_id) as data_attb_type_desc,(select uom_name from unit_measure where unit_measure.uom_id=da.uom_id ) as uom_name FROM data_attribute da where 1=1 ";
		if($data!="")
		{
			$data = str_replace(array('%'), array('\%'), $data);
			$sql .=" and (";
			$sql .=" lower(data_attb_label) like '%".$data."%' ";
			//$sql .=" or lower(data_attb_data_type_desc) like '%".$data."%' ";
			$sql .=" or data_attb_type_id in (select data_attb_type_id from data_attribute_type where lower(data_attb_type_desc) like '%".$data."%') ";
			//$sql .=" or lower(lk_code) like '%".$data."%' ";
			$sql .=" or uom_id in (select uom_id from unit_measure where lower(uom_name) like '%".$data."%') ";
			$sql .=" or data_attribute_group_id in (select data_attribute_group_id from data_attribute_group where lower(data_attribute_group_desc) like '%".$data."%') ";
			$sql .=" ) ";
		}
		$sql .=" Order By data_attb_label asc OFFSET ".$offset."LIMIT ".$perPage;
        $q = $this->db->query($sql);
        return $q->result();
	}

	// Add Check Query For Selected Data Attribute
	function add_check_dataatt($data,$inputtype,$datatype,$uom)
	{	
		$data=str_replace("'","''",$data);
		if($datatype && $uom){
			$query=$this->db->query("select data_attb_label from data_attribute where data_attb_label='$data' AND data_attb_type_id=$inputtype AND data_attb_data_type_id=$datatype AND uom_id=$uom");
			}
		elseif($datatype){
			$query=$this->db->query("select data_attb_label from data_attribute where data_attb_label='$data' AND data_attb_type_id=$inputtype AND data_attb_data_type_id=$datatype");
			}
		else {
			$query=$this->db->query("select data_attb_label from data_attribute where data_attb_label='$data' AND data_attb_type_id=$inputtype");
			}
		return $query->num_rows();
	}

	//Function to add new record
	function add_dataatt($data)
	{	
		// Inserting in Table Data Attribute
		$this->db->insert('data_attribute', $data);
		return true;
	}

	// Update Check Query For Selected Data Attribute
	function update_check_dataatt($id,$data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select data_attb_label from data_attribute where data_attb_id!=$id and data_attb_label='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected Data Attribute
	function update_dataatt($id,$data)
	{
		$this->db->where('data_attb_id', $id);
		$this->db->update('data_attribute', $data);
	}

	// Delete Check Query For Selected Data Attribute
	function delete_check_dataatt($id)
	{//journal_data_entry_audit_log journal_data_entry_detail journal_data_validate_detail journal_detail
		$query=$this->db->query("select data_attb_id from journal_detail where data_attb_id=$id");
		if($query->num_rows()==0)
		{
			$query1=$this->db->query("select data_attb_id from journal_data_entry_detail where data_attb_id=$id");
			if($query1->num_rows()==0)
			{
				$query2=$this->db->query("select data_attb_id from journal_data_validate_detail where data_attb_id=$id");
				if($query2->num_rows()==0)
				{
					$query3=$this->db->query("select data_attb_id from journal_data_entry_audit_log where data_attb_id=$id");
					return $query3->num_rows();
				}
				else
				{
					return $query2->num_rows();
				}
			}
			else
			{
				return $query1->num_rows();
			}
		}
		else
		{
			return $query->num_rows();
		}
	}

	// Delete the Selected Data Attribute record
	function delete_dataatt($id)
	{
		$this->db->where('data_attb_id', $id);
		$this->db->delete('data_attribute');
	}

	// Function to fetch all Data Type
	function show_inputtype_nonp()
	{
		$query = $this->db->get('data_attribute_type_nonprogressive');
		$query_result = $query->result();
		return $query_result;
	}

	// Function to fetch total number of records
	function totaldataattnop($data)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);
		$sql = "SELECT da.*,(select data_attb_type_desc from data_attribute_type_nonprogressive where data_attribute_type_nonprogressive.data_attb_type_id=da.data_attb_type_id) as data_attb_type_desc FROM data_attribute_nonprogressive da where 1=1";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(data_attb_label) like '%".$data."%' ";
			$sql .=" or data_attb_type_id in (select data_attb_type_id from data_attribute_type_nonprogressive where lower(data_attb_type_desc) like '%".$data."%') ";
			$sql .=" ) ";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	//Function to Fetch All Data Attribute Record

	function show_dataattsnonp()
	{
		$sql="SELECT da.data_attb_id,da.data_attb_label,dat.data_attb_type_desc FROM data_attribute_nonprogressive da,data_attribute_type_nonprogressive dat where da.data_attb_type_id=dat.data_attb_type_id ";
		$q = $this->db->query($sql);
        return $q->result();
	}

	// Function To Fetch All Data Attribute Record
	function show_dataattnop($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$sql = "SELECT da.*,(select data_attb_type_desc from data_attribute_type_nonprogressive where data_attribute_type_nonprogressive.data_attb_type_id=da.data_attb_type_id) as data_attb_type_desc FROM data_attribute_nonprogressive da where 1=1";
		if($data!="")
		{
			$sql .=" and (";
			$sql .=" lower(data_attb_label) like '%".$data."%' ";
			$sql .=" or data_attb_type_id in (select data_attb_type_id from data_attribute_type_nonprogressive where lower(data_attb_type_desc) like '%".$data."%') ";
			$sql .=" ) ";
		}
		$sql .=" Order By data_attb_label asc OFFSET ".$offset."LIMIT ".$perPage;
        $q = $this->db->query($sql);
        return $q->result();
	}

	// Add Check Query For Selected Data Attribute
	function add_check_dataattnop($data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select data_attb_label from data_attribute_nonprogressive where data_attb_label='$data'");
		return $query->num_rows();
	}

	//Function to add new record
	function add_dataattnop($data)
	{
		// Inserting in Table Data Attribute
		$this->db->insert('data_attribute_nonprogressive', $data);
		return true;
	}

	// Update Check Query For Selected Data Attribute
	function update_check_dataattnop($id,$data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select data_attb_label from data_attribute_nonprogressive where data_attb_id!=$id and data_attb_label='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected Data Attribute
	function update_dataattnop($id,$data)
	{
		$this->db->where('data_attb_id', $id);
		$this->db->update('data_attribute_nonprogressive', $data);
	}

	// Delete Check Query For Selected Data Attribute
	function delete_check_dataattnop($id)
	{
		//journal_data_entry_audit_log journal_data_entry_detail journal_data_validate_detail journal_detail
		return '0';
	}

	// Delete the Selected Data Attribute record
	function delete_dataattnop($id)
	{
		$this->db->where('data_attb_id', $id);
		$this->db->delete('data_attribute_nonprogressive');
	}

	// Function to fetch total number of records
	function totaluom($data)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);
		$sql = "SELECT * FROM unit_measure";
		if($data!="")
		{
			$sql .=" where lower(uom_name) like '%".$data."%' ";
			$sql .=" or lower(uom_desc) like '%".$data."%' ";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	// Function To Fetch All UOM Record
	function show_uom($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query ="SELECT * FROM  unit_measure";
		if($data!="")
		{
			$query .=" where lower(uom_name) like '%".$data."%' ";
			$query .=" or lower(uom_desc) like '%".$data."%' ";
		}
		$query .=" Order By uom_name asc OFFSET ".$offset."LIMIT ".$perPage;
        $q = $this->db->query($query);
        return $q->result();
	}

	// Function to fetch all UOM
	function show_uoms()
	{
		$query = $this->db->get('unit_measure');
		$query_result = $query->result();
		return $query_result;
	}

	// Function To Fetch Selected Uom Record
	function show_uom_id($data)
	{
		$this->db->select('*');
		$this->db->from('unit_measure');
		$this->db->where('uom_id', $data);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	// Add Check Query For Selected UOM
	function add_check_uom($data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select uom_id from unit_measure where uom_name='$data'");
		return $query->num_rows();
	}

	//Function to add new record
	function add_uom($data)
	{
		// Inserting in Table unit_measure
		$this->db->insert('unit_measure', $data);
		return true;
	}

	// Update Check Query For Selected UOM
	function update_check_uom($id,$data)
	{
		$data=str_replace("'","''",$data);
		$query=$this->db->query("select uom_id from unit_measure where uom_id!=$id and uom_name='$data'");
		return $query->num_rows();
	}

	// Update Query For Selected Uom
	function update_uom($id,$data)
	{
		$this->db->where('uom_id', $id);
		$this->db->update('unit_measure', $data);
	}

	// Delete Check Query For Selected UOM
	function delete_check_uom($id)
	{

		$query=$this->db->query("select uom_id from data_attribute where uom_id=$id");
		return $query->num_rows();
	}

	// Delete the selected record
	function delete_uom($id)
	{
		$this->db->where('uom_id', $id);
		$this->db->delete('unit_measure');
	}

	// Function to fetch total number of records
	function totallookupdata($data)
    {
        $data=strtolower($data);
        $data=str_replace("'","''",$data);
		$sql = "SELECT * FROM lookup_data,lookup_data_detail where lookup_data.data_set_id=lookup_data_detail.data_set_id ";
		if($data!="")
		{
			$sql .=" and ( lower(lk_code) like '%".$data."%' ";
			$sql .=" or lower(lk_data) like '%".$data."%' ";
			if (is_numeric($data))
			{
				$sql .=" or lk_value='".$data."'";
			}
			$sql .=" )";
		}
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	// Function To Fetch All Lookup Data Record
	function show_lookupdata($data,$offset,$perPage)
	{
		$data=strtolower($data);
		$data=str_replace("'","''",$data);
		$query = "SELECT lookup_data.data_set_id,lk_code,data_set_detail_id,lk_data,lk_value FROM lookup_data,lookup_data_detail where lookup_data.data_set_id=lookup_data_detail.data_set_id ";
		if($data!="")
		{
			$data = str_replace(array('%'), array('\%'), $data);
			$query .=" and ( lower(lk_code) like '%".$data."%' ";
			$query .=" or lower(lk_data) like '%".$data."%' ";
			if (is_numeric($data))
			{
				$query .=" or lk_value='".$data."'";
			}
			$query .=" )";
		}
		$query .=" Order By lookup_data.lk_code asc,lookup_data_detail.lk_data asc OFFSET ".$offset."LIMIT ".$perPage;
        $q = $this->db->query($query);
        return $q->result();
	}

	// Function to fetch all Lookup Data
	function show_lookupdatas()
	{
		$query = $this->db->get('lookup_data');
		$query_result = $query->result();
		return $query_result;
	}

	// Add Check Query For Selected Lookup Data
	function add_check_lookupdata($code,$data)
	{
		$data=str_replace("'","''",$data);
		$code=str_replace("'","''",$code);
		$query=$this->db->query("select data_set_id from lookup_data where lk_code='$code'");
		if($query->num_rows()==0)
		{
			return $query->num_rows();
		}
		else
		{
			$rows=$query->result();
			foreach ($rows as $row):
				$dataid=$row->data_set_id;
			endforeach;
			$query1=$this->db->query("select data_set_id from lookup_data_detail where lk_data='$data' and data_set_id=$dataid");
			return $query1->num_rows();
		}
	}

	//Function to add new record
	function add_lookupdata($code,$data,$value)
	{
		$code=str_replace("'","''",$code);
		$query=$this->db->query("select data_set_id from lookup_data where lk_code='".$code."'");
		$norow=$query->num_rows();
		$rows=$query->result();
		if($norow==1)
		{
			foreach ($rows as $row):
			$dataid=$row->data_set_id;
			endforeach;
		}
		else
		{
			$this->db->query("insert into lookup_data (lk_code) values('$code') ");
			$query1=$this->db->query("select data_set_id from lookup_data where lk_code='".$code."'");
			$rows1=$query1->result();
			foreach ($rows1 as $row1):
			$dataid=$row1->data_set_id;
			endforeach;
		}
		$data1 = array('data_set_id' => $dataid ,'lk_data' => $data,'lk_value' => $value);
		$this->db->insert('lookup_data_detail', $data1);
	}

	// Update Check Query For Selected Lookup Data
	function update_check_lookupdata($id,$id1,$code)
	{

		$query=$this->db->query("select data_set_id from data_attribute where data_set_id=$id");
		if($query->num_rows()==0)
		{
			return $query->num_rows();
		}
		else
		{
			$query1=$this->db->query("select lk_code from lookup_data where data_set_id=$id");
			$rows=$query1->result();
			foreach ($rows as $row):
				$codeold=$row->lk_code;
			endforeach;
			if($code==$codeold)
			{
				return 0;
			}
			else
			{
				$query2=$this->db->query("select data_set_id from lookup_data_detail where data_set_id=$id and data_set_detail_id!=$id1");
				if($query2->num_rows()==0)
				{
					return 1;
				}
				else
				{
					return 0;
				}
			}
		}
	}

	// Update Check Query For Selected Lookup Data
	function update_check_lookupdata1($id1,$code,$data)
	{
		$data=str_replace("'","''",$data);
		$code=str_replace("'","''",$code);
		$query=$this->db->query("select data_set_id from lookup_data where lk_code='".$code."'");
		$norow=$query->num_rows();
		if($norow==1)
		{
			$rows=$query->result();
			foreach ($rows as $row):
				$dataid=$row->data_set_id;
			endforeach;
			$query1=$this->db->query("select data_set_id from lookup_data_detail where data_set_id=$dataid and lk_data='$data' and data_set_detail_id!=$id1");
			return $query1->num_rows();
		}
		else
		{
			return 0;
		}
	}

	// Update Query For Selected Lookup Data
	function update_lookupdata($id,$id1,$code,$data,$value)
	{
		$code=str_replace("'","''",$code);
		$query=$this->db->query("select data_set_id from lookup_data where lk_code='".$code."'");
		$norow=$query->num_rows();
		$rows=$query->result();
		if($norow==1)
		{
			foreach ($rows as $row):
			$dataid=$row->data_set_id;
			endforeach;
		}
		else
		{
			$this->db->query("insert into lookup_data (lk_code) values('$code') ");
			$query1=$this->db->query("select data_set_id from lookup_data where lk_code='".$code."'");
			$rows1=$query1->result();
			foreach ($rows1 as $row1):
			$dataid=$row1->data_set_id;
			endforeach;
		}
		$data1 = array('data_set_id' => $dataid ,'lk_data' => $data,'lk_value' => $value);
		$this->db->where('data_set_detail_id', $id1);
		$this->db->update('lookup_data_detail', $data1);

		$query = $this->db->query("select lk_data from lookup_data_detail where data_set_id='".$id."'");
		$rows= $query->num_rows();

		if($rows==0)
		{
			$this->db->where('data_set_id',$id);
			$this->db->delete('lookup_data');
		}
	}

	// Delete Check Query For Selected Lookup Data
	function delete_check_lookupdata($id)
	{
		$query=$this->db->query("select data_set_id from data_attribute where data_set_id=$id");
		if($query->num_rows()==0)
		{
			return $query->num_rows();
		}
		else
		{
			$query1=$this->db->query("select data_set_id from lookup_data_detail where data_set_id=$id");
			if($query1->num_rows()==1)
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
	}

	// Delete the selected record
	function delete_lookupdata($id,$id1)
	{
		$this->db->where('data_set_detail_id', $id1);
		$this->db->delete('lookup_data_detail');


		$query = $this->db->query("select lk_data from lookup_data_detail where data_set_id='".$id."'");
		$rows= $query->num_rows();

		if($rows==0)
		{
			$this->db->where('data_set_id',$id);
			$this->db->delete('lookup_data');
		}
	}
}
?>