<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::orderBy('sort_order', 'asc')->latest()->get();
        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link'  => 'required|string', // Product ya Category ka link
            'video' => 'required|mimes:mp4,mov,ogg,qt|max:20000', // Max 20MB (Adjust php.ini if needed)
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Poster Image
        ]);

        $videoPath = null;
        $imagePath = null;

        // 1. Upload Video
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $filename = time() . '_vid.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/videos'), $filename);
            $videoPath = 'uploads/videos/' . $filename;
        }

        // 2. Upload Poster Image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_thumb.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/videos/thumbs'), $filename);
            $imagePath = 'uploads/videos/thumbs/' . $filename;
        }

        Video::create([
            'title' => $request->title,
            'link' => $request->link,
            'video' => $videoPath,
            'image' => $imagePath,
            'status' => $request->status ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Video Uploaded Successfully');
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'link'  => 'required|string',
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:20000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Update Video File if New Uploaded
        if ($request->hasFile('video')) {
            // Old delete
            if (File::exists(public_path($video->video))) {
                File::delete(public_path($video->video));
            }
            $file = $request->file('video');
            $filename = time() . '_vid.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/videos'), $filename);
            $video->video = 'uploads/videos/' . $filename;
        }

        // Update Image if New Uploaded
        if ($request->hasFile('image')) {
            if (File::exists(public_path($video->image))) {
                File::delete(public_path($video->image));
            }
            $file = $request->file('image');
            $filename = time() . '_thumb.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/videos/thumbs'), $filename);
            $video->image = 'uploads/videos/thumbs/' . $filename;
        }

        $video->update([
            'title' => $request->title,
            'link' => $request->link,
            'status' => $request->status ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.videos.index')->with('success', 'Video Updated Successfully');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        if (File::exists(public_path($video->video))) {
            File::delete(public_path($video->video));
        }
        if (File::exists(public_path($video->image))) {
            File::delete(public_path($video->image));
        }
        $video->delete();
        return redirect()->back()->with('success', 'Video Deleted');
    }
}
