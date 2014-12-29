@section('content')


  <div class="container">
  <h4>Displaying report for: <span class="report-date">{{$reportDate}}</span></h4>
    <div class="date-picker">
      Choose date <input class="datepicker" data-date-format="mm/dd/yyyy">
      <a href="#" class="btn btn-primary filter-by-date has-spinner" data-user="{{$user}}">
        <span class="spinner"><i class="glyphicon glyphicon-refresh icon-refresh"></i></span>
        Filter
      </a>
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
          if(!date){
            return;
          }
          
          var icon = $( this ).find( ".icon-refresh" ),
              animateClass = "icon-refresh-animate";
          icon.addClass(animateClass);

          var date = $('.datepicker').val();
          var email = $('.filter-by-date').data('user');
          $.ajax({
            type: 'GET',
            url: '/user?email=' + email + '&date=' + date,
            success: function(data){
              $('.usage-data').html(data);
              $('.report-date').html(date);
              icon.removeClass( animateClass );
              // icon.addClass('hide');
            }
          });
        });

      });    
    </script>
  @stop

@stop