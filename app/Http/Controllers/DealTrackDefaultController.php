<?php

namespace App\Http\Controllers;

use App\Models\DealTrackDefault;
use Illuminate\Http\Request;

class DealTrackDefaultController extends Controller
{
    public function DealTrackDefault(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/deal-tracking-default', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'deal_model'=>'required',
            'title'=>'required',
        ]);
        $deal_model_id=request('deal_model');
        $MaxRow=DealTrackDefault::where('deal_model_id',$deal_model_id)->orderBy('row','DESC')->first();
        $row=1;
        if($MaxRow)
            $row=$MaxRow->row+1;
        DealTrackDefault::create([
            'deal_model_id'=>$deal_model_id,
            'row'=>$row,
            'title'=>request('title'),
        ]);
    }

    public function edit(Request $request){
        $request->validate([
            '_id'=>'required',
            'title'=>'required|string',
        ]);
        $DealTrackDefault = DealTrackDefault::find(request('_id'));
        $DealTrackDefault->title = request('title');
        $DealTrackDefault->save();

    }
    public function rowRefresh(Request $request){
        $request->validate([
            'tracking'=>'required',
        ]);

        $i=0;

        foreach (request('tracking') as $id) {
            $i++;
            $DealTrackDefault = DealTrackDefault::find($id);
            $DealTrackDefault->row =$i;
            $DealTrackDefault->save();
        }
    }

    public function getDealTrackingDefault(Request $request){
        $request->validate([
            'deal_model'=>'required',
        ]);
        $DealTrackDefault = DealTrackDefault::where('deal_model_id',$request->deal_model)->orderBy('row','ASC')->get();

        $stepInput='';
        foreach ($DealTrackDefault as $row){
            $actionHtml='<a href="#trackingAdd" data-toggle="modal" class="tracking-edit-record"><i class="users-edit-icon feather icon-edit-1"></i></a>
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
        return $stepInput;
    }

    public function delete(){
        $DealTrackDefault = DealTrackDefault::find( request('Delete') );
        $deal_model_id=$DealTrackDefault->deal_model_id;
        $DealTrackDefault->delete();

        $DealTrackDefault = DealTrackDefault::where('deal_model_id',$deal_model_id)->orderBy('row','ASC')->get();
        $i=0;
        foreach ($DealTrackDefault as $row){
            $i++;
            $DealTrackDefault = DealTrackDefault::find($row->id);
            $DealTrackDefault->row = $i;
            $DealTrackDefault->save();
        }
    }
}
