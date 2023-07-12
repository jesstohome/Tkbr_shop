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
                    <label class="col-sm-3 control-label" for="num">套餐数量</label>
                    <div class="col-sm-9">
                        <input type="number" placeholder="套餐数量" id="num" name="num" class="form-control" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="min_stock">每个套餐里商品随机数量区间</label>
                    <div class="col-md-9">
                        <input type="number" placeholder="最小" id="min_product_num" name="min_product_num" class="form-control d-inline col-5" required> ~
                        <input type="number" placeholder="最大" id="max_product_num" name="max_product_num" class="form-control d-inline col-5" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-12 col-from-label num-tips"></label>
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
            });
        }
    </script>
@endsection
