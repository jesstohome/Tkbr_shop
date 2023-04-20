<form action="{{ route('reviews.store') }}"  enctype="multipart/form-data" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{$product->id}}">
    <div class="modal-body">
        <div class="form-group">
            <label class="opacity-60">{{ translate('Product')}}</label>
            <p>{{ $product->getTranslation('name') }}</p>
        </div>
        <div class="form-group">
            <label class="opacity-60">{{translate('Customer')}}</label>
            <div>
                @php
                    $customers = \App\Models\User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->orderBy('created_at', 'desc')->get();
                @endphp
                <select name="user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true">
                    @foreach ($customers as $key => $customer)
                        <option value="{{ $customer->id }}" data-contact="{{ $customer->email }}">
                            {{ $customer->name }} @if($customer->is_virtual == 1) (<font color="red">{{translate('Virtual')}}</font>) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="form-group">
            <label class="opacity-60">{{ translate('Rating')}}</label>
            <div class="rating rating-input">
                <label> <input type="radio" name="rating" value="1" required> <i class="las la-star"></i> </label>
                <label> <input type="radio" name="rating" value="2"> <i class="las la-star"></i> </label> <label>
                    <input type="radio" name="rating" value="3"> <i class="las la-star"></i> </label> <label>
                    <input type="radio" name="rating" value="4"> <i class="las la-star"></i> </label> <label>
                    <input type="radio" name="rating" value="5"> <i class="las la-star"></i> </label>
            </div>
        </div>

        <div class="form-group">
            <label class="opacity-60">{{ translate('Comment')}}</label>
            <textarea class="form-control" rows="4" name="comment" placeholder="{{ translate('Your review')}}" required></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-sm btn-primary">{{translate('Submit review')}}</button>
        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
    </div>
</form>
