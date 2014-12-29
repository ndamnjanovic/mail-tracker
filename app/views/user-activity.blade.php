@section('content')


  <div class="container">
    <h4>Displaying report for: <strong>{{$user}}</strong> for period 
      <span class="report-date">
        <em class="last-day">{{$reportsDate[6]}}</em> - <em>{{$reportsDate[0]}}</em>
      </span>
    </h4>
    <div class="date-picker">
      Choose specific date <input class="datepicker" data-date-format="mm/dd/yyyy">
      <a href="#" class="btn btn-primary filter-by-date has-spinner" data-user="{{$user}}">
        <span class="spinner"><i class="glyphicon glyphicon-refresh icon-refresh"></i></span>
        Filter
      </a>
      OR
      <a href="#" class="btn btn-primary previous-seven-days">
        <span class="spinner"><i class="glyphicon glyphicon-refresh previous-icon-refresh"></i></span>
        Previous 7 days
      </a>
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Date</th>
          <th>Num of emails exchanged</th>
          <th>Num of emails received</th>
          <th>Num of emails sent</th>
          <th>Num of spam emails received</th>
        </tr>
      </thead>
      <tbody class="table-body">
        {{--<tr class="usage-data">--}}
          @include('user-usage-data', array('reportsDate' => $reportsDate))
        {{--</tr>--}}
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
              $('.table-body').html(data);
              $('.report-date').html('<em class="last-day">' + date + '</em>');
              icon.removeClass( animateClass );
              // icon.addClass('hide');
            }
          });
        });

        $('.previous-seven-days').click(function(e){
          var date = $('.last-day').html();
          var icon = $( this ).find( ".previous-icon-refresh" ),
              animateClass = "icon-refresh-animate";
          icon.addClass(animateClass);

          var email = $('.filter-by-date').data('user');
          $.ajax({
            type: 'GET',
            url: '/user?email=' + email + '&previous-date=' + date,
            success: function(data){
              $('.table-body').html(data);
              var prevDay = new Date(date);
              var prevDayCopy = new Date(date);
              var newLastDay= prevDay.setDate(prevDay.getDate() - 7);
              var formatNewLastDay = new Date(newLastDay);
              var year = formatNewLastDay.getFullYear();
              var month = formatNewLastDay.getMonth() + 1;
              var day = formatNewLastDay.getDate();

              var newLastDay1 = prevDayCopy.setDate(prevDayCopy.getDate() - 1);
              var formatNewLastDay1 = new Date(newLastDay1);
              var year1 = formatNewLastDay1.getFullYear();
              var month1 = formatNewLastDay1.getMonth() + 1;
              var day1 = formatNewLastDay1.getDate();

              $('.report-date').html('<em class="last-day">' + year + '-' + month + '-' + day + '</em> - <em>' +  year1 + '-' + month1 + '-' + day1    + '</em>');
              icon.removeClass( animateClass );
              // icon.addClass('hide');
            }
          });
        });

      });    
    </script>
  @stop

@stop