<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Admin;
use App\Models\Company;
use App\Models\ContactNote;
use App\Models\Property;
use App\Models\MasterProject;
use App\Models\Community;
use App\Models\Contact;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactNoteController extends Controller
{
    public function Store(Request $request){
        $request->validate([
            'contact'=>'required',
            'note_subject'=>'required',
            // 'note'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $note_subject=request('note_subject');

        $property_id=null;
        if($note_subject==2)
            $property_id=request('property');

        $note_text=request('note');
        $data=ContactNote::create([
            'company_id'=>$adminAuth->company_id,
            'admin_id'=>$adminAuth->id,
            'note_subject'=>$note_subject,
            'property_id'=>$property_id,
            'date_at'=>request('date_at'),
            'time_at'=>request('time_at'),
            'contact_id'=>request('contact'),
            'note'=>$note_text
        ]);

        $Contact = Contact::find(request('contact'));
        $Contact->last_activity=$data->created_at;
        $Contact->save();

        $note=ContactNote::find($data->id);
        $admin=Admin::find($note->admin_id);
        $ActivityProperty=Property::where('id',$note->property_id)->first();
        if($ActivityProperty){
            $ActivityMasterProject=MasterProject::where('id',$ActivityProperty->master_project_id)->first();
            $ActivityCommunity=Community::find($ActivityProperty->community_id);
        }

        if($note_subject==2 || $note_subject==3 || $note_subject==5) {
            $Properties=[];
            if($note_subject==2) {
                $subject='Viewing';
                $Properties=[request('property')];
                $body = 'Dear ' . $Contact->firstname . '<br><br>Hope all well, your viewing has been set up on ' . str_replace('/','at',\Helper::changeDatetimeFormat($note->date_at . ' ' . $note->time_at)) . ' with ' . $admin->firstname . ' ' . $admin->lastname . ' ' . $admin->mobile_number;
            }
            if($note_subject==3) {
                $subject='Appointment';
                $body = 'Dear ' . $Contact->firstname . '<br><br>Hope all well, your appointment has been set up on ' . str_replace('/','at',\Helper::changeDatetimeFormat($note->date_at . ' ' . $note->time_at)) . ' with ' . $admin->firstname . ' ' . $admin->lastname . ' ' . $admin->mobile_number;
            }
            if($note_subject==5) {
                $subject=env('APP_NAME');
                $body = 'Dear ' . $Contact->firstname . '<br><br>'.nl2br($note_text);
            }

            $details = [
                'subject' => $subject,
                'body' => $body,
                'properties' => $Properties
            ];

            try {
                Mail::to($Contact->email)->send(new SendMail($details));
            }catch (\Exception $e){

            }
        }

        if($note_subject==2 || $note_subject==3) {
            Survey::create([
                'company_id' => $adminAuth->company_id,
                'admin_id' => $adminAuth->id,
                'model' => 'Contact_' . NoteSubject[$note_subject],
                'model_id' =>$note->id,
            ]);
        }
        $company=Company::find($note->company_id);
        return '<tr class="note-description" data-title="'.NoteSubject[$note->note_subject].'" data-desc="'.$note->note.'">
                    <td>
                    <div class="action" data-id="'.$note->id.'" data-model="'.route('contact.note.cancel').'">'
                    .( (($note->note_subject==2 || $note->note_subject==3) && $note->note=='' && $note->status==1 && date('Y-m-d H:i:s') > $note->date_at.' '.$note->time_at) ? '<a href="#ActivityFeedbackModal" class="feedback-register" data-toggle="modal" data-note="'.$note->note.'"><span class="btn btn-primary" style="min-width:100%">Feedback</span></a>' : '' ).
                    ((($note->note_subject==2 || $note->note_subject==3) && $note->status==1 && date('Y-m-d H:i:s') < $note->date_at.' '.$note->time_at) ? '<a href="javascript:void(0);" class="disabled"><span class="btn btn-danger" style="min-width:100%">Cancel</span></a>' : '').
                    '</div>
                    </td>
                    <td data-target="#ViewModal" data-toggle="modal">'.NoteSubject[$note->note_subject].'</td>
                    <td>'. (($ActivityProperty) ? '<a href="/admin/property/view/'.$ActivityProperty->id.'">'.$company->sample.'-'.( (($ActivityProperty->listing_type_id==1) ? 'S' : 'R').'-'.$ActivityProperty->ref_num.'<br>'.( ($ActivityMasterProject) ? $ActivityMasterProject->name : '' ).' | '.( ($ActivityCommunity) ? $ActivityCommunity->name : '' ) ).'</a>' : 'N/A' ).'</td>
                    <td data-target="#ViewModal" data-toggle="modal">'.
                    ( ($note->date_at) ? \Helper::changeDatetimeFormat($note->date_at.' '.$note->time_at).'<br>' : '' )
                    .'<span class="note'.$note->id.'">'. \Illuminate\Support\Str::limit(strip_tags($note->note),50)
                    .( ( $note->status==2) ? (($note->note) ? '<br>':'').'<span class="text-danger">'.NoteSubject[$note->note_subject].' Cancelld</span>' : '' )
                    .'</span>
                    </td>
                    <td>'.$note->admin->firstname.' '.$note->admin->lastname.'</td>
                    <td data-target="#ViewModal" data-toggle="modal">'.\Helper::changeDatetimeFormat( $note->created_at).'</td>
                </tr>';
    }

    public function Edit(Request $request){
        $request->validate([
            'note'=>'required',
        ]);
        $ContactNote = ContactNote::find(request('id'));
        $ContactNote->note = request('note');
        $ContactNote->save();
        return 'true';
    }

    public function cancel(Request $request){
        $request->validate([
            'disabled'=>'required',
        ]);

        $ContactNote = ContactNote::find(request('disabled'));
        $admin = Admin::find($ContactNote->admin_id);
        $Contact = Contact::find($ContactNote->contact_id);
        $ContactNote->status = 2;
        $ContactNote->save();

        Survey::where('model','Contact_'.NoteSubject[$ContactNote->note_subject])->where('model_id',$ContactNote->id)->delete();

        $Properties=[];
        if($ContactNote->note_subject==2) {
            $subject='Viewing Cancelled';
            $Properties=[$ContactNote->property_id];
            $body = 'Dear ' . $Contact->firstname . '<br><br>Hope all well, your viewing with ' . $admin->firstname . ' ' . $admin->lastname.' on '. str_replace('/','at',\Helper::changeDatetimeFormat($ContactNote->date_at . ' ' . $ContactNote->time_at)) . ' has been cancelled.' ;
        }

        if($ContactNote->note_subject==3) {
            $subject='Appointment Cancelled';
            $body = 'Dear ' . $Contact->firstname . '<br><br>Hope all well, your appointment with ' . $admin->firstname . ' ' . $admin->lastname.' on '. str_replace('/','at',\Helper::changeDatetimeFormat($ContactNote->date_at . ' ' . $ContactNote->time_at)) . ' has been cancelled.' ;
        }

        $details = [
            'subject' => $subject,
            'body' => $body,
            'properties' => $Properties
        ];

        try {
            Mail::to($Contact->email)->send(new SendMail($details));
        }catch (\Exception $e){

        }

        return redirect('/admin/contact/view/'.$ContactNote->contact_id);
    }
}
