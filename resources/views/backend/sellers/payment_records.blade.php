@extends('backend.layouts.app')
<style>
    .card .card-header:nth-child(-n+2) {
        border-bottom: 0!important;
    }
</style>

@section('content')
    <script src='/My97DatePicker/WdatePicker.js'></script>
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="h3">{{translate('All Records')}} ({{translate('Total')}}: {{$total_seller}} {{translate('People')}}, {{$total}} {{translate('Transactions')}}, {{single_price($total_amount)}} {{translate('Amount')}})</h1>
        </div>
        <div class="col text-right"></div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_sellers" action="" method="GET">
        <div class="card-header row gutters-5">
            @php
                $customers = filter_by_bloc(\App\Models\User::query()->where("user_type", "customer"));
                $customers = $customers->get();
                $sellers = filter_by_bloc(\App\Models\User::query()->where("user_type", "seller"));
                $sellers = $sellers->get();
            @endphp

            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="order_no" name="order_no" value="{{ $order_no ?? '' }}" placeholder="{{ translate('Enter Order Number') }}">
                </div>
            </div>

            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}" autocomplete="off">
                </div>
            </div>

            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="pay_status" id="pay_status">
                    <option value="">{{translate('All')}}</option>
                    <option value="1"  @if($pay_status == '1') selected @endif >{{translate('Paid')}}</option>
                    <option value="0"  @if($pay_status == '0') selected @endif >{{translate('Unpaid')}}</option>
                </select>
            </div>

            <div class="col-md-2 ml-auto">
                <select name="buyer_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                    <option value="">{{translate('All Customers')}}</option>
                    @foreach ($customers as $key => $salesman)
                        <option value="{{ $salesman->id }}" @if($buyer_id == $salesman->id) selected @endif data-contact="{{ $salesman->email }}">
                            {{ $salesman->name }} ({{$salesman->email}})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 ml-auto">
                <select name="seller_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="sort_sellers()">
                    <option value="">{{translate('All Sellers')}}</option>
                    @foreach ($sellers as $key => $salesman)
                        <option value="{{ $salesman->id }}" @if($seller_id == $salesman->id) selected @endif data-contact="{{ $salesman->email }}">
                            {{ $salesman->name }} ({{$salesman->email}})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-header row gutters-5">
            @if (isSupperAdmin())
                <div class="col-md-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="bloc_id" id="bloc_id" onchange="sort_sellers()">
                        <option value="">{{translate('Filter by Bloc')}}</option>
                        @foreach(\App\Models\Bloc::all() as $bloc)
                            <option value="{{$bloc->id}}"  @isset($bloc_id) @if($bloc_id == $bloc->id) selected @endif @endisset>{{$bloc->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (isSupperAdmin() || isBlocManage())
                @php
                    $salesmans = filter_by_bloc(\App\Models\User::where('user_type', '!=', 'customer'))->orderBy('id', 'desc')->get();
                @endphp
                <div class="col-md-2 ml-auto">
                    <select name="salesman_user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="sort_sellers()">
                        <option value="">{{translate('All Ssalesman')}}</option>
                        @foreach ($salesmans as $key => $salesman)
                            <option value="{{ $salesman->id }}" @if($salesman_user_id == $salesman->id) selected @endif>
                                {{ $salesman->name }} ({{$salesman->email}})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @include('backend.partials.filters.payment_code')

            <div class="col-lg-4 ml-auto">
                <input type="text" class="form-control d-inline col-5" id="min-price" name="min_price" value="{{ $min_price ?: '' }}" placeholder="最小价格">
                ~
                <input type="text" class="form-control d-inline col-5" id="max-price" name="max_price" value="{{ $max_price ?: ''}}" placeholder="最大价格">
            </div>

            <div class="col-md-2">
                <div class="form-group mb-0">
                  <input type="text" class="form-control" id="out_order_no" name="out_order_no" value="{{ $out_order_no ?? '' }}" placeholder="{{ translate('Enter Third Order Number') }}">
                </div>
            </div>
        </div>
        <div class="card-header row gutters-5">
            <div class="col-md-6">
                <button type="submit" class="btn btn-success btn-styled">{{ translate('Search') }}</button>
                <button class="btn btn-md btn-primary" type="reset" onclick="reset_form()">重置</button>
            </div>
        </div>

        <div class="card-body" style="overflow-x: auto">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th></th>
                    <th>{{translate('Order Code')}}</th>
                    @if (isSupperAdmin())<th>{{ translate('Bloc') }}</th>@endif
                    <th data-breakpoints="lg">{{translate('Customer Account')}}</th>
                    <th data-breakpoints="lg">{{translate('Seller Account')}}</th>
                    @if (isSupperAdmin() || isBlocManage())
                        <th data-breakpoints="lg">{{ translate('Salesman') }}</th>
                    @endif
                    <th data-breakpoints="lg">{{translate('Amount')}}</th>
                    <th data-breakpoints="lg">{{translate('Payment Channel')}}</th>
                    <th data-breakpoints="lg">{{translate('Payment Status')}}</th>
                    <th data-breakpoints="lg">{{ translate('Third Order Number') }}</th>
                    <th data-breakpoints="lg">{{ translate('Created At') }}</th>
                    <th data-breakpoints="lg">{{ translate('Pay Time') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $key => $record)
                    <tr>
                        <td>
                            {{$key + 1}}
                        </td>
                        <td>{{$record->order->code}}</td>
                        @if (isSupperAdmin())<td>{{$record->bloc ? $record->bloc->name : ''}}</td>@endif
                        <td>{{$record->buyer->email}}</td>
                        <td>{{$record->seller->email}}</td>
                        @if (isSupperAdmin() || isBlocManage())
                            <td>
                                @php
                                    $uid = $record->seller->pid;
                                    if( $uid == '')
                                    {
                                       echo '---';
                                    }
                                    else
                                    {
                                      $r =  \App\Models\User::where('id',$uid)->first() ;
                                     echo $r['name'];

                                    }
                                @endphp
                            </td>
                        @endif
                        <td>{{single_price($record->amount)}}</td>
                        <td>
                            @if($record->payment_code == 'paypal')
                                Paypal
                            @elseif($record->payment_code == 'wallet')
                                {{translate('Wallet Balance')}}
                            @elseif($record->payment_code == 'offline_transfer')
                                {{translate('Offline Transfer')}}
                            @else
                                {{$record->payment_code && 'work_order' != $record->payment_code ? $record->payment_code : translate($record->payment_code ?: 'Third Party Payment')}}
                            @endif
                        </td>
                        <td>
                            @if($record->pay_status == 1)
                            {{ translate('Paid') }}
                            @else
                            {{ translate('Unpaid') }}
                            @endif
                        </td>
                        <td>{{$record->out_order_no}}</td>
                        <td>{{$record->created_at}}</td>
                        <td>{{$record->created_at}}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
              {{ $list->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
	<!-- Delete Modal -->
	@include('modals.delete_modal')

    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5">{{ translate('Any query about this seller') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('conversations.admin_store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="receiver_id" id="receiver_id" value="">
                    <div class="modal-body gry-bg px-3 pt-3">
                        {{--<div class="form-group">
                            <input type="text" class="form-control mb-3" name="title"
                                value="" placeholder="{{ translate('Title') }}"
                                required>
                        </div>--}}
                        <div class="form-group">
                            <textarea class="form-control" rows="8" name="title" required
                                placeholder="{{ translate('Title') }}"></textarea>
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

	<!-- Seller Profile Modal -->
	<div class="modal fade" id="profile_modal">
		<div class="modal-dialog">
			<div class="modal-content" id="profile-modal-content">

			</div>
		</div>
	</div>

	<!-- Seller Payment Modal -->
	<div class="modal fade" id="payment_modal">
	    <div class="modal-dialog">
	        <div class="modal-content" id="payment-modal-content">

	        </div>
	    </div>
	</div>

	<!-- Ban Seller Modal -->
	<div class="modal fade" id="confirm-ban">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
					<button type="button" class="close" data-dismiss="modal">
					</button>
				</div>
				<div class="modal-body">
                    <p>{{translate('Do you really want to ban this seller?')}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
					<a class="btn btn-primary" id="confirmation">{{translate('Proceed!')}}</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Unban Seller Modal -->
	<div class="modal fade" id="confirm-unban">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
						<button type="button" class="close" data-dismiss="modal">
						</button>
					</div>
					<div class="modal-body">
							<p>{{translate('Do you really want to unban this seller?')}}</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
						<a class="btn btn-primary" id="confirmationunban">{{translate('Proceed!')}}</a>
					</div>
				</div>
			</div>
		</div>




    <!-- Guarantee Money Modal -->
    <div class="modal fade" id="guarantee_money">
        <div class="modal-dialog">
            <div class="modal-content" id="guarantee-money-content">

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ static_asset('assets/js/layer.min.js') }}"></script>
    <script type="text/javascript">


        function show_seller_guarantee_money_modal(id){
            $.post('{{ route('sellers.guarantee_money_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#guarantee_money #guarantee-money-content').html(data);
                $('#guarantee_money').modal('show', {backdrop: 'static'});
            });
        }


        function show_bzj(shop_id, bzj) {
            layer.prompt( {
                title: "保证金金额", //提示框标题
                value: bzj //初始时的值，默认空字符
            }, function (value, index, elem)
            {
                $.post( '{{ route('sellers.setbzj') }}', {
                    _token: '{{ @csrf_token() }}',
                    shop_id: shop_id,
                    bzj: value
                }, function (data)
                {
                    layer.msg( data.msg, function ()
                    {
                        location.reload();
                    } );
                }, 'json' );
                layer.close( index );
            } );
        }
     function set_pid(shop_id,pid)
     {
          @php

         $Salesmans =  \App\Models\User::where('user_type','salesman')->get();
         @endphp
         var html = '';
          @foreach ($Salesmans as $key => $us)
            html +="<option ";
            if( pid == @php echo $us['id'];@endphp )
            {
                html += ' selected ';
            }

            html += " value='@php echo $us['id'];@endphp'> @php echo $us['name'];@endphp</option>";
          @endforeach

          var html2 = "<select class='form-control' name='userid' id='userid'> ";
        html = html2+html+"</select>";
          layer.open({

        type: 1,
        title:'设置推销员',
        skin:'layui-layer-rim',
        area:['450px', 'auto'],

        content: ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
           + html
            +'</div>'
            +'</div>'

              +'</div>'
        ,
        btn:['保存','取消'],
        btn1: function (index,layero) {
            var userid = $("#userid").val();
              $.post('{{ route('sellers.setpid') }}',{_token:'{{ @csrf_token() }}', shop_id:shop_id,pid:userid}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });

            },'json');
        },
        btn2:function (index,layero) {
             layer.close(index);
        }
          });
     }

     function show_package(shop_id,seller_package_id)
     {
         @php
         $seller_packages = \App\Models\SellerPackage::all();
         @endphp
         var html = '';
          @foreach ($seller_packages as $key => $seller_package)
            html +="<option ";
            if( seller_package_id == @php echo $seller_package['id'];@endphp )
            {
                html += ' selected ';
            }

            html += " value='@php echo $seller_package['id'];@endphp'> @php echo $seller_package['name'];@endphp</option>";
          @endforeach

          var html2 = "<select class='form-control' name='packageid' id='packageid'> ";
        html = html2+html+"</select>";
          layer.open({

        type: 1,
        title:'设置套餐',
        skin:'layui-layer-rim',
        area:['450px', 'auto'],

        content: ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
           + html
            +'</div>'
            +'</div>'

              +'</div>'
        ,
        btn:['保存','取消'],
        btn1: function (index,layero) {
            var packageid = $("#packageid").val();
              $.post('{{ route('sellers.setpackage') }}',{_token:'{{ @csrf_token() }}', shop_id:shop_id,packageid:packageid}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });

            },'json');
        },
        btn2:function (index,layero) {
             layer.close(index);
        }

    });



     }
     function show_view(shop_id,view_inc_num,view_base_num) {


          var content = ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 基础访问量：</span>'
            +'<input id="base_num" type="text" value="'+view_base_num+'" class="form-control" placeholder="基础访问量">'
            +'</div>'
            +'</div>'

               +'<div class="col-sm-12" style="margin-top:3px;">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 每日递增量：</span>'
            +'<input id="inc_num" type="text" value="'+view_inc_num+'" class="form-control" placeholder="每日递增">'
            +'</div>'
            +'</div>'

              +'</div>';

            layer.open({

        type: 1,
        title:'访问量',
        skin:'layui-layer-rim',
        area:['450px', 'auto'],

        content: content,
        btn:['保存','取消'],
        btn1: function (index,layero) {
            var inc_num = $("#inc_num").val();
            var base_num = $("#base_num").val();

             $.post('{{ route('sellers.setviews') }}',{_token:'{{ @csrf_token() }}', shop_id:shop_id,inc_num:inc_num,base_num:base_num}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });
                },'json');

        },
        btn2:function (index,layero) {
             layer.close(index);
        }

    });







            return false;

            layer.prompt({

              title: "访问量", //提示框标题

              value: views, //初始时的值，默认空字符

            },function(value, index, elem){

              $.post('{{ route('sellers.setviews') }}',{_token:'{{ @csrf_token() }}', shop_id:shop_id,view_inc_num:view_inc_num}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });

            },'json');

              layer.close(index);

            });

        }

    /**
         * 修改以后信用分
         * @param shop_id
         * @param view_inc_num
         * @param view_base_num
         */
        function update_creditscore(seller_id) {


          var content = ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'
            +'<div class="col-sm-12">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 信用分：</span>'
            +'<input id="creditscore" type="text" value="" class="form-control" placeholder="信用分">'
            +'</div>'
            +'</div>'

            +'<div class="col-sm-12" style="margin-top:3px;">'
            +'<div class="input-group">'
            +'<span class="input-group-addon"> 备  注：</span>'
            +'<input id="remark" type="text" value="" class="form-control" placeholder="备注">'
            +'</div>'
            +'</div>'

            +'</div>';

            layer.open({
                type: 1,
                title:'信用分',
                skin:'layui-layer-rim',
                area:['450px', 'auto'],

                content: content,
                btn:['保存','取消'],
                btn1: function (index,layero) {
                    var creditscore = $("#creditscore").val();
                    var remark = $("#remark").val();
                    //alert(creditscore);
                     $.post('{{ route('sellers.updatecreditscore') }}',{_token:'{{ @csrf_token() }}', seller_id:seller_id,seller_score:creditscore,seller_remark:remark}, function(data){
                        layer.msg(data.msg,function(){
                            location.reload();
                        });
                        },'json');

                },
                btn2:function (index,layero) {
                     layer.close(index);
                }
            });

            return false;

            layer.prompt({

              title: "信用分", //提示框标题

              value: views, //初始时的值，默认空字符

            },function(value, index, elem){

              $.post('{{ route('sellers.updatecreditscore') }}',{_token:'{{ @csrf_token() }}', seller_id:seller_id,seller_score:creditscore,seller_remark:remark}, function(data){
                layer.msg(data.msg,function(){
                    location.reload();
                });

            },'json');

              layer.close(index);

            });

        }

        function show_chat_modal(receiver_id) {
            $('#receiver_id').val(receiver_id);
            $('#chat_modal').modal('show');
        }

        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function show_seller_payment_modal(id){
            $.post('{{ route('sellers.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#payment_modal #payment-modal-content').html(data);
                $('#payment_modal').modal('show', {backdrop: 'static'});
                $('.demo-select2-placeholder').select2();
            });
        }

        function show_seller_profile(id){
            $.post('{{ route('sellers.profile_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#profile_modal #profile-modal-content').html(data);
                $('#profile_modal').modal('show', {backdrop: 'static'});
            });
        }

        function update_approved(el) {
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }

            $.post('{{ route('sellers.approved') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Approved sellers updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_comment_permission(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('sellers.comment_permission') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Comment permission sellers updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_home_display(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('sellers.home_display') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Home display sellers updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_sellers(el){
            return false
            $('#sort_sellers').submit();
        }

        function confirm_ban(url)
        {
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href' , url);
        }

        function confirm_unban(url)
        {
            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href' , url);
        }

        function bulk_delete() {
            var data = new FormData($('#sort_sellers')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-seller-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }



    </script>
@endsection
