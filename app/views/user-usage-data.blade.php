@foreach($usageReports as $index=>$dailyUsage)
  <tr>
  <td>{{$reportDates[$index]}}</td>
  @foreach($dailyUsage as $usage)
    <?php $keys = array_keys($usage); ?>
    <td> 
     {{$usage[$keys[1]]}}
    </td>
  @endforeach
  </tr>
@endforeach