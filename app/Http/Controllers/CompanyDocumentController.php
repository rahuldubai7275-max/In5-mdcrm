<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyDocumentController extends Controller
{
    public function companyDocuments(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $adminAuth=\Auth::guard('admin')->user();
        $CompanyDocuments=CompanyDocument::where('company_id',$adminAuth->company_id)->where('type',1)->get();
        return view('/admin/company-documents', [
            'pageConfigs' => $pageConfigs,
            'companyDocuments'=>$CompanyDocuments,
            'page_title'=>'Company Documents',
            'page_name'=>'Company Documents',
            'type'=>1
        ]);
    }
    public function agentForms(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $adminAuth=\Auth::guard('admin')->user();
        $CompanyDocuments=CompanyDocument::where('company_id',$adminAuth->company_id)->where('type',2)->get();
        return view('/admin/company-documents', [
            'pageConfigs' => $pageConfigs,
            'companyDocuments'=>$CompanyDocuments,
            'page_title'=>'Agent Forms',
            'page_name'=>'Agent Forms',
            'type'=>2
        ]);
    }
    public function agentForms_sm(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $adminAuth=\Auth::guard('admin')->user();
        $CompanyDocuments=CompanyDocument::where('company_id',$adminAuth->company_id)->where('type',2)->get();
        return view('/admin/company-documents-sm', [
            'pageConfigs' => $pageConfigs,
            'companyDocuments'=>$CompanyDocuments,
            'page_title'=>'Agent Forms',
            'page_name'=>'Agent Forms',
            'type'=>2
        ]);
    }
    public function Store(Request $request){
        $adminAuth=\Auth::guard('admin')->user();
        $type=request('type');
        CompanyDocument::create([
            'company_id' => $adminAuth->company_id,
            'admin_id' => $adminAuth->id,
            'type' => $type,
            'category' => request('category'),
            'name' => request('name'),
            'docname' => request('docname')
        ]);

        if($type==1)
            return redirect('/admin/company-documents');

        if($type==2)
            return redirect('/admin/agent-forms');
    }
    public function Delete(){
        $CompanyDocument = CompanyDocument::find( request('Delete') );
        $type=$CompanyDocument->type;
        Storage::delete('public/images/'.$CompanyDocument->docname);
        $CompanyDocument->delete();

        if($type==1)
            return redirect('/admin/company-documents');

        if($type==2)
            return redirect('/admin/agent-forms');
    }

}
