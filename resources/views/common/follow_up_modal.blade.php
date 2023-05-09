<div class="modal fade" id="followupModal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Follow Up</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <form method="post" action="{{url('add_follow_up')}}" class="form-horizontal row">
                    {{ csrf_field() }}
                    <input type="hidden" name="quotation_id" id="quotation_id">
                    <div class="col-sm-12 col-md-3">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="follow_up_date" placeholder="dd/mm/yyyy" required name="follow_up_date">
                            <label for="follow_up_date">Next Follow Up Date</label>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="remark" placeholder="Enter remark" required name="remark" rows="4"></textarea>
                            <label for="remark">Customer Remark</label>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="executive_note" placeholder="Enter Executive Note" required name="executive_note" rows="4"></textarea>
                            <label for="executive_note">Executive Note</label>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-1 mr-0 ml-auto">
                       <!--  <button class="btn btn-secondary waves-effect btn-sm" data-bs-dismiss="modal" aria-label="Close">x</button> -->
                        <button class="btn btn-primary waves-effect btn-sm" type="submit">Save</button>
                    </div>
                </form>
                <hr>
                <h5 class="text-muted">Follow UP History</h5>
                <div class="text-center">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Next Follow-up Date</th>
                                    <th>Customer Remark</th>
                                    <th>Executive Note</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="follow_body">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
