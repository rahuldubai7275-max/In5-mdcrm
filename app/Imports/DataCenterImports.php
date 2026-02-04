<?php
namespace App\Imports;

use App\Models\DataCenter;
use App\Models\DataCenterUpload;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DataCenterImports implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        // Define how to create a model from the Excel row data

        $master_project= ($row[0])?:'-';
        $project= ($row[1])?:'-';
        $st_cl_fr= ($row[2])?:'-';
        $villa_unit_no= ($row[3])?:'-';
        $bedrooms= ($row[4])?:0;
        $bedrooms= (int)$bedrooms;
        $size= ($row[5])?:0;
        $size= (int)$size;
        $plot_size= ($row[6])?:0;
        $plot_size= (int)$plot_size;
        $name= ($row[7])?:'-';
        $phone_number= ($row[8])?:'-';
        $phone_number_2= ($row[9])?:'-';
        $email= ($row[10])?:'-';
        $nationality= ($row[11])?:'-';

        $DataCenter=DataCenterUpload::where('master_project',$master_project)->
        where('project',$project)->
        where('st_cl_fr',$st_cl_fr)->
        where('villa_unit_no',$villa_unit_no)->
        // where('bedrooms',$bedrooms)->
        // where('size',$size)->
        // where('plot_size',$plot_size)->
        where('name',$name)->
        where('phone_number',$phone_number.'')->
        where('phone_number_2',$phone_number_2.'')->
        where('email',$email)->first();

        if(!$DataCenter) {
            return new DataCenterUpload([
                'master_project' => $master_project,
                'project' => $project,
                'st_cl_fr' => $st_cl_fr,
                'villa_unit_no' => $villa_unit_no,
                'bedrooms' => $bedrooms,
                'size' => $size,
                'plot_size' => $plot_size,
                'name' => $name,
                'phone_number' => $phone_number.'',
                'phone_number_2' => $phone_number_2.'',
                'email' => $email,
                'nationality' => $nationality,
                // Add more columns as needed
            ]);
        }
    }
}
