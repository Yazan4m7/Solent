<?php
/**
 * User: Yazan
 * Date: 10/4/2021
 * Time: 8:36 PM
 */
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\JobType;
class JobTypeController extends Controller
{
    public function index(){
        $jobTypes = JobType::all();
        return view('jobType.index',compact("jobTypes"));
    }

    public function returnCreate()
    {
       return view('jobType.create');
    }
    public function create(Request $request)
    {
        $this->validate($request, [
            'jobtype_name'     => 'required|max:30',
            'teeth_or_jaw' =>'required|numeric'
        ]);

        $jobType = new jobtype();

        try {
            $jobType->name = $request->jobtype_name;
            $jobType->teeth_or_jaw = $request->teeth_or_jaw;

            $jobType->save();

            return back()->with('success', 'Job Type has been successfully created');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }
    public function returnUpdate($id)
    {

        $jobType = jobtype::findOrFail($id);
        return view('jobType.edit',compact('jobType'));
    }
    public function update(Request $request)
    {
        try {
        $jobType = jobtype::where('id', $request->jobtype_id)->first();
        if (!$jobType) {
            return back()->with('Job Type Not found');
        }
        $jobType->name = $request->jobtype_name;
        $jobType->teeth_or_jaw = $request->teeth_or_jaw;
        $jobType->save();


        return back()->with('success', 'Job Type has been successfully updated');
    } catch (Exception $e) {
        return back()->with('error', $e);
}
    }
}