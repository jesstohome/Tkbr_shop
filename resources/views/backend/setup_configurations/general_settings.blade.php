@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h1 class="mb-0 h6">{{translate('General Settings')}}</h1>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Name')}}</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="types[]" value="site_name">
                                <input type="text" name="site_name" class="form-control" value="{{ get_setting('site_name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Logo - White')}}</label>
                            <div class="col-sm-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                    <input type="hidden" name="types[]" value="system_logo_white">
                                    <input type="hidden" name="system_logo_white" value="{{ get_setting('system_logo_white') }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm"></div>
                                <small>{{ translate('Will be used in admin panel side menu') }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Logo - Black')}}</label>
                            <div class="col-sm-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                    <input type="hidden" name="types[]" value="system_logo_black">
                                    <input type="hidden" name="system_logo_black" value="{{ get_setting('system_logo_black') }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm"></div>
                                <small>{{ translate('Will be used in admin panel topbar in mobile + Admin login page') }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('System Timezone')}}</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="types[]" value="timezone">
                                <select name="timezone" class="form-control aiz-selectpicker" data-live-search="true">
                                    @foreach (timezones() as $key => $value)
                                        <option value="{{ $value }}" @if (app_timezone() == $value)
                                            selected
                                        @endif>{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('Admin login page background')}}</label>
                            <div class="col-sm-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                    <input type="hidden" name="types[]" value="admin_login_background">
                                    <input type="hidden" name="admin_login_background" value="{{ get_setting('admin_login_background') }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label">{{translate('Seller Shop Default Logo')}}</label>
                            <div class="col-sm-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                    <input type="hidden" name="types[]" value="seller_shop_default_logo">
                                    <input type="hidden" name="seller_shop_default_logo" value="{{ get_setting('seller_shop_default_logo') }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">Api Url</label>
                            <div class="col-md-8">
                                <input readonly type="text" class="form-control"   value="<?php echo "https://".$_SERVER['HTTP_HOST'].'/apicj'; ?>" />
                            </div>
					    </div>

					 <div class="form-group row">
                        <label class="col-md-3 col-from-label">Key</label>
						<div class="col-md-8">
						 	<input type="hidden" name="types[]" value="caiji_key">
								<input type="text" class="form-control"  value="{{ get_setting('caiji_key') }}"placeholder="采集Key" name="caiji_key"  >



						</div>
					</div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">lazada-Key</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="wanbang_key">
                                <input type="text" class="form-control"  value="{{ get_setting('wanbang_key') }}" placeholder="万邦Key" name="wanbang_key"  >



                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">lazada-secret</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="wanbang_secret">
                                <input type="text" class="form-control"  value="{{ get_setting('wanbang_secret') }}" placeholder="万邦secret" name="wanbang_secret"  >

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">CJ API Key</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="cj_api_key">
                                <input type="text" class="form-control" value="{{ get_setting('cj_api_key') }}" placeholder="CJdropshipping API Key" name="cj_api_key">
                                <small class="text-muted">
                                    CJ后台 → 个人中心 → API → 添加API → 选择"API Key"类型 → 复制完整Key粘贴到这里
                                </small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">工单对话话术</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="work_order_caveat">
                                <input type="text" class="form-control"  value="{{ get_setting('work_order_caveat') }}" placeholder="" name="work_order_caveat"  />

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">精选商品规则</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="custom_selection_rule">
                                <textarea name="custom_selection_rule" rows="5" class="form-control">{{get_setting('custom_selection_rule')}}</textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">行政后台Ip白名单</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="backend_ip_whitelist">
                                <textarea name="backend_ip_whitelist" rows="5" class="form-control">{{get_setting('backend_ip_whitelist')}}</textarea>
                            </div>
                        </div>

                        <!-- 店铺相关设置 -->
                        <hr>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Automatically Unfrozen')}} ( {{translate('Days')}} )</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="frozen_funds_unfrozen_days">
                                <input   type="text" class="form-control"  value="{{ get_setting('frozen_funds_unfrozen_days') }}" placeholder="{{translate('Automatically Unfrozen')}}" name="frozen_funds_unfrozen_days"  >

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Warehouse Product Merchant Limit')}}</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="warehouse_product_merchant_limit">
                                <input   type="number" class="form-control"  value="{{ get_setting('warehouse_product_merchant_limit') }}" placeholder="{{translate('Each item can have up to N sellers listed simultaneously')}}" name="warehouse_product_merchant_limit"  >

                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Original Price Ratio')}}</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="original_price_ratio">
                                <input type="number" class="form-control" max="10" min="0.1" step="0.01" value="{{ get_setting('original_price_ratio') }}" name="original_price_ratio" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Maximum number of shelves removed per day')}}</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="max_off_shelf_num">
                                <input type="number" class="form-control" value="{{ get_setting('max_off_shelf_num') }}" name="max_off_shelf_num" />
                            </div>
                        </div>

                        <div class="text-right">
    						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
    					</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
