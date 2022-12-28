<?php

namespace App;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;  

class TokenHelper{
    public static function getTokenAndRefreshToken(OClient $oClient, $email, $password) {
        try{
            $options = ['CURLOPT_SSL_VERIFYPEER' => false, 'debug'=> fopen('php://stderr', 'w') ,'exceptions'=>true];
            $http = new Client($options);
            $payload = [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'username' => $email,
                    'password' => $password,
                    'scope' => '',
                ],
            ];
            $response = $http->request('POST', 'http://www.mysite.local/oauth/token',$payload );
            $result = json_decode((string) $response->getBody(), true);
            Log::debug('Token response'. print_r( $result , 1));
            $access = [
                'access_token' =>$result['access_token'],
                'refresh_token'=>$result['refresh_token'],
            ];
            return response()->json($access, 200);
        }catch(\Exception $e){
            return ["error" => $e->getMessage()];
        }
    }
}

?>
