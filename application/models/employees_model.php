<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees_Model extends CI_Model {

    protected $table = "employees";
    protected $table_notes = "notes";
    protected $table_reviews = "reviews";
    protected $error = "";

	public function find_all($limit = false, $index = false) {
		
		$emp_list = array();
		$this->db->select()
				 ->from($this->table)
				 ->order_by('name','asc');
		if ($limit !== false && $index === false) { 
			$this->db->limit($limit); 
		} else if ($limit !== false && $index !== false) { 
			$this->db->limit($index, $limit); 
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$emp = array('employee_id'=>$row->employee_id,'name'=>$row->name,
											'hire_date'=>$row->hire_date,'location'=>$row->location,'consultant'=>($row->consultant == 'Y' ? true : false));
				// Sub queries
				$notes = array();
				$this->db->select('date,content')
					->from($this->table_notes)
					->where('employee_id',$row->employee_id);
				$n_query = $this->db->get();
				if ($n_query->num_rows() > 0) {
					foreach ($n_query->result() as $n_row) {
						array_push($notes, array('date'=>date('m/d/Y',strtotime($n_row->date)),'content'=>$n_row->content));
					}
				}
				$n_query->free_result();
				if (sizeof($notes) > 0) $emp = $emp + array('notes'=>$notes);
				
				$reviews = array();
				$this->db->select('date, reviewed_by, status')
					->from($this->table_reviews)
					->where('employee_id',$row->employee_id);
				$r_query = $this->db->get();
				if ($r_query->num_rows() > 0) {
					foreach ($r_query->result() as $r_row) {
						array_push($reviews, array('date'=>date('m/d/Y',strtotime($r_row->date)),'reviewed_by'=>$r_row->reviewed_by,'status'=>$r_row->status));
					}
				}
				$r_query->free_result();
				if (sizeof($reviews) > 0) $emp = $emp + array('reviews'=>$reviews);

				array_push($emp_list, $emp);
			}
		}
		$query->free_result();
		return $emp_list;
	}
	
    public function save($type = "add", $data = false, $employee_id = false) {
        if ($data === false) {
			$error = "No employee data was received.";
			return false;
		}
        if ($type == "add") {
            // Add
            return $this->db->insert($this->table,$data);
        } else {
            if ($employee_id === false) {
				$error = "No employee id was received.";
				return false;
			}
			// EDIT
            $this->db->where('employee_id',$employee_id);
            return $this->db->update($this->table,$data);
        }
    }

    public function delete($employee_id = false) {
        if ($employee_id === false) {
			$error = "No employee id was received.";
			return false;
		}
        $this->db->where('employee_id',$employee_id);
        return $this->db->delete($this->table);
    }
}
