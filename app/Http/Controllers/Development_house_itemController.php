<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Development_house_category;
use App\Models\Development_house_item;

use Illuminate\Http\Request;

class Development_house_itemController extends Controller
{

    public function index() {

        $categorydata = Development_house_category::query()->get();
        $data = Development_house_item::query()->with('category')->get();
        // $data = Development_house_item::query()->get();
        // print_r( $data );
        // die;
        return view( 'development_house.development_house_item.development_house_item', compact( 'data','categorydata' ) );
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
                'id','development_house_category_id','development_house_img','development_house_url','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Development_house_item::select('*')->with('category')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('development_house_category_name', function ($row) {
                    return $row->category->development_house_category_name ? $row->category->development_house_category_name : 'N/A';
                })
                // ->addColumn('development_house_img', function ($row) {
                //     $imgUrl = $row->development_house_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Image" width="70" height="70">';
                // })
                ->addColumn('development_house_img', function ($row) {
                    $imgPath = $row->development_house_img ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })
                ->editColumn('development_house_url', function ($row) {
                    return '<a href="'. $row->development_house_url. '" target="_blank">Click</a>';
                })
                // ->editColumn('created_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                // })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex">
                            <a href="' . route('development-house-item.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('development-house-item.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['development_house_category_name','status','action','development_house_img','development_house_url'])
                ->make(true);
        }
    }


    public function add() {
        $categorydata = Development_house_category::query()->get();
        return view( 'development_house.development_house_item.add_development_house_item',compact( 'categorydata' ) );
    }

    public function store( Request $request )  {

        // print_r( $request->creative_house_video_url );
        // die;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        $image = upload_file_to_s3($request, 'development_house_img', 'development-house-image');
        // if ( $request->hasFile( 'development_house_img' ) ) {
        //     $file = $request->file( 'development_house_img' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'development-house-image/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

        $userId = Auth::user()->id;

        $data = new Development_house_item;
        $data->development_house_category_id = $request->development_house_category_id??0;
        $data->development_house_img = $image ?? '';
        $data->development_house_url = $request->development_house_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'home/development_house/development_house_item' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $categorydata = Development_house_category::query()->get();
        $data = Development_house_item::find($id);
        // print_r( $data->toArray() );
        // die;
        return view( 'development_house.development_house_item.edit_development_house_item', compact( 'data','categorydata' ) );
    }

    public function update( Request $request ) {
        $id = $request->id;

        // $file = $request->file( 'post_image' );
        // $filename = time() . '_' . $file->getClientOriginalName();
        // Append original filename
        // $file->move( 'post-image/', $filename );
        //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //if want store in  storage file then this function

        $image = upload_file_to_s3($request, 'development_house_img', 'development-house-image');
        // if ( $request->hasFile( 'development_house_img' ) ) {

        //     // Retrieve the uploaded file
        //     $file = $request->file( 'development_house_img' );

        //     // Generate a unique filename
        //     $filename = time() . '_' . $file->getClientOriginalName();

        //     // Move the new file to the specified location
        //     $file->move( 'development-house-image/', $filename );
        // }
        // echo $filename;
        // die;
        $data = Development_house_item::find( $id );

        $userId = Auth::user()->id;

        $update = [

            'development_house_category_id' => $request->development_house_category_id ?? 0,
            'development_house_img' => $image ?? $data->development_house_img ?? '',
            'development_house_url' => $request->development_house_url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        // $data = Admin_post::find( $id );
        $data->update( $update );
        // $data->save();

        return redirect( 'home/development_house/development_house_item' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = Development_house_item::find( $id );
        $data->delete();

        return redirect( 'home/development_house/development_house_item' );
        // return response()->json( [ 'data' => $data ] );
    }

}
