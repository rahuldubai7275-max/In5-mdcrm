<?php
namespace App\Exports;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\ContactPropertyType;
use App\Models\ContactSource;
use App\Models\PropertyType;
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

class ExportContact extends StringValueBinder  implements FromCollection,ShouldAutoSize,WithHeadings,WithColumnFormatting,WithEvents,WithStyles {
    public function collection()
    {

        //$contacts= Contact::get();
        $contactsArray=[];

        $adminAuth=\Auth::guard('admin')->user();
        $where=' WHERE contacts.company_id='.$adminAuth->company_id.' ';
        if(request('contact')=='buyers'){
            $where.=' AND contacts.contact_category="buyer"';

            if($adminAuth->type>2)
                $where.=' AND (client_manager='.$adminAuth->id.' OR client_manager_tow='.$adminAuth->id.' )';
        }

        if( request('first_name') )
            $where.=' AND CONCAT(firstname," ", lastname)  LIKE "%'.request('first_name').'%"';

        if( request('last_name') )
            $where.=' AND lastname LIKE "%'.request('last_name').'%"';

        if( request('email_address') )
            $where.=' AND email LIKE "%'.request('email_address').'%"';

        if( request('contact_categories') ){
            $contact_categories=explode(',',request('contact_categories'));
            $c_cat='';
            $c_cat_arr=[];
            foreach($contact_categories as $category){
                $c_cat.='"'.$category.'",';
                $c_cat_arr[]=array_search ($category, ContactCategory);;
            }
            $where.=' AND (contact_category IN ('.rtrim($c_cat,",").') OR contacts.id IN (select contact_id FROM contact_category WHERE cat_id IN ('.join(',',$c_cat_arr).') ) )';
        }

        if( request('private') ){
            $where.=' AND contacts.private="'.request('private').'"';
            $where .= ' AND contacts.admin_id=' . $adminAuth->id;
        }else{
            $where.=' AND contacts.private=0 ';
        }

        if( request('client_manager') )
            $where.=' AND (client_manager IN ('.request('client_manager').') OR client_manager_tow IN ('.request('client_manager').') )';//$where.=' AND client_manager IN ('.request('client_manager').')';

        if( request('creator') )
            $where.=' AND admin_id IN ('.request('creator').')';

        if( request('finance_status') )
            $where.=' AND buy_type = "'.request('finance_status').'"';

        if( request('contact_number') )
            $where.=' AND main_number LIKE "%'.request('contact_number').'%"';

//        if( request('p_type') )
//            $where.=' AND p_type = "'.request('p_type').'"';

        if( request('property_type') )
            $where.=' AND contact_property_type.property_type_id IN ('.request('property_type').')';

        if( request('master_project') )
            $where.=' AND contact_master_project.master_project_id IN ('.request('master_project').')';

        if( request('community') )
            $where.=' AND contact_community.community_id IN ('.request('community').')';

        if( request('bedroom') )
            $where.=' AND contact_bedroom.bedroom_id IN ('.request('bedroom').')';

        if( request('budget_from') )
            $where.=' AND sale_budget >='.str_replace(',','',request('budget_from'));

        if( request('budget_to') )
            $where.=' AND sale_budget <='.str_replace(',','',request('budget_to'));

        if( request('contact_source') )
            $where.=' AND contact_source IN ('.request('contact_source').')';

        if( request('id') )
            $where.=' AND contacts.id ='.request('id');

        if( request('select_color') ){
            $today = date('Y-m-d');

            $color=request('select_color');
            if($color=="Red"){
                $where.= ' AND last_activity <="'.date('Y-m-d',strtotime($today. "- 30 days") ).'"';
            }

            if($color=="Yellow"){
                $where.= ' AND last_activity BETWEEN "'.date('Y-m-d',strtotime($today. "- 30 days") ).'" AND "'.date('Y-m-d',strtotime($today. "- 15 days") ).'"';
            }

            if($color=="Green"){
                $where.= ' AND last_activity >="'.date('Y-m-d',strtotime($today. "- 15 days") ).'"';
            }
        }

        if($where==' WHERE 1 ' && request('contact')=='contacts') {
            $where .= ' AND contacts.contact_category IN ("buyer","tenant")';
        }

        $query="SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where." ORDER BY contacts.id DESC";
        $Records=DB::select($query);

        foreach ($Records as $row){
            $ContactSource=ContactSource::find( $row->contact_source );
            $ClientManager=Admin::find( $row->client_manager );

            $PropertyTypes=ContactPropertyType::where('contact_id',$row->id)->whereNull('cat_id')->get();
            $PropertyType='';
            foreach ($PropertyTypes as $pt_row){
                $PType=PropertyType::where('id',$pt_row->property_type_id)->first();
                $PropertyType.=$PType->name.',';
            }

            $obj=[];
            $obj['name']=$row->firstname.' '.$row->lastname;
            $obj['phone']=$row->main_number;
            $obj['phone_2']=$row->number_two;
            $obj['email']=$row->email;
            $obj['category']=ucfirst($row->contact_category);
            $obj['property_type']=($PropertyType) ? $PropertyType : 'N/A';
            $obj['budget']=($row->sale_budget) ? number_format($row->sale_budget) : '';
            $obj['source']=($ContactSource) ? $ContactSource->name : '';
            $obj['client_manager']=($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : 'N/A';

            $contactsArray[]=$obj;
        }

        return new Collection($contactsArray);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Phone',
            'Second Number',
            'Email',
            'Category',
            'Property Type',
            'Budget',
            'Source',
            'CM1',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '0',
            'C' => '0',
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $event->sheet->getDelegate()->getStyle('B')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                $event->sheet->getDelegate()->getStyle('F')
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
