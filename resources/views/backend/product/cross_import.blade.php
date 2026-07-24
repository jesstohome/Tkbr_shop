@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">跨系统商品导入</h1>
        </div>
    </div>
</div>

@if(!$configured)
<div class="card">
    <div class="card-header"><h5 class="mb-0 h6 text-danger">数据库连接失败</h5></div>
    <div class="card-body">
        <p>无法连接源数据库，请检查 <code>app/Http/Controllers/CrossImportController.php</code> 中的数据库配置。</p>
    </div>
</div>
@else

{{-- 筛选区 --}}
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">第一步：筛选源系统商品</h5>
    </div>
    <div class="card-body">
        <div class="row gutters-5">
            <div class="col-md-3">
                <select class="form-control aiz-selectpicker" data-live-search="true" id="src-category">
                    <option value="">全部分类</option>
                    @foreach($sourceCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->prefix }}{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" class="form-control" id="src-min-price" placeholder="最低价格">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" class="form-control" id="src-max-price" placeholder="最高价格">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="src-keyword" placeholder="关键词（可选）">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-block btn-sm" id="btn-search" onclick="searchProducts()">
                    <i class="las la-search"></i> 搜索
                </button>
            </div>
        </div>
        <div class="text-muted fs-12 mt-2">
            选择父级分类会包含所有子分类，仅显示原始商品（original_id 为空的内部商品）。
        </div>
    </div>
</div>

{{-- 结果 + 导入区 --}}
<div class="card mt-3">
    <div class="card-header row gutters-5 align-items-center">
        <div class="col">
            <h5 class="mb-0 h6">
                第二步：勾选商品并导入
                <span id="result-count" class="text-muted ml-2"></span>
            </h5>
        </div>
        <div class="col-md-3">
            <select class="form-control aiz-selectpicker" data-live-search="true" id="target-category">
                <option value="">-- 选择目标分类 --</option>
                @foreach($targetCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->getTranslation('name') }}</option>
                    @foreach($cat->childrenCategories as $child)
                        <option value="{{ $child->id }}">-- {{ $child->getTranslation('name') }}</option>
                        @foreach($child->childrenCategories as $sub)
                            <option value="{{ $sub->id }}">---- {{ $sub->getTranslation('name') }}</option>
                        @endforeach
                    @endforeach
                @endforeach
            </select>
        </div>
        <div class="col-md-auto">
            <button class="btn btn-success btn-sm" id="btn-import" onclick="doImport(false)" disabled>
                <i class="las la-download"></i> 导入选中
            </button>
            <button class="btn btn-warning btn-sm ml-1" id="btn-import-all" onclick="doImport(true)" disabled>
                <i class="las la-cloud-download-alt"></i> 导入筛选结果全部
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="loading" class="text-center py-5" style="display:none;">
            <i class="las la-spinner la-spin la-3x text-muted"></i>
            <p class="mt-2 text-muted">正在搜索源系统商品...</p>
        </div>
        <div id="no-results" class="text-center py-5 text-muted">
            选择筛选条件后点击搜索
        </div>

        <div id="results-table" style="display:none;">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="40">
                                <label class="aiz-checkbox mb-0">
                                    <input type="checkbox" class="check-all">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </th>
                            <th width="70">图片</th>
                            <th>商品名称</th>
                            <th>分类</th>
                            <th width="90">价格</th>
                            <th width="80">操作</th>
                        </tr>
                    </thead>
                    <tbody id="product-body"></tbody>
                </table>
            </div>
            <div id="pagination-links" class="mt-3 d-flex justify-content-center"></div>
        </div>
    </div>
</div>

{{-- 导入结果 --}}
<div id="import-progress" class="card mt-3" style="display:none;">
    <div class="card-header"><h5 class="mb-0 h6">导入结果</h5></div>
    <div class="card-body" id="import-result"></div>
</div>
@endif
@endsection

@section('script')
<script>
    var currentPage = 1;

    function searchProducts(page) {
        currentPage = page || 1;
        $('#results-table, #no-results').hide();
        $('#loading').show();
        $('#btn-search').prop('disabled', true);

        $.post('{{ route("cross.import.list") }}', {
            _token: '{{ csrf_token() }}',
            category_id: $('#src-category').val(),
            min_price: $('#src-min-price').val(),
            max_price: $('#src-max-price').val(),
            keyword: $('#src-keyword').val(),
            page: currentPage
        }, function(resp) {
            $('#loading').hide();
            console.log('API返回:', resp);

            var list = resp.data || [];
            var rows = '';
            if (list.length) {
                for (var i = 0; i < list.length; i++) {
                    var r = list[i];
                    var img = '-';
                    if (r.thumb_url && r.thumb_url.length > 15) {
                        img = '<img src="'+r.thumb_url+'" style="width:50px;height:50px;object-fit:cover;">';
                    }
                    rows +=
                        '<tr>'+
                        '<td><label class="aiz-checkbox mb-0"><input type="checkbox" class="check-one" value="'+r.id+'"><span class="aiz-square-check"></span></label></td>'+
                        '<td>'+img+'</td>'+
                        '<td style="max-width:280px;word-break:break-all;">'+(r.name||'')+'</td>'+
                        '<td>'+(r.category_name||'-')+'</td>'+
                        '<td>&yen;'+(parseFloat(r.unit_price)||0).toFixed(2)+'</td>'+
                        '<td><button class="btn btn-xs btn-outline-primary" onclick="importOne('+r.id+')">导入</button></td>'+
                        '</tr>';
                }
            } else {
                rows = '<tr><td colspan="6" class="text-center py-4 text-muted">没有匹配的商品</td></tr>';
            }

            $('#product-body').html(rows);
            $('#results-table').show();
            $('#result-count').text('共 '+(resp.total||0)+' 条');

            var pg = '';
            if (resp.last_page > 1) {
                pg += '<ul class="pagination">';
                for (var j = 1; j <= resp.last_page; j++) {
                    pg += '<li class="page-item '+(j==resp.current_page?'active':'')+'"><a class="page-link" href="#" onclick="searchProducts('+j+');return false;">'+j+'</a></li>';
                }
                pg += '</ul>';
            }
            $('#pagination-links').html(pg);

            $('.check-all').prop('checked', false);
            updateImportBtn();
        }, 'json').fail(function(xhr) {
            $('#loading').hide();
            console.error('搜索失败:', xhr);
            var msg = '搜索失败';
            try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
            AIZ.plugins.notify('danger', msg);
        }).always(function() {
            $('#btn-search').prop('disabled', false);
        });
    }

    $(document).on('change', '.check-all', function() {
        $('.check-one').prop('checked', this.checked);
        updateImportBtn();
    }).on('change', '.check-one', function() {
        updateImportBtn();
    });
    $('#target-category').on('change', function() { updateImportBtn(); });

    function updateImportBtn() {
        var hasTarget = $('#target-category').val() != '';
        $('#btn-import').prop('disabled', !($('.check-one:checked').length && hasTarget));
        $('#btn-import-all').prop('disabled', !hasTarget);
    }

    function importOne(id) {
        $('.check-one').prop('checked', false);
        $('.check-one[value="'+id+'"]').prop('checked', true);
        updateImportBtn();
        doImport(false);
    }

    function doImport(importAll) {
        var ids = [];
        if (!importAll) {
            ids = $('.check-one:checked').map(function(){ return this.value; }).get();
            if (!ids.length) return AIZ.plugins.notify('warning', '请至少勾选一个商品');
        }
        var targetCat = $('#target-category').val();
        if (!targetCat) return AIZ.plugins.notify('warning', '请选择目标分类');

        if (importAll && !confirm('将导入当前筛选条件下的全部商品（跳过已存在的），确认继续？')) return;

        var label = importAll ? '全部筛选结果' : (ids.length + ' 个');
        var btn = importAll ? $('#btn-import-all') : $('#btn-import');
        btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> 导入中...');
        $('#import-progress').show();
        $('#import-result').html('<div class="text-center py-3">正在导入 '+label+' 商品...</div>');

        $.post('{{ route("cross.import.do") }}', {
            _token: '{{ csrf_token() }}',
            ids: ids,
            import_all: importAll ? 1 : 0,
            category_id: $('#src-category').val(),
            min_price: $('#src-min-price').val(),
            max_price: $('#src-max-price').val(),
            keyword: $('#src-keyword').val(),
            target_category_id: targetCat
        }, function(resp) {
            var h = '<div class="alert alert-success mb-0">导入完成！共 <b>'+(resp.total||0)+'</b> 条，成功: <b>'+resp.imported+'</b>，跳过: <b>'+(resp.skipped||0)+'</b>';
            if (resp.errors && resp.errors.length) h += '<br><small class="text-danger">'+resp.errors.slice(0,5).join('; ')+'</small>';
            h += '</div>';
            $('#import-result').html(h);
            if (resp.imported) AIZ.plugins.notify('success', '成功导入 '+resp.imported+' 个');
        }, 'json').fail(function(xhr) {
            var msg = '导入失败';
            try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
            $('#import-result').html('<div class="alert alert-danger">'+msg+'</div>');
            AIZ.plugins.notify('danger', msg);
        }).always(function() {
            btn.prop('disabled', false).html(importAll ? '<i class="las la-cloud-download-alt"></i> 导入筛选结果全部' : '<i class="las la-download"></i> 导入选中');
            updateImportBtn();
        });
    }
</script>
@endsection
