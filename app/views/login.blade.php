@section('content')

<div class="container">

  @if(Session::get('error'))
    <div class="flash-error alert alert-danger" role="alert">
      {{Session::get('error')}}
    </div>
  @endif

  {{Form::open(array('url' => '/login', 'method' => 'post', 'class' => 'form-signin'))}}
    <h2 class="form-signin-heading">Please log in</h2>
    {{Form::label('inputEmail', 'Email address', array('class' => 'sr-only'))}}
    {{Form::text('email', Input::old('email'), array('class' => 'form-control', 'placeholder' => 'Email address'))}}
    
    {{Form::label('inputPassword', 'Password', array('class' => 'sr-only'))}}
    {{Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password'))}}
    
    <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
  {{Form::close()}}

</div> <!-- /container -->
@stop