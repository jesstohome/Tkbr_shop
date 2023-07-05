<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bloc_id = $request->bloc_id;
        $staff_user_id = $request->staff_user_id;

        $roles = Role::query()->orderByDesc('id');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $roles = $roles->where('name', 'like', '%' . $sort_search . '%');
        }

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_var = explode("/", $request->date_range);
            $start_time = $date_var[0];
            $end_time = $date_var[1];
            $roles = $roles->where( 'created_at', '>=', trim($start_time));
            $roles = $roles->where('created_at', '<=', trim($end_time) . " 23:59:59");
        }
        if ($bloc_id) {
            $roles = $roles->where('bloc_id', $bloc_id);
        }
        if ($staff_user_id) {
            if ($staff_user_id == Auth::id() && isSupperAdmin()) {
                $roles = $roles->where('admin_id', 0);
            } else {
                $roles = $roles->where('admin_id', $staff_user_id);
            }
        }

        $roles = filter_by_bloc($roles);
        $roles = $roles->paginate(10);
        return view('backend.staff.staff_roles.index', compact('roles', 'date_range', 'sort_search', 'staff_user_id', 'bloc_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.staff.staff_roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('menu_ids')){
            $role = new Role;
            $role->name = $request->name;
            $role->bloc_id = Auth::user()->bloc_id;
            $role->admin_id = Auth::user()->id;
            $role->is_manage = $request->get('is_manage', 0);
            $role->permissions = json_encode(explode(",", $request->menu_ids));
            $role->save();

            $role_translation = RoleTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'role_id' => $role->id]);
            $role_translation->name = $request->name;
            $role_translation->save();

            flash(translate('Role has been inserted successfully'))->success();
            return redirect()->route('roles.index');
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
        $role = Role::findOrFail($id);
        return view('backend.staff.staff_roles.edit', compact('role','lang'));
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
        $role = Role::findOrFail($id);

        if($request->has('menu_ids')){
            if($request->lang == env("DEFAULT_LANGUAGE")){
                $role->name = $request->name;
            }
            $role->is_manage = $request->get('is_manage', 0);
            $role->permissions = is_string($request->menu_ids) ? explode(",", $request->menu_ids) : $request->menu_ids;
            $role->save();

            $role_translation = RoleTranslation::firstOrNew(['lang' => $request->lang, 'role_id' => $role->id]);
            $role_translation->name = $request->name;
            $role_translation->save();

            flash(translate('Role has been updated successfully'))->success();
            return redirect()->route('roles.index');
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
        $role = Role::findOrFail($id);
        foreach ($role->role_translations as $key => $role_translation) {
            $role_translation->delete();
        }

        Role::destroy($id);
        flash(translate('Role has been deleted successfully'))->success();
        return redirect()->route('roles.index');
    }
}
