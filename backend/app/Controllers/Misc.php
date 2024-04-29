<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\MiscModel;
use App\Models\SeriesModel;
use App\Models\SettingsModel;

class Misc extends BaseController
{

    public function __construct(){
        $this->miscModel = new MiscModel();
        $this->seriesModel = new SeriesModel();
        $this->settingsModel = new SettingsModel();
    }

    public function getSettings(){
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

        //Select Query for finding User Information
        $settingList = $this->settingsModel->getSettingValues();

        //Set Api Response return to the FE
        if($settingList){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($settingList));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }   

    public function getUserTypes(){
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

        //Select Query for finding User Information
        $categories = [];
        $categories['list'] = $this->miscModel->getTypeList();

        //Set Api Response return to the FE
        if($categories){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($categories));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }   

    public function getCategories(){
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

        //Select Query for finding User Information
        $categories = [];
        $categories['list'] = $this->miscModel->getCatList();

        //Set Api Response return to the FE
        if($categories){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($categories));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    } 

    public function getUnits(){
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

        //Select Query for finding User Information
        $categories = [];
        $categories['list'] = $this->miscModel->getUnitList();

        //Set Api Response return to the FE
        if($categories){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($categories));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }   

    public function getBranches(){
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

        //Select Query for finding User Information
        $unitareas = [];
        $unitareas['list'] = $this->miscModel->getBranchList();

        //Set Api Response return to the FE
        if($unitareas){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($unitareas));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }  

    public function getDateSeriesList(){
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

        $payload = $this->request->getJSON();

        //Select Query for finding User Information
        $where = [
            // "seriesDate" => $payload->date,
            "status" => 1,
        ];
        $list = [];
        $list['list'] = $this->seriesModel->getSeriesListWithUser($where);

        //Set Api Response return to the FE
        if($list){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }  

    public function setDailySeries(){
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
        
        $payload = $this->request->getJSON();
        $query = $this->seriesModel->insert($payload);

        //Set Api Response return to the FE
        if($query){
            $response = [
                'title' => 'Registration Complete',
                'message' => 'User referenece has been successfully submitted.'
            ];
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'error' => 400,
                'message' => 'There is something wrong in registering series'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
    }   

    public function getDailySeries(){
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

        $payload = $this->request->getJSON();

        //Select Query for finding User Information
        $where = [
            "userId" => $payload->userId,
            "status" => 1,
        ];
        $query = $this->seriesModel->getSeriesInfo($where);

        //Set Api Response return to the FE
        if($query){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
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

    public function getAdminUserSeries(){
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

        $payload = $this->request->getJSON();

        //Select Query for finding User Information
        $where = [
            "status" => 1,
        ];
        $query = $this->seriesModel->getSeriesListWithUser($where);

        //Set Api Response return to the FE
        if($query){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
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

    public function updateSeries(){
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

        $payload = $this->request->getJSON();

        //Select Query for finding User Information
        $where = [
            "id" => $payload->sid,
        ];
        $data = [
            "start" => $payload->start
        ];
        $query = $this->seriesModel->updateSeriesInfo($where, $data);

        //Set Api Response return to the FE
        if($query){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
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
    public function updateSettings(){
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

        $payload = $this->request->getJSON();

        //Select Query for finding User Information
        $where = [
            "settingName" => $payload->settingName,
        ];
        $data = [
            "value" => $payload->settingValue
        ];
        $query = $this->settingsModel->updateSettingInfo($where, $data);

        //Set Api Response return to the FE
        if($query){
            //Update
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($query));
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

}