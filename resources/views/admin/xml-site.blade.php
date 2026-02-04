<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
@php
    $company=\App\Models\Company::find(1);
@endphp
<Listings>
    @foreach ($response as $row)
        <Listing>
            <count>{{ ($loop->index+1) }}</count>
            <Ad_Type>{{ $row['listing_type'] }}</Ad_Type>
            <Unit_Type>{{ $row['property_type_site'] }}</Unit_Type>
            <Unit_Model></Unit_Model>
            <Primary_View>{{$row['view']}}</Primary_View>
            <Unit_Builtup_Area>{{$row['Property_Size']}}</Unit_Builtup_Area>
            <No_of_Bathroom>{{$row['Bathroom']}}</No_of_Bathroom>
            <Property_Title>{{$row['Property_Title']}}</Property_Title>
            <Website_Title>{{$row['Website_Title']}}</Website_Title>
            <Web_Remarks><![CDATA[{!!   $row['Property_Description'] !!}]]></Web_Remarks>
            <Emirate>{{ $row['city'] }}</Emirate>
            <Community>{{ $row['locality'] }}</Community>
            <Exclusive></Exclusive>
            <Cheques>{{$row['number_cheques']}}</Cheques>
            <Plot_Area>{{$row['plot_size']}}</Plot_Area>
            <Property_Name>{{$row['sub_locality'].' '.$row['tower_name']}}</Property_Name>
            <Property_Ref_No>{{$row['id']}}</Property_Ref_No>
            <Listing_Agent>{{ $row['Listing_Agent'] }}</Listing_Agent>
            <Listing_Agent_Phone>{{ $row['Listing_Agent_Phone'] }}</Listing_Agent_Phone>
            <Listing_Date>{{ $row['Listing_Date'] }}</Listing_Date>
            <Last_Updated>{{ $row['Last_Updated'] }}</Last_Updated>
            <Bedrooms>{{ $row['Bedrooms'] }}</Bedrooms>
            <Listing_Agent_Email>{{ $row['Listing_Agent_Email'] }}</Listing_Agent_Email>
            <price>{!!   $row['price'] !!}</price>
            <Frequency>{{ $row['Rent_Frequency'] }}</Frequency>
            <Unit_Reference_No>{{ $row['id'] }}</Unit_Reference_No>
            <No_of_Rooms>{{ $row['Bedrooms'] }}</No_of_Rooms>
            <Latitude></Latitude>
            <Longitude></Longitude>
            <unit_measure>Sq.Ft.</unit_measure>
            <Featured></Featured>
            <Fitted></Fitted>
            <Images>
                {!!  $row['img_site']  !!}
            </Images>
            <Land_Department_QR_Code><![CDATA[{!!   $row['land_department_qr'] !!}]]></Land_Department_QR_Code>
            <Facilities>
                {!! $row['Facilities'] !!}
            </Facilities>
            <company_name>{{($company)  ?  $company->name  :  ''}}</company_name>
            <Web_Tour></Web_Tour>
            <Threesixty_Tour></Threesixty_Tour>
            <Audio_Tour></Audio_Tour>
            <Virtual_Tour></Virtual_Tour>
            <QR_Code></QR_Code>
            <view360>{{ $row['view360'] }}</view360>
            <Video>{{ $row['Video'] }}</Video>
            <company_logo>{{ $row['parking'] }}</company_logo>
            <Parking>{{ $row['parking'] }}</Parking>
            <PreviewLink>{{ $row['PreviewLink'] }}</PreviewLink>
            <price_on_application>{{ $row['price_on_application'] }}</price_on_application>
            <Off_Plan>{{ $row['Off_Plan'] }}</Off_Plan>
            <permit_number>{{ $row['Permit_Number'] }}</permit_number>
            <dtcm_number>{{ $row['dtcm_number'] }}</dtcm_number>
            <completion_status>{{ $row['status2'] }}</completion_status>
        </Listing>
    @endforeach
</Listings>
