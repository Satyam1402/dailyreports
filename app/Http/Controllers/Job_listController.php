<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;


use App\Models\Job_list;
use App\Models\Job_applicants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class Job_listController extends Controller
{

    public function index()
    {
        $data = Job_list::query()->get();
        // print_r($data->toArray());
        // die;
        return view('job.job_list.index', compact('data'));
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
                'id',
                'job_title',
                'job_experience',
                'job_type',
                'job_location',
                'job_salary',
                'job_description',
                'display_order',
                'user_id',
                'status',
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $data = Job_list::select('*')->orderBy($sortColumn, $sortDirection);

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
                // ->editColumn('banner_video_url', function ($row) {
                //     return '<a href="'. $row->banner_video_url. '" target="_blank">Click</a>';
                // })
                ->editColumn('workplace_type', function ($row) {

                    $workplaceType = $row->workplace_type;
                    switch ($workplaceType) {
                        case 'on_site':
                            return 'On-Site';
                        case 'remote':
                            return 'Remote';
                        case 'hybrid':
                            return 'Hybrid';
                        default:
                            return 'Not Specified';
                    }

                })


                ->editColumn('job_type', function ($row) {

                    $jobType = $row->job_type;
                    switch ($jobType) {
                        case 'full_time':
                            return 'Full Time';
                        case 'part_time':
                            return 'Part Time';
                        case 'freelance':
                            return 'Freelance';
                        case 'internship':
                            return 'Internship';
                        case 'contract':
                            return 'Contract';
                        default:
                            return 'Not Specified';
                    }
                })


                ->editColumn('job_experience', function ($row) {

                    return $row->job_experience == 0 ? 'Fresher' : "{$row->job_experience} years";
                })

                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->addColumn('action', function ($row) {
                    $previewButton = '';
                    // if ($row->status == 1) {
                    //     $previewButton = '<a href="https://d1r5jv57b9z1uu.cloudfront.net/" target="_blank" class="mb-1 btn btn-info btn-sm mr-2">
                    //             <i class="fas fa-eye"></i>
                    //         </a>';
                    // }
                    return '
                        <div class="d-flex">
                            <a href="' . route('job-list.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            ' . $previewButton . '
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('job-list.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->addColumn('navigate', function ($row) {
                    $applicantCount = Job_applicants::where('job_id', $row->id)->count();
                    return '
                        <div class="d-flex flex-column">
                            <a href="' . route('applicant-info.index', ['job_id' => $row->id]) . '" class="mb-1 btn btn-primary btn-sm text-nowrap">
                                Applicant Details( ' . $applicantCount . ' )
                            </a>                        
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action', 'navigate'])
                ->make(true);
        }

    }
    public function add()
    {

        return view('job.job_list.add');

    }

    public function store(Request $request)
    {
        $request->validate([
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ]);


        $userId = Auth::user()->id;

        $data = new Job_list;
        $data->job_title = $request->job_title ?? '';
        $data->job_location = $request->job_location ?? '';
        $data->job_experience = $request->job_experience ?? '';
        $data->job_type = $request->job_type ?? '';
        $data->workplace_type = $request->workplace_type ?? '';
        $data->job_salary = $request->job_salary ?? '';
        $data->job_description = $request->job_description ?? '';
        $data->display_order = $request->display_order ?? 0;
        $data->user_id = $userId;
        $data->status = $request->status ?? 0;
        $data->save();

        // return redirect('group/service/service_category');
        return redirect()->route('job-list.index');


    }

    public function show(Request $request, $id)
    {
        $data = Job_list::find($id);
        //   echo '<pre>';
        //   print_r($data->toArray());
        //   die;
        return view('job.job_list.edit', compact('data'));
    }

    public function update(Request $request)
    {

        $request->validate([
            // 'title'=>'required',

        ], [
            // 'name.required' => 'Name cannot be empty',

        ]);

        $id = $request->id;


        $data = Job_list::find($id);

        $userId = Auth::user()->id;

        $update = [

            'job_title' => $request->job_title ?? '',
            'job_location' => $request->job_location ?? '',
            'job_experience' => $request->job_experience ?? '',
            'job_type' => $request->job_type ?? '',
            'workplace_type' => $request->workplace_type ?? '',
            'job_salary' => $request->job_salary ?? '',
            'job_description' => $request->job_description ?? '',
            'display_order' => $request->display_order ?? 0,
            'user_id' => $userId,
            'status' => $request->status ?? 0,

        ];
        // print_r($update);
        // die;

        $data->update($update);
        // $data->save();

        return redirect()->route('job-list.index');

    }

    public function destroy($id)
    {
        $data = Job_list::find($id);
        $data->delete();

        return redirect()->route('job-list.index');
    }

    public function upload_job()
    {
        return view('job.upload.upload_job');
    }
    public function upload_job_store(Request $request)
    {

        $request->validate([
            // 'dropdown' => 'required|in:video,shorts',
            'csv_file' => 'required|file|mimes:csv|max:20000',
        ]);

        $file = $request->file('csv_file');
        $fileName = $file->getClientOriginalName();
        $filePath = 'job_upload/' . $fileName;

        // Check if file with the same name already exists
        // if (Storage::disk('local')->exists($filePath)) {
        //     return response()->json(['error' => 'This file has already been uploaded.'], 400);
        // }

        // Store the file
        // $dropdownValue = $request->input( 'dropdown' );
        // $path = $request->file( 'csv_file' )->store( 'csv_files_video' );

        // Store the file if it doesn’t exist
        $path = $file->storeAs('job_upload', $fileName, 'local');

        // Read and process the CSV file
        // $filedata = Storage::path( $path );
        $filedata = storage_path('app/' . $path);
        // dd($filedata); 
        $data = array_map('str_getcsv', file($filedata));
        // print_r($data);
        // die;

        // Remove the header row
        $header = array_shift($data);
        // echo '<pre>';
        // print_r($data);
        // die();

        // $dataToInsert = [];


        $userId = Auth::user()->id;

        foreach ($data as $row) {
            // Check if the row has the correct number of columns
            // if (count($row) == 9) { // Adjust the count based on your CSV structure
            //     Job_list::create([
            //         'job_title'       => $row[0],  // HR
            //         'job_experience'  => $row[1],  // 2
            //         'job_type'        => $row[2],  // full_time
            //         'workplace_type'  => $row[3],  // remote
            //         'job_location'    => $row[4],  // pune
            //         'job_salary'      => $row[5],  // 120000
            //         'job_description' => $row[6],  // hello world 1dflndfdmf
            //         'display_order'   => $row[7],  // 11
            //         'user_id'         => $userId,  // 1
            //         'status'          => $row[8],        // Assuming status is active (or adjust as needed)
            //     ]);
            // }
            // Assuming $data is already populated with CSV data
            $successfulInserts = 0;
            $failedRows = [];

            foreach ($data as $index => $row) {
                // Validate if the row has exactly 9 columns
                if (count($row) == 9) {
                    try {
                        Job_list::create([
                            'job_title' => $row[0],  // HR
                            'job_experience' => $row[1],  // 2
                            'job_type' => $row[2],  // full_time
                            'workplace_type' => $row[3],  // remote
                            'job_location' => $row[4],  // pune
                            'job_salary' => $row[5],  // 120000
                            'job_description' => $row[6],  // hello world 1dflndfdmf
                            'display_order' => $row[7],  // 11
                            'user_id' => $userId,  // 
                            'status' => $row[8],        // Assuming status is active
                        ]);
                        $successfulInserts++;
                    } catch (\Exception $e) {
                        $failedRows[] = [
                            'row' => $index + 1,
                            'error' => $e->getMessage()
                        ];
                    }
                }
                 else {
                    $failedRows[] = [
                        'row' => $index + 1,
                        'error' => 'Invalid format. Expected 9 columns, found ' . count($row)
                    ];
                }
            }

            // Final response
            if (!empty($failedRows)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Some rows failed to insert.',
                    'failed_rows' => $failedRows
                ], 400);
            } else {
                return $successfulInserts;
                // return response()->json([
                //     'status' => 'success',
                //     'message' => "$successfulInserts jobs added successfully!"
                // ]);
            }
        }

    }



}
