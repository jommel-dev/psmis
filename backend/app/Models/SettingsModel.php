<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = "tblsettings";

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $allowedFields = ['settingName', 'value'];


    public function getLastCustomerID($where){
        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }
}