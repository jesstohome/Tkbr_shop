@extends('backend.layouts.app')

@section('content')

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Set Meal Information')}}</h5>
        </div>
        <form action="{{ route('product_set_meal.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row mb-3">
                    <label class="col-sm-3 control-label" for="category_id">{{translate('Category')}}</label>
                    <div class="col-sm-9">
                        <select name="category_id" id="category_id" class="form-control aiz-selectpicker" required data-placeholder="{{ translate('Choose Category') }}" data-live-search="true" data-selected-text-format="count" onchange="changeCategory()">
                            <option value="0">{{ translate('Please select a category') }}</option>
                            @foreach(\App\Models\Category::orderBy('id', 'desc')->select(["id", "name"])->get() as $category)
                                <option value="{{$category->id}}">{{ $category->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-3 control-label">套餐名称</label>
                    <div class="col-sm-9">
                        <input type="text" id="meal_name" name="name" class="form-control" placeholder="套餐名称(选择分类后自动预估,可修改)">
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-3 control-label">生成方式</label>
                    <div class="col-sm-9">
                        <label class="mr-4"><input type="radio" name="gen_type" value="1" checked onchange="toggleMode()"> 随机均衡(批量生成)</label>
                        <label class="mr-4"><input type="radio" name="gen_type" value="2" onchange="toggleMode()"> 按价格区间</label>
                        <label><input type="radio" name="gen_type" value="3" onchange="toggleMode()"> 按排序取数</label>
                    </div>
                </div>

                <div id="mode-random">
                    <div class="form-group row mb-3">
                        <label class="col-sm-3 control-label" for="num">套餐数量</label>
                        <div class="col-sm-9">
                            <input type="number" placeholder="套餐数量" id="num" name="num" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label class="col-sm-3 col-form-label" for="min_stock">每个套餐商品随机数量区间</label>
                        <div class="col-sm-9">
                            <input type="number" placeholder="最小" id="min_product_num" name="min_product_num" class="form-control d-inline col-5"> ~
                            <input type="number" placeholder="最大" id="max_product_num" name="max_product_num" class="form-control d-inline col-5">
                        </div>
                    </div>
                </div>

                <div id="mode-price" style="display:none;">
                    <div class="form-group row mb-3">
                        <label class="col-sm-3 col-form-label">价格区间(美元)</label>
                        <div class="col-sm-9">
                            <input type="number" step="0.01" placeholder="最低价" id="range_from" name="price_range_from" class="form-control d-inline col-5"> ~
                            <input type="number" step="0.01" placeholder="最高价" id="range_to" name="price_range_to" class="form-control d-inline col-5">
                            <small class="text-muted d-block mt-1">该价格区间内的仓库商品生成一个套餐,区间不重叠则商品不重复</small>
                        </div>
                    </div>
                </div>

                <div id="mode-sort" style="display:none;">
                    <div class="form-group row mb-3">
                        <label class="col-sm-3 col-form-label">排序取数(按最新上架)</label>
                        <div class="col-sm-9">
                            <input type="number" placeholder="开始序号" id="range_from_sort" name="sort_range_from" class="form-control d-inline col-5"> ~
                            <input type="number" placeholder="结束序号" id="range_to_sort" name="sort_range_to" class="form-control d-inline col-5">
                            <small class="text-muted d-block mt-1">按最新上架排序后,取第N到第M个商品生成一个套餐,区间不重叠则商品不重复</small>
                        </div>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-3 col-form-label">分类统计</label>
                    <div class="col-sm-9">
                        <div>当前分类已有套餐数量:<span class="num-tips badge badge-inline badge-info">0</span> 个</div>
                        <div>当前分类仓库商品数量:<span class="product-tips badge badge-inline badge-info">0</span> 个(只算仓库商品,不含卖家铺货)</div>
                    </div>
                </div>

                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        function changeCategory() {
            $.get('{{ route('product_set_meal.get_has_nums') }}', {_token:'{{ csrf_token() }}', category_id: $("#category_id").val()}, function(data){
                $('.num-tips').html(data);
                // 自动预估套餐名称：下一个字母
                var count = parseInt(data) || 0;
                $('#meal_name').val(String.fromCharCode(65 + count));
            });
            $.get('{{ route('product_set_meal.get_category_product_count') }}', {_token:'{{ csrf_token() }}', category_id: $("#category_id").val()}, function(data){
                $('.product-tips').html(data);
            });
        }

        function toggleMode() {
            var mode = $('input[name=gen_type]:checked').val();
            $('#mode-random').toggle(mode == '1');
            $('#mode-price').toggle(mode == '2');
            $('#mode-sort').toggle(mode == '3');
        }
    </script>
@endsection
