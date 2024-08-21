<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table      = 'tblapplicants';
    protected $tableLoan      = 'tblloans';
    protected $tableHistory = 'tblloan_history';
    protected $tableTransaction = 'tbltransactions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'customerNo', 
        'firstName', 
        'lastName', 
        'middleName', 
        'suffix', 
        'addressLine', 
        'addressDetails', 
        'contactNo', 
        'sex', 
        'birthDate', 
        'otherDetails', 
        'identifications', 
        'eSignature', 
        'profile', 
        'createdBy', 
        'status'
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

    public function getAllCustomer($uid){

        $query = $this->db->table($this->table)->where([
            "status" => 1,
            // "createdBy" => $uid
        ])->orderBy('id', 'DESC')->get();
        $results = $query->getResult(); 

        return $results;
    }

    public function updateCustomerInfo($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    

    public function getFilteredClientTransaction($req){
        $sql = "SELECT * FROM tblloans WHERE customerId = (SELECT id FROM tblapplicants WHERE customerNo = :customerNo:) AND orNumber = :orNumber:";
       
        $query = $this->db->query($sql, $req);
        $results = $query->getResult();

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $clientInfo = $this->db->table($this->table)->where('id', $el->customerId)->get();
                $el->customerInfo = $clientInfo->getRow();
            }
            return $el;
        }, $results);

        return $all;
    }

}