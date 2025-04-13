<?php

if (!function_exists('upload_file_to_s3')) {
    /**
     * Handle the uploading of a file to S3 with dynamic field and folder names.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $fieldName  The name of the file input field.
     * @param string $folderName The folder name where the file should be stored.
     * @return string|null
     */
    function upload_file_to_s3($request, $fieldName = 'file', $folderName = 'default')
    {
        if ($request->hasFile($fieldName)) {
            // Retrieve the uploaded file
            $file = $request->file($fieldName);
            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            // Upload the file to the specified folder in the S3 bucket
            $path = $file->storeAs($folderName, $filename, 's3');
            
            // Optionally, you can get the full URL to the file
            // $url = Storage::disk('s3')->url($path);

            return $path;  // Returning the URL to the uploaded file
        }

        return null;  // Return null if no file was uploaded
    }
}
