<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = "tblsettings";

    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['settingName', 'value'];


    public function getSettingValue($where){
        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }

    public function getSettingValues(){
        $query = $this->db->table($this->table)->get();
        $results = $query->getResult();

        return (array)$results;
    }

    public function updateSettingInfo($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    
}