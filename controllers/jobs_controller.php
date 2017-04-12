<?php

class Jobs_controller extends Base_controller
{

    public function list_action(){

        if($this->_method !== 'get' || count($this->_parameters['uri']) > 0){
            $status = false;
            $code = 404;
            $data = array('message' => 'The requested action is not available');
            self::make_response($status, $code, $data);
            return;
        }
        $query = 'SELECT `jb_id` AS id, `jb_position` AS position, `jb_description` AS description, `jb_status` as status , `jb_created_on` AS crated_on
                  FROM `jobs`';
        $data = $this->db->get_all($query);
        $status = true;
        $code = 200;
        self::make_response($status, $code, $data);
        return;
    }
    public function unknown_action($param = null)
    {
        $status = false;
        $code = 404;
        $data = array('message' => 'The requested action is not available');

        switch ($this->_method) {
            case 'get': {
                if ((int)$param > 0 && count($this->_parameters['uri']) == 0) {
                    $data = $this->get_job($param);
                    $code = 200;
                }
                break;
            }
            case 'post': {
                if (!isset($param) && count($this->_parameters['uri']) == 0) {
                    $data = $this->create_job();
                    $code = 200;
                }
                break;
            }
            case 'put': {
                if ((int)$param > 0 && count($this->_parameters['uri']) == 0) {
                    $data = $this->update_job($param);
                    $code = 200;
                }
                break;
            }
            case 'delete': {
                if ((int)$param > 0 && count($this->_parameters['uri']) == 0) {
                    $data = $this->delete_job($param);
                    $code = 200;
                }
                break;
            }
        }
        if ($code == 200 && isset($data['id'])) {
            $status = true;
        }
        self::make_response($status, $code, $data);
        return;
    }

    /**
     * return job by its id
     * @param $id - requested job id
     * @return array|mixed
     */
    private function get_job($id)
    {
        $query = 'SELECT `jb_id` AS id, `jb_position` AS position, `jb_description` AS description, `jb_status` as status , `jb_created_on` AS crated_on
                  FROM `jobs`
                  WHERE jb_id = :id';
        $params = array(array(':id', $id, PDO::PARAM_INT));
        $job = $this->db->get_row($query, $params);
        if ($job === false) {
            $job = array('message' => 'Requested job not found');
        }

        return $job;

    }

    /**
     * create new job
     * requested post parameters: 'position', 'description'
     * @return array - returns created job's id when successful
     */
    private function create_job()
    {
        $data = array();
        if (isset($this->_parameters['post']['position']) && isset($this->_parameters['post']['description'])) {
            $pos = $this->_parameters['post']['position'];
            $desc = $this->_parameters['post']['description'];
            if (!is_string($pos) || strlen($pos) < 3) {
                $data['message'] = 'position should be string with at least 3 characters';
                return $data;
            }
            if (!is_string($desc) || strlen($desc) < 3) {
                $data['message'] = 'description should be string with at least 3 characters';
                return $data;
            }
            $query = 'INSERT INTO `jobs` SET `jb_position` = :p, `jb_description` = :d, `jb_created_on` = NOW()';
            $params = array(
                array(':p', $pos, PDO::PARAM_STR),
                array(':d', $desc, PDO::PARAM_STR)
            );
            $job = $this->db->query($query, $params);
            if ($job > 0) {
                $data['id'] = $this->db->last_insert_id();
            } else {
                $data['message'] = 'System error occurred. Try again later.';
            }
        } else {
            $data['message'] = 'position and description are required';
        }
        return $data;
    }

    private function update_job($id)
    {
        $update_vals = '';
        $params = array();
        if (isset($this->_parameters['put']['position'])) {
            $pos = $this->_parameters['put']['position'];
            if (!is_string($pos) || strlen($pos) < 3) {
                $data['message'] = 'position should be string with at least 3 characters';
                return $data;
            }
            $update_vals .= ' `jb_position` = :p, ';
            $params[] = array(':p', $pos, PDO::PARAM_STR);
        }
        if (isset($this->_parameters['put']['description'])) {
            $desc = $this->_parameters['put']['description'];
            if (!is_string($desc) || strlen($desc) < 3) {
                $data['message'] = 'description should be string with at least 3 characters';
                return $data;
            }
            $update_vals .= ' `jb_description` = :d, ';
            $params[] = array(':d', $desc, PDO::PARAM_STR);
        }
        if (isset($this->_parameters['put']['status'])) {
            $status = $this->_parameters['put']['status'];
            if (!is_numeric($status) || !in_array((int)$status, array(0, 1))) {
                $data['message'] = 'status should be 0 or 1';
                return $data;
            }
            $update_vals .= ' `jb_status` = :s, ';
            $params[] = array(':s', $status, PDO::PARAM_INT);
        }
        if (strlen($update_vals) == 0) {
            $data['message'] = 'nothing to update';
            return $data;
        }
        $update_vals = trim($update_vals, ', ');
        $query = 'UPDATE `jobs` SET ' . $update_vals . ' WHERE `jb_id` = :id';
        $params[] = array(':id', $id, PDO::PARAM_INT);
        $ujob = $this->db->query($query, $params);
        if ($ujob > 0) {
            $data['id'] = $id;
        } else {
            $data['message'] = 'nothing was updated.';
        }
        return $data;
    }

    /**
     * Delete job
     * @param $id - job's id
     * @return array
     */
    private function delete_job($id)
    {
        $data = array();
        $query = 'DELETE
                  FROM `jobs`
                  WHERE jb_id = :id';
        $params = array(array(':id', $id, PDO::PARAM_INT));
        $affected_r = $this->db->query($query, $params);
        if ($affected_r == 1) {
            $data['id'] = $id;
        } else {
            $data['message'] = 'Requested job not found';
        }
        return $data;
    }

}