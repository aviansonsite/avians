
<!DOCTYPE html>
<html>
<style>
	@page {
             margin: 10px 10px;
    }
	table, td, div {
	  border:1px solid black;
	  border-collapse: collapse;
	  width:100%;
	  border-color: #e8e9eb;
	  padding: 3px;
    word-break: normal;
	}
	body{
		font-family: sans-serif;
		font-size: 10px;
	}

	th{
		
		border:1px solid black;
		border-collapse: collapse;
		width:100%;
  	border-color: #e8e9eb;
  	padding: 5px;
  	text-align:left; 
    white-space: nowrap;
	}
</style>
<body>
    <p style="text-align:center; font-size: 14px;margin-top: 1px;">Technician Attendance Report</p>
    <!-- TAX INVOICE DETAILS HEADER -->
    <table style="margin-top: 0px;margin-bottom: 0px;">
        <tr>

            <td colspan="6" style="color: rgb(51,51,51);border-left: none; width: 150%; text: center">

                <h2 style='text-align:center;font-size:12px;line-height:12px;margin:5px;padding:0;'>
                Avians Innovation Technology Pvt. Ltd
                </h2>
            </td>
           
        </tr>
        @foreach($u_obj as $uo)
        <tr>       
            <td style="width:80px;"> Technician Name : <br/> <b> {{$uo->name}} </b></td>
            <td style="width:30px;"> Mobile No.: <br/> <b> {{$uo->mobile}} </b></td>
            
            <td style="width:30px;"> From Date : <br/> <b> {{$from_date}} </b></td>
            <td style="width:30px;"> To Date : <br/> <b> {{$to_date}} </b></td>
            <td style="width:30px;"> Total Days : <br/> <b>{{$totalDays}}</b></td>
            <td style="width:30px;"> Total Hours : <br/> <b>{{$totalHours}}</b></td>
        </tr>
        @endforeach
       
    </table>

    <table style='margin-top: 0px;'>
        <tr>
            <th  style="width:60px;text-align:center;">Date</th>
            <th  style="width:60px;white-space:wrap;text-align:center;">OA No</th>
            <th  style="width:150px;text-align:center;">Project Name</th>
            <th  style="width:60px;white-space:wrap;text-align:center;">Punch In <br>(HH:MM:SS)</th>
            <th  style="width:60px;white-space:wrap;text-align:center;">Punch Out <br>(HH:MM:SS)</th>
            <th  style="width:60px;white-space:wrap;text-align:center;">Total Time <br>(HH:MM:SS)</th>
            <th  style="text-align:center;">Remark</th>
        </tr>

        @foreach($p_obj as $pd)
      
            @if($pd->pin_date != null)
                <?php $pin_date = date('d-m-Y', strtotime($pd->pin_date)); ?>
                <tr style="border-bottom: none; border-top: none;">
                
                    <td style="width: ;text-align:center;">{{$pin_date}}</td>
                    <td style="width: ;text-align:center;">{{$pd->so_number}}</td>
                    <td style="width: ;text-align:center;">{{$pd->project_name}}</td>
                    <td style="width: ;text-align:center;">{{$pd->pin_time}}</td>

                    @if($pd->created_at == $pd->updated_at)
                        <td style="text-align:center;"> - </td>
                    @else
                        <td style="width: ;text-align:center;">{{$pd->pout_time}}</td>
                    @endif
                    
                    <td style="width: ;text-align:center;">{{$pd->totalDuration}}</td>

                    @if($pd->regular_remark != null)
                        <td style="width: ;text-align:center;">{{$pd->regular_remark}}</td>
                    @else
                        <td style="text-align:center;"> - </td>
                    @endif 
                    
                </tr>
            @else

                @if($pd->pin_date != null)
                    <?php $date = date('d-m-Y', strtotime($pd->pin_date)); ?>
                @else
                    <?php $date = date('d-m-Y', strtotime($pd->pout_date)); ?>
                @endif

                <tr style="border-bottom: none; border-top: none;">
                    <td style="width: ;text-align:center;">{{$date}}</td>
                    <td style="width: ;text-align:center;">{{$pd->so_number}}</td>
                    <td style="width: ;text-align:center;">{{$pd->project_name}}</td>

                    @if($pd->created_at == $pd->updated_at)
                        <td style="text-align:center;"> - </td>
                    @else
                        <td style="width: ;text-align:center;">{{$pd->pin_time}}</td>
                    @endif
                    
                    <td style="width: ;text-align:center;">{{$pd->pout_time}}</td>
                    <td style="width: ;text-align:center;">{{$pd->totalDuration}}</td>

                    @if($pd->regular_remark != null)
                        <td style="width: ;text-align:center;">{{$pd->regular_remark}}</td>
                    @else
                        <td style="text-align:center;"> - </td>
                    @endif 
                    
                </tr>
            @endif
        @endforeach
       
        <tr style="border-bottom: none; border-top: none;">
            <td colspan="4" style="text-align: right; font-style: normal;"></td>
            <td  colspan="3"style="text-align:right;">
                <b style="vertical-align: text-top;">For Avians Innovation Technology Pvt. Ltd </b>
                <br/><br/><br/><br/><br/> 
                <small>( Authorised Signatory )</small>
            </td>
        </tr>

    </table>
    <p style="text-align:center;font-size: 9px;">( This is computer generated Attendance Report Statement. )</p>  
</body>
</html>