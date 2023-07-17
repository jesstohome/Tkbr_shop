@foreach($customers as $customer)
    @php
        $content = $customer->name;
        if ($customer->is_virtual_user || $customer->is_virtual) {
            $content = '(' . translate('Virtual') . ')' . $customer->name;
        }
        if ($customer->total_conversation) {
            $content .= '(<span style=\'color:red\'>o</span>)';
        }
        if ($customer->total_orders) {
            $content .= '(<span style=\'color:red\'>⭐</span>)';
        }
    @endphp
    <option value="{{$customer->id}}" data-content="{{$content}}">{{$customer->name}}</option>
@endforeach
