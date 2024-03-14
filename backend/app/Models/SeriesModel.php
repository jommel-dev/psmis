<?php

namespace App\Models;

use CodeIgniter\Model;

class SeriesModel extends Model
{
    protected $table      = 'tblseries';
    protected $userTable  = 'tblusers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['userId', 'seriesDate', 'start', 'end', 'createdBy', 'reportStatus'];

    protected $useTimestamps = false;
    // protected $createdField  = 'createdDate';
    // protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;


    public function getSeriesListWithUser($where){
        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $user = $this->db->table($this->userTable)->where('id', $el['userId'])->get()->getRow();
                $el['userInfo'] = $user;
            }
            return $el;
        }, $results);

        return $all;
    }

    public function getSeriesInfo($where){
        $query = $this->db->table($this->table)->where($where)->get();
        $results = $query->getRow();

        return $results;
    }

    // public function getSeriesInfo($id){
    //     $query = $this->db->table($this->table)->where('id', $id)->get();
    //     $results = $query->getRow();
    //     return $results;
    // }

    public function updateSeriesInfo($where, $setData){
        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;
    }
    

}