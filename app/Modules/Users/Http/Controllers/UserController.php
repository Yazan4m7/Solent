<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;

use App\GalleryMedia;
use Illuminate\Http\Request;
use App\User;
use App\UserPermission;
use App\Permission;
use DB;
use Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $status =$request->status != null ? $request->status  :1 ;
        if($status == 0){
            if(isset($users))
                $users = $users->where('status', 0);
            else
                $users = User::where('status', 0);
        }
        if($status == 1) {
            if(isset($users))
                $users = $users->where('status', 1);
            else
                $users = User::where('status', 1);

        }

        $users = $users->paginate(20)->appends(['status' => $status, 'search' => $request->search]);

return view('users.index')->with('users', $users)->with('status',$status)->with('search', $request->search);
    }

        public function returnCreate()
    {
        $permissions = Permission::all();
        return view('users.create')->with('permissions', $permissions);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users,username',
            'first_name'     => 'required',
            'last_name'    => 'required',
            'password' => 'required|confirmed|min:1',
            'password_confirmation' => 'required',
            'phone'    => 'required',
            'permission' => 'required_if:is_admin,null',
            'permission.*' => 'exists:permissions,id',
            'photo' => 'nullable|image|mimes:png|max:5120',
            'driver_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $transaction = DB::transaction(function ()  use ($request) {
            $admin = $request->is_admin ? 1 : 0;
            $hasPhoto = $request->hasFile('photo') ? 1 : 0;

            // Prepare data for user creation
            $userData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'username' => $request->username,
                'is_admin' => $admin,
                'name_initials' => $request->name_initials,
                'has_photo' => $hasPhoto
            ];

            // Handle driver image for delivery personnel
            if ($request->hasFile('driver_image') && !$admin && in_array(131, $request->permission ?? [])) {
                $driverImage = $request->file('driver_image');
                $imageName = 'driver_' . time() . '.' . $driverImage->getClientOriginalExtension();
                $imagePath = 'users/drivers/' . $imageName;

                // Make sure directory exists
                $directory = public_path('users/drivers');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Move the file
                $driverImage->move($directory, $imageName);

                // Add image path to user data
                $userData['img'] = $imagePath;
            }

            $users = User::create($userData);

            // Handle profile image upload
            if ($request->hasFile('photo')) {
                // Create directory if it doesn't exist
                $path = public_path('/users/' . $users->id);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }

                // Move the uploaded file
                $request->file('photo')->move($path, 'profile_picture.png');
            }

            if (!$request->is_admin && $request->permission) {
                foreach ($request->permission as $permission) {
                    $perm = new UserPermission();
                    $perm->user_id = $users->id;
                    $perm->permission_id = $permission;
                    $perm->save();
                }
            }
            return $users;
        });
        if ($transaction == true) {
            return back()->with('success', 'The user has been successfully created');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    public function edit($id)
    {
        $user = User::with('permissions')->where('id', $id)->first();
        if (!$user) {
            abort(404);
        }
        $permissions = Permission::all();

        return view('users.edit')->with('user', $user)->with('permissions', $permissions);
    }


    public function block($id){
        $user = User::where('id', $id)->first();
        if (!$user) {
            abort(404);
        }
        if($user->status){
            $user->status = 0;
        } else {
            $user->status = 1;
        }

        $user->save();
        return back()->with('success', 'User has been updated');
    }

    public function update(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if (!$user) {
            abort(404);
        }
        $this->validate($request, [
            'id'    => 'required',
            'first_name'     => 'required',
            'last_name'     => 'required',
            'phone' => 'required',
            'permission' => 'required_if:is_admin,null',
            'permission.*' => 'exists:permissions,id',
            'status' => 'nullable',
            'password_confirmation' => 'min:1|max:200|nullable',
            'driver_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $transaction = DB::transaction(function ()  use ($request, $user) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->name_initials = $request->name_initials;

            ////////////// Profile Image Part
            if ($request->hasFile("photo")) {
                \Log::info('Photo file detected in request');

                // Validate the file
                $request->validate([
                    'photo' => 'required|image|mimes:png|max:5120',
                ]);

                \Log::info('Photo validation passed');

                // Create directory if it doesn't exist
                $path = public_path('/users/' . $user->id);
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                    \Log::info('Created directory: ' . $path);
                }

                // Delete old profile picture if it exists
                $oldImagePath = $path . '/profile_picture.png';
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                    \Log::info('Deleted old profile picture');
                }

                // Move the uploaded file
                $uploadResult = $request->file('photo')->move($path, 'profile_picture.png');
                \Log::info('File upload result: ' . ($uploadResult ? 'success' : 'failed'));

                // Update user photo flag
                $user->has_photo = 1;
                \Log::info('Updated user has_photo flag to 1');
            } else {
                \Log::info('No photo file in request');
            }

            // Handle driver image for delivery personnel
            if ($request->hasFile('driver_image') && !$request->is_admin &&
                (is_array($request->permission) && in_array(131, $request->permission))) {

                $driverImage = $request->file('driver_image');
                $imageName = 'driver_' . $user->id . '_' . time() . '.' . $driverImage->getClientOriginalExtension();
                $imagePath = 'users/drivers/' . $imageName;

                // Make sure directory exists
                $directory = public_path('users/drivers');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }

                // Delete old driver image if exists
                if (!empty($user->img) && file_exists(public_path($user->img))) {
                    unlink(public_path($user->img));
                }

                // Move the file
                $driverImage->move($directory, $imageName);

                // Update user's img field
                $user->img = $imagePath;
            }



            if ($request->permission) {
                UserPermission::where('user_id', $request->id)->delete();
                if (!$request->is_admin && count($request->permission)) {
                    foreach ($request->permission as $permission) {
                        $perm = new UserPermission();
                        $perm->user_id = $request->id;
                        $perm->permission_id = $permission;
                        $perm->save();
                    }
                    $user->is_admin = false;
                    $permissions =  UserPermission::where('user_id', $request->id)->get();
                    Cache::forget('user'.$request->id);
                    Cache::forever('user'.$user->id,$permissions);
                }
            }
            $new_password      = $request->get('password_confirmation');
            if ($new_password) {
                User::where('id', $request->id)->update([
                    'password' => Hash::make($new_password)
                ]);
            }
            $user->status = $request->status == 'on' ? 1 : 0;
            if ($request->is_admin) {
                UserPermission::where('user_id', $request->id)->delete();
                $user->is_admin = true;
            }
            return $user->save();
        });
        if ($transaction == true) {
            return back()->with('success', 'The user has been updated successfully');
        } else {
            return back()->with('error', 'Something went wrong!');
        }
    }

    public function softDelete($id)
    {
        try {
            $user = User::where('id', $id)->first();
            if (!$user) {
                return back()->with('error', 'User not found');
            }
            $user->delete();
            return back()->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
