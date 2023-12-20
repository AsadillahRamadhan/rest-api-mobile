<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Intervention\Image\Facades\Image;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Data::where('user_id', auth()->user()->id)->get();

        if(count($data) > 0){
            return response()->json($data);
        } else {
            return response()->json(['message' => 'kosong']);
        }
            
    }

    public function predict(Request $request){
        $photo = $request->file('photo');
        $path = $photo->store('public');
        $data = new Data();
        $data->link = 'storage/' . basename($path);
        $data->user_id = auth()->user()->id;
        $data->save();
        $client = new Client();
        $url = 'https://5ace-2a09-bac5-3a1a-16d2-00-246-3b.ngrok-free.app/upload';
        $file = 'C:/laragon/www/rest-api-mobile/public/storage/' . basename($path);
        $link = ['link' => $file];
        try {
            $response = $client->post($url, ['form_params' => $link]);
            $json1 = json_decode($response->getBody(), true);
            $data->tanggal = $json1['tanggal'];
            $data->event = $json1['event'];
            $data->nama = $json1['nama'];
            $data->nomor = $json1['nomor'];
            $data->instansi = $json1['instansi'];
            $data->save();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Data::find($id);
        return response()->json(['content' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = Data::find($id);
        $data->nomor = $request->nomor;
        $data->nama = $request->nama;
        $data->instansi = $request->instansi;
        $data->event = $request->event;
        $data->tanggal = $request->tanggal;
        $data->save();
        
        return response()->json(['message' => "Data Berhasil Diupdate!"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Data::destroy($id)){
            return response()->json(['message' => "Data Berhasil Dihapus"]);
        }
    }
}
