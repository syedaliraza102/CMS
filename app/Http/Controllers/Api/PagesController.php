<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Models\Pages;
use App\Http\Controllers\Controller;
use App\Common;
use Illuminate\Support\Facades\Input;
use Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use GuzzleHttp\Psr7\Request as Req2;
class PagesController extends Controller
{
    public function __construct()
    { }
    public function pagedetails($slug, Request $request)
    {
        $data = [];
        $pagedata = Pages::where('slug', $slug)->first();
        $data['api'] =  $pagedata;
        $data['page_type'] = $pagedata['page_type'] ?? 'pagenotfound';
        $view_type = $pagedata['view_type'] ?? '';
        if ($view_type == 'view' && $data['page_type'] != 'pagenotfound') {
            $data['page_type'] = 'custom';
            $data['render'] = view('pages.' . $slug, ['data' => $pagedata])->render();
        }
        $seo = !empty($pagedata['seo']) && is_array($pagedata['seo']) ? $pagedata['seo'] : [];
        $data['seo'] = Common::seodata($seo, $pagedata);
        return ['flag' => 1, 'data' => $data];
    }
    public function iadam(Request $request)
    {
        //$real_filename = Storage::disk('public_uploads')->put('sound/' .$request->file);
	$real_filename = Storage::disk('public_uploads')->put('sound' ,$request->file);

        $client = new Client();
        $options = [
        'multipart' => [
            [
            'name' => 'text',
            'contents' => $request->text
            ],
            [
            'name' => 'user_audio_file',
            'contents' => file_get_contents($request->file),
            'filename' => $real_filename,
            'headers'  => [
                'Content-Type' => '<Content-type header>'
            ]
            ],
            [
            'name' => 'question_info',
            'contents' => '\'u1/q1\''
            ],
            [
            'name' => 'no_mc',
            'contents' => '1'
            ]
        ]];
        $request = new Req2('POST', 'https://api2.speechace.com/api/scoring/text/v9.2/json?key=WDOo4z8y7I2QKVSK9QhNDH0R6BsxdgxbBW4DGtL1vIRF5HN9liv96XTuGwr%2FhShrd5iTgP6QR%2BNKC27hmA8%2B3DjKLK%2BWuIwjw1uujQI8lMnUPcuSPy6BrmZgqVtoIXBP&user_id=1234&dialect=en-us');
        $res = $client->sendAsync($request, $options)->wait();
        return $res->getBody();
    }
}
