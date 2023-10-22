<?php

namespace App\Models;

use CodeIgniter\Model;

class MiscModel extends Model
{
    protected $usertypeTable = "tblusertypes";
    protected $branchTable = "tblbranches";
    protected $catTable = "tblcategory";
    protected $unitTable = "tblunits";


    public function getTypeList(){
        $query = $this->db->table($this->usertypeTable)->get();
        $results = $query->getResult();
        return $results;
    }

    public function getBranchList(){
        $query = $this->db->table($this->branchTable)->get();
        $results = $query->getResult();
        return $results;
    }
    public function getCatList(){
        $query = $this->db->table($this->catTable)->get();
        $results = $query->getResult();
        return $results;
    }
    public function getUnitList(){
        $query = $this->db->table($this->unitTable)->get();
        $results = $query->getResult();
        return $results;
    }

    public function getUserType($userType){

        $query = $this->db->table($this->usertypeTable)->where('id', $userType)->get();
        $results = $query->getRow();

        return $results;

    }
    public function getBranch($branchId){

        $query = $this->db->table($this->branchTable)->where('id', $branchId)->get();
        $results = $query->getRow();

        return $results;

    }

}