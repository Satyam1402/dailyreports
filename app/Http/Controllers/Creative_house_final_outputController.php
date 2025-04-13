<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Models\Creative_house_category;
use App\Models\Creative_house_item;
use App\Models\Creative_house_final_output;
use Illuminate\Support\Facades\Auth;


class Creative_house_final_outputController extends Controller
{

    public function index($item_id = null) {
        $itemdata = Creative_house_item::query()->get();
        $data = Creative_house_final_output::query()->with('item')->get();
        // $data = Creative_house_item::query()->get();
        // print_r( $data );
        // die;
        return view( 'creative_house.creative_house_final_output.creative_house_final_output', compact( 'data','itemdata','item_id' ) );
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            // $columns = [
            //     'id','creative_house_item_id','approach_title','approach_thumbnail','approach_video_url','approach_heading','approach_description','display_order','status'
            // ];
            $columns = [
                'id','creative_house_item_id','final_output_title','final_output_video_url','final_output_thumbnail','display_order','status'
            ];
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            // $data = Creative_house_final_output::select('*')->with('item')->orderBy($sortColumn, $sortDirection);

            $query = Creative_house_final_output::select('*')->with('item.category');

            // Check if item_id is passed in the request and filter based on it
            if ($request->has('item_id') && $request->item_id != null) {
                $query->where('creative_house_item_id',  $request->input('item_id') ); // Filter by item_id
            }

            // Apply sorting
            $query->orderBy($sortColumn, $sortDirection);

            // Get the data
            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('creative_house_video_title', function ($row) {
                    return $row->item->creative_house_video_title ? $row->item->creative_house_video_title : '';
                })
                // ->addColumn('final_output_thumbnail', function ($row) {
                //     $imgUrl = $row->final_output_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Image" width="70" height="70">';
                // })
                ->addColumn('final_output_thumbnail', function ($row) {
                    $imgPath = $row->final_output_thumbnail ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->editColumn('final_output_video_url', function ($row) {
                    return '<a href="'. $row->final_output_video_url. '" target="_blank">Click</a>';
                })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    $previewButton ='';
                    // Check if all three statuses are 1
                    if ($row->status == 1 && $row->item->status == 1 && $row->item->category->status == 1) {
                        // If all statuses are 1, display the preview button
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/Single-Video/' . $row->item->id . '" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                            <i class="fas fa-eye"></i>
                                        </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('creative-house-final-output.show', ['id' => $row->id, 'item_id' => request('item_id')]) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('creative-house-final-output.destroy', ['id' => $row->id, 'item_id' => request('item_id')]) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['creative_house_video_title','status','action','final_output_thumbnail','final_output_video_url'])
                ->make(true);
        }
    }

    public function add($item_id = null) {

        $itemdata = Creative_house_item::query()->get();

        return view( 'creative_house.creative_house_final_output.add_creative_house_final_output',compact('itemdata','item_id'));
    }

    public function store( Request $request )  {

        $item_id = $request->item_id;

        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3($request, 'final_output_thumbnail', 'creative-house-final-ouptut-thumbnail');
        $video = upload_file_to_s3($request, 'final_output_video_url', 'creative-house-final-output-video');
        // if ( $request->hasFile( 'creative_house_thumbnail' ) ) {
        //     $file = $request->file( 'creative_house_thumbnail' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'creative-house-thumbnail/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

        $userId = Auth::user()->id;

        $data = new Creative_house_final_output;
        $data->creative_house_item_id = $request->creative_house_item_id??0;
        $data->final_output_title = $request->final_output_title?? '';
        $data->final_output_thumbnail = $image ?? '';
        $data->final_output_upload_video_url = $video ?? '';
        $data->final_output_video_url = $request->final_output_video_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect()->route('creative-house-final-output.index',['item_id'=>$item_id]);
        // return redirect( 'creative_house/creative_house_final_output' );
    }

    public function show( Request $request, $id ,$item_id= null) {

        $itemdata = Creative_house_item::query()->get();
        $data = Creative_house_final_output::find($id);

        return view( 'creative_house.creative_house_final_output.edit_creative_house_final_output', compact( 'data','itemdata','item_id') );
    }

    public function update( Request $request ) {

        $id = $request->id;
        $item_id = $request->item_id;

        $image = upload_file_to_s3($request, 'final_output_thumbnail', 'creative-house-final-output-thumbnail');


        $data = Creative_house_final_output::find( $id );
        $userId = Auth::user()->id;

        $video = null;
        if ($request->hasFile('final_output_upload_video_url')) {
            $video = upload_file_to_s3($request, 'final_output_upload_video_url', 'creative-house-final-output-video');        // if ( $request->hasFile( 'creative_house_thumbnail' ) ) {
        } elseif ($request->has('existing_video') && $request->existing_video) {
            $video = $request->existing_video;
        } else {
            $video = ''; 
        }

        $update = [

            'creative_house_item_id' => $request->creative_house_item_id ?? 0,
            'final_output_title' =>  $request->final_output_title ?? '',
            'final_output_thumbnail' => $image ??$data->final_output_thumbnail ?? '',
            'final_output_upload_video_url'=>$video,
            'final_output_video_url' => $request->final_output_video_url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        // $data = Admin_post::find( $id );
        $data->update( $update );
        // $data->save();

        return redirect()->route('creative-house-final-output.index',['item_id'=>$item_id]);
        // return redirect( 'creative_house/creative_house_final_output' );
    }

    public function destroy( Request $request, $id ,$item_id=null) {

        $data = Creative_house_final_output::find( $id );
        $data->delete();

        return redirect()->route('creative-house-final-output.index',['item_id'=>$item_id]);
        // return redirect( 'creative_house/creative_house_final_output' );
    }

}
