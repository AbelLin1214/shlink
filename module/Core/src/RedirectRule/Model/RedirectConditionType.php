<?php

namespace Shlinkio\Shlink\Core\RedirectRule\Model;

enum RedirectConditionType: string
{
    case DEVICE = 'device';
    case LANGUAGE = 'language';
    case QUERY_PARAM = 'query-param';
    case IP_ADDRESS = 'ip-address';
    case GEOLOCATION_COUNTRY_CODE = 'geolocation-country-code';
}
