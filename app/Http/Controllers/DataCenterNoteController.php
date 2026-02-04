<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\DataCenter;
use App\Models\DataCenterNote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DataCenterNoteController extends Controller
{
    public function Store(Request $request){
        $request->validate([
            'data_center'=>'required',
            'note_subject'=>'required',
            // 'note'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $note_subject=request('note_subject');

        $property_id=null;
        if($note_subject==2)
            $property_id=request('property');

        $note_text=request('note');
        $data=DataCenterNote::create([
            'admin_id'=>$adminAuth->id,
            'note_subject'=>$note_subject,
            'date_at'=>request('date_at'),
            'time_at'=>request('time_at'),
            'data_center_id'=>request('data_center'),
            'note'=>$note_text
        ]);

        $note=DataCenterNote::find($data->id);

        return '<tr class="note-description" data-title="'.NoteSubject[$note->note_subject].'" data-desc="'.$note->note.'">
                    <td data-target="#ViewModal" data-toggle="modal">'.NoteSubject[$note->note_subject].'</td>
                    <td data-target="#ViewModal" data-toggle="modal">'.
            ( ($note->date_at) ? \Helper::changeDatetimeFormat($note->date_at.' '.$note->time_at).'<br>' : '' )
            .'<span class="note'.$note->id.'">'. \Illuminate\Support\Str::limit(strip_tags($note->note),50)
            .'</span>
                    </td>
                    <td>'.$note->admin->firstname.' '.$note->admin->lastname.'</td>
                    <td data-target="#ViewModal" data-toggle="modal">'.\Helper::changeDatetimeFormat( $note->created_at).'</td>
                </tr>';
    }

    public function GetActivities(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $data = array();

        $adminAuth=\Auth::guard('admin')->user();

        //$where=' WHERE 1 ';
        $where=' ';

        if($adminAuth->type>2 && $adminAuth->type!=5 && $adminAuth->type!=6)
            $totalRecords = DataCenterNote::where('admin_id',$adminAuth->id)->count();
        else
            $totalRecords = DataCenterNote::count();

        //if($searchValue)
        //$where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        if( request('type') )
            $where.=' AND type="'.request('type').'"';

        if( request('property') )
            $where.=' AND property_id='.request('property');

        if( request('contact') )
            $where.=' AND contact_id='.request('contact');

        if( request('admin') )
            $where.=' AND admin_id='.request('admin');

        if($adminAuth->type>2 && $adminAuth->type!=5 && $adminAuth->type!=6)
            $where.=' AND admin_id='.$adminAuth->id;

        if( request('note_subject') )
            $where.=' AND note_subject ='.request('note_subject');

        $now=date('Y-m-d H:i:s');
        if( request('cancelled') ) {
            $cancelled=request('cancelled');
            if($cancelled==2) {
                $where .= ' AND status =' . $cancelled;
            }

            if( !request('note_subject') )
                $where.=' AND note_subject IN (2,3)';

            if($cancelled==1) {
                $where.=' AND data_center_note.status =1 AND concat(date_at," ",time_at) <="'.$now.'"';
            }
            if($cancelled==3) {
                $where.=' AND data_center_note.status =1 AND concat(date_at," ",time_at) >="'.$now.'"';
            }
        }

        if( request('from_date') )
            $where.=' AND data_center_note.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND data_center_note.created_at <="'.request('to_date').' 23:59:59"';

        #record number with filter

        if($rowperpage=='-1'){
            $query="SELECT data_center_note.*,firstname,lastname FROM data_center_note,admins WHERE data_center_note.admin_id=admins.id ".$where."  ORDER BY ".$columnName." ".$columnSortOrder;
            $Records=DB::select($query);
        }else{
            $query="SELECT data_center_note.*,firstname,lastname FROM data_center_note,admins WHERE data_center_note.admin_id=admins.id ".$where." ORDER BY  data_center_note.".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage;
            $Records=DB::select($query);
        }


        $totalRecordwithFilter=count(DB::select("SELECT data_center_note.*,firstname,lastname FROM data_center_note,admins WHERE data_center_note.admin_id=admins.id ".$where) );

        $obj=[];

        foreach($Records as $row){

            $created_at_html='';
            $contactProperty='';
            $DataCenter=DataCenter::find($row->data_center_id);

            if($adminAuth->type==6){
                $created_at_html='DC-'.$DataCenter->id;
            }else {
                $created_at_html='<a href="/admin/data-center-view/' . $DataCenter->id . '" target="_blank">DC-' . $DataCenter->id . '</a>';
            }

            $obj['note_subject']= NoteSubject[$row->note_subject];
            $obj['data_center_id']= $created_at_html;
            $obj['firstname']= $row->firstname.' '.$row->lastname;
            $obj['date_at']= ($row->date_at) ? \Helper::changeDatetimeFormat( $row->date_at.' '.$row->time_at) : '';

            $obj['note']= ($row->note) ? '<span class="note" data-target="#ViewModal" data-toggle="modal" data-title="'.NoteSubject[$row->note_subject].'" data-desc="'.$row->note.'" >'.\Illuminate\Support\Str::limit(strip_tags($row->note),50).'</span>' : '';

            $obj['created_at']= \Helper::changeDatetimeFormat( $row->created_at);
            $obj['action']='<div class="action d-flex font-medium-3" data-id="'.$row->id.'" data-model="">

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
}
