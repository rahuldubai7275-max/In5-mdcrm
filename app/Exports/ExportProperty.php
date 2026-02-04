<?php
namespace App\Exports;
use App\Models\Admin;
use App\Models\Bedroom;
use App\Models\ClusterStreet;
use App\Models\Community;
use App\Models\Contact;
use App\Models\MasterProject;
use App\Models\Property;
use App\Models\VillaType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
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

class ExportProperty implements FromCollection,ShouldAutoSize,WithHeadings,WithColumnFormatting,WithEvents,WithStyles {
    public function collection()
    {
//        $properties= Property::get();
        $propertiesArray=[];

        $adminAuth=\Auth::guard('admin')->user();

        $where=' WHERE property.company_id='.$adminAuth->company_id.' ';

        if(request('property')=='new_listing'){
            $d30before= date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 10 days") );
            $where=',property_status_history WHERE property.id=property_status_history.property_id AND property_status_history.status=1 AND property_status_history.created_at>="'.$d30before.'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='rfl'){
            $where.=' AND property.status=11';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='unlisted'){
            $where.=' AND property.status=2';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='listing'){
            $where.=' AND property.status=1';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='ma'){
            $where.=' AND property.status=4';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        $today = date('Y-m-d');
        if(request('property')=='30-15'){
            $where.=' AND property.expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ 15 days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ 30 days")).'" ';
        }

        $today = date('Y-m-d');
        if(request('property')=='15-7'){
            $where.=' AND property.expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ 7 days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ 15 days")).'" ';
        }

        $today = date('Y-m-d');
        if(request('property')=='7-0'){
            $where.=' AND property.expiration_date BETWEEN "'.$today.'"  AND "'.date('Y-m-d',strtotime($today. "+ 7 days")).'" ';
        }

        if(request('property')=='RL'){
            $where.=' AND property.status=11';
        }

        if( request('listing') )
            $where.=' AND listing_type_id="'.request('listing').'"';

        if( request('status') )
            $where.=' AND status IN ('.request('status').')';

        if( request('type') )
            $where.=' AND property.type = '.request('type');

        if( request('property_type') )
            $where.=' AND property_type_id IN ('.request('property_type').')';

        if( request('creator') )
            $where.=' AND admin_id IN ('.request('creator').')';

        if( request('client_manager') )
            $where.=' AND (client_manager_id IN ('.request('client_manager').') OR client_manager2_id IN ('.request('client_manager').') )';//$where.=' AND client_manager_id IN ('.request('client_manager').')';

        if( request('master_project') )
            $where.=' AND master_project_id IN ('.request('master_project').')';

        if( request('community') )
            $where.=' AND community_id IN ('.request('community').')';

        if( request('bedrooms') )
            $where.=' AND bedroom_id IN ('.request('bedrooms').')';

        if( request('bathrooms') )
            $where.=' AND bathroom_id IN ('.request('bathrooms').')';

        if( request('unit_villa_number') )
            $where.=' AND villa_number="'.request('unit_villa_number').'"';

        if( request('off_plan') )
            $where.=' AND off_plan="'.request('off_plan').'"';

        if( request('property_management') )
            $where.=' AND property_management="'.request('property_management').'"';

        if( request('rera_permit') )
            $where.=' AND rera_permit="'.request('rera_permit').'"';

        if(request('listing')!=2){
            if( request('from_price') )
                $where.=' AND expected_price >='.str_replace(',','',request('from_price'));

            if( request('to_price') )
                $where.=' AND expected_price <='.str_replace(',','',request('to_price'));
        }else{
            if(request('rent_price')){
                $rent_price_field=request('rent_price');
            }else{
                $rent_price_field='yearly';
            }

            if( request('from_price') )
                $where.=' AND '.$rent_price_field.' >='.str_replace(',','',request('from_price'));

            if( request('to_price') )
                $where.=' AND '.$rent_price_field.' <='.str_replace(',','',request('to_price'));
        }

        if( request('id') )
            $where.=' AND id ='.request('id');


        if($where==' WHERE 1 '  && request('property')=='properties') {
            //$where .= ' AND status IN (1,2)';
            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id .')';
        }


        $Records=DB::select("SELECT DISTINCT property.* FROM `property` ".$where." ORDER BY  property.id DESC");


        foreach ($Records as $row){
            $MasterProject=MasterProject::find($row->master_project_id);
            $Community=Community::find($row->community_id);
            $ClusterStreet=ClusterStreet::find($row->cluster_street_id);
            $VillaType=VillaType::find($row->villa_type_id);
            $Bedroom=Bedroom::find($row->bedroom_id);
            $Contact=Contact::find($row->contact_id);
            $ClientManager=Admin::find( $row->client_manager_id );

            $expected_price=($row->expected_price) ? number_format($row->expected_price) : '';

            if($row->listing_type_id==2){

                if($row->yearly){
                    $rent_price=$row->yearly;
                }else if($row->monthly){
                    $rent_price=$row->monthly;
                }else if($row->weekly){
                    $rent_price=$row->weekly;
                }else{
                    $rent_price=$row->daily;
                }

                $expected_price=number_format($rent_price);
            }

            $obj=[];
            $obj['master_project']=($MasterProject) ? $MasterProject->name : 'N/A';
            $obj['community']=($Community) ? $Community->name : 'N/A';
            $obj['cluster_street']=($ClusterStreet) ? $ClusterStreet->name : 'N/A';
            $obj['villa_number']=$row->villa_number;
            $obj['type']=($VillaType) ? $VillaType->name : 'N/A';
            $obj['bedroom']=($Bedroom) ? $Bedroom->name : 'N/A';
            $obj['price']=$expected_price;
            $obj['owner']=($Contact) ? $Contact->firstname.' '.$Contact->lastname : 'N/A';
            $obj['phone']=($Contact) ? $Contact->main_number : 'N/A';
            $obj['email']=($Contact) ? $Contact->email : 'N/A';
            $obj['client_manager']=($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : 'N/A';

            $propertiesArray[]=$obj;
        }

        return new Collection($propertiesArray);
    }

    public function headings(): array
    {
        return [
            'Master Project',
            'Project',
            'Street / Cluster',
            'Villa / Unit No',
            'Type',
            'Bedrooms',
            'Price',
            'Owner Name',
            'Phone',
            'Email',
            'CM1',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $event->sheet->getDelegate()->getStyle('C:G')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                $event->sheet->getDelegate()->getStyle('I')
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
