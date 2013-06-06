<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employees extends CI_Controller {


    public function __construct() {
        $this->load->model('employee_model');
    }

	public function index()
	{
        $error = false;
        $json_out = array("result"=>array(),"code"=>200,"status"=>"OK");

        $json_out['result']['items'] = $this->employee_model->list();

        $this->output->set_header('Content-type: application/json');
        $this->output->set_output(json_encode($json_out));

    }

    public function save()
    {
        $error = false;
        $json_out = array("result"=>array(),"code"=>200,"status"=>"OK");

        if ($this->input->post('items'))
        {
            $items = json_decode($this->input->post('items'));
            $data = array('name'		=> $items->name,
                'hire_date'	 		    => (isset($items->hire_date) ? $items->hire_date : date('m/d/Y')),
                'location'	 	        => $items->location,
                'consultant'            => (isset($items->consultant)) ? $items->consultant : 'N',
                'employee_id' 		    => $items->employee_id,
                'type' 		             => $items->type,
            );
            $error = !$this->employee_model->save($data);
            $json_out['result']['success'] = $error;
        }
        else
        {
         $error = true;
            $status = "Post Data was missing.";
        }
        if ($error)
        {
            $json_out['code'] = 301;
            $json_out['status'] = "error:".$status;
            $json_out['result'] = 'An error occurred.';
        }
        $this->output->set_header('Content-type: application/json');
        $this->output->set_output(json_encode($json_out));

    }

    public function delete()
    {
        $error = false;
        $json_out = array("result"=>array(),"code"=>200,"status"=>"OK");

        if ($this->input->post('employee_id'))
        {
            $employee_id = json_decode($this->input->post('employee_id'));
            $error = !$this->employee_model->delete($employee_id);
            $json_out['result']['success'] = $error;
        }
        else
        {
            $error = true;
            $status = "Post Data was missing.";
        }
        if ($error)
        {
            $json_out['code'] = 301;
            $json_out['status'] = "error:".$status;
            $json_out['result'] = 'An error occurred.';
        }
        $this->output->set_header('Content-type: application/json');
        $this->output->set_output(json_encode($json_out));


    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */