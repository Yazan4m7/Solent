<?php

namespace App\Http\Controllers;

use App\galleryMedia;
use Google\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Display icons page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $media = GalleryMedia::all();
        return view('media.index',compact('media'));
    }
    public function create()
    {
        return view('media.create');
    }

    public function createPost(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|min:3|max:40',
            'video' => 'required|file|mimetypes:video/mp4',
            'image' => 'required|image|mimes:jpg',
        ]);
        $media= new GalleryMedia();
        $media->text = $request->title;
        $media->save();
        Storage::disk('my_files')->makeDirectory('/gallery/' . $media->id);
        $path = public_path('/gallery/' . $media->id);
        try {
            $request->file('video')->move($path,'video.mp4');
            $request->file('image')->move($path,'thumbnail.jpg');
            $media = GalleryMedia::all();
            return redirect()->route("media-index")->with("success", "Media created successfully");
        }
        catch (Exception $e){
            $media->forceDelete();
            return view('media.create')->with("error","Unknown error");
        }
    }


    public function edit($id)
    {
        $media = GalleryMedia::where('id',$id)->first();
        return view('media.edit')->with("media",$media);
    }
    public function editPost(Request $request)
    {
        $media = GalleryMedia::where('id',$request->media_id)->first();
        $path = public_path(). '/media/'.$media->id;
        if($request->hasFile("video"))
        $request->file('video')->move($path,$media->id.'.mp4');
        if($request->hasFile("image"))
        $request->file('image')->move($path,$media->id.'.jpg');
        $media->text = $request->title;
        $media->save();
        return redirect()->route("media-index",compact('media'))->with("success", "Media updated successfully");

    }

    public function delete($id)
    {
        $media = GalleryMedia::where('id',$id)->first();
        $deleted = File::deleteDirectory(public_path(). '/gallery/'.$media->id);
        if($deleted) {
            $media->delete();
            return back()->with("success", "Media deleted successfully");
        }
        else
            return back()->with("error", "Unable to delete media at ". public_path(). '/gallery/'.$media->id);
    }
}
