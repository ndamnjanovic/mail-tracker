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