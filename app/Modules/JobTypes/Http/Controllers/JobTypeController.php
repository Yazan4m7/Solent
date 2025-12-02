<?php
namespace App\Modules\JobTypes\Http\Controllers;

use App\Http\Controllers\Controller;
use App\JobType;
use Exception;
use Illuminate\Http\Request;

class JobTypeController extends Controller
{
    public function index()
    {
        $jobTypes = JobType::all();

        return view('jobtypes::index', compact('jobTypes'));
    }

    public function returnCreate()
    {
        return view('jobtypes::create');
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'jobtype_name' => 'required|max:30',
            'teeth_or_jaw' => 'required|numeric',
        ]);

        $jobType = new JobType();

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
        $jobType = JobType::findOrFail($id);

        return view('jobtypes::edit', compact('jobType'));
    }

    public function update(Request $request)
    {
        try {
            $jobType = JobType::where('id', $request->jobtype_id)->first();
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

