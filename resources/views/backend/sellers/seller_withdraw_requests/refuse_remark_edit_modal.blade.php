<form id="refuse_remark_edit_form" class="form-horizontal" action="{{ route('commissions.update_refuse_remarks') }}" method="POST">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title h6">编辑拒绝备注</h5>
        <button type="button" class="close" data-dismiss="modal"></button>
    </div>
    <input type="hidden" value="{{$id}}" name="withdraw_request_id" />
    <div class="modal-body">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label" for="remarks">备注</label>
            <div class="col-sm-9">
                <textarea cols="50" rows="6" class="form-control" name="remarks">{{$seller_withdraw_request->remarks}}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button id="btn_update_remark" type="button" class="btn btn-primary">保存</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">取消</button>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        $("#btn_update_remark").on("click", function () {
            $(this).attr("disabled", true);
            $("#refuse_remark_edit_form").submit();
        });
    });
</script>
