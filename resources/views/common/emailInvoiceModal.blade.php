<div class="modal fade" id="emailInvoiceModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Invoice On E-mail</h5>
                
                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
                    <input type="hidden" name="email_inv_id" id="email_inv_id">
                    <h5 class="font-size-15 mb-1" ><span id="customer_name">Customer Name</span></h5>

                    <h6 class="font-size-15 mb-3" ><span class="text-muted" id="email_inv_po_no">PO No.:</span> <span class="text-muted" id="email_inv_no">INVOICE NO.:</span></h6>

                    <div class="col-md-12 col-sm-12">
                        <div class="form-group mb-3">
                        <label for="cc_email_ids">Enter Comma Sepaated Email Ids </label>
                        <textarea class="form-control " id="cc_email_ids" name="cc_email_ids" rows="5" placeholder="Enter comma separated CC Emails."></textarea>
                    </div>
                    </div>
                    
                    <hr/>
                    <strong>Do you really wants to send Invoice on E-mail? </strong>
                     <br/>
                    <small>It may take up to 1 minute. Because it includes PDF generation and sending process at the same time.</small>
                    <hr/>

                    <div class="col-sm-12 col-md-12 mr-0 ml-auto">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">No</button>
                       <button type="button" class="btn btn-primary waves-effect waves-light emailInvButton"><i id="emailInvButtonIcon" class="bx bx-check-double font-size-16 align-middle me-2"></i>Send Email</button>
                    </div>
                
            </div>
        </div>
    </div>
</div>