<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

use App\Models\Client;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    
    public function index()
    {
        $data = Client::query()->get();
        // print_r($data);
        // die;
        return view('client.client',compact('data'));
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
                'id','client_img','client_title','display_order','service_display_order','status'
            ]; // value depend on datatable field not in table
    
            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table
            
            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Client::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                // ->addColumn('client_img', function ($row) {
                //     $imgUrl = $row->client_img ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                ->addColumn('client_img', function ($row) {
                    $imgPath = $row->client_img ?? '';
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
                    $previewButton = '';
                    // Check if status is 1, and display the preview button if true
                    if ($row->status == 1) {
                        $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                                <i class="fas fa-eye"></i>
                            </a>';
                    }
                    return '
                        <div class="d-flex">
                            <a href="' . route('client.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            '.$previewButton.'
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('client.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action','client_img'])
                ->make(true);
        }
    }

    public function add(){
        return view('client.add_client');
    }

    public function store( Request $request ) 
    {

        $request->validate( [
            // 'title'=>'required',
        ], [
            // 'name.required' => 'Name cannot be empty',
        ] );

        $image = upload_file_to_s3($request, 'client_img', 'client-image');
       
        $userId=Auth::user()->id;
        
        $data = new Client;
        $data->client_img = $image ?? '';
        $data->client_title = $request->client_title ?? '';
        $data->client_description = $request->client_description ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->service_display_order = $request->service_display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();
        
        return redirect('home/client');
    }

    public function show(Request $request,$id)
    {
            // $id = $request->id;
            $data = Client::find($id);
            return view('client.edit_client',compact('data'));
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $image = upload_file_to_s3($request, 'client_img', 'client-image');

        $data = Client::find($id);
        
        $userId=Auth::user()->id;
    
        $update = [

            'client_img' => $image ?? $data->client_img ?? '',
            'client_title' => $request->client_title ?? '',
            'client_description' => $request->client_description ?? '',            
            'display_order' => $request->display_order ?? 0,
            'service_display_order' => $request->service_display_order ?? 0,
            'user_id' => $userId,
            'status'=> $request->status ?? 0,
          
        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();
        
        return redirect('home/client');
    }

    public function destroy(Request $request,$id)
    {
        // $id = $request->id;
        $data = Client::find($id);
        $data->delete();
        
        return redirect('home/client');
    }




}
