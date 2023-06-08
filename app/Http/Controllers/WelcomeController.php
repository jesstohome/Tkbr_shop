<?php


namespace App\Http\Controllers;


use App\Models\Bloc;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function bloc_list() {
        if (!isSupperAdmin()) {
            return redirect()->route('welcome.edit', ['id' => \Auth::user()->bloc->id]);
        }
        
        $blocs = Bloc::paginate(10);
        return view('backend.staff.bloc_welcome_message.index', compact('blocs'));
    }

    public function edit(Request $request) {
        $id = $request->id;
        $bloc = Bloc::findOrFail($id);
        return view('backend.staff.bloc_welcome_message.edit', compact('bloc'));
    }

    public function update(Request $request) {
        $id = $request->id;
        $bloc = Bloc::findOrFail($id);

        if(!empty($bloc)) {
            $bloc->examine_welcome_message = $request->examine_welcome_message;
            $bloc->welcome_message = $request->welcome_message;
            $bloc->work_order_welcome_message = $request->work_order_welcome_message;
            $bloc->save();

            flash(translate('Welcome has been updated successfully'))->success();
            return redirect()->route('welcome.bloc_list');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

}
