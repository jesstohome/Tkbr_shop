@extends('backend.layouts.app')

@section('content')

    @php
        CoreComponentRepository::instantiateShopRepository();
        CoreComponentRepository::initializeCache();
    @endphp
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All products')}}</h1>
        </div>
        @if($type != 'Seller')
        <div class="col text-right">
            <a href="{{ route('products.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Product')}}</span>
            </a>
        </div>
        @endif

        @if (isSupperAdmin())
        <div class="ml-auto" style="margin-right: 6px;">
            <button id="generate_shop_product" type="button" class="btn btn-outline-primary btn-block">
                生成店铺产品</button>
        </div>
        @endif
    </div>
</div>
<br>

<div class="card">
    <form class="form" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Product') }}</h5>
            </div>

            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()"> {{translate('Delete selection')}}</a>
                    @if($type == 'In House')
                        <a class="dropdown-item" href="#" onclick="bulk_add_to_storehouse()"> {{translate('Add selection to storehouse')}}</a>
                        <a class="dropdown-item" href="#" onclick="bulk_add_to_storehouse('all')"> {{translate('Add All to storehouse')}}</a>
                    @endif
                </div>
            </div>

            @if($type == 'Seller')
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_id" data-live-search="true" name="user_id" onchange="sort_products()">
                    <option value="">{{ translate('All Sellers') }}</option>
                    @foreach (filter_by_bloc(App\Models\User::where('user_type', '=', 'seller'))->get() as $key => $seller)
                        <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>
                            {{ $seller->name }} ({{ $seller->name }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" data-live-search="true" id="category_id" name="category_id" onchange="sort_products()">
                    <option value="">{{ translate('Categories') }}</option>
                    @foreach (App\Models\Category::all() as $key => $category)
                        <option value="{{ $category->id }}" @if ($category->id == $category_id) selected @endif>
                            {{ $category->getTranslation('name') }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($type == 'All')
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" data-live-search="true" id="user_id" name="user_id" onchange="sort_products()">
                    <option value="">{{ translate('All Sellers') }}</option>
                        @foreach (filter_by_bloc(App\Models\User::where('user_type', '=', 'seller'))->get() as $key => $seller)
                            <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>{{ $seller->name }}</option>
                        @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" name="type" id="type" onchange="sort_products()">
                    <option value="">{{ translate('Sort By') }}</option>
                    <option value="rating,desc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'desc') selected @endif @endisset>{{translate('Rating (High > Low)')}}</option>
                    <option value="rating,asc" @isset($col_name , $query) @if($col_name == 'rating' && $query == 'asc') selected @endif @endisset>{{translate('Rating (Low > High)')}}</option>
                    <option value="num_of_sale,desc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset>{{translate('Num of Sale (High > Low)')}}</option>
                    <option value="num_of_sale,asc"@isset($col_name , $query) @if($col_name == 'num_of_sale' && $query == 'asc') selected @endif @endisset>{{translate('Num of Sale (Low > High)')}}</option>
                    <option value="unit_price,desc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset>{{translate('Base Price (High > Low)')}}</option>
                    <option value="unit_price,asc"@isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset>{{translate('Base Price (Low > High)')}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>

        <div class="card-body">
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
                        <!--<th data-breakpoints="lg">#</th>-->
                        <th>{{translate('Name')}}</th>
                        <th data-breakpoints="lg">{{translate('Category')}}</th>
                    @if($type == 'Seller' || $type == 'All')
                            <th data-breakpoints="lg">{{translate('Added By')}}</th>
                        @endif
                        <th data-breakpoints="sm">{{translate('Info')}}</th>
                        <th data-breakpoints="md">{{translate('Total Stock')}}</th>
                        @if($type != 'Seller') <th data-breakpoints="lg">{{translate('Published')}}</th> @endif
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                            <th data-breakpoints="lg">{{translate('Approved')}}</th>
                        @endif
                        <th data-breakpoints="lg">{{translate('Featured')}}</th>
                        <th data-breakpoints="lg">{{translate('Shop')}}</th>
                        @if($type == 'In House')
                            <th data-breakpoints="lg">{{translate('In storehouse')}}</th>
                        @endif
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $key => $product)
                    <tr>
                        <!--<td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>-->
                        <td>
                            <div class="form-group d-inline-block">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one" name="id[]" value="{{$product->id}}">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
                                </div>
                            </div>
                        </td>
                            <td>{{$product->category ? $product->category->getTranslation('name') : ''}}</td>
                        @if($type == 'Seller' || $type == 'All')
                            <td>{{ $product->user->name }}</td>
                        @endif
                        <td>
                            <strong>{{translate('Num of Sale')}}:</strong> {{ $product->num_of_sale }} {{translate('times')}} </br>
                            <strong>{{translate('Base Price')}}:</strong> {{ single_price($product->unit_price) }} </br>
                            <strong>{{translate('Rating')}}:</strong> {{ $product->rating }} </br>
                        </td>
                        <td>
                            @php
                                $qty = 0;
                                if($product->variant_product) {
                                    foreach ($product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                        echo $stock->variant.' - '.$stock->qty.'<br>';
                                    }
                                }
                                else {
                                    //$qty = $product->current_stock;
                                    $qty = optional($product->stocks->first())->qty;
                                    echo $qty;
                                }
                            @endphp
                            @if($qty <= $product->low_stock_quantity)
                                <span class="badge badge-inline badge-danger">Low</span>
                            @endif
                        </td>
                        @if($type != 'Seller')
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->todays_deal == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @endif
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->published == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_approved(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->approved == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        @endif
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->featured == 1) echo "checked"; ?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                            @if(!empty($product->user->shop->name))
                                <td>{{$product->user->name}} ({{$product->user->shop->name}})</td>
                            @else
                                <td>{{$product->user->name}}</td>
                            @endif
                        @if($type == 'In House')
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_in_storehouse(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->in_storehouse == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        @endif
                        <td class="text-right">
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" style="width: auto"  href="javascript:void(0);" onclick="product_review('{{ $product->id }}')" title="{{ translate('Review') }}">
                                {{ translate('Review') }}
                            </a>
                            <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{ route('product', $product->slug) }}" target="_blank" title="{{ translate('View') }}">
                                <i class="las la-eye"></i>
                            </a>
                            @if ($type == 'Seller')
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.seller.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @else
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('products.admin.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            @endif
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{route('products.duplicate', ['id'=>$product->id, 'type'=>$type]  )}}" title="{{ translate('Duplicate') }}">
                                <i class="las la-copy"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('products.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="generate_shop_product_modal" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Generate Shop Products')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 16px; font-size: 14px; ">
                        <i>提交后，脚本会自动把未上架过产品的分类中的产品，随机导入到店铺，等待后台把产品导入完成后，清理缓存，并在前台对应分类下查看导入的产品</i>
                    </div>
                    <form class="form-horizontal" action="{{ route('products.generate_shop_product') }}" method="POST">
                        <div class="form-group row">
                            <div class="col-lg-2">店铺数量</div>
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

    <!-- Product Review Modal -->
    <div class="modal fade" id="product-review-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Review')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <div id="product-review-modal-content">

                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        function product_review(product_id) {
            $.post('{{ route('product_admin_review_modal') }}', {
                _token: '{{ @csrf_token() }}',
                product_id: product_id
            }, function(data) {
                $('#product-review-modal-content').html(data);
                $('#product-review-modal').modal('show', {
                    backdrop: 'static'
                });
                AIZ.extra.inputRating();
            });
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

        function update_todays_deal(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.todays_deal') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_approved(el){
            if(el.checked){
                var approved = 1;
            }
            else{
                var approved = 0;
            }
            $.post('{{ route('products.approved') }}', {
                _token      :   '{{ csrf_token() }}',
                id          :   el.value,
                approved    :   approved
            }, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Product approval update successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_in_storehouse(el){
            if(el.checked){
                var in_storehouse = 1;
            }
            else{
                var in_storehouse = 0;
            }
            $.post('{{ route('product-storehouse-update') }}', {_token:'{{ csrf_token() }}', id:el.value, in_storehouse:in_storehouse}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Products storehouse updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_products(el){
            $('#sort_products').submit();
        }

        function bulk_delete() {
            var data = new FormData($('#sort_products')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-product-delete')}}",
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

        function bulk_add_to_storehouse(type) {
            var data = new FormData($('#sort_products')[0]);
            data.append('type', type || '');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-product-storehouse-add')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response)
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

        $(document).ready(function () {
            // 触发导入产品
            $( '#generate_shop_product' ).bind( 'click', function () {
                $( '#generate_shop_product_modal' ).modal( 'show' );
            });
            $( '#submitVirtualCustomer' ).bind( 'click', function () {
                let target = $( this );
                if ( target.hasClass( 'disabled' ) ) return false;

                target.addClass( 'disabled' );
                let max = $( 'input[name=quantity]' ).val();

                fetch( '{{route('products.generate_shop_product')}}', {
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
                    if ( res == 1 ) {
                        AIZ.plugins.notify( 'success', '触发成功，等待导入产品' );
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
    </script>
@endsection
