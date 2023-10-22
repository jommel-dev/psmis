<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\HistoryModel;
use \Firebase\JWT\JWT;

class Client extends BaseController
{
    public function __construct(){
        //Models
        $this->userModel = new UsersModel();
        $this->historyModel = new HistoryModel();
    }

    public function registerClient(){
        // Check Auth header bearer
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        if(!$authorization){
            $response = [
                'message' => 'Unauthorized Access'
            ];

            return $this->response
                    ->setStatusCode(401)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            exit();
        }

        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();
        $payload = json_decode(json_encode($payload), true);
        $caseForm =  $payload['caseForm'];
        $isAntigen = $caseForm['laboratoryInfo'][0]['typeOfTest'] != 'Antigen' ? true : false;

        //Check the Last Created Reference
        // $suffix = "MLIS";
        $suffix = $isAntigen ?  "MLIS" : "MLISAT";
        $numberSequence = date('Ymd') .'0'. 101 ;
        $reference = '';

        $checkReference = $this->reqModel->getLastinsertedReference();
        
        if($checkReference){
            //if has exists generate addition 
            $lastCountDigit = substr($checkReference[0]->referenceId, -3);
            $latestRefID =  $lastCountDigit + 1;
            $numberSequence = date('Ymd') . '0' . $latestRefID;
            $reference = $suffix . $numberSequence;
        } else {
            $reference = $suffix . $numberSequence;
        }
            
        

        $spacimentApplicationData = [
            "referenceId" => $reference,
            "status" => 'MLIS000',
            "status" => $suffix .'000',
            "statusDescription" => $isAntigen ? 'New specimen has ben submitted.' : 'New antigen specimen has ben submitted.',
            // "statusDescription" => 'New specimen has ben submitted.',
            "branchId" => $payload['branchId'],
            "contactTracing" => json_encode($payload['contactTracing']),
            "interviewForm" => json_encode($payload['interviewForm']),
            "patientForm" => json_encode($payload['patientForm']),
            "caseForm" => json_encode($payload['caseForm']),
            "createdBy" => $payload['userId'],
            "specimenNumber" => isset($payload['isDuplicate']) ?  $payload['specimenNumber'] : 1,
        ];

        //INSERT QUERY TO APPLICATION
        $query = $this->reqModel->insertApplication($spacimentApplicationData);

        if($query){
            $lastInserted = $this->reqModel->insertID();
            $history = [
                'applicationId' => $lastInserted,
                'requestData' => json_encode($spacimentApplicationData),
                'actionStatus' => 'New specimen has been submitted.',
                'createdBy' => $payload['userId'],
            ];

            $this->historyModel->insert($history);


            $response = [
                'title' => 'Case Investigation Form',
                'message' => 'Your application has been submitted.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    function getUserName($n) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
     
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
     
        return $randomString;
    }


}