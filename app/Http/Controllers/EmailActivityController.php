<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Mail\UserEmail;
use App\EmailActivity;

class EmailActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EmailActivity::all();
    }

    /**
     * Send email for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendEmail($id)
    {
        $email = EmailActivity::findOrFail($id);
        Mail::to($email->recipient)->queue(new UserEmail($email));
        return $email;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'sender' => 'required|email',
            'recipient' => 'required|email',
            'subject' => 'required',
            'status' => [
                'required',
                Rule::in(['posted', 'sent', 'failed']),
            ],
        ]);

        $data = $request->all();
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '__' . $file->getClientOriginalName();
                $file->storeAs('attachments', $fileName);
                array_push($attachments, [
                    'orig_name' => $file->getClientOriginalName(),
                    'saved_name' => $fileName,
                ]);
            }
            $data['attachments'] = json_encode($attachments);
        }

        return EmailActivity::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return EmailActivity::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => [
                Rule::in(['posted', 'sent', 'failed']),
            ],
        ]);

        $ea = EmailActivity::find($id);
        $ea->update($request->all());
        return $ea;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $email = EmailActivity::find($id);
        if ($email->attachments) {
            $attachments = json_decode($email->attachments, true);
            foreach ($attachments as $attachment) {
                $file = 'attachments/' . $attachment['saved_name'];
                if (Storage::exists($file)) {
                    Storage::delete($file);
                }
            }
        }
        return $email->destroy($id);
    }
}
