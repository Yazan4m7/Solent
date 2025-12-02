<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\implant;
use DB;


class ImplantsController extends Controller
{
    public function index(){
        $implants = implant::all();
        return view('implants.index',compact("implants"));
    }

    public function returnCreate()
    {
        return view('implants.create');
    }
    public function create(Request $request)
    {
        $this->validate($request, [
            'implant_name'     => 'required|max:50',

        ]);

        $implant = new implant();

        try {
            $implant->name = $request->implant_name;
            $implant->save();

            return back()->with('success', 'Implant has been successfully created');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }
    public function returnUpdate($id)
    {

        $implant = implant::findOrFail($id);
        return view('implants.edit',compact('implant'));
    }
    public function update(Request $request)
    {
        try {
            $implant = implant::where('id', $request->implant_id)->first();
            if (!$implant) {
                return back()->with('Implant Not found');
            }
            $implant->name = $request->implant_name;

            $implant->save();


            return back()->with('success', 'Implant has been successfully updated');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

}