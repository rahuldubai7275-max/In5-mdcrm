<?php

namespace App\Http\Controllers;
use App\Mail\SendMail;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\OffPlanProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendEmail(Request $request){

        $contacts=explode(',',request('submit'));
        $Subject=request('Subject');
        $Properties=request('Properties');
        $DeveloperProject=request('DeveloperProject');
        $Message=request('Message');
        $attach=request('attach');

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $propertyies_ref=[];
        if($Properties) {
            foreach ($Properties as $id) {
                $property = \App\Models\Property::find($id);
                $propertyies_ref[] = $company->sample.'-' . ((($property->listing_type_id == 1) ? 'S' : 'R') . '-' . $property->ref_num);
            }
        }

        foreach ($contacts as $id) {
            $contact=Contact::find($id);
            if($contact->email) {
                $body =  'Dear ' . $contact->firstname . '<br><br>' .nl2br($Message);
                $offPlanProject=null;
                $OffPlanProjectStore=null;
                if($DeveloperProject) {
                    $url = env('MD_URL') . '/api/off-plan-project/detail';
                    $token = $company->md_token;

                    $data = ['id' => $DeveloperProject];

                    $response = Http::withBody(json_encode($data), 'application/json')->withToken($token)->post($url);

                    $offPlanProject = json_decode($response);

                    $offPlanProjectCheck = OffPlanProject::where('company_id', $adminAuth->company_id)->where('md_crm_id', $offPlanProject->id)->first();
                    $OffPlanProjectStore = $offPlanProjectCheck;
                    if (!$offPlanProjectCheck) {
                        $OffPlanProjectStore = OffPlanProject::create(['md_crm_id' => $offPlanProject->id,
                            'company_id' => $adminAuth->company_id,
                            'property_type_id' => $offPlanProject->property_type_id,
                            'master_project_name' => $offPlanProject->master_project_name,
                            'project_name' => $offPlanProject->project_name]);
                    }
                }

                $details = [
                    'subject' => $Subject,
                    'body' => $body,
                    'properties' => $Properties,
                    'DeveloperProject' => $offPlanProject,
                    'attach_file' => $attach
                ];

                $data = ContactNote::create([
                    'company_id' => $adminAuth->company_id,
                    'admin_id' => $adminAuth->id,
                    'off_plan_project_id' => ($OffPlanProjectStore) ? $OffPlanProjectStore->id : null,
                    'note_subject' => '5',
                    'property_id' => null,
                    'contact_id' => $contact->id,
                    'note' => $body . '<br>' . join(', ', $propertyies_ref)
                ]);

                $Contact = Contact::find($contact->id);
                $Contact->last_activity = $data->created_at;
                $Contact->save();

                //try {
                    Mail::to($contact->email)->send(new SendMail($details));
                /*} catch (\Exception $e) {

                }*/
            }
        }
        return redirect('/admin/contacts');
    }
}
