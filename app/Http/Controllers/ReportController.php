<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function repoets(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $filter='';
        $admins='';
        $from_date='';
        $to_date='';
        $filterForListedUnlisted='';
        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->type > 2 && $adminAuth->type != 5 && $adminAuth->type != 6){
            $filter.=' AND admin_id ='.$adminAuth->id;
            $filterForListedUnlisted.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.')';
        }
        return view('/admin/report', [
            'pageConfigs' => $pageConfigs,
            'filter' => $filter,
            'filterForListedUnlisted' => $filterForListedUnlisted,
            'admins' => $admins,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function reportFilters(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $admins=request('admin');
        $from_date=request('from_date');
        $to_date=request('to_date');
        $filter='';
        $filterForListedUnlisted='';

        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->type > 2 && $adminAuth->type != 5 && $adminAuth->type != 6){
            $filter.=' AND admin_id ='.$adminAuth->id;
            $filterForListedUnlisted.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.')';
        }

        if(request('admin')){
            $filter.=' AND admin_id in ('.$admins.')';
            $filterForListedUnlisted.=' AND (client_manager_id='.$admins.' OR client_manager2_id='.$admins.')';
        }

        if(request('from_date')){
            $filter.=' AND created_at >="'.$from_date.' 00:00:00"';
        }
        if(request('to_date')){
            $filter.=' AND created_at <="'.$to_date.' 23:59:59"';
        }

        return view('/admin/report', [
            'pageConfigs' => $pageConfigs,
            'filter' => $filter,
            'filterForListedUnlisted' => $filterForListedUnlisted,
            'admins' => $admins,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function telesalesRepoets(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $filter='';
        $admins='';
        $from_date='';
        $to_date='';
        $filterForListedUnlisted='';
        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->type > 2 && $adminAuth->type != 5 && $adminAuth->type != 6){
            $admins=$adminAuth->id;
            $filter.=' AND admin_id ='.$adminAuth->id;
            $filterForListedUnlisted.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.')';
        }
        return view('/admin/data-center-report', [
            'pageConfigs' => $pageConfigs,
            'filter' => $filter,
            'filterForListedUnlisted' => $filterForListedUnlisted,
            'admins' => $admins,
            'from_date' => $from_date,
            'to_date' => $to_date,
        ]);
    }

    public function telesalesReportFilters(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $admins=request('admin');
        $from_date=request('from_date');
        $to_date=request('to_date');
        $filter='';
        $filterForListedUnlisted='';

        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->type > 2 && $adminAuth->type != 5 && $adminAuth->type != 6){
            $filter.=' AND admin_id ='.$adminAuth->id;
            $filterForListedUnlisted.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.')';
        }

        if(request('admin')){
            $filter.=' AND admin_id in ('.$admins.')';
            $filterForListedUnlisted.=' AND (client_manager_id='.$admins.' OR client_manager2_id='.$admins.')';
        }

        if(request('from_date')){
            $filter.=' AND created_at >="'.$from_date.' 00:00:00"';
        }
        if(request('to_date')){
            $filter.=' AND created_at <="'.$to_date.' 23:59:59"';
        }

        $data=[];
        $call_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=1 '.$filter);
        $data['call_count']=$call_count[0]->countAll;

        //$note_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=4 '.$filter);
        //$data['note_count']=$note_count[0]->countAll;

        $email_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=5 '.$filter);
        $data['email_count']=$email_count[0]->countAll;

        $reminder_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=6 '.$filter);
        $data['reminder_count']=$reminder_count[0]->countAll;

        $lead_count=DB::select('SELECT Count(*) as countAll FROM leads WHERE 1 '.(($admins) ? str_replace('admin_id','telesales_id',$filter) : str_replace('admin_id','telesales_id',$filter).' AND telesales_id IS NOT NULL'));
        $data['lead_count']=$lead_count[0]->countAll;

        return $data;
    }

    public function repoetsBestAgent(Request $request){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $today = date('Y-m-d');
        $year = date('Y');
        $month = date('m');

        $month_start=date('Y-m').'-01 00:00:00';
        $month_end=date('Y-m-d H:i:s');//date("Y-m-t", strtotime($today)).' 23:59:59';

        $adminAuth=\Auth::guard('admin')->user();

        $where=" AND created_at>='".$month_start."' AND created_at<='".$month_end."'";
        if(request('year') && request('month')){
            $year=request('year');
            $month=request('month');
            $month_start=$year.'-'.$month.'-01 00:00:00';

            if($year.'-'.$month==date('Y-m'))
                $month_end = date('Y-m-d H:i:s');
            else
                $month_end=date("Y-m-t", strtotime($month_start)).' 23:59:59';

            $where=" AND created_at>='".$month_start."' AND created_at<='".$month_end."'";
        }elseif(request('year') && !request('month')){
            if(request('year')!='all') {
                $year=request('year');
                $month='';
                $month_start = $year . '-01-01 00:00:00';
                if($year==date('Y'))
                    $month_end = date('Y-m-d H:i:s');
                else
                    $month_end = date("Y-m-t", strtotime($year . '-12-01'));

                $where=" AND created_at>='".$month_start."' AND created_at<='".$month_end."'";
            }else{
                $year=request('year');
                $where="";
            }
        }

        $calls=DB::select("SELECT * FROM (
                            SELECT admin_id, COUNT(admin_id) AS countAll FROM `all_activity_coll_count` WHERE company_id=".$adminAuth->company_id."
                                ".$where."
                            GROUP BY admin_id
                        ) AS a WHERE countAll IN(
                            SELECT MAX(countAll) FROM (
                                SELECT admin_id, COUNT(admin_id) AS countAll FROM all_activity_coll_count WHERE company_id=".$adminAuth->company_id."
                                    ".$where."
                                GROUP BY admin_id
                            ) AS b)");
        $call_agent='';
        foreach ($calls as $row){
            $admin=Admin::find($row->admin_id);
            $call_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->countAll).'), ';
        }

        //$viewing=DB::select("SELECT * FROM (SELECT admin_id , COUNT(admin_id) AS countAll FROM view_activities WHERE note_subject=2 ".str_replace('created_at','concat(date_at," ",time_at)',$where)." AND status=1 GROUP BY admin_id) as a WHERE countAll in (SELECT MAX(countAll) FROM (SELECT admin_id , COUNT(admin_id) AS countAll FROM view_activities WHERE note_subject=2 ".str_replace('created_at','concat(date_at," ",time_at)',$where)." AND status=1 GROUP BY admin_id ) as b)");
        $viewing=DB::select("SELECT
                                        *
                                    FROM
                                        (
                                        SELECT admin_id, admins.status as admin_status ,COUNT(admin_id) AS countAll FROM view_activities,admins WHERE view_activities.admin_id=admins.id AND view_activities.company_id=".$adminAuth->company_id." AND admins.status=1 AND note_subject = 2 ".str_replace('created_at','concat(date_at," ",time_at)',$where)." AND view_activities.STATUS = 1 GROUP BY admin_id , admins.status
                                    ) AS a
                                    WHERE
                                        countAll IN(
                                        SELECT MAX(countAll) FROM ( SELECT admin_id, COUNT(admin_id) AS countAll FROM view_activities, admins WHERE view_activities.admin_id=admins.id AND view_activities.company_id=".$adminAuth->company_id." AND admins.status=1 AND note_subject = 2 ".str_replace('created_at','concat(date_at," ",time_at)',$where)." AND view_activities.STATUS = 1 GROUP BY admin_id ) AS b
                                    )");
        $viewing_agent='';
        foreach ($viewing as $row){
            $admin=Admin::find($row->admin_id);
            $viewing_agent.=$admin->firstname.' '.$admin->lastname. ' ('.$row->countAll.'), ';
        }

        $appointment=DB::select("SELECT
                                        *
                                    FROM
                                        (
                                        SELECT admin_id, admins.status as admin_status ,COUNT(admin_id) AS countAll FROM view_activities,admins WHERE view_activities.admin_id=admins.id AND view_activities.company_id=".$adminAuth->company_id." AND admins.status=1 AND note_subject = 3 ".str_replace('created_at','concat(date_at," ",time_at)',$where)." AND view_activities.STATUS = 1 GROUP BY admin_id , admins.status
                                    ) AS a
                                    WHERE
                                        countAll IN(
                                        SELECT MAX(countAll) FROM ( SELECT admin_id, COUNT(admin_id) AS countAll FROM view_activities, admins WHERE view_activities.admin_id=admins.id AND view_activities.company_id=".$adminAuth->company_id." AND admins.status=1 AND note_subject = 3 ".str_replace('created_at','concat(date_at," ",time_at)',$where)." AND view_activities.STATUS = 1 GROUP BY admin_id ) AS b
                                    )");
        $appointment_agent='';
        foreach ($appointment as $row){
            $admin=Admin::find($row->admin_id);
            $appointment_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->countAll).'), ';
        }

        $addedProperty=DB::select("SELECT * FROM (SELECT admin_id ,COUNT(admin_id) AS countAll FROM property,admins WHERE property.admin_id=admins.id AND property.company_id=".$adminAuth->company_id." AND admins.status=1 ".str_replace('created_at','property.created_at',$where)." GROUP BY admin_id) as a WHERE countAll in (SELECT MAX(countAll) FROM (SELECT admin_id ,COUNT(admin_id) AS countAll FROM property,admins WHERE property.admin_id=admins.id AND property.company_id=".$adminAuth->company_id." AND admins.status=1 ".str_replace('created_at','property.created_at',$where)." GROUP BY admin_id) as b)");
        $addedProperty_agent='';
        foreach ($addedProperty as $row){
            $admin=Admin::find($row->admin_id);
            $addedProperty_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->countAll).'), ';
        }

        $addedContact=DB::select("SELECT * FROM (SELECT admin_id ,COUNT(admin_id) AS countAll FROM contacts,admins WHERE contacts.admin_id=admins.id AND contacts.company_id=".$adminAuth->company_id." AND admins.status=1 ".str_replace('created_at','contacts.created_at',$where)." GROUP BY admin_id) as a WHERE countAll in (SELECT MAX(countAll) FROM (SELECT admin_id ,COUNT(admin_id) AS countAll FROM contacts,admins WHERE contacts.admin_id=admins.id AND contacts.company_id=".$adminAuth->company_id." AND admins.status=1 ".str_replace('created_at','contacts.created_at',$where)." GROUP BY admin_id) as b)");
        $addedContact_agent='';
        foreach ($addedContact as $row){
            $admin=Admin::find($row->admin_id);
            $addedContact_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->countAll).'), ';
        }

        $addedLead=DB::select("SELECT * FROM (SELECT result_specifier, COUNT(result_specifier) as countAll FROM leads,admins WHERE leads.result_specifier=admins.id AND leads.company_id=".$adminAuth->company_id." AND admins.status=1 AND leads.status=1 ".str_replace('created_at','result_date',$where)." GROUP BY result_specifier) as a WHERE countAll in (SELECT MAX(countAll) FROM (SELECT result_specifier, COUNT(result_specifier) as countAll FROM leads,admins WHERE leads.result_specifier=admins.id AND leads.company_id=".$adminAuth->company_id." AND admins.status=1 AND leads.status=1 ".str_replace('created_at','result_date',$where)."  GROUP BY result_specifier) as b)");
        $addedLead_agent='';
        foreach ($addedLead as $row){
            $admin=Admin::find($row->result_specifier);
            $addedLead_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->countAll).'), ';
        }

        $closedLead=DB::select("SELECT * FROM (SELECT result_specifier, COUNT(result_specifier) as countAll FROM leads,admins WHERE leads.result_specifier=admins.id AND leads.company_id=".$adminAuth->company_id." AND admins.status=1 AND leads.status=2 ".str_replace('created_at','result_date',$where)."  GROUP BY result_specifier) as a WHERE countAll in (SELECT MAX(countAll) FROM (SELECT result_specifier, COUNT(result_specifier) as countAll FROM leads,admins WHERE leads.result_specifier=admins.id AND leads.company_id=".$adminAuth->company_id." AND admins.status=1 AND leads.status=2 ".str_replace('created_at','result_date',$where)."  GROUP BY result_specifier) as b)");
        $closedLead_agent='';
        foreach ($closedLead as $row){
            $admin=Admin::find($row->result_specifier);
            $closedLead_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->countAll).'), ';
        }

        $countDeal=DB::select("SELECT * FROM (SELECT agent_id, COUNT(agent_id) as countAll FROM deal,deal_agents,admins WHERE deal.id=deal_agents.deal_id AND deal_agents.agent_id=admins.id AND deal.company_id=".$adminAuth->company_id." AND admins.status=1 AND deal.status=1 ".str_replace('created_at','deal.created_at',$where)." GROUP BY agent_id) as a WHERE countAll in (SELECT MAX(countAll) FROM (SELECT agent_id, COUNT(agent_id) as countAll FROM deal,deal_agents,admins WHERE deal.id=deal_agents.deal_id AND deal_agents.agent_id=admins.id AND deal.company_id=".$adminAuth->company_id." AND admins.status=1 AND deal.status=1 ".str_replace('created_at','deal.created_at',$where)." GROUP BY agent_id) as b)");
        $countDeal_agent='';
        foreach ($countDeal as $row){
            $admin=Admin::find($row->agent_id);
            $countDeal_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->countAll).'), ';
        }

        $sumCommission=DB::select("SELECT * FROM (SELECT agent_id, SUM(deal_agents.commission) as sumAll FROM deal,deal_agents,admins WHERE deal.id=deal_agents.deal_id AND deal_agents.agent_id=admins.id AND deal.company_id=".$adminAuth->company_id." AND admins.status=1 AND deal.status=1 ".str_replace('created_at','deal.created_at',$where)." GROUP BY agent_id) as a WHERE sumAll in (SELECT MAX(sumAll) FROM (SELECT agent_id, SUM(deal_agents.commission) as sumAll FROM deal,deal_agents,admins WHERE deal.id=deal_agents.deal_id AND deal_agents.agent_id=admins.id AND deal.company_id=".$adminAuth->company_id." AND admins.status=1 AND deal.status=1 ".str_replace('created_at','deal.created_at',$where)." GROUP BY agent_id) as b)");
        $sumCommission_agent='';
        foreach ($sumCommission as $row){
            $admin=Admin::find($row->agent_id);
            $sumCommission_agent.=$admin->firstname.' '.$admin->lastname. ' ('.number_format($row->sumAll).'), ';
        }

        return view('/admin/report-best', [
            'pageConfigs' => $pageConfigs,
            'mostCaller' => rtrim($call_agent,', '),
            'mostViewing' => rtrim($viewing_agent,', '),
            'mostAppointment' => rtrim($appointment_agent,', '),
            'mostAddedProperty' => rtrim($addedProperty_agent,', '),
            'mostAddedContact' => rtrim($addedContact_agent,', '),
            'mostAddedLead' => rtrim($addedLead_agent,', '),
            'mostClosedLead' => rtrim($closedLead_agent,', '),
            'mostCountDeal' => rtrim($countDeal_agent,', '),
            'mostCommission' => rtrim($sumCommission_agent,', '),
            'year' => $year,
            'month' => $month
        ]);
    }

    public function listBestAgent(){

        $adminAuth=\Auth::guard('admin')->user();

        $type=request('type');
        $activity_type=request('activity_type');

        $today = date('Y-m-d');
        $year = date('Y');
        $month = date('m');

        $month_start=date('Y-m').'-01 00:00:00';
        $month_end=date('Y-m-d H:i:s');//date("Y-m-t", strtotime($today)).' 23:59:59';

        $where=" AND created_at>='".$month_start."' AND created_at<='".$month_end."'";
        if(request('year') && request('month')){
            $year=request('year');
            $month=request('month');
            $month_start=$year.'-'.$month.'-01 00:00:00';

            if($year.'-'.$month==date('Y-m'))
                $month_end = date('Y-m-d H:i:s');
            else
                $month_end=date("Y-m-t", strtotime($month_start)).' 23:59:59';

            $where=" AND created_at>='".$month_start."' AND created_at<='".$month_end."'";
        }elseif(request('year') && !request('month')){
            if(request('year')!='all') {
                $year=request('year');
                $month='';
                $month_start = $year . '-01-01 00:00:00';

                if($year==date('Y'))
                    $month_end = date('Y-m-d H:i:s');
                else
                    $month_end = date("Y-m-t", strtotime($year . '-12-01'));
                $where=" AND created_at>='".$month_start."' AND created_at<='".$month_end."'";
            }else{
                $year=request('year');
                $where="";
            }
        }

        $list_output = '<table class="table table-striped dataex-html5-selectors truncate-table"><thead>
                                <tr>
                                    <th>No</th>
                                    <th>Client Manager</th>
                                    <th>Number</th>
                                </tr>
                            </thead>
                            <tbody>';

        if($type=='activity') {
            if($activity_type!=1){
                if($activity_type==2 || $activity_type==3){
                    $where=str_replace('created_at','concat(date_at," ",time_at)',$where);
                }
                $where=str_replace('created_at','view_activities.created_at',$where);

                $activities = DB::select("SELECT admin_id , COUNT(admin_id) AS countAll FROM view_activities,admins WHERE view_activities.admin_id=admins.id AND view_activities.company_id=".$adminAuth->company_id." AND admins.status=1 AND note_subject=".$activity_type." AND view_activities.status=1 ".$where." GROUP BY admin_id ORDER BY countAll DESC");

            }else{
                $activities = DB::select("SELECT admin_id , COUNT(admin_id) AS countAll FROM all_activity_coll_count WHERE company_id=".$adminAuth->company_id." ".$where." GROUP BY admin_id ORDER BY countAll DESC");

            }
            $i=0;
            foreach ($activities as $row) {
                $i++;
                $admin = Admin::find($row->admin_id);
                $list_output .= '<tr>';
                $list_output .= '<td>'.$i.'</td>';
                $list_output .= '<td>'.$admin->firstname . ' ' . $admin->lastname.'</td>';
                $list_output .= '<td>'.number_format($row->countAll).'</td>';
                $list_output .= '</tr>';
            }
        }

        if($type=='property') {
            $where=str_replace('created_at','property.created_at',$where);
            $addedProperty=DB::select("SELECT admin_id ,COUNT(admin_id) AS countAll FROM property,admins WHERE property.admin_id=admins.id AND property.company_id=".$adminAuth->company_id." AND admins.status=1 ".$where." GROUP BY admin_id ORDER BY countAll DESC");

            $i=0;
            foreach ($addedProperty as $row){
                $i++;
                $admin = Admin::find($row->admin_id);
                $list_output .= '<tr>';
                $list_output .= '<td>'.$i.'</td>';
                $list_output .= '<td>'.$admin->firstname . ' ' . $admin->lastname.'</td>';
                $list_output .= '<td>'.number_format($row->countAll).'</td>';
                $list_output .= '</tr>';
            }
        }

        if($type=='contact') {
            $where=str_replace('created_at','contacts.created_at',$where);
            $addedContact=DB::select("SELECT admin_id ,COUNT(admin_id) AS countAll FROM contacts,admins WHERE contacts.admin_id=admins.id AND contacts.company_id=".$adminAuth->company_id." AND admins.status=1 ".$where." GROUP BY admin_id ORDER BY countAll DESC");

            $i=0;
            foreach ($addedContact as $row){
                $i++;
                $admin = Admin::find($row->admin_id);
                $list_output .= '<tr>';
                $list_output .= '<td>'.$i.'</td>';
                $list_output .= '<td>'.$admin->firstname . ' ' . $admin->lastname.'</td>';
                $list_output .= '<td>'.number_format($row->countAll).'</td>';
                $list_output .= '</tr>';
            }
        }

        if($type=='leadAdded') {
            $addedLead=DB::select("SELECT result_specifier, COUNT(result_specifier) as countAll FROM leads,admins WHERE leads.result_specifier=admins.id AND leads.company_id=".$adminAuth->company_id." AND admins.status=1 AND leads.status=1 ".str_replace('created_at','result_date',$where)." AND result_specifier IS NOT null GROUP BY result_specifier ORDER BY countAll DESC");

            $i=0;
            foreach ($addedLead as $row){
                $i++;
                $admin = Admin::find($row->result_specifier);
                $list_output .= '<tr>';
                $list_output .= '<td>'.$i.'</td>';
                $list_output .= '<td>'.$admin->firstname . ' ' . $admin->lastname.'</td>';
                $list_output .= '<td>'.number_format($row->countAll).'</td>';
                $list_output .= '</tr>';
            }
        }

        if($type=='closedLead') {
            $closedLead=DB::select("SELECT result_specifier, COUNT(result_specifier) as countAll FROM leads,admins WHERE leads.result_specifier=admins.id AND leads.company_id=".$adminAuth->company_id." AND admins.status=1 AND leads.status=2 ".str_replace('created_at','result_date',$where)." AND result_specifier IS NOT null GROUP BY result_specifier ORDER BY countAll DESC");

            $i=0;
            foreach ($closedLead as $row){
                $i++;
                $admin = Admin::find($row->result_specifier);
                $list_output .= '<tr>';
                $list_output .= '<td>'.$i.'</td>';
                $list_output .= '<td>'.$admin->firstname . ' ' . $admin->lastname.'</td>';
                $list_output .= '<td>'.number_format($row->countAll).'</td>';
                $list_output .= '</tr>';
            }
        }

        if($type=='deal') {
            $countDeal=DB::select("SELECT agent_id, COUNT(agent_id) as countAll FROM deal,deal_agents,admins WHERE deal.id=deal_agents.deal_id AND deal_agents.agent_id=admins.id AND deal.company_id=".$adminAuth->company_id." AND admins.status=1 AND deal.status=1 ".str_replace('created_at','deal.created_at',$where)." GROUP BY agent_id ORDER BY countAll DESC");

            $i=0;
            foreach ($countDeal as $row){
                $i++;
                $admin = Admin::find($row->agent_id);
                $list_output .= '<tr>';
                $list_output .= '<td>'.$i.'</td>';
                $list_output .= '<td>'.$admin->firstname.' '.$admin->lastname.'</td>';
                $list_output .= '<td>'.number_format($row->countAll).'</td>';
                $list_output .= '</tr>';
            }
        }

        if($type=='commission') {
            $list_output=str_replace('Number','Price (AED)',$list_output);
            $sumCommission=DB::select("SELECT agent_id, SUM(deal_agents.commission) as sumAll FROM deal,deal_agents,admins WHERE deal.id=deal_agents.deal_id AND deal_agents.agent_id=admins.id AND deal.company_id=".$adminAuth->company_id." AND admins.status=1 AND deal.status=1 ".str_replace('created_at','deal.created_at',$where)." GROUP BY agent_id ORDER BY sumAll DESC");

            $i=0;
            foreach ($sumCommission as $row){
                $i++;
                $admin = Admin::find($row->agent_id);
                $list_output .= '<tr>';
                $list_output .= '<td>'.$i.'</td>';
                $list_output .= '<td>'.$admin->firstname.' '.$admin->lastname.'</td>';
                $list_output .= '<td>'.number_format($row->sumAll).'</td>';
                $list_output .= '</tr>';
            }
        }

        $list_output .= '</tbody></table>';
        return $list_output;
    }


}
