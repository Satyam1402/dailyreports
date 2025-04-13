<?php

namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;

use App\Models\Job_applicants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Job_applicant_infoController extends Controller
{
    public function index($job_id = null){
        $data = Job_applicants::query()->get();

        // print_r($data->toArray());
        // print_r($job_id);
        // die;
        return view('job.job_applicant_info.index', compact('data','job_id'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {

            $job_id = $request->input('job_id');
            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table
            // echo 'working';
            // echo $sortColumnIndex;

            $sortDirection = $request->get('order')[0]['dir'];

            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','first_name','last_name','email','company_name','phone_no','schedule_date','schedule_time','schedule_duration','timezone','website_link','instagram_link','facebook_link','x_link','youtube_link','msg','created_at'
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // echo 'working';
            // echo $sortColumnIndex;
            // echo $sortColumn;
            // die;

            // Apply the sorting to the query
            $query = Job_applicants::select('*');

            if ($job_id) {
                $query = $query->where('job_id', $job_id); // Filter by job_id
            }

            $query = $query->orderBy($sortColumn, $sortDirection);

            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('banner_video_thumbnail', function ($row) {
                //     $imgUrl = $row->banner_video_thumbnail ?? '';
                //     return '<img src="' . $imgUrl . '" alt="Profile Image" width="70" height="70">';
                // })
                ->editColumn('portfolio_link', function ($row) {
                    return '<a href="'. $row->portfolio_link. '" target="_blank">Click</a>';
                })

                ->editColumn('upload_resume', function ($row) {
                    // Get the full URL of the resume stored in S3
                    // $resumeUrl = \Storage::disk('s3')->url($row->resume_path); // 'resume_path' is the column where the file path is stored
                    $uploadresumeUrl = $row->upload_resume; 
                    $baseUrl = env('AWS_URL'); 
                    $resumeUrl = $baseUrl . '/' . $uploadresumeUrl;
                    // Return the HTML for the download and preview links
                    return ' <a href="' . $resumeUrl . '" target="_blank">Preview</a>';
                })
                // ->editColumn('instagram_link', function ($row) {
                //     return '<a href="'. $row->instagram_link. '" target="_blank">Click</a>';
                // })
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
                })
                // ->editColumn('updated_at', function ($row) {
                //     return \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y');
                // })
                ->rawColumns(['portfolio_link','upload_resume'])
                ->make(true);
        }
    }
}
