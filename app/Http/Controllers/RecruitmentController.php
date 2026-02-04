<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Language;
use App\Models\Recruitment;
use App\Models\RecruitmentLanguage;
use App\Models\RecruitmentNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RecruitmentController extends Controller
{
    public function recruitments(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/recruitments', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function recruitmentForm(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/recruitment-form', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function recruitment(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/recruitment",'name'=>"Recruitment"], ['name'=>"Add Recruitment"]
        ];
        $route="recruitment.add";
        $recruitment='';
        return view('/admin/recruitment', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'recruitment'=>$recruitment,
            'route'=>$route
        ]);
    }

    public function GetRecruitment(){
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

        $where=' WHERE recruitment.job_title_id=job_title.id ';

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM recruitment ");
        $totalRecords = $totalRecords[0]->countAll;

//        if($searchValue)
//            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        if( request('job_title') )
            $where.=' AND job_title_id='.request('job_title');

        if( request('education_level') )
            $where.=' AND gender='.request('education_level');

        if( request('nationally') )
            $where.=' AND nationally="'.request('nationally').'"';

        if( request('gender') )
            $where.=' AND gender='.request('gender');

        if( request('name') )
            $where.=' AND CONCAT(first_name," ", last_name) LIKE "%'.request('name').'%"';

        if( request('languages') )
            $where.=' AND recruitment.id IN (SELECT recruitment_id FROM recruitment_language WHERE language_id='.request('languages').')';

        if( request('mobile_number') )
            $where.=' AND mobile_number LIKE "%'.request('mobile_number').'%"';

        if( request('email') )
            $where.=' AND email LIKE "%'.request('email').'%"';

        if( request('from_expected_salary') )
            $where.=' AND expected_salary >='.str_replace(',','',request('from_expected_salary'));

        if( request('to_expected_salary') )
            $where.=' AND expected_salary <='.str_replace(',','',request('to_expected_salary'));

        if( request('from_date') )
            $where.=' AND recruitment.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND recruitment.created_at <="'.request('to_date').' 23:59:59"';

        #record number with filter

        if($rowperpage=='-1'){
            $query="SELECT recruitment.*,job_title.name FROM recruitment,job_title ".$where."  ORDER BY ".$columnName." ".$columnSortOrder;
            $Records=DB::select($query);
        }else{
            $query="SELECT recruitment.*,job_title.name FROM recruitment,job_title ".$where." ORDER BY  ".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage;
            $Records=DB::select($query);
        }

        //$totalRecordwithFilter=0;
        //if($where!=' WHERE 1 ')
            $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM recruitment,job_title ".$where);
            $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;

        $obj=[];
        foreach($Records as $row){
            $RecruitmentLanguage=RecruitmentLanguage::where('recruitment_id',$row->id)->get();
            $language='';
            foreach ($RecruitmentLanguage as $RLang){
                $Language=Language::find($RLang->language_id);
                $language.=$Language->name.', ';
            }

            $obj['first_name']= $row->first_name.' '.$row->last_name;
            $obj['expected_salary']= number_format($row->expected_salary);
            $obj['commission_percent']= $row->commission_percent;
            $obj['mobile_number']= $row->mobile_number;
            $obj['email']= $row->email;
            $obj['name']= $row->name;
            $obj['language']= rtrim($language,', ');

            $obj['created_at']= \Helper::changeDatetimeFormat( $row->created_at);

            $editAction='';
            if($row->admin_id!='')
                $editAction='<a href="/admin/recruitment-edit/'.$row->id.'" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>';

            $obj['action']='<div class="action d-flex font-medium-3" data-id="'.$row->id.'" data-model="'.route('recruitment.delete').'">
                                '.$editAction.'
                                <a href="javascript:void(0)" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
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

    public function store(Request $request){
        $request->validate([
            'first_name'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $Recruitment=Recruitment::create([
            'admin_id'=>$adminAuth->id,
            'job_title_id'=>request('job_title'),
            'first_name'=>request('first_name'),
            'last_name'=>request('last_name'),
            'expected_salary'=>(request('expected_salary')) ? str_replace(',','',request('expected_salary')) : null,
            'commission_percent'=>request('commission_percent'),
            'mobile_number'=>request('mobile_number'),
            'email'=>request('email'),
            'cv'=>request('cv'),
            'gender'=>request('gender'),
            'nationally'=>request('nationally'),
            'education_level'=>request('education_level'),
            'years_of_experience'=>request('years_of_experience'),
            'special_note'=>request('special_note'),
            'starting_date'=>request('starting_date')
        ]);
        if(request('languages')){
            foreach ( request('languages') as $language_id){
                RecruitmentLanguage::create([
                    'recruitment_id'=>$Recruitment->id,
                    'language_id'=>$language_id
                ]);
            }
        }
        return redirect('/admin/recruitment-view/'.$Recruitment->id);
    }

    public function storeForm(Request $request){
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'mobile_number'=>'required',
            'email'=>'required',
            'gender'=>'required',
        ]);

        $Recruitment=Recruitment::create([
            'job_title_id'=>request('job_title'),
            'first_name'=>request('first_name'),
            'last_name'=>request('last_name'),
            'expected_salary'=>(request('expected_salary')) ? str_replace(',','',request('expected_salary')) : null,
            'commission_percent'=>request('commission_percent'),
            'mobile_number'=>request('mobile_number'),
            'email'=>request('email'),
            'cv'=>request('cv'),
            'gender'=>request('gender'),
            'nationally'=>request('nationally'),
            'education_level'=>request('education_level'),
            'years_of_experience'=>request('years_of_experience'),
            'special_note'=>request('special_note'),
            'starting_date'=>request('starting_date')
        ]);
        if(request('languages')){
            foreach ( request('languages') as $language_id){
                RecruitmentLanguage::create([
                    'recruitment_id'=>$Recruitment->id,
                    'language_id'=>$language_id
                ]);
            }
        }

        Session::flash('success','Your information has been registered successfully.');
        return back()->withInput();
    }

    public function details(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/recruitment",'name'=>"Recruitment"], ['name'=>"Edit Recruitment"]
        ];
        $adminAuth=\Auth::guard('admin')->user();
        $recruitment=Recruitment::find(request('id'));
        $route="recruitment.edit";

        $hr_access=\App\Models\SettingAdmin::where('setting_id',16)->pluck('admin_id')->toArray();
        $hr_admin=\App\Models\Admin::where('type',6)->where('status',1)->pluck('id')->toArray();
        if($hr_admin){
            $hr_access=$hr_admin;
        }
        if(($adminAuth->super==1 || in_array($adminAuth->id, $hr_access))){
            return view('/admin/recruitment', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
                'route'=>$route,
                'recruitment' => $recruitment,
            ]);
        }else{
            return redirect('/admin/recruitment');
        }

    }

    public function edit(Request $request){
        $request->validate([
            '_id' => 'required',
        ]);
        $Recruitment=Recruitment::find(request('_id'));

        $Recruitment->job_title_id=request('job_title');
        $Recruitment->first_name=request('first_name');
        $Recruitment->last_name=request('last_name');
        $Recruitment->expected_salary=(request('expected_salary')) ? str_replace(',','',request('expected_salary')) : null;
        $Recruitment->commission_percent=request('commission_percent');
        $Recruitment->mobile_number=request('mobile_number');
        $Recruitment->email=request('email');
        $Recruitment->cv=request('cv');
        $Recruitment->gender=request('gender');
        $Recruitment->nationally=request('nationally');
        $Recruitment->education_level=request('education_level');
        $Recruitment->years_of_experience=request('years_of_experience');
        $Recruitment->special_note=request('special_note');
        $Recruitment->starting_date=request('starting_date');

        RecruitmentLanguage::where('recruitment_id', $Recruitment->id)->delete();
        if(request('languages')){
            foreach ( request('languages') as $language_id){
                RecruitmentLanguage::create([
                    'recruitment_id'=>$Recruitment->id,
                    'language_id'=>$language_id
                ]);
            }
        }

        $Recruitment->save();

        return redirect('/admin/recruitment-view/'.$Recruitment->id);
    }

    public function view(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/recruitment",'name'=>"Recruitment"], ['name'=>"View"]
        ];

        if(request('reminder')){
            $RecruitmentNote=RecruitmentNote::find(request('reminder'));
            $RecruitmentNote->seen=1;
            $RecruitmentNote->save();
        }

        $id=request('id');

        $recruitment = Recruitment::find($id);

        return view('/admin/recruitment-view', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'recruitment' => $recruitment
        ]);
    }

    public function Delete(){
        $Recruitment = Recruitment::find( request('Delete') );
        $Recruitment->delete();
        return redirect('/admin/recruitment');
    }
}
