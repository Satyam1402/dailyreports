<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Gallery;

use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $data = Gallery::query()->get();
        return view('gallery.index',compact('data'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = Gallery::select('*')->orderBy('updated_at', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('image', function ($row) {
                //     return '<img src="' . $row->image_file . '" alt="Gallery Image" style="width:150px; height:auto;">';
                // })
                ->addColumn('image', function ($row) {
                    $imgPath = $row->image_file ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->editColumn('image_file', function ($image) {
                    $baseUrl = env('AWS_URL');
                    $imgPath = $image->image_file;
                    $filePath = $baseUrl . '/' . $imgPath;
                    return '
                        <button class="btn btn-sm btn-primary copy-btn" data-path="' . $filePath . '" data-toggle="tooltip" data-placement="top" title="Copy Path">
                            <i class="fas fa-copy"></i>
                        </button>';
                })
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->editColumn('updated_at', function ($row) {
                    return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('gallery.edit', $row->id) . '" class="mb-1 btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('gallery.destroy', $row->id) . '\');">
                            <i class="fas fa-trash"></i>
                        </a>
                    ';
                })
                ->rawColumns(['image_file','image','status','action'])
                ->make(true);
        }
    }

    public function add()
    {
        return view('gallery.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_title' => 'required',
            'image_file'=>'required',
            // 'status'=>'required',
            
        ]);
        // $fileDetails = [
        //     'original_name' => $file->getClientOriginalName(),
        //     'file_name' =>     pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
        //     'mime_type'     => $file->getMimeType(),
        //     'size'          => $file->getSize(),
        //     'extension'     => $file->getClientOriginalExtension(),
        //     'path'          => $file->getPathName(),
        // ];

        $image = upload_file_to_s3($request, 'image_file', 'Gallery-image-file');

        $userId=Auth::user()->id;

        // if ( $request->hasFile('image_file') ) {
        //     $file = $request->file( 'image_file' );
        //     $ext = $file->getClientOriginalExtension();
        //     $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //     $file_slug = $this->generateSlug($file_name);
        //     $filename = time().'-'.$file_slug.'.'.$ext;
        //     $file->move( 'gallery-image/', $filename );
        //     $filePath = asset('gallery-image/' . $filename);
        // } else {
        //     $filePath = asset('gallery-image/' . 'default.png');
        // }

        $data = new Gallery;
        $data->image_title = $request->image_title ?? '';
        $data->image_file = $image ?? '';
        $data->user_id = $userId;
        $data->display_order = $request->display_order ?? 0;
        $data->status=$request->status ?? 0;
        $data->save();
        return redirect('gallery');
    }

    public function edit(Request $request,$id)
    {
        $data = Gallery::find($id);
        return view('gallery.edit',compact('data'));
    }

    public function update(Request $request)
    {   
        $request->validate([
            'image_title' => 'required',
            'status' => 'required',
        ]);

        $id = $request->id;
        $data = Gallery::find($id);

        $userId=Auth::user()->id;

        if ($request->hasFile('image_file')) {
            $image = upload_file_to_s3($request, 'image_file', 'Gallery-image-file');
            // $file = $request->file('image_file');
            // $ext = $file->getClientOriginalExtension();
            // $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // $file_slug = $this->generateSlug($file_name);
            // $filename = time() . '-' . $file_slug . '.' . $ext;
            // $file->move('gallery-image/', $filename);
            // $filePath = asset('gallery-image/' . $filename);
        } else {
            $image = $data->image_file ?? null;
        }

        $update = [
            'image_title' => $request->image_title ?? '',
            'image_file' => $image,
            'user_id' => $userId,
            'display_order' => $request->display_order ?? 0,
            'status' => $request->status,
        ];

        $data->update($update);
        
        return redirect('gallery');
    }


    // public function generateSlug($file_name)
    // {
    //     $slug = strtolower($file_name); 
    //     $slug = preg_replace('/\s+/', '-', $slug);
    //     $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

    //     return $slug;
    // }

    public function destroy(Request $request,$id)
    {
        $data = Gallery::find($id);
        $data->delete();

        return redirect('gallery');
    }
}
