@extends('seller.layouts.app')

@section('panel_content')

    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Products') }}</h1>
        </div>
      </div>
    </div>

    <div class="row gutters-10 justify-content-center">
        @if (addon_is_activated('seller_subscription'))
            <div class="col-md-4 mx-auto mb-3" >
                <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x text-white"></i>
                  </span>
                  <div class="px-3 pt-3 pb-3">
                      <div class="h4 fw-700 text-center">{{ max(0, $package->product_upload_limit - auth()->user()->products()->count()) }}</div>
                      <div class="opacity-50 text-center">{{  translate('Remaining Uploads') }}</div>
                  </div>
                </div>
            </div>
        @endif

        <div class="col-md-4 mx-auto mb-3" >
            <a href="{{ route('seller.products.create')}}">
              <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                  <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                      <i class="las la-plus la-3x text-white"></i>
                  </span>
                  <div class="fs-18 text-primary">{{ translate('Add New Product') }}</div>
              </div>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Products') }}</h5>
            </div>
            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()"> {{translate('Delete selection')}}</a>
                </div>
            </div>
            <div class="col-md-auto">
                <form class="" id="sort_products" action="" method="GET">
                    <div class="row gutters-5 align-items-center">
                        {{-- <div class="col-auto">
                            <select class="form-control form-control-sm aiz-selectpicker" data-live-search="true" name="brand_id" onchange="sort_products()">
                                <option value="">{{ translate('Brands') }}</option>
                                @foreach (App\Models\Brand::all() as $key => $brand)
                                    <option value="{{ $brand->id }}" @if ($brand->id == $brand_id) selected @endif>
                                        {{ $brand->getTranslation('name') }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-auto">
                            <input type="text" class="form-control form-control-sm" id="search" name="search" @isset($search) value="{{ $search }}" @endisset placeholder="{{ translate('Search product') }}">
                        </div>
                        <div class="col-auto pl-0">
                            <button class="btn btn-primary btn-sm" type="submit">{{ translate('Search') }}</button>
                        </div>
                    </div>
                </form>
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
                        <th>#</th>
                        <th data-breakpoints="md">
                            {{translate('Thumbnail Image')}}
                        </th>
                        <th width="30%">{{ translate('Name')}}</th>
                        <th data-breakpoints="md">{{ translate('Category')}}</th>
                        <th data-breakpoints="md">{{ translate('Current Qty')}}</th>
                        <th data-breakpoints="md">{{ translate('Pick Up Price')}}</th>
                        <th>{{ translate('Base Price')}}</th>
                        <th>{{ translate('Profit')}}</th>
                        @if(get_setting('product_approve_by_admin') == 1)
                            <th data-breakpoints="md">{{ translate('Approval')}}</th>
                        @endif
                        <th data-breakpoints="md">{{ translate('Published')}}</th>
                        <th data-breakpoints="md">{{ translate('Featured')}}</th>
                        <th data-breakpoints="md" class="text-center">{{ translate('Options')}}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($products as $key => $product)
                        <tr>
                            <td>
                                <div class="form-group d-inline-block">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{$product->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </td>
                            <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                            <td>
                                <img
                                    width="80px"
                                    class="lazyload"
                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                    alt="{{  $product->getTranslation('name')  }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                >
                            </td>
                            <td class="name-td">
                                <a href="{{ route('product', $product->slug) }}" target="_blank" class="text-reset" style="word-wrap: break-word;">
                                    {{ $product->getTranslation('name') }}
                                </a>
                            </td>
                            <td>
                                @if ($product->category != null)
                                    {{ $product->category->getTranslation('name') }}
                                @endif
                            </td>
                            <td>
                                @php
                                    $qty = 0;
                                    foreach ($product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                    }
                                    echo $qty;
                                @endphp
                            </td>
                            <td>{{ $product->origin->unit_price }}</td>
                            <td>{{ $product->unit_price }}</td>
                            <td>{{ $product->unit_price - $product->origin->unit_price }}</td>
                            @if(get_setting('product_approve_by_admin') == 1)
                                <td>
                                    @if ($product->approved == 1)
                                        <span class="badge badge-inline badge-success">{{ translate('Approved')}}</span>
                                    @else
                                        <span class="badge badge-inline badge-info">{{ translate('Pending')}}</span>
                                    @endif
                                </td>
                            @endif
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->published == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->seller_featured == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
		                      <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{route('seller.products.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')])}}" title="{{ translate('Edit') }}">
		                          <i class="las la-edit"></i>
		                      </a>
                              <a href="{{route('seller.products.duplicate', $product->id)}}" class="btn btn-soft-success btn-icon btn-circle btn-sm"  title="{{ translate('Duplicate') }}">
    							   <i class="las la-copy"></i>
    						  </a>
                              <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('seller.products.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                  <i class="las la-trash"></i>
                              </a>
                          </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $products->links() }}
          	</div>
        </div>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
    <!-- 选择直通车 -->
    <div class="modal fade" id="select_payment_type_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form class="" id="package_payment_form" action="{{ route('seller.products.spread') }}" method="post">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ translate('Select Spread Package') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product_id" name="product_id" value="">
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Spread Package')}}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control aiz-selectpicker" name="seller_spread_package_payment_id"
                                        data-minimum-results-for-search="Infinity" required>
                                        <option value="">{{ translate('Select One')}}</option>
                                        @foreach ($seller_spread_packages_payments as $key => $seller_spread_packages_payment)
                                            <option value="{{$seller_spread_packages_payment['id']}}">{{ $seller_spread_packages_payment['seller_spread_package']['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-sm btn-primary transition-3d-hover mr-1" id="select_type_cancel" data-dismiss="modal">{{translate('Cancel')}}</button>
                            <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('Confirm')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>



@endsection

@section('script')
    <style>
        .dropdown-item:hover {
            color: #212529 !important;
        }
    </style>
    <script type="text/javascript">
        function sort_products(){
            $('#sort_products').submit();
        }

        function bulk_delete() {
            var selected = [];
            $('.check-one:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                AIZ.plugins.notify('warning', '{{ translate('Please select at least one product') }}');
                return;
            }

            var data = {_token:'{{ csrf_token() }}', ids: selected};
            $.post('{{ route('seller.products.bulk-destroy') }}', data, function(response){
                if(response == 1) {
                    location.reload();
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        $(document).on('change', '.check-all', function() {
            $('.check-one').prop('checked', $(this).is(':checked'));
        });

        function update_featured(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('seller.products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                    location.reload();
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
            $.post('{{ route('seller.products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                } else if (data == 3) {
                    AIZ.plugins.notify('warning', '{{ sprintf(translate('Up to %s items can be removed from shelves in a single day'), get_max_off_shelf_num()) }}');
                    el.checked = 1;
                } else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                    location.reload();
                }
            });
        }

        function toJump(){
            AIZ.plugins.notify('danger', '请开通直通车');
            window.location.href = "{{route('seller.seller_spread_packages_list')}}"
        }

        function select_spread_package(product_id){
            $('input[name=product_id]').val(product_id);
            $('#select_payment_type_modal').modal('show');
        }

        $(document).ready(function () {
            @if(!is_pc())
            setTimeout(function () {
                $(".name-td").css("display", "flex").find("a").css("width", "100px");
            }, 2e3);
            @endif
        })
    </script>
@endsection
