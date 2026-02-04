<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Bathroom;
use App\Models\Bedroom;
use App\Models\Community;
use App\Models\Contact;
use App\Models\MasterProject;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyController extends Controller
{

    public function Property(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $Property=Property::find(request('id'));
        $SimilarProperties=Property::where('master_project_id','=',$Property->master_project_id)
            ->where('id','!=',request('id'))
            ->take(3)
            ->get();
        return view('/site/property', [
            'pageConfigs' => $pageConfigs,
            'property'=>$Property,
            'SimilarProperties'=>$SimilarProperties
        ]);
    }

    public function ListingProperty(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        $Properties=Property::paginate(10);
        $MinPrice=Property::min('expected_price');
        $MaxPrice=Property::max('expected_price');

        $PropertyTypes=PropertyType::get();
        $MasterProjects=MasterProject::get();
        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();

        return view('/site/property-filter', [
            'pageConfigs' => $pageConfigs,
            'properties'=>$Properties,
            'PropertyTypes'=>$PropertyTypes,
            'MasterProjects'=>$MasterProjects,
            'Bedrooms'=>$Bedrooms,
            'Bathrooms'=>$Bathrooms,
            'MinPrice'=>$MinPrice,
            'MaxPrice'=>$MaxPrice
        ]);
    }

}

