@extends('backend.layouts.app')

@section('content')

<div class="col-lg-7 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Set Meal Information')}}</h5>
        </div>
        <form action="{{ route('product_set_meal.update', $row->id) }}" method="POST">
            @csrf
            <input name="_method" type="hidden" value="PATCH">
            <div class="card-body">
                <div class="form-group row mb-3">
                    <label class="col-sm-3 control-label" for="category_id">{{translate('Category')}}</label>
                    <div class="col-sm-9">
                        <select name="category_id" id="category_id" class="form-control aiz-selectpicker" required data-placeholder="{{ translate('Choose Category') }}" data-live-search="true" data-selected-text-format="count" onchange="changeCategory()">
                            @foreach(\App\Models\Category::orderBy('id', 'desc')->select(["id", "name"])->get() as $category)
                                <option value="{{$category->id}}" {{$row->category_id == $category->id ? 'selected' : ''}}>{{ $category->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-3">
                    <label class="col-sm-3 control-label" for="products">{{translate('Products')}}</label>
                    <div class="col-sm-9 products-selector">
                        <select name="product_ids[]" id="product_ids" class="form-control aiz-selectpicker" multiple required data-placeholder="{{ translate('Choose Products') }}" data-live-search="true" data-selected-text-format="count">
                            @if(!empty($row->category_id))
                                @foreach(\App\Models\Product::where('category_id', $row->category_id)->orderBy('id', 'desc')->select(["id", "name", 'unit_price'])->get() as $product)
                                    <option value="{{$product->id}}" {{!empty($row->product_ids) && is_array($row->product_ids) && in_array($product->id, $row->product_ids) ? 'selected' : ''}}>({{single_price($product->unit_price)}}) {{ $product->getTranslation('name') }}</option>
                                @endforeach
                            @endif
                        </select>

                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Total Stock')}}</label>
                    <div class="col-md-9">
                        <input type="number" placeholder="{{translate('Total Stock')}}" id="stock" name="stock" class="form-control" value="{{$row->stock}}" required>
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
            $.post('{{ route('product_set_meal.products') }}', {_token:'{{ csrf_token() }}', category_id: $("#category_id").val()}, function(data){
                $('.products-selector').html(data);
                $(".aiz-selectpicker").selectpicker("refresh");
            });
        }
    </script>
@endsection
