<?php

namespace App\Http\Controllers\Vendor;

use App\Classes\GeniusMailer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AdminUserConversation;
use App\Models\AdminUserMessage;
use App\Models\Generalsetting;
use App\Models\Notification;
use App\Models\Pagesetting;
use App\Models\User;


class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function messages()
    {
        $user = Auth::guard('web')->user();
        $convs = Conversation::withCount(['messages'=> function ($query) {
                     $query->where('seen',0);
                }])->where('sent_user','=',$user->id)->orWhere('recieved_user','=',$user->id)->orderBy('id','desc')->get();
     
       
        return view('vendor.message.index',compact('user','convs'));            
    }

    public function message($id)
    {
            $user = Auth::guard('web')->user();
            $conv = Conversation::findOrfail($id);
            $up=Message::where('conversation_id',$id)->update(['seen'=>1]);
            return view('vendor.message.create',compact('user','conv'));                 
    }
    
     public function adminmessages()
    {
            $user = Auth::guard('web')->user();
            $convs = AdminUserConversation::where('type','=','Ticket')->where('user_id','=',$user->id)->get();
            return view('vendor.ticket.index',compact('convs'));            
    }

    public function adminDiscordmessages()
    {
            $user = Auth::guard('web')->user();
            $convs = AdminUserConversation::where('type','=','Dispute')->where('user_id','=',$user->id)->get();
            return view('vendor.dispute.index',compact('convs'));            
    }
    
     public function adminmessage($id)
    {
            $conv = AdminUserConversation::findOrfail($id);
            return view('vendor.ticket.create',compact('conv'));                 
    }   
    
}