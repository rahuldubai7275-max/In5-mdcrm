<?php
namespace App\Exports;
use App\Models\Admin;
use App\Models\Community;
use App\Models\DataCenterAccess;
use App\Models\Lead;
use App\Models\MasterProject;
use App\Models\Property;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ExportDataCenter implements FromCollection,ShouldAutoSize,WithHeadings,WithColumnFormatting,WithEvents,WithStyles {
    public function collection()
    {
        $dataArray=[];

        $adminAuth=\Auth::guard('admin')->user();

        $where=' WHERE 1 ';

        if(request('unmatched')){
            $unmatched=request('unmatched');
            if($unmatched==1)
                $where .= ' AND project_id IS NOT NULL';
            if($unmatched==2)
                $where .= ' AND project_id IS NULL';
        }

        $firstWhere='';
        $projects = DataCenterAccess::where('admin_id', $adminAuth->id)->whereNotNull('projects')->pluck('projects')->toArray();
        if ($projects) {
            $firstWhere.=" OR project_id IN (".join(',', $projects).")";
        }
        $master_projects = DataCenterAccess::where('admin_id', $adminAuth->id)->whereNull('projects')->pluck('master_project_id')->toArray();
        if ($master_projects) {
            $firstWhere.=" OR master_project_id IN (".join(',', $master_projects).")";
        }

        if(request('name')){
            $where.=' AND name LIKE "%'.request('name').'%"';
        }

        if(request('phone_number')){
            $where.=' AND (a.phone_number LIKE "%'.request('phone_number').'%" OR a.phone_number_2 LIKE "%'.request('phone_number').'%")';
        }

        if(request('bedroom')){
            $where.=' AND a.bedrooms="'.request('bedroom').'"';
        }

        if(request('bua_from')){
            $where.=' AND a.size>='.request('bua_from');
        }

        if(request('bua_to')){
            $where.=' AND a.size<='.request('bua_to');
        }

        if(request('plot_from')){
            $where.=' AND a.plot_size>='.request('plot_from');
        }

        if(request('plot_to')){
            $where.=' AND a.plot_size<='.request('plot_to');
        }

        if(request('master_project_id')){
            $master_project_id=request('master_project_id');
            $where .= ' AND a.master_project_id=' . $master_project_id;
        }

        if(request('villa_unit_no')){
            $where.=' AND villa_unit_no LIKE "%'.request('villa_unit_no').'%"';
        }

        if(request('ref_id')){
            $where .= ' AND id=' . request('ref_id');
        }

        if(request('assigned_to')){
            $where.=' AND agent_assign='.request('assigned_to');
        }

        if(request('project_id')){
            $where.=' AND a.project_id IN ('.request('project_id').')';
        }

        if(request('status')){
            if(request('status')=='added_to_property') {
                $where .= ' AND added_to_property IS NOT NULL';
                //$where .= ' AND added_to_property_admin='.$adminAuth->id;
            }elseif(request('status')=='added_to_contact') {
                $where .= ' AND added_to_contact IS NOT NULL';
                //$where .= ' AND added_to_contact_admin='.$adminAuth->id;
            }else {
                $status=request('status');
                $where .= ' AND added_to_contact IS NULL AND added_to_property IS NULL AND status=' . $status;
                //if($status!=1 && $status!=4){
                //    $where .= ' AND status_by='.$adminAuth->id;
                //}
            }
        }else{
            $where .= ' AND added_to_contact IS NULL AND added_to_property IS NULL AND status IN (1,4)';
            //$where .= ' AND status_by='.$adminAuth->id;
        }

        if(request('assigned')){
            if(request('assigned')==1)
                $where.=' AND agent_assign IS NOT NULL';
            if(request('assigned')==2)
                $where.=' AND agent_assign IS NULL';
        }


        $Records=DB::select("SELECT * FROM (SELECT * FROM `data_center`  WHERE agent_assign=".$adminAuth->id.$firstWhere." ) a ".$where." ORDER BY id DESC");

        foreach ($Records as $row){
            $master_project='';
            if($row->master_project_id){
                $MProject=MasterProject::find($row->master_project_id);
                $master_project=$MProject->name;
            }else{
                $master_project=$row->master_project;
            }

            $project='';
            if($row->project_id){
                $Community=Community::find($row->project_id);
                $project=$Community->name;
            }else{
                $project=$row->project;
            }

            $obj=[];
            $obj['master_project']=$master_project;
            $obj['project']=$project;
            $obj['st_cl_fr']=$row->st_cl_fr;
            $obj['villa_unit_no']=$row->villa_unit_no;
            $obj['bedrooms']=$row->bedrooms;
            $obj['size']=($row->size) ? number_format($row->size) : '';
            $obj['plot_size']=($row->plot_size) ? number_format($row->plot_size) : '';
            $obj['name']=$row->name;
            $obj['phone_number']=$row->phone_number;
            $obj['phone_number_2']=$row->phone_number_2;
            $obj['email']=$row->email;
            $obj['nationality']=$row->nationality;

            $dataArray[]=$obj;
        }

        return new Collection($dataArray);
    }

    public function headings(): array
    {
        return [
            'Master Project',
            'Project',
            'Cluster / Street / Frond',
            'Unit/Villa Number',
            'Bedrooms',
            'BUA',
            'plot Size',
            'Name',
            'Phone Number',
            'Phone Number 2',
            'Email',
            'Nationality'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('B')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

}
?>
