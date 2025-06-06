<?php

namespace App\Models;

use CodeIgniter\Model;

class AuctionModel extends Model
{
    protected $table      = 'tblauction';
    protected $applicantTable  = 'tblapplicants';
    protected $catTable = "tblcategory";
    protected $primaryKey = 'id';

    protected $allowedFields = ["customerId", "loanId", "loanDate", "expDate", "orNumber", "catId", "itemInfo", "principal", "soldPrice", "soldDate", "createdDate", "status", "remarks"];

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getAllList($where){

        $query = $this->db->table($this->table)->where($where)->orderBy('id', 'ASC')->get();
        $results = $query->getResult();
        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->applicantTable)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
                $category = $this->db->table($this->catTable)->where('id', $el->catId)->get();
                $el->categoryInfo = $category->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }
    public function getAdminAllList(){

        $query = $this->db->table($this->table)->orderBy('id', 'ASC')->get();
        $results = $query->getResult();
        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->applicantTable)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
                $category = $this->db->table($this->catTable)->where('id', $el->catId)->get();
                $el->categoryInfo = $category->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

    // This should get on the TBL Loan History
    public function getAuctionListReport($params){

        $sql = "SELECT * FROM ".$this->table." a
        WHERE a.status = 0 AND
        DATE_FORMAT(a.loanDate, '%Y-%m-%d') BETWEEN :dateFrom: AND :dateTo:";
       
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->applicantTable)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
                $category = $this->db->table($this->catTable)->where('id', $el->catId)->get();
                $el->categoryInfo = $category->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

    public function updateAuctionData($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    public function deleteAuctionData($where){
        $query = $this->db->table($this->table)->where($where)->delete();
        return $query ? true : false;
    }

}