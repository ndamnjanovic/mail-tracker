@section('content')


  <div class="container">
    <h4>Displaying report for: {{$reportDate}}</h4>
    <div class="date-picker">
      Choose date <input class="datepicker" data-date-format="mm/dd/yyyy">
      <a href="#" class="btn btn-primary filter-by-date" data-user="{{$user}}">Filter</a>
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>User</th>
          <th>Num of emails exchanged</th>
          <th>Num of emails received</th>
          <th>Num of emails sent</th>
          <th>Num of spam emails received</th>
          <th>Last access time</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{{$user}}</td>
          @foreach($usageReports as $usage)
            <?php $keys = array_keys($usage); ?>
            <td> 
              @if(strpos($keys[1], 'time') !== false)
                {{date('Y-m-d H:i', strtotime($usage[$keys[1]]))}}
              @else 
                {{$usage[$keys[1]]}}
              @endif
            </td>
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>

  @section('js')
    <script type="text/javascript">
      $('document').ready(function(){
        $('.datepicker').datepicker();

        $('.filter-by-date').click(function(e){
          e.preventDefault();
          var date = $('.datepicker').val();
          var email = $('.filter-by-date').data('user');
          $.ajax({
            type: 'POST',
            url: '/user?email=' + email + '&date=' + date,
            success: function(){
              
            }
          });
        });

      });    
    </script>
  @stop

@stop