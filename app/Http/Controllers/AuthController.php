<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function sendEmail(Request $request)
    {
        $user = User::where('email', $request->all()['email'])->first();
        if (!$user) {
            return response([
                'status' => 'failed',
                'result' => 'Email does not exsits'
            ]);
        }

        // $key = Str::random(16);
        $key = rand(1000, 9999);
        $user->remember_token = $key;

        $data = [
            'name' => $user->name,
            'message' => 'Verification Code: ' . $key,
        ];

        $email = $user->email;

        $message = "Hello {$data['name']},\n\n{$data['message']}\n\nBest regards";
        Mail::raw($message, function ($m) use ($email) {
            $m->to($email)->subject('Recover Password');
        });

        $user->save();
        return response([
            'status' => 'success',
            'result' => 'Email was sent successfully',
            'key' => $key,
        ]);
    }


    public function resetPassowrd(Request $request)
    {
        $user = User::where('email', $request->all()['email'])->first();
        if (!$user) {
            return response([
                'status' => 'failed',
                'result' => 'Email does not exsits'
            ], 400);
        }

        if ($user->remember_token !== $request->all()['key']) {
            return response(
                [
                    'status' => 'failed',
                    'result' => 'Wrong Key'
                ],
                400
            );
        }

        $user->remember_token = '';
        $user->save();

        $user->update([
            'password' => Hash::make($request->all()['password'])
        ]);

        return  response(
            [
                'status' => 'success',
                'result' => 'Success'
            ],
            200
        );
    }
}
