<?php

namespace App\Http\Controllers;
use App\failureCause;

use Illuminate\Http\Request;
use DB;


class FailCausesController extends Controller
{
    public function index(){
        $causes = failureCause::all();
        return view('failures.causes.index',compact("causes"));
    }

    public function returnCreate()
    {
        return view('failures.causes.create');
    }
    public function create(Request $request)
    {

        $newCause= new failureCause();

        try {
            $newCause->text = $request->cause_text;
            $newCause->save();
            return redirect()->route( 'f-causes-index' )->with('success', 'Failure Cause has been successfully created');

        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }
    public function returnUpdate($id)
    {

        $cause = failureCause::findOrFail($id);
        return view('failures.causes.edit',compact('cause'));
    }
    public function update(Request $request)
    {
        try {
            $cause = failureCause::where('id', $request->cause_id)->first();
            if (!$cause) {
                return back()->with('Cause Not found');
            }
            $cause->text = $request->cause_text;

            $cause->save();
            return redirect()->route( 'f-causes-index' )->with('success', 'Failure cause has been successfully updated');

        } catch (\Exception $e) {
            return back()->with('error', $e);
        }
    }

}