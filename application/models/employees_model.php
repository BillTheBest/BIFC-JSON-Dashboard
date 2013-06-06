<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees_Model extends CI_Model {

    protected $table = "employees";


    public function save($data = false) {
        if ($data === false) return;

        if (!isset($data->type) || (isset($data->type) && isset($data->type) == "add")) {
            // Add
            return $this->db->insert($this->table,$data);
        } else {
            // EDIT
            $this->db->where('employee_id',$data->employee_id);
            return $this->db->update($this->table,$data);
        }
    }

    public function delete($employee_id = false) {
        if ($employee_id === false) return;

        $this->db->where('employee_id',$employee_id);
        return $this->db->delete($this->table);

    }
}
