<?php

namespace App\Http\Controllers;

use App\Models\Bloc;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Role;
use App\Models\User;
use Hash;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->user_id;
        $bloc_id = $request->bloc_id;
        $staff_id = $request->staff_id;

        $staffs = Staff::query()->orderByDesc('id');

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_var = explode("/", $request->date_range);
            $start_time = $date_var[0];
            $end_time = $date_var[1];
            $staffs = $staffs->where( 'created_at', '>=', trim($start_time));
            $staffs = $staffs->where('created_at', '<=', trim($end_time) . " 23:59:59");
        }
        if ($user_id) {
            $staffs = $staffs->where('user_id', $user_id);
        }
        if ($bloc_id) {
            $staffs = $staffs->where('bloc_id', $bloc_id);
        }
        if ($staff_id) {
            $staffs = $staffs->where('id', $staff_id);
        }

        $staffs = filter_by_bloc($staffs);
        $staffs = $staffs->paginate(10);
        return view('backend.staff.staffs.index', compact('staffs', 'date_range', 'user_id', 'bloc_id', 'staff_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = filter_by_bloc(Role::query())->get();
        $blocs = \Auth::user()->user_type == 'admin' ? Bloc::all() : Bloc::query()->where(['id' => \Auth::user()->bloc_id])->get();
        return view('backend.staff.staffs.create', compact('roles', 'blocs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bloc_id = $request->bloc_id ?: \Auth::user()->bloc_id;
        if(User::where('email', $request->email)->first() == null){
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->mobile;
            $user->bloc_id = $bloc_id;
            $user->user_type = "staff";
            $user->password = Hash::make($request->password);
            $user->google2fa_secret = $request->google2fa_secret;
            if($user->save()){
                $staff = new Staff;
                $staff->user_id = $user->id;
                $staff->role_id = $request->role_id;
                $staff->bloc_id = $bloc_id;
                $staff->invite_code = mt_rand(10000000, 99999999);
                if($staff->save()){
                    flash(translate('Staff has been inserted successfully'))->success();
                    return redirect()->route('staffs.index');
                }
            }
        }

        flash(translate('Email already used'))->error();
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
        $staff = Staff::findOrFail(decrypt($id));
        $roles = filter_by_bloc(Role::query())->get();

        $blocs = \Auth::user()->user_type == 'admin' ? Bloc::all() : Bloc::query()->where(['id' => \Auth::user()->bloc_id])->get();
        return view('backend.staff.staffs.edit', compact('staff', 'roles', 'blocs'));
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
        $isAdmin = $user_type = \Auth::user()->user_type == 'admin';
        $staff = Staff::findOrFail($id);
        $user = $staff->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->mobile;
        if ($isAdmin) {
            $user->bloc_id = $request->bloc_id;
        }
        if(strlen($request->password) > 0){
            $user->password = Hash::make($request->password);
        }
        if($request->has('google2fa_secret')){
            $user->google2fa_secret = $request->google2fa_secret;
        }
        if($user->save()){
            $staff->role_id = $request->role_id;
            if ($isAdmin) {
                $staff->bloc_id = $request->bloc_id;
            }
            $staff->invite_code = $request->invite_code;
            if($staff->save()){

                if(strlen($request->password) > 0){
                    // 密码改了，标识用户需要重新登录
                    \Cache::set('password_changed:' . $user->id, 1);
                }

                flash(translate('Staff has been updated successfully'))->success();
                return redirect()->route('staffs.index');
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
        User::destroy(Staff::findOrFail($id)->user->id);
        if(Staff::destroy($id)){
            flash(translate('Staff has been deleted successfully'))->success();
            return redirect()->route('staffs.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }
}
