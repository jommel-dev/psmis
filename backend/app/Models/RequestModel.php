<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestModel extends Model
{
    protected $table = "tblapplicants";
    // PSMIS
    protected $tableLoan = "tblloans";
    protected $tableTransac = "tbltransactions";
    // Olds
    protected $tableUser = "tblusers";
    protected $tableProcess = "tblprocessflow";
    protected $tableBuilding = "tblbuilding_info";
    protected $tableArchive = "tblapplications_1";

    // protected $createdField  = 'createdDate';
    public function getLastinsertedReference(){

        $query = $this->db->table($this->table)->orderBy('id', 'DESC')->limit(1)->get();
        $results = $query->getResult();
        return $results;

    }

    public function getTenantRequests($id){

        $query = $this->db->table($this->table)->where(['tenantId'=> $id])->get();
        $results = $query->getResult();
        return $results;

    }
    
    public function insertApplication($data){

        $query = $this->db->table($this->table)->insert($data);
        return $query ? true : false;

    }

    public function updateApplication($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }

    public function getAllApplication($status, $id){

        $query = $this->db->table($this->table)->whereIn('status', $status)->where('userId', $id)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }
    public function getAllApplicationApproverMD($status, $id){

        $query = $this->db->table($this->table)->whereIn('status', $status)->where(['userId' => $id, "approveBy" => ""])->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }
    public function getAllApplicationApproverHead($status, $id){

        $query = $this->db->table($this->table)->whereIn('status', $status)->where(['userId' => $id, "approveBy !=" => ""])->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }
    public function getAllApplicationBranch($status, $id, $branch){

        $query = $this->db->table($this->table)->whereIn('status', $status)->where(['userId' => $id, 'branchId' => $branch])->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }

    public function getAllApplicationAdmin($status){

        $query = $this->db->table($this->table)->whereIn('status', $status)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();

        return $results;
    }

    public function getDetails($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }

    public function getDetailsByPatientName($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }

    // For OLD Data
    public function getDetailsOld($where, $tbl){

        $query = $this->db->table($tbl)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }




    public function getRequisitioner($where){

        $query = $this->db->table($this->tableUser)->select('lastName, firstName, middleName, suffix')->where($where)->get();
        $results = $query->getRow();

        return $results;
    }
    public function getUserSignature($where){

        $query = $this->db->table($this->tableUser)->select('lastName, firstName, middleName, suffix, eSignature, prcNumber')->where($where)->get();
        $results = $query->getRow();

        return $results;
    }


    public function getDashboardResults(){
        $query = $this->db->table($this->table)->get();
        $results = $query->getResult();

        return $results;
    }

    public function getDashboardResultsUser($where){

        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }

    public function getDashboardTotalLoanUser($where){

        $query = $this->db->table($this->tableLoan)->where($where)->get();
        $results = $query->getResult();

        return $results;
    }

    public function getDashboardTotalSales($where){
        $total = 0;
        $query = $this->db->table($this->tableTransac)->where($where)->get();
        $results = $query->getResult();

        foreach ($results as $key => $value) {
            $total += $value->amount;
        }

        return $total;
    }
   
    public function getDashboardTodayUser($params){
        $total = 0;
        $sql = "SELECT * FROM ".$this->table." WHERE createdBy = :createdBy: AND DATE_FORMAT(createdDate, '%Y-%m-%d') BETWEEN :createdDate: AND :createdDate:";
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        return $results;
    }

    public function getDashboardTodayLoans($params){
        $total = 0;
        $sql = "SELECT * FROM ".$this->tableLoan." WHERE createdBy = :createdBy: AND status = :status: AND DATE_FORMAT(createdDate, '%Y-%m-%d') BETWEEN :createdDate: AND :createdDate:";
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        return $results;
    }

    public function getDashboardTodaySales($params){
        $total = 0;
        $sql = "SELECT * FROM `tbltransactions` WHERE createdBy = :createdBy: AND transactionType=:transactionType: AND DATE_FORMAT(createdDate, '%Y-%m-%d') BETWEEN :createdDate: AND :createdDate:";
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        foreach ($results as $key => $value) {
            $total += $value->amount;
        }

        return $total;
    }




    public function getDashboardAdminTodaySales($params){
        $total = 0;
        $sql = "SELECT * FROM `tbltransactions` WHERE transactionType=:transactionType: AND DATE_FORMAT(createdDate, '%Y-%m-%d') BETWEEN :createdDate: AND :createdDate:";
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        foreach ($results as $key => $value) {
            $total += $value->amount;
        }

        return $total;
    }

    // Archive Applications
    

}