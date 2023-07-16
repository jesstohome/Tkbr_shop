<?php

namespace App\Http\Controllers;

use App\Models\TicketHuaShu;
use App\Models\TicketHuaShuGroup;
use Illuminate\Http\Request;

class TicketHuaShuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = get_huashu_groups();
        $groupIds = $groups->pluck("id")->toArray();
        $list = TicketHuaShu::query()->whereIn("group_id", $groupIds)->get();
        return view('backend.support.support_tickets.fast_reply_modal', compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('backend.support.ticket_huashu.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->has('content')) {
            $huashu = new TicketHuaShu;
            $huashu->bloc_id = \Auth::user()->bloc_id;
            $huashu->staff_id = \Auth::user()->staff_id;
            $huashu->group_id = $request->group_id ?? 0;
            $huashu->abstract = $request->abstract ?? '';
            $huashu->content = $request->content ?? '';
            if($huashu->save()){
                if ($request->ajax()) {
                    return response()->json(['success' => 1, 'data' => $huashu]);
                }
                flash(translate('Ticket Fast Reply has been inserted successfully'))->success();
                return redirect()->route('huashu.index');
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => 0]);
        }

        flash(translate('Something Error'))->error();
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
//        $row = TicketHuaShu::find($id);
//        return view('backend.support.ticket_huashu.edit', compact('row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $id = $request->post('id');
        if ($id) {
            $huashu = TicketHuaShu::find($id) ;
            $huashu->abstract = $request->abstract ?? '';
            $huashu->content = $request->content ?? '';
            if($huashu->save()){
                if ($request->ajax()) {
                    return response()->json(['success' => 1]);
                }
                flash(translate('Ticket Fast Reply has been updated successfully'))->success();
                return redirect()->route('huashu.index');
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => 0]);
        }
        flash(translate('Something Error'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->post('id');
        if(TicketHuaShu::destroy($id)){
            if ($request->ajax()) {
                return response()->json(['success' => 1]);
            }
            flash(translate('Staff has been deleted successfully'))->success();
            return redirect()->route('huashu.index');
        }

        if ($request->ajax()) {
            return response()->json(['success' => 0]);
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }
}
