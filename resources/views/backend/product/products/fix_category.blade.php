@extends('backend.layouts.app')

<style>
.fix-category-img { width: 200px; height: 200px; object-fit: cover; border-radius: 4px; border: 1px solid #e0e0e0; }
.fix-category-table td { vertical-align: middle; }
.fix-category-name { max-width: 300px; word-break: break-all; }
</style>

@section('content')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">快速修改分类 - 当前分类ID: {{ $category_id }}</h1>
        </div>
    </div>
</div>

<div class="card">
    <form id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col-auto">
                <select class="form-control form-control-sm aiz-selectpicker" data-live-search="true" id="category_id" name="category_id" onchange="this.form.submit()">
                    <option value="">-- 选择分类 --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @if ($cat->id == $category_id) selected @endif>
                            {{ $cat->getTranslation('name') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <span class="badge badge-info" style="font-size: 14px; padding: 8px 12px;">
                    共 {{ $products->total() }} 条记录
                </span>
            </div>
            <div class="col"></div>
            <div class="col-auto">
                <div class="dropdown">
                    <button class="btn btn-warning dropdown-toggle" type="button" data-toggle="dropdown">
                        批量操作
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="show_change_category_modal()">
                            <i class="las la-exchange-alt"></i> 批量修改分类
                        </a>
                        <a class="dropdown-item text-danger" href="#" onclick="bulk_delete()">
                            <i class="las la-trash"></i> 批量删除
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($products->count() > 0)
            <table class="table aiz-table fix-category-table">
                <thead>
                    <tr>
                        <th width="40">
                            <label class="aiz-checkbox">
                                <input type="checkbox" class="check-all">
                                <span class="aiz-square-check"></span>
                            </label>
                        </th>
                        <th width="220">图片</th>
                        <th>商品名称</th>
                        <th>分类</th>
                        <th>品牌</th>
                        <th>价格</th>
                        <th>创建日期</th>
                        <th width="80">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <label class="aiz-checkbox">
                                <input type="checkbox" class="check-one" name="id[]" value="{{ $product->id }}">
                                <span class="aiz-square-check"></span>
                            </label>
                        </td>
                        <td>
                            <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                 alt="Image"
                                 class="fix-category-img"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </td>
                        <td>
                            <div class="fix-category-name">
                                <strong>{{ $product->getTranslation('name') }}</strong>
                            </div>
                            <small class="text-muted">ID: {{ $product->id }}</small>
                        </td>
                        <td>
                            <span class="badge badge-inline badge-secondary">
                                {{ $product->category ? $product->category->getTranslation('name') : '-' }}
                            </span>
                        </td>
                        <td>{{ $product->brand ? $product->brand->getTranslation('name') : '-' }}</td>
                        <td>{{ single_price($product->unit_price) }}</td>
                        <td>{{ $product->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('products.admin.edit', ['id' => $product->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                               class="btn btn-soft-primary btn-icon btn-circle btn-sm" target="_blank" title="编辑">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="{{ route('products.fix_category.destroy', $product->id) }}"
                               class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                               onclick="return confirm('确认删除该商品？')" title="删除">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <h4 class="text-muted">该分类下暂无商品</h4>
            </div>
            @endif
        </div>
    </form>
</div>

@endsection

@section('modal')
    {{-- 批量修改分类弹窗 --}}
    <div class="modal fade" id="change-category-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">批量修改分类</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>选择目标分类</label>
                        <select class="form-control aiz-selectpicker" data-live-search="true" id="new-category-id">
                            <option value="">-- 选择分类 --</option>
                            @foreach ($categories as $cat)
                                @if ($cat->id != $category_id)
                                    <option value="{{ $cat->id }}">{{ $cat->getTranslation('name') }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <small class="text-warning">
                            <i class="las la-exclamation-triangle"></i>
                            已选中 <strong id="selected-count">0</strong> 个商品，将把它们的分类修改为目标分类。
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" id="confirm-change-category">确认修改</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).on("change", ".check-all", function() {
        $('.check-one:checkbox').each(function() {
            this.checked = $('.check-all').prop('checked');
        });
        updateSelectedCount();
    });

    $(document).on("change", ".check-one", function() {
        updateSelectedCount();
    });

    function updateSelectedCount() {
        var count = $('.check-one:checkbox:checked').length;
        $('#selected-count').text(count);
    }

    function show_change_category_modal() {
        var count = $('.check-one:checkbox:checked').length;
        if (count === 0) {
            AIZ.plugins.notify('warning', '请先选择商品');
            return;
        }
        $('#selected-count').text(count);
        $('#change-category-modal').modal('show');
        setTimeout(function() {
            $('#new-category-id').selectpicker('destroy').selectpicker({liveSearch: true});
        }, 200);
    }

    $('#confirm-change-category').on('click', function() {
        var newCategoryId = $('#new-category-id').val();
        if (!newCategoryId) {
            AIZ.plugins.notify('warning', '请选择目标分类');
            return;
        }

        var ids = [];
        $('.check-one:checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        var btn = $(this);
        btn.prop('disabled', true).text('处理中...');

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route('products.fix_category.bulk_update') }}',
            type: 'POST',
            data: { id: ids, new_category_id: newCategoryId },
            success: function(count) {
                if (count > 0) {
                    AIZ.plugins.notify('success', '成功修改 ' + count + ' 个商品的分类');
                    $('#change-category-modal').modal('hide');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    AIZ.plugins.notify('warning', '修改失败');
                }
            },
            error: function() {
                AIZ.plugins.notify('danger', '请求失败');
            },
            complete: function() {
                btn.prop('disabled', false).text('确认修改');
            }
        });
    });

    function bulk_delete() {
        var count = $('.check-one:checkbox:checked').length;
        if (count === 0) {
            AIZ.plugins.notify('warning', '请先选择商品');
            return;
        }
        if (!confirm('确认删除选中的 ' + count + ' 个商品？此操作不可撤销！')) {
            return;
        }
        var data = new FormData($('#sort_products')[0]);
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route('products.fix_category.bulk_delete') }}',
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response == 1) {
                    AIZ.plugins.notify('success', '删除成功');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    AIZ.plugins.notify('danger', '删除失败');
                }
            }
        });
    }
</script>
@endsection
