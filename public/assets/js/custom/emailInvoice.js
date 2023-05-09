$(document).on("click",'.emailInvoice',function()
{       
        $("#emailInvButton").removeClass("bx-loader bx-spin").addClass("bx-check-double");
        $(".emailInvButton").prop('disabled', false);
        $('#email_inv_po_no').text(''); 
        $('#email_inv_no').text(''); 
        $('#customer_name').text('');
        $('#cc_email_ids').val('');  


    var inv_id = $(this).data('id');
    var inv_no = $(this).data('inv-no');
    var cust_name = $(this).data('cust-name');
    var po_no = $(this).data('po-no');

    // THEN INSERT NEW DATA
    $('#email_inv_no').text('( #INVOICE NO.: '+inv_no+' )'); 
    $('#email_inv_po_no').text('( #PO NO.: '+po_no+' )'); 
    $('#customer_name').text(cust_name); 
    $('#email_inv_id').val(inv_id); 

    $('#emailInvoiceModal').modal('show');
});

$( function() {
    $( "#emailInvoiceModal" ).draggable();
} );
// INVOICE FORM 
function inv_form_reset () {

        // FIRST EMPTY
        $("#emailInvButtonIcon").removeClass("bx-loader bx-spin").addClass("bx-check-double");
        $(".emailInvButton").prop('disabled', false);
        $('#email_inv_po_no').text(''); 
        $('#email_inv_no').text(''); 
        $('#customer_name').text('');  
        $('#cc_email_ids').val('');  

}

// EMAIL INVOICE
$('.emailInvButton').click(function(e){
 
    var inv_id= $('#email_inv_id').val();
    var cc_email_ids= $('#cc_email_ids').val(); 

    
    
    if(inv_id.length!=0)
    {
        $("#emailInvButtonIcon").removeClass("bx-check-double").addClass("bx-loader bx-spin");
        $(".emailInvButton").prop('disabled', true); 

        $.ajax({
        url:'email-invoice',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
        type:'post',
        data:{
            inv_id:inv_id,
            cc_email_ids:cc_email_ids,
        },
        cache: false,
        dataType: 'json',
        success:function(dt)
        {
            
            if(dt.data==true)
            {
                toastr.options.timeOut = 5000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.options.showEasing= 'swing';
                toastr.options.hideEasing= 'linear';
                toastr.options.showMethod= 'fadeIn';
                toastr.options.hideMethod= 'fadeOut';
                toastr.options.closeButton= true;
                toastr.success(dt.msg);
                
                $("#emailInvButtonIcon").removeClass("bx-loader bx-spin").addClass("bx-check-double");
                $(".emailInvButton").prop('disabled', false);
                $('#emailInvoiceModal').modal('hide');
                
            }
            else
            {
                toastr.options.timeOut = 5000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.options.showEasing= 'swing';
                toastr.options.hideEasing= 'linear';
                toastr.options.showMethod= 'fadeIn';
                toastr.options.hideMethod= 'fadeOut';
                toastr.options.closeButton= true;
                toastr.error(dt.msg);

                $("#emailInvButtonIcon").removeClass("bx-loader bx-spin").addClass("bx-check-double");
                $(".emailInvButton").prop('disabled', false);
                $('#emailInvoiceModal').modal('hide');
            }
        }
        });
    }
    else
    {
        e.preventDefault();
    }
    
});