
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
    <p style="text-align:center; font-size: 14px;margin-top: 1px;">Site Expenses Statement</p>
    <!-- TAX INVOICE DETAILS HEADER -->
    <table style="margin-top: 0px;margin-bottom: 0px;">
        <tr>

            <td colspan="6" style="color: rgb(51,51,51);border-left: none; width: 150%; text: center">

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
            <td style="width:30px;"> Adv Total Amount.: <br/> <b> {{$uo->adv_amnt}} </b></td>
            <td style="width:30px;"> From Date.: <br/> <b> {{$uo->from_date}} </b></td>
            <td style="width:30px;"> To Date.: <br/> <b> {{$uo->to_date}} </b></td>
            <td style="width:30px;">  </td>
        </tr>
        @endforeach
        <tr>       
            <td colspan="6"> Adv VH No.: <br/><br/><br/> <b>  </b></td>
        </tr>
    </table>

    <table style='margin-top: 0px;'>
        <tr>
            <th  style="width:60px;text-align:center;">Date</th>
            <th  style="width:120px;text-align:center;"> Expense Description</th>
            <th  style="width:20px!important;white-space:wrap;text-align:center;">From Location</th>
            <th  style="width:20px!important;white-space:wrap;text-align:center;">To Location</th>
            <th  style="width:60px!important;white-space:wrap;text-align:center;">Approver Admin</th>
            <th  style="width:60px!important;white-space:wrap;text-align:center;">Approver SuperAdmin</th>
            <th  style="width:50px!important;text-align:center;">OA NO.</th>
            <th  style="width:90px!important;text-align:center;">Project Name.</th>
            <th  style="width:30px!important;text-align:center;">Bills</th>
            <th  style="width:30px!important;white-space:wrap;text-align:center;">No of Persons</th>
            <th  style="width:50px!important;white-space:wrap;text-align:center;">Travel Exp</th>
            <th  style="width:40px!important;text-align:center;">Hotel</th>
            <th  style="width:40px!important;white-space:wrap;text-align:center;">DA</th>
            <th  style="width:40px!important;white-space:wrap;text-align:center;">Mat Purchase</th>
            <th  style="width:40px!important;white-space:wrap;text-align:center;">Other Exp</th>
            <th  style="width:50px;white-space:wrap;text-align:center;">Amt (in Rs.)</th>
        </tr>
      <?php  $n=7?>
      <?php $j = 0; $total_tech_exp_amount=$sa_aprvd_amount=$total_no_of_person=$total_trav_exp=$total_hotel=$total_da=$total_mat_purchase=$total_other_exp=0;?>
        @foreach($tech_exp as $te)
            <?php   
                    $total_tech_exp_amount += $te->amount;
                    $sa_aprvd_amount += $te->aprvd_amount;
                    $exp_date = date('d-m-Y', strtotime($te->exp_date));
            ?>
            <tr style="border-bottom: none; border-top: none;">
            
                <td style="width: ;text-align:center;">{{$exp_date}}</td>
                <td  style="">

                    @if($te->exp_desc == null)
                        <strong> - </strong>
                    @else
                        <strong>{{$te->exp_desc}}</strong>
                    @endif
                </td>
                @if(empty($te->from_location))
                    <td style="width: ;text-align:center;"> - </td>
                @else
                    <td style="width: ;text-align:center;"> {{$te->from_location}}</td>
                @endif

                @if(empty($te->to_location))
                    <td style="width: ;text-align:center;"> - </td>
                @else
                    <td style="width: ;text-align:center;"> {{$te->to_location}}</td>
                @endif

                <td style="width: ;text-align:center;">{{$te->project_admin}}</td>
                <td style="width: ;text-align:center;">{{$te->super_admin}}</td>
                <td style="width: ;text-align:center;">{{$te->so_number}}</td>
                <td style="width: ;text-align:center;">{{$te->project_name}}</td>
                @if($te->attachment == null)
                    <td  style="text-align:center;"><strong> N </strong></td>
                @else
                    <td  style="text-align:center;"><strong> Y </strong></td>
                @endif 


                @if($te->no_of_person == null)
                    <td style="text-align:center;"> - </td>
                @else
                <?php $total_no_of_person = $total_no_of_person + $te->no_of_person ;?>
                    <td style="text-align:center;">{{$te->no_of_person}}</td>
                @endif 


                @if(($te->exp_type == 'Bus') || ($te->exp_type == 'Train') || ($te->exp_type == 'Bike') || ($te->exp_type == 'Shared_Auto') || ($te->exp_type == 'Private_Auto') || ($te->exp_type == 'Own Car') )
                    <?php $total_trav_exp = $total_trav_exp + $te->aprvd_amount;?>
                    <td style="width: ;text-align:center;">{{$te->aprvd_amount}}</td>
                @else
                    <td style="width: ;text-align:center;"> </td>
                @endif 

                @if($te->exp_type == 'Hotel')
                    <?php $total_hotel = $total_hotel + $te->aprvd_amount;?>

                    <td style="width: ;text-align:center;">{{$te->aprvd_amount}}</td>
                @else
                    <td style="width: ;text-align:center;"> </td>
                @endif 

                @if($te->exp_type == 'Daily Allowance')
                    <?php $total_da = $total_da + $te->aprvd_amount;?>

                    <td style="width: ;text-align:center;">{{$te->aprvd_amount}}</td>
                @else
                    <td style="width: ;text-align:center;"> </td>
                @endif

                @if($te->exp_type == 'Material_Purchase')
                    <?php $total_mat_purchase = $total_mat_purchase + $te->aprvd_amount;?>

                    <td style="width: ;text-align:center;">{{$te->aprvd_amount}}</td>
                @else
                    <td style="width: ;text-align:center;"> </td>
                @endif

                @if(($te->exp_type == 'Crane/Hydra') || ($te->exp_type == 'Labour_Hired') || ($te->exp_type == 'Scaffolding') || ($te->exp_type == 'Other'))
                    <?php $total_other_exp = $total_other_exp + $te->aprvd_amount;?>

                    <td style="width: ;text-align:center;">{{$te->aprvd_amount}}</td>
                @else
                    <td style="width: ;text-align:center;"> </td>
                @endif

                <td style="width: ;white-space:wrap;text-align:center;">{{$te->aprvd_amount}}</td>

            </tr>
        @endforeach
        <tr style="border-bottom: none; border-top: none;">
            <td colspan="9" style="text-align: right; font-style: normal;"><strong>Total</strong></td>
            <td style="width: 50px;text-align:center;">{{$total_no_of_person}}</td>
            <td style="width: 50px;text-align:center;">{{$total_trav_exp}}</td>
            <td style="width: 50px;text-align:center;">{{$total_hotel}}</td>
            <td style="width: 50px;text-align:center;">{{$total_da}}</td>
            <td style="width: 50px;text-align:center;">{{$total_mat_purchase}}</td>
            <td style="width: 50px;text-align:center;">{{$total_other_exp}}</td>
            <td style="width: 50px;text-align:center;">{{$total_tech_exp_amount}}</td>
        </tr>
        <tr style="border-bottom: none; border-top: none;">
            <td colspan="15" style="text-align: right; font-style: normal;"><strong>Total claimed Amount (Technician)</strong></td>
            <td style="width: 50px;text-align:center;">{{$total_tech_exp_amount}}</td>
        </tr>
        <tr style="border-bottom: none; border-top: none;">
            <td colspan="15" style="text-align: right; font-style: normal;"><strong>Total Approved Amount (super admin)</strong></td>
            <td style="width: 50px;text-align:center;">{{$sa_aprvd_amount}}</td>
        </tr>
        <tr style="border-bottom: none; border-top: none;">
            <td colspan="15" style="text-align: right; font-style: normal;"><strong>Balance /refundable Amount to Company,if any</strong></td>
            <td style="width: 50px;text-align:center;">{{$adv_amnt - $sa_aprvd_amount}}</td>
        </tr>   
        <tr style="border-bottom: none; border-top: none;">
            <td colspan="12" style="text-align: right; font-style: normal;"></td>
            <td  colspan="4"style="text-align:right;">
                    <b style="vertical-align: text-top;">For  Avians Innovation Technology Pvt. Ltd </b>
                    <br/><br/><br/><br/><br/> 
                    <small>( Authorised Signatory )</small>
        </tr>

    </table>
    <p style="text-align:center;font-size: 9px;">( This is computer generated Site Expenses Statement. )</p>
    <footer style="page-break-after: always;"></footer>
    <!-- PAGE 3 -->
    <h2 style="text-align:center;">Expense Attachments</h2>
    <?php $count = count($tech_exp)?>
       
        @if($count >= 2)
            <table>
                @foreach($tech_exp as $te) 
                    @if(($te->exp_type == 'Bus') || ($te->exp_type == 'Train') || ($te->exp_type == 'Bike') || ($te->exp_type == 'Shared_Auto') || ($te->exp_type == 'Private_Auto') || ($te->exp_type == 'Own Car') )
                        @if($te->attachment != null)
                            <tr style="text-align:center;">
                                <td >
                                    <h3>{{$te->exp_type}} </h3>
                                    <img style="height: 300px; width: 300px;" src='{{URL::asset("files/user/travel_expense/$te->attachment")}}'/>
                                </td>
                            </tr>
                        @endif    
                    @else
                        @if($te->attachment != null)
                            <tr  style="text-align:center;">
                                <td >
                                    <h3>{{$te->exp_type}} </h3>
                                    <img style="height: 300px; width: 300px;" src='{{URL::asset("files/user/expense/$te->attachment")}}'/>
                                </td>
                            </tr>
                        @endif    
                    @endif 

                @endforeach
            </table>
        @endif
        <br/>
        <p style="font-family: monospace;text-align:center;">The Expenses photographs provided above are only for reference.</p>
        <!-- <footer style="page-break-after: always;"></footer> -->
</body>
</html>