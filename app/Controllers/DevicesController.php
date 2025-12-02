<?php

namespace App\Http\Controllers;
use App\device;
use App\file;
use Exception;
use Illuminate\Http\Request;
use App\abutment;
use DB;


class DevicesController extends Controller
{

    public function index(){
        $devices = device::all();
        return view('devices.index',compact("devices"));
    }


    public function returnCreate()
    {
        return view('devices.create');
    }
    public function create(Request $request)
    {
        $this->validate($request, [
            'device_name'     => 'required|max:50',

        ]);
    //    dd($request);

        $device = new device();

        try {
            $device->name = $request->device_name;
            $device->type = $request->device_type;
            $device->sorting_order =$request->device_order;
            $device->save();

            if ($request->hasFile('device_image')) {

                $file = $request->file('device_image');
                    $name = $file->getClientOriginalName();
                    $file->move('devicesImages/' . $device->id . '/', $name);
                    $newFile = new file();
                    $newFile->path = 'devicesImages/' . $device->id . '/' . $name;
                    $newFile->case_id = $device->id;
                    $newFile->added_by = Auth()->user()->id;
                    $newFile->save();
                $device->img =  $newFile->path;
                $device->save();

            }
            return redirect('/device/index')->with('success', 'Device has been successfully created');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }
    public function returnUpdate($id)
    {

        $device = device::findOrFail($id);
        return view('devices.edit2',compact('device'));
    }
    public function update(Request $request)
    {
       // dd($request);
        try {
            $device = device::where('id', $request->device_id)->first();
            if (!$device) {
//                dd("$--device22"  .  $device);
                return back()->with('Device Not found');
            }
            $device->name = $request->device_name;
            $device->type = $request->device_type;
            $device->sorting_order=$request->device_order;
            $device->save();
            if ($request->hasFile('device_image')) {


                $file = $request->file('device_image');
               // dd("file"  .  $file);
                $name = $file->getClientOriginalName();
                $file->move('devicesImages/' . $device->id . '/', $name);
                $newFile = new file();
                $newFile->path = 'devicesImages/' . $device->id . '/' . $name;
                $newFile->case_id = $device->id;
                $newFile->added_by = Auth()->user()->id;
                $newFile->save();
                //dd("newfile"  .  $newFile);
                $device->img= isset($newFile)? $newFile->path : "N/A";
                $device->save();
            }

           //s dd("======"  .  $device);
          //  dd("Device has been successfully updated");
            return redirect('/device/index')->with('success', 'Device has been successfully updated');
        } catch (Exception $e) {
        //    dd($e);
            return back()->with('error', $e);
        }
    }

    public function toggleVisibility($id)
    {
        try {
            $device = device::where('id', $id)->first();
            if (!$device) {
                return back()->with('Implant Not found');
            }
            if($device->hidden == 1)
                $device->hidden = 0;
            else
                $device->hidden = 1;

            $device->save();


            return back()->with('success', 'Device updated successfully');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }
    public function delete($id){
        device::where('id', $id)->first()->delete();
        return back()->with('success', 'Device deleted successfully');
    }

    public function softDelete($id){
        try {
            $device = device::where('id', $id)->first();
            if (!$device) {
                return back()->with('error', 'Device not found');
            }
            $device->delete();
            return back()->with('success', 'Device deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }



}
