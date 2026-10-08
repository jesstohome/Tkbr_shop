@extends('backend.layouts.app')

@section('content')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('Product Set Meal')}}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('product_set_meal.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Set Meal')}}</span>
            </a>
        </div>
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Product Set Meal') }}</h5>
            </div>

            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" data-live-search="true" id="category_id" name="category_id" onchange="sort_products()">
                    <option value="">{{ translate('All Categories') }}</option>
                    @foreach (App\Models\Category::all() as $key => $category)
                        <option value="{{ $category->id }}" @if ($category->id == $category_id) selected @endif>
                            {{ translate($category->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>
                            <div class="d-flex align-items-center" style="gap:16px;">
                                <input type="checkbox" class="check-all" style="width:16px;height:16px;flex-shrink:0;margin:0;">
                                <a href="#" id="bulk_delete" class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="{{ translate('Delete selection') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </div>
                        </th>
                        <th>{{translate('Name')}}</th>
                        <th data-breakpoints="md">生成类型</th>
                        <th data-breakpoints="md">{{translate('Number of times added')}}</th>
                        <th data-breakpoints="lg">产品范围</th>
                        <th data-breakpoints="lg">{{translate('Total Products In Storehouse')}}</th>
                        <th data-breakpoints="lg">{{translate('Not Added Total Products')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Products')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Set Meal')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Stock')}}</th>
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $key => $row)
                    @if (empty($row->category)) @continue @endif
                    <tr>
                        <td>
                            <div class="form-group d-inline-block">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one ids" name="id[]" value="{{$row->id}}">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($row->category->icon)}}" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $row->category->getTranslation('name') }} - {{$row->name}}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($row->gen_type == 2)
                                <span class="badge badge-inline badge-primary">价格区间</span>
                            @elseif($row->gen_type == 3)
                                <span class="badge badge-inline badge-info">排序区间</span>
                            @else
                                <span class="badge badge-inline badge-success">随机</span>
                            @endif
                        </td>
                        <td>{{$row->added_times}}</td>
                        <td>
                            @if($row->gen_type == 2)
                                ${{$row->range_from}} ~ ${{$row->range_to}}
                            @elseif($row->gen_type == 3)
                                第{{$row->range_from}} ~ {{$row->range_to}}个
                            @else
                                {{$row->min_product_num}} ~ {{$row->max_product_num}}
                            @endif
                        </td>
                        <td>{{\App\Models\Product::query()->where("category_id", $row->category_id)->where("in_storehouse", 1)->count()}}</td>
                        <td>{{$row->uninclude_product_total}}</td>
                        <td>{{count(is_string($row->product_ids) ? json_decode($row->product_ids, true) : $row->product_ids)}}</td>
                        <td>{{\App\Models\ProductSetMeal::query()->where("category_id", $row->category_id)->count()}}</td>
                        <td>{{$row->stock}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('product_set_meal.edit', ['id' => $row->id] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('product_set_meal.destroy', $row->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>

                        </td>
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
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">

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

        // 批量删除(事件委托,兼容表格插件克隆表头)
        $(document).on('click', '#bulk_delete', function (e) {
            e.preventDefault();
            var ids = [];
            $('.check-one:checked').each(function () {
                ids.push($(this).val());
            });
            if (!ids.length) {
                AIZ.plugins.notify('warning', '{{ translate("Please select items") }}');
                return;
            }
            if (!confirm('{{ translate("Are you sure to delete selected items?") }}')) {
                return;
            }
            $.post('{{ route("product_set_meal.bulk_delete") }}', {
                _token: '{{ csrf_token() }}',
                ids: ids
            }, function (data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate("Deleted successfully") }}');
                    setTimeout(function () { location.reload(); }, 500);
                } else {
                    AIZ.plugins.notify('danger', '{{ translate("Something went wrong") }}');
                }
            }).fail(function (xhr) {
                AIZ.plugins.notify('danger', '请求失败(' + xhr.status + '),请检查路由是否已部署');
            });
        });

        function sort_products(el){
            $('#sort_products').submit();
        }

    </script>
@endsection
