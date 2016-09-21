<?php

//include the functions for calling google places
require_once( dirname( __FILE__ ) . "/generic-api.php" );

/**
 * Created by PhpStorm.
 * User: guille
 * Date: 27/04/16
 * Time: 0:55
 */
class GooglePlacesApi extends GenericApi
{
    const BASE_URL = "https://maps.googleapis.com/maps/api/place/";
    const API_KEY = 'AIzaSyCtLGSYWlfyvT7jyzVSHDGOlqs8rOgn2yw';

    /**
     * @param string $location
     * @return array|mixed|object
     */
    public static function search($location)
    {
        if (self::DEBUG) {
            return self::jsonDecode(self::getSearchMock());
        }

        $location = preg_replace("/&/", "and", $location);
        $location = urlencode(trim(preg_replace("/[^0-9a-zA-Z -]/", "", $location)));

        $url = self::BASE_URL."autocomplete/json?input=$location&components=country:es&types=establishment&location=41.3862874,2.170621&radius=10&language=es";

        $json = self::execute($url);
        $response = self::jsonDecode($json);

        return $response;
    }

    /**
     * @param string $placeId
     * @return array|mixed|object
     */
    public static function placeDetails($placeId)
    {
        if (self::DEBUG) {
            $json = self::getDetailPlaceMock();
        } else {
            $url = self::BASE_URL."details/json?placeid=$placeId&language=es";
            $json = self::execute($url);
        }

        $response = self::jsonDecode($json);

        $place = [];
        if (isset($response['result'])) {
            $place['name']=isset($response['result']['name']) ? $response['result']['name'] : '';
            $place['address']=isset($response['result']['vicinity']) ? $response['result']['vicinity'] : '';
            $place['hours']=isset($response['result']['opening_hours']['weekday_text']) ? $response['result']['opening_hours']['weekday_text'] : '';
            $place['openNow']=isset($response['result']['opening_hours']['open_now']) ? $response['result']['opening_hours']['open_now'] : '';
            $place['priceLevel']=isset($response['result']['price_level']) ? $response['result']['price_level'] : '';
            $place['rating']=isset($response['result']['rating']) ? $response['result']['rating'] : '';
            $place['phoneNumber']=isset($response['result']['formatted_phone_number']) ? $response['result']['formatted_phone_number'] : '';
            $place['website']=isset($response['result']['website']) ? $response['result']['website'] : '';
            $place['lat']=isset($response['result']['geometry']['location']['lat']) ? $response['result']['geometry']['location']['lat'] : '';
            $place['lng']=isset($response['result']['geometry']['location']['lng']) ? $response['result']['geometry']['location']['lng'] : '';
            $place['permanentlyClosed']=isset($response['result']['permanently_closed']) ? $response['result']['permanently_closed'] : '';
            $place['reviews']=isset($response['result']['reviews']) ? $response['result']['reviews'] : '';
            $place['photos']=isset($response['result']['photos']) ? $response['result']['photos'] : '';
        }
        
        return $place;
    }

    public static function urlPhoto($photoReference, $maxWidth = 800)
    {
        if (self::DEBUG) {
            $url = plugins_url( '/../img/test-image-800px.jpg', __FILE__ );
        } else {
            $url = self::BASE_URL."photo?maxwidth=$maxWidth&photoreference=$photoReference&key=".self::API_KEY;
        }

        $imageData = base64_encode(file_get_contents($url));

        echo 'data: '.mime_content_type($url).';base64,'.$imageData;
    }
    /**
     * @param string $url
     * @return string
     */
    protected static function execute($url)
    {
        $url .= "&key=" . self::API_KEY;

        return parent::execute($url);
    }

    /**
     * @return string
     */
    private static function getSearchMock()
    {
        return '{
           "predictions" : [
              {
                 "description" : "ChichaLimoná, Paseo de San Juan, Barcelona",
                 "id" : "29b0bb87841b26c7f69b85c38f7b584153c59161",
                 "matched_substrings" : [
                    {
                       "length" : 12,
                       "offset" : 0
                    }
                 ],
                 "place_id" : "ChIJbYaS5eiipBIRsFgpNrRxFL4",
                 "reference" : "CkQ6AAAA7VnDpnc1SyiY5TobihyigBKxLQcNxM4a35UeTYv3dO89jU4ciWVQYKgqeM3Xjahbaydo1hHXd2dKjV0ioiyC5RIQhKKv8VLiqHjjZeFoBwnRchoUzfoUykWPwfQvRLysMkG86noaW5w",
                 "terms" : [
                    {
                       "offset" : 0,
                       "value" : "ChichaLimoná"
                    },
                    {
                       "offset" : 14,
                       "value" : "Paseo de San Juan"
                    },
                    {
                       "offset" : 33,
                       "value" : "Barcelona"
                    }
                 ],
                 "types" : [ "establishment" ]
              }
           ],
           "status" : "OK"
        }';
    }

    /**
     * @return string
     */
    private static function getDetailPlaceMock()
    {
        return '{
           "html_attributions" : [],
           "result" : {
              "address_components" : [
                 {
                    "long_name" : "80",
                    "short_name" : "80",
                    "types" : [ "street_number" ]
                 },
                 {
                    "long_name" : "Passeig de Sant Joan",
                    "short_name" : "Passeig de Sant Joan",
                    "types" : [ "route" ]
                 },
                 {
                    "long_name" : "Barcelona",
                    "short_name" : "Barcelona",
                    "types" : [ "locality", "political" ]
                 },
                 {
                    "long_name" : "Barcelona",
                    "short_name" : "Barcelona",
                    "types" : [ "administrative_area_level_2", "political" ]
                 },
                 {
                    "long_name" : "España",
                    "short_name" : "ES",
                    "types" : [ "country", "political" ]
                 },
                 {
                    "long_name" : "08009",
                    "short_name" : "08009",
                    "types" : [ "postal_code" ]
                 }
              ],
              "adr_address" : "\u003cspan class=\"street-address\"\u003ePasseig de Sant Joan, 80\u003c/span\u003e, \u003cspan class=\"postal-code\"\u003e08009\u003c/span\u003e \u003cspan class=\"locality\"\u003eBarcelona\u003c/span\u003e, \u003cspan class=\"region\"\u003eBarcelona\u003c/span\u003e, \u003cspan class=\"country-name\"\u003eEspaña\u003c/span\u003e",
              "formatted_address" : "Passeig de Sant Joan, 80, 08009 Barcelona, Barcelona, España",
              "formatted_phone_number" : "932 77 64 03",
              "geometry" : {
                 "location" : {
                    "lat" : 41.39743259999999,
                    "lng" : 2.1726161
                 }
              },
              "icon" : "https://maps.gstatic.com/mapfiles/place_api/icons/restaurant-71.png",
              "id" : "29b0bb87841b26c7f69b85c38f7b584153c59161",
              "international_phone_number" : "+34 932 77 64 03",
              "name" : "ChichaLimoná",
              "opening_hours" : {
                 "open_now" : true,
                 "periods" : [
                    {
                       "close" : {
                          "day" : 0,
                          "time" : "1700"
                       },
                       "open" : {
                          "day" : 0,
                          "time" : "0930"
                       }
                    },
                    {
                       "close" : {
                          "day" : 3,
                          "time" : "0100"
                       },
                       "open" : {
                          "day" : 2,
                          "time" : "0830"
                       }
                    },
                    {
                       "close" : {
                          "day" : 4,
                          "time" : "0100"
                       },
                       "open" : {
                          "day" : 3,
                          "time" : "0830"
                       }
                    },
                    {
                       "close" : {
                          "day" : 5,
                          "time" : "0100"
                       },
                       "open" : {
                          "day" : 4,
                          "time" : "0830"
                       }
                    },
                    {
                       "close" : {
                          "day" : 6,
                          "time" : "0200"
                       },
                       "open" : {
                          "day" : 5,
                          "time" : "0830"
                       }
                    },
                    {
                       "close" : {
                          "day" : 0,
                          "time" : "0200"
                       },
                       "open" : {
                          "day" : 6,
                          "time" : "0930"
                       }
                    }
                 ],
                 "weekday_text" : [
                    "lunes: Cerrado",
                    "martes: 8:30–1:00",
                    "miércoles: 8:30–1:00",
                    "jueves: 8:30–1:00",
                    "viernes: 8:30–2:00",
                    "sábado: 9:30–2:00",
                    "domingo: 9:30–17:00"
                 ]
              },
              "photos" : [
                 {
                    "height" : 864,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/104734255297595107997/photos\"\u003eChichaLimoná\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAOrzj9cqd7ddf3WG5foNC28ZgPKDIyikI2qOIA3g7c2O1q-tKit1KUEY5bTEDzxgg1xvjzh7JeZz8ygW7kveW8KFKvBE49IXIWhjwv9i-iCIfPWo4TooQv2RFvQQ5tQVBsAmLYIPgAVsPI8hK3dk7NxOvW8Xv8451Zf1wW6BofzHEhAR-OTFN0EvxZBjAT3io-mFGhTRoEFpUmlWvjm7V8Aa7X4ht52MSQ",
                    "width" : 1296
                 },
                 {
                    "height" : 3264,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/108387418100239063623/photos\"\u003eDani Granché\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAICf7dKaaK9zF1zQMu3kP7492f7bUe8WmR7KSzzjI1qUWHK461q2IC2pT_uyhjirhxTOTNa7RCERCMSS6gQVKA1MIzIzjcni2RUNhNG2ytN0Ue96zgJlQs8fYvpp1Mte7Gw8_s9rFdwtV2o2bVcV96FQ2794cq2Mi-XdauUM2OcWEhA2-67f6Q_bCqbaghMwan1BGhQEalILGC8PRBJERvCT8saySEVaQg",
                    "width" : 2448
                 },
                 {
                    "height" : 1466,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/113602292044706855251/photos\"\u003eIgor Koskov\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAADIJ4BGFpoSQ56WPlLssqlbtPZFR8x_xMPJLKy2OIxTiyPqnmrDOhrVXI6zN6FuZVghEJ38whesn_2dqdRd-tP3Qr3pusZNncHLlYYYVwWgDmlblEwEmVfTHsZxvcktZgYnF61cpe7VehqqaBmxS4nbYBiXyt9PrUNnnJj9h3M8lEhCXSxNXCE5bWRpL3nwJs8qQGhQhlpx5zBgp0uZtOKqq-zG2cEDuqw",
                    "width" : 1100
                 },
                 {
                    "height" : 1934,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/104734255297595107997/photos\"\u003eChichaLimoná\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAEZSFMu5FwoTioU1HIDolr1Z3XCJMq7osfmpqxs9U9rUHn166EVI64oC2egt_Mf-V3D3NFwPVwyaZtpeCxGspanoKh-o55C9OFma9Bd5YBDzn0PBZf8WrSdC7bAfJo3ZEObyExTJUBg1dES5Yw5CXyMS6dzPhGc4in7wmxcpvxApEhByNxegaqsZOHjGl51RHnGiGhSCKaOOUnzhsEE083YFLzV73R9klg",
                    "width" : 2905
                 },
                 {
                    "height" : 2448,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/114365854808128913509/photos\"\u003eVeronica Montuenga\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAOWQQOkFxv0CmU_iWVOwtfvOQO9MUXSAjp0ZVCES1BlBHHEXK3OtKU9oiou7QiDgDr_tm3fME7YfACc8oXn2XlHo-8_8Ncs5GQtyYuHWgloauD3Jx9BgYUN-x4R2w9novAvMyfsHwM7lFSniz4jlzXXlF1_yWGeeuUoUFps5t8ZrEhCjbm6MhruiCsoXioVJQ8qvGhS-_ND7ig2m27EsSsNq52RQ2QLusA",
                    "width" : 3264
                 },
                 {
                    "height" : 4032,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/104734255297595107997/photos\"\u003eChichaLimoná\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAAsYo5K0mF1JywYox9URzPvlly2nm2JpcXjS9mPijCHVRmMYvtyx1zpPVl1mDPzXvCpNNO4d1lAf8lmsnyyRUz6fQbLy-xi2QJkXV2zlC9rglqPkrfaD4f1p7Uhosr-1UmUORByNnXl4L5TRfDCFtOXOOqNpXkQnGpf8Bt9rJWWuEhDlGhR-TyJB7h-oIW7J9B4yGhQm5hVoe4eHSNHxbyQ-C7YJRPTjTg",
                    "width" : 3024
                 },
                 {
                    "height" : 2056,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/104734255297595107997/photos\"\u003eChichaLimoná\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAFz1YDj2jV-F5ZMUj9epWF_JxvS5uE4dknqSxSyqNEO66hroCGK9oB3ZmS0xFV6k9DOP_O8pf-p47aH7XAluMI754iFax5vY_6rPQsMXqMGODfooy-0-nhnqpJuRKFXOuNiJjYDxOTMcQ7CTpBil-eLPz-qnibNEWhougxugL5TOEhD2tDo0GACBffDHJXqa5r12GhQ2HG958KUHs7yawiEu5vBnALnmbA",
                    "width" : 3088
                 },
                 {
                    "height" : 2448,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/114365854808128913509/photos\"\u003eVeronica Montuenga\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAJ4SQTxMimeIeCgBVZ6p8LXLc26pFGRNyTn4o8X6B3gDs4kg6LJ_ovOPZu3wu7nCr-oLnS7yjlsFMjUJdTfF7SZ8hUXDIu3XVV095k6ZIsTBQ_7-Al6n7euoP3iSFZFrcP4pjmhKbJx6gfmYx7fhUdd6yshuWu0EBLCajVnNlHCVEhDMVKWN1JH39i64EUxlqPsOGhRPkjnTewcdFeAr88vAd9BQ7AMUMg",
                    "width" : 3264
                 },
                 {
                    "height" : 3078,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/104734255297595107997/photos\"\u003eChichaLimoná\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAJkJRIpl8se2mNm5LUn0ptclhYBPruL2GpuN50lkNLYxP8t_s1sot4U-rUajoSSFVdEBt_H2cZpUEFqXKDwVqRFPUsJem26qM0alFfNwA6jCINpjZfrPdmEI4GLnjuWWtuYevdVAxgDYeInjTzSA9O-gBi1z25LzOkkl8LwpkFLtEhCz7mnYu0Odvzo5bjI7jBvrGhSRqAgcetT2vlg4MFEhFZ3gp4AIDw",
                    "width" : 2049
                 },
                 {
                    "height" : 3024,
                    "html_attributions" : [
                       "\u003ca href=\"https://maps.google.com/maps/contrib/104734255297595107997/photos\"\u003eChichaLimoná\u003c/a\u003e"
                    ],
                    "photo_reference" : "CoQBcwAAAEOC45aHSY094Y5W3L3Mto8XvvVTPTizbCYOMROywvPA1rLGn4V_j8j3MyOkpdfkzy0N7V1KEWfMB3IMvqvjQ_fhuvexI00S6ClumBVJXabKyzy1tV5q7av58w958ZpMCQE85W8SUV6V51gyOCfq_XKc9a9YGf-WIH7pS9o6guJCEhDmLcantjhTGeWBi6fduEx4GhQ2OyF6Jg2jnKk94-2t1MBvaQpW2A",
                    "width" : 4032
                 }
              ],
              "place_id" : "ChIJbYaS5eiipBIRsFgpNrRxFL4",
              "rating" : 4.1,
              "reference" : "CnRhAAAA63ltGe7AqcIhFHtoSvI2B_Fu3y8Sd5hg7R_I1zVy6_AbW3aSrvHyHZHACl2PHLz9j8BzDPgDm_ZG9o6stnTGhnBXkC-8v5Nu2YYwIx4EhbqOfIFKwrtdbT1pNIZuxtxRMAmmLIZh2jBITC61650tORIQpxwjSMj9833aWP2LZr3RBhoUf13a49_h0fsnnEjPJ04A_uv-BRE",
              "reviews" : [
                 {
                    "aspects" : [
                       {
                          "rating" : 3,
                          "type" : "overall"
                       }
                    ],
                    "author_name" : "Carol Rubio",
                    "author_url" : "https://plus.google.com/112991738613074076297",
                    "language" : "es",
                    "rating" : 5,
                    "text" : "Lo mejor del barrio y con diferencia. Nos hacía mucha falta algo así. He cenado infinidad de veces y debo decir que ni tan sólo una me ha decepcionado. Tienen una calidad en sus productos muy buena. El personal es súper amable y los precios si los comparas con los bares de alrededor seguramente te pareceran caros, pero hay una cosa que está clara, la calidad, el lugar y la atención se paga. De todos modos y desde mi punto de vista, no sale tan caro, normalmente las veces que he ido ha sido una media de 20 por persona. Por cierto, felicidades a los cocineros porque lo hacen todo super rico.",
                    "time" : 1461665284
                 },
                 {
                    "aspects" : [
                       {
                          "rating" : 3,
                          "type" : "overall"
                       }
                    ],
                    "author_name" : "Chema Bescós",
                    "author_url" : "https://plus.google.com/116471503342823058251",
                    "language" : "es",
                    "profile_photo_url" : "//lh6.googleusercontent.com/-4bB59LhQkOc/AAAAAAAAAAI/AAAAAAAAFU0/JxspXFh7aRE/photo.jpg",
                    "rating" : 5,
                    "text" : "Deliciosos los sándwiches del Limoná, el viking (de llata de ternera, salsa especial y queso) y el de berenjena, queso y brotes. Ambos planchados con buen pan de payés y muuuucha chicha dentro, como debe ser! \nLas patatas un poquito aburridas, no me convencieron tanto.\nLocal amplio, luminoso y cálido, en la línea hipster tan de moda, buen café y buenos pasteles caseros, probad el lemon pie y el cheese cake\nAl lado, su hermano mayor el Chicha, en un formato más formal, para comidas y cenas...habrá que probarlo ;)\nEl Paseo San Juan se viene arriba!",
                    "time" : 1461429048
                 },
                 {
                    "aspects" : [
                       {
                          "rating" : 3,
                          "type" : "overall"
                       }
                    ],
                    "author_name" : "Veronica Montuenga",
                    "author_url" : "https://plus.google.com/114365854808128913509",
                    "language" : "es",
                    "profile_photo_url" : "//lh6.googleusercontent.com/-V9nzPryDswM/AAAAAAAAAAI/AAAAAAAAO5E/PFkjU_X2j18/photo.jpg",
                    "rating" : 5,
                    "text" : "Cada día me gusta más el ChichaLimoná! Esta reseña la escribo con conocimiento de causa, pues últimamente voy mucho por allí. Los desayunos está geniales, me encanta ir a merendar, los zumos son frescos y recién preparados; y las comidas y cenas perfectas. \n\nChica es la parte más formal, es donde se sirven las cenas y las comidas. El menú está genial y el precio es muy razonable. \n\nLimoná en cambio es una cafetería hípster de esas que tanto gustan ahora, pero con un servicio impecable. Los sándwiches de la carta, especialmente el viking, están de muerte. La tarta de queso y los rolls de cardamomo son adictivos, y de paso puedes comprar pan y llevártelo a casa. Ah! Y es que esto no lo he dicho todo el pan, bollos y tartas están hecho en casa… a esto conviene añadir que siempre tienen buen café y buen té.\n",
                    "time" : 1458909775
                 },
                 {
                    "aspects" : [
                       {
                          "rating" : 3,
                          "type" : "overall"
                       }
                    ],
                    "author_name" : "Alfonso Simó Maimó",
                    "author_url" : "https://plus.google.com/107879446793988701279",
                    "language" : "es",
                    "profile_photo_url" : "//lh6.googleusercontent.com/-V_sf8Bincn4/AAAAAAAAAAI/AAAAAAAADS4/O0pmW4mHqtI/photo.jpg",
                    "rating" : 5,
                    "text" : "Por la atención, por la tranquilidad y por el Lemon Pie es un sitio en el que repetiré seguramente. Me encanta ",
                    "time" : 1458927143
                 },
                 {
                    "aspects" : [
                       {
                          "rating" : 3,
                          "type" : "overall"
                       }
                    ],
                    "author_name" : "pol Grases",
                    "author_url" : "https://plus.google.com/110299087009114519360",
                    "language" : "es",
                    "profile_photo_url" : "//lh5.googleusercontent.com/--Hw1a2yIQV8/AAAAAAAAAAI/AAAAAAAAAbI/0byl3aw5k8I/photo.jpg",
                    "rating" : 5,
                    "text" : "Un sitió excepcional. Una vez en tu vida tienes que ir a ese lugar. Excelente decoración, todo tipo de zumos naturales y sus fantasticos bocadillos variados. La calidad de los productos tambien es asombrosa. Un buen jamón, el pan exquisito...  \nEl precio, bueno, si tienes poco en el bolsillo vete a un bar normal pero con su calidad tiene pies y cabeza.\nPerfecto",
                    "time" : 1449518222
                 }
              ],
              "scope" : "GOOGLE",
              "types" : [ "restaurant", "food", "point_of_interest", "establishment" ],
              "url" : "https://maps.google.com/?cid=13696697385557252272",
              "user_ratings_total" : 44,
              "utc_offset" : 120,
              "vicinity" : "Passeig de Sant Joan, 80, Barcelona",
              "website" : "http://www.chichalimona.com/"
           },
           "status" : "OK"
        }';
    }
}