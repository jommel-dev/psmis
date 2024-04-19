<?php

namespace App\Models;

use CodeIgniter\Model;

class CutoffModel extends Model
{
    protected $table      = 'tblcutoff';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['startDate', 'endDate', 'endDateSales', 'startedBy'];

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getCurrentCutOffDate($where){
        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();
        return $results;
    }

    public function getCurrentCutOffDateUser(){
        $query = $this->db->table($this->table)->orderBy('id', 'DESC')->limit(1)->get();
        $results = $query->getRow();
        return $results;
    }

    public function updateCutoffDate($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }


}