$(document).on("click",'.downloadInvPDF',function()
{       
        $("#emailInvButton").removeClass("bx-loader bx-spin").addClass("bx-check-double");
        $(".emailInvButton").prop('disabled', false);
        $('#pdf_inv_po_no').text(''); 
        $('#pdf_inv_no').text(''); 
        $('#pdf_inv_customer_name').text('');
        $('#cc_email_ids').val('');  


    var inv_id = $(this).data('id');
    var inv_no = $(this).data('inv-no');
    var cust_name = $(this).data('cust-name');
    var po_no = $(this).data('po-no');

    // THEN INSERT NEW DATA
    $('#pdf_inv_no').text('( #INVOICE NO.: '+inv_no+' )'); 
    $('#pdf_inv_po_no').text('( #PO NO.: '+po_no+' )'); 
    $('#pdf_inv_customer_name').text(cust_name); 
    $('#pdf_inv_id').val(inv_id);  

    $("#inv_original_copy").attr("href", 'generate_inv_pdf/'+inv_id+'/O');
    $("#inv_dup_copy").attr("href", 'generate_inv_pdf/'+inv_id+'/D');
    $("#inv_trip_copy").attr("href", 'generate_inv_pdf/'+inv_id+'/T');
    $("#inv_transport_copy").attr("href", 'generate_inv_pdf/'+inv_id+'/TR');
    $('#downloadInvPDF').modal('show');
});

$( function() {
    $( "#downloadInvPDF" ).draggable();
} );
