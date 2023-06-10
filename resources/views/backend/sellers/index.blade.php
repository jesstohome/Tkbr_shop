@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Sellers')}}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('shops.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add Virtual Seller')}}</span>
            </a>
        </div>
        @if (isSupperAdmin())
        <div class="ml-auto" style="margin-right: 6px;">
            <button id="create_virtual_sellers" type="button" class="btn btn-outline-primary btn-block">
                批量创建虚拟卖家</button>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <form class="" id="sort_sellers" action="" method="GET">
        <div class="card-header row gutters-5">
            @php
                $salesmans = filter_by_bloc(\App\Models\User::where('user_type', '!=', 'customer'))->orderBy('created_at', 'desc')->get();
            @endphp

            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}">
                </div>
            </div>

            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="is_virtual_user" id="is_virtual_user" onchange="sort_sellers()">
                    <option value="">{{translate('All')}}</option>
                    <option value="1"  @isset($is_virtual_user) @if($is_virtual_user == '1') selected @endif @endisset>{{translate('Virtual Account')}}</option>
                    <option value="0"  @isset($is_virtual_user) @if($is_virtual_user == '0') selected @endif @endisset>{{translate('General Account')}}</option>
                </select>
            </div>

            <div class="col-md-2 ml-auto">
                <select name="salesman_user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="sort_sellers()">
                    <option value="">{{translate('All Ssalesman')}}</option>
                    @foreach ($salesmans as $key => $salesman)
                        <option value="{{ $salesman->id }}" @if($salesman_user_id == $salesman->id) selected @endif data-contact="{{ $salesman->email }}">
                            {{ $salesman->name }} ({{$salesman->email}})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="approved_status" id="approved_status" onchange="sort_sellers()">
                    <option value="">{{translate('Filter by Approval')}}</option>
                    <option value="1"  @isset($approved) @if($approved == 'paid') selected @endif @endisset>{{translate('Approved')}}</option>
                    <option value="0"  @isset($approved) @if($approved == 'unpaid') selected @endif @endisset>{{translate('Non-Approved')}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                  <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name or email & Enter') }}">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-styled">{{ translate('Search') }}</button>
        </div>

        <div class="card-body" style="overflow-x: auto">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>
                        <div class="form-group">
                            <div class="aiz-checkbox-inline">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-all">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                    </th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Phone')}}</th>
                    <th data-breakpoints="lg">{{translate('Email Address')}}</th>
                    <th data-breakpoints="lg">{{translate('Verification Info')}}</th>
                    <th data-breakpoints="lg">{{translate('Approval')}}</th>
                    <th data-breakpoints="lg">{{translate('Responsible sub account')}}</th>
                    <th data-breakpoints="lg">{{ translate('Num. of Products') }}</th>
                    <th data-breakpoints="lg">{{ translate('Pending Balance') }}</th>
                    <th data-breakpoints="lg">{{ translate('Creditscore') }}</th>
                    <th data-breakpoints="lg">{{ translate('Wallet Money') }}</th>
                    <th data-breakpoints="lg">{{ translate('Guarantee Money') }}</th>
                    <th data-breakpoints="lg" style="width:20%;">{{ translate('Views') }}</th>
                    <th data-breakpoints="lg">{{ translate('Comment Permission') }}</th>
                    <th data-breakpoints="lg">{{ translate('Home Display') }}</th>
                    <th data-breakpoints="lg">{{ translate('Created Time') }}</th>
                    <th data-breakpoints="lg">{{ translate('Total recharge') }}</th>
                    <th data-breakpoints="lg">{{ translate('Total withdrawal amount') }}</th>
                    <th data-breakpoints="lg">{{ translate('Recharge difference') }}</th>
                    <th data-breakpoints="lg">{{ translate('Salesman') }}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $total_recharge = '0.00';
                    $total_withdraw_money = '0.00';
                    $total_difference = '0.00';
                @endphp
                @foreach($shops as $key => $shop)
                    <tr>
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{$shop->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>@if($shop->user->banned == 1) <i class="fa fa-ban text-danger" aria-hidden="true"></i> @endif {{$shop->name}} @if($shop->user->is_virtual == 1) (<font color="red">{{translate('Virtual')}}</font>) @endif</td>
                        <td>{{$shop->user->phone}}</td>
                        <td>{{$shop->user->email}}</td>
                        <td>
                            @if ($shop->verification_info != null)
                                <a href="{{ route('sellers.show_verification_request', $shop->id) }}">
                                    <span class="badge badge-inline badge-info">{{translate('Show')}}</span>
                                </a>
                            @endif
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_approved(this)" value="{{ $shop->id }}" type="checkbox" <?php if($shop->verification_status == 1) echo "checked";?> @if($shop->verification_status) disabled @endif>
                                <span class="slider round"></span>
                            </label>
                            @if(hget_plus('new_shop_created_tip', $shop->id))<span class="badge badge-danger badge-circle badge-sm badge-dot"></span> @endif
                        </td>
                        <td>
                            @php
                                if (Auth::user()->user_type == 'staff' && !Auth::user()->staffInfo->role->is_manage) {
                                    $staffs = [Auth::user()->staffInfo];
                                } else {
                                    $staffs = filter_by_bloc(\App\Models\Staff::query())->get();
                                }

                                $admin_ids = [];
                                foreach ($shop->admins as $admin) {
                                    $admin_ids[] = $admin->admin_id;
                                }
                            @endphp
                            <select class="form-control admin_ids" data-max-options="50" data-live-search="true" name="admin_ids[]" data-selected="{{$admin_ids}}" data-seller-id="{{$shop->user->id}}" style="width: 100px" onchange="changeStaff(this)" @if (Auth::user()->user_type == 'staff' && !Auth::user()->staffInfo->role->is_manage || $shop->verification_status) disabled @endif>
                                <option value="">请选择一个负责人</option>
                                @foreach($staffs as $staff)
                                    <option value="{{$staff->id}}" @if($shop->staff_id == $staff->id) selected @endif>{{$staff->user->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{ $shop->user->products->count() }}</td>
                        <td>
                            @if ($shop->admin_to_pay >= 0)
                                {{ single_price($shop->admin_to_pay) }}
                            @else
                                {{ single_price(abs($shop->admin_to_pay)) }} ({{ translate('Due to Admin') }})
                            @endif
                        </td>
                         <td>
                            {{$shop->user->creditscore}}
                        </td>
                        <td  >
                            {{single_price($shop->user->balance)}}
                        </td>
                         <td  >
                            {{single_price($shop->bzj_money)}}
                        </td>

                          <td  >
                          {{translate('base num')}}：{{$shop->view_base_num}}
                            <br>
                           {{translate('inc num')}}：{{$shop->view_inc_num}}
                        </td>


                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_comment_permission(this)" value="{{ $shop->id }}" type="checkbox" <?php if($shop->comment_permission == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_home_display(this)" value="{{ $shop->id }}" type="checkbox" <?php if($shop->home_display == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>{{$shop->created_at}}</td>

                        @php
                            $wallets = $shop->user->wallets;
                            $recharge = 0;
                            foreach ($wallets as $wallet) {
                                if ($wallet->approval==1) $recharge += $wallet->amount;
                            }
                            $withdraws = $shop->user->seller_withdraw_requests;
                            $withdraw_money = '0.00';
                            foreach ($withdraws as $withdraw) {
                                if ($withdraw->status==1) $withdraw_money += $withdraw->amount;
                            }
                            $difference = $recharge - $withdraw_money;
                            $total_recharge += $recharge;
                            $total_withdraw_money += $withdraw_money;
                            $total_difference += $difference;
                        @endphp
                        {{--Total recharge--}}
                        <td>{{single_price($recharge)}}</td>
                        <td>{{single_price($withdraw_money)}}</td>
                        <td>{{single_price($difference)}}</td>
                        <td>
                            @php
                                $uid = $shop->user->pid;
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



                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-circle btn-soft-primary btn-icon dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="las la-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                    <a href="#" onclick="show_seller_profile('{{$shop->id}}');"  class="dropdown-item">
                                        {{translate('Profile')}}
                                    </a>

                                    <a href="{{route('sellers.edit', encrypt($shop->id))}}" class="dropdown-item">
                                        {{translate('Edit')}}
                                    </a>
                                    @if($shop->user->banned != 1)
                                        <a href="#" onclick="confirm_ban('{{route('sellers.ban', $shop->id)}}');" class="dropdown-item">
                                        {{translate('Ban this seller')}}
                                        <i class="fa fa-ban text-danger" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        <a href="#" onclick="confirm_unban('{{route('sellers.ban', $shop->id)}}');" class="dropdown-item">
                                        {{translate('Unban this seller')}}
                                        <i class="fa fa-check text-success" aria-hidden="true"></i>
                                        </a>
                                    @endif


                                     <span onclick="show_view({{$shop->id}},{{$shop->view_inc_num}},{{$shop->view_base_num}}, '{{$shop->view_rand_range}}')" class="dropdown-item" style="cursor:pointer;">
                                        {{translate('Views')}}
                                    </span>

                                     <span onclick="update_creditscore({{$shop->user->id}})" class="dropdown-item" style="cursor:pointer;">
                                        {{translate('Modified Credit Score')}}
                                    </span>

                                    <span onclick="show_package({{$shop->id}},{{$shop->seller_package_id}})" class="dropdown-item" style="cursor:pointer;">
                                        {{translate('Set Package')}}
                                    </span>
                                       <span onclick="set_pid({{$shop->id}},{{$shop->user->pid}})" class="dropdown-item" style="cursor:pointer;">
                                        {{translate('Set Salesman')}}
                                    </span>



                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                @if(count($shops))
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{single_price($total_recharge)}}</td>
                        <td>{{single_price($total_withdraw_money)}}</td>
                        <td>{{single_price($total_difference)}}</td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
                </tbody>
            </table>
            <div class="aiz-pagination">
              {{ $shops->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
	<!-- Delete Modal -->
	@include('modals.delete_modal')

    <div class="modal fade" id="virtual_user_form" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Create Virtual Sellers')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 16px; font-size: 14px; ">
                        <i>{{translate('N:B: You can create virtual sellers here, with a maximum of 100 people')}}</i>
                    </div>
                    <form class="form-horizontal" action="{{ route('shops.create_virtual_sellers') }}" method="POST">
                        <div class="form-group row">
                            <div class="col-lg-2">{{translate('Quantity')}}</div>
                            <div class="col-lg-6">
                                <input type="number" min="1" step="1" max="100" class="form-control" name="quantity" value="1" placeholder="Quantity of generate" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="submitVirtualCustomer" class="btn btn-primary">{{translate('Submit')}}</a>
                </div>
            </div>
        </div>
    </div>

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
        $(function() {
            var params = {
                timePicker:true,
                timePickerSeconds:true,
                timePicker24Hour:true,
                locale:{format:'YYYY-MM-DD HH:mm:ss', cancelLabel: 'Clear'}
            };
            @if(!empty($start_time))
                params.startDate = "{{$start_time}}";
            @endif
            @if(!empty($end_time))
                params.endDate = "{{$end_time}}";
            @endif

            $('input[name="date_range"]').daterangepicker(params).on('cancel.daterangepicker', function(ev, picker) {
                //做点什么，比如清除输入
                $('input[name="date_range"]').val('');
            });

            @if(empty($start_time) && empty($end_time))
            $('input[name="date_range"]').val('');
            @endif

            // 批量创建虚拟卖家
            $( '#create_virtual_sellers' ).bind( 'click', function () {
                $( '#virtual_user_form' ).modal( 'show' );
            });
            $( '#submitVirtualCustomer' ).bind( 'click', function ()
            {
                let target = $( this );
                if ( target.hasClass( 'disabled' ) ) return false;

                target.addClass( 'disabled' );
                let max = $( 'input[name=quantity]' ).val();

                fetch( '{{route('shops.create_virtual_sellers')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'fetch'
                    },
                    body: JSON.stringify( {
                        _token: '{{csrf_token()}}',
                        max,
                    } )
                } ).then( resp => resp.text() ).then( res => {
                    console.log("res=", res);
                        if ( res == 1 ) {
                            AIZ.plugins.notify( 'success', '{{translate('Successfully created virtual seller')}}' )
                            setTimeout( () =>
                            {
                                window.location.reload()
                            }, 500 )
                        }
                        else {
                            AIZ.plugins.notify( 'danger', '{{translate('Executed failure Try again')}}' )
                            target.removeClass( 'disabled' )
                        }
                    } ).catch( err => null ).finally( () => {} )
            } );
        });


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
     function show_view(shop_id,view_inc_num,view_base_num, view_rand_range) {
            var min_default = "{{DEFAULT_VISITS_MIN}}";
            var max_default = "{{DEFAULT_VISITS_MAX}}";
         var view_rand_range = view_rand_range.split('-');
          var content = ' <div class="row" style="width: 420px;  margin-left:7px; margin-top:10px;">'

              +'<div class="col-sm-12" style="margin-top:3px;">'
              +'<div class="input-group">'
              +'<span class="input-group-addon"> 访问量随机秒数1：</span>'
              +'<input id="view_rand_range1" type="text" value="'+ (view_rand_range[0] || min_default)+'" class="form-control" placeholder="访问量随机秒数1">'
              +'</div>'
              +'</div>'

              +'<div class="col-sm-12" style="margin-top:3px;">'
              +'<div class="input-group">'
              +'<span class="input-group-addon"> 访问量随机秒数2：</span>'
              +'<input id="view_rand_range2" type="text" value="'+ (view_rand_range[1] || max_default) +'" class="form-control" placeholder="访问量随机秒数2">'
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
            var view_rand_range = $("#view_rand_range1").val() + '-' + $("#view_rand_range2").val();

             $.post('{{ route('sellers.setviews') }}',{_token:'{{ @csrf_token() }}', shop_id:shop_id,inc_num:inc_num,base_num:base_num, view_rand_range:view_rand_range}, function(data){
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
            var admin_ids = [];
            if(el.checked){
                var status = 1;
                admin_ids = $(el).parents("td").next().find("select.admin_ids").val();
                if (admin_ids.length == 0) {
                    setTimeout(() => {
                        el.checked = false
                    }, 100)
                    AIZ.plugins.notify('danger', '{{ translate('Please select a responsible person') }}');
                    return false;
                }
            }
            else{
                var status = 0;
            }

            $.post('{{ route('sellers.approved') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status, admin_ids: admin_ids}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Approved sellers updated successfully') }}');
                    $(el).parents("td").next().find("select.admin_ids").attr("disabled", true);
                    $(el).attr("disabled", true);
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

        function changeStaff(evt) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('sellers.update-seller-staff')}}",
                type: 'POST',
                data: {
                    seller_id: $(evt).data("seller-id"),
                    staff_id: $(evt).val(),
                },
                success: function (response) {
                    if(response == 1) {
                        AIZ.plugins.notify('success', '{{ translate('updated successfully') }}');
                    }
                }
            });
        }

        $(document).ready(function () {
        });

    </script>
@endsection
