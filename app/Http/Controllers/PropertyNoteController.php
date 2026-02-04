<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Admin;
use App\Models\PropertyNote;
use App\Models\Property;
use App\Models\Contact;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PropertyNoteController extends Controller
{
    public function Store(Request $request){
        $request->validate([
            'property'=>'required',
            'note_subject'=>'required',
            // 'note'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $note_subject=request('note_subject');
        $note_text=request('note');

        $contact_id=null;
        if($note_subject==2)
            $contact_id=request('contact');

        $data=PropertyNote::create([
            'company_id'=>$adminAuth->company_id,
            'admin_id'=>$adminAuth->id,
            'note_subject'=>$note_subject,
            'contact_id'=>$contact_id,
            'date_at'=>request('date_at'),
            'time_at'=>request('time_at'),
            'property_id'=>request('property'),
            'note'=>$note_text
        ]);

        $Property = Property::find(request('property'));
        $Property->last_activity=$data->created_at;
        $Property->save();

        $owner=Contact::find($Property->contact_id);
        $owner->last_activity=$data->created_at;
        $owner->save();

        $note=PropertyNote::find($data->id);
        $ActivityContact=Contact::where('id',$note->contact_id)->first();
        //$owner=Contact::where('id',$Property->contact_id)->first();
        if($ActivityContact){
            $Contact = Contact::find($note->contact_id);
            $Contact->last_activity=$data->created_at;
            $Contact->save();
        }
        $to='';
        if($note_subject==2 || $note_subject==3 || $note_subject==5) {

            $Properties=[];
            if($note_subject==2) {
                $subject='Viewing';
                $Properties=[request('property')];
                $body = 'Dear ' . $ActivityContact->firstname . '<br><br>Hope all well, your viewing has been set up on ' . str_replace('/','at',\Helper::changeDatetimeFormat($note->date_at . ' ' . $note->time_at)) . ' with ' . $note->admin->firstname . ' ' . $note->admin->lastname . ' ' . $note->admin->mobile_number;
                $to=$ActivityContact->email;
            }
            if($note_subject==3) {
                $subject='Appointment';
                $body = 'Dear ' . $owner->firstname . '<br><br>Hope all well, your appointment has been set up on ' . str_replace('/','at',\Helper::changeDatetimeFormat($note->date_at . ' ' . $note->time_at)) . ' with ' . $note->admin->firstname . ' ' . $note->admin->lastname . ' ' . $note->admin->mobile_number;
                $to=$owner->email;
            }
            if($note_subject==5) {
                $subject=env('APP_NAME');
                $body = 'Dear ' . $owner->firstname . '<br><br>'.nl2br($note_text);
                $to=$owner->email;
            }

            $details = [
                'subject' => $subject,
                'body' => $body,
                'properties' => $Properties
            ];

            try {
                Mail::to($to)->send(new SendMail($details));
            }catch (\Exception $e){

            }
        }

        if($note_subject==2 || $note_subject==3) {
            Survey::create([
                'company_id' => $adminAuth->company_id,
                'admin_id' => $adminAuth->id,
                'model' => 'Property_' . NoteSubject[$note_subject],
                'model_id' =>$note->id,
            ]);
        }

        return '<tr class="note-description" data-title="'.NoteSubject[$note->note_subject].'" data-desc="'.$note->note.'">
                    <td>
                     <div class="action" data-id="'.$note->id.'"  data-model="'.route('property.note.cancel').'">'
                    . ( (($note->note_subject==2 || $note->note_subject==3) && $note->note=='' && $note->status==1 && date('Y-m-d H:i:s') > $note->date_at.' '.$note->time_at ) ? '<a href="#ActivityFeedbackModal" class="feedback-register" data-toggle="modal" data-note="'.$note->note.'"><span class="btn btn-primary" style="min-width:100%">Feedback</span></a>' : '') .
                    ((($note->note_subject==2 || $note->note_subject==3) && $note->status==1 && date('Y-m-d H:i:s') < $note->date_at.' '.$note->time_at) ? '<a href="javascript:void(0);" class="disabled"><span class="btn btn-danger" style="min-width:100%">Cancel</span></a>' : '').
                    '</div>
                    </td>
                    <td data-target="#ViewModal" data-toggle="modal" >'.NoteSubject[$note->note_subject].'</td>
                    <td>'.( ($ActivityContact) ? '<a href="/admin/contact/view/'.$ActivityContact->id.'">'.$ActivityContact->firstname.' '.$ActivityContact->lastname.'</a>' : 'N/A' ).'</td>
                    <td data-target="#ViewModal" data-toggle="modal" >'
                        .( ($note->date_at) ? \Helper::changeDatetimeFormat($note->date_at.' '.$note->time_at).'<br>' : '' )
                        .'<span class="note'.$note->id.'">'.\Illuminate\Support\Str::limit(strip_tags($note->note),50)
                        .( ( $note->status==2) ? (($note->note) ? '<br>':'').'<span class="text-danger">'.NoteSubject[$note->note_subject].' Cancelld</span>' : '' )
                        .'</span>
                    </td>
                    <td data-target="#ViewModal" data-toggle="modal" >'.$note->admin->firstname.' '.$note->admin->lastname.'</td>
                    <td data-target="#ViewModal" data-toggle="modal" >'.\Helper::changeDatetimeFormat( $note->created_at).'</td>
                </tr>';
    }

    public function Edit(Request $request){
        $request->validate([
            'note'=>'required',
        ]);
        $PropertyNote = PropertyNote::find(request('id'));
        $PropertyNote->note = request('note');
        $PropertyNote->save();
        return 'true';
    }

    public function cancel(Request $request){
        $request->validate([
            'disabled'=>'required',
        ]);
        $PropertyNote = PropertyNote::find(request('disabled'));
        $admin = Admin::find($PropertyNote->admin_id);
        $Contact = Contact::find($PropertyNote->contact_id);

        $Property = Property::find($PropertyNote->property_id);
        $owner = Contact::find($Property->contact_id);

        $PropertyNote->status = 2;
        $PropertyNote->save();

        Survey::where('model','Property_'.NoteSubject[$PropertyNote->note_subject])->where('model_id',$PropertyNote->id)->delete();

        $Properties=[];
        $to='';
        if($PropertyNote->note_subject==2) {
            $subject='Viewing Cancelled';
            $Properties=[$PropertyNote->property_id];
            $body = 'Dear ' . $Contact->firstname . '<br><br>Hope all well, your viewing with ' . $admin->firstname . ' ' . $admin->lastname.' on '. str_replace('/','at',\Helper::changeDatetimeFormat($PropertyNote->date_at . ' ' . $PropertyNote->time_at)) . ' has been cancelled.' ;
            $to=$Contact->email;
        }

        if($PropertyNote->note_subject==3) {
            $subject='Appointment Cancelled';
            $body = 'Dear ' . $owner->firstname . '<br><br>Hope all well, your appointment with ' . $admin->firstname . ' ' . $admin->lastname.' on '. str_replace('/','at',\Helper::changeDatetimeFormat($PropertyNote->date_at . ' ' . $PropertyNote->time_at)) . ' has been cancelled.' ;
            $to=$owner->email;
        }

        $details = [
            'subject' => $subject,
            'body' => $body,
            'properties' => $Properties
        ];

        try {
            Mail::to($to)->send(new SendMail($details));
        }catch (\Exception $e){

        }

        return redirect('/admin/property/view/'.$PropertyNote->property_id);
    }
}
