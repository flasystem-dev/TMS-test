<?php


namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Cloud\Language\LanguageClient;
use Illuminate\Support\Facades\Date;
use AdityaDees\LaravelBard\LaravelBard;
use Illuminate\Support\Facades\Storage;

use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;

class GoogleBardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public static function bard_api()
    {
        $languageClient = new LanguageClient([
            'keyFilePath' => '/var/www/flasystem_net/app/Util/key.json',
            "projectId" => 'My First Project'
        ]);
        $text = "2023. 12. 14. 오전";

//        $result = $languageClient->analyzeEntities($text);
        $result = $languageClient->analyzeEntities($text) ->entitiesByType('DATE');


        dd($result);
    }

    public static function vision_api()
    {
        $vision = new ImageAnnotatorClient([
            'keyFilePath' => '/var/www/flasystem_net/app/Util/key.json',
            "projectId" => 'My First Project'
        ]);
        $path = "/assets/images/test_img.png";
        $image = file_get_contents(public_path($path));
        $result = $vision -> textDetection($image) -> getFullTextAnnotation();

        dd($result);

    }
}
