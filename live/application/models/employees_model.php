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
				$emp = array('employee_id'=>intval($row->employee_id),'name'=>$row->name,
											'hire_date'=>$row->hire_date,'location'=>intval($row->location),'consultant'=>($row->consultant == 'Y' ? true : false));
				$emp_id = intval($row->employee_id);
				// Sub queries
                $notes = $this->find_all_notes($emp_id);
				if (sizeof($notes) > 0) $emp = $emp + array('notes'=>$notes);
				
				$reviews = array();
				$this->db->select('date, reviewed_by, status')
					->from($this->table_reviews)
					->where('employee_id',$emp_id);
				$r_query = $this->db->get();
                //echo($this->db->last_query()."<br />");
                //echo("Query object result rows = ".$r_query->num_rows()."<br />");
				if ($r_query->num_rows() > 0) {
					foreach ($r_query->result() as $r_row) {
						array_push($reviews, array('date'=>date('m/d/Y',strtotime($r_row->date)),'reviewed_by'=>$r_row->reviewed_by,'status'=>$r_row->status));
					}
				}
               // echo("Size of reviews = ".sizeof($reviews)."<br />");
				if (sizeof($reviews) > 0) $emp = $emp + array('reviews'=>$reviews);
                $r_query->free_result();

                array_push($emp_list, $emp);
			}
		}
		$query->free_result();
		return $emp_list;
	}
	
    public function save($type = "add", $data = false, $employee_id = false) {
        if ($data === false) {
            $this->error = "No employee data was received.";
			return false;
		}
        if ($type == "add") {
            // Add
            return $this->db->insert($this->table,$data);
        } else {
            if ($employee_id === false) {
                $this->error = "No employee id was received.";
				return false;
			}
			// EDIT
            $this->db->where('employee_id',$employee_id);
            return $this->db->update($this->table,$data);
        }
    }

    public function delete($employee_id = false) {
        if ($employee_id === false) {
			$this->error = "No employee id was received.";
			return false;
		}
        $this->db->where('employee_id',$employee_id);
        return $this->db->delete($this->table);
    }

    public function add_note($data = false) {
        if ($data === false) {
            $error = "No employee data was received.";
            return false;
        }
        $this->db->insert($this->table_notes,$data);
        $id = $this->db->insert_id();
        if (!isset($id)) {
            $this->error = "An error occurred inserting the record.";
            return false;
        } else {
            return $this->find_all_notes($data['employee_id']);
        }
    }
    public function find_all_notes($employee_id = false) {
        if ($employee_id === false) {
            $error = "No employee data was received.";
            return false;
        }
        $notes = array();
        $this->db->select('date,content')
            ->from($this->table_notes)
            ->where('employee_id',$employee_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                array_push($notes, array('date'=>date('m/d/Y',strtotime($row->date)),'content'=>$row->content));
            }
        }
        $query->free_result();
        return $notes;
    }
}
