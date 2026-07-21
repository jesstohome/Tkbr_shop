@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('CJdropshipping Product Collect') }}</h1>
        </div>
    </div>
</div>

@if(!$hasApiKey)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="las la-exclamation-circle la-4x text-warning mb-3"></i>
        <h4>{{ translate('CJ API Key 未配置') }}</h4>
        <p class="text-muted">
            请先在 <strong>系统设置 → 常规设置</strong> 中找到 <strong>"CJ API Key"</strong> 字段，填入你的 CJ API Key。<br>
            CJ API Key 获取方式：登录 CJdropshipping 后台 → 个人中心 → API → 添加 API → 选择 "API Key" 类型 → 复制完整 Key。
        </p>
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Search CJ Products') }}</h5>
    </div>
    <div class="card-body">
        <form id="cj-search-form" class="mb-3">
            <div class="row gutters-5">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="keyword" name="keyword"
                           placeholder="{{ translate('Enter keyword to search CJ products') }}">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" id="startPrice" name="startPrice"
                           placeholder="{{ translate('Min Price ($)') }}">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" id="endPrice" name="endPrice"
                           placeholder="{{ translate('Max Price ($)') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block" id="search-btn">
                        <i class="las la-search"></i> {{ translate('Search') }}
                    </button>
                </div>
                <div class="col-md-2">
                    <span class="text-muted fs-12">
                        {{ translate('Price range is optional') }}
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Search Results') }}
            <span id="result-count" class="text-muted ml-2"></span>
        </h5>
    </div>
    <div class="card-body">
        <div id="loading" class="text-center py-5" style="display:none;">
            <i class="las la-spinner la-spin la-3x"></i>
            <p class="mt-2">{{ translate('Searching CJ products...') }}</p>
        </div>

        <div id="no-results" class="text-center py-5" style="display:none;">
            <i class="las la-search la-3x text-muted"></i>
            <p class="mt-2 text-muted">{{ translate('Enter a keyword and click Search') }}</p>
        </div>

        <div id="results-container" style="display:none;">
            <div class="row gutters-5" id="product-list"></div>

            <div class="mt-4 text-center" id="pagination"></div>
        </div>
    </div>
</div>
@endif

<!-- Import Modal -->
<div class="modal fade" id="import-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Import Product') }}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="import-pid">
                <div class="form-group">
                    <label>{{ translate('Select Category') }} <span class="text-danger">*</span></label>
                    <select class="form-control aiz-selectpicker" data-live-search="true" id="import-category">
                        <option value="">{{ translate('Choose a category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                            @foreach($category->childrenCategories as $child)
                                <option value="{{ $child->id }}">-- {{ $child->getTranslation('name') }}</option>
                                @foreach($child->childrenCategories as $subChild)
                                    <option value="{{ $subChild->id }}">---- {{ $subChild->getTranslation('name') }}</option>
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirm-import">{{ translate('Import') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    var currentPage = 1;
    var totalPages = 1;

    function renderProducts(products) {
        var html = '';
        if (products.length === 0) {
            html = '<div class="col-12 text-center py-5 text-muted">{{ translate("No products found") }}</div>';
        }
        products.forEach(function(p) {
            var priceHtml = '<span class="text-danger fw-600">$' + (p.sellPrice || 'N/A') + '</span>';
            if (p.nowPrice && p.nowPrice != p.sellPrice) {
                priceHtml = '<span class="text-muted text-line-through mr-1">$' + p.sellPrice + '</span>' +
                            '<span class="text-danger fw-600">$' + p.nowPrice + '</span>';
            }

            var name = p.name || '';
            if (name.length > 80) name = name.substring(0, 80) + '...';

            html += '<div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 mb-3">' +
                '<div class="border rounded h-100 bg-white d-flex flex-column">' +
                    '<a href="' + (p.image || '') + '" target="_blank">' +
                        '<img src="' + (p.image || '{{ static_asset("assets/img/placeholder.jpg") }}') + '" ' +
                             'class="w-100" style="height:180px;object-fit:cover;" ' +
                             'onerror="this.onerror=null;this.src=\'{{ static_asset("assets/img/placeholder.jpg") }}\';" ' +
                             'alt="' + p.name + '">' +
                    '</a>' +
                    '<div class="p-2 flex-grow-1 d-flex flex-column justify-content-between">' +
                        '<div>' +
                            '<small class="text-muted d-block text-truncate-2 mb-1" style="line-height:1.3;height:2.6em;">' + name + '</small>' +
                            '<div class="mb-1">' + priceHtml + '</div>' +
                        '</div>' +
                        '<div>' +
                            '<small class="text-muted d-block">SKU: ' + (p.sku || '-') + '</small>' +
                            '<small class="text-muted d-block">{{ translate("Stock") }}: ' + (p.inventoryNum || 0) + ' | {{ translate("Sold") }}: ' + (p.listedNum || 0) + '</small>' +
                            '<button class="btn btn-sm btn-primary btn-block mt-1" onclick="openImport(\'' + p.pid + '\', \'' + p.name.replace(/'/g, "\\'") + '\')">' +
                                '<i class="las la-download"></i> {{ translate("Import") }}' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });
        $('#product-list').html(html);
    }

    function renderPagination(page, pages, total) {
        $('#result-count').text('{{ translate("Total") }}: ' + total);

        if (pages <= 1) {
            $('#pagination').html('');
            return;
        }

        var html = '<div class="btn-group">';
        html += '<button class="btn btn-sm btn-outline-primary" onclick="searchPage(' + (page - 1) + ')" ' + (page <= 1 ? 'disabled' : '') + '>{{ translate("Prev") }}</button>';
        html += '<span class="btn btn-sm disabled">' + page + ' / ' + pages + '</span>';
        html += '<button class="btn btn-sm btn-outline-primary" onclick="searchPage(' + (page + 1) + ')" ' + (page >= pages ? 'disabled' : '') + '>{{ translate("Next") }}</button>';
        html += '</div>';
        $('#pagination').html(html);
    }

    function searchPage(page) {
        currentPage = page;
        doSearch();
    }

    function doSearch() {
        var keyword = $('#keyword').val().trim();
        if (!keyword) {
            AIZ.plugins.notify('warning', '{{ translate("Please enter a keyword") }}');
            return;
        }

        $('#results-container').hide();
        $('#no-results').hide();
        $('#loading').show();
        $('#search-btn').prop('disabled', true);

        $.ajax({
            url: '{{ route("cj.collect.search") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                keyword: keyword,
                page: currentPage,
                size: 24,
                startSellPrice: $('#startPrice').val(),
                endSellPrice: $('#endPrice').val(),
            },
            success: function(resp) {
                $('#loading').hide();
                if (resp.error) {
                    AIZ.plugins.notify('danger', resp.error);
                    $('#no-results').show();
                    return;
                }
                $('#results-container').show();
                renderProducts(resp.products);
                totalPages = resp.pages || 1;
                renderPagination(currentPage, totalPages, resp.total || 0);
            },
            error: function(xhr) {
                $('#loading').hide();
                var msg = xhr.responseJSON?.error || '{{ translate("Search failed, please try again") }}';
                AIZ.plugins.notify('danger', msg);
            },
            complete: function() {
                $('#search-btn').prop('disabled', false);
            }
        });
    }

    $('#cj-search-form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        doSearch();
    });

    function openImport(pid, name) {
        $('#import-pid').val(pid);
        $('#import-modal').modal('show');
    }

    $('#confirm-import').on('click', function() {
        var pid = $('#import-pid').val();
        var categoryId = $('#import-category').val();

        if (!categoryId) {
            AIZ.plugins.notify('warning', '{{ translate("Please select a category") }}');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).text('{{ translate("Importing...") }}');

        $.ajax({
            url: '{{ route("cj.collect.import") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                pid: pid,
                category_id: categoryId,
            },
            success: function(resp) {
                if (resp.success) {
                    AIZ.plugins.notify('success', resp.message);
                    $('#import-modal').modal('hide');
                } else {
                    AIZ.plugins.notify('danger', resp.message);
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || '{{ translate("Import failed") }}';
                AIZ.plugins.notify('danger', msg);
            },
            complete: function() {
                btn.prop('disabled', false).text('{{ translate("Import") }}');
            }
        });
    });
</script>
@endsection
