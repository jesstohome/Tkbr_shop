<?php

namespace App\Http\Controllers;

use App\Models\Bloc;
use Illuminate\Http\Request;

class BlocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blocs = Bloc::paginate(10);
        return view('backend.staff.blocs.index', compact('blocs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.staff.blocs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!empty($request->name)){
            $bloc = new Bloc();
            $bloc->name = $request->name;
            $bloc->lang = $request->lang;
            $bloc->time_zone = $request->time_zone;
            $bloc->currency_code = $request->currency_code;
            $bloc->welcome_message = $request->welcome_message;
            $bloc->save();

            flash(translate('Bloc has been inserted successfully'))->success();
            return redirect()->route('bloc.index');
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
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $bloc = Bloc::findOrFail($id);
        return view('backend.staff.blocs.edit', compact('bloc','lang'));
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
        $bloc = Bloc::findOrFail($id);

        if(!empty($bloc) && !empty($request->name)){
            $bloc->name = $request->name;
            $bloc->lang = $request->lang;
            $bloc->time_zone = $request->time_zone;
            $bloc->currency_code = $request->currency_code;
            $bloc->welcome_message = $request->welcome_message;
            $bloc->save();

            flash(translate('Bloc has been updated successfully'))->success();
            return redirect()->route('bloc.index');
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
        Bloc::destroy($id);
        flash(translate('Bloc has been deleted successfully'))->success();
        return redirect()->route('bloc.index');
    }

    /**
     * 启用禁用
     * author: Sym
     * time: 2023-04-23 10:03
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ban($id) {
        $bloc = Bloc::findOrFail(decrypt($id));

        if ( $bloc->status == 1 )
        {
            $bloc->status = 0;
            flash(translate('Bloc UnBanned Successfully'))->success();
        }
        else
        {
            $bloc->status = 1;
            flash(translate('Bloc Banned Successfully'))->success();
        }

        $bloc->save();

        return back();
    }
}
