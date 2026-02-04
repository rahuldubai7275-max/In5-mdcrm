<?php
namespace App\Imports;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ContactImports implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row){
        // Define how to create a model from the Excel row data

        $contact_category=($row[0])?:null;
        $firstname=($row[1])?:null;
        $lastname=($row[2])?:null;

        $main_number = $row[3].'';
        $number_two = $row[4].'';

        $email = ($row[5])?:null;

        if($contact_category && $firstname && $lastname) {

            $adminAuth = \Auth::guard('admin')->user();

            $main_number = str_replace("00971", "+971", $main_number);

            $country_code = substr($main_number, 0, 4);

            $mainNumberContact = '';
            $mumberTwoContact = '';
            $emailContact = '';

            if ($country_code == '+971') {
                if ($main_number && $main_number != '+971')
                    $mainNumberContact = DB::select('SELECT * FROM contacts WHERE developer_id IS NULL AND company_id='.$adminAuth->company_id.' AND (main_number="' . $main_number . '" OR number_two="' . $main_number . '") LIMIT 0,1 ');
                else
                    $main_number = null;
            }

            if ($number_two)
                $mumberTwoContact = DB::select('SELECT * FROM contacts WHERE developer_id IS NULL AND company_id='.$adminAuth->company_id.' AND (main_number="' . $number_two . '" OR number_two="' . $number_two . '") LIMIT 0,1 ');

            if ($email)
                $emailContact = DB::select('SELECT * FROM contacts WHERE developer_id IS NULL AND company_id='.$adminAuth->company_id.' AND (email="' . $email . '" OR email_two="' . $email . '") LIMIT 0,1 ');

            if (!$mainNumberContact && !$mumberTwoContact && !$emailContact) {
                return new Contact([
                    'company_id' => $adminAuth->company_id,
                    'admin_id' => $adminAuth->id,
                    'client_manager' => $adminAuth->id,
                    'contact_category' => strtolower($contact_category),
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'main_number' => $main_number,
                    'number_two' => $number_two,
                    'email' => $email,
                    'sale_budget' => $row[6],
                ]);
            }
        }
    }
}
