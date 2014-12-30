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

    if($currentTime < $token->getEndOfLife()){
      return $this->getUsersData();
    } 
    // token expired, request new one
    Session::forget('token');
    if(Request::ajax()){
      return Response::json(array('token_expired' => true));
    } else {
      return Redirect::to('/');
    }
  }

  private function getUsersData(){
    $userEmail = Input::get('email');
    $specificDate = Input::get('date');
    $previousDate = Input::get('previous-date');

    $reportDates = array();
    $usageData = array();

    if(!empty($specificDate)){
      $reportDate = date('Y-m-d', strtotime($specificDate));
      $reportDates[] = $reportDate;
      $usageData[] = $this->getDataFromGoogle($userEmail, $reportDate);
    } else if(!empty($previousDate)){
      for ($i=1; $i<8; $i++) {
        $reportDate = date('Y-m-d', strtotime($previousDate . '-' . $i . ' days'));
        $reportDates[] = $reportDate;
        $usageData[] = $this->getDataFromGoogle($userEmail, $reportDate);
      }
    } else {
      // because of Google, we can't fetch from yesterday, but two days before
      for ($i=2; $i<9; $i++) {
        $reportDate = date('Y-m-d', strtotime('-' . $i . ' days'));
        $reportDates[] = $reportDate;
        $usageData[] = $this->getDataFromGoogle($userEmail, $reportDate);
      }
    }

    if(!empty($specificDate) || !empty($previousDate)){
      return View::make('user-usage-data', array(
        'reportDates' => $reportDates,
        'user' => $userEmail,
        'usageReports' => $usageData
      ));
    } else {
      $this->layout->content = View::make('user-activity', array(
        'reportDates' => $reportDates,
        'user' => $userEmail,
        'usageReports' => $usageData
      ));      
    }
  }

  private function getDataFromGoogle($email, $date){
   
    $consumer = $this->buildConsumer();
    // Send a request with it
    $result = json_decode( $consumer->request('https://www.googleapis.com/admin/reports/v1/usage/users/' . $email . '/dates/' . $date . '?'  
      . 'parameters=gmail:num_emails_exchanged,'
      . 'gmail:num_emails_received,'
      . 'gmail:num_emails_sent,'
      . 'gmail:num_spam_emails_received'), true);
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

}
