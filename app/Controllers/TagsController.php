<?php

namespace App\Http\Controllers;
use App\tag;
use Illuminate\Http\Request;
use DB;


class TagsController extends Controller
{
    public function index(){
        $tags = tag::all();
        return view('tags.index',compact("tags"));
    }

    public function returnCreate()
    {
        return view('tags.create');
    }
    public function create(Request $request)
    {

        $newTag = new tag();

        try {
            $newTag->text = $request->tag_text;
            $newTag->color = $request->tag_color;
            $newTag->icon = $request->tag_icon;
            $newTag->save();
            return redirect()->route( 'tags-index' )->with('success', 'Tag has been successfully created');

        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }
    public function returnUpdate($id)
    {

        $tag = tag::findOrFail($id);
        return view('tags.edit',compact('tag'));
    }
    public function update(Request $request)
    {
        try {
            $tag = tag::where('id', $request->tag_id)->first();
            if (!$tag) {
                return back()->with('Tag Not found');
            }
            $tag->text = $request->tag_text;
            $tag->color = $request->tag_color;
            $tag->icon = $request->tag_icon;
            $tag->save();


            return back()->with('success', 'Tag has been successfully updated');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

}