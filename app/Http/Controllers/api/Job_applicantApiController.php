<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Job_applicants;
use Validator;

class Job_applicantApiController extends Controller
{
    
    public function store(Request $request)
    {
        // Validate incoming data
        $validator = Validator::make($request->all(), [
            // 'job_id'            => 'required',
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'phone_no'          => 'required|string|max:15',
            'email'             => 'required|email|max:255',
            'experience'        => 'nullable',
            'linkedin_profile'  => 'nullable',
            'annual_ctc'        => 'nullable',
            'job_prefrence'    => 'nullable',
            'notice_period_days' => 'nullable',
            'portfolio_link'    => 'nullable',
            'upload_resume'     => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $resumeUrl = upload_file_to_s3($request, 'upload_resume', 'job_applicants/resumes');

        // Create a new job applicant record in the database
        $jobApplicant = Job_applicants::create([
            'job_id'            => $request->job_id ,
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'phone_no'          => $request->phone_no,
            'email'             => $request->email,
            'experience'        => $request->experience ?? '',
            'linkedin_profile'  => $request->linkedin_profile ?? '',
            'annual_ctc'        => $request->annual_ctc ?? '',
            'job_prefrence'    => $request->job_prefrence ?? '',
            'notice_period_days' => $request->notice_period_days ?? '',
            'portfolio_link'    => $request->portfolio_link ?? '',
            'upload_resume'     => $resumeUrl ?? '',
        ]);

        return response()->json([
            'message' => 'Job applicant created successfully!',
            'data'    => $jobApplicant
        ], 201);
    }
}