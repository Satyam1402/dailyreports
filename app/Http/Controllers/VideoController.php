<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Video;

use Illuminate\Http\Request;

class VideoController extends Controller 
{

    public function index() {
        $data = Video::query()->get();
        // print_r( $data );
        // die;
        return view( 'video.video', compact( 'data' ) );
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            // Get the smallest display_order value
            $smallestDisplayOrder = Video::where('status', 1)->min('display_order');

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','video_url','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Video::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('banner_video_thumbnail', function ($row) {
                //     $imgUrl = $row->banner_video_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                // ->editColumn('video_url', function ($row) {
                //     return '<a href="'. $row->video_url. '" target="_blank">Click</a>';
                // })
                     ->addColumn('video_url', function ($row) {
                    $videoPath = $row->video_url ?? '';
                    $baseUrl = env('AWS_URL'); 
                    
                    if (!$videoPath) {  
                        return 'No Video Available';
                    }

                    $videoUrl = $baseUrl . '/' . $videoPath; // Concatenate the base URL with the stored path
                    return '<a href="'. $videoUrl. '" target="_blank">Click</a>';
                })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) use ($smallestDisplayOrder) {
                    // Check if the current row has the smallest display_order and status is 1
                    $previewButton = '';
                    
                    if ($row->status == 1 && $row->display_order == $smallestDisplayOrder) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';

                        // $previewButton = '<button type="button" class="mb-1 btn btn-info btn-sm mr-2" onclick="openPreview()">
                        //                     <i class="fas fa-eye"></i> Preview
                        //                   </button>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('video.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('video.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','video_url'])
                ->make(true);
        }
    }

    public function add() {
        return view( 'video.add_video' );
    }

    public function store( Request $request )  {

        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        // $image = upload_file_to_s3($request, 'video_thumbnail_img', 'video-thumbnail');
        $video = upload_file_to_s3($request, 'video_url', 'Home-video');
        // if ( $request->hasFile( 'video_thumbnail_img' ) ) {
        //     $file = $request->file( 'video_thumbnail_img' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'video-thumbnail-img/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

        $userId = Auth::user()->id;

        $data = new Video;
        $data->video_url = $video ?? '';
        // $data->video_thumbnail_img = $image ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'home/video' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $data = Video::find( $id );
        return view( 'video..edit_video', compact( 'data' ) );
    }

    public function update( Request $request ) {
        $id = $request->id;
        // $file = $request->file( 'post_image' );
        // $filename = time() . '_' . $file->getClientOriginalName();
        // Append original filename
        // $file->move( 'post-image/', $filename );
        //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //if want store in  storage file then this function

        // $image = upload_file_to_s3($request, 'video_thumbnail_img', 'video-thumbnail');
        $video = upload_file_to_s3($request, 'video_url', 'Home-video');

        // if ( $request->hasFile( 'video_thumbnail_img' ) ) {

        //     // Retrieve the uploaded file
        //     $file = $request->file( 'video_thumbnail_img' );

        //     // Generate a unique filename
        //     $filename = time() . '_' . $file->getClientOriginalName();

        //     // Move the new file to the specified location
        //     $file->move( 'video-thumbnail-img/', $filename );
        // }
        // echo $filename;
        // die;
        $data = Video::find( $id );

        $userId = Auth::user()->id;

        $update = [

            // 'brand_image' =>  $filename ?? $request->brand_image ?? '',
            'video_url' => $video ?? $data->video_url ?? '',
            // 'video_thumbnail_img' => $image ?? $data->video_thumbnail_img ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        // $data = Admin_post::find( $id );
        $data->update( $update );
        // $data->save();

        return redirect( 'home/video' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Video::find( $id );
        $data->delete();

        return redirect( 'home/video' );
        // return response()->json( [ 'data' => $data ] );
    }
}
