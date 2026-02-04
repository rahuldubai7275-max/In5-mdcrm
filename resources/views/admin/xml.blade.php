<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<Properties>
    @foreach ($response as $row)
        <Property>
            <Property_Ref_No><![CDATA[{{ $row['id'] }}]]></Property_Ref_No>
            <Property_purpose><![CDATA[{{ $row['listing_type'] }}]]></Property_purpose>
            <Property_Type><![CDATA[{{ $row['property_type'] }}]]></Property_Type>
            <Property_Status><![CDATA[{{ $row['status'] }}]]></Property_Status>
            <City><![CDATA[{{ $row['city'] }}]]></City>
            <Locality><![CDATA[{{ $row['locality'] }}]]></Locality>
            <Sub_Locality><![CDATA[{{ $row['sub_locality'] }}]]></Sub_Locality>
            <Tower_Name><![CDATA[{{ $row['tower_name'] }}]]></Tower_Name>
            <Property_Title><![CDATA[{{ $row['Property_Title'] }}]]></Property_Title>
            <Property_Title_AR><![CDATA[{{ $row['Property_Title_AR'] }}]]></Property_Title_AR>
            <Property_Description><![CDATA[{!!   $row['Property_Description'] !!}]]></Property_Description>
            <Property_Description_AR><![CDATA[{!! $row['Property_Description_AR'] !!}]]></Property_Description_AR>
            <Property_Size><![CDATA[{{ $row['Property_Size'] }}]]></Property_Size>
            <Property_Size_Unit><![CDATA[{{ $row['Property_Size_Unit'] }}]]></Property_Size_Unit>
            <Bedrooms><![CDATA[{{ ($row['Bedrooms']=='Studio') ? '-1' : (($row['Bedrooms']>10) ? '10+' : $row['Bedrooms']) }}]]></Bedrooms>
            <Bathroom><![CDATA[{{ $row['Bathroom'] }}]]></Bathroom>
            <Price><![CDATA[{{ $row['Price_BD'] }}]]></Price>
            <Listing_Agent><![CDATA[{{ $row['Listing_Agent'] }}]]></Listing_Agent>
            <Listing_Agent_Phone><![CDATA[{{ $row['Listing_Agent_Phone'] }}]]></Listing_Agent_Phone>
            <Listing_Agent_Email><![CDATA[{{ $row['Listing_Agent_Email'] }}]]></Listing_Agent_Email>
            <Features>
                {!!  $row['Features']  !!}
            </Features>
            <Images>
                {!!  $row['Images']  !!}
            </Images>
            <Videos>
                <Video>{{ $row['Video'] }}</Video>
            </Videos>
            <Floor_Plans>
                <Floor_Plan>
                    <![CDATA[ ]]>
                </Floor_Plan>
            </Floor_Plans>
            <Last_Updated><![CDATA[{{ $row['Last_Updated'] }}]]></Last_Updated>
            <Permit_Number><![CDATA[{{ $row['Permit_Number'] }}]]></Permit_Number>
            <Rent_Frequency><![CDATA[{{ $row['Rent_Frequency'] }}]]></Rent_Frequency>
            <Furnished><![CDATA[{{ $row['furnished'] }}]]></Furnished>
            <plotArea><![CDATA[{{ $row['plot_size'] }}]]></plotArea>
            <Off_Plan><![CDATA[{{ $row['Off_Plan'] }}]]></Off_Plan>
            <Portals>
                @php
                    $Bayut=\App\Models\PortalProperty::where('portal_id','2')->where('property_id',$row['only_id'])->first();
                    $dubizzle=\App\Models\PortalProperty::where('portal_id','3')->where('property_id',$row['only_id'])->first();
                @endphp
                {!! ($Bayut) ? '<Portal><![CDATA[Bayut]]></Portal>' : '' !!}
                {!! ($dubizzle) ? '<Portal><![CDATA[dubizzle]]></Portal>' : '' !!}
            </Portals>
        </Property>
    @endforeach
</Properties>
