<?php
/*------------------------------------------------------------------------
# AA User IP and Location
# ------------------------------------------------------------------------
# author    AA Extensions https://aaextensions.com/
# Copyright (C) 2018 AA Extensions. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://aaextensions.com/
# https://open.er-api.com/v6/latest/USD
# https://www.exchangerate-api.com/docs/free
-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$unique_id    = uniqid();

?>

<style type="text/css">
    .uipl-card-lip-info{
        background: <?php echo $uipl_bcolor; ?>;
        color: <?php echo $uipl_fcolor; ?>;
    }

    .uipl-main-container{
        max-width: <?php echo  $uipl_cwidth; ?>px;
    }
</style>

<?php

$client  = @$_SERVER["HTTP_CF_CONNECTING_IP"];
$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$a = @$_SERVER['HTTP_X_FORWARDED'];
$b = @$_SERVER['HTTP_FORWARDED_FOR'];
$c = @$_SERVER['HTTP_FORWARDED'];
$d = @$_SERVER['HTTP_CLIENT_IP'];
$remote  = @$_SERVER['REMOTE_ADDR'];

if(filter_var($client, FILTER_VALIDATE_IP)){
    $ip = $client;
}
elseif(filter_var($forward, FILTER_VALIDATE_IP)){
    $ip = $forward;
}
elseif(filter_var($a, FILTER_VALIDATE_IP)){
    $ip = $a;
}
elseif(filter_var($b, FILTER_VALIDATE_IP)){
    $ip = $b;
}
elseif(filter_var($c, FILTER_VALIDATE_IP)){
    $ip = $c;
}
elseif(filter_var($remote, FILTER_VALIDATE_IP)){
    $ip = $remote;
}
else {
    $ip = '';
}

if($ip != null)
{
    $ip_data = @json_decode(file_get_contents( "http://ip-api.com/json/".$ip));

    //Country Code
    if(isset($ip_data->countryCode)){
        $countryCode_data = $ip_data->countryCode;

        //Showing Flag
        $flag = $modulePath.'flags/'.strtolower($countryCode_data).'.png';
        $flag_data = '<img src="'.$flag.'" style="height:auto!important; width:'.$uipl_fwidth.'px!important;" >';
    } else {
        $countryCode_data = '';
        $flag_data = '';
    }

    //Country
    if(isset($ip_data->country)) {
        $country_data = $ip_data->country;
    } else {
        $country_data = '';
    }

    //Region
    if(isset($ip_data->region)) {
        $region_data = $ip_data->region;
    } else {
        $region_data = '';
    }

    //City
    if(isset($ip_data->city)) {
        $city_data = $ip_data->city;
    } else {
        $city_data = '';
    }

    //Latitude
    if(isset($ip_data->lat)) {
        $lat_data = $ip_data->lat;
    } else {
        $lat_data = '';
    }

    //Longitude
    if(isset($ip_data->lon)) {
        $lon_data = $ip_data->lon;
    } else {
        $lon_data = '';
    }

    //Timezone
    if(isset($ip_data->timezone)) {
        $timezone_data = $ip_data->timezone;
    } else {
        $timezone_data = '';
    }

    //ISP
    if(isset($ip_data->isp)) {
        $isp_data = $ip_data->isp;
    } else {
        $isp_data = '';
    }

    //IP
    if(isset($ip_data->query)) {
        $ip_data = $ip_data->query;
    } else {
        $ip_data = '';
    }

    //Flag

}

?>



<div class="uipl-main-container">
    <div class="uipl-card-lip-info">

        <?php if($uipl_ip==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UIP"); ?> : <?php echo $ip_data; ?> </p>
        <?php endif; ?>

        <?php if($uipl_country==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UCOUNTRY"); ?> : <?php echo $country_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_countrycode==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UCOUNTRYCODE"); ?> : <?php echo $countryCode_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_region==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UREGION"); ?> : <?php echo $region_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_city==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UCITY"); ?> : <?php echo $city_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_latitude==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_ULATITUDE"); ?> : <?php echo $lat_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_longitude==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_ULONGITUDE"); ?> : <?php echo $lon_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_timezone==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UTIMEZONE"); ?> : <?php echo $timezone_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_isp==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UISP"); ?> : <?php echo $isp_data; ?></p>
        <?php endif; ?>

        <?php if($uipl_countryflag==1) : ?>
            <p><?php echo JText::_("MOD_UIPL_UCFLAG"); ?> : <?php echo $flag_data; ?></p>
        <?php endif; ?>

    </div>
</div>

