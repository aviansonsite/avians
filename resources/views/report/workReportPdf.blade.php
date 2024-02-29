
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
    <p style="text-align:center; font-size: 14px;margin-top: 1px;">Technician Work Report</p>
    <!-- TAX INVOICE DETAILS HEADER -->
    <table style="margin-top: 0px;margin-bottom: 0px;">
        <tr>

            <td colspan="5" style="color: rgb(51,51,51);border-left: none; width: 150%; text: center">

                <h2 style='text-align:center;font-size:12px;line-height:12px;margin:5px;padding:0;'>
                Avians Innovation Technology Pvt. Ltd
                </h2>
            </td>
           
        </tr>
        @foreach($u_obj1 as $uo)
        <?php $adv_amnt = $uo->adv_amnt?>
        <tr>       
            <td style="width:80px;"> Technician Name.: <br/> <b> {{$uo->name}} </b></td>
            <td style="width:30px;"> Mobile No.: <br/> <b> {{$uo->mobile}} </b></td>
            
            <td style="width:30px;"> From Date.: <br/> <b> {{$uo->from_date}} </b></td>
            <td style="width:30px;"> To Date.: <br/> <b> {{$uo->to_date}} </b></td>
            <td style="width:30px;">  <br/> <b></b></td>
        </tr>
        @endforeach
       
    </table>

    <table style='margin-top: 0px;'>
        <tr>
            <th  style="width:60px;text-align:center;">Date</th>
            <th  style="width:60px;white-space:wrap;text-align:center;">No Of People</th>
            <th  style="width:60px;white-space:wrap;text-align:center;">OA No</th>
            <th  style="width:60px;white-space:wrap;text-align:center;">Project Name</th>
            <th  style="text-align:center;">Work Description</th>
            <th  style="width:30px;text-align:center;">Attachment</th>
        </tr>
      <?php  $n=7?>
      <?php $j=$k= 0;?>
        @foreach($pout_date as $pd)
            <?php   
                    
                    $poutDate = date('d-m-Y', strtotime($pd->pout_date));
            ?>
            <tr style="border-bottom: none; border-top: none;">
            
                <td style="width: ;text-align:center;">{{$poutDate}}</td>
                <td style="width: ;text-align:center;">{{$pd->people_count}}</td>
                <td style="width: ;text-align:center;">{{$pd->so_number}}</td>
                <td style="width: ;text-align:center;">{{$pd->project_name}}</td>
             
                @if($pd->pout_work_desc == null)
                    <td  style="text-align:center;"> - </td>
                @else
                    <td  style=""><strong>{{$pd->pout_work_desc}}</strong></td>
                @endif
                
                @if($pd->work_attachment == null)
                <td  style="text-align:center;"><strong> N </strong></td>
                @else
                <?php $k++; ?>
                    <td  style="text-align:center;"><strong> Y </strong></td>
                @endif 
                

            </tr>
        @endforeach
       
        <tr style="border-bottom: none; border-top: none;">
            <td colspan="3" style="text-align: right; font-style: normal;"></td>
            <td  colspan="3"style="text-align:right;">
                    <b style="vertical-align: text-top;">For  Avians Innovation Technology Pvt. Ltd </b>
                    <br/><br/><br/><br/><br/> 
                    <small>( Authorised Signatory )</small>
        </tr>

    </table>
    <p style="text-align:center;font-size: 9px;">( This is computer generated Work Report Statement. )</p>
    <footer style="page-break-after: always;"></footer>

    <!-- PAGE 3 -->
    <h2 style="text-align:center;">Work Attachments</h2>
    <?php $count = count((array) $pout_date);?>
       
        @if($k >= 2)
            <table>

                @foreach($pout_date as $pd) 
                    <?php   
                        $pdate = date('d-m-Y', strtotime($pd->pout_date));
                    ?>
                    @if($pd->work_attachment != null)
                        <tr style="text-align:center;">
                            <td>
                                <h3>Date : {{$pdate}} </h3>
                                <img style="height: 200px; width: 300px;" src='{{URL::asset("files/attendance/workAttachments/$pd->work_attachment")}}'/>
                            </td>                           

                        </tr>
                    @endif    

                @endforeach
                
            </table>
        @else

            <table>
                <tr style="text-align:center;">
                    @foreach($pout_date as $pd) 
                        <?php   
                            $pdate = date('d-m-Y', strtotime($pd->pout_date));
                        ?>
                        @if($pd->work_attachment != null)
                            
                                <td>
                                    <h3>Date : {{$pdate}} </h3>
                                    <img style="height: 500px; width: 500px;" src='{{URL::asset("files/attendance/workAttachments/$pd->work_attachment")}}'/>
                                </td>
                            
                        @endif
                    @endforeach

                </tr>

            </table>
       
        @endif

       

    <br/>
    <p style="font-family: monospace;text-align:center;">The Work photographs provided above are only for reference.</p>
        <!-- <footer style="page-break-after: always;"></footer> -->
  
</body>
</html>