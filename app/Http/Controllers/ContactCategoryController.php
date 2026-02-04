<?php

namespace App\Http\Controllers;

use App\Models\ContactBedroom;
use App\Models\ContactCategory;
use App\Models\ContactCategoryDetail;
use App\Models\ContactCommunity;
use App\Models\ContactMasterProject;
use App\Models\ContactPropertyType;
use Illuminate\Http\Request;

class ContactCategoryController extends Controller
{

    public function Store(Request $request){
        $request->validate([
            'contact'=>'required',
        ]);

        $contact_id=request('contact');
        $cat_id=array_search(request('ContactCategory'), ContactCategory);
        $cat=ContactCategory::create([
            'contact_id'=>$contact_id,
            'cat_id'=>$cat_id,
            'looking_for'=>request('LookingFor'),
            'emirate_id'=>request('Emirate'),
            'p_type'=>request('P_Type'),
            'sale_budget'=>str_replace(',','',request('SaleBudget')),
            'buy_type'=>request('BuyType'),
            'buyer_type'=>request('BuyerType'),
            'number_cheques'=>request('NumberCheques'),
            'move_in_day'=>request('MoveInDay'),
            'agency_name'=>request('AgencyName')
        ]);

        if(request('PropertyType')){
            foreach ( request('PropertyType') as $property_type_id){
                ContactPropertyType::create([
                    'contact_id'=>$contact_id,
                    'cat_id'=>$cat->id,
                    'property_type_id'=>$property_type_id
                ]);
            }
        }

        if(request('MasterProject')){
            foreach ( request('MasterProject') as $master_project_id){
                ContactMasterProject::create([
                    'contact_id'=>$contact_id,
                    'cat_id'=>$cat->id,
                    'master_project_id'=>$master_project_id
                ]);
            }
        }

        if(request('Community')){
            foreach ( request('Community') as $project_id){
                ContactCommunity::create([
                    'contact_id'=>$contact_id,
                    'cat_id'=>$cat->id,
                    'community_id'=>$project_id
                ]);
            }
        }

        if(request('Bedroom')){
            foreach ( request('Bedroom') as $bedroom_id){
                ContactBedroom::create([
                    'contact_id'=>$contact_id,
                    'cat_id'=>$cat->id,
                    'bedroom_id'=>$bedroom_id
                ]);
            }
        }

        return redirect('/admin/contact/view/'.$contact_id);
    }

}
