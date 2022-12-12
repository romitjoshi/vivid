<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\{User};
use DB, Helper;
class AuthController extends Controller
{
    public function login()
    {
        $breadcrumbs = [
            ['link' => "home", 'name' => "Home"], ['name' => "Index"]
        ];
        return view('/content/Admin/login', ['breadcrumbs' => $breadcrumbs]);
    }

    public function loginAdmin(Request $request)
    {
        $rules = array(
            'email' => 'required|email',
            'password' => 'required',
        );

        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'email' => 'Email',
            'password' => 'Password',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->with('errors', $validator->errors())->withInput();
        }
        else
        {
            $getDetails = User::select('status')->where('email', $request->email)->where('role', 1)->first();
            if(!empty($getDetails['status']))
            {
                if($getDetails['status'] == 1)
                {
                    if (auth('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 1, 'status' => 1, 'login_type'=>1])) {
                        //$user = auth('admin')->user();
                        return redirect('/admin/home');
                    } else {
                        Session::flash('error', 'Invalid email or password.');
                        return redirect()->back();
                    }
                }
                else
                {
                    Session::flash('error', 'Inactice Account');
                    return redirect()->back();
                }
            }
            else
            {
                Session::flash('error', 'Invalid email or password.');
                return redirect()->back();
            }
        }
    }

    public function logout(Request $request)
    {
        auth('admin')->logout();
        return redirect('/admin/login');
    }
    public function profile()
    {
        $id=auth('admin')->User()->id;

        $profile = User::select('*')->where('id',$id)->first();
        $breadcrumbs = [

        ];
        return view('/content/Admin/profile', ['breadcrumbs' => $breadcrumbs,'profile'=>$profile]);

    }

    public function update(Request $request)
    {
        // print_r($request->all());
        // die('a');
        $rules = array(
            'name' => 'required|min:2',
            'email'=> 'required'
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'name' => 'Category Name',
            'email'=> 'Email',
            'image' => 'Image',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            $response['status'] = false;
            $response['message'] = ERROR;
        }
        else
        {
           $ids=auth('admin')->user()->id;
// print_r($ids);
// die('a');

           $file="";

             $profile=User::find($ids);
            //$profile = new User;
            $profile->name = $request->name;

            $profile->email = $request->email;
            if($request->hasFile('image')){
                $image=$request->file('image');
                $file=Helper::imageupload($image);
                $profile->image = $file;
            }

            $profile->update();

            $response['status'] = true;
            $response['message'] = PROFILEUPDATE;
        }
        echo json_encode($response);
    }

    public function updatepass(Request $request)
    {
        $rules = array(
            'oldpassword' => 'required|min:2',
            'password'=> 'required',
            'cpassword' =>'required|same:password',
        );
        $messages = array(
            'required' => ':attribute is required.'
        );
        $fieldNames = array(
            'oldpassword' => 'Old Password',
            'password'=> 'New Password',
            'cpassword' =>'Confirm Password',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails())
        {
            $response['status'] = false;
            $response['message'] = $validator->messages()->first();
        }
        else
        {
           $user=auth('admin')->user();
           if(Hash::check($request->oldpassword, $user->password)){
                User::find(auth()->user()->id)->update(['password'=> Hash::make($request->password)]);

                $response['status'] = true;
                $response['message'] = PROFILESUCSESS;
           }
           else{
            $response['status'] = false;
            $response['message'] = 'Old Password Not Match Please Try Again';
           }

        }
        echo json_encode($response);
    }
}
