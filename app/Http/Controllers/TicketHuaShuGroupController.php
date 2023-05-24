<?php

namespace App\Http\Controllers;

use App\Models\TicketHuaShu;
use App\Models\TicketHuaShuGroup;
use Illuminate\Http\Request;

class TicketHuaShuGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = filter_by_bloc(TicketHuaShuGroup::all());
        return view('backend.support.ticket_huashu_group.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.support.ticket_huashu_group.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $group = new TicketHuaShuGroup();
        $group->bloc_id = \Auth::user()->bloc_id;
        $group->staff_id = \Auth::user()->staff_id;
        $group->name = $request->name;
        if($group->save()) {
            if ($request->huashu_ids) {
                TicketHuaShu::query()->whereIn('id', $request->huashu_ids)->update(['group_id' => $group->id]);
            }
            flash(translate('Group has been inserted successfully'))->success();
            return redirect()->route('huashu_group.index');
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
        $group = TicketHuaShuGroup::find($id);
        return view('backend.support.ticket_huashu_group.edit', compact('group'));
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
        $group = TicketHuaShuGroup::find($id);
        $group->name = $request->name;
        if($group->save()){
            TicketHuaShu::query()->where('group_id', $group->id)->update(['group_id' => 0]);

            if ($request->huashu_ids) {
                TicketHuaShu::query()->whereIn('id', $request->huashu_ids)->update(['group_id' => $group->id]);
            }

            flash(translate('Group has been updated successfully'))->success();
            return redirect()->route('huashu_group.index');
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
        if(TicketHuaShuGroup::destroy($id)){
            flash(translate('Group has been deleted successfully'))->success();
            return redirect()->route('huashu_group.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
