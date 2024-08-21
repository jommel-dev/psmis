<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\HistoryModel;
use App\Models\ProfileModel;
use App\Models\KycModel;
use App\Models\RequestModel;
use App\Models\ClientModel;
use App\Models\LoanModel;
use App\Models\AuctionModel;
use App\Models\SettingsModel;
use App\Models\SeriesModel;
use \Firebase\JWT\JWT;

class Client extends BaseController
{
    public function __construct(){
        //Models
        $this->userModel = new UsersModel();
        $this->historyModel = new HistoryModel();
        $this->reqModel = new RequestModel();
        $this->clientModel = new ClientModel();
        $this->loanModel = new LoanModel();
        $this->auctionModel = new AuctionModel();
        $this->settingsModel = new SettingsModel();
        $this->seriesModel = new SeriesModel();
        $this->profileModel = new ProfileModel();
        $this->kycModel = new KycModel();
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

        // Get the last inserted Client
        $checkReference = $this->reqModel->getLastinsertedReference();
        $lastPreffix = $this->settingsModel->getSettingValue(["settingName" => "lastCustomerId"]);

        if($checkReference){
            $customerNumber = $checkReference[0]->customerNo + 1;
        } else {
            $customerNumber =  $lastPreffix->value + 1;
        }

        // Insert the customer Data
        $customerInfo = [
            "customerNo" => $customerNumber,
            "name" => $payload->firstName .' '. $payload->lastName .' '. $payload->suffix,
            "firstName" => $payload->firstName,
            "lastName" => $payload->lastName,
            "middleName" => $payload->middleName,
            "suffix" => $payload->suffix,
            "addressLine" => $payload->addressLine,
            "addressDetails" => json_encode($payload->addressDetails),
            "contactNo" => $payload->contactNo,
            "sex" => $payload->sex,
            "birthDate" => $payload->birthDate,
            "otherDetails" => json_encode($payload->otherDetails),
            "createdBy" => $payload->createdBy,
            "status" => 1
        ];

        // //INSERT QUERY TO APPLICATION
        $query = $this->clientModel->insert($customerInfo);

        if($query){
            // json_encode($payload->identifications)
            $lastInserted = $this->clientModel->insertID();
            $profileData = [
                "appId" => $lastInserted,
                "profile" => $payload->profile,
                "eSignature" => $payload->eSignature,
            ];
            $this->profileModel->insert($profileData);

            foreach ($payload->identifications as $key => $value) {
                $kycValues = [
                    "customerId" => $lastInserted,
                    "idType" => $value->type,
                    "idNumber" => $value->idNumber,
                    "validityDate" => $value->validUntil,
                    "file" => $value->image,
                    "uploadedBy" => $payload->createdBy,
                ];
                $this->kycModel->insert($kycValues);
            }
            


            $response = [
                'title' => 'Customer Information',
                'message' => 'Your application has been added.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function updateClientDetails(){
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

        // Insert the customer Data
        $customerInfo =  [
            "firstName" => $payload->firstName,
            "lastName" => $payload->lastName,
            "middleName" => $payload->middleName,
            "suffix" => $payload->suffix,
            "addressLine" => $payload->addressLine,
            "addressDetails" => json_encode($payload->addressDetails),
            "contactNo" => $payload->contactNo,
            "sex" => $payload->sex,
            "birthDate" => $payload->birthDate,
            "otherDetails" => json_encode($payload->otherDetails),
            "createdBy" => $payload->createdBy,
            "status" => 1
        ];

        // //INSERT QUERY TO APPLICATION
        $query = $this->clientModel->where(["id" => $payload->clientId])->set($customerInfo)->update();

        if($query){
            
            $checkProfile = $this->profileModel->getDetails(['appId' => $payload->clientId]);

            if($checkProfile){
                $profileData = [
                    "profile" => $payload->profile,
                    "eSignature" => $payload->eSignature,
                ];
                $this->profileModel->where("appId", $payload->clientId)->set($profileData)->update();
            } else {
                $profileData = [
                    "appId" => $payload->clientId,
                    "profile" => $payload->profile,
                    "eSignature" => $payload->eSignature,
                ];
                $this->profileModel->insert($profileData);
            }
            
            

            // foreach ($payload->identifications as $key => $value) {
            //     $kycValues = [
            //         "idType" => $value->type,
            //         "idNumber" => $value->idNumber,
            //         "validityDate" => $value->validUntil,
            //         "file" => $value->image,
            //         "uploadedBy" => $payload->createdBy,
            //     ];
            //     $this->kycModel->where("customerId", $payload->clientId)->insert($kycValues);
            // }
            


            $response = [
                'title' => 'Customer Information',
                'message' => 'Your application has been updated.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function getUserProfile(){
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
        
        $query = $this->profileModel->getDetails(['appId' => $payload->uid]);

        if($query){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
        } else {
            $response = [
                'title' => 'Error',
                'message' => $query
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getAllClientList(){
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

        $list = [];
        
        $query = $this->clientModel->getAllCustomer($payload->uid);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            
            $list['list'][$key] = [
                "key" => $value->id,
                "customerNo" => $value->customerNo,
                "firstName" => $value->firstName,
                "lastName" => $value->lastName,
                "middleName" => $value->middleName,
                "suffix" => $value->suffix,
                "addressLine" => $value->addressLine,
                "addressDetails" => json_decode($value->addressDetails),
                "contactNo" => $value->contactNo,
                "sex" => $value->sex,
                "birthDate" => $value->birthDate,
                "otherDetails" => json_decode($value->otherDetails),
                // "identifications" =>  json_decode($value->identifications),
                "createdBy" => $value->createdBy,
                "status" => $value->status
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getClientListFilter(){
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

        $list = [];
        
        $query = $this->clientModel->getFilteredClientTransaction($payload);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            // $cinfo->identifications = json_decode($cinfo->identifications);
            $cinfo->otherDetails = json_decode($cinfo->otherDetails);
            
            $list['list'][$key] = [
                "key" => $value->id,
                'customerInfo' => [
                    "key" => $cinfo->id,
                    "customerNo" => $cinfo->customerNo,
                    "firstName" => $cinfo->firstName,
                    "lastName" => $cinfo->lastName,
                    "middleName" => $cinfo->middleName,
                    "suffix" => $cinfo->suffix,
                    "addressLine" => $cinfo->addressLine,
                    "addressDetails" => $cinfo->addressDetails,
                    "contactNo" => $cinfo->contactNo,
                    "sex" => $cinfo->sex,
                    "birthDate" => $cinfo->birthDate,
                    "otherDetails" => $cinfo->otherDetails,
                    // "identifications" => $cinfo->identifications,
                    "createdBy" => $cinfo->createdBy,
                    "status" => $cinfo->status
                ],
                'orNumber' => $value->orNumber, 
                'catId' => $value->catId, 
                // 'identification' => json_decode($value->identification), 
                'itemDetails' => json_decode($value->itemDetails), 
                'loanAmount' => $value->loanAmount, 
                'terms' => $value->terms, 
                'interest' => $value->interest, 
                'charge' => $value->charge, 
                'payStatus' => $value->payStatus, 
                'computationDetails' => json_decode($value->computationDetails), 
                'totalAmount' => $value->totalAmount, 
                'maturityDate' => $value->maturityDate, 
                'expirationDate' => $value->expirationDate, 
                'status' => $value->status,
                'orStatus' => $value->orStatus, 
                'createdBy' => $value->createdBy
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }


    // Loan Process
    public function getLoanList(){
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

        $list = [];
        
        $query = $this->loanModel->getAllLoans(["customerId" => $payload->uid]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            unset($value->identification);
            $fmoneyword = new \NumberFormatter("en", \NumberFormatter::SPELLOUT); 
            $value->amountWord = $fmoneyword->format($value->loanAmount);
            $value->interestWord = $fmoneyword->format($value->interest);
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            // $cinfo->identifications = json_decode($cinfo->identifications);
            $cinfo->otherDetails = json_decode($cinfo->otherDetails);
            // $value->identification = json_decode($value->identification);
            $value->itemDetails = json_decode($value->itemDetails);
            $value->computationDetails = json_decode($value->computationDetails);
            $value->datesOfMaturity = json_decode($value->datesOfMaturity);
            
            
            $list['list'][$key] = [
                "key" => $value->id,
                'customerInfo' => $cinfo, 
                'orNumber' => $value->orNumber, 
                'catId' => $value->catId, 
                // 'identification' => $value->identification, 
                'itemDetails' => $value->itemDetails, 
                'loanAmount' => $value->loanAmount, 
                'terms' => $value->terms, 
                'interest' => $value->interest, 
                'charge' => $value->charge, 
                'payStatus' => $value->payStatus, 
                'computationDetails' => $value->computationDetails, 
                'totalAmount' => $value->totalAmount, 
                'maturityDate' => $value->maturityDate, 
                'expirationDate' => $value->expirationDate, 
                'gracePeriodDate' => $value->gracePeriodDate, 
                'status' => $value->status,
                'orStatus' => $value->orStatus, 
                'createdBy' => $value->createdBy,
                'origData' => $value
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getLoanListForPrint(){
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
        
        $list = [];
        
        $query = $this->loanModel->getAllLoans([]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $fmoneyword = new \NumberFormatter("en", \NumberFormatter::SPELLOUT); 
            $value->amountWord = $fmoneyword->format($value->loanAmount);
            $value->interestWord = $fmoneyword->format($value->interest);
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            $cinfo->otherDetails = json_decode($cinfo->otherDetails);
            // $value->identification = json_decode($value->identification);
            $value->itemDetails = json_decode($value->itemDetails);
            $value->computationDetails = json_decode($value->computationDetails);
            $value->datesOfMaturity = json_decode($value->datesOfMaturity);
            
            $list['list'][$key] = [
                "key" => $value->id,
                'customerInfo' => $cinfo, 
                'orNumber' => $value->orNumber, 
                'catId' => $value->catId, 
                // 'identification' => $value->identification, 
                'itemDetails' => $value->itemDetails, 
                'loanAmount' => $value->loanAmount, 
                'terms' => $value->terms, 
                'interest' => $value->interest, 
                'charge' => $value->charge, 
                'payStatus' => $value->payStatus, 
                'computationDetails' => $value->computationDetails, 
                'totalAmount' => $value->totalAmount, 
                'maturityDate' => $value->maturityDate, 
                'expirationDate' => $value->expirationDate, 
                'gracePeriodDate' => $value->gracePeriodDate, 
                'status' => $value->status,
                'orStatus' => $value->orStatus, 
                'createdBy' => $value->createdBy,
                'origData' => $value
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getLoanHistory(){
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

        $list = [];
        
        $query = $this->loanModel->getAllLoanHistory(["loanId" => $payload->lid]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $snaps = json_decode($value->snapShot);
            if($value->actionType === "0"){
                $snaps->computationDetails = json_decode($snaps->computationDetails);
                $snaps->identification = json_decode($snaps->identification);
                $snaps->itemDetails = json_decode($snaps->itemDetails);
            }
            
            
            $list['list'][$key] =  [
                "key" => $value->id,
                "actionTaken" => $value->actionTaken,
                "actionType" => $value->actionType,
                "snapShot" => $snaps,
                "createdDate" => $value->createdDate,
                "createdBy" => $value->creatorInfo,
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function addLoan(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();

        // Insert the Loan Data
        $loanInfo = [
            'customerId' => $payload->customerId, 
            'orNumber' => $payload->orNumber, 
            'catId' => $payload->catId, 
            // 'identification' => json_encode($payload->identification), 
            'itemDetails' => json_encode($payload->itemDetails), 
            'loanAmount' => $payload->loanAmount, 
            'terms' => $payload->terms, 
            'interest' => $payload->interest, 
            'charge' => $payload->charge, 
            'computationDetails' => json_encode($payload->computationDetails), 
            'totalAmount' => $payload->totalAmount - $payload->charge,
            'datesOfMaturity' => json_encode($payload->datesOfMaturity),  
            'maturityDate' => $payload->maturityDate, 
            'expirationDate' => $payload->expirationDate, 
            'gracePeriodDate' => $payload->gracePeriodDate, 
            'status' => $payload->status,
            'orStatus' => $payload->orStatus, 
            'createdBy' => $payload->createdBy,
            'cutoffDate' => $payload->cutoffDate
        ];

        // //INSERT QUERY TO APPLICATION
        $query = $this->loanModel->insert($loanInfo);

        if($query){
            $lastInserted = $this->loanModel->insertID();
            // Add History
            $history = [
                "loanId" => $lastInserted,
                "actionTaken" => "Created new loan application",
                "actionType" => 0,
                "snapShot" => json_encode($loanInfo),
                "officialReceipt" => $payload->officialReceipt,
                "createdBy" => $payload->createdBy,
            ];
            $this->createHistory($history);
            // Add Transaction

            $payment = [
                "loanId" => $lastInserted,
                "dateMaturity" => "--",
                "amount" => $payload->charge,
                "orStatus" => 0,
                "officialReceipt" => $payload->officialReceipt,
                "orNumber" => $payload->orNumber,
                'status'=>'charges',
                "transactionType" => 4,
                "createdBy" => $payload->createdBy,
            ];
            $outgoing = [
                "loanId" => $lastInserted,
                "dateMaturity" => "--",
                "amount" => $payload->totalAmount,
                "orStatus" => $payload->orStatus,
                "orNumber" => $payload->orNumber,
                "officialReceipt" => $payload->officialReceipt,
                "transactionType" => 1,
                'status'=>'new',
                "createdBy" => $payload->createdBy,
            ];
            $this->createTransaction($payment, "Loan charge payment");
            $this->createTransaction($outgoing, "Loan released to pawner");
            
            // Get Data of last Inserted to Print
            $print = $this->loanModel->getLoanData(["id"=>$lastInserted]);
            $fmoneyword = new \NumberFormatter("en", \NumberFormatter::SPELLOUT); 
            $print->amountWord = $fmoneyword->format($print->loanAmount);
            $print->interestWord = $fmoneyword->format($print->interest);
            $cinfo = $print->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            // $cinfo->identifications = json_decode($cinfo->identifications);
            $cinfo->otherDetails = json_decode($cinfo->otherDetails);
            // $print->identification = json_decode($print->identification);
            $print->itemDetails = json_decode($print->itemDetails);
            $print->computationDetails = json_decode($print->computationDetails);
            $print->datesOfMaturity = json_decode($print->datesOfMaturity);
            $print->customerInfo = $cinfo;

            // Response 
            $response = [
                'title' => 'Loan Information',
                'message' => 'Your loan application has been added.',
                'dataPrint' => $print
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function renewLoan(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();
       

        // Update the Loan Data and Insert Transaction
        $where = ['id' => $payload->loanId];
        $payload->updateLoan->datesOfMaturity = json_encode($payload->updateLoan->datesOfMaturity);
        $loanDataUpdate = json_decode(json_encode($payload->updateLoan), true);
        $transactionData = json_decode(json_encode($payload->transaction), true);

        // //INSERT QUERY TO APPLICATION
        $query = $this->loanModel->updateLoanApplication($where, $loanDataUpdate);

        if($query){
            // Add History
            $history = [
                "loanId" => $payload->loanId,
                "actionTaken" => "Renew loan payment",
                "actionType" => 5,
                "snapShot" => json_encode($transactionData),
                "officialReceipt" => $payload->transaction->officialReceipt,
                "createdBy" => $payload->createdBy,
            ];
            $this->createHistory($history);

            // Add Transaction
            $this->createTransactionOnly($transactionData, "Loan application renewal");
            
            
            // Response 
            $response = [
                'title' => 'Loan Renewal',
                'message' => 'Your loan application has been renewed.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function fullPaidLoan(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();
       

        // Update the Loan Data and Insert Transaction
        $where = ['id' => $payload->loanId];
        $loanDataUpdate = json_decode(json_encode($payload->updateLoan), true);
        $transactionData = json_decode(json_encode($payload->transaction), true);

        // //INSERT QUERY TO APPLICATION
        $query = $this->loanModel->updateLoanApplication($where, $loanDataUpdate);

        if($query){
            // Add History
            $history = [
                "loanId" => $payload->loanId,
                "actionTaken" => "Loan pay in full and redeemed",
                "actionType" => 3,
                "snapShot" => json_encode($transactionData),
                "createdBy" => $payload->createdBy,
            ];
            $this->createHistory($history);

            // Add Transaction
            $this->createTransactionOnly($transactionData, "Redeemed loan");
            
            
            // Response 
            $response = [
                'title' => 'Loan Renewal',
                'message' => 'Your loan application has been renewed.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function spoiledTicket(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();
       

        // Update the Loan Data and Insert Transaction
        $where = ['orNumber' => $payload->ticketNumber];
        $updateTransaction = json_decode(json_encode($payload->updateTransaction), true);

        
        $query = $this->loanModel->getLoanData($where);

        if($query){
            // Add History
            $history = [
                "loanId" => $query->id,
                "actionTaken" => $payload->reason,
                "actionType" => 6,
                "snapShot" => json_encode($updateTransaction),
                "createdBy" => $payload->createdBy,
            ];
            $this->createHistory($history);
            $this->updateTransactionOnly($updateTransaction, ['orNumber' => $payload->ticketNumber]);

            // Response 
            $response = [
                'title' => 'Spoiled Ticket',
                'message' => 'Your ticket number has been spoiled.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Process Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function getReferenceSequence(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();

        $where = [
            "createdBy" => $payload->uid,
        ];

        // Check if there was starting loan application on the user
        $result = [];
        $firstReference = $this->settingsModel->getSettingValue(["settingName" => "ticketNo"]);
        $query = $this->loanModel->getLastinsertedReference(["createdBy" => $payload->uid]);
        $series = $this->seriesModel->getSeriesInfo(["userId" => $payload->uid]);

        // if the series is not yet started
        if($query){
            $series->start = sprintf('%06d', $query[0]->orNumber + 1);
        }

        $result = $series;
        
        if($result){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($result));
        } else {
            $response = [
                'error' => 404,
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function createHistory($data){
        $this->loanModel->addLoanHistory($data);
    }

    public function createTransactionOnly($data, $message){
        $this->loanModel->addLoanTransaction($data);
    }
    public function updateTransactionOnly($data, $where){
        $this->loanModel->updateLoanTransaction($data, $where);
    }

    public function createTransaction($data, $message){
        $this->loanModel->addLoanTransaction($data);
        // Add History for transaction
        $payload = [
            "loanId" => $data['loanId'],
            "actionTaken" => $message,
            "actionType" => 3,
            "snapShot" => json_encode((Object)$data),
            "createdBy" => $data['createdBy'],
        ];
        $this->createHistory($payload);
    }


    public function getRenewRecordList(){
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

        $list = [];
        
        $query = $this->loanModel->getAllLoanHistory(["loanId" => $payload->loanId, "actionType" => 5]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $snaps = json_decode($value->snapShot);
            if($value->actionType === "0"){
                $snaps->computationDetails = json_decode($snaps->computationDetails);
                // $snaps->identification = json_decode($snaps->identification);
                $snaps->itemDetails = json_decode($snaps->itemDetails);
            }
            
            
            $list['list'][$key] =  [
                "key" => $value->id,
                "description" => $value->actionTaken,
                "actionType" => $value->actionType,
                "snapShot" => $snaps,
                "dateTransaction" => $value->createdDate,
                "createdBy" => $value->creatorInfo,
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'error' => 404,
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function getAllRecordList(){
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

        $list = [];
        
        $query = $this->loanModel->getAllLoanHistory(["loanId" => $payload->loanId]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $snaps = json_decode($value->snapShot);
            if($value->actionType === "0"){
                $snaps->computationDetails = json_decode($snaps->computationDetails);
                // $snaps->identification = json_decode($snaps->identification);
                $snaps->itemDetails = json_decode($snaps->itemDetails);
            }
            
            
            $list['list'][$key] =  [
                "key" => $value->id,
                "description" => $value->actionTaken,
                "actionType" => $value->actionType,
                "snapShot" => $snaps,
                "dateTransaction" => $value->createdDate,
                "createdBy" => $value->creatorInfo,
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'error' => 404,
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    // Auction Actions
    public function getAuctionedList(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();

        $list = [];
        
        if($payload->utype === 1){
            $query = $this->auctionModel->getAdminAllList();
        } else {
            $query = $this->auctionModel->getAllList(["status"=>0]);
        }
        
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $cinfo = $value->customerInfo;
            $cat = $value->categoryInfo;

            $list['list'][$key] = [
                "key" => $value->id,
                "customerId" => $value->customerId,
                "loanId" => $value->loanId,
                'customerName' => $cinfo->lastName .", ". $cinfo->firstName ." ". $cinfo->suffix ." ".  $cinfo->middleName, 
                'orNumber' => $value->orNumber, 
                'category' => $cat->value,
                'expDate' => $value->expDate,
                'item' => $value->itemInfo,
                'principal' => $value->principal,
                'soldPrice' => $value->soldPrice,
                'soldDate' => $value->soldDate,
                'createdDate' => $value->createdDate,
                'status' => $value->status,
                'remarks' => $value->remarks,
                'orStatus' => $value->orStatus,
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'error' => 404,
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    
    public function soldAuctionedItem(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();
       

        // Update the Loan Data and Insert Transaction
        $where = ['id' => $payload->auctionId];
        $auctionData = json_decode(json_encode($payload->auction), true);
        $transactionData = json_decode(json_encode($payload->transaction), true);

        // //INSERT QUERY TO APPLICATION
        $query = $this->auctionModel->updateAuctionData($where, $auctionData);

        if($query){
            // Add Transaction
            $this->createTransactionOnly($transactionData, "Item Sold from Auction");
            
            // Response 
            $response = [
                'title' => 'Auction Item Sold',
                'message' => 'Your loan application has been renewed.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    public function updateAuctionedItem(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();
       

        // Update the Loan Data and Insert Transaction
        $where = ['id' => $payload->auctionId];
        $auctionData = json_decode(json_encode($payload->auction), true);

        // //INSERT QUERY TO APPLICATION
        $query = $this->auctionModel->updateAuctionData($where, $auctionData);

        if($query){
            
            // Response 
            $response = [
                'title' => 'Auction Item Updated',
                'message' => 'Your loan application has been renewed.'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'title' => 'Data Submit Failed',
                'message' => 'Please contact the admin for concern'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }

    }

    // --------------------- End of Auction Action ------------------- //
    // Spoiled Actions
    public function getSpoiledList(){
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

        //Get API Request Data from UI
        $payload = $this->request->getJSON();

        $list = [];
        
        $query = $this->loanModel->getAllSpoiledTickets(["transactionType"=>5]);
        
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $items = json_decode($value->loanInfo->itemDetails);
            $list['list'][$key] = [
                "key" => $value->id,
                "loanId" => $value->loanId,
                'orNumber' => $value->orNumber,
                'loanValue' => $value->loanInfo,
                'itemInfo' => $items
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'error' => 404,
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }
    // --------------------- End of Spoiled Action ------------------- //

    public function getLoansList(){
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

        $list = [];
        
        $query = $this->loanModel->getAllLoanAdmin([1]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            unset($value->identification);
            $fmoneyword = new \NumberFormatter("en", \NumberFormatter::SPELLOUT); 
            $value->amountWord = $fmoneyword->format($value->loanAmount);
            $value->interestWord = $fmoneyword->format($value->interest);
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            // $cinfo->identifications = json_decode($cinfo->identifications);
            $cinfo->otherDetails = json_decode($cinfo->otherDetails);
            // $value->identification = json_decode($value->identification);
            $value->itemDetails = json_decode($value->itemDetails);
            $value->computationDetails = json_decode($value->computationDetails);
            
            
            $list['list'][$key] = [
                "key" => $value->id,
                'customerInfo' => $cinfo, 
                'orNumber' => $value->orNumber, 
                'catId' => $value->catId, 
                // 'identification' => $value->identification, 
                'itemDetails' => $value->itemDetails, 
                'loanAmount' => $value->loanAmount, 
                'terms' => $value->terms, 
                'interest' => $value->interest, 
                'charge' => $value->charge, 
                'payStatus' => $value->payStatus, 
                'computationDetails' => $value->computationDetails, 
                'totalAmount' => $value->totalAmount, 
                'maturityDate' => $value->maturityDate, 
                'expirationDate' => $value->expirationDate, 
                'gracePeriodDate' => $value->gracePeriodDate, 
                'status' => $value->status,
                'orStatus' => $value->orStatus, 
                'createdBy' => $value->createdBy,
                'origData' => $value
            ];
        }

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }


}