<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Target;
use App\Models\Property;
use App\Models\TargetHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TargetController extends Controller
{
    public function Targets(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/target', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'admin'=>'required',
            'num_calls'=>'required',
            'num_viewing'=>'required',
            'num_ma'=>'required',
            'num_listing'=>'required',
            'commission'=>'required'
        ]);
        $adminAuth=\Auth::guard('admin')->user();

        $admin=request('admin');
        $period=request('period');
        $target=Target::where('admin_id',$admin)->where('period',$period)->first();
        if(!$target){
            $admin_id=request('admin');
            $num_calls=str_replace(',','',request('num_calls'));
            $num_viewing=str_replace(',','',request('num_viewing'));
            $num_ma=str_replace(',','',request('num_ma'));
            $num_listing=str_replace(',','',request('num_listing'));
            $commission=str_replace(',','',request('commission'));
            Target::create([
                'period'=>1,
                'company_id'=>$adminAuth->company_id,
                'admin_id'=>$admin_id,
                'num_calls'=>$num_calls,
                'num_viewing'=>$num_viewing,
                'num_ma'=>$num_ma,
                'num_listing'=>$num_listing,
                'commission'=>$commission
            ]);
            Target::create([
                'period'=>2,
                'company_id'=>$adminAuth->company_id,
                'admin_id'=>$admin_id,
                'num_calls'=>$num_calls*12,
                'num_viewing'=>$num_viewing*12,
                'num_ma'=>$num_ma*12,
                'num_listing'=>$num_listing*12,
                'commission'=>$commission*12
            ]);


        }else{
            Session::flash('error','You cannot define a target twice for an agent');
        }
        return redirect('/admin/target/'.$period);
    }

    public function Edit(Request $request){
        $request->validate([
            'admin'=>'required',
            'num_calls'=>'required',
            'num_viewing'=>'required',
            'num_ma'=>'required',
            'num_listing'=>'required',
            'commission'=>'required'
        ]);
        $admin_id=request('admin');
        $num_calls=str_replace(',','',request('num_calls'));
        $num_viewing=str_replace(',','',request('num_viewing'));
        $num_ma=str_replace(',','',request('num_ma'));
        $num_listing=str_replace(',','',request('num_listing'));
        $commission=str_replace(',','',request('commission'));

        $Target = Target::find(request('update'));
        $Target->admin_id = $admin_id;
        $Target->num_calls = $num_calls;
        $Target->num_viewing =$num_viewing;
        $Target->num_ma =$num_ma;
        $Target->num_listing =$num_listing;
        $Target->commission =$commission;
        $Target->save();

        $TargetYearly = Target::where('admin_id',$admin_id)->where('period',2)->first();
        $TargetYearly->admin_id = $admin_id;
        $TargetYearly->num_calls = $num_calls*12;
        $TargetYearly->num_viewing =$num_viewing*12;
        $TargetYearly->num_ma =$num_ma*12;
        $TargetYearly->num_listing =$num_listing*12;
        $TargetYearly->commission =$commission*12;
        $TargetYearly->save();


        return redirect('/admin/target/'.$Target->period);
    }

    public function Delete(){
        $Target = Target::find( request('Delete') );

        $TargetYearly = Target::where('period',2)->where( 'admin_id',$Target->admin_id )->first();
        $TargetYearly->delete();
        $Target->delete();
        return redirect('/admin/target/'.$Target->period);
    }

    public function GetTargets(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $adminAuth=\Auth::guard('admin')->user();

        $period=request('target');
        $where=' AND targets.company_id='.$adminAuth->company_id.' AND period='.$period;

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `targets` WHERE 1 ".$where);
        $totalRecords=$totalRecords[0]->countAll;//$TargetCount->where('period',$period)->count();

        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM admins,targets WHERE admins.id=targets.admin_id ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT targets.*,firstname,lastname FROM admins,targets WHERE admins.id=targets.admin_id ".$where." ORDER BY ".$columnName." ".$columnSortOrder);
        }else{
            $Records=DB::select("SELECT targets.*,firstname,lastname FROM admins,targets WHERE admins.id=targets.admin_id ".$where." ORDER BY ".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $obj['firstname']=$row->firstname.' '.$row->lastname;
            $obj['num_calls']=number_format($row->num_calls);
            $obj['num_viewing']=number_format($row->num_viewing);
            $obj['num_ma']=number_format($row->num_ma);
            $obj['num_listing']=number_format($row->num_listing);
            $obj['commission']=number_format($row->commission);

            $obj['Action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="'.route("target.delete").'" data-edit="'.route("target.edit").'"
            data-admin="'.$row->admin_id.'"
            data-num_calls="'.$row->num_calls.'"
            data-num_viewing="'.$row->num_viewing.'"
            data-num_ma="'.$row->num_ma.'"
            data-num_listing="'.$row->num_listing.'"
            data-commission="'.$row->commission.'"
            >
                            <a href="#ModalTaregt" data-toggle="modal" class="edit-record" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                            '.(($adminAuth->type==1) ? '<a href="javascript:void(0)" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>' : '' ).'
                          </div>';
            $data[] = $obj;
            $obj=[];
        }
        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return json_encode($response);
    }

    function ajaxDashboard(){

        $period=request('period');
        $admin_id='';
        $agent_id='';
        $year=request('year');
        $month=request('month');


        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->type==1) {
            if(request('admin')!='') {
                $admin_id = 'AND admin_id=' . request('admin');
                $agent_id = 'AND deal_agents.agent_id=' . request('admin');
            }
        }else{
            $admin_id = 'AND admin_id=' . $adminAuth->id;
            $agent_id = 'AND deal_agents.agent_id=' . $adminAuth->id;
        }

        if($period==1) {
            $dateLimit = $year.'-'.$month.'-01 00:00:01';
            $end_dateLimit = date("Y-m-t", strtotime($dateLimit)).' 23:59:59';

            $v_dateLimit=$year.'-'.$month.'-01';
            $end_v_dateLimit=date("Y-m-t", strtotime($v_dateLimit));
        }else {
            $dateLimit = $year . '-01-01 00:00:01';
            $end_dateLimit = date("Y-m-t", strtotime($year.'-12-01')).' 23:59:59';

            $v_dateLimit=$year.'-01-01';
            $end_v_dateLimit=date("Y-m-t", strtotime($v_dateLimit));
        }

        $month_listed_count=DB::select('SELECT Count(DISTINCT property_id) as countAll FROM property_status_history, property WHERE property_status_history.property_id=property.id AND company_id='.$adminAuth->company_id.' AND h_admin_id IN (SELECT DISTINCT admin_id FROM targets) AND property_status_history.status=11 AND rfl_status=1 '.str_replace('admin_id','h_admin_id',$admin_id).' AND accept_date>="'.$dateLimit.'" AND accept_date<="'.$end_dateLimit.'"');

        $month_ma_count=DB::select('SELECT Count(*) as countAll FROM property_status_history, property WHERE property_status_history.property_id=property.id AND company_id='.$adminAuth->company_id.' AND h_admin_id IN (SELECT DISTINCT admin_id FROM targets) AND  property_status_history.status=4 AND ma_first=1 '.str_replace('admin_id','h_admin_id',$admin_id).' AND property_status_history.created_at>="'.$dateLimit.'" AND property_status_history.created_at<="'.$end_dateLimit.'"');

        $month_call_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND admin_id IN (SELECT DISTINCT admin_id FROM targets) AND `note_subject`=1  '.$admin_id.' AND created_at>="'.$dateLimit.'" AND created_at<="'.$end_dateLimit.'"');

        $month_call_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND admin_id IN (SELECT DISTINCT admin_id FROM targets) AND `note_subject`=1  '.$admin_id.' AND created_at>="'.$dateLimit.'" AND created_at<="'.$end_dateLimit.'"');

        $month_call_dc_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE company_id='.$adminAuth->company_id.' AND admin_id IN (SELECT DISTINCT admin_id FROM targets) AND `note_subject`=1 '.$admin_id.' AND created_at>="'.$dateLimit.'" AND created_at<="'.$end_dateLimit.'"');

        $month_viewing_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND admin_id IN (SELECT DISTINCT admin_id FROM targets) AND `note_subject`=2 AND `status`=1  '.$admin_id.' AND date_at>="'.$v_dateLimit.'" AND date_at<="'.$end_v_dateLimit.'"');

        $month_viewing_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND admin_id IN (SELECT DISTINCT admin_id FROM targets) AND `note_subject`=2 AND `status`=1  '.$admin_id.' AND date_at>="'.$v_dateLimit.'" AND date_at<="'.$end_v_dateLimit.'"');

        $month_commission_sum=DB::select('SELECT SUM(deal_agents.commission) as sumAll FROM deal,deal_agents WHERE deal.id=deal_agents.deal_id AND company_id='.$adminAuth->company_id.' AND deal.status=1 '.$agent_id.'  AND deal_agents.created_at>="'.$dateLimit.'" AND deal_agents.created_at<="'.$end_dateLimit.'"');

        $num_calls='';
        $num_viewing='';
        $num_listing='';
        $num_ma='';
        $commission='';
        $check='====';
        if(request('admin')!='') {
            $target_month_sum=DB::select('SELECT num_calls , num_viewing , num_ma , num_listing , commission FROM `targets` WHERE company_id='.$adminAuth->company_id.' AND period=1 '.$admin_id );
            $num_calls=$target_month_sum[0]->num_calls;
            $num_viewing=$target_month_sum[0]->num_viewing;
            $num_listing=$target_month_sum[0]->num_listing;
            $num_ma=$target_month_sum[0]->num_ma;
            $commission=$target_month_sum[0]->commission;
            $check=1;
            if($period==1) {
                if (request('year') && request('month') && request('month') != date('m')) {
                    $TargetHistory = TargetHistory::where('admin_id', request('admin'))->where('year', $year)->where('month', $month)->first();
                    if ($TargetHistory) {
                        $check=2;
                        $num_calls = $TargetHistory->num_calls;
                        $num_viewing = $TargetHistory->num_viewing;
                        $num_listing = $TargetHistory->num_listing;
                        $num_ma = $TargetHistory->num_ma;
                        $commission = $TargetHistory->commission;
                    }
                }
            }else{
                if (request('year') && request('year') != date('y')) {
                    $TargetHistory=DB::select("SELECT SUM(`num_calls`) as num_calls , SUM(`num_viewing`) as  num_viewing , SUM(`num_ma`) as num_ma , SUM(`num_listing`) as num_listing , SUM(`commission`) as commission FROM `targets_history` WHERE company_id=".$adminAuth->company_id." AND admin_id=".request('admin')." AND year='".$year."'");
                    //$TargetHistory = TargetHistory::where('admin_id', request('admin'))->where('year', $year)->where('month', $month)->first();
                    if ($TargetHistory) {
                        $check=3;
                        $num_calls += $TargetHistory[0]->num_calls;
                        $num_viewing += $TargetHistory[0]->num_viewing;
                        $num_listing += $TargetHistory[0]->num_listing;
                        $num_ma += $TargetHistory[0]->num_ma;
                        $commission += $TargetHistory[0]->commission;
                    }
                }
            }

        }else{
            $check="+";
            $target_month_current_sum='';
            $target_month_sum=DB::select('SELECT SUM(`num_calls`) as num_calls , SUM(`num_viewing`) as  num_viewing , SUM(`num_ma`) as num_ma , SUM(`num_listing`) as num_listing , SUM(`commission`) as commission FROM `targets` WHERE company_id='.$adminAuth->company_id.' AND period='.$period.' '.$admin_id );

            if($period==1) {
                if ($year && $month != date('m')) {
                    $check='-';
                    $target_month_sum=DB::select("SELECT SUM(`num_calls`) as num_calls , SUM(`num_viewing`) as  num_viewing , SUM(`num_ma`) as num_ma , SUM(`num_listing`) as num_listing , SUM(`commission`) as commission FROM `targets_history` WHERE company_id='.$adminAuth->company_id.' AND year='".$year."' AND month='".$month."' ".$admin_id);
                }
            }else{
                if ($year && $year != date('y')) {
                    $target_month_current_sum=DB::select('SELECT SUM(`num_calls`) as num_calls , SUM(`num_viewing`) as  num_viewing , SUM(`num_ma`) as num_ma , SUM(`num_listing`) as num_listing , SUM(`commission`) as commission FROM `targets` WHERE company_id='.$adminAuth->company_id.' AND period=1 '.$admin_id );
                    $target_month_sum = DB::select("SELECT SUM(`num_calls`) as num_calls , SUM(`num_viewing`) as  num_viewing , SUM(`num_ma`) as num_ma , SUM(`num_listing`) as num_listing , SUM(`commission`) as commission FROM `targets_history` WHERE company_id='.$adminAuth->company_id.' AND year='" . $year ."' ".$admin_id);
                }
            }

            $num_calls=$target_month_sum[0]->num_calls;
            $num_viewing=$target_month_sum[0]->num_viewing;
            $num_listing=$target_month_sum[0]->num_listing;
            $num_ma=$target_month_sum[0]->num_ma;
            $commission=$target_month_sum[0]->commission;

            if($period==2) {
                $num_calls+=$target_month_current_sum[0]->num_calls;
                $num_viewing+=$target_month_current_sum[0]->num_viewing;
                $num_listing+=$target_month_current_sum[0]->num_listing;
                $num_ma+=$target_month_current_sum[0]->num_ma;
                $commission+=$target_month_current_sum[0]->commission;
            }
        }

        $show_target='';
        $show_target_modal='';
        if($period==1) {
            $show_target='show-target cursor-pointer';
            $show_target_modal='data-toggle="modal" data-target="#targetModal"';
        }

        $month_num_calls=$num_calls;
        $per_month_num_calls=$month_num_calls == 0 ? 0 : round(( ($month_call_property_count[0]->countAll + $month_call_contact_count[0]->countAll + $month_call_dc_count[0]->countAll )*100)/$month_num_calls);

        $month_num_viewing=$num_viewing;
        $per_month_num_viewing=$month_num_viewing == 0 ? 0 : round(( ($month_viewing_property_count[0]->countAll + $month_viewing_contact_count[0]->countAll )*100)/$month_num_viewing);

        $month_num_listing=$num_listing;
        $per_month_num_listing=$month_num_listing == 0 ? 0 : round(($month_listed_count[0]->countAll*100)/$month_num_listing);

        $month_num_ma=$num_ma;
        $per_month_ma_count=$month_num_ma == 0 ? 0 : round(($month_ma_count[0]->countAll*100)/$month_num_ma);

        $month_commission=$commission;
        $per_month_commission=$month_commission == 0 ? 0 : round(($month_commission_sum[0]->sumAll*100)/$month_commission);

        echo '<div class="d-flex justify-content-between mb-25">
            <div class="browser-info">
                <p class="mb-25">Number of phone calls</p>
                <h4>'.$per_month_num_calls.'%</h4>
            </div>
            <div class="stastics-info text-right">
                <span>'.number_format($month_call_property_count[0]->countAll + $month_call_contact_count[0]->countAll + $month_call_dc_count[0]->countAll).' / '.number_format($month_num_calls).'</span>
            </div>
        </div>
        <div class="progress progress-bar-primary mb-2">
            <div class="progress-bar" role="progressbar" aria-valuenow="'.$per_month_num_calls.'" aria-valuemin="'.$per_month_num_calls.'" aria-valuemax="100" style="width:'.$per_month_num_calls.'%"></div>
        </div>

        <div class="d-flex justify-content-between mb-25">
            <div class="browser-info">
                <p class="mb-25">Number of viewings</p>
                <h4>'.$per_month_num_viewing.'%</h4>
            </div>
            <div class="stastics-info text-right">
                <span>'.number_format($month_viewing_property_count[0]->countAll + $month_viewing_contact_count[0]->countAll).' / '.number_format($month_num_viewing).'</span>
            </div>
        </div>
        <div class="progress progress-bar-primary mb-2">
            <div class="progress-bar" role="progressbar" aria-valuenow="'.$per_month_num_viewing.'" aria-valuemin="'.$per_month_num_viewing.'" aria-valuemax="100"
                 style="width:'.$per_month_num_viewing.'%"></div>
        </div>

        <div class="d-flex justify-content-between mb-25 '.$show_target.'" '.$show_target_modal.' data-type="MA">
            <div class="browser-info">
                <p class="mb-25">Number of MA</p>
                <h4>'.$per_month_ma_count.'%</h4>
            </div>
            <div class="stastics-info text-right">
                <span>'.number_format($month_ma_count[0]->countAll).' / '.number_format($month_num_ma).'</span>
            </div>
        </div>
        <div class="progress progress-bar-primary mb-2">
            <div class="progress-bar" role="progressbar" aria-valuenow="'.$per_month_ma_count.'" aria-valuemin="'.$per_month_ma_count.'" aria-valuemax="100"
                 style="width:'.$per_month_ma_count.'%"></div>
        </div>

        <div class="d-flex justify-content-between mb-25 '.$show_target.'" '.$show_target_modal.' data-type="Listings">
            <div class="browser-info">
                <p class="mb-25">Number of Listings</p>
                <h4>'.$per_month_num_listing.'%</h4>
            </div>
            <div class="stastics-info text-right">
                <span>'.number_format($month_listed_count[0]->countAll).' / '.number_format($month_num_listing).'</span>
            </div>
        </div>
        <div class="progress progress-bar-primary mb-2">
            <div class="progress-bar" role="progressbar" aria-valuenow="'.$per_month_num_listing.'" aria-valuemin="'.$per_month_num_listing.'" aria-valuemax="100"
                 style="width:'.$per_month_num_listing.'%"></div>
        </div>

        <div class="d-flex justify-content-between mb-25">
            <div class="browser-info">
                <p class="mb-25">Commission</p>
                <h4>'.$per_month_commission.'%</h4>
            </div>
            <div class="stastics-info text-right">
                <span>'.number_format($month_commission_sum[0]->sumAll).' / '.number_format($month_commission).'</span>
            </div>
        </div>
        <div class="progress progress-bar-primary mb-50">
            <div class="progress-bar" role="progressbar" aria-valuenow="'.$per_month_commission.'" aria-valuemin="'.$per_month_commission.'" aria-valuemax="100"
                 style="width:'.$per_month_commission.'%"></div>
        </div>';
    }

    public function show(){
        $property_status=request('type');
        $period=request('period');
        $admin_id='';
        $agent_id='';
        $year=request('year');
        $month=request('month');


        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        if($adminAuth->type==1) {
            if(request('admin')!='') {
                $admin_id = 'AND admin_id=' . request('admin');
                $agent_id = 'AND deal_agents.agent_id=' . request('admin');
            }
        }else{
            $admin_id = 'AND admin_id=' . $adminAuth->id;
            $agent_id = 'AND deal_agents.agent_id=' . $adminAuth->id;
        }

        if($period==1) {
            $dateLimit = $year.'-'.$month.'-01 00:00:01';
            $end_dateLimit = date("Y-m-t", strtotime($dateLimit)).' 23:59:59';

            $v_dateLimit=$year.'-'.$month.'-01';
            $end_v_dateLimit=date("Y-m-t", strtotime($v_dateLimit));
        }else {
            $dateLimit = $year . '-01-01 00:00:01';
            $end_dateLimit = date("Y-m-t", strtotime($year.'-12-01')).' 23:59:59';

            $v_dateLimit=$year.'-01-01';
            $end_v_dateLimit=date("Y-m-t", strtotime($v_dateLimit));
        }

        //$month_listed_count=DB::select('SELECT Count(DISTINCT property_id) as countAll FROM property_status_history WHERE h_admin_id IN (SELECT DISTINCT admin_id FROM targets) AND status=11 AND rfl_status=1 '.str_replace('admin_id','h_admin_id',$admin_id).' AND updated_at>="'.$dateLimit.'" AND updated_at<="'.$end_dateLimit.'"');
        $datetimeBy='created_at';
        if($property_status=='Listings') {
            $property_status_history=DB::select('SELECT * FROM property_status_history WHERE h_admin_id IN (SELECT DISTINCT admin_id FROM targets) AND status=11 AND rfl_status=1 '.str_replace('admin_id','h_admin_id',$admin_id).' AND accept_date>="'.$dateLimit.'" AND accept_date<="'.$end_dateLimit.'" ORDER BY created_at DESC');

            $datetimeBy='accept_date';
        }

        if($property_status=='MA') {
            $property_status_history=DB::select('SELECT * FROM property_status_history WHERE h_admin_id IN (SELECT DISTINCT admin_id FROM targets) AND status=4 AND ma_first=1 '.str_replace('admin_id','h_admin_id',$admin_id).' AND created_at>="'.$dateLimit.'" AND created_at<="'.$end_dateLimit.'" ORDER BY created_at DESC');

            $datetimeBy='created_at';
        }

        $output='';
        $i=0;
        foreach ($property_status_history as $row){
            $property=Property::find($row->property_id);
            $admin=Admin::find($row->h_admin_id);
            $i++;
            $output.='<tr>
                        <td>'.$i.'</td>
                        <td><a href="/admin/property/view/'.$property->id.'" target="_blank">'.$company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num.'</a></td>
                        <td>'.$admin->firstname.' '.$admin->lastname.'</td>
                        <td>'.\Helper::changeDatetimeFormat( $row->$datetimeBy ).'</td>
                    </tr>';
        }

        return $output;
    }

}
