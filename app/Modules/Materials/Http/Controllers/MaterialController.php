<?php
namespace App\Modules\Materials\Http\Controllers;

use App\Http\Controllers\Controller;
use App\JobType;
use App\material;
use App\materialJobtype;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = material::all();

        return view('materials::index', compact('materials'));
    }

    public function returnCreate()
    {
        $jobTypes = JobType::all();

        return view('materials::create', compact('jobTypes'));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'mat_name' => 'required|max:30',
            'price' => 'required|numeric',
            'jobTypes' => 'required|array|min:1',
        ]);

        $material = new material();
        $material->name = $request->mat_name;
        $material->price = $request->price;
        $material->design = isset($request->design) ? 1 : 0;
        $material->mill = $request->manufacturing == 2 ? 1 : 0;
        $material->print_3d = $request->manufacturing == 3 ? 1 : 0;
        $material->sinter_furnace = $request->furnace == 4 ? 1 : 0;
        $material->press_furnace = $request->furnace == 5 ? 1 : 0;
        $material->metal_work = $request->furnace == 9 ? 1 : 0;
        $material->finish = isset($request->finishing) ? 1 : 0;
        $material->qc = isset($request->qc) ? 1 : 0;
        $material->delivery = isset($request->delivery) ? 1 : 0;
        $material->count_as_unit = isset($request->count_as_unit) ? 1 : 0;
        $material->save();

        foreach ($request->jobTypes as $jobType) {
            $jt = new materialJobtype();
            $jt->material_id = $material->id;
            $jt->jobtype_id = $jobType;
            $jt->save();
        }

        return back()->with('success', 'Material has been successfully created');
    }

    public function returnUpdate($id)
    {
        $jobTypes = JobType::all();
        $material = material::findOrFail($id);
        $matJobTypes = $material->jobtypes->pluck('jobtype_id')->toArray();

        return view('materials::edit', compact('material', 'matJobTypes', 'jobTypes'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'mat_id' => 'required',
            'mat_name' => 'required|max:30',
            'price' => 'required|numeric',
            'jobTypes' => 'required|array|min:1',
        ]);

        $material = material::where('id', $request->mat_id)->first();
        if (!$material) {
            return back()->with('Material Not found');
        }

        $material->name = $request->mat_name;
        $material->price = $request->price;
        $material->design = isset($request->design) ? 1 : 0;
        $material->mill = $request->manufacturing == 2 ? 1 : 0;
        $material->print_3d = $request->manufacturing == 3 ? 1 : 0;
        $material->sinter_furnace = $request->furnace == 4 ? 1 : 0;
        $material->press_furnace = $request->furnace == 5 ? 1 : 0;
        $material->metal_work = $request->furnace == 9 ? 1 : 0;
        $material->finish = isset($request->finishing) ? 1 : 0;
        $material->qc = isset($request->qc) ? 1 : 0;
        $material->delivery = isset($request->delivery) ? 1 : 0;
        $material->count_as_unit = isset($request->count_as_unit) ? 1 : 0;
        $material->save();

        foreach ($material->jobTypes as $jobTypeRelation) {
            materialJobtype::findOrFail($jobTypeRelation->id)->delete();
        }

        if (isset($request->jobTypes)) {
            foreach ($request->jobTypes as $jobType) {
                $jt = new materialJobtype();
                $jt->material_id = $material->id;
                $jt->jobtype_id = $jobType;
                $jt->save();
            }
        }

        return back()->with('success', 'Material has been successfully updated');
    }

    public function delete(Request $request)
    {
        try {
            $material = material::findOrFail($request->material_id);
            $material->delete();

            return back()->with('success', 'Material has been successfully deleted');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting material: ' . $e->getMessage(),
            ], 500);
        }
    }
}

