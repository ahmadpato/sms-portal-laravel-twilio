<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index(){
        $users = Contact::all();
        return view('welcome',['users' => $users]);
    }

    public function store(Request $request){

        $request->validate([
            'phone' => 'required|unique:contacts|numeric'
        ]);

        $contact = new Contact;
        $contact->phone = $request->phone;
        $contact->save();

        $this->sendMessage('Contact registered successfully!!', $request->phone);
        return back()->with(['success' =>"{$request->phone} registered"]);
    }

    public function sendCustomMessage(Request $request){

        $request->validate([
            'contact' => 'required|array',
            'body'  => 'required',
        ]);

        $recepients = $request->contact;

        foreach($recepients as $recepient){
            $this->sendMessage($request->body, $recepient);
        }

        return back()->with(['success' => "Message on its way to recepients"]);
    }

    private function sendMessage($message, $recepients){

        $account_sid    = 'AC7e953f12b3db006f2ab2d551bc0f4d86';
        $auth_token     = 'e3be05e792208b849b721cd6931c79eb';
        $twilio_number  = '+19037763029';
        $client         = new Client($account_sid, $auth_token);
        $client->messages->create($recepients,['from' => $twilio_number, 'body' => $message]);

    }
}
