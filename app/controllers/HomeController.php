<?php

use OAuth\OAuth2\Token\StdOAuth2Token;

class HomeController extends BaseController {

  protected $layout = 'layouts.master';

  public function index(){
    if (Auth:: check()) {
      return $this->showUsers();
    } else {
      return $this->showLogin();
    }
  }

  private function showLogin(){
    $this->layout->content = View::make('login');
  }

  public function doLogin(){
    if (Auth::attempt(Input::only('email', 'password'))) {
      return Redirect::to('/');
    } else {
      return Redirect::to('/')->with('error', 'Invalid email/password combination')->withInput();
    }
  }

  public function logout(){
    Auth::logout();
    return Redirect::to('/');
  }

  private function showUsers(){
    $users = User::all();
    $this->layout->content = View::make('index', array('users' => $users));
  }

  public function showUser(){

    $user = Auth::user();
  
    if(!empty($user->access_token)){
      return $this->getUsersData($user, $user->access_token);
    } else if(Input::get('code')){
      return $this->saveGoogleToken($user);
    } else {
      return $this->getGoogleToken();
    }
  }

  private function getUsersData($user, $token){

    $userEmail = Input::get('email');

    if(!empty(Input::get('date'))){
      $reportDate = date('Y-m-d', strtotime(Input::get('date')));
    } else{
      $reportDate = date('Y-m-d', strtotime('-2 days'));      
    }

    $usageData = $this->getDataFromGoogle($user, $userEmail, $reportDate);
    if(!empty(Input::get('date'))){
      return View::make('user-usage-data', array(
        'user' => $userEmail,
        'usageReports' => $usageData
      ));
    } else {
      $this->layout->content = View::make('user-activity', array(
        'reportDate' => $reportDate,
        'user' => $userEmail,
        'usageReports' => $usageData
      ));      
    }
  }

  private function getDataFromGoogle($user, $email, $date){
   
    $consumer = $this->buildConsumer($user);
    // Send a request with it
    $result = json_decode( $consumer->request('https://www.googleapis.com/admin/reports/v1/usage/users/' . $email . '/dates/' . $date . '?'  
      . 'parameters=gmail:num_emails_exchanged,'
      . 'gmail:num_emails_received,'
      . 'gmail:num_emails_sent,'
      . 'gmail:num_spam_emails_received,'
      . 'gmail:last_access_time'), true);
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

  private function saveGoogleToken($user){
    $code = Input::get('code');
    $googleService = OAuth::consumer('Google');
    $token = $googleService->requestAccessToken($code);
    $user->update(array(
      'access_token' => $token->getAccessToken(),
      'refresh_token' => $token->getRefreshToken()
    ));
    return Redirect::to('/');//$this->getUsersData($user, $token);
  }

  private function buildConsumer($user){
    $token = new StdOAuth2Token($user->access_token);
    if($token->isExpired()){
      $token->setRefreshToken($user->refresh_token);      
    }
    $consumer = OAuth::consumer('Google');
    $consumer->getStorage()->storeAccessToken("Google", $token);
    return $consumer;
  }

}
