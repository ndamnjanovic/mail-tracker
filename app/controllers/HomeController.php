<?php

use OAuth\OAuth2\Token\StdOAuth2Token;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

  protected $layout = 'layouts.master';

  public function index(){
    if (Auth:: check()) {
      return $this->showUsers();
    } else {
      return $this->showLogin();
    }
  }

  public function showLogin(){
    $this->layout->content = View::make('login');
  }

  public function doLogin(){
    if (Auth::attempt(Input::only('email', 'password'))) {
      return Redirect::to('/');
    } else {
      return Redirect::to('/')->with('error', 'Invalid email/password combination')->withInput();
    }
  }

  public function showUsers(){
    $users = User::all();
    $this->layout->content = View::make('index', array('users' => $users));
  }

  public function showUser(){

    $user = Auth::user();

    Session::put('email_to_check', Input::get('email'));
    
    if(!empty($user->access_token)){
      return $this->getUsersData($user, $user->access_token);
    } else if(Input::get('code')){
      return $this->saveGoogleToken($user);
    } else {
      return $this->getGoogleToken();
    }
  }

  public function getUsersData($user, $token){

    $userEmail = Session::get('email_to_check');
    $reportDate = date('Y-m-d', strtotime('-2 days'));

    $this->layout->content = $this->getDataFromGoogle($user, $userEmail, $reportDate);
  }

  public function filterDataByDate(){
    $user = Auth::user();

    $userEmail = Input::get('email');
    $reportDate = date('Y-m-d', strtotime(Input::get('date')));

    return $this->getDataFromGoogle($user, $userEmail, $reportDate);
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
    return View::make('user-activity', array(
      'reportDate' => $date,
      'user' => $email,
      'usageReports' => $result['usageReports'][0]['parameters']
    ));
  }

  public function getGoogleToken(){
    $googleService = OAuth::consumer('Google');
    // get googleService authorization
    $url = $googleService->getAuthorizationUri();
    // return to google login url
    return Redirect::to( (string)$url );
  }

  public function saveGoogleToken($user){
    $code = Input::get('code');
    $googleService = OAuth::consumer('Google');
    $token = $googleService->requestAccessToken($code);
    $user->update(array(
      'access_token' => $token->getAccessToken()
    ));
    return Redirect::to('/');//$this->getUsersData($user, $token);
  }

  private function buildConsumer($user){
    $token = new StdOAuth2Token($user->access_token);
    $consumer = OAuth::consumer('Google');
    $consumer->getStorage()->storeAccessToken("Google", $token);
    return $consumer;
  }

}
