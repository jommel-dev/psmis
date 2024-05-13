<?php

namespace App\Models;

use CodeIgniter\Model;

class KycModel extends Model
{
    protected $table = "tblkyc";

    protected $allowedFields = ['customerId', 'idType', 'idNumber', 'validityDate', 'file', 'uploadedBy'];

    public function getDetails($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }
}