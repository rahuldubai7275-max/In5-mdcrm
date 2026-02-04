<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealTracking;
use Illuminate\Http\Request;

class DealTrackingController extends Controller
{
    public function tracking(Request $request){

        $DealTracking=DealTracking::where('deal_id',request('deal_id'))->orderBy('row','ASC')->get();
        $deal=Deal::where('id',request('deal_id'))->first();

        if(!$DealTracking){
            return 'page not found';
        }
        return view('/admin/deal-tracking', [
            'dealTracking' => $DealTracking,
            'deal' => $deal,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'deal'=>'required',
            'title'=>'required',
        ]);
        $deal_id=request('deal');
        $MaxRow=DealTracking::where('deal_id',$deal_id)->where('type',0)->orderBy('row','DESC')->first();
        $row=1;
        if($MaxRow)
            $row=$MaxRow->row+1;
        DealTracking::create([
            'deal_id'=>$deal_id,
            'row'=>$row,
            'title'=>request('title'),
        ]);

        $completedTracking = DealTracking::where('deal_id',$deal_id)->where('type',1)->first();
        $row++;
        $completedTracking->row =$row;
        $completedTracking->save();
    }

    public function edit(Request $request){
        $request->validate([
            '_id'=>'required',
            'title'=>'required|string',
        ]);
        $DealTracking = DealTracking::find(request('_id'));
        $DealTracking->title = request('title');
        $DealTracking->save();

    }

    public function statusAction(Request $request){
        $request->validate([
            'tracking'=>'required|string',
        ]);

        $DealTracking = DealTracking::find(request('tracking'));
        if($DealTracking->type==1){
            $checkStepTracking = DealTracking::where('type',0)->where('status',0)->where('deal_id',$DealTracking->deal_id)->first();
            if($checkStepTracking){
                return ['r'=>'0','msg'=>'You need to complete all above fields.'];
            }
        }
        $DealTracking->done_date = request('date');
        $DealTracking->status = 1;
        $DealTracking->save();

        return ['r'=>'1','msg'=>''];
    }

    public function rowRefresh(Request $request){
        $request->validate([
            'tracking'=>'required',
        ]);

        $i=0;
        $deal_id;

        foreach (request('tracking') as $id) {
            $i++;
            $DealTracking = DealTracking::find($id);
            $DealTracking->row =$i;
            $deal_id=$DealTracking->deal_id;
            $DealTracking->save();
        }

        $completedTracking = DealTracking::where('deal_id',$deal_id)->where('type',1)->first();
        $i++;
        $completedTracking->row =$i;
        $completedTracking->save();
    }

    public function getDealTracking(Request $request){
        $request->validate([
            'deal'=>'required',
        ]);
        $DealTracking = DealTracking::where('deal_id',$request->deal)->where('type',0)->orderBy('row','ASC')->get();

        $stepInput='';
        foreach ($DealTracking as $row){
            $actionHtml='<a href="#trackingAdd" data-toggle="modal" class="tracking-edit-record"><i class="users-edit-icon feather icon-edit-1"></i></a>
                         <a href="#doneTracking" data-toggle="modal" class="tracking-done"><i class="users-edit-icon feather icon-check-circle"></i></a>
                         <a href="javascript:void(0);" class="ajax-delete" title="Delete"><i class="users-delete-icon feather icon-trash-2"></i></a>';
            if($row->status==1){
                $actionHtml='';
            }
            $stepInput.='<li class="list-group-item">
                            <div class="float-left">
                                <div class="d-flex">
                                <div><span class="badge badge-'.( ($row->status==1) ? 'success' : 'secondary' ).' badge-pill mr-1">'.$row->row.'</span></div>
                                <span>'.$row->title.( ($row->done_date) ? '<br><small>'.$row->done_date.'</small>' : '').'</span>

                                </div>

                            </div>
                            <div class="action float-right font-medium-1"  data-model="'.route('deal-tracking.delete').'" data-id="'.$row->id.'" data-title="'.$row->title.'">
                                '.$actionHtml.'
                            </div>
                        </li>';
        }

        $completedInput='';
        $completedTracking = DealTracking::where('deal_id',$request->deal)->where('type',1)->first();
        if($completedTracking){
            $actionHtml='<a href="#doneTracking" data-toggle="modal" class="tracking-done"><i class="users-edit-icon feather icon-check-circle"></i></a>';
            if($completedTracking->status==1){
                $actionHtml='';
            }
            $completedInput.='<li class="list-group-item">
                            <div class="float-left">
                                <div class="d-flex">
                                <div><span class="badge badge-'.( ($completedTracking->status==1) ? 'success' : 'secondary' ).' badge-pill mr-1">'.$completedTracking->row.'</span></div>
                                <span>'.$completedTracking->title.( ($completedTracking->done_date) ? '<br><small>'.$completedTracking->done_date.'</small>' : '').'</span>

                                </div>

                            </div>
                            <div class="action float-right font-medium-1"  data-id="'.$completedTracking->id.'">
                                '.$actionHtml.'
                            </div>
                        </li>';
        }
        return ['step'=>$stepInput,'completed'=>$completedInput];
    }

    public function delete(){
        $DealTracking = DealTracking::find( request('Delete') );
        $deal_id=$DealTracking->deal_id;
        $DealTracking->delete();

        $DealTracking = DealTracking::where('deal_id',$deal_id)->orderBy('row','ASC')->get();
        $i=0;
        foreach ($DealTracking as $row){
            $i++;
            $DealTracking = DealTracking::find($row->id);
            $DealTracking->row = $i;
            $DealTracking->save();
        }
//        return redirect('/admin/deal-view/'.$deal_id);
    }
}
