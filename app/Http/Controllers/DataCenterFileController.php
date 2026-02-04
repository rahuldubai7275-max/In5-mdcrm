<?php

namespace App\Http\Controllers;

use App\Models\DataCenterFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataCenterFileController extends Controller
{
    public function DataCenterFile(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/data-center-file', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Delete(){
        $DataCenterFile = DataCenterFile::find( request('Delete') );
        $DataCenterFile->delete();
        return redirect('/admin/data-center-file');
    }

}

