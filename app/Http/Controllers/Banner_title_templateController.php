<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Banner_title_template;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Banner_title_templateController extends Controller
{

    public function index() {
        $data = Banner_title_template::query()->get();
        // print_r( $data );
        // die;
        return view( 'banner_title_template.index', compact( 'data') );
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;
           
            $sortDirection = $request->get('order')[0]['dir'];
    
            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','banner_name','banner_title','banner_description','banner_total_video','banner_short_text','banner_bg_img','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Banner_title_template::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('banner_bg_img', function ($row) {
                //     $imgUrl = $row->banner_bg_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                ->addColumn('banner_bg_img', function ($row) {
                    $imgPath = $row->banner_bg_img ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                // ->editColumn('website_url', function ($row) {
                //     return '<a href="'. $row->website_url. '" target="_blank">Click</a>';
                // })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex">
                            <a href="' . route('banner-title-template.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('banner-title-template.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','banner_bg_img'])
                ->make(true);
        }
    }

    public function add() {

        return view( 'banner_title_template.add');
    }

    public function store( Request $request )  {


        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3($request, 'banner_bg_img', 'banner-background-image');


        $userId = Auth::user()->id;

        $data = new Banner_title_template;
        $data->banner_name = $request->banner_name ?? '';
        $data->banner_bg_img = $image ?? '';
        $data->banner_title = $request->banner_title?? '';
        $data->banner_description = $request->banner_description?? '';
        $data->banner_total_video = $request->banner_total_video?? '';
        $data->banner_short_text = $request->banner_short_text?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'template/banner_title_template' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $data = Banner_title_template::find($id);
        // print_r( $data->toArray() );
        // die;
        return view( 'banner_title_template.edit', compact( 'data') );
    }

    public function update( Request $request ) {
        $id = $request->id;

        $image = upload_file_to_s3($request, 'banner_bg_img', 'banner-background-image');
   
        $data = Banner_title_template::find( $id );

        $userId = Auth::user()->id;

        $update = [

            'banner_name' => $request->banner_name ?? '',
            'banner_bg_img' => $image ?? $data->banner_bg_img ?? '',
            'banner_title' => $request->banner_title?? '',
            'banner_description' => $request->banner_description?? '',
            'banner_total_video' => $request->banner_total_video?? '',
            'banner_short_text' => $request->banner_short_text?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        $data->update( $update );
        // $data->save();

        return redirect( 'template/banner_title_template' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Banner_title_template::find( $id );
        $data->delete();

        return redirect( 'template/banner_title_template' );
        // return response()->json( [ 'data' => $data ] );
    }

}
