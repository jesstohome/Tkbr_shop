<div class="modal-header">
    <div class="col-md-6 text-md-right">
        <a href="javascript:void (0)" class="btn btn-circle btn-info">
            <span>{{translate('Add New Staffs')}}</span>
        </a>
    </div>
</div>
<div class="modal-body">
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade show in active" id="home">
            <table class="table mb-0">
                <thead>
                    <th>{{translate('Abstract')}}</th>
                    <th>{{translate('Content')}}</th>
                    <th>{{translate('Options')}}</th>
                </thead>
                <tbody>
                @foreach($list as $item)
                    <tr>
                        <td>{{$item->abstract}}</td>
                        <td>{{$item->content}}</td>
                        <td>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('huashu.edit',$item->id)}}" title="{{ translate('Edit') }}">
                                <i class="las la-pen"></i>
                            </a>

                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('huashu.destroy', $item->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
</div>
