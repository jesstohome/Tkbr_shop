@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Product Collect') }}</h1>
        </div>
    </div>
</div>

@if(!$hasApiKey)
<div class="card">
    <div class="card-body text-center py-5">
        <i class="las la-exclamation-circle la-4x text-warning mb-3"></i>
        <h4>{{ translate('万邦 API 未配置') }}</h4>
        <p class="text-muted">
            请先在 <strong>系统设置 → 常规设置</strong> 中填入 <strong>"万邦Key"</strong> 和 <strong>"lazada-secret"</strong>。<br>
            注册地址: <a href="https://www.onebound.cn" target="_blank">https://www.onebound.cn</a>，注册后可免费试用。
        </p>
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Search Products') }}</h5>
    </div>
    <div class="card-body">
        <form id="search-form" class="mb-3">
            <div class="row gutters-5 align-items-end">
                <div class="col-md-2">
                    <label class="fs-12 text-muted">{{ translate('Platform') }}</label>
                    <select class="form-control form-control-sm aiz-selectpicker" id="platform" name="platform">
                        @foreach($platforms as $key => $p)
                            <option value="{{ $key }}">{{ $p['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2" id="nation-col">
                    <label class="fs-12 text-muted">{{ translate('Country') }}</label>
                    <select class="form-control form-control-sm aiz-selectpicker" data-live-search="true" id="nation" name="nation">
                        <option value="">{{ translate('Select Country') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="fs-12 text-muted">{{ translate('Keyword') }}</label>
                    <input type="text" class="form-control form-control-sm" id="keyword" name="keyword"
                           placeholder="{{ translate('Enter English keyword, e.g. dress, phone case') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm btn-block" id="search-btn">
                        <i class="las la-search"></i> {{ translate('Search') }}
                    </button>
                </div>
            </div>
        </form>
        <div class="text-muted fs-12">
            <i class="las la-info-circle"></i>
            <span style="color:#28a745;">测试可用: 淘宝 / 1688 (免费权限)</span> &nbsp;|&nbsp;
            跨境平台: Shopee / AliExpress / Amazon 等 (需购买API权限)
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0 h6">
            <span id="result-title">{{ translate('Results') }}</span>
            <span id="result-count" class="text-muted ml-2"></span>
        </h5>
    </div>
    <div class="card-body">
        <div id="loading" class="text-center py-5" style="display:none;">
            <i class="las la-spinner la-spin la-3x"></i>
            <p class="mt-2 text-muted">{{ translate('Searching...') }}</p>
        </div>
        <div id="no-results" class="text-center py-5">
            <i class="las la-search la-3x text-muted"></i>
            <p class="mt-2 text-muted">{{ translate('Select a platform, enter a keyword, and start searching') }}</p>
        </div>
        <div id="results-container" style="display:none;">
            <div class="row gutters-5" id="product-list"></div>
            <div class="mt-4 text-center">
                <button class="btn btn-outline-primary btn-sm" id="prev-page" style="display:none;"><i class="las la-angle-left"></i> {{ translate('Prev') }}</button>
                <span id="page-info" class="mx-3 fs-14"></span>
                <button class="btn btn-outline-primary btn-sm" id="next-page" style="display:none;">{{ translate('Next') }} <i class="las la-angle-right"></i></button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Import Modal -->
<div class="modal fade" id="import-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ translate('Import Product') }}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="import-num-iid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ translate('Category') }} <span class="text-danger">*</span></label>
                            <select class="form-control aiz-selectpicker" data-live-search="true" id="import-category">
                                <option value="">{{ translate('Choose category') }}</option>
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
                    <div class="col-md-6">
                        <p class="fs-13 text-muted mt-4">
                            {{ translate('Product will be imported with:') }}<br>
                            - {{ translate('Images downloaded to local storage') }}<br>
                            - {{ translate('Brand auto-created if exists') }}<br>
                            - {{ translate('Published = No (need manual review)') }}<br>
                            - {{ translate('Source = platform name for tracking') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" class="btn btn-primary" id="confirm-import">{{ translate('Import Now') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    var currentPage = 1;
    var currentPlatform = '';

    // 国家联动
    var nationData = @json($nations);

    function updateNations(platform) {
        var sel = $('#nation');
        sel.empty();
        sel.append('<option value="">{{ translate("Select Country") }}</option>');

        var nations = nationData[platform];
        if (nations) {
            $('#nation-col').show();
            $.each(nations, function(code, name) {
                sel.append('<option value="'+code+'">'+name+' ('+code+')</option>');
            });
        } else {
            $('#nation-col').hide();
        }
        sel.selectpicker('refresh');
    }

    $('#platform').on('change', function() {
        updateNations($(this).val());
    });

    // 初始化
    updateNations($('#platform').val());

    function renderProducts(products, platform) {
        var html = '';
        if (!products || products.length === 0) {
            html = '<div class="col-12 text-center py-5 text-muted">{{ translate("No results, try different keyword") }}</div>';
        } else {
            products.forEach(function(p) {
                var name = p.title || '';
                if (name.length > 60) name = name.substring(0, 60) + '...';
                var priceStr = p.price ? '$' + parseFloat(p.price).toFixed(2) : 'N/A';

                html += '<div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6 mb-3">' +
                    '<div class="border rounded h-100 bg-white d-flex flex-column">' +
                        '<a href="'+(p.detail_url || '#')+'" target="_blank">' +
                            '<img src="'+(p.pic_url || '{{ static_asset("assets/img/placeholder.jpg") }}')+'" ' +
                                 'class="w-100" style="height:170px;object-fit:cover;" ' +
                                 'onerror="this.onerror=null;this.src=\'{{ static_asset("assets/img/placeholder.jpg") }}\';">' +
                        '</a>' +
                        '<div class="p-2 flex-grow-1 d-flex flex-column justify-content-between">' +
                            '<div>' +
                                '<small class="d-block text-truncate-2 mb-1" style="line-height:1.3;height:2.6em;font-size:12px;">'+name+'</small>' +
                                '<span class="text-danger fw-600 fs-14">'+priceStr+'</span>' +
                            '</div>' +
                            '<div>' +
                                '<small class="text-muted d-block fs-11">ID: '+(p.num_iid || '-')+'</small>' +
                                '<small class="text-muted d-block fs-11">{{ translate("Sales") }}: '+(p.sales || 0)+'</small>' +
                                '<button class="btn btn-sm btn-primary btn-block mt-1" onclick="openImport(\''+p.num_iid+'\')">' +
                                    '<i class="las la-download"></i> {{ translate("Import") }}' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
        }
        $('#product-list').html(html);
    }

    function doSearch(page) {
        var platform = $('#platform').val();
        var keyword = $('#keyword').val().trim();
        var nation = $('#nation').val();

        if (!keyword) {
            AIZ.plugins.notify('warning', '{{ translate("Please enter keyword") }}');
            return;
        }
        if ($('#nation-col').is(':visible') && !nation) {
            AIZ.plugins.notify('warning', '{{ translate("Please select country") }}');
            return;
        }

        currentPlatform = platform;
        currentPage = page || 1;

        $('#results-container').hide();
        $('#no-results').hide();
        $('#loading').show();
        $('#search-btn').prop('disabled', true);

        $.ajax({
            url: '{{ route("onebound.collect.search") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                platform: platform,
                keyword: keyword,
                nation: nation,
                page: currentPage,
            },
            success: function(resp) {
                $('#loading').hide();
                if (resp.error) {
                    var msg = resp.error;
                    if (resp._debug_response) {
                        msg += '<br><small style="font-size:11px;">API返回: ' + resp._debug_response + '</small>';
                    }
                    AIZ.plugins.notify('danger', msg);
                    return;
                }
                $('#result-title').text(platform.charAt(0).toUpperCase()+platform.slice(1)+' - "'+keyword+'"');
                $('#result-count').text('{{ translate("Total") }}: '+ (resp.total || 0));
                $('#results-container').show();
                $('#no-results').hide();
                renderProducts(resp.products, platform);

                // Pagination
                $('#page-info').text('{{ translate("Page") }} ' + currentPage);
                $('#prev-page').toggle(currentPage > 1);
                $('#next-page').toggle(resp.products && resp.products.length >= 20);
            },
            error: function(xhr) {
                $('#loading').hide();
                var errMsg = '服务器错误 (HTTP ' + xhr.status + ')';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg += ': ' + xhr.responseJSON.message;
                }
                AIZ.plugins.notify('danger', errMsg);
            },
            complete: function() {
                $('#search-btn').prop('disabled', false);
            }
        });
    }

    $('#search-form').on('submit', function(e) {
        e.preventDefault();
        doSearch(1);
    });

    $('#prev-page').on('click', function() { if (currentPage > 1) doSearch(currentPage - 1); });
    $('#next-page').on('click', function() { doSearch(currentPage + 1); });

    function openImport(numIid) {
        $('#import-num-iid').val(numIid);
        $('#import-modal').modal('show');
    }

    $('#confirm-import').on('click', function() {
        var numIid = $('#import-num-iid').val();
        var categoryId = $('#import-category').val();

        if (!categoryId) {
            AIZ.plugins.notify('warning', '{{ translate("Please select category") }}');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).text('{{ translate("Importing...") }}');

        $.ajax({
            url: '{{ route("onebound.collect.import") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                platform: currentPlatform,
                num_iid: numIid,
                nation: $('#nation').val(),
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
                AIZ.plugins.notify('danger', xhr.responseJSON?.message || '{{ translate("Import failed") }}');
            },
            complete: function() {
                btn.prop('disabled', false).text('{{ translate("Import Now") }}');
            }
        });
    });
</script>
@endsection
