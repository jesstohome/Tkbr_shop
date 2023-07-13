@extends('seller.layouts.app')
<style>
    .no-product {
        width: 100%;
        text-align: center;
    }
    .text-truncate {
        text-overflow: inherit !important;
        white-space: break-spaces !important;
    }

    .set_meal_name .badge {
        width: auto;
        margin-left: 3px;
        margin-top: 3px;
    }
</style>
@section('panel_content')

    <section class="gry-bg py-4 profile">
        <div class="container-fluid">
            <form class="" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row gutters-10">
                    <div class="col-md">
                        <div class="row gutters-5 mb-3">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="form-group mb-0">
                                    <input class="form-control form-control-lg" type="text" name="keyword"
                                           placeholder="{{translate('Search by Product Name/Barcode')}}" onkeyup="filterProducts()">
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <select name="poscategory" class="form-control form-control-lg aiz-selectpicker"
                                        data-live-search="true" onchange="filterProducts()">
                                    <option value="">{{ translate('All Categories') }}</option>
                                    @foreach (\App\Models\Category::all() as $key => $category)
                                        <option
                                            value="category-{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-6">
                                <select name="pos_type" class="form-control form-control-lg aiz-selectpicker"
                                        data-live-search="true" onchange="filterProducts()">
                                    <option value="set_meal">{{ translate('Set Meal') }}</option>
                                    <option value="single_item">{{ translate('Single item') }}</option>

                                </select>
                            </div>
                        </div>
                        <div class="aiz-pos-product-list c-scrollbar-light">
                            <div class="d-flex flex-wrap justify-content-center" id="product-list">

                            </div>
                            <div id="load-more" class="text-center">
                                <div class="fs-14 d-inline-block fw-600 btn btn-soft-primary c-pointer"
                                     onclick="loadMoreProduct()">{{ translate('Loading..') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto w-md-350px w-lg-400px w-xl-500px">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div id="set_meal_name" class="set_meal_name"></div>
                                <div class="">
                                    <div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
                                        <ul class="list-group list-group-flush" id="product-selection">
                                            <li class="no-product">{{translate('Please click to select the product')}}</li>
                                        </ul>
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
                                            onclick="addPost(0)">
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

        var products = null;
        var selected_set_meal_id = 0;

        $(document).ready(function () {
            // $('body').addClass('side-menu-closed');
            $('#product-list').on('click', '.add-plus.product:not(.c-not-allowed)', function () {
                var product_id = $(this).data('product-id');
                var product_name = $(this).data('product-name');
                var product_price = $(this).data('product-price');
                var ok = updateSelection(product_id, product_name, product_price);
                if (!ok) {
                    AIZ.plugins.notify('warning', '{{ translate('Some products are duplicated, Do not add again') }}');
                } else {
                    AIZ.plugins.notify('success', '{{ translate('Successfully added, bottom view') }}');
                }
            });

            $('#product-list').on('click', '.add-plus.set-meal:not(.c-not-allowed)', function () {
                var set_meal_id = $(this).data('set-meal-id');
                updateSetMealSelection(set_meal_id);
            });

            filterProducts();
        });

        var loadingSetMealProducts = false;
        var selected_set_meal_ids = [];
        function updateSetMealSelection(set_meal_id) {
            if (loadingSetMealProducts) {
                // 连续点击的情况下，不要重复提示。就出现一个提示，然后等进度条走完，再次点击的话，才会再提示
                if ($(".aiz-notify .progress-bar").length > 0) return;

                AIZ.plugins.notify('warning', '{{translate('Operation too fast')}}');
                return;
            }

            loadingSetMealProducts = true;
            $.ajax( {
                url: "{{route('seller.get_products_by_set_meal')}}",
                type: 'GET',
                data: {
                    id: set_meal_id
                },
                success: function (response) {
                    loadingSetMealProducts = false;
                    if (response.success) {
                        if (response.msg) {
                            AIZ.plugins.notify('warning', response.msg);
                        }

                        var product_ids = '';
                        if (response.products) {
                            product_ids = response.products.map(item => item.id);
                            product_ids = product_ids.join(",")
                        }

                        var meal_item = '<span class="badge badge-primary">\n' +
                            response.set_meal_name +
                            '  <button type="button" class="close" aria-label="Close" onclick="removeMeal(this)" data-set-meal-id="' + set_meal_id + '" data-ids="' + product_ids + '">\n' +
                            '    <span aria-hidden="true">&times;</span>\n' +
                            '  </button>\n' +
                            '</span>';
                        if (selected_set_meal_ids.indexOf(set_meal_id) === -1) {
                            selected_set_meal_ids.push(set_meal_id);
                            $("#set_meal_name").html($("#set_meal_name").html() + meal_item);
                        }

                        if (response.products.length > 0) {
                            var success_num = 0;
                            var repeat_num = 0;
                            response.products.forEach((product) => {
                                var flag = updateSelection(product.id, product.name, product.unit_price, set_meal_id);
                                if (flag) {
                                    success_num++;
                                } else {
                                    repeat_num++;
                                }
                            });
                            if (repeat_num > 0) {
                                if ($(".aiz-notify .progress-bar").length > 0) return;
                                AIZ.plugins.notify('warning', '{{ translate('Some products are duplicated, Do not add again') }}');
                            } else {
                                AIZ.plugins.notify('success', '{{ translate('Successfully added, bottom view') }}');
                            }
                        } else {
                            if ($(".aiz-notify .progress-bar").length > 0) return;
                            AIZ.plugins.notify('warning', '{{ translate('There are no products in the package') }}');
                        }
                    }
                }
            } );
        }

        function removeMeal(evt) {
            var set_meal_id = $(evt).data("set-meal-id");
            var ids = ($(evt).data("ids") + '').split(",");
            $('#product-selection li').each((k, it) => {
                let product_id = $(it).data("product-id");
                if (ids.includes(product_id + '')) {
                    $(it).remove();
                }
            });


            let index = selected_set_meal_ids.indexOf(set_meal_id);
            if (index !== -1) {
                selected_set_meal_ids.splice(index, 1);
            }

            $(evt).parent().remove();

            //  重新计算已添加产品数量
            $("#product-num").html("{{translate('Product Number')}}:" + $('#product-selection li').length);
        }

        function updateSelection(product_id, product_name, product_price, set_meal_id) {
            if (!set_meal_id) set_meal_id = 0;

            selected_set_meal_id = set_meal_id;

            let already_selected_ids = getSelectedIds();
            let container = $('#product-selection');

            if (!already_selected_ids.includes(product_id)) {
                if (container.find(".no-product").length > 0) {
                    container.html('');
                }
                container.append(`<li class="list-group-item py-3 pl-2" data-product-id="${product_id}">
                                            <div class="row gutters-5 align-items-center">

                                                <div class="col">
                                                    <div class="text-truncate-2">${product_name}</div>
                                                    <span
                                                        class="span badge badge-inline fs-12 badge-soft-secondary"></span>
                                                </div>
                                                <div class="col-auto">

                                                    <div class="fs-15 fw-600">${product_price}</div>
                                                </div>
                                                ${set_meal_id ? '' : `<div class="col-auto">
                                                    <button type="button"
                                                            class="btn btn-circle btn-icon btn-sm btn-soft-danger ml-2 mr-0"
                                                            onclick="removeSelected(${product_id})">
                                                        <i class="las la-trash-alt"></i>
                                                    </button>
                                                </div>`}

                                            </div>
                                        </li>`)
                $("#product-num").html("{{translate('Product Number')}}:" + container.find("li").length);
                return true;
            } else {
                container.find("li[data-product-id='" + product_id + "']")
                    .clearQueue().stop()
                    .fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100)
            }

            return false;
        }

        function getSelectedIds() {
            let already_selected_ids = []
            $('#product-selection').find('li').each(function () {
                already_selected_ids.push($(this).data('product-id'))
            })
            return already_selected_ids
        }

        function removeSelected(product_id) {
            $('#product-selection').find("li[data-product-id='" + product_id + "']").remove();
            $("#product-num").html("{{translate('Product Number')}}:" + $('#product-selection').find("li").length);
        }

        function filterProducts() {
            var keyword = $('input[name=keyword]').val();
            var category = $('select[name=poscategory]').val();
            var pos_type = $('select[name=pos_type]').val();
            var api_url = pos_type === 'set_meal' ? '{{ route('seller.product_storehouse.search_set_meal') }}' : '{{ route('seller.product_storehouse.search') }}';
            if (pos_type === 'set_meal') {
                $("#add-all-btn").hide()
            } else {
                $("#add-all-btn").show()
            }

            $.get(api_url, {
                keyword: keyword,
                category: category,
                pos_type: pos_type
            }, function (data) {
                products = data;
                $('#product-list').html(null);
                if (pos_type === 'set_meal') {
                    setSetMealList(data);
                } else {
                    setProductList(data);
                }
            });
        }

        function loadMoreProduct() {
            var pos_type = $('select[name=pos_type]').val();
            if (products != null && products.links.next != null) {
                $('#load-more').find('.btn').html('{{ translate('Loading..') }}');
                @if(env('APP_ENV') != 'local')
                    products.links.next = products.links.next.replace("http:", "https:");
                @endif
                $.get(products.links.next, {}, function (data) {
                    products = data;
                    if (pos_type === 'set_meal') {
                        setSetMealList(data);
                    } else {
                        setProductList(data);
                    }
                });
            }
        }

        // 显示产品
        function setProductList(data) {
            for (var i = 0; i < data.data.length; i++) {
                $('#product-list').append(
                    `<div class="w-130px w-xl-180px w-xxl-210px mx-2">
                        <div class="card bg-white c-pointer product-card hov-container">
                            <div class="position-relative">
                                <span class="absolute-top-left mt-1 ml-1 mr-0">
                                    ${data.data[i].qty > 0
                        ? `<span class="badge badge-inline badge-success fs-13">{{ translate('In stock') }}`
                        : `<span class="badge badge-inline badge-danger fs-13">{{ translate('Out of stock') }}`}
                                    : ${data.data[i].qty}</span>
                                </span>
                                ${data.data[i].variant != null
                        ? `<span class="badge badge-inline badge-warning absolute-bottom-left mb-1 ml-1 mr-0 fs-13 text-truncate">${data.data[i].variant}</span>`
                        : ''}
                                <img src="${data.data[i].thumbnail_image}" class="card-img-top img-fit h-120px h-xl-180px h-xxl-210px mw-100 mx-auto" >
                            </div>
                            <div class="card-body p-2 p-xl-3">
                                <div class="text-truncate fw-600 fs-14 mb-2">${data.data[i].name}</div>
                                <div class="">
                                    ${data.data[i].price != data.data[i].base_price
                        ? `<del class="mr-2 ml-0">${data.data[i].base_price}</del><span>${data.data[i].price}</span>`
                        : `<span>${data.data[i].base_price}</span>`
                    }
                                </div>
                            </div>
                            <div class="add-plus product absolute-full rounded overflow-hidden hov-box ${data.data[i].qty <= 0 ? 'c-not-allowed' : ''}" data-product-id="${data.data[i].id}" data-product-name="${data.data[i].name}"  data-product-price="${data.data[i].price != data.data[i].base_price ? data.data[i].price : data.data[i].base_price}">
                                <div class="absolute-full bg-dark opacity-50">
                                </div>
                                <i class="las la-plus absolute-center la-6x text-white"></i>
                            </div>
                        </div>
                    </div>`
                );
            }
            if (data.links.next != null) {
                $('#load-more').find('.btn').html('{{ translate('Load More.') }}');
            } else {
                $('#load-more').find('.btn').html('{{ translate('Nothing more found.') }}');
            }
        }

        // 显示套餐
        function setSetMealList(data) {
            for (var i = 0; i < data.data.length; i++) {
                $('#product-list').append(
                    `<div class="w-130px w-xl-180px w-xxl-210px mx-2">
                        <div class="card bg-white c-pointer product-card hov-container">
                            <div class="position-relative">
                                <img src="${data.data[i].thumbnail_image}" class="card-img-top img-fit h-120px h-xl-180px h-xxl-210px mw-100 mx-auto" >
                            </div>
                            <div class="card-body p-2 p-xl-3">
                                <div class="text-truncate fw-600 fs-14 mb-2">${data.data[i].name}</div>
                                <div class="">
                                    <span>${data.data[i].min_price} ~ ${data.data[i].max_price}</span>
                                </div>
                            </div>
                            <div class="add-plus set-meal absolute-full rounded overflow-hidden hov-box ${data.data[i].stock <= data.data[i].added_times ? 'c-not-allowed' : ''}" data-set-meal-id="${data.data[i].id}">
                                <div class="absolute-full bg-dark opacity-50">
                                </div>
                                <i class="las la-plus absolute-center la-6x text-white"></i>
                            </div>
                        </div>
                    </div>`
                );
            }
            if (data.links.next != null) {
                $('#load-more').find('.btn').html('{{ translate('Load More.') }}');
            } else {
                $('#load-more').find('.btn').html('{{ translate('Nothing more found.') }}');
            }
        }

        function addPost(all) {
            let addAllBtn = $('#add-all-btn')
            let addSelectionBtn = $('#add-selection-btn')
            if (all == 0) {
                let selected_ids = getSelectedIds()
                if (selected_ids.length) {
                    addAllBtn.prop('disabled', true);
                    addSelectionBtn.prop('disabled', true);
                    addSelectionBtn.find('span.spinner-border').removeClass('d-none');
                    doPost(0, selected_ids)
                }
            } else {
                addAllBtn.prop('disabled', true);
                addSelectionBtn.prop('disabled', true);
                addAllBtn.find('span.spinner-border').removeClass('d-none');
                doPost(1, [])
            }
        }

        function doPost(all, productIds) {
            let addAllBtn = $('#add-all-btn')
            let addSelectionBtn = $('#add-selection-btn')
            $.post('{{ route('seller.product_storehouse.add') }}', {
                _token: AIZ.data.csrf,
                all: all,
                product_ids: productIds,
                set_meal_id: selected_set_meal_id,
                pos_type: $('select[name=pos_type]').val()
            }, function (data) {
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
            }).fail(function () {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }).always(function () {
                addAllBtn.prop('disabled', false);
                addSelectionBtn.prop('disabled', false);
                addAllBtn.find('span.spinner-border').addClass('d-none');
                addSelectionBtn.find('span.spinner-border').addClass('d-none');
            });
        }
    </script>
@endsection
