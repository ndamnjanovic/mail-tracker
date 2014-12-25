<?php

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

  public function showLogin(){
    $this->layout->content = View::make('login');
  }

  public function doLogin(){
    if (Auth::attempt(Input::only('email', 'password'))) {
      return Redirect::to('/index');
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
    
    if(!empty($user->token)){
      return $this->getUsersData($user->token);
    } else if(Input::get('code')){
      return $this->saveGoogleToken();
    } else {
      return $this->getGoogleToken();
    }
  }

  public function getUsersData($token){
    // TODO create googleservice as dependency
    $googleService = OAuth::consumer( 'Google' );
    $token = $googleService->requestAccessToken( $token );
    // Send a request with it
    $result = json_decode( $googleService->request( 'https://www.googleapis.com/admin/reports/v1/usage/users/ned@sharpeak.com/dates/2014-12-23' ), true );
    var_dump($result);
  }

  public function getGoogleToken(){
    // TODO create googleservice as dependency
    $googleService = OAuth::consumer( 'Google' );
    // get googleService authorization
    $url = $googleService->getAuthorizationUri();
    // return to google login url
    return Redirect::to( (string)$url );
  }

  public function saveGoogleToken(){
    $token = Input::get('code');
    Auth::user()->update(array('token', $token));
    return $this->getUsersData($token);
  }

}
