
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

        <tr>       
            <td style="width:80px;"> Technician Name.: <br/> <b> {{$u_obj1[0]->name}} </b></td>
            <td style="width:30px;"> Mobile No.: <br/> <b> {{$u_obj1[0]->mobile}} </b></td>
            <td style="width:30px;"> Adv Total Amount.: <br/> <b> {{$u_obj1[0]->adv_amnt}} </b></td>
            <td style="width:30px;"> From Date.: <br/> <b> {{$u_obj1[0]->from_date}} </b></td>
            <td style="width:30px;"> To Date.: <br/> <b> {{$u_obj1[0]->to_date}} </b></td>
            <td style="width:30px;">  <br/> <b>  </b></td>
        </tr>
        <tr>       
            <td colspan="6"> Adv VH No.: <br/><br/><br/> <b>  </b></td>
        </tr>
    </table>

    <table style='margin-top: 0px;'>
        <tr>
            
            <th  style="width:50px;">Date</th>
            <th  style="width:60px;white-space:wrap;">Approval Admin</th>
            <th  style="width:60px;white-space:wrap;">Approval Super Admin</th>
            <th >Description</th>
            <th  style="width:90px;"> OA NO.</th>
            <th  style="width:50px;"> Bills (Y / N)</th>
            <th  style="width:50px;white-space:wrap;">Travel Expense</th>
            <th  style="width:40px;">Hotel</th>
            <th  style="width:50px;white-space:wrap;">Daily Allowance</th>
            <th  style="width:60px;white-space:wrap;">Material Purchase</th>
            <th  style="width:;white-space:wrap;">Other Expenses (Crane,scaffolding, labor hired)</th>
            <th >Amount (in Rs.)</th>
        </tr>
      <?php  $n=7?>
      <?php $j = 0; $total_tech_exp_amount=0; $exp_total_amount=0;?>
    @foreach($tech_exp as $tech_exp)
        <?php   $total_tech_exp_amount += $tech_exp['total_tech_exp_amount'];
                $exp_total_amount += $tech_exp['exp_total_amount'];
        ?>
        <tr style="border-bottom: none; border-top: none;">
           
            <td style="width: ;">{{$tech_exp['exp_date']}}</td>
            <td style="width: ;">{{$tech_exp['approval_admin']}}</td>
            <td style="width: ;">{{$tech_exp['approval_super_admin']}}</td>
            <td  style="">
                @foreach($tech_exp['travel_desc'] as $td) 
                     <strong>{{$td->travel_desc}}</strong> <br> 
                @endforeach
            </td>
            <td style="width:;">{{$tech_exp['oa_number']}}</td>
            <td style="width: ;"> Yes </td>
            <td style="width: ;">{{$tech_exp['travel_expense']}}</td>
            <td style="width: ;">{{$tech_exp['hotel']}}</td>
            <td style="width: ;">{{$tech_exp['daily_allowance']}}</td>
            <td style="width: ;">{{$tech_exp['material_purchase']}}</td>
            <td style="width: ;">{{$tech_exp['other']}}</td>
            <td style="width: ;">{{$tech_exp['exp_total_amount']}}</td>

        </tr>
    @endforeach
    <tr style="border-bottom: none; border-top: none;">
        <td colspan="11" style="text-align: right; font-style: normal;"><strong>Sum Amount claimed by technician</strong></td>
        <td style="width: 100px;">{{$total_tech_exp_amount}}</td>
    </tr>
    <tr style="border-bottom: none; border-top: none;">
        <td colspan="11" style="text-align: right; font-style: normal;"><strong>Approved Amount by super admin</strong></td>
        <td style="width: 100px;">{{$exp_total_amount}}</td>
    </tr>
    <tr style="border-bottom: none; border-top: none;">
        <td colspan="11" style="text-align: right; font-style: normal;"><strong>Balance /refundable Amount,if any</strong></td>
        <td style="width: 100px;">{{$total_tech_exp_amount - $exp_total_amount}}</td>
    </tr>   
    <tr style="border-bottom: none; border-top: none;">
        <td colspan="9" style="text-align: right; font-style: normal;"></td>
        <td  colspan="3"style="text-align:right;">
                  <b style="vertical-align: text-top;">For  Avians Innovation Technology Pvt. Ltd </b>
                  <br/><br/><br/><br/><br/> 
                  <small>( Authorised Signatory )</small>
    </tr>

    </table>
    <p style="text-align:center;font-size: 9px;">( This is computer generated Site Expenses Statement. )</p>
</body>
</html>