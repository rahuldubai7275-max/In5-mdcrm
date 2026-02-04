<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<list last_update="{{$last_updated}}" listing_count="{{$listing_count}}">
    @foreach ($response as $row)
        <property last_update="{{$row['updated_at']}}">
            <reference_number>{{ $row['id'] }}</reference_number>
            <offering_type>{{ $row['offering_type'] }}</offering_type>
            <permit_number>{{ $row['Permit_Number'] }}</permit_number>
            @if($row['dtcm_number'])<dtcm_permit>{{ $row['dtcm_number'] }}</dtcm_permit>@endif
            <property_type>{{ $row['property_type_pf'] }}</property_type>
            <price_on_application>{{ $row['price_on_application'] }}</price_on_application>
            <price>{!! $row['price']  !!}</price>

            <service_charge>{{$row['service_charge']}}</service_charge>
            @if($row['offering_type']=='RR' || $row['offering_type']=='CR')<cheques>{{$row['number_cheques']}}</cheques>@endif
            <city>{{ $row['city'] }}</city>
            <community>{{ $row['locality'] }}</community>
            <sub_community>{{ $row['sub_locality'] }}</sub_community>
            <property_name>{{ $row['tower_name'] }}</property_name>
            <title_en>{{ $row['Property_Title'] }}</title_en>
            <title_ar>{{ $row['Property_Title_AR'] }}</title_ar>
            <description_en><![CDATA[{!!   $row['Property_Description'] !!}]]></description_en>
            <description_ar><![CDATA[{!! $row['Property_Description_AR'] !!}]]></description_ar>
            <private_amenities>{!!  $row['PF_Features']  !!}</private_amenities>
            {{--<private_amenities>{!!  $row['commercial_amenities']  !!}</private_amenities>--}}
            @if($row['plot_size']>0)<plot_size>{!!  $row['plot_size']  !!}</plot_size>@endif
            <size>{!!  $row['Property_Size']  !!}</size>
            <bedroom>{{ ($row['Bedrooms']=='Studio') ? 0 : ( ($row['Bedrooms']>7) ? '7+' : $row['Bedrooms'] ) }}</bedroom>
            <bathroom>{{  ($row['Bathroom']>7) ? '7+' : $row['Bathroom']  }}</bathroom>

            <agent>
                <id>{{ $row['Listing_Agent_id'] }}</id>
                <name>{{ $row['Listing_Agent'] }}</name>
                <email>{{ $row['Listing_Agent_Email'] }}</email>
                <phone>{{ $row['Listing_Agent_Phone'] }}</phone>
                <photo>{{ $row['Listing_Agent_Photo'] }}</photo>
                <license_no>{{ $row['license_no'] }}</license_no>
                <info>{{ $row['info'] }}</info>
            </agent>
            <build_year>{{ $row['build_year'] }}</build_year>
            <floor>{{ $row['floor'] }}</floor>
            <stories>{{ $row['stories'] }}</stories>
            <parking>{{ $row['parking'] }}</parking>
            <furnished>{{ $row['furnished'] }}</furnished>
            <view360>{{ $row['view360'] }}</view360>
            <photo>
                {!!  $row['photo']  !!}
            </photo>
            <floor_plan>
                {!!  $row['floor_plan']  !!}
            </floor_plan>
            <geopoints>{{ $row['geopoints'] }}</geopoints>
            <title_deed>{{ $row['title_deed'] }}</title_deed>
            <availability_date>{{ $row['availability_date'] }}</availability_date>
            <video_tour_url>{{ $row['Video'] }}</video_tour_url>
            <Developer>{{ $row['Developer'] }}</Developer>
            <project_name>{{ $row['project_name'] }}</project_name>
            <completion_status>{{ $row['completion_status'] }}</completion_status>
        </property>
    @endforeach
</list>
