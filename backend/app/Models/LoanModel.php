<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanModel extends Model
{
    protected $table      = 'tblloans';
    protected $applicantTable  = 'tblapplicants';
    protected $usersTable  = 'tblusers';
    protected $tableHistory = 'tblloan_history';
    protected $tableTransaction = 'tbltransactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'customerId', 
        'orNumber', 
        'oldTicket', 
        'loanStatus', 
        'catId', 
        // 'identification', 
        'itemDetails', 
        'loanAmount', 
        'terms', 
        'interest', 
        'charge', 
        'payStatus', 
        'computationDetails', 
        'totalAmount', 
        'datesOfMaturity',
        'maturityDate', 
        'expirationDate', 
        'gracePeriodDate',
        'createdDate',
        'status', 
        'orStatus', 
        'createdBy',
        'cutoffDate'
    ];

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function updateLoanApplication($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }

    public function getAllLoans($where){

        $query = $this->db->table($this->table)->where($where)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();
        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->applicantTable)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;

        // return $results;
    }
    public function getAllSpoiledTickets($where){

        $query = $this->db->table($this->tableTransaction)->where($where)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();
        $all = array_map(function($el){
            foreach($el as $key => $val){
                $loanInfo = $this->db->table($this->table)->where('id', $el->loanId)->get();
                $el->loanInfo = $loanInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;

        // return $results;
    }
    public function getLoanData($where){

        $query = $this->db->table($this->table)->where($where)->orderBy('id', 'DESC')->get();
        $results = $query->getRow();
        $clientInfo = $this->db->table($this->applicantTable)->where('id', $results->customerId)->get();
        $results->customerInfo = $clientInfo->getRow();

        return $results;
    }

    public function getAllLoanAdmin($status){

        $query = $this->db->table($this->table)->whereIn('status', $status)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();
        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->applicantTable)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;

        // return $results;
    }

    public function getAllLoanHistory($where){

        $query = $this->db->table($this->tableHistory)->where($where)->orderBy('id', 'DESC')->get();
        $results = $query->getResult();
        $all = array_map(function($el){
            foreach($el as $key => $val){
                $userInfo = $this->db->table($this->usersTable)->where('id', $el->createdBy)->get();
                $el->creatorInfo = $userInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;

        // return $results;
    }

    public function getLastinsertedReference($req){
        $query = $this->db->table($this->table)->where($req)->orderBy('orNumber', 'DESC')->limit(1)->get();
        $results = $query->getResult();
        return $results;
    }

    // Insert History
    public function addLoanHistory($hisotryData){
        $query = $this->db->table($this->tableHistory)->insert($hisotryData);
        return $query ? true : false;
    }
    public function updateLoanHistory($setData, $where){
        $query = $this->db->table($this->tableHistory)->set($setData)->where($where)->update();
        return $query ? true : false;
    }

    // Insert Transactions
    public function addLoanTransaction($transactionData){
        $query = $this->db->table($this->tableTransaction)->insert($transactionData);
        return $query ? true : false;
    }
    public function updateLoanTransaction($setData, $where){
        $query = $this->db->table($this->tableTransaction)->set($setData)->where($where)->update();
        return $query ? true : false;
    }

    // Reports
    public function getSalesReportList($params){

        // $query = $this->db->table($this->table)->where($where)->orderBy('id', 'DESC')->get();
        // $results = $query->getResult();
        $sql = "SELECT * FROM ".$this->tableTransaction." WHERE orStatus = :status: AND transactionType = :type: AND DATE_FORMAT(createdDate, '%Y-%m-%d') BETWEEN :dateFrom: AND :dateTo: ";
       
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $loanInfo = $this->db->table($this->table)->where('id', $el->loanId)->get();
                $el->loanInfo = $loanInfo->getRow();
                // $customer = $this->db->table($this->applicantTable)->where('id', $el->loanInfo->customerId)->get();
                // $el->customerInfo = $customer->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

    // This should get on the TBL Loan History
    public function getTenColumnReportList($params){

        $sql = "SELECT * FROM ".$this->tableTransaction." a
        INNER JOIN ".$this->table." b ON a.loanId = b.id
        WHERE a.status IN ('renew', 'redeem') AND 
        a.orStatus = :status: AND 
        DATE_FORMAT(a.createdDate, '%Y-%m-%d') BETWEEN :dateFrom: AND :dateTo:";
       
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->applicantTable)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }
    // This should get on the TBL Loan History
    public function getTwentyFourColumnReportList($params){

        $sql = "SELECT * FROM ".$this->tableTransaction." a
        INNER JOIN ".$this->table." b ON a.loanId = b.id
        WHERE a.status IN ('new', 'spoiled') AND 
        a.orStatus = :status: AND 
        DATE_FORMAT(a.createdDate, '%Y-%m-%d') BETWEEN :dateFrom: AND :dateTo:";
       
        $query = $this->db->query($sql, $params);
        $results = $query->getResult();

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->applicantTable)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

}