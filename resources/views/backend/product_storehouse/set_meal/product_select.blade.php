<select name="product_ids[]" id="product_ids" class="form-control aiz-selectpicker" multiple required data-placeholder="{{ translate('Choose Products') }}" data-live-search="true" data-selected-text-format="count">
@if(!empty($category_id))
@foreach(\App\Models\Product::where('category_id', $category_id)->orderBy('id', 'desc')->select(["id", "name"])->get() as $product)
    <option value="{{$product->id}}">{{ $product->getTranslation('name') }}</option>
@endforeach
@endif
</select>
