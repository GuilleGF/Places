<?php

//include the functions for calling google places
require_once( dirname( __FILE__ ) . "/generic-api.php" );

/**
 * Created by PhpStorm.
 * User: guille
 * Date: 29/04/16
 * Time: 19:24
 */
class FoursquareApi extends GenericApi
{
    const CLIENT_ID = 'LFD1Z4YPB2TL2KHB2EFBYLG3QFG4I5DCHRJC3TS410ZA2EQU';
    const CLIENT_SECRET = 'DPIY1LP2ZIM0IOVQ3TGHADGC5OBQ0HEWSGLADFKUTY4BUWGN';
    const LOCALE = 'es';

    const URL_BASE = "https://api.foursquare.com/v2/venues/";

    public $serach = "https://api.foursquare.com/v2/venues/search?client_id=&client_secret=&ll=41.3862874,2.170621&v=20160429&query=chicha";

    /**
     * @param $term
     * @return array|mixed|object|string
     */
    public static function search($term)
    {
        if (self::DEBUG) {
            return self::jsonDecode(self::searchMock());
        }

        return '';
    }
    
    
    
    private static function getUrl() {
        $version = date('Ymd');

        $url = self::URL_BASE.'search?client_id='.self::CLIENT_ID.'&client_secret='.self::CLIENT_SECRET.
            '&locale='.self::LOCALE.'&v='.$version.'ll=41.3862874,2.170621&&query=chicha';

    }

    /**
     * @return string
     */
    private static function searchMock()
    {
        return '{"meta":{"code":200,"requestId":"57248d00498eeca8f96dced5"},"response":{"venues":[{"id":"555dd601498e912d00ae6e7a","name":"Chicha Limoná","contact":{"phone":"+34932776403","formattedPhone":"+34 932 77 64 03","facebook":"1563253257279401","facebookUsername":"ChichaLimona","facebookName":"ChichaLimoná"},"location":{"address":"Pg. Sant Joan, 80","crossStreet":"Carrer Aragó","lat":41.397374602623735,"lng":2.1724573754501346,"distance":1243,"postalCode":"08009","cc":"ES","city":"Barcelona","state":"Cataluña","country":"España","formattedAddress":["Pg. Sant Joan, 80 (Carrer Aragó)","08009 Barcelona Cataluña","España"]},"categories":[{"id":"4bf58dd8d48988d1db931735","name":"Restaurante de tapas","pluralName":"Restaurantes de tapas","shortName":"Tapas","icon":{"prefix":"https:\/\/ss3.4sqi.net\/img\/categories_v2\/food\/tapas_","suffix":".png"},"primary":true}],"verified":true,"stats":{"checkinsCount":499,"usersCount":330,"tipCount":43},"url":"http:\/\/www.chichalimona.com","allowMenuUrlEdit":true,"specials":{"count":0,"items":[]},"venuePage":{"id":"136275883"},"hereNow":{"count":1,"summary":"Una persona más está aquí","groups":[{"type":"others","name":"Otras personas aquí","count":1,"items":[]}]},"referralId":"v-1462013184","venueChains":[]}]}}';
        
    }
    
}