<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendmail(Request $req)
    {
        $this->check_account($req);
        
        $param_insert = array(
            'name' => 'mail_sendmail',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        /* Validation */
        $validator = Validator::make($req->all(), [
          'to' => 'required|max:255',
          'alias' => 'required|max:255',
          'subject' => 'required|max:255',
          'content' => 'required|max:255',
          'name' => 'required|max:255',
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($mails,400,'Bad Request.',true);
            
            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 400);
        }else{
            $data['to']         = $req->to;
            $data['alias']      = $req->alias;
            $data['subject']    = $req->subject;
            $data['content']    = $req->content;
            $data['name']       = $req->name;

            $email              = $data;
            Mail::send('emails.template', ['params'=>$data], function($send) use ($email){
                $send->to($email['to'])->subject($email['subject']);
                $send->from('admin@pandu.com', $email['alias']);
            });

            if(Mail::failures()) {
                $result = $this->generate_response($mails,409,'Something Wrong, email Can not sent.',true);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }else{
                $result = $this->generate_response($email, 200, 'All Data.', false);

                $this->update_access_log($access_log_id, $result);
                
                return response()->json($result, 200);
            }
        }
    }
}
