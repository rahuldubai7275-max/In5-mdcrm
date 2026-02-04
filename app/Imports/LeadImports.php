<?php
namespace App\Imports;

use App\Models\Company;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class LeadImports implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row){
        // Define how to create a model from the Excel row data

        $contact_category=($row[0]) ? trim(strtolower($row[0]),' ') : null;
        $name=($row[1])?:null;
        $mobile_number=($row[2])?:null;
        $mobile_number_2=($row[3])?:null;
        $email=($row[4])?:null;
        $budget=($row[5])?:null;

        $adminAuth = \Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        $private=0;

        //if($adminAuth->type==3 || $adminAuth->type==4){
            $assign_to=$adminAuth->id;
        //}

        if($company->private==1 && $assign_to==$adminAuth->id){
            $private=1;
        }

        return new Lead([
            'company_id'=>$adminAuth->company_id,
            'type'=>'manual',
            'admin_id'=>$adminAuth->id,
            'private'=>$private,
            'name'=>$name,
            'mobile_number'=>$mobile_number,
            'mobile_number_2'=>$mobile_number_2,
            'email'=>$email,
            'contact_category'=>$contact_category,
            'source'=>47,
            'budget'=>($budget) ? str_replace(',','',$budget) : null,
            'assign_to'=>$assign_to,
            'assign_time'=>date('Y-m-d H:i:s'),
            'seen'=>($private==1) ? 1:0,
        ]);
    }
}
