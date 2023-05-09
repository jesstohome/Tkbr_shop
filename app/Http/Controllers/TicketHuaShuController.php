<?php

namespace App\Http\Controllers;

use App\Models\TicketHuaShu;
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
        $list = TicketHuaShu::all();
        return view('backend.support.ticket_huashu.index', compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.support.ticket_huashu.create');
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
            $huashu->abstract = $request->abstract ?? '';
            $huashu->content = $request->content ?? '';
            if($huashu->save()){
                flash(translate('Ticket Fast Reply has been inserted successfully'))->success();
                return redirect()->route('ticket_huashu.index');
            }
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
        $row = TicketHuaShu::find($id);
        return view('backend.support.ticket_huashu.edit', compact('row'));
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
        if ($request->has('content')) {
            $huashu = TicketHuaShu::find($id) ;
            $huashu->abstract = $request->abstract ?? '';
            $huashu->content = $request->content ?? '';
            if($huashu->save()){
                flash(translate('Ticket Fast Reply has been updated successfully'))->success();
                return redirect()->route('ticket_huashu.index');
            }
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
    public function destroy($id)
    {
        if(TicketHuaShu::destroy($id)){
            flash(translate('Staff has been deleted successfully'))->success();
            return redirect()->route('staffs.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
