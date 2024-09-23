<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RequestModel;
use App\Models\AuthModel;
use App\Models\LoanModel;
use App\Models\AuctionModel;
use App\Models\HistoryModel;
use App\Models\CutoffModel;
use \Firebase\JWT\JWT;

class Dashboard extends BaseController
{
    public function __construct(){
        //Models
        $this->reqModel = new RequestModel();
        $this->authModel = new AuthModel();
        $this->loanModel = new LoanModel();
        $this->auctionModel = new AuctionModel();
        $this->historyModel = new HistoryModel();
        $this->cutoffModel = new CutoffModel();
    }

    public function getCutoffDate(){
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

        $payload = $this->request->getJSON();

        $where = json_decode(json_encode($payload->checkDate), true);
        $query = $this->cutoffModel->getCurrentCutOffDate($where);

        if($query){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
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
    public function getCutoffDateLast(){
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

        $query = $this->cutoffModel->getCurrentCutOffDateUser();

        if($query){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
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

    public function startCutoffDate(){
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

        $payload = $this->request->getJSON();

        $cutoffDetails = [
            "startDate" => $payload->currDate,
            "startedBy" => $payload->userId
        ];

        // //INSERT QUERY TO APPLICATION
        $query = $this->cutoffModel->insert($cutoffDetails);

        if($query){
            $response = [
                'title' => 'Start of Day',
                'message' => 'Date has been set'
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
    public function endCutoffDate(){
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
        
        try {
            $payload = $this->request->getJSON();

            $where = ["id"=>$payload->cutOffId];
            $setData = ["endDate"=>$payload->endDate];
            $query = $this->cutoffModel->updateCutoffDate($where,$setData);
            
            if($query){
                $response = [
                    'title' => 'End of Day',
                    'message' => 'Date has been set'
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
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public function getDashboard(){
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
        $loans = 0;
        $salesToday = 0;
        $totalSales = 1000;

        $payload = $this->request->getJSON();

        if($payload->userType != 1){
            //All Dashboard from branch display in Main branch
            $query = $this->reqModel->getDashboardResultsUser(['createdBy' => $payload->userId]);
            $totalLoans = $this->reqModel->getDashboardTotalLoanUser(['createdBy' => $payload->userId, 'status'=>1]);
            $totalRedeem = $this->reqModel->getDashboardTotalLoanUser(['createdBy' => $payload->userId, 'status'=>4]);
            $totalExpired = $this->reqModel->getDashboardTotalLoanUser(['createdBy' => $payload->userId, 'status'=>0]);
            $totalSales = $this->reqModel->getDashboardTotalSales(['createdBy' => $payload->userId, 'transactionType'=>0]);
            $totalCharge = $this->reqModel->getDashboardTotalSales(['createdBy' => $payload->userId, 'transactionType'=>4]);
            $totalLoanExpense = $this->reqModel->getDashboardTotalSales(['createdBy' => $payload->userId, 'transactionType'=>1]);
            $salesToday = $this->reqModel->getDashboardTodaySales([
                'createdBy' => $payload->userId, 
                'transactionType'=>0, 
                'createdDate'=>$payload->currDate
            ]);
        } else {
            //here is the per branch Dashboard
            $query = $this->reqModel->getDashboardResults();
            $totalLoans = $this->reqModel->getDashboardTotalLoanUser(['status'=>1]);
            $totalRedeem = $this->reqModel->getDashboardTotalLoanUser(['status'=>4]);
            $totalExpired = $this->reqModel->getDashboardTotalLoanUser(['status'=>0]);
            $totalSales = $this->reqModel->getDashboardTotalSales(['transactionType'=>0]);
            $totalCharge = $this->reqModel->getDashboardTotalSales(['transactionType'=>4]);
            $totalLoanExpense = $this->reqModel->getDashboardTotalSales(['transactionType'=>1]);
            $salesToday = $this->reqModel->getDashboardAdminTodaySales([
                'transactionType'=>0, 
                'createdDate'=>$payload->currDate
            ]);
        }


        $list = [
            'todaySales' => $salesToday,
            'applicants' => sizeof($query),
            'totalLoans' => sizeof($totalLoans),
            'totalRedeem' => sizeof($totalRedeem),
            'totalExpired' => sizeof($totalExpired),
            'totalSales' => $totalSales,
            'totalCharge' => $totalCharge,
            'totalLoanExpense' => $totalLoanExpense,
        ];

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

    public function getDailyDashboard(){
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
        $loans = 0;
        $salesToday = 0;

        $payload = $this->request->getJSON();

        if($payload->userType != 1){
            //All Dashboard from branch display in Main branch
            $query = $this->reqModel->getDashboardTodayUser(['createdBy' => $payload->userId, "createdDate" => $payload->currDate]);
            $totalLoans = $this->reqModel->getDashboardTodayLoans(['createdBy' => $payload->userId, 'status'=>1, "createdDate" => $payload->currDate]);
            $totalRedeem = $this->reqModel->getDashboardTodayLoans(['createdBy' => $payload->userId, 'status'=>4, "createdDate" => $payload->currDate]);
            $totalExpired = $this->reqModel->getDashboardTodayLoans(['createdBy' => $payload->userId, 'status'=>0, "createdDate" => $payload->currDate]);
            $totalCharge = $this->reqModel->getDashboardTodaySales([
                'createdBy' => $payload->userId, 
                'transactionType'=>4,
                'createdDate'=>$payload->currDate
            ]);
            $salesToday = $this->reqModel->getDashboardTodaySales([
                'createdBy' => $payload->userId, 
                'transactionType'=>0, 
                'createdDate'=>$payload->currDate
            ]);
        } else {
            //here is the per branch Dashboard
            $query = $this->reqModel->getDashboardResults();
            $totalLoans = $this->reqModel->getDashboardTotalLoanUser(['status'=>1]);
            $totalRedeem = $this->reqModel->getDashboardTotalLoanUser(['status'=>4]);
            $totalExpired = $this->reqModel->getDashboardTotalLoanUser(['status'=>0]);
            $totalCharge = $this->reqModel->getDashboardTotalSales(['transactionType'=>4]);
            $salesToday = $this->reqModel->getDashboardAdminTodaySales([
                'transactionType'=>0, 
                'createdDate'=>$payload->currDate
            ]);
        }


        $list = [
            'todaySales' => $salesToday,
            'applicants' => sizeof($query),
            'totalLoans' => sizeof($totalLoans),
            'totalRedeem' => sizeof($totalRedeem),
            'totalExpired' => sizeof($totalExpired),
            'totalCharge' => $totalCharge,
        ];

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

    public function updateLoanApplicationTerms(){
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
        
        try {
            $payload = $this->request->getJSON();

            // Status 1 - Active Application and check for the Maturity to Expiry to grace period
            // Status 2 - Bidding Items
            // Status 3 - Sold from Bid/Auction Items
            // Status 4 - Redeemed Items
            // Status 5... - Deffered Items (Expired)
            $query = $this->loanModel->getAllLoans([
                "status" => 1
            ]);
            foreach ($query as $key => $value) {
                $date_now = date($payload->currDate);
                $value->itemDetails = json_decode($value->itemDetails);
                // Start of checking of Pay Status and Term to GracePeriod Matches
                // if Reach the Expiration
                if($value->payStatus === $value->terms){
                    // check the Curr Date > Grace Period 
                    // 'createdDate'=>$payload->currDate
                    $dateGrace = date($value->gracePeriodDate);
                    if($date_now > $dateGrace){
                        $updateDate = [
                            'loanStatus' => 'Auctioned Expire',
                            'status' => 0,
                        ];
                        $qUpdate = $this->loanModel->updateLoanApplication(['id' => $value->id], $updateDate);
                        // $history = [
                        //     "loanId" => $value->id,
                        //     "actionTaken" => "Loan gets expired and moved to Auction List",
                        //     "actionType" => 2,
                        //     "snapShot" => json_encode($value),
                        //     "createdBy" => 0,
                        // ];
                        // $this->createHistory($history);
                        $this->insertToAuctionList($value);
                    }
                } else {
                    // first check curr date > maturity
                    // check for the payStatus that starts to 1 and check curr date > maturity date
                    // Loop the Terms to identify conditions
                    for ($i = 0; $i < $value->terms; $i++) {
                        // get the Maturity Date recorded
                        $date = $value->maturityDate;
                        // Add months based on the terms of loan
                        $newDate = date('Y-m-d', strtotime($date. ' + '.$i.' months'));
                        // from looped stats is the match point of payStatus datafield
                        $stats = $i+1;
                        // Here is where the payStatus will update check of the current Date is > to the date generated from newDate
                        if($date_now > $newDate && (int)$value->payStatus === $stats){
                            $updateDate = [
                                'loanStatus' => 'Past Due',
                                'payStatus' => (int)$value->payStatus+1
                            ];
                            $qUpdate = $this->loanModel->updateLoanApplication(['id' => $value->id], $updateDate);
                        }
                        
                    }
                }
            }
    
            return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setBody(json_encode(["message"=> "Update the status of application done"]));
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }

    public function createHistory($data){
        $this->loanModel->addLoanHistory($data);
    }

    public function insertToAuctionList($dataItem){
        // print_r($dataItem);
        // exit();
        // Insert the Loan Data
        foreach ($dataItem->itemDetails as $key => $value) {
            $loanInfo = [
                'customerId' => $dataItem->customerId, 
                'loanId' => $dataItem->id, 
                'expDate' => $dataItem->gracePeriodDate, 
                'orNumber' => $dataItem->orNumber, 
                'catId' => $dataItem->catId, 
                'itemInfo' => $value->qty ." ". $value->unit->value ." ". $value->type->value .", ". $value->description .", ". $value->property .", ". $value->weight, 
                'principal' => $dataItem->loanAmount,
                'remarks' => $value->remarks,
                'orStatus' => $dataItem->orStatus,
            ];

            //INSERT QUERY TO APPLICATION
            $this->auctionModel->insert($loanInfo);
        }
        

        
    }
    

}