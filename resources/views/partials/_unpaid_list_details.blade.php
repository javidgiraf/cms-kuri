  @if(count($current_plan_history['result_dates']) > 0)
  @foreach($current_plan_history['result_dates'] as $result_date)

  @if($result_date['status']=='0'||$result_date['status']=='2')
  <tr id="tableRow_{{$result_date['date']}}">
    <th scope="row">
      <input type="checkbox" id="{{$result_date['date']}}" class='permission'>

    </th>
    <td id="date{{$result_date['date']}}">{{$result_date['date']}}</td>
    <td id="amount{{$result_date['date']}}">
      @if($result_date['amount'] == null) 
        {{ $result_date['amount'] }}
      @else 
        <input type="text" name="amount" class="form-control"> 
      @endif
    </td>



  </tr>
  @endif
  @endforeach
  @else
    <tr>
      <td>No Records available in table</td>
    </tr>
  @endif