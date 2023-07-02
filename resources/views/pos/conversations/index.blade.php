@extends('backend.layouts.app')
<style>
    li.li-header div {
        padding: 0 10px;
    }
</style>
@section('content')
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
          <div class="col-md-6">
              <b class="h4">{{ translate('Conversations')}}</b>
          </div>
      </div>
    </div>

    <div class="card">
        <form>
            <div class="card-header row gutters-5">
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="customer_id" name="customer_id" data-live-search="true">
                            <option value="">{{ translate('All Customers') }}</option>
                            @foreach (filter_by_bloc(App\Models\User::where('user_type', '=', 'customer'))->get() as $key => $customer)
                                <option value="{{ $customer->id }}" @if ($customer->id == $customer_id) selected @endif>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @include('backend.partials.filters.bloc_staff')
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="seller_id" id="seller_id" data-live-search="true">
                        <option value="">{{translate('Filter by Shop')}}</option>
                        @foreach(filter_by_bloc(\App\Models\User::query()->where('user_type', 'seller'))->get() as $seller)
                            <option value="{{$seller->id}}"  @isset($seller_id) @if($seller_id == $seller->id) selected @endif @endisset>{{$seller->shop->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-styled">{{ translate('Search') }}</button>
                <button class="btn btn-md btn-primary" type="reset" onclick="reset_form()">重置</button>
            </div>
        </form>
      <div class="card-body">
        <ul class="list-group list-group-flush">
            <li class="list-group-item px-0 li-header">
                <div class="row gutters-10">
                    <div class="col-auto" style="width:55px">头像</div>
                    <div class="col-auto col-lg-3"><p>客户姓名</p></div>
                    <div class="col-auto col-lg-1">店铺名</div>
                    @if(isSupperAdmin()) <div class="col-auto col-lg-1">集团</div> @endif
                    <div class="col-auto col-lg-1">员工</div>
                    <div class="col-12 col-lg">标题</div>
                    <div class="col-auto col-lg-1 text-right">操作</div>
                </div>
            </li>
          @foreach ($conversations as $key => $conversation)
              @if ($conversation->receiver != null && $conversation->sender != null)
                    <li class="list-group-item px-0">
                      <div class="row gutters-10">
                          <div class="col-auto">
                              <div class="media">
                                  <span class="avatar avatar-sm flex-shrink-0">
                                    @if (Auth::user()->id == $conversation->sender_id)
                                        <img @if ($conversation->receiver->avatar_original == null) src="{{ static_asset('assets/img/avatar-place.png') }}" @else src="{{ uploaded_asset($conversation->receiver->avatar_original) }}" @endif onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                    @else
                                        <img @if ($conversation->sender->avatar_original == null) src="{{ static_asset('assets/img/avatar-place.png') }}" @else src="{{ uploaded_asset($conversation->sender->avatar_original) }}" @endif class="rounded-circle" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                    @endif
                                </span>
                              </div>
                          </div>
                          <div class="col-auto col-lg-3">
                              <p>
                                  @if (Auth::user()->id == $conversation->sender_id)
                                      <span class="fw-600">{{ $conversation->receiver->name }}</span>
                                  @else
                                      <span class="fw-600">{{ $conversation->sender->name }}</span>
                                  @endif
                                  <br>
                                  <span class="opacity-50">
                                      {{ date('H:i:m d-m-Y', strtotime($conversation->updated_at)) }}
                                  </span>
                              </p>
                          </div>
                          <div class="col-auto col-lg-1">
                              {{$conversation->receiver->shop ? $conversation->receiver->shop->name : ''}}
                          </div>
                          @if(isSupperAdmin())
                          <div class="col-auto col-lg-1">
                              {{$conversation->bloc->name ?: ''}}
                          </div>
                          @endif
                          <div class="col-auto col-lg-1">
                              {{$conversation->staff->user->name ?: ''}}
                          </div>
                          <div class="col-12 col-lg">
                              <div class="block-body">
                                  <div class="block-body-inner pb-3">
                                      <div class="row no-gutters">
                                          <div class="col">
                                              <h6 class="mt-0">n.
                                                  <a href="{{ route('poin-of-sales.conversation-show', encrypt($conversation->id)) }}" class="text-dark fw-600">
                                                      {{ $conversation->title }}
                                                  </a>
                                                  @if ((Auth::user()->id == $conversation->sender_id && $conversation->sender_viewed == 0) || (Auth::user()->id == $conversation->receiver_id && $conversation->receiver_viewed == 0))
                                                      <span class="badge badge-inline badge-danger">{{ translate('New') }}</span>
                                                  @else

                                                      @if ( in_array(Auth::user()->user_type, ['admin', 'staff']) && $conversation->admin_viewed == 0)
                                                          <span class="badge badge-danger badge-circle badge-sm badge-dot"> </span>
                                                      @endif

                                                  @endif
                                              </h6>
                                          </div>
                                      </div>
                                      <p class="mb-0 opacity-50">
                                          {{ $conversation->messages->last()->message }}
                                      </p>
                                  </div>
                              </div>
                          </div>
                          <div class="col-auto col-lg-1 text-right">
                              <button type="button" class="btn btn-primary" onclick="location.href='{{ route('poin-of-sales.conversation-show', encrypt($conversation->id)) }}'">{{ translate('Reply') }}</button>
                          </div>
                      </div>
                    </li>
              @endif
          @endforeach
      </ul>
      </div>
    </div>
    <div class="aiz-pagination">
      	{{ $conversations->links() }}
    </div>

@endsection
