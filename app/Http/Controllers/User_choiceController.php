<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;

use App\Models\User_choice;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User_choiceController extends Controller
{

    public function index() {
        $data = User_choice::query()->get();
        // print_r( $data );
        // die;
        return view( 'user_choice.user_choice', compact( 'data' ) );
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
                'id','user_choice_image','user_choice_title','user_choice_description','user_choice_button_text','display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = User_choice::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })

                ->addColumn('user_choice_image', function ($row) {
                    $imgPath = $row->user_choice_image ?? '';
                    $baseUrl = env('AWS_URL'); // Get the base URL from the .env file
                    $imgUrl = $baseUrl . '/' . $imgPath; // Concatenate the base URL with the stored path
                    return '<img src="' . $imgUrl . '" alt="No Image Available" width="70" height="70">';
                })

                // ->addColumn('brand_image', function ($row) {
                //     $imgUrl = $row->brand_image ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
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
                            <a href="' . route('user-choice.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('user-choice.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','user_choice_image'])
                ->make(true);
        }
    }



    public function add() {
        return view( 'user_choice.add_user_choice' );
    }

    public function store( Request $request )  {

        // print_r( $request->creative_house_video_url );
        // die;
        $request->validate( [
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ] );

        // if ( $request->hasFile( 'development_house_img' ) ) {
        //     $file = $request->file( 'development_house_img' );
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     // Append original filename
        //     $file->move( 'development-house-image/', $filename );
        //     //  $imagepath = $request->file( 'image' )->store( 'uploads' );
        //     //if want store in  storage file then this function
        // }

        $image = upload_file_to_s3($request, 'user_choice_image', 'user-choice-image');

        $userId = Auth::user()->id;

        $data = new User_choice;
        $data->user_choice_title = $request->user_choice_title ?? '';
        $data->user_choice_image = $image ?? '';
        $data->user_choice_description = $request->user_choice_description ?? '';
        $data->user_choice_button_text = $request->user_choice_button_text ?? '';
        $data->user_choice_button_url = $request->user_choice_button_url ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        return redirect( 'template/user_choice' );
    }

    public function show( Request $request, $id ) {
        // $id = $request->id;
        $data = User_choice::find( $id );
        // print_r( $data->toArray() );
        // die;
        return view( 'user_choice.edit_user_choice', compact( 'data' ) );
    }

    public function update( Request $request ) {
        $id = $request->id;

        $data = User_choice::find( $id );

        $image = upload_file_to_s3($request, 'user_choice_image', 'user-choice-image');

        $userId = Auth::user()->id;

        $update = [

            'user_choice_title' => $request->user_choice_title ?? '',
            'user_choice_image' => $image ?? $data->user_choice_image ?? '',
            'user_choice_description' => $request->user_choice_description ?? '',
            'user_choice_button_text' => $request->user_choice_button_text ?? '',
            'user_choice_button_url' => $request->user_choice_button_url ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,

        ];
        // print_r( $update );
        // die;

        // $data = Admin_post::find( $id );
        $data->update( $update );
        // $data->save();

        return redirect( 'template/user_choice' );
    }

    public function destroy( Request $request, $id ) {
        // $id = $request->id;
        $data = User_choice::find( $id );
        $data->delete();

        return redirect( 'template/user_choice' );
    }

}
