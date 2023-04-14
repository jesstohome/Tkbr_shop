<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h6">{{translate('Comment')}}</h5>
            <button type="button" class="close" data-dismiss="modal">
            </button>
        </div>

        <div class="modal-body">

            <div class="form-group">
                <p>{{$review->comment ?: '无内容'}}</p>
            </div>
        </div>
    </div>
</div>
