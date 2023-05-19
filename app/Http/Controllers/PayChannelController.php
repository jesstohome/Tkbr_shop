<?php

namespace App\Http\Controllers;

use App\Models\Bloc;
use App\Models\Country;
use App\Models\PayChannel;
use App\Models\PayChannelField;
use Illuminate\Http\Request;

class PayChannelController extends Controller
{

    private $fields = [
        // 银行卡
        'bank' => [
            'bank_no' => 'Bank No',
            'bank_name' => 'Bank Name',
            'bank_code' => 'Bank Code',
            'bank_account_no' => 'Bank Account No',
            'bank_account_name' => 'Bank Account Name',
            'ifsc_code' => 'IFSC Code',
        ],

        // 钱包
        'wallet' => [
            'e_wallet_name' => 'Wallet Name',
            'e_wallet_address' => 'Wallet Address',
        ]
    ];

    // 银行列表
    private $banks = [

    ];

    // 钱包列表
    private $wallets = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pay_channels = PayChannel::all();
        return view('backend.sellers.pay_channel.index', compact('pay_channels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $blocs = Bloc::all();
        $countries = Country::all();
        $fields = $this->fields;

        return view('backend.sellers.pay_channel.create', compact('countries', 'blocs', 'fields'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pay_channel = new PayChannel();
        $pay_channel->country_id = $request->country_id;
        $pay_channel->bloc_ids = is_string($request->bloc_ids) ? $request->bloc_ids : join(",", $request->bloc_ids);
        if ($pay_channel->save()) {
            if (!empty($request->field_codes)) {
                foreach ($request->field_codes as $field_code) {
                    $pay_channel_field = new PayChannelField();
                    $pay_channel_field->field_code = $field_code;
                    $pay_channel_field->save();
                }
            }

            flash(translate('Pay Channel has been created successfully'))->success();
            return redirect()->route('pay_channel.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pay_channel = PayChannel::find($id);
        $blocs = Bloc::all();
        $countries = Country::all();

        return view('backend.sellers.pay_channel.edit', compact('pay_channel', 'countries', 'blocs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pay_channel = PayChannel::find($id);
        if (!empty($pay_channel)) {
            $pay_channel->country_id = $request->country_id;
            $pay_channel->bloc_ids = is_string($request->bloc_ids) ? $request->bloc_ids : join(",", $request->bloc_ids);
            if ($pay_channel->save()) {
                // 先清空
                PayChannelField::where("pay_channel_id", $id)->delete();

                if (!empty($request->field_codes)) {
                    foreach ($request->field_codes as $field_code) {
                        $pay_channel_field = new PayChannelField();
                        $pay_channel_field->field_code = $field_code;
                        $pay_channel_field->save();
                    }
                }

                flash(translate('Pay Channel has been created successfully'))->success();
                return redirect()->route('pay_channel.index');
            }
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(PayChannel::destroy($id)){
            flash(translate('Pay Channel has been deleted successfully'))->success();
            return redirect()->route('pay_channel.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
