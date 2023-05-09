<div class="modal fade" id="downloadInvPDF" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Invoice PDF</h5>
                
                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               
                    <input type="hidden" name="pdf_inv_id" id="pdf_inv_id">
                    <h5 class="font-size-15 mb-1" ><span id="pdf_inv_customer_name">Customer Name</span></h5>

                    <h6 class="font-size-15 mb-3" ><span class="text-muted" id="pdf_inv_po_no">PO No.:</span> <span class="text-muted" id="pdf_inv_no">INVOICE NO.:</span></h6>

                     <br/>
                    <ol>
                        <li> <a id="inv_original_copy" target="_blank" rel="tooltip" title="DOWNLOAD ORIGINAL COPY FOR CUSTOMER"> ORIGINAL COPY FOR CUSTOMER</a> </li>
                        <li> <a id="inv_dup_copy" target="_blank" rel="tooltip" title="DOWNLOAD DUPLICATE FOR SUPPLIER">DUPLICATE FOR SUPPLIER </a></li>
                        <li> <a id="inv_trip_copy" target="_blank" rel="tooltip" title="DOWNLOAD TRIPLICATE FOR SUPPLIER"> TRIPLICATE FOR SUPPLIER </a></li>
                        <li> <a id="inv_transport_copy" target="_blank" rel="tooltip" title="DOWNLOAD TRIPLICATE FOR SUPPLIER">FOR TRANSPORTER </a></li>
                    </ol>    

                    <div class="col-sm-12 col-md-12 mr-0 ml-auto">
                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">CLOSE</button>
                       
                    </div>
                
            </div>
        </div>
    </div>
</div>