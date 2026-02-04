<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>



        .border-color-gold{
            border-color: #EBD2AE !important;
        }

        .color-gold{
            color: #EBD2AE !important;
        }

        .card-icon-box i{
            border: 1px solid;
            border-radius: 50%;
            padding: 5px;
            width: 25px;
            height: 25px;
            text-align: center;
        }

        .mb-10{
            margin-bottom: 10px !important;
        }

        @media print {

            .img-fluid {
                width: 100%;
            }

            .bg-gray{
                background:#F7F7F7;
                -webkit-print-color-adjust: exact;
            }
            .text-gray{
                color:#ACACAC;
                -webkit-print-color-adjust: exact;
            }

            .border-color-gold{
                border: 3px;
                border-color: #EBD2AE !important;

                -webkit-print-color-adjust: exact;
            }

            .border-color-dark{
                border-color: #000 !important;
                -webkit-print-color-adjust: exact;
            }

            .color-gold{
                color: #EBD2AE !important;
                -webkit-print-color-adjust: exact;
            }


            .color-dark{
                color: #000 !important;
                -webkit-print-color-adjust: exact;
            }

            .card-icon-box i{
                border: 1px solid;
                border-radius: 50%;
                padding: 5px;
                width: 25px;
                height: 25px;
                text-align: center;
            }

            .mb-10{
                margin-bottom: 10px !important;
            }
        }

        .property-box{
            display: flex;
            width: 100%;
            margin-bottom: 15px;
        }

        .property-row{
            width: 3%;
            display: flex;
            height: 150px;
            margin-top:65px;
        }

        .property-img{
            width: 25%;
        }

        .property-info{
            display: flex;
            width: 50%
        }

        .property-info-left{
            width: 50%;
        }

        .property-info-right{
            width: 50%;
        }

        .more-box{
            width: 22%;
            height: 150px;
            display: block;
        }

        .more{
            text-decoration: unset;
            border: 1px solid #EBD2AE;
            display:inline-block;
            padding: 10px;
            border-radius: 20px;
            margin-top: 45px;
            margin-left: 20px
        }

        @media (max-width: 575.98px) {
            .property-box{
                display: block !important;
            }

            .property-row{
                display: none !important;
            }

            .property-img{
                width: 100% !important;
            }

            .property-info{
                display: flex;
                width: 100% !important;
            }

            .more-box{
                width: 100% !important;
                height: 40px !important;
            }

            .more{
                text-align: center;
                display: block;
                padding: 10px;
                border-radius: 20px;
                margin-top: 10px;
                margin-left: auto;
                margin-right: auto;
                margin-bottom: 22px;
            }
        }
    </style>
</head>
<body>
<div>{!! $details['body'] !!}</div>
<div style="margin-top: 20px">
    @php
        $agent = Auth::guard('admin')->user();
        $company=\App\Models\Company::find(1);
    @endphp
    @isset($details['properties'])
        @foreach($details['properties'] as $id)
            @php
            $property=\App\Models\Property::find($id);
            $MasterProject=\App\Models\MasterProject::find($property->master_project_id);
            $PropertyType=\App\Models\PropertyType::find($property->property_type_id);
            $Community=\App\Models\Community::find($property->community_id);
            $Bedroom=App\Models\Bedroom::where('id',$property->bedroom_id)->first();

            $pictures=explode(',', $property->pictures);
                $img='';
                if($property->pictures)
                    $img='<img style="width: 100%;border-radius: 15px;height:150px;" src="'.request()->getSchemeAndHttpHost().'/storage/'.$pictures[0].'">';

            $expected_price=0;
            if($property->expected_price){
                $expected_price=$property->expected_price;
                //if($Property->property_type_id==3 || $Property->property_type_id==4 || $Property->property_type_id==6){
                    $mc= $property->bua==0 ? 0 : ($property->expected_price/$property->bua) ;
                //}else{
                //    $mc= $Property->plot_sqft==0 ? 0 : ($Property->expected_price/$Property->plot_sqft);
                //}
            }

            if($property->listing_type_id==2){
                if($property->yearly){
                    $expected_price=$property->yearly;
                }else if($property->monthly){
                    $expected_price=$property->monthly;
                }else if($property->weekly){
                    $expected_price=$property->weekly;
                }else{
                    $expected_price=$property->daily;
                }
            }
            @endphp
            <div class="property-box">
                <div class="property-row"><span>{{($loop->index+1)}}</span></div>
                <div class="property-img">{!! $img !!}</div>
                <div class="property-info">
                    <ul class="property-info-left">
                        <li style="margin-bottom: 10px">FOR {{(($property->listing_type_id==1) ? 'SALE' : 'RENT')}}</li>
                        <li style="margin-bottom: 10px">{{SAMPLE}}-{{(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->id}}</li>
                        <li style="margin-bottom: 10px">{{(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' / '.$Community->name : '')}}</li>
                        <li>{{($PropertyType) ? $PropertyType->name : ''}}</li>
                    </ul>
                    <ul class="property-info-right">
                        <li style="margin-bottom: 10px">No of Beds: {{($Bedroom) ? $Bedroom->name : ''}}</li>
                        <li style="margin-bottom: 10px">BUA: {{number_format($property->bua)}} Sq Ft</li>
                        <li style="margin-bottom: 10px">Plot: {{number_format($property->plot_sqft).' Sq Ft'}}</li>
                        <li>AED{{number_format($expected_price)}}</li>
                    </ul>
                </div>
                <div class="more-box">
                    <a class="more" href="{{request()->getSchemeAndHttpHost()}}/property/brochure/{{ \Helper::idCode($property->id) }}?a={{\Helper::idCode($agent->id)}}">Click for more</a>
                </div>
            </div>
        @endforeach
    @endisset
    @isset($details['DeveloperProject'])
            @php
                $OffPlanProject=$details['DeveloperProject'];
                //$OffPlanProject=\App\Models\OffPlanProject::find($details['DeveloperProject']);
                //$MasterProject=\App\Models\MasterProject::find($OffPlanProject->master_project_id);
                $PropertyType=\App\Models\PropertyType::find($OffPlanProject->property_type_id);
                //$Community=\App\Models\Community::find($OffPlanProject->community_id);

                $pictures=explode(',', $OffPlanProject->pictures);
                    $img='';
                    if($OffPlanProject->pictures)
                        $img='<img style="width: 100%;border-radius: 15px;height:150px;" src="'.env('MD_URL').'/storage/'.$pictures[0].'">';

            @endphp
            <div class="property-box">
                <div class="property-img">{!! $img !!}</div>
                <div class="property-info">
                    <ul class="property-info-left">
                        <li style="margin-bottom: 10px">{{$OffPlanProject->master_project_name . ' / '.$OffPlanProject->project_name}}</li>
                        <li style="margin-bottom: 10px">{{($PropertyType) ? $PropertyType->name : ''}}</li>
                        <li style="margin-bottom: 10px">{{$OffPlanProject->developer->name}}</li>
                        <li>{{(($OffPlanProject->quarter) ? 'Q'.$OffPlanProject->quarter: '').(($OffPlanProject->year) ? ' '.$OffPlanProject->year: '')}}</li>
                    </ul>
                    <ul class="property-info-right">
                    </ul>
                </div>
                <div class="more-box">
                    <a class="more" href="{{request()->getSchemeAndHttpHost()}}/off-plan/brochure/{{ \Helper::idCode($OffPlanProject->id) }}?a={{\Helper::idCode($agent->id)}}">Click for more</a>
                </div>
            </div>
    @endisset

    <br><br>
    <p>Best Regards</p>
        <div style="word-wrap:break-word;">
        <table>
            <tbody>
            <tr>
                <td>
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <p class="MsoNormal"><b><span
                                                        style="font-size:12.5pt;font-family:&quot;Arial&quot;,sans-serif;color:black">{{$agent->firstname.' '.$agent->lastname}}</span></b><span
                                                    style="color:#767171"><br></span><b><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#767171">{{$agent->job_title}}</span></b>
                                            </p></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table>
                        <tbody>
                        <tr style="height:75.7pt">
                            <td>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td><p class="MsoNormal"><a
                                                    href="{{$company->website}}" rel="noreferrer" target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#337ab7;text-decoration:none"><img
                                                            border="0"
                                                            style="max-width: 100%;"
                                                            src="{{request()->getSchemeAndHttpHost()}}/images/{{LOGO}}"></span></a>
                                            </p></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="10" style="width:7.25pt;padding:0in 7.5pt 0in 0in;height:75.7pt"></td>
                            <td width="1" style="width:.7pt;background:#000001;padding:0in 0in 0in 0in;height:75.7pt"></td>
                            <td width="10" style="width:7.25pt;padding:0in 7.5pt 0in 0in;height:75.7pt"></td>
                            <td style="padding:0in 0in 0in 0in;height:75.7pt">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <p class="MsoNormal" style="margin: 0;">
                                                <b><span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:black">M:&nbsp;</span></b>
                                                <a href="tel:{{($agent->main_number) ? $agent->main_number : $agent->mobile_number}}" rel="noreferrer" target="_blank">
                                                    <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:gray;text-decoration:none">{{($agent->main_number) ? $agent->main_number : $agent->mobile_number}}</span>
                                                </a>
                                                <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:black"><br><b>T:&nbsp; </b></span>
                                                <a href="tel:{{$agent->office_tel}}" rel="noreferrer" target="_blank">
                                                    <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:gray;text-decoration:none">{{$agent->office_tel}}</span>
                                                </a>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="MsoNormal" style="margin: 0;">
                                                <span style="display: flex">
                                                    <b><span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:black">E:&nbsp; </span></b><a
                                                    href="mailto:{{$agent->email}}" rel="noreferrer" target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:gray;text-decoration:none">{{$agent->email}}</span></a>
                                                    <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:black">

                                                    </span>
                                                </span>
                                                <span style="display: flex">
                                                <b><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#444444">W</span></b><b><span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:black">:&nbsp; </span></b><a
                                                    href="{{$company->website}}" rel="noreferrer" target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:gray;text-decoration:none">{{$company->website}}</span></a>
                                                    </span>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr style="height:.4in">
                                        <td width="167" valign="top" style="width:125.0pt;padding:0in 0in 0in 0in;height:.4in">
                                            <p class="MsoNormal" style="margin: 0;">
                                                <b><span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#444444">ORN: </span></b>
                                                <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:gray">{{$company->rera_orn}}</span>
                                            </p>
                                            <p class="MsoNormal" style="margin: 0;">
                                                <a href="{{$company->facebook}}" rel="noreferrer" target="_blank">
                                                    <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#337ab7;text-decoration:none">
                                                        <img border="0" width="19" height="19" src="{{request()->getSchemeAndHttpHost()}}/images/facebook-logo.png" style="width:.1979in;height:.1979in">
                                                    </span>
                                                </a>
                                                <span style="color:black"> </span><a
                                                    href="{{$company->instagram}}" rel="noreferrer"
                                                    target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#337ab7;text-decoration:none"><img
                                                            border="0" width="19" height="19" src="{{request()->getSchemeAndHttpHost()}}/images/instagram-logo.png"
                                                            style="width:.1979in;height:.1979in"></span></a><span
                                                    style="color:black">&nbsp;</span><a
                                                    href="{{$company->tiktok}}" rel="noreferrer" target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#337ab7;text-decoration:none"><img
                                                            border="0" width="19" height="19" src="{{request()->getSchemeAndHttpHost()}}/images/tiktok-logo.png"
                                                            style="width:.1979in;height:.1979in"></span></a><span
                                                    style="color:black">&nbsp;</span><a
                                                    href="{{$company->linkedin}}"
                                                    rel="noreferrer" target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#337ab7;text-decoration:none"><img
                                                            border="0" width="19" height="19" src="{{request()->getSchemeAndHttpHost()}}/images/linkedin-logo.png"
                                                            style="width:.1979in;height:.1979in"></span></a><span
                                                    style="color:black">&nbsp;</span><a
                                                    href="{{$company->youtube}}"
                                                    rel="noreferrer" target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#337ab7;text-decoration:none"><img
                                                            border="0" width="19" height="19" src="{{request()->getSchemeAndHttpHost()}}/images/youtube-logo.png"
                                                            style="width:.1979in;height:.1979in"></span></a><span
                                                    style="color:black">&nbsp;</span><a href="https://wa.me/{{($agent->main_number) ? $agent->main_number : $agent->mobile_number}}"
                                                                                        rel="noreferrer" target="_blank"><span
                                                        style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:#337ab7;text-decoration:none"><img
                                                            border="0" width="19" height="19" src="{{request()->getSchemeAndHttpHost()}}/images/whatsapp-logo.png"
                                                            style="width:.1979in;height:.1979in"></span></a>
                                            </p></td>
                                    </tr>
                                    <tr>
                                        <td width="167" valign="top" style="width:125.0pt;padding:0in 0in 0in 0in"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="359" style="width:269.35pt;padding:0in 0in 0in 0in"></td>
            </tr>
            </tbody>
        </table>
        </div>
        <p class="MsoNormal"><a href="javascript:void(0);" rel="noreferrer" target="_blank"><span style="font-size:10.0pt;font-family:&quot;Arial&quot;,sans-serif;color:gray;text-decoration:none">{{$company->address}}</span></a></p>
{{--    </div>--}}
</div>
</body>
</html>
