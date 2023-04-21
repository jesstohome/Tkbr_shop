<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menu::OrderBy('id', 'DESC')->paginate(20);
        return view('backend.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $menus = Menu::all();
        return view('backend.menus.create', compact('menus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $menu = new Menu();
        $menu->pid = $request->pid;
        $menu->name = $request->name;
        $menu->addon_name = $request->addon_name;
        $menu->setting_name = $request->setting_name;
        $menu->route = $request->route;
        $menu->active_routes = $request->active_routes;
        $menu->icon = $request->icon;
        $menu->red_dot_keys = $request->red_dot_keys;

        if($menu->save()){
            flash(translate('Menu has been inserted successfully'))->success();
            return redirect()->route('menu.index');
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
        $menu = Menu::findOrFail($id);
        $menus = Menu::all();
        return view('backend.menus.edit', compact('menu', 'menus'));
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
        $menu = Menu::findOrFail($id);;
        $menu->pid = $request->pid;
        $menu->name = $request->name;
        $menu->addon_name = $request->addon_name;
        $menu->setting_name = $request->setting_name;
        $menu->route = $request->route;
        $menu->active_routes = $request->active_routes;
        $menu->icon = $request->icon;
        $menu->red_dot_keys = $request->red_dot_keys;

        if($menu->save()){
            flash(translate('Menu has been updated successfully'))->success();
            return redirect()->route('menu.index');
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
        if(Menu::destroy($id)){
            flash(translate('Menu has been deleted successfully'))->success();
            return redirect()->route('menu.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
