<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title h6">{{translate('Review')}}</h5>
            <button type="button" class="close" data-dismiss="modal">
            </button>
        </div>
        <form action="{{ route('reviews.store') }}"  enctype="multipart/form-data" method="POST">
            <div id="product-review-modal-content">
                @csrf
                <input type="hidden" name="product_id" value="{{$product->id}}">
                <input type="hidden" name="user_id" value="{{$user_id}}">
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="opacity-60">{{ translate('Product')}}</label>
                    <p>{{ $product->getTranslation('name') }}</p>
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

    </div>
</div>
