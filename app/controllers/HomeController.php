<?php

use OAuth\OAuth2\Token\StdOAuth2Token;

class HomeController extends BaseController {

  protected $layout = 'layouts.master';

  public function index(){
    $token = Session::get('token');
    if (!empty($token)) {
      return $this->showUsers();
    } else if(Input::get('code')){
      return $this->saveGoogleToken();
    } else {
      return $this->showLogin();
    }
  }

  private function showLogin(){
    return $this->getGoogleToken();
  }

  private function showUsers(){
    $this->clearSessionData();
    $users = $this->getUsers();
    $this->layout->content = View::make('index', array('users' => $users));
  }

  private function getUsers(){
    $consumer = $this->buildConsumer();
    // Send a request with it
    $result = json_decode( $consumer->request('https://www.googleapis.com/admin/reports/v1/usage/users/all/dates/2014-12-24'), true);
    $users = array_pluck(array_pluck($result['usageReports'], 'entity'), 'userEmail');
    return $users;
  }

  public function showUser(){
    $token = Session::get('token');
    $currentTime = time();

    // check if token expired
    if($currentTime < $token->getEndOfLife()){

      $userEmail = Input::get('email');
      $specificDate = Input::get('date');
      $previousDate = Input::get('previous-date');

      if(empty($specificDate)){
        if(empty($previousDate)){
          $previousDate = date('Y-m-d', strtotime('-1 day'));
        }
        return $this->getUsersDataForPeriod($userEmail, $previousDate);
      } else {
        return $this->getUsersDataForDay($userEmail, $specificDate);        
      }

    } 

    // token expired, request new one
    Session::forget('token');
    if(Request::ajax()){
      return Response::json(array('token_expired' => true));
    } else {
      return Redirect::to('/');
    }
  }

  private function getUsersDataForPeriod($userEmail, $tillDate){
    
    $reportDates = array();
    $usageData = array();

    for ($i=1; $i<8; $i++) {
      $reportDate = date('Y-m-d', strtotime($tillDate . '-' . $i . ' days'));
      $reportDates[] = $reportDate;
      $usageData[] = $this->getDataFromGoogle($userEmail, $reportDate);
    }
    return $this->prepareViewAndHandleSessionData($userEmail, $reportDates, $usageData);
  }

  private function prepareViewAndHandleSessionData($userEmail, $reportDates, $usageData){

    $viewData = $this->handleSessionData($usageData, $reportDates);
    
    if(Request::ajax()){
      return View::make('user-usage-data', array(
        'reportDates' => $viewData['reportDates'],
        'user' => $userEmail,
        'usageReports' => $viewData['usageData']
      ));
    } else {
      $this->layout->content = View::make('user-activity', array(
        'reportDates' => $viewData['reportDates'],
        'user' => $userEmail,
        'usageReports' => $viewData['usageData']
      ));      
    }
  }

  private function getUsersDataForDay($userEmail, $specificDate){

    $reportDates = array();
    $usageData = array();

    $this->clearSessionData();
    
    $reportDate = date('Y-m-d', strtotime($specificDate));
    $reportDates[] = $reportDate;
    $usageData[] = $this->getDataFromGoogle($userEmail, $reportDate);
    
    Session::put('usageData', $usageData);
    Session::put('reportDates', $reportDates);

    return View::make('user-usage-data', array(
      'reportDates' => $reportDates,
      'user' => $userEmail,
      'usageReports' => $usageData
    ));
  }

  private function getDataFromGoogle($email, $date){
   
    $consumer = $this->buildConsumer();
    // Send a request with it
    $result = json_decode( $consumer->request('https://www.googleapis.com/admin/reports/v1/usage/users/' . $email . '/dates/' . $date . '?'  
      . 'parameters=gmail:num_emails_exchanged,'
      . 'gmail:num_emails_received,'
      . 'gmail:num_emails_sent'), true);
    return $result['usageReports'][0]['parameters'];
  }

  private function getGoogleToken(){
    $googleService = OAuth::consumer('Google');
    $googleService->setAccessType('offline');
    // get googleService authorization
    $url = $googleService->getAuthorizationUri();
    // return to google login url
    return Redirect::to( (string)$url );
  }

  private function saveGoogleToken(){
    $code = Input::get('code');
    $googleService = OAuth::consumer('Google');
    $token = $googleService->requestAccessToken($code);
    Session::put('token', $token);
    return Redirect::to('/');
  }

  private function buildConsumer(){
    $token = new StdOAuth2Token(Session::get('token')->getAccessToken());
    $consumer = OAuth::consumer('Google');
    $consumer->getStorage()->storeAccessToken("Google", $token);
    return $consumer;
  }

  private function handleSessionData($usageData, $reportDates){
    $sessionUsageData = Session::get('usageData');
    $previousUsageData = ($sessionUsageData) ? $sessionUsageData : array();
    $usageData = array_merge($previousUsageData, $usageData);

    $sessionReportDates = Session::get('reportDates');
    $previousReportDates = ($sessionReportDates) ? $sessionReportDates : array();
    $reportDates = array_merge($previousReportDates, $reportDates);

    Session::put('usageData', $usageData);
    Session::put('reportDates', $reportDates);

    return array('usageData' => $usageData, 'reportDates' => $reportDates);
  }

  private function clearSessionData(){
    Session::forget('usageData');
    Session::forget('reportDates');
  }

}
