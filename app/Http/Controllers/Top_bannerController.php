<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Top_banner;
use App\Models\Book_call;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Top_bannerController extends Controller
{
    public function index(){

        $data = Top_banner::query()->get();
        $bookcalldata = Book_call::query()->get();

        return view('home.top_banner.top_banner' , compact('data'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $smallestDisplayOrder = Top_banner::where('status', 1)->min('display_order');


            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','heading','sub_heading','banner_button_text','banner_video_thumbnail','banner_video_url','display_order',
                'status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Top_banner::select('*')->orderBy($sortColumn, $sortDirection);

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
                ->addColumn('banner_video_thumbnail', function ($row) {
                    $imgPath = $row->banner_video_thumbnail ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                
                ->editColumn('banner_video_url', function ($row) {
                    return '<a href="'. $row->banner_video_url. '" target="_blank">Click</a>';
                })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) use ($smallestDisplayOrder) {

                $previewButton = '';
                
                if ($row->status == 1 && $row->display_order == $smallestDisplayOrder) {
                    $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                            <i class="fas fa-eye"></i>
                        </a>';
                }
                    return '
                        <div class="d-flex">
                            <a href="' . route('top-banner.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                             ' . $previewButton . '
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('top-banner.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','banner_video_thumbnail','banner_video_url'])
                ->make(true);
        }
    }

    public function add(){
        // $data = Top_banner::query()->get();
        $bookcalldata = Book_call::query()->get();

        return view('home.top_banner.add_top_banner',compact('bookcalldata'));
    }

    public function store(Request $request){
        
        $request->validate([
            // 'heading'=>'required',
        ],[
            // 'heading.required'=>'heading cannot be empty'
        ]);

        $image = upload_file_to_s3($request, 'banner_video_thumbnail', 'banner-video-thumbnail');

        $userId=Auth::user()->id;
        // print_r($userId);
        // die;

        $data = new Top_banner;
        $data->book_call_template_id = $request->book_call_template_id ?? 0;
        $data->heading = $request->heading ?? '';
        $data->sub_heading = $request->sub_heading ?? '';
        $data->banner_button_text = $request->banner_button_text ?? '';
        $data->banner_button_url = $request->banner_button_url ?? '';
        $data->banner_video_url = $request->banner_video_url ?? '';
        $data->banner_video_thumbnail = $image ?? '';
        $data->display_order=$request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status=$request->status ?? 0;
        $data->save();

        return redirect('home/top_banner');
    }

    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Top_banner::find($id);
            $bookcalldata = Book_call::query()->get();

            
            return view('home.top_banner.edit_top_banner',compact('data','bookcalldata'));
            // return response()->json(['data' => $data]);
    }

    public function update(Request $request)
            {
                $id = $request->id;
                // ECHO '<PRE>';
                // print_r($request->banner_video_thumbnail);
                // die;
               
                $image = upload_file_to_s3($request, 'banner_video_thumbnail', 'banner-video-thumbnail');

                $data = Top_banner::find($id);
               
                $userId=Auth::user()->id;
            
                $update = [
                    'book_call_template_id'=>$request->book_call_template_id ?? 0,
                    'heading' => $request->heading ?? '',
                    'sub_heading' => $request->sub_heading ?? '',
                    'banner_button_text' =>$request->banner_button_text ?? '',
                    'banner_button_url'=> $request->banner_button_url ?? '',
                    'banner_video_url' =>$request->banner_video_url ?? '',
                    'banner_video_thumbnail'=>$image??$data->banner_video_thumbnail?? '',
                    'display_order'=>$request->display_order ?? 0,
                    'user_id' => $userId,
                    'status'=>   $request->status ?? 0,
                ];
                // print_r($update);
                // die;

                // $data = Admin_post::find($id);
                $data->update($update);
                $data->save();
                
                return redirect('home/top_banner');;
            }


    public function destroy(Request $request,$id)
    {
        // $id = $request->id;
        $data = Top_banner::find($id);
        $data->delete();
        
        return redirect('home/top_banner');
        // return response()->json(['data' => $data ] );
    }
}
