@section('content')


  <div class="container">
    <h4>Displaying report for: <strong>{{$user}}</strong> for period 
      <span class="report-date">
        <em class="last-day">{{$reportDates[6]}}</em> - <em>{{$reportDates[0]}}</em>
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
          @include('user-usage-data', array('reportDates' => $reportDates))
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
              var prevPeriodFirstDay = new Date(date);
              var prevPeriodLastDay = new Date(date);

              var firstDay= prevPeriodFirstDay.setDate(prevPeriodFirstDay.getDate() - 7);
              var formatFirstDay = new Date(firstDay);
              var prevPerFirstDateYear = formatFirstDay.getFullYear();
              var prevPerFirstDateMonth = formatFirstDay.getMonth() + 1;
              var prevPerFirstDateDay = formatFirstDay.getDate();

              var lastDay = prevPeriodLastDay.setDate(prevPeriodLastDay.getDate() - 1);
              var formatLastDay = new Date(lastDay);
              var prevPerLastDateYear = formatLastDay.getFullYear();
              var prevPerLastDateMonth = formatLastDay.getMonth() + 1;
              var prevPerLastDateDay = formatLastDay.getDate();

              $('.report-date').html('<em class="last-day">' + prevPerFirstDateYear + '-' + prevPerFirstDateMonth + '-' + prevPerFirstDateDay + '</em> - <em>' +  prevPerLastDateYear + '-' + prevPerLastDateMonth + '-' + prevPerLastDateDay    + '</em>');
              icon.removeClass( animateClass );
            }
          });
        });

      });    
    </script>
  @stop

@stop