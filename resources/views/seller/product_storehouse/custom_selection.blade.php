@extends('seller.layouts.app')

@section('panel_content')

    <section class="gry-bg py-4 profile">
        <div class="container-fluid">
            <form class="" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row gutters-10">
                    <div class="col-md-12 w-md-350px w-lg-400px w-xl-500px">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0 h6">{{ translate('Custom Selection')}}</h6>
                            </div>
                            <div class="card-body">
                                <div id="set_meal_name" style="white-space: pre-line;">
                                    {{translate(get_setting('custom_selection_rule'), null, false, true)}}
                                </div>
                                <div class="">
                                    <div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
                                        <textarea name="product_names" rows="15" class="form-control" placeholder="{{translate('Please add supplier products')}}" oninput="checkLines(this)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pos-footer mar-btm">
                            <div class="my-2 my-md-0" style="display: flex;justify-content: space-between;">
                                <span id="product-num">{{translate('Product Number')}}:0</span>
                                <span id="remaining-uploads">{{translate('Remaining uploads')}}:{{max(0, $package->product_upload_limit - auth()->user()->products()->count())}}</span>
                            </div>
                            <div class="d-flex flex-column flex-md-row justify-content-between">
                                <div class="my-2 my-md-0">
                                    <button id="add-selection-btn" type="button" class="btn btn-primary btn-block"
                                            onclick="addPost()">
                                        <span class="spinner-border spinner-border-sm d-none" role="status"
                                              aria-hidden="true"></span>
                                        {{ translate('Add to my product') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        function checkLines(evt) {
            let content = $(evt).val();
            let lines = content.split("\n").length;
            $("#product-num").html("{{translate('Product Number')}}:" + lines);
        }

        function addPost() {
            let addSelectionBtn = $('#add-selection-btn');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('seller.product_storehouse.add')}}",
                type: 'POST',
                data: {
                    // product_names: JSON.stringify($("textarea[name=product_names]").val().split("\n"))
                    product_names: $("textarea[name=product_names]").val().split("\n")
                },
                success: function (data) {
                    if (data.success == 1) {
                        AIZ.plugins.notify('success', data.message ? data.message : '{{ translate('Product has been updated successfully') }}');
                        location.reload();
                    } else if (data.success == 2) {
                        AIZ.plugins.notify('warning', data.message ? data.message : '{{ translate('Due to restrictions on the number of product merchants, some products were not successfully uploaded') }}');
                        setTimeout(function () {
                            location.reload();
                        }, 1000)
                    } else {
                        AIZ.plugins.notify('danger', data.message ? data.message : '{{ translate('Something went wrong') }}');
                    }
                }
            }).fail(function () {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }).always(function () {
                addSelectionBtn.prop('disabled', false);
                addSelectionBtn.find('span.spinner-border').addClass('d-none');
            });;
        }
    </script>
@endsection
