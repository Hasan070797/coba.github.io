<?php

namespace App\Http\Controllers;

use App\City;
use App\Provincy;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        return ("Test");
    }

    public function getprovince(){
       $client = new Client();

       try{
           $response = $client->get('https://api.rajaongkir.com/starter/province',
            array(
               'headers' => array(
                'key' => 'df103d0d25e11a6cce886bc756c4fb16',
               ) 
            )
        );
       } catch(RequestException $e){
           var_dump($e->getResponse()->getBody()->getContents());
       }
       

       $json = $response->getBody()->getContents();

       $array_result = json_decode($json, true);

    //    print_r($array_result);
        for($i = 0; $i < count($array_result["rajaongkir"]["results"]); $i++)
        {
            $province = new Provincy;
            $province->id = $array_result["rajaongkir"]["results"][$i]["province_id"];
            $province->name = $array_result["rajaongkir"]["results"][$i]["province"];
            $province->save();

        }
    }

    public function getcity(){
        $client = new Client();
 
        try{
            $response = $client->get('https://api.rajaongkir.com/starter/city',
             array(
                'headers' => array(
                 'key' => 'df103d0d25e11a6cce886bc756c4fb16',
                ) 
             )
         );
        } catch(RequestException $e){
            var_dump($e->getResponse()->getBody()->getContents());
        }
        
 
        $json = $response->getBody()->getContents();
 
        $array_result = json_decode($json, true);
 
        // print_r($array_result);
        for($i = 0; $i < count($array_result["rajaongkir"]["results"]); $i++)
        {
            $city = new City;
            $city->id = $array_result["rajaongkir"]["results"][$i]["city_id"];
            $city->name = $array_result["rajaongkir"]["results"][$i]["city_name"];
            $city->id_province = $array_result["rajaongkir"]["results"][$i]["province_id"];
            $city->save();

        }
     }


    public function checkshipping()
    {
        $city = City::get();
        return view('cek',compact('city'));

    }

    public function prossesshipping(Request $request)
    {

        $client = new Client();
 
        try{
            $response = $client->request('POST','https://api.rajaongkir.com/starter/cost',
             [
                 'body' => 'origin='.$request->origin.'&destination='.$request->destination.'&weight='.$request->weight.'&courier=jne',
                 'headers' => [
                    'key' => 'df103d0d25e11a6cce886bc756c4fb16',
                    'content-type' => 'application/x-www-form-urlencoded'
                 ]
             ]
         );
        } catch(RequestException $e){
            var_dump($e->getResponse()->getBody()->getContents());
        }
        
 
        $json = $response->getBody()->getContents();
 
        $array_result = json_decode($json, true);
        $i=0;
        $origin = $array_result["rajaongkir"]["origin_details"]["city_name"];
        $destination = $array_result["rajaongkir"]["destination_details"]["city_name"];
        $layanan = $array_result["rajaongkir"]["results"][0]["costs"][$i]["service"];
        $tarif = $array_result["rajaongkir"]["results"][0]["costs"][$i]["cost"][0]["value"];
        $etd = $array_result["rajaongkir"]["results"][0]["costs"][$i]["cost"][0]["etd"];

        return view('result',compact('origin','destination','layanan','tarif','etd','array_result'));
        // print_r($array_result);

    }
}
