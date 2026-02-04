<?php

namespace App\Http\Controllers;

// use App\Models\Calendar;
use App\Models\Admin;
use App\Models\Company;
use App\Models\DataCenter;
use App\Models\DataCenterNote;
use App\Models\PropertyNote;
use App\Models\ContactNote;
use App\Models\LeadNote;
use App\Models\Property;
use App\Models\Lead;
use App\Models\MasterProject;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Task;
use App\Models\TaskTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function Calendar(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/calendar', [
            'pageConfigs' => $pageConfigs,
            'Calendars'=>''
        ]);
    }

    public function getJson(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $data=[];
        $ojb=[];

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $calendar_viewing_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_viewing')->first();
        $calendar_appointment_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_appointment')->first();
        $calendar_reminder_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_reminder')->first();
        $calendar_cancelled_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_cancelled')->first();

        $subject_color=[
            '2'=> $calendar_viewing_setting->value,
            '3'=> $calendar_appointment_setting->value,
            '6'=> $calendar_reminder_setting->value,
        ];

        if($adminAuth->type>2 && $adminAuth->type != 5 && $adminAuth->type != 6) {
            $PropertyNote = PropertyNote::whereIn('note_subject', array(2, 3, 6))->where('company_id', $adminAuth->company_id)->where('admin_id', $adminAuth->id)->orderBy('id', 'desc')->get();
            $ContactNote=ContactNote::whereIn('note_subject',array(2,3, 6))->where('company_id', $adminAuth->company_id)->where('admin_id', $adminAuth->id)->orderBy('id', 'desc')->get();
            $LeadNote=LeadNote::whereIn('note_subject',array(2,3, 6))->where('company_id', $adminAuth->company_id)->where('admin_id', $adminAuth->id)->orderBy('id', 'desc')->get();
        }else {
            $PropertyNote = PropertyNote::whereIn('note_subject', array(2, 3))->where('company_id', $adminAuth->company_id)->orderBy('id', 'desc')->get();
            $PropertyNoteRemainder = PropertyNote::where('note_subject', 6)->where('company_id', $adminAuth->company_id)->where('admin_id', $adminAuth->id)->get();
            $PropertyNote=$PropertyNote->merge($PropertyNoteRemainder);

            $ContactNote=ContactNote::whereIn('note_subject',array(2,3))->where('company_id', $adminAuth->company_id)->orderBy('id', 'desc')->get();
            $ContactNoteRemainder=ContactNote::where('note_subject',6)->where('company_id', $adminAuth->company_id)->where('admin_id', $adminAuth->id)->orderBy('id', 'desc')->get();
            $ContactNote=$ContactNote->merge($ContactNoteRemainder);

            $LeadNote=LeadNote::whereIn('note_subject',array(2,3))->where('company_id', $adminAuth->company_id)->orderBy('id', 'desc')->get();
            $LeadNoteRemainder=LeadNote::where('note_subject',6)->where('company_id', $adminAuth->company_id)->where('admin_id', $adminAuth->id)->orderBy('id', 'desc')->get();
            $LeadNote=$LeadNote->merge($LeadNoteRemainder);
        }
        $DataCenterNote=DataCenterNote::where('note_subject', 6)->where('company_id', $adminAuth->company_id)->where('admin_id', $adminAuth->id)->orderBy('id', 'desc')->get();

        $Tasks=DB::select('SELECT * FROM `tasks` WHERE company_id='.$adminAuth->company_id.' AND `assign_to`='.$adminAuth->id);

        $userBirth=DB::select('SELECT * FROM `admins` WHERE company_id='.$adminAuth->company_id.' AND date_birth IS NOT NULL');

        $contactBirth=DB::select('SELECT * FROM `contacts` WHERE company_id='.$adminAuth->company_id.' AND date_birth IS NOT NULL AND (client_manager='.$adminAuth->id.' OR client_manager_tow='.$adminAuth->id.')');


        $now=date('Y-m-d H:i:s');
        foreach($PropertyNote as $note){
            $admin=Admin::find($note->admin_id);

            $property=Property::where('id',$note->property_id)->first();
            $contact=Contact::where('id',$note->contact_id)->first();

            $ojb["type"]='property';
            $ojb["title"]=NoteSubject[$note->note_subject];//.( ($note->note_subject==2) ? ' ('.$admin->firstname.' '.$admin->lastname.')' : '' );
            $ojb["user"]=$admin->firstname.' '.$admin->lastname;
            $ojb["description"]=$note->note;
            $ojb["start"]=$note->date_at;
            $ojb["end"]=$note->date_at;
            $ojb["time"]=$note->time_at;
            $ojb["date_show"]=($note->date_at)? date('d-m-Y',strtotime($note->date_at)) : 'N/A';
            $ojb["property"]=($property) ? $company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num : 'N/A';
            $ojb["contact"]=($contact) ? $contact->firstname.' '.$contact->lastname : 'N/A';
            $ojb["color"]=($note->status==1) ? $subject_color[$note->note_subject] : $calendar_cancelled_setting->value;

            $status='';

            if($note->status==2){
                $status='<span class="badge badge-pill badge-light-danger">Cancelled</span>';
            }else{
                if($note->note_subject==2 || $note->note_subject==3) {
                    $datetime = $note->date_at . ' ' . $note->time_at;
                    if (strtotime($datetime) < strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-primary">Done</span>';
                    }
                    if (strtotime($datetime) > strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-success">Upcoming</span>';
                    }
                }
            }
            $ojb['status']=$status;
            $data[]=$ojb;
        }

        foreach($ContactNote as $note){

            $admin=Admin::find($note->admin_id);

            $property=Property::where('id',$note->property_id)->first();
            $contact=Contact::where('id',$note->contact_id)->first();

            $ojb["type"]='contact';
            $ojb["title"]=NoteSubject[$note->note_subject];//.( ($note->note_subject==2) ? ' ('.$admin->firstname.' '.$admin->lastname.')' : '' );
            $ojb["user"]=$admin->firstname.' '.$admin->lastname;
            $ojb["description"]=$note->note;
            $ojb["start"]=$note->date_at;
            $ojb["end"]=$note->date_at;
            $ojb["time"]=$note->time_at;
            $ojb["date_show"]=($note->date_at)? date('d-m-Y',strtotime($note->date_at)) : 'N/A';
            $ojb["property"]=($property) ? $company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num : 'N/A';
            $ojb["contact"]=($contact) ? $contact->firstname.' '.$contact->lastname : 'N/A';
            $ojb["color"]=($note->status==1) ? $subject_color[$note->note_subject] : $calendar_cancelled_setting->value;
            $status='';
//            if($note->status==2){
//                $status='<span class="badge badge-pill badge-light-danger">Cancelled</span>';
//            }

            if($note->status==2){
                $status='<span class="badge badge-pill badge-light-danger">Cancelled</span>';
            }else{
                if($note->note_subject==2 || $note->note_subject==3) {
                    $datetime = $note->date_at . ' ' . $note->time_at;
                    if (strtotime($datetime) < strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-primary">Done</span>';
                    }
                    if (strtotime($datetime) > strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-success">Upcoming</span>';
                    }
                }
            }
            $ojb['status']=$status;
            $data[]=$ojb;
        }

        foreach($LeadNote as $note){

            $admin=Admin::find($note->admin_id);

            $property=Property::where('id',$note->property_id)->first();
            $lead=Lead::where('id',$note->lead_id)->first();

            $ojb["type"]='lead';
            $ojb["title"]=NoteSubject[$note->note_subject];//.( ($note->note_subject==2) ? ' ('.$admin->firstname.' '.$admin->lastname.')' : '' );
            $ojb["user"]=$admin->firstname.' '.$admin->lastname;
            $ojb["description"]=$note->note;
            $ojb["start"]=$note->date_at;
            $ojb["end"]=$note->date_at;
            $ojb["time"]=$note->time_at;
            $ojb["date_show"]=($note->date_at)? date('d-m-Y',strtotime($note->date_at)) : 'N/A';
            $ojb["property"]=($property) ? $company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num : 'N/A';
            $ojb["contact"]=($lead) ? $lead->name : 'N/A';
            $ojb["color"]=($note->status==1) ? $subject_color[$note->note_subject] : $calendar_cancelled_setting->value;
            $status='';
//            if($note->status==2){
//                $status='<span class="badge badge-pill badge-light-danger">Cancelled</span>';
//            }

            if($note->status==2){
                $status='<span class="badge badge-pill badge-light-danger">Cancelled</span>';
            }else{
                if($note->note_subject==2 || $note->note_subject==3) {
                    $datetime = $note->date_at . ' ' . $note->time_at;
                    if (strtotime($datetime) < strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-primary">Done</span>';
                    }
                    if (strtotime($datetime) > strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-success">Upcoming</span>';
                    }
                }
            }
            $ojb['status']=$status;
            $data[]=$ojb;
        }

        foreach($DataCenterNote as $note){

            $admin=Admin::find($note->admin_id);

            $DataCenter=DataCenter::where('id',$note->data_center_id)->first();

            $ojb["type"]='data-center';
            $ojb["title"]=NoteSubject[$note->note_subject];//.( ($note->note_subject==2) ? ' ('.$admin->firstname.' '.$admin->lastname.')' : '' );
            $ojb["user"]=$admin->firstname.' '.$admin->lastname;
            $ojb["description"]=$note->note;
            $ojb["start"]=$note->date_at;
            $ojb["end"]=$note->date_at;
            $ojb["time"]=$note->time_at;
            $ojb["date_show"]=($note->date_at)? date('d-m-Y',strtotime($note->date_at)) : 'N/A';
            $ojb["property"]=($DataCenter) ? 'DC-'.$DataCenter->id : 'N/A';
            $ojb["contact"]='N/A';
            $ojb["color"]=($note->status==1) ? $subject_color[$note->note_subject] : $calendar_cancelled_setting->value;
            $status='';

            $ojb['status']=$status;
            $data[]=$ojb;
        }

        foreach($Tasks as $task){

            $admin=Admin::find($task->admin_id);
            $assign_to=Admin::find($task->assign_to);

            $title=\Illuminate\Support\Str::limit(strip_tags($task->description),50);
            $typeColor='#ffdbba';
            if($task->task_title_id){
                $TaskTitle=TaskTitle::where('id',$task->task_title_id)->first();
                $title=$TaskTitle->title;
                $typeTask=Task_Type[$TaskTitle->type];
                $typeColor=$typeTask[1];
            }

            $ojb["type"]='task';
            $ojb["title"]=$title;
            $ojb["user"]=$admin->firstname.' '.$admin->lastname;
            $ojb["assign_to"]=$assign_to->firstname.' '.$assign_to->lastname;
            $ojb["description"]=$task->description;
            $ojb["start"]=$task->date_at;
            $ojb["end"]=$task->date_at;
            $ojb["time"]=$task->time_at;
            $ojb["date_show"]=($task->date_at)? date('d-m-Y',strtotime($task->date_at)) : 'N/A';
            $ojb["color"]=$typeColor;
            $status='';
            if($task->status==2){
                $status='<span class="badge badge-pill badge-light-danger">Cancelled</span>';
            }else{
                $datetime = $task->date_at . ' ' . $task->time_at;
                if (strtotime($datetime) > strtotime($now)) {
                    $status = '<span class="badge badge-pill badge-light-success">Upcoming</span>';
                }
                if ($task->status==1) {
                    $status = '<span class="badge badge-pill badge-light-primary">Done</span>';
                }
            }
            $ojb['status']=$status;
            $data[]=$ojb;
        }

        foreach($userBirth as $uBirth){

            $typeColor='#ffdbba';

            $date_birth=explode('-',$uBirth->date_birth);
            $date_birth[0]=date('Y');
            $date_birth=join('-',$date_birth);
            $ojb["type"]='user_birth';
            $ojb["title"]='(BD) '. $uBirth->firstname.' '.$uBirth->lastname;
            $ojb["user"]='Today is the birthday of your colleague <b>'.$uBirth->firstname.' '.$uBirth->lastname.'<br>';
            $ojb["description"]='';
            $ojb["start"]=$date_birth;
            $ojb["end"]=$date_birth;
            $ojb["time"]='';
            $ojb["color"]=$typeColor;
            $data[]=$ojb;
        }

        foreach($contactBirth as $cBirth){

            $typeColor='#c7c2f2';

            $date_birth=explode('-',$cBirth->date_birth);
            $date_birth[0]=date('Y');
            $date_birth=join('-',$date_birth);
            $ojb["type"]='user_birth';
            $ojb["title"]='(BD) '. $cBirth->firstname.' '.$cBirth->lastname;
            $ojb["user"]='Today is the birthday of your client <b>'.$cBirth->firstname.' '.$cBirth->lastname.'<br>';
            $ojb["description"]='';
            $ojb["start"]=$date_birth;
            $ojb["end"]=$date_birth;
            $ojb["time"]='';
            $ojb["color"]=$typeColor;
            $data[]=$ojb;
        }

        return json_encode($data);
    }

    public function calendarActivity(){
        $date=request('date');

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        if($adminAuth->type<3 || $adminAuth->type == 5 || $adminAuth->type == 6) {
            $ActivityNote = DB::select("SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, contact_note.status, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND contact_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND note_subject IN (2,3)
                    UNION
                    SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, property_note.status, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND property_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND note_subject IN (2,3)
                    UNION
                    SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, contact_note.status, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND contact_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND admin_id=".$adminAuth->id." AND note_subject=6
                    UNION
                    SELECT 'DataCenter' as type,data_center_note.id, admin_id, data_center_id as 'property_id', note_subject, null as contact_id, note, date_at, time_at, data_center_note.status, data_center_note.created_at,firstname,lastname FROM data_center_note,admins WHERE data_center_note.admin_id=admins.id AND data_center_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND admin_id=".$adminAuth->id." AND note_subject=6
                    UNION
                    SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, property_note.status, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND property_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND admin_id=".$adminAuth->id." AND note_subject=6 ORDER BY CONCAT(date_at,' ',time_at) desc");//created_at
        }else{
            $ActivityNote = DB::select("SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, contact_note.status, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND contact_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND admin_id=".$adminAuth->id." AND note_subject IN ("."2,3,6".")
                    UNION
                    SELECT 'DataCenter' as type,data_center_note.id, admin_id, data_center_id as 'property_id', note_subject, null as contact_id, note, date_at, time_at, data_center_note.status, data_center_note.created_at,firstname,lastname FROM data_center_note,admins WHERE data_center_note.admin_id=admins.id AND data_center_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND admin_id=".$adminAuth->id." AND note_subject=6
                    UNION
                    SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, property_note.status, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND property_note.company_id=".$adminAuth->company_id." AND date_at='" . $date . "' AND admin_id=".$adminAuth->id." AND note_subject IN ("."2,3,6".") ORDER BY CONCAT(date_at,' ',time_at) desc");
        }
        $output='';
        foreach($ActivityNote as $note){

            // $admin=Admin::find($note->admin_id);

            $contact_or_property='';
            if($note->type=='contact'){
                $ActivityProperty=Property::where('id',$note->property_id)->first();
                if($ActivityProperty){
                    $ActivityMasterProject=MasterProject::where('id',$ActivityProperty->master_project_id)->first();
                    $ActivityCommunity=Community::find($ActivityProperty->community_id);
                    if($adminAuth->type==6) {
                        $contact_or_property=$company->sample.'-'.( (($ActivityProperty->listing_type_id==1) ? 'S' : 'R').'-'.$ActivityProperty->ref_num.'<br>'.( ($ActivityMasterProject) ? $ActivityMasterProject->name : '' ).' | '.( ($ActivityCommunity) ? $ActivityCommunity->name : '' ) );
                    }else {
                        $contact_or_property = '<a href="/admin/property/view/' . $ActivityProperty->id . '">' . $company->sample . '-' . ((($ActivityProperty->listing_type_id == 1) ? 'S' : 'R') . '-' . $ActivityProperty->ref_num . '<br>' . (($ActivityMasterProject) ? $ActivityMasterProject->name : '') . ' | ' . (($ActivityCommunity) ? $ActivityCommunity->name : '')) . '</a>';
                    }
                }

                if($note->note_subject==3){
                    $ActivityContact=Contact::where('id',$note->contact_id)->first();
                    if($ActivityContact){
                        if($adminAuth->type==6) {
                            $contact_or_property =$ActivityContact->firstname . ' ' . $ActivityContact->lastname;
                        }else{
                            $contact_or_property='<a href="/admin/contact/view/'.$ActivityContact->id.'">'.$ActivityContact->firstname.' '.$ActivityContact->lastname.'</a>';
                        }
                    }
                }

                if($note->note_subject==6){
                    $ActivityContact=Contact::where('id',$note->contact_id)->first();
                    if($ActivityContact) {
                        if($adminAuth->type==6) {
                            $contact_or_property =$ActivityContact->firstname . ' ' . $ActivityContact->lastname;
                        }else{
                            $contact_or_property = '<a href="/admin/contact/view/' . $ActivityContact->id . '">' . $ActivityContact->firstname . ' ' . $ActivityContact->lastname . '</a>';
                        }
                    }
                }
            }

            if($note->type=='property'){
                $ActivityContact=Contact::where('id',$note->contact_id)->first();
                if($ActivityContact){
                    if($adminAuth->type==6) {
                        $contact_or_property = $ActivityContact->firstname . ' ' . $ActivityContact->lastname;
                    }else{
                        $contact_or_property = '<a href="/admin/contact/view/' . $ActivityContact->id . '">' . $ActivityContact->firstname . ' ' . $ActivityContact->lastname . '</a>';
                    }
                }

                if($note->note_subject==3){
                    $ActivityProperty=Property::where('id',$note->property_id)->first();
                    if($ActivityProperty){
                        $ActivityMasterProject=MasterProject::where('id',$ActivityProperty->master_project_id)->first();
                        $ActivityCommunity=Community::find($ActivityProperty->community_id);
                        if($adminAuth->type==6) {
                            $contact_or_property = $company->sample . '-' . ((($ActivityProperty->listing_type_id == 1) ? 'S' : 'R') . '-' . $ActivityProperty->ref_num . '<br>' . (($ActivityMasterProject) ? $ActivityMasterProject->name : '') . ' | ' . (($ActivityCommunity) ? $ActivityCommunity->name : ''));
                        }else {
                            $contact_or_property = '<a href="/admin/property/view/' . $ActivityProperty->id . '">' . $company->sample . '-' . ((($ActivityProperty->listing_type_id == 1) ? 'S' : 'R') . '-' . $ActivityProperty->ref_num . '<br>' . (($ActivityMasterProject) ? $ActivityMasterProject->name : '') . ' | ' . (($ActivityCommunity) ? $ActivityCommunity->name : '')) . '</a>';
                        }
                    }
                }
                if($note->note_subject==6){
                    $ActivityProperty=Property::where('id',$note->property_id)->first();
                    if($ActivityProperty){
                        $ActivityMasterProject=MasterProject::where('id',$ActivityProperty->master_project_id)->first();
                        $ActivityCommunity=Community::find($ActivityProperty->community_id);
                        if($adminAuth->type==6) {
                            $contact_or_property =  $company->sample . '-' . ((($ActivityProperty->listing_type_id == 1) ? 'S' : 'R') . '-' . $ActivityProperty->ref_num . '<br>' . (($ActivityMasterProject) ? $ActivityMasterProject->name : '') . ' | ' . (($ActivityCommunity) ? $ActivityCommunity->name : ''));
                        }else{
                            $contact_or_property = '<a href="/admin/property/view/' . $ActivityProperty->id . '">' . $company->sample . '-' . ((($ActivityProperty->listing_type_id == 1) ? 'S' : 'R') . '-' . $ActivityProperty->ref_num . '<br>' . (($ActivityMasterProject) ? $ActivityMasterProject->name : '') . ' | ' . (($ActivityCommunity) ? $ActivityCommunity->name : '')) . '</a>';
                        }
                    }
                }
            }

            if($note->type=='DataCenter'){
                $DataCenter=DataCenter::where('id',$note->property_id )->first();
                if($DataCenter)
                    $contact_or_property='<a href="/admin/data-center-view/'.$DataCenter->id.'">DC-'.$DataCenter->id.'</a>';
            }

            $now=date('Y-m-d H:i:s');
            $status='';
            if($note->status==2){
                $status='<span class="badge badge-pill badge-light-danger" style="min-width: 100%">Cancelled</span>';
            }else{
                if($note->note_subject==2 || $note->note_subject==3) {
                    $datetime = $note->date_at . ' ' . $note->time_at;
                    if (strtotime($datetime) < strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-primary" style="min-width: 100%">Done</span>';
                    }
                    if (strtotime($datetime) > strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-success" style="min-width: 100%">Upcoming</span>';
                    }
                }
            }

            $output.= '<tr class="note-description" data-title="'.NoteSubject[$note->note_subject].'" data-desc="'.$note->note.'">
                    <td data-target="#ViewModal" data-toggle="modal">'.NoteSubject[$note->note_subject].'</td>
                    <td>'. (($contact_or_property) ? $contact_or_property : '' ).'</td>
                    <td data-target="#ViewModal" data-toggle="modal">'.\Helper::changeDatetimeFormat($note->date_at.' '.$note->time_at) .'<br><span class="note'.$note->id.'">'. \Illuminate\Support\Str::limit(strip_tags($note->note),50) .'</span></td>
                    <td>'.$note->firstname.' '.$note->lastname.'</td>
                    <td data-target="#ViewModal" data-toggle="modal">'.str_replace('/','<br>',\Helper::changeDatetimeFormat( $note->created_at)).'</td>
                    <td>'.$status.'</td>
                </tr>';
        }

        return $output;
    }

}
