<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\RequestModel;
use App\Models\InvoiceModel;
use App\Models\AuthModel;
use App\Models\HistoryModel;
use App\Models\ClientModel;
use App\Models\LoanModel;
use App\Models\AuctionModel;

use \Firebase\JWT\JWT;

// QR Code Components
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class Generate extends BaseController
{
    public function __construct(){
        //Models
        $this->reqModel = new RequestModel();
        $this->invModel = new InvoiceModel();
        $this->authModel = new AuthModel();
        $this->historyModel = new HistoryModel();
        $this->clientModel = new ClientModel();
        $this->loanModel = new LoanModel();
        $this->auctionModel = new AuctionModel();
    }

    public function createInvoice(){

        $data = $this->request->getJSON();
        $downloadLink = base_url("index.php/psmis/api/v1/generate/print/invoice/". $data->invoiceId);

        $response = [
            "urlLink" =>  $downloadLink
        ];
            
        return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));

    }

    public function generateInvoicePdf($id){

        $query = $this->invModel->getInvoiceDetails(["id" => $id]);
        $query->customerInfo->contactPerson = json_decode($query->customerInfo->contactPerson);
        $query->customerInfo->addressInfo = json_decode($query->customerInfo->addressInfo);

        // echo "<pre>";
        // print_r($query);

        ob_start();
        $dompdf = new \Dompdf\Dompdf(['enable_font_subsetting' => true]); 
        $dompdf->loadHtml(view('invoice/print', (array)$query));
        $dompdf->setPaper('Legal', 'portrait');
        $dompdf->render();
        $dompdf->stream('Invoice', ["Attachment" => false]);
    }

    public function salesReport(){
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
        
        $query = $this->loanModel->getSalesReportList([
            "dateFrom" => $payload->from,
            "dateTo" => $payload->to,
            "status" => $payload->status,
            "type" => $payload->type,
        ]);
        

        foreach ($query as $key => $value) {
            $fmoneyword = new \NumberFormatter("en", \NumberFormatter::SPELLOUT); 
            $value->amountWord = $fmoneyword->format($value->loanAmount);
            $value->interestWord = $fmoneyword->format($value->interest);
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            $cinfo->identifications = json_decode($cinfo->identifications);
            $cinfo->otherDetails = json_decode($cinfo->otherDetails);
            $value->identification = json_decode($value->identification);
            $value->itemDetails = json_decode($value->itemDetails);
            $value->computationDetails = json_decode($value->computationDetails);
            
            $list['list'][$key] = [
                "key" => $value->id,
                'customerInfo' => $cinfo, 
                'orNumber' => $value->orNumber, 
                'catId' => $value->catId, 
                'identification' => $value->identification, 
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

    // 10 Columns
    public function generateTenCoulumn($dateFrom, $dateTo, $reportType){
        $genDate = $dateFrom === $dateTo ? 
        date("F j, Y", strtotime($dateFrom)) : 
        date("F j, Y", strtotime($dateFrom)) ." - ". date("F j, Y", strtotime($dateTo));
        $list = [
            "generatedDate" => $genDate,
            "columns" => [
                [ 
                    "name"=>'no', 
                    "label" => 'NO', 
                    "field" => 'no',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'pawnerName', 
                    "label" => 'NAME OF THE PAWNER', 
                    "field" => 'pawnerName',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'date', 
                    "label" => 'DATE', 
                    "field" => 'date',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnTicket', 
                    "label" => 'PAWN TICKET NO.', 
                    "field" => 'pawnTicket',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'orNumber', 
                    "label" => 'O.R. No.', 
                    "field" => 'orNumber',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'cashOnHand', 
                    "label" => 'CASH ON HAND', 
                    "field" => 'cashOnHand',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'principal', 
                    "label" => 'PRINCIPAL LOAN', 
                    "field" => 'principal',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'interest', 
                    "label" => 'EARNED INTEREST', 
                    "field" => 'interest',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'interestPassedMonth', 
                    "label" => 'PAST DUE INTEREST', 
                    "field" => 'interestPassedMonth',
                    "align" => 'left',
                    "type" => 'Number',
                ],
            ],
        ];
        
        $query = $this->loanModel->getTenColumnReportList([
            "status" => $reportType,
            "dateFrom" => $dateFrom,
            "dateTo" => $dateTo
        ]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $fmoneyword = new \NumberFormatter("en", \NumberFormatter::SPELLOUT); 
            
            $cinfo = $value->customerInfo;
            $linfo = $value->loanInfo;
            $linfo->itemDetails = json_decode($linfo->itemDetails);
            $linfo->computationDetails = json_decode($linfo->computationDetails);
            $value->amountWord = $fmoneyword->format($linfo->loanAmount);
            $value->interestWord = $fmoneyword->format($linfo->interest);
            $pastDue = $linfo->payStatus <= 1 ? ($linfo->computationDetails->amountPercentage * $linfo->payStatus) : ($value->pastDue * $linfo->computationDetails->amountPercentage) + (($linfo->loanAmount * 0.02) * $value->pastDue);
            $earnedInterest = $linfo->payStatus <= 1 ? $linfo->computationDetails->amountPercentage : $linfo->computationDetails->amountPercentage * $linfo->terms;
            $coh = $linfo->loanAmount + $pastDue + $earnedInterest;

            $list['list'][$key] = [
                "key" => $value->id,
                "no" => $key + 1,
                "pawnerName" => $cinfo->lastName .", ". $cinfo->firstName ." ". $cinfo->suffix ." ". $cinfo->middleName,
                "date" => date("F j, Y", strtotime($linfo->createdDate)),
                "pawnTicket" => $value->oldTicketNo,
                "orNumber" => $value->officialReceipt,
                "cashOnHand" => number_format($coh, 2, '.', ','),
                "principal" => number_format($linfo->loanAmount, 2, '.', ','),
                "interest" => number_format($earnedInterest, 2, '.', ','),
                "interestPassedMonth" => number_format($pastDue, 2, '.', ','),
            ];
        }

        ob_start();
        $dompdf = new \Dompdf\Dompdf(['enable_font_subsetting' => true]); 
        $dompdf->loadHtml(view('reports/10columns', $list));
        $dompdf->setPaper(array(0, 0, 612, 936), 'landscape');
        $dompdf->render();
        $dompdf->stream('24 Column Report', ["Attachment" => false]);
    }

    public function tenColumnReport(){
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

        $list = [
            "columns" => [
                [ 
                    "name"=>'no', 
                    "label" => 'NO', 
                    "field" => 'no',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'pawnerName', 
                    "label" => 'NAME OF THE PAWNER', 
                    "field" => 'pawnerName',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'date', 
                    "label" => 'DATE', 
                    "field" => 'date',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnTicket', 
                    "label" => 'PAWN TICKET NO.', 
                    "field" => 'pawnTicket',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'orNumber', 
                    "label" => 'O.R. No.', 
                    "field" => 'orNumber',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'cashOnHand', 
                    "label" => 'CASH ON HAND', 
                    "field" => 'cashOnHand',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'principal', 
                    "label" => 'PRINCIPAL LOAN', 
                    "field" => 'principal',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'interest', 
                    "label" => 'EARNED INTEREST', 
                    "field" => 'interest',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'interestPassedMonth', 
                    "label" => 'PAST DUE INTEREST', 
                    "field" => 'interestPassedMonth',
                    "align" => 'left',
                    "type" => 'Number',
                ],
            ],
        ];
        
        $query = $this->loanModel->getTenColumnReportList([
            "status" => $payload->status,
            "dateFrom" => $payload->from,
            "dateTo" => $payload->to
        ]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $fmoneyword = new \NumberFormatter("en", \NumberFormatter::SPELLOUT); 
            $cinfo = $value->customerInfo;
            $linfo = $value->loanInfo;
            $linfo->itemDetails = json_decode($linfo->itemDetails);
            $linfo->computationDetails = json_decode($linfo->computationDetails);
            $pastDue = $linfo->payStatus <= 1 ? ($linfo->computationDetails->amountPercentage * $linfo->payStatus) : ($value->pastDue * $linfo->computationDetails->amountPercentage) + (($linfo->loanAmount * 0.02) * $value->pastDue);
            $earnedInterest = $linfo->payStatus <= 1 ? $linfo->computationDetails->amountPercentage : $linfo->computationDetails->amountPercentage * $linfo->terms;
            // $pastDue = $linfo->payStatus <= 1 ? 0 : ($linfo->computationDetails->amountPercentage * $linfo->payStatus);
            $value->amountWord = $fmoneyword->format($linfo->loanAmount);
            $value->interestWord = $fmoneyword->format($linfo->interest);
            // temporary Solution
            $coh = $linfo->loanAmount + $pastDue + $earnedInterest;

            $list['list'][$key] = [
                "key" => $value->id,
                "no" => $key + 1,
                "pawnerName" => $cinfo->lastName .", ". $cinfo->firstName ." ". $cinfo->suffix ." ". $cinfo->middleName,
                "date" => date("F j, Y", strtotime($linfo->createdDate)),
                "pawnTicket" => $value->oldTicketNo,
                "orNumber" => $value->officialReceipt,
                "cashOnHand" => $coh,
                "principal" => $linfo->loanAmount,
                "interest" => $earnedInterest,
                "interestPassedMonth" => $pastDue,
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

    // 24 Column Report
    public function generateTwentyFourCoulumn($dateFrom, $dateTo, $reportType){
        $genDate = $dateFrom === $dateTo ? 
        date("F j, Y", strtotime($dateFrom)) : 
        date("F j, Y", strtotime($dateFrom)) ." - ". date("F j, Y", strtotime($dateTo));
        $list = [
            "generatedDate" => $genDate,
            "columns" => [
                [ 
                    "name"=>'no', 
                    "label" => 'NO', 
                    "field" => 'no',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'pawnerName', 
                    "label" => 'NAME OF THE PAWNER', 
                    "field" => 'pawnerName',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'dateOfLoan', 
                    "label" => 'DATE', 
                    "field" => 'dateOfLoan',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnTicket', 
                    "label" => 'PAWN TICKET NO.', 
                    "field" => 'pawnTicket',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'serviceCharge', 
                    "label" => 'SERVICE CHARGE', 
                    "field" => 'serviceCharge',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'principal', 
                    "label" => 'PRINCIPAL LOAN', 
                    "field" => 'principal',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'redeemed', 
                    "label" => 'REDEEMED', 
                    "field" => 'redeemed',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'canceled', 
                    "label" => 'CANCELLED', 
                    "field" => 'canceled',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'spoiled', 
                    "label" => 'SPOILED', 
                    "field" => 'spoiled',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'address', 
                    "label" => 'ADDRESS OF PAWNER', 
                    "field" => 'address',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'items', 
                    "label" => 'DESCRIPTION OF THE PAWN', 
                    "field" => 'items',
                    "align" => 'left',
                    "type" => 'String',
                ],
            ],
        ];
        
        $query = $this->loanModel->getTwentyFourColumnReportList([
            "status" => $reportType,
            "dateFrom" => $dateFrom,
            "dateTo" => $dateTo
        ]);

        foreach ($query as $key => $value) {
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            $addressText = $cinfo->addressLine .", ". $cinfo->addressDetails->barangay->label || '-' .", ". $cinfo->addressDetails->city->label || '-' .", ". $cinfo->addressDetails->province->label || '-';

            $rinfo = $value->renewRecord;

            $linfo = $value->loanInfo;
            $linfo->itemDetails = json_decode($linfo->itemDetails);
            $item = [];
            foreach ($linfo->itemDetails as $ikey => $ivalue) {
                if(isset($ivalue->remarks)){
                    $item[$ikey] = $ivalue->qty ." ". $ivalue->unit->value ." ". $ivalue->type->value .", ". $ivalue->description .", ". $ivalue->weight .", ". $ivalue->property .", ". $ivalue->remarks;
                } else {
                    $item[$ikey] = $ivalue->qty ." ". $ivalue->unit->value ." ". $ivalue->type->value .", ". $ivalue->description .", ". $ivalue->weight .", ". $ivalue->property;
                }
            }
            $item = implode(" ", $item);
            
            $spoiledValue = $value->transactionType == 5 ? "ST" : "";

            $list['list'][$key] = [
                "key" => $value->id,
                "no" => $key + 1,
                "pawnerName" => $cinfo->lastName .", ". $cinfo->firstName ." ". $cinfo->suffix ." ". $cinfo->middleName,
                "pawnTicket" => $value->orNumber,
                "date" => date("F j, Y", strtotime($value->createdDate)),
                "serviceCharge" => $linfo->charge,
                "principal" => number_format($linfo->loanAmount, 2, '.', ','),
                "redeemed" => $linfo->redeemDate,
                "canceled" => count($rinfo) !== 0 && date("F j, Y", strtotime($dateFrom)) !== date("F j, Y", strtotime($rinfo[0]->createdDate)) ? date("F j, Y", strtotime($rinfo[0]->createdDate)) : "",
                "spoiled" => $spoiledValue,
                "address" => $this->truncateString($addressText, 25),
                "items" => $this->truncateString($item, 52)
            ];
        }

        ob_start();
        $dompdf = new \Dompdf\Dompdf(['enable_font_subsetting' => true]); 
        $dompdf->loadHtml(view('reports/24columns', $list));
        $dompdf->setPaper(array(0, 0, 612, 936), 'landscape');
        $dompdf->render();
        $dompdf->stream('24 Column Report', ["Attachment" => false]);
    }

    public function twentyFourCoulumnReport(){
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

        $list = [
            "columns" => [
                [ 
                    "name"=>'no', 
                    "label" => 'NO', 
                    "field" => 'no',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'pawnerName', 
                    "label" => 'NAME OF THE PAWNER', 
                    "field" => 'pawnerName',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'date', 
                    "label" => 'DATE', 
                    "field" => 'date',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnTicket', 
                    "label" => 'PAWN TICKET NO.', 
                    "field" => 'pawnTicket',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'serviceCharge', 
                    "label" => 'SERVICE CHARGE', 
                    "field" => 'serviceCharge',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'principal', 
                    "label" => 'PRINCIPAL LOAN', 
                    "field" => 'principal',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'redeemed', 
                    "label" => 'REDEEMED', 
                    "field" => 'redeemed',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'canceled', 
                    "label" => 'CANCELLED', 
                    "field" => 'canceled',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'spoiled', 
                    "label" => 'SPOILED', 
                    "field" => 'spoiled',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'address', 
                    "label" => 'ADDRESS OF PAWNER', 
                    "field" => 'address',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'items', 
                    "label" => 'DESCRIPTION OF THE PAWN', 
                    "field" => 'items',
                    "align" => 'left',
                    "type" => 'String',
                ],
            ],
        ];
        
        $query = $this->loanModel->getTwentyFourColumnReportList([
            "status" => $payload->status,
            "dateFrom" => $payload->from,
            "dateTo" => $payload->to
        ]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            $rinfo = $value->renewRecord;

            $linfo = $value->loanInfo;
            $linfo->itemDetails = json_decode($linfo->itemDetails);
            $item = [];
            foreach ($linfo->itemDetails as $ikey => $ivalue) {
                if(isset($ivalue->remarks)){
                    $item[$ikey] = $ivalue->qty ." ". $ivalue->unit->value ." ". $ivalue->type->value .", ". $ivalue->description .", ". $ivalue->weight .", ". $ivalue->property .", ". $ivalue->remarks;
                } else {
                    $item[$ikey] = $ivalue->qty ." ". $ivalue->unit->value ." ". $ivalue->type->value .", ". $ivalue->description .", ". $ivalue->weight .", ". $ivalue->property;
                }
            }
            $spoiledValue = $value->transactionType == 5 ? "ST" : "";
            
            $list['list'][$key] = [
                "key" => $value->id,
                "no" => $key + 1,
                "pawnerName" => $cinfo->lastName .", ". $cinfo->firstName ." ". $cinfo->suffix ." ". $cinfo->middleName,
                "date" => date("F j, Y", strtotime($value->createdDate)),
                "pawnTicket" => $value->orNumber,
                "serviceCharge" => $linfo->charge,
                "principal" => number_format($linfo->loanAmount, 2, '.', ','),
                "redeemed" => $linfo->redeemDate,
                "canceled" => count($rinfo) !== 0 && date("F j, Y", strtotime($payload->from)) !== date("F j, Y", strtotime($rinfo[0]->createdDate)) ? date("F j, Y", strtotime($rinfo[0]->createdDate)) : "",
                "spoiled" => $spoiledValue,
                "address" => $cinfo->addressLine .", ". $cinfo->addressDetails->barangay->label .", ". $cinfo->addressDetails->city->label .", ". $cinfo->addressDetails->province->label,
                "items" => implode(" ", $item)
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


    public function auctionList(){
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

        $list = [
            "columns" => [
                [ 
                    "name"=>'no', 
                    "label" => 'NO', 
                    "field" => 'no',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'date', 
                    "label" => 'DATE', 
                    "field" => 'date',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnTicket', 
                    "label" => 'TICKET NO.', 
                    "field" => 'pawnTicket',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnerName', 
                    "label" => 'CUSTOMER', 
                    "field" => 'pawnerName',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'category', 
                    "label" => 'CATEGORY', 
                    "field" => 'category',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'itemPawn', 
                    "label" => 'ITEM', 
                    "field" => 'itemPawn',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'principal', 
                    "label" => 'PRINCIPAL', 
                    "field" => 'principal',
                    "align" => 'left',
                    "type" => 'String',
                ],
            ],
        ];
        
        $query = $this->auctionModel->getAuctionListReport([
            "status" => $payload->status,
            "dateFrom" => $payload->from,
            "dateTo" => $payload->to
        ]);
        // print_r($query);
        // exit;

        foreach ($query as $key => $value) {
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            // $value->catInfo = json_decode($value->categoryInfo);
            
            $list['list'][$key] = [
                "key" => $value->id,
                "no" => $key + 1,
                "pawnTicket" => $value->orNumber,
                "date" => date("F j, Y", strtotime($value->createdDate)),
                "pawnerName" => $cinfo->lastName .", ". $cinfo->firstName ." ". $cinfo->suffix ." ". $cinfo->middleName,
                "category" => $value->categoryInfo->label,
                "itemPawn" => $value->itemInfo,
                "principal" => $value->principal,
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

    public function generateAuctionList($datefrom, $dateto){
        $list = [
            "generatedDate" => date("F j, Y", strtotime($datefrom)),
            "columns" => [
                [ 
                    "name"=>'no', 
                    "label" => 'NO', 
                    "field" => 'no',
                    "align" => 'left',
                    "type" => 'Number',
                ],
                [ 
                    "name"=>'date', 
                    "label" => 'DATE', 
                    "field" => 'date',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnTicket', 
                    "label" => 'TICKET NO.', 
                    "field" => 'pawnTicket',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'pawnerName', 
                    "label" => 'CUSTOMER', 
                    "field" => 'pawnerName',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'category', 
                    "label" => 'CATEGORY', 
                    "field" => 'category',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'itemPawn', 
                    "label" => 'ITEM', 
                    "field" => 'itemPawn',
                    "align" => 'left',
                    "type" => 'String',
                ],
                [ 
                    "name"=>'principal', 
                    "label" => 'PRINCIPAL', 
                    "field" => 'principal',
                    "align" => 'left',
                    "type" => 'String',
                ],
            ],
        ];
        
        $query = $this->auctionModel->getAuctionListReport([
            "dateFrom" => $datefrom,
            "dateTo" => $dateto
        ]);

        foreach ($query as $key => $value) {
            $cinfo = $value->customerInfo;
            $cinfo->addressDetails = json_decode($cinfo->addressDetails);
            
            $list['list'][$key] = [
                "key" => $value->id,
                "no" => $key + 1,
                "pawnTicket" => $value->orNumber,
                "date" => date("F j, Y", strtotime($value->loanDate)),
                "pawnerName" => $cinfo->lastName .", ". $cinfo->firstName ." ". $cinfo->suffix ." ". $cinfo->middleName,
                "category" => $value->categoryInfo->label,
                "itemPawn" => $value->itemInfo,
                "principal" => $value->principal,
            ];
        }

        ob_start();
        $dompdf = new \Dompdf\Dompdf(['enable_font_subsetting' => true]); 
        $dompdf->loadHtml(view('reports/auctionList', $list));
        $dompdf->setPaper(array(0, 0, 612, 936), 'landscape');
        $dompdf->render();
        $dompdf->stream('Auction List', ["Attachment" => false]);
    }

    public function truncateString($string, $length, $ellipsis = '...') {
        if (strlen($string) <= $length) {
            return $string;
        }
    
        $truncatedString = substr($string, 0, $length);
        $lastSpace = strrpos($truncatedString, ' ');
    
        if ($lastSpace !== false) {
            $truncatedString = substr($truncatedString, 0, $lastSpace);
        }
    
        return $truncatedString . $ellipsis;
    }
}