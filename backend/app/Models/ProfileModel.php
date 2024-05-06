<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table = "tblprofiles";

    protected $allowedFields = ['appId', 'profile', 'eSignature'];

    public function getDetails($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }
}