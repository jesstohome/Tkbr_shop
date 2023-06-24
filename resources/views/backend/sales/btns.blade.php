@if ($order->product_storehouse_status)
    <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="AIZ.plugins.notify('danger', '{{translate('The order has been picked up and cannot be deleted')}}');return false;" title="{{ translate('Delete') }}">
        <i class="las la-trash"></i>
    </a>
    <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="AIZ.plugins.notify('danger', '{{translate('The order has been picked up and cannot be cancel')}}');return false;" title="{{ translate('Cancel') }}">
        <i class="las la-tint"></i>
    </a>
@else
    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Delete') }}">
        <i class="las la-trash"></i>
    </a>
    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-cancel" data-href="{{route('orders.cancel', ['order_id' => $order->id])}}" title="{{ translate('Cancel') }}">
        <i class="las la-tint"></i>
    </a>
@endif
