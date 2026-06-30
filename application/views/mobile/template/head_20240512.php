<?php $latlong = ''; ?>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
<meta name='robots' content='noindex,nofollow' />

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

<?php
if (isset($shortcut_image1) && file_exists('images/touch_icons/' . $shortcut_image1)) {
?>
    <link rel="apple-touch-startup-image" href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image1 . '?' . rand(111111, 9999999); ?>">
    <link href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image1; ?>" rel="apple-touch-icon" sizes="180x180" />
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image1; ?>">
<?php
} ?>

<?php
if (isset($shortcut_image2) && file_exists('images/touch_icons/' . $shortcut_image2)) {
?>
    <link href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image2 . '?' . rand(111111, 9999999); ?>" rel="icon" sizes="192x192" />
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo url::base(); ?>images/touch_icons/<?php echo $shortcut_image2 . '?' . rand(111111, 9999999); ?>">
<?php
} ?>
<?php
if ($stylesheets) :
    foreach ($stylesheets as $stylesheet) :
        echo (isset($stylesheet['ie_only'])) ? "<!--[if IE]>\n\t\t" : '';
        echo html::style($stylesheet['href'], array('media' => $stylesheet['media']), false);
        echo (isset($stylesheet['ie_only'])) ? "<![endif]-->\n\t\t" : '';
    endforeach;
endif;

if ($javascripts) :
    foreach ($javascripts as $javascript) :
        echo html::script($javascript, null, false) . "\t\t";
    endforeach;
endif;
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-167618191-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'UA-167618191-1');
</script>

<?php
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

function is_bot($user_agent)
{
    $botRegexPattern = "(googlebot\/|Googlebot\-Mobile|Googlebot\-Image|Google favicon|Mediapartners\-Google|bingbot|slurp|java|wget|curl|Commons\-HttpClient|Python\-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST\-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub\.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum\.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips\-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail\.RU_Bot|discobot|heritrix|findthatfile|europarchive\.org|NerdByNature\.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb\-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web\-archive\-net\.com\.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks\-robot|it2media\-domain\-crawler|ip\-web\-crawler\.com|siteexplorer\.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki\-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e\.net|GrapeshotCrawler|urlappendbot|brainobot|fr\-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf\.fr_bot|A6\-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive\.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j\-asr|Domain Re\-Animator Bot|AddThis|YisouSpider|BLEXBot|YandexBot|SurdotlyBot|AwarioRssBot|FeedlyBot|Barkrowler|Gluten Free Crawler|Cliqzbot)";

    return preg_match("/{$botRegexPattern}/", $user_agent);
}

$data = array();
if (!is_bot($_SERVER['HTTP_USER_AGENT'])) {

    $xml = file_get_contents('https://api.ipgeolocation.io/ipgeo?ip=' . getRealIpAddr() . '&apiKey=5179bfaee22047ebbe2dbfe70682125d');

    $xml = json_decode($xml);

    $statename = $xml->state_prov;
    $coutrycode = $xml->country_code2;
    $cityname = $xml->city;
    $latitude = $xml->latitude;
    $longitude = $xml->longitude;
    $ip = $xml->ip;
    $country = $xml->country_name;
    $timezone = $xml->time_zone->name;


    // $s[5] = $xml->state_prov;
    // $s[10] = $xml->country_code2;
    // $s[4] = $xml->city;
    // $s[16] = $xml->latitude;
    // $s[17] = $xml->longitude;
    // $s[0] = $xml->ip;
    // $s[11] = $xml->country_name;
    // $s[19] = $xml->time_zone->name;

    // $xml = simplexml_load_file('http://www.geoplugin.net/xml.gp?ip=' . getRealIpAddr());

    // foreach ($xml as $key => $value) {
    //     array_push($data, $value);
    // }
}
?>
<?php
// $s = $data;

$last_part = substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1);

if (is_numeric($last_part) || true) {
?>
    <script type="text/javascript">
        function getMobileOperatingSystem() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;

            // Windows Phone must come first because its UA also contains "Android"
            if (/windows phone/i.test(userAgent)) {
                return "Windows Phone";
            }

            if (/android/i.test(userAgent)) {
                return "Android";
            }

            // iOS detection from: http://stackoverflow.com/a/9039885/177710
            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                return "iOS";
            }

            return "unknown";
        }


        var browser = '';
        var browserVersion = 0;
        if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
            browser = 'Opera';
        } else if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
            browser = 'MSIE';
        } else if (/Navigator[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
            browser = 'Netscape';
        } else if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
            browser = 'Chrome';
        } else if (/Safari[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
            browser = 'Safari';
            /Version[\/\s](\d+\.\d+)/.test(navigator.userAgent);
            browserVersion = new Number(RegExp.$1);
        } else if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
            browser = 'Firefox';
        }
        if (browserVersion === 0) {
            browserVersion = parseFloat(new Number(RegExp.$1));
        }
        var browsername = browser;
        var version = browserVersion;

        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        var element = document.getElementById('text');
        if (isMobile) {
            var devics = "Mobile";
            var ssmobilesystems = getMobileOperatingSystem();

        } else {

            var OSName = "Unknown OS";
            if (navigator.appVersion.indexOf("Win") != -1) OSName = "Windows";
            if (navigator.appVersion.indexOf("Mac") != -1) OSName = "MacOS";
            if (navigator.appVersion.indexOf("X11") != -1) OSName = "UNIX";
            if (navigator.appVersion.indexOf("Linux") != -1) OSName = "Linux";
            var devics = "Desktop";
            var mobilesystems = OSName;

        }
        const userAgent = navigator.userAgent.toLowerCase();
        const isTablet = /(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk|(puffin(?!.*(IP|AP|WP))))/.test(userAgent);
        if (isTablet == false) {
            //alert("false");
        } else {
            //alert("true");
            var devics = "tablet";

        }

        //details info browser
        (function(window) {
            {
                var unknown = '-';

                // screen
                var screenSize = '';
                if (screen.width) {
                    width = (screen.width) ? screen.width : '';
                    height = (screen.height) ? screen.height : '';
                    screenSize += '' + width + " x " + height;
                }

                // browser
                var nVer = navigator.appVersion;
                var nAgt = navigator.userAgent;
                var browser = navigator.appName;
                var version = '' + parseFloat(navigator.appVersion);
                var majorVersion = parseInt(navigator.appVersion, 10);
                var nameOffset, verOffset, ix;

                // Opera
                if ((verOffset = nAgt.indexOf('Opera')) != -1) {
                    browser = 'Opera';
                    version = nAgt.substring(verOffset + 6);
                    if ((verOffset = nAgt.indexOf('Version')) != -1) {
                        version = nAgt.substring(verOffset + 8);
                    }
                }
                // Opera Next
                if ((verOffset = nAgt.indexOf('OPR')) != -1) {
                    browser = 'Opera';
                    version = nAgt.substring(verOffset + 4);
                }
                // Edge
                else if ((verOffset = nAgt.indexOf('Edge')) != -1) {
                    browser = 'Microsoft Edge';
                    version = nAgt.substring(verOffset + 5);
                }
                // MSIE
                else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
                    browser = 'Microsoft Internet Explorer';
                    version = nAgt.substring(verOffset + 5);
                }
                // Chrome
                else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
                    browser = 'Chrome';
                    version = nAgt.substring(verOffset + 7);
                }
                // Safari
                else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
                    browser = 'Safari';
                    version = nAgt.substring(verOffset + 7);
                    if ((verOffset = nAgt.indexOf('Version')) != -1) {
                        version = nAgt.substring(verOffset + 8);
                    }
                }
                // Firefox
                else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
                    browser = 'Firefox';
                    version = nAgt.substring(verOffset + 8);
                }
                // MSIE 11+
                else if (nAgt.indexOf('Trident/') != -1) {
                    browser = 'Microsoft Internet Explorer';
                    version = nAgt.substring(nAgt.indexOf('rv:') + 3);
                }
                // Other browsers
                else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
                    browser = nAgt.substring(nameOffset, verOffset);
                    version = nAgt.substring(verOffset + 1);
                    if (browser.toLowerCase() == browser.toUpperCase()) {
                        browser = navigator.appName;
                    }
                }
                // trim the version string
                if ((ix = version.indexOf(';')) != -1) version = version.substring(0, ix);
                if ((ix = version.indexOf(' ')) != -1) version = version.substring(0, ix);
                if ((ix = version.indexOf(')')) != -1) version = version.substring(0, ix);

                majorVersion = parseInt('' + version, 10);
                if (isNaN(majorVersion)) {
                    version = '' + parseFloat(navigator.appVersion);
                    majorVersion = parseInt(navigator.appVersion, 10);
                }

                // mobile version
                var mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);

                // cookie
                var cookieEnabled = (navigator.cookieEnabled) ? true : false;

                if (typeof navigator.cookieEnabled == 'undefined' && !cookieEnabled) {
                    document.cookie = 'testcookie';
                    cookieEnabled = (document.cookie.indexOf('testcookie') != -1) ? true : false;
                }

                // system
                var os = unknown;
                var clientStrings = [{
                        s: 'Windows 10',
                        r: /(Windows 10.0|Windows NT 10.0)/
                    },
                    {
                        s: 'Windows 8.1',
                        r: /(Windows 8.1|Windows NT 6.3)/
                    },
                    {
                        s: 'Windows 8',
                        r: /(Windows 8|Windows NT 6.2)/
                    },
                    {
                        s: 'Windows 7',
                        r: /(Windows 7|Windows NT 6.1)/
                    },
                    {
                        s: 'Windows Vista',
                        r: /Windows NT 6.0/
                    },
                    {
                        s: 'Windows Server 2003',
                        r: /Windows NT 5.2/
                    },
                    {
                        s: 'Windows XP',
                        r: /(Windows NT 5.1|Windows XP)/
                    },
                    {
                        s: 'Windows 2000',
                        r: /(Windows NT 5.0|Windows 2000)/
                    },
                    {
                        s: 'Windows ME',
                        r: /(Win 9x 4.90|Windows ME)/
                    },
                    {
                        s: 'Windows 98',
                        r: /(Windows 98|Win98)/
                    },
                    {
                        s: 'Windows 95',
                        r: /(Windows 95|Win95|Windows_95)/
                    },
                    {
                        s: 'Windows NT 4.0',
                        r: /(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/
                    },
                    {
                        s: 'Windows CE',
                        r: /Windows CE/
                    },
                    {
                        s: 'Windows 3.11',
                        r: /Win16/
                    },
                    {
                        s: 'Android',
                        r: /Android/
                    },
                    {
                        s: 'Open BSD',
                        r: /OpenBSD/
                    },
                    {
                        s: 'Sun OS',
                        r: /SunOS/
                    },
                    {
                        s: 'Chrome OS',
                        r: /CrOS/
                    },
                    {
                        s: 'Linux',
                        r: /(Linux|X11(?!.*CrOS))/
                    },
                    {
                        s: 'iOS',
                        r: /(iPhone|iPad|iPod)/
                    },
                    {
                        s: 'Mac OS X',
                        r: /Mac OS X/
                    },
                    {
                        s: 'Mac OS',
                        r: /(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/
                    },
                    {
                        s: 'QNX',
                        r: /QNX/
                    },
                    {
                        s: 'UNIX',
                        r: /UNIX/
                    },
                    {
                        s: 'BeOS',
                        r: /BeOS/
                    },
                    {
                        s: 'OS/2',
                        r: /OS\/2/
                    },
                    {
                        s: 'Search Bot',
                        r: /(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/
                    }
                ];
                for (var id in clientStrings) {
                    var cs = clientStrings[id];
                    if (cs.r.test(nAgt)) {
                        os = cs.s;
                        break;
                    }
                }

                var osVersion = unknown;

                if (/Windows/.test(os)) {
                    osVersion = /Windows (.*)/.exec(os)[1];
                    os = 'Windows';
                }

                switch (os) {
                    case 'Mac OS X':
                        osVersion = /Mac OS X (10[\.\_\d]+)/.exec(nAgt)[1];
                        break;

                    case 'Android':
                        osVersion = /Android ([\.\_\d]+)/.exec(nAgt)[1];
                        break;

                    case 'iOS':
                        osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
                        osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);
                        break;
                }

                // flash (you'll need to include swfobject)
                var flashVersion = 'no check';
                if (typeof swfobject != 'undefined') {
                    var fv = swfobject.getFlashPlayerVersion();
                    if (fv.major > 0) {
                        flashVersion = fv.major + '.' + fv.minor + ' r' + fv.release;
                    } else {
                        flashVersion = unknown;
                    }
                }
            }

            window.jscd = {
                screen: screenSize,
                browser: browser,
                browserVersion: version,
                browserMajorVersion: majorVersion,
                mobile: mobile,
                os: os,
                osVersion: osVersion,
                cookies: cookieEnabled,
                flashVersion: flashVersion
            };
        }(this));
        var OS = jscd.os;
        var osversion = jscd.osVersion;
        var browserextraversion = jscd.browser;
        var broversion = jscd.browserMajorVersion;
        var normalbversion = jscd.browserVersion;
        var mobiledevics = jscd.mobile;
        var screensize = jscd.screen;
        var fullagent_version = navigator.userAgent;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("cartsuccess").innerHTML = this.responseText;
                setTimeout(function() {}, 2000);
            }
        };

        /*
        setTimeout(function() {

            if (apiCallResponse.length > 0 || allowLocation == false) {

                // var postalCodeAndStateName = googleApiResponse[5].trim().split(' ');

                // if (allowLocation) {
                //     var googleApiResponse = apiCallResponse[0].split(',');

                //     var statename = "";
                //     var countrycodes = "";
                //     if (typeof googleApiResponse[5] != 'undefined') {
                //         var postalCodeAndStateName = googleApiResponse[5].trim().split(' ');
                //         statename = postalCodeAndStateName[0];
                //         countrycodes = postalCodeAndStateName[1];;
                //     } else {
                //         statename = "<?php echo $s[5]; ?>";
                //         countrycodes = "<?php echo $s[10]; ?>";
                //     }
                //     var latitude = latitudeAndlongitude[0].lat;
                //     var longitude = latitudeAndlongitude[0].lng;
                //     var street_address = googleApiResponse[0] + ',' + googleApiResponse[1] + ',' + googleApiResponse[2];
                //     var city_name = googleApiResponse[4];
                //     var countrycode = countrycodes;
                // } else {

                //     statename = "<?php echo $s[5]; ?>";
                //     countrycodes = "<?php echo $s[10]; ?>";
                //     var city_name = "<?php echo $s[4]; ?>";
                //     var latitude = "<?php echo $s[16]; ?>";
                //     var longitude = "<?php echo $s[17]; ?>";

                // }


                // var itemId = "<?php echo $last_part; ?>";
                // var itemid = itemId.split("?")[0];


                // var ipaddress = "<?php echo $s[0]; ?>";
                // var country = "<?php echo $s[11]; ?>";
                // var timezone = "<?php echo $s[19]; ?>";
                // var eventid = "<?php echo mt_rand(); ?>";


                // var itemid = "<?php echo substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1); ?>";
                // itemid = itemid.split("?")[0];

                // var url = "<?php echo url::base(); ?>server_google/match_data?scanhitid=" + <?php echo mt_rand(); ?> + "&id_item=" + itemid + "&country_code=" + countrycode + "&country_name=" + country + "&region_name=" + statename + "&city_name=" + street_address + "," + city_name + "&latitude=" + latitude + "&longitude=" + longitude + "&ip_add=" + ipaddress + "&timezone=" + timezone + "&id_browsercat=" + browserextraversion + "&browser_version=" + normalbversion + "&id_platformcat=" + mobilesystems + "&id_devicecat=" + devics + "&screensize=" + screensize;

                // var parent_data = window.parent.document.getElementById('request_uri');
                // var has_cookie = false;
                // document.cookie.split(';').forEach(function(el) {
                //     var cookie = el.split('=');
                //     $(cookie).each(function(key, value) {
                //         if (value.trim() == 'form_save_cookie') {
                //             has_cookie = true;
                //         }
                //     });
                // });
                // $temp_var = "";
                // var has_cookie = "<?php echo !empty($temp_var) ? $temp_var : '' ?>";

                // if (parent_data == null && has_cookie == "") {
                //     <?php setcookie('form_save_cookie', null, time() - 3600, "/"); ?>
                //     $.ajax({
                //         url: url,
                //         type: 'GET',
                //         dataType: 'json',
                //         contentType: "application/json",
                //         success: function(res) {
                //             console.log('success');
                //         },
                //         error: function(error) {
                //             console.error(error);
                //         }
                //     })
                // }


            }

        }, 2500);*/
    </script>


    <div id="cartsuccess"></div>
<?php
} else { //echo "not numeric";
}
?>
<style>
    .remove_image {
        float: left;
    }
</style>
<script>
    $(function() {
        if (window.File && window.FileList && window.FileReader) {
            $(document).on('change', ".multiple_file_upload", function(e) {

                var element = $(this);
                var file_data = $(this).prop('files');
                console.log(file_data);
                var question_id = $(this).data('question_id');

                var form = document.getElementById('frm');
                var formData = new FormData();
                var files = e.target.files,
                    filesLength = files.length;

                for (let index = 0; index < filesLength; index++) {
                    var fname = file_data[index].name;
                    var re = /(\.jpg|\.jpeg|\.bmp|\.gif|\.png|\.doc|\.docx|\.pdf|\.xls|\.xlsx)$/i;
                    if (!re.exec(fname)) {
                        alert("Allow only image,pdf,document files");
                        return false;
                    }
                    formData.append('hwms_question_' + question_id + '[]', file_data[index]);
                }

                $('#progress_bar_mobile').show();


                $.ajax({
                    type: 'post',
                    url: "<?php echo url::base(); ?>" + 'swmsfileuploader/upload_swms_multiple_file_upload',
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData,
                    success: function(res) {
                        $('#progress_bar_mobile').hide();

                        $.each(res[question_id], function(key, val) {

                            var html = $("<span class=\"pip\">" +
                                "<input type='hidden' name='hwms_uploaded_file_" + question_id + "[]' value=" + val.original_name + "><img class=\"imageThumb\" src=\"" + val.display_icon + "\"  height='75' width='75'>" +
                                "<br/><b  class=\"remove_image\" data-image-name='" + val.original_name + "' style='color:red;cursor:pointer'>X</b>" +
                                "</span>"); //.appendTo($(this).parent().next('row'));

                            $(html).appendTo(element.parent().next());
                            // $("<span class=\"pip\">" +
                            // "<input type='hidden' name='hwms_uploaded_file_"+question_id+"[]' value="+val+"><img class=\"imageThumb\" src=\"" + val + "\"  height='75' width='75'>" +
                            // "<br/><b class=\"remove_image\" data-image-name='"+val+"' style='color:red'>X</b>" +
                            // "</span>").appendTo(".upload_file_preview_"+question_id);
                        });
                    }
                });
            });
        } else {
            alert("Your browser doesn't support to File API")
        }

        $(document).on('click', '.remove_image', function() {
            $(this).parent(".pip").remove();
            var image_name = $(this).data('image-name');
            $.ajax({
                url: "<?php echo url::base(); ?>" + 'swmsfileuploader/remove_swms_multiple_uploaded_file',
                type: 'post',
                data: {
                    'file_name': image_name
                },
                success: function() {

                },
                error: function() {

                }
            });
        });
    });
</script>
<script type="text/javascript">
    var apiCallResponse = [];
    var latitudeAndlongitude = [];
    var latlongvalue = "";
    var allowLocation = true;
    getLocation();

    function showLocation(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        latlongvalue = position.coords.latitude + "," + position.coords.longitude;

        (function() {
            navigator.geolocation.getCurrentPosition(function(position) {
                getUserAddressBy(position.coords.latitude, position.coords.longitude)
            }, function(error) {
                console.log("Please accept the location to get the accurate location");
                location.reload();
            });

            function getUserAddressBy(lat, long) {
                var xhttp = new XMLHttpRequest();


                var ajax_url = "<?php echo URL::base(); ?>geolocationapi/geo_location_api?lat=" + lat + "&long=" + long;

                // xhttp.open("GET", "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + latlongvalue + "&key=<?php echo GOOGLE_GEO_LOCATION_API_KEY; ?> ", true);
                xhttp.open("GET", ajax_url, true);
                xhttp.responseType = 'json';
                xhttp.send();
                //  xhttp.onreadystatechange = function() {
                //     if (this.readyState == 4 && this.status == 200) {
                //         var address = JSON.parse(this.responseText);
                //     }
                // };


                xhttp.onreadystatechange = function() {
                    /*
                    // if (xhttp.readyState == XMLHttpRequest.DONE) {
                    //     var result = xhttp.response.results[1].formatted_address;
                    //     var address = xhttp.response.results[1].geometry.location;
                    //     latitudeAndlongitude.push(address);
                    //     apiCallResponse.push(result);

                    //      
                    //     if (allowLocation) {
                    //         var googleApiResponse = apiCallResponse[0].split(',');

                    //         var statename = "";
                    //         var countrycodes = "";
                    //         if (typeof googleApiResponse[5] != 'undefined') {
                    //             var postalCodeAndStateName = googleApiResponse[5].trim().split(' ');
                    //             statename = postalCodeAndStateName[0];
                    //             countrycodes = postalCodeAndStateName[1];;
                    //         } else {
                    //             statename = "<?php echo $s[5]; ?>";
                    //             countrycodes = "<?php echo $s[10]; ?>";
                    //         }
                    //         var latitude = latitudeAndlongitude[0].lat;
                    //         var longitude = latitudeAndlongitude[0].lng;
                    //         var street_address = googleApiResponse[0] + ',' + googleApiResponse[1] + ',' + googleApiResponse[2];
                    //         var city_name = googleApiResponse[4];
                    //         var countrycode = countrycodes;
                    //     } else {

                    //         statename = "<?php echo $s[5]; ?>";
                    //         countrycodes = "<?php echo $s[10]; ?>";
                    //         var city_name = "<?php echo $s[4]; ?>";
                    //         var latitude = "<?php echo $s[16]; ?>";
                    //         var longitude = "<?php echo $s[17]; ?>";

                    //     }


                    //     var itemId = "<?php echo $last_part; ?>";
                    //     var itemid = itemId.split("?")[0];


                    //     var ipaddress = "<?php echo $s[0]; ?>";
                    //     var country = "<?php echo $s[11]; ?>";
                    //     var timezone = "<?php echo $s[19]; ?>";
                    //     var eventid = "<?php echo mt_rand(); ?>";


                    //     var itemid = "<?php echo substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1); ?>";
                    //     itemid = itemid.split("?")[0];

                    //     var url = "<?php echo url::base(); ?>server_google/match_data?scanhitid=" + <?php echo mt_rand(); ?> + "&id_item=" + itemid + "&country_code=" + countrycode + "&country_name=" + country + "&region_name=" + statename + "&city_name=" + street_address + "," + city_name + "&latitude=" + latitude + "&longitude=" + longitude + "&ip_add=" + ipaddress + "&timezone=" + timezone + "&id_browsercat=" + browserextraversion + "&browser_version=" + normalbversion + "&id_platformcat=" + mobilesystems + "&id_devicecat=" + devics + "&screensize=" + screensize;

                    //     var parent_data = window.parent.document.getElementById('request_uri');
                    //     var has_cookie = false;
                    //     document.cookie.split(';').forEach(function(el) {
                    //         var cookie = el.split('=');
                    //         $(cookie).each(function(key, value) {
                    //             if (value.trim() == 'form_save_cookie') {
                    //                 has_cookie = true;
                    //             }
                    //         });
                    //     });
                    //     $temp_var = "";
                    //     var has_cookie = "<?php echo !empty($temp_var) ? $temp_var : '' ?>";

                    //     if (parent_data == null && has_cookie == "") {
                    //         <?php setcookie('form_save_cookie', null, time() - 3600, "/"); ?>
                    //         $.ajax({
                    //             url: url,
                    //             type: 'GET',
                    //             dataType: 'json',
                    //             contentType: "application/json",
                    //             success: function(res) {
                    //                 console.log('success');
                    //             },
                    //             error: function(error) {
                    //                 console.error(error);
                    //             }
                    //         })
                    //     }
                    //      

                    // }*/

                    if (this.readyState == 4 && this.status == 200) {
                        var json_response = xhttp.response;
                        if (allowLocation) {
                            var statename = "";
                            var countrycodes = "";
                            var cityname = "";

                            if (json_response && typeof(json_response.state) != undefined && json_response.state && json_response.state != "") {
                                statename = json_response.state;
                            } else {
                                statename = "<?php echo isset($statename) ? $statename : ''; ?>";
                            }
                            if (json_response && json_response.postalcode) {
                                countrycodes = json_response.postalcode;
                            } else {
                                countrycodes = "<?php echo isset($statename) ? $statename : ''; ?>";
                            }
                            if (json_response && json_response.city && typeof(json_response.city) != undefined) {
                                cityname = json_response.city;
                            } else {
                                cityname = "<?php echo isset($statename) ? $statename : ''; ?>";
                            }

                            if (json_response && json_response.lat) {
                                var latitude = json_response.lat;
                            } else {
                                var latitude = $latitude;
                            }

                            if (json_response && json_response.lng) {
                                var longitude = json_response.lng;
                            } else {
                                var longitude = $longitude;
                            }


                            var street_address = ((json_response.address && json_response.address) ? json_response.address : '') + " " + ((json_response && json_response.short_code) ? json_response.short_code : '') + " " + countrycodes; //+ "," + cityname + ','+statename; //googleApiResponse[0] + ',' + googleApiResponse[1] + ',' + googleApiResponse[ ];
                            var city_name = cityname;
                            var countrycode = countrycodes;

                        } else {

                            statename = "<?php echo isset($statename) ? $statename : ''; ?>";
                            countrycodes = "<?php echo isset($coutrycode) ? $coutrycode : ''; ?>";
                            var city_name = "<?php echo isset($cityname) ? $cityname : ''; ?>";
                            var latitude = "<?php echo isset($latitude) ? $latitude : ''; ?>";
                            var longitude = "<?php echo isset($longitude) ? $longitude : ''; ?>";

                        }

                        var itemId = "<?php echo $last_part; ?>";
                        var itemid = itemId.split("?")[0];

                        var ipaddress = "<?php echo isset($ip) ? $ip : ''; ?>";
                        var country = "<?php echo isset($country) ? $country : ''; ?>";
                        var timezone = "<?php echo isset($timezone) ? $timezone : ''; ?>";
                        var eventid = "<?php echo mt_rand(); ?>";

                        var itemid = "<?php echo substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1); ?>";
                        itemid = itemid.split("?")[0];

                        var url = "<?php echo url::base(); ?>server_google/match_data?scanhitid=" + <?php echo mt_rand(); ?> + "&id_item=" + itemid + "&country_code=" + countrycode + "&country_name=" + country + "&region_name=" + statename + "&city_name=" + street_address + "," + city_name + "&latitude=" + latitude + "&longitude=" + longitude + "&ip_add=" + ipaddress + "&timezone=" + timezone + "&id_browsercat=" + browserextraversion + "&browser_version=" + normalbversion + "&id_platformcat=" + mobilesystems + "&id_devicecat=" + devics + "&screensize=" + screensize + "&scan_type='GPS'";

                        var parent_data = window.parent.document.getElementById('request_uri');
                        var has_cookie = false;
                        document.cookie.split(';').forEach(function(el) {
                            var cookie = el.split('=');
                            $(cookie).each(function(key, value) {
                                if (value.trim() == 'form_save_cookie') {
                                    has_cookie = true;
                                }
                            });
                        });
                        $temp_var = "";
                        var has_cookie = "<?php echo !empty($temp_var) ? $temp_var : '' ?>";

                        if (true || parent_data == null && has_cookie == "") {
                            <?php setcookie('form_save_cookie', null, time() - 3600, "/"); ?>
                            $.ajax({
                                url: url,
                                type: 'GET',
                                dataType: 'json',
                                contentType: "application/json",
                                success: function(res) {
                                    console.log('success');
                                },
                                error: function(error) {
                                    console.error(error);
                                }
                            })
                        }
                        /** */
                    }
                }
                xhttp.onerror = function() { // only triggers if the request couldn't be made at all
                    allowLocation = false;
                };
            }



        })();
    }

    function errorHandler(err) {
        if (err.code == 1) {
            allowLocation = false;
            if (allowLocation) {
                var googleApiResponse = apiCallResponse[0].split(',');

                var statename = "";
                var countrycodes = "";
                if (typeof googleApiResponse[5] != 'undefined') {
                    var postalCodeAndStateName = googleApiResponse[5].trim().split(' ');
                    statename = postalCodeAndStateName[0];
                    countrycodes = postalCodeAndStateName[1];;
                } else {
                    statename = "<?php echo isset($statename) ? $statename : ''; ?>";
                    countrycodes = "<?php echo isset($coutrycode) ? $coutrycode : ''; ?>";
                }
                var latitude = latitudeAndlongitude[0].lat;
                var longitude = latitudeAndlongitude[0].lng;
                var street_address = googleApiResponse[0] + ',' + googleApiResponse[1] + ',' + googleApiResponse[2];
                var city_name = googleApiResponse[4];
                var countrycode = countrycodes;
            } else {

                statename = "<?php echo isset($statename) ? $statename : ''; ?>";
                countrycodes = "<?php echo isset($coutrycode) ? $coutrycode : ''; ?>";
                var city_name = "<?php echo isset($cityname) ? $cityname : ''; ?>";
                var latitude = "<?php echo isset($latitude) ? $latitude : ''; ?>";
                var longitude = "<?php echo isset($s[17]) ? $s[17] : ''; ?>";

            }


            var itemId = "<?php echo $last_part; ?>";
            var itemid = itemId.split("?")[0];


            var ipaddress = "<?php echo isset($ip) ? $ip : ''; ?>";
            var country = "<?php echo isset($country) ? $country : ''; ?>";
            var timezone = "<?php echo isset($timezone) ? $timezone : ''; ?>";
            var eventid = "<?php echo mt_rand(); ?>";


            var itemid = "<?php echo substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1); ?>";
            itemid = itemid.split("?")[0];

            var url = "<?php echo url::base(); ?>server_google/match_data?scanhitid=" + <?php echo mt_rand(); ?> + "&id_item=" + itemid + "&country_code=" + countrycode + "&country_name=" + country + "&region_name=" + statename + "&city_name=" + (street_address ? street_address + "," : "") + city_name + "&latitude=" + latitude + "&longitude=" + longitude + "&ip_add=" + ipaddress + "&timezone=" + timezone + "&id_browsercat=" + browserextraversion + "&browser_version=" + normalbversion + "&id_platformcat=" + mobilesystems + "&id_devicecat=" + devics + "&screensize=" + screensize;

            var parent_data = window.parent.document.getElementById('request_uri');
            var has_cookie = false;
            document.cookie.split(';').forEach(function(el) {
                var cookie = el.split('=');
                $(cookie).each(function(key, value) {
                    if (value.trim() == 'form_save_cookie') {
                        has_cookie = true;
                    }
                });
            });
            $temp_var = "";
            var has_cookie = "<?php echo !empty($temp_var) ? $temp_var : '' ?>";

            if (true || parent_data == null && has_cookie == "") {
                <?php setcookie('form_save_cookie', null, time() - 3600, "/"); ?>
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    contentType: "application/json",
                    success: function(res) {
                        console.log('success');
                    },
                    error: function(error) {
                        console.error(error);
                    }
                })
            }

        } else if (err.code == 2) {
            // alert("Error: Position is unavailable!");
        }
    }

    function getLocation() {
        if (navigator.geolocation) {
            // timeout at 60000 milliseconds (60 seconds)
            var options = {
                timeout: 60000
            };
            navigator.geolocation.getCurrentPosition(showLocation, errorHandler, options);
        } else {
            alert("Sorry, browser does not support geolocation!");
        }
    }
</script>