<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Social_work_category;
use App\Models\Social_work_item;


class Social_work_itemController extends Controller
{

    public function index() {

        $categorydata = Social_work_category::query()->get();
        // $data = Service_item::query()->get();
        $data = Social_work_item::query()->with('category')->get();
        // $data = Social_work_item::query()->get();
        // echo '<pre>';
        // print_r( $data ->toArray());
        // die;
        return view( 'social_work.social_work_item.social_work_item', compact( 'data','categorydata' ) );
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
                'id','social_work_category_id','social_work_img','social_work_title','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Social_work_item::select('*')->with('category')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('social_work_category_name', function ($row) {
                    return $row->category->social_work_category_name ? $row->category->social_work_category_name : '';
                })
                // ->addColumn('social_work_img', function ($row) {
                //     $imgUrl = $row->social_work_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="No Image" width="70" height="70">';
                // })
                ->addColumn('social_work_img', function ($row) {
                    $imgPath = $row->social_work_img ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                // ->editColumn('client_description', function ($row) {
                //     return '<a href="'. $row->client_description. '" target="_blank">Click</a>';
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
                            <a href="' . route('social-work-item.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('social-work-item.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','social_work_img','social_work_category_name'])
                ->make(true);
        }
    }

    public function add() {
        $categorydata = Social_work_category::query()->get();
        return view( 'social_work.social_work_item.add_social_work_item',compact( 'categorydata' )  );
    }

    public function store( Request $request )  {

        // print_r( $request->creative_house_video_url );
        // die;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3($request, 'social_work_img', 'social-work-image');
        // if ( $request->hasFile( 'social_work_img' ) ) {
        //     $file = $request->file( 'social_work_img' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'social-work-image/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

        $userId = Auth::user()->id;

        $data = new Social_work_item;
        $data->social_work_category_id = $request->social_work_category_id ?? 0;
        $data->social_work_img = $image ?? '';
        $data->social_work_title = $request->social_work_title ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'home/social_work/social_work_item' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $categorydata = Social_work_category::query()->get();
        $data = Social_work_item::find( $id );
        // print_r( $data->toArray() );
        // die;
        return view( 'social_work.social_work_item.edit_social_work_item', compact( 'data','categorydata' ) );
    }

    public function update( Request $request ) {
        $id = $request->id;

        // $file = $request->file( 'post_image' );
        // $filename = time() . '_' . $file->getClientOriginalName();
        // Append original filename
        // $file->move( 'post-image/', $filename );
        //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //if want store in  storage file then this function


        $image = upload_file_to_s3($request, 'social_work_img', 'social-work-image');
        // if ( $request->hasFile( 'social_work_img' ) ) {

        //     // Retrieve the uploaded file
        //     $file = $request->file( 'social_work_img' );

        //     // Generate a unique filename
        //     $filename = time() . '_' . $file->getClientOriginalName();

        //     // Move the new file to the specified location
        //     $file->move( 'social-work-image/', $filename );
        // }
        // echo $filename;
        // die;
        $data = Social_work_item::find( $id );

        $userId = Auth::user()->id;

        $update = [

            'social_work_category_id' =>$request->social_work_category_id ?? 0,
            'social_work_img'=> $image ??$data->social_work_img ?? '',
            'social_work_title'=> $request->social_work_title ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        // $data = Admin_post::find( $id );
        $data->update( $update );
        // $data->save();

        return redirect( 'home/social_work/social_work_item' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Social_work_item::find( $id );
        $data->delete();

        return redirect( 'home/social_work/social_work_item' );
        // return response()->json( [ 'data' => $data ] );
    }

}
