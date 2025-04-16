<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function index()
    {
        $data=User::all();
        // print_r($data->toArray());
        // die;
        return view('auth.login');
    }

    public function auth(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();

            $notification = [
                'status' => 'toast_success',
                'title' => 'Login Successful',
                'message' => 'You have logged in successfully',
            ];

            // Define the user
            $user = Auth::user();

            // Update last login timestamp
            $user->updated_at = now();
            $user->save();

            // Now safely use $user
            if ($user->user_role === 'employee') {
                return redirect()->route('employee.tasks.index')->with($notification);
            }

            if ($user->user_role === 'admin') {
                return redirect()->route('admin.reports.index')->with($notification);
            }

            return redirect()->intended('/dashboard')->with($notification);
        }

        $notification = [
            'status' => 'error',
            'title' => 'Login Failed',
            'message' => 'Incorrect username or password',
        ];
        return back()->with($notification);
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $notification = array(
            'status' => 'toast_success',
            'title' => 'Logout Successful',
            'message' => 'Thank you for using our application',
        );

        return redirect('/login')->with($notification);
    }

    public function register()
    {
        echo "<h1>Registration is not available</h1>";
    }
}
