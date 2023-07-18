@extends('seller.layouts.app')

@section('panel_content')
    <style type="text/css">
        .aiz-table td, .aiz-table th {
            padding: 1rem 0.1rem;
        }
        table span.badge {
            word-break: break-word;
            width: 5rem;
            height: auto;
            display: block;
            white-space: normal;
        }
    </style>
    <div class="row gutters-10 justify-content-center">
        @php
            $count = DB::table('orders')->where('seller_id', Auth::user()->id)->where('created_at', '<=', date('Y-m-d H:i:s'))
                ->count();
            $grand_total = DB::table('orders')->where('seller_id', Auth::user()->id)->where('delivery_status', '!=', 'cancelled')->where('created_at', '<=', date('Y-m-d H:i:s'))
                ->sum('orders.grand_total');
            $product_storehouse_total = DB::table('orders')->where('seller_id', Auth::user()->id)->where('delivery_status', '!=', 'cancelled')->where('created_at', '<=', date('Y-m-d H:i:s'))
                ->sum('orders.product_storehouse_total');
            $total_turnover = "$".sprintf('%.2f',$grand_total);
            $total_profit = "$".sprintf('%.2f',($grand_total - $product_storehouse_total));
        @endphp
        <div class="col-md-4 mx-auto mb-3">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x" style="color: #007bff"></i>
                  </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $count }}</div>
                    <div class="opacity-50 text-center">{{  translate('Total Orders') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mx-auto mb-3">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x" style="color: #007bff"></i>
                  </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $total_turnover }}</div>
                    <div class="opacity-50 text-center">{{  translate('Total Turnover') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mx-auto mb-3">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x" style="color: #007bff"></i>
                  </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ $total_profit }}</div>
                    <div class="opacity-50 text-center">{{  translate('Total Profit') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <form id="sort_orders" action="" method="GET">
          <div class="card-header row gutters-5">
              <div class="col-md-2 ml-auto">
                  <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Payment Status')}}" name="payment_status">
                      <option value="">{{ translate('Filter by Payment Status')}}</option>
                      <option value="paid" @isset($payment_status) @if($payment_status == 'paid') selected @endif @endisset>{{ translate('Buyer has paid')}}</option>
                      <option value="unpaid" @isset($payment_status) @if($payment_status == 'unpaid') selected @endif @endisset>{{ translate('Un-Paid')}}</option>
                  </select>
              </div>

              <div class="col-md-2 ml-auto">
                  <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Pickup Status')}}" name="product_storehouse_status">
                      <option value="">{{ translate('Filter by Pickup Status')}}</option>
                      <option value="1" @isset($product_storehouse_status) @if($product_storehouse_status) selected @endif @endisset>{{ translate('Picked up')}}</option>
                      <option value="0" @isset($product_storehouse_status) @if(!is_null($product_storehouse_status) && $product_storehouse_status == 0) selected @endif @endisset>{{ translate('Not picked up')}}</option>
                  </select>
              </div>

              <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Filter by Delivery Status')}}" name="delivery_status">
                    <option value="">{{ translate('Filter by Deliver Status')}}</option>
                    @foreach(get_express_status() as $status_key => $status_text)
                    <option value="{{$status_key}}" @isset($delivery_status) @if($delivery_status == $status_key) selected @endif @endisset>{{ $status_text}}</option>
                    @endforeach
                    <option value="cancelled" @isset($delivery_status) @if($delivery_status == 'cancelled') selected @endif @endisset>{{translate('Cancelled')}}</option>

                </select>
              </div>

              <div class="col-md-2">
                  <div class="form-group mb-0">
                      <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}" autocomplete="off" data-advanced-range="true">
                  </div>
              </div>

              <div class="col-md-2">
                <div class="from-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
              </div>
              <div class="col-md-2">
                  @if(is_pc())
                      <div class="form-group mt-1 float-right">
                          <button class="btn btn-light" type="reset" onclick="reset_form()">重置</button>
                          <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                      </div>
                  @else
                      <div class="form-group mt-1" style="display: flex;justify-content: space-between;">
                          <button class="btn btn-sm btn-light" type="reset" onclick="reset_form()">重置</button>
                          <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                      </div>
                  @endif
              </div>
          </div>
        </form>

        @if (count($orders) > 0)
            <div class="card-body p-3">
                @if(is_pc())
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th data-breakpoints="lg">#</th>
                            <th>{{ translate('Order Code')}}</th>
                            <th data-breakpoints="lg">{{ translate('Order Type') }}</th>
                            <th data-breakpoints="lg">{{ translate('Num. of Products')}}</th>
                            <th data-breakpoints="lg">{{ translate('Customer')}}</th>
                            <th data-breakpoints="md">{{ translate('Pick Up Price')}}</th>
                            <th data-breakpoints="md">{{ translate('Amount')}}</th>
                            <th data-breakpoints="md">{{ translate('Profit')}}</th>
                            <th>{{ translate('Pick Up Status') }}</th>
                            <th data-breakpoints="lg">{{ translate('Delivery Status')}}</th>
                            <th>{{ translate('Payment Status')}}</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $key => $order_id)
                            @php
                                $order = \App\Models\Order::find($order_id->id);
                            @endphp
                            @if($order != null)
                                <tr>
                                    <td>
                                        {{ $key+1 }}
                                    </td>
                                    <td>
                                        <a href="#{{ $order->code }}">{{ $order->code }}</a>
                                    </td>
                                    <td>
                                         @if ($order->order_type == 6)
                                          <span class="badge badge-inline badge-danger">{{ translate('Urgent') }}</span>
                                         @elseif ($order->order_type == 24)
                                          {{ translate('ordinary') }}
                                         @else
                                          {{ translate('ordinary') }}
                                         @endif
                                    </td>
                                    <td>
                                        {{ $order->orderDetails->where('seller_id', Auth::user()->id)->sum("quantity") }}
                                    </td>
                                    <td>
                                        @if ($order->user_id != null)
                                            {{ optional($order->user)->name }}
                                        @else
                                            {{ translate('Guest') }} ({{ $order->guest_id }})
                                        @endif
                                    </td>
                                    <td>
                                        {{ single_price($order->product_storehouse_total) }}
                                    </td>
                                    <td>
                                        {{ single_price($order->grand_total) }}
                                    </td>
                                    <td>
                                        @if ($order->product_storehouse_total > 0)
                                            {{ single_price($order->grand_total - $order->product_storehouse_total) }}
                                        @else
                                            {{ translate('None') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->delivery_status == 'cancelled')
                                            <span class="badge badge-inline badge-danger">{{translate('Cancelled')}}</span>
                                        @else
                                            @if ($order->product_storehouse_status)
                                                <span class="badge badge-inline badge-success">{{translate('Picked Up')}}</span>
                                            @else
                                                @if($order->paymentStatement)
                                                        <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                                @else
                                                    @if ($order->product_storehouse_total)
                                                        <span class="badge badge-inline badge-danger">{{translate('Unpicked Up')}}</span>
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $order->delivery_status;
                                        @endphp
                                        {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                                    </td>
                                    <td>
                                        @if($order->delivery_status == 'cancelled')
                                            <span class="badge badge-inline badge-danger">{{translate('Cancelled')}}</span>
                                        @else
                                            @if ($order->payment_status == 'paid')
                                                <span class="badge badge-inline badge-success">{{ translate('Buyer has paid')}}</span>
                                            @else
                                                <span class="badge badge-inline badge-danger">{{ translate('Unpaid')}}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div style="display: flex;justify-content: flex-end;">
                                            <a href="{{ route('seller.orders.show', encrypt($order->id)) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Order Details') }}" @if($order->delivery_status == 'cancelled') onclick="AIZ.plugins.notify('warning', '{{translate('The order has been cancelled')}}');return false;" @endif>
                                                <i class="las la-eye"></i>
                                            </a>
                                            <a href="{{ is_android() ? 'javascript:void(0);' : route('seller.invoice.download', $order->id) }}" class="btn btn-soft-warning btn-icon btn-circle btn-sm" title="{{ translate('Download Invoice') }}" @if (is_android()) onclick="downloadInvoicePdf('{{route('seller.invoice.download', $order->id)}}');" @endif>
                                                <i class="las la-download"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Order Details') }}" onclick="order_reply({{!empty($order->orderDetails[0]) ? $order->orderDetails[0]->product_id : 0}}, '{{!empty($order->orderDetails[0]->product->slug) ? route('product', $order->orderDetails[0]->product->slug) : ''}}', {{$order->seller_id}}, {{$order->user_id}}, '{{addslashes($order->user->name)}}');">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-text-fill" viewBox="0 0 16 16">
                                                    <path d="M16 8c0 3.866-3.582 7-8 7a9.06 9.06 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7zM4.5 5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 2.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                @else
                    @include("seller.orders.mobile_table")
                @endif
                <div class="aiz-pagination">
                    {{ $orders->links() }}
              	</div>
            </div>
        @endif
    </div>

@endsection


@section('modal')
    <!-- 对话框 -->
    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="" action="{{ route('conversations.store') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" value="">
                    <input type="hidden" name="receiver_id" value="">
                    <input type="hidden" name="product_id" value="">
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <a class="btn btn-primary btn-md product-url" href="" target="_blank">{{ translate('View conversation products') }}</a>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="message" required
                                      placeholder="{{ translate('Your Question') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary fw-600"
                                data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary fw-600">{{ translate('Send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ static_asset('assets/js/clipboard-polyfill.js') }}"></script>
<script>
    // 显示对话框
    function order_reply(product_id, product_url, seller_id, receiver_id, user_name) {
        if (!receiver_id) {
            AIZ.plugins.notify('danger', '请选择一个买家');
            return false;
        }
        $("#chat_modal .modal-title").html(user_name);
        if (product_url === '') {
            $("#chat_modal a.product-url").hide();
        }
        $("#chat_modal a.product-url").attr('href', product_url);
        $("#chat_modal input[name=user_id]").val(seller_id);
        $("#chat_modal input[name=receiver_id]").val(receiver_id);
        $("#chat_modal input[name=product_id]").val(product_id);

        // 加载对话内容
        $('#chat_modal').modal('show');
    }

    $(".order-item .show-more").on("click", function () {
        if (!$(this).hasClass("show")) {
            $(this).addClass("show").html("{{translate('Retract')}}<i class=\"icon-angle-up\"></i>");
            $(this).prev(".order-info").show();
        } else {
            $(this).removeClass("show").html("{{translate('Show More')}}<i class=\"icon-angle-down\"></i>");
            $(this).prev(".order-info").hide();
        }
    });

    // 复制订单号
    // 创建 ClipboardJS 实例
    const clipboard = navigator.clipboard;
    $(".order-item .order-code").on("click", function () {
        try {
            clipboard.writeText($(this).data("order-code"));
        } catch (e) {
            AIZ.plugins.notify('danger', e);

            const textArea = document.createElement('textArea')
            textArea.value = $(this).data("order-code");
            textArea.style.width = 0;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999px';
            textArea.style.top = '10px';
            textArea.setAttribute('readonly', 'readonly');
            document.body.appendChild(textArea)

            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea)
        }

        // if ($(".aiz-notify .progress-bar").length > 0) return;
        AIZ.plugins.notify('success', "{{translate('Copy Successfully')}}");
    });
</script>
@endsection
