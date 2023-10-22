<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table      = 'tblapplicants';
    protected $primaryKey = 'id';

    protected $allowedFields = ['customerNo', 'firstName', 'lastName', 'middleName', 'suffix', 'addressLine', 'addressDetails', 'contactNo', 'sex', 'birthDate', 'otherDetails', 'kycId', 'createdBy', 'status'];

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

}