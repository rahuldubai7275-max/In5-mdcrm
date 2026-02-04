<?php
namespace App\Exports;
use App\Models\Admin;
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

class ExportLead implements FromCollection,ShouldAutoSize,WithHeadings,WithColumnFormatting,WithEvents,WithStyles {
    public function collection()
    {
        //$leads= Lead::get();
        $leadsArray=[];

        $status=[0=>'Open',1=>'Added To Contact',2=>'Close'];

        $adminAuth=\Auth::guard('admin')->user();

        $where=' AND leads.company_id='.$adminAuth->company_id.' ';

        if( request('client_manager') )
            $where.=' AND leads.assign_to="'.request('client_manager').'"';

        if( request('contact_category') )
            $where.=' AND leads.contact_category="'.request('contact_category').'"';

        if( request('source') )
            $where.=' AND leads.source="'.request('source').'"';

        if( request('master_project') )
            $where.=' AND leads.master_project_id ="'.request('master_project').'"';

        if( request('ref_number') )
            $where.=' AND leads.id ="'.request('ref_number').'"';

        if( request('from_date') )
            $where.=' AND leads.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND leads.created_at <="'.request('to_date').' 23:59:59"';

        if( request('private') ){
            $where.=' AND leads.private="'.request('private').'"';
            $where .= ' AND leads.admin_id=' . $adminAuth->id;
        }else{
            $where.=' AND leads.private=0 ';
        }

        if( request('status') )
            $where.=' AND leads.status ='.request('status');
        else
            $where.=' AND leads.status=0';

        $Records=DB::select("SELECT * FROM leads WHERE 1 ".$where." ORDER BY id DESC");

        foreach ($Records as $row){
            $MasterProject=MasterProject::find($row->master_project_id);
            $ClientManager=Admin::find( $row->assign_to );
            $property=Property::find( $row->property_id  );

            $obj=[];
            $obj['name']=$row->name;
            $obj['phone']=$row->mobile_number;
            $obj['email']=$row->email;
            $obj['property']=($property) ? 'LL-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->id : 'N/A';
            $obj['master_project']=($MasterProject) ? $MasterProject->name : 'N/A';
            $obj['category']=ucfirst($row->contact_category);
            $obj['date']=date('d-m-Y',strtotime($row->created_at));
            $obj['client_manager']=($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : 'N/A';
            $obj['status']=$status[$row->status];

            $leadsArray[]=$obj;
        }

        return new Collection($leadsArray);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone',
            'Email',
            'Property Name',
            'Master Project',
            'Category',
            'Date',
            'CM1',
            'Status',
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
