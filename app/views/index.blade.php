@section('content')
<div class="container">
  <table class="table table-striped users-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Email</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $index=>$user)
        <tr>
          <th>{{$index+1}}</th>
          <td>{{$user->email}}</td>
          <td><a class="btn btn-primary" href="/user?email={{$user->email}}">Details</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@stop