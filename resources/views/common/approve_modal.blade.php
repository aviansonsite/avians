<div id="approve_record_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
      <form method="post" action class="form-horizontal">
        <div class="modal-header">
            <h5 class="modal-title">Confirm Work Order Approval</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <strong>Do you really wants to approve the work order..? </strong>
          <div class="form-group">
            <div class="col-md-4">
              <input type="hidden" name="id" class="form-control"/>
            </div>
          </div>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </div>
        <div class="modal-footer"> 
          <button type="button" class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary text-white btn-sm"><i class="fe fe-check mr-2"></i>Approve</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="reject_record_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
      <form method="post" action class="form-horizontal">
        <div class="modal-header">
            <h5 class="modal-title">Confirm Work Order Rejection</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <strong>Do you really wants to reject the work order..? </strong>
          <div class="form-group">
            <div class="col-md-4">
              <input type="hidden" name="id" class="form-control"/>
            </div>
          </div>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </div>
        <div class="modal-footer"> 
          <button type="button" class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary text-white btn-sm"><i class="fe fe-check mr-2"></i>Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>