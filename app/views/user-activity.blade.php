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
        <tr class="usage-data">
          @include('user-usage-data')
        </tr>
      </tbody>
    </table>
  </div>

  @section('js')
    <script type="text/javascript">
      $('document').ready(function(){
        $('.datepicker').datepicker({
          endDate: '-2d'
        });

        $('.filter-by-date').click(function(e){
          e.preventDefault();
          var date = $('.datepicker').val();
          var email = $('.filter-by-date').data('user');
          $.ajax({
            type: 'POST',
            url: '/user?email=' + email + '&date=' + date,
            success: function(data){
              $('.usage-data').html(data);
            }
          });
        });

      });    
    </script>
  @stop

@stop