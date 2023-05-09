<!-- delete Modal -->

<div id="delete_record_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
      <form  class="form-horizontal">
        <div class="modal-header">
            <h5 class="modal-title">Confirm Record Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <strong>Do you really wants to delete the record..? </strong>
          <!-- <strong>Do you really want to Reset Password For this Account..? </strong> -->

          <div class="form-group">
            <div class="col-md-4">
              <input type="hidden" id="id" name="id" class="form-control"/>
            </div>
          </div>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </div>
        <div class="modal-footer"> 
          <button type="button" class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary text-white btn-sm " id="del_rec"><i class="fe fe-check mr-2"></i>Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- delete Modal -->

<div id="pass_reset_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
      <form  class="form-horizontal">
        <div class="modal-header">
            <h5 class="modal-title">Confirm Reset Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
         

          <div class="form-group row">
            <div class="col-md-12">
              <input type="hidden" id="rp_id" name="rp_id" class="form-control"/>
              <input type="hidden" id="name" name="name" class="form-control"/>
              <input type="hidden" id="wemp_number" name="emp_number" class="form-control"/>
             
              <strong>Full Name : </strong> <span id="u_name"> - </span><br>
              <strong>Employee Id : </strong><span id="u_emp_number"> - </span>
            </div>
          </div>
          <hr>
           <!-- <strong>Do you really wants to delete the record..? </strong> -->
           <strong>Do you really want to Reset Password For this Account..? </strong>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </div>
        <div class="modal-footer"> 
          <button type="button" class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary text-white btn-sm " id="pass_res"><i class="fe fe-check mr-2"></i>Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>