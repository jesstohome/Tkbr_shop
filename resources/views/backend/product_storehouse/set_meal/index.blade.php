@extends('backend.layouts.app')

@section('content')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('Product Set Meal')}}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('product_set_meal.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Set Meal')}}</span>
            </a>
        </div>
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Product Set Meal') }}</h5>
            </div>

            <div class="col-md-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="category_id" name="category_id" onchange="sort_products()">
                    <option value="">{{ translate('All Categories') }}</option>
                    @foreach (App\Models\Category::all() as $key => $category)
                        <option value="{{ $category->id }}" @if ($category->id == $category_id) selected @endif>
                            {{ translate($category->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </th>
                        <th>{{translate('Name')}}</th>
                        <th data-breakpoints="md">{{translate('Number of times added')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Stock')}}</th>
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $key => $row)
                    <tr>
                        <td>
                            <div class="form-group d-inline-block">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-one ids" name="id[]" value="{{$row->id}}">
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="row gutters-5 w-200px w-md-300px mw-100">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($row->category->icon)}}" alt="Image" class="size-50px img-fit">
                                </div>
                                <div class="col">
                                    <span class="text-muted text-truncate-2">{{ $row->category->getTranslation('name') }} - {{$row->name}}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{$row->added_times}}</td>
                        <td>{{$row->stock}}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('product_set_meal.edit', ['id' => $row->id] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('product_set_meal.destroy', $row->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $list->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">

        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        function sort_products(el){
            $('#sort_products').submit();
        }

    </script>
@endsection
