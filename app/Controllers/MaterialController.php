<?php
/**
 * User: Yazan
 * Date: 10/4/2021
 * Time: 8:36 PM
 */
namespace App\Http\Controllers;
use App\materialJobtype;
use Illuminate\Http\Request;
use App\material;
use App\JobType;
use Illuminate\Support\Facades\Schema;


class MaterialController extends Controller
{
    public function index(Request $request){

            // Show active materials
            $materials = material::active()->get();


        return view('material.index', compact("materials" ));
    }

    public function returnCreate()
    {
        $jobTypes =  JobType::all();
        return view('material.create',compact('jobTypes'));
    }
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'     => 'mat_name|max:30',
            'price'    => 'required|numeric',
        ]);

        $material = new material();


            $material->name = $request->mat_name;
            $material->price = $request->price;
            $material->design = isset($request->design) ? 1 : 0;
            $material->mill = $request->manufacturing == 2  ? 1 : 0;
            $material->print_3d = $request->manufacturing == 3  ? 1 : 0;
            $material->sinter_furnace = $request->furnace == 4  ? 1 : 0;
            $material->press_furnace = $request->furnace == 5 ? 1 : 0;
            $material->finish = isset($request->finishing) ? 1 : 0;
            $material->qc = isset($request->qc) ? 1 : 0;
            $material->delivery = isset($request->delivery) ? 1 : 0;
        $material->count_as_unit = isset($request->count_as_unit) ? 1 : 0;
            $material->save();

        foreach($request->jobTypes as $jobType){
          $jt = new materialJobtype();
          $jt->material_id= $material->id;
          $jt->jobtype_id  = $jobType;
          $jt->save();
         }

            return back()->with('success', 'Material has been successfully created');

    }
    public function returnUpdate($id)
    {

        $jobTypes =  JobType::all();
        $material = material::findOrFail($id);
        $matJobTypes =$material->jobtypes->pluck("jobtype_id")->toArray();;

        return view('material.edit',compact('material','matJobTypes','jobTypes'));

    }
    public function update(Request $request)
    {


        $material = material::where('id', $request->mat_id)->first();
        if (!$material) {
            return back()->with('Material Not found');
        }

            $material->name = $request->mat_name;
            $material->price = $request->price;
            $material->design = isset($request->design) ? 1 : 0;
            $material->mill = $request->manufacturing == 2  ? 1 : 0;
            $material->print_3d = $request->manufacturing == 3  ? 1 : 0;
            $material->sinter_furnace = $request->furnace == 4  ? 1 : 0;
            $material->press_furnace = $request->furnace == 5 ? 1 : 0;
            $material->finish = isset($request->finishing) ? 1 : 0;
            $material->qc = isset($request->qc) ? 1 : 0;
            $material->delivery = isset($request->delivery) ? 1 : 0;
            $material->count_as_unit = isset($request->count_as_unit) ? 1 : 0;
            $material->save();

        foreach($material->jobTypes as $jobTypeRelation){
            materialJobtype::findOrFail($jobTypeRelation->id)->delete();
        }

            if (isset($request->jobTypes))
            foreach($request->jobTypes as $jobType){
                $jt = new materialJobtype();
                $jt->material_id= $material->id;
                $jt->jobtype_id  = $jobType;
                $jt->save();
            }

            return back()->with('success', 'Material has been successfully updated');

    }

    public function delete(Request $request)
    {
        try {
            $material = material::findOrFail($request->material_id);

            /// Check if material is used in any cases
             $usedInCases = \App\job::where('material_id', $material->id)->exists();

             if ($usedInCases) {
                 return response()->json([
                     'status' => 'error',
                     'message' => 'This material cannot be deleted because it has been used in one or more cases. Would you like to disable it instead?',
                     'canDisable' => true
                 ]);
             }

            $material->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Material has been successfully deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting material: ' . $e->getMessage()
            ], 500);
        }
    }

    public function disable(Request $request)
    {
        try {
            $material = material::findOrFail($request->material_id);

            // Check if is_active column exists
            if (Schema::hasColumn('materials', 'is_active')) {
                $material->is_active = 0;
                $material->save();
                $message = 'Material has been successfully disabled.';
            } else {
                // If column doesn't exist, we need to run the migration first
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database needs to be updated. Please run the migration to add is_active column to materials table.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error disabling material: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enable(Request $request)
    {
        try {
            $material = material::findOrFail($request->material_id);

            // Check if is_active column exists
            if (Schema::hasColumn('materials', 'is_active')) {
                $material->is_active = 1;
                $material->save();
                $message = 'Material has been successfully enabled.';
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database needs to be updated. Please run the migration to add is_active column to materials table.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error enabling material: ' . $e->getMessage()
            ], 500);
        }
    }
}
