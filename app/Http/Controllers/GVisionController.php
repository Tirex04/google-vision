<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Core\ServiceBuilder;

class GVisionController extends Controller
{
    public function gvison()
    {
        $cloud = new ServiceBuilder([
            'keyFilePath' => base_path('google-vision.json'),
            'projectId' => config('gcp.projectId')
        ]);

        $vision = $cloud->vision();

        $output = imagecreatefromjpeg(public_path('friends.jpg'));

        $image = $vision->image(file_get_contents(public_path('friends.jpg')), ['FACE_DETECTION']);

        $results = $vision->annotate($image);

        foreach ($results->faces() as $face) {
            $vertices = $face->boundingPoly()['vertices'];

            $x1 = $vertices[0]['x'] ?? 0;
            $y1 = $vertices[0]['y'] ?? 0;
            $x2 = $vertices[2]['x'] ?? 0;
            $y2 = $vertices[2]['y'] ?? 0;

            // print_r($face);

            imagerectangle($output, $x1, $y1, $x2, $y2, 0x00ff00);
        }

        header('Content-Type: image/jpeg');
        imagejpeg($output);
        imagedestroy($output);

    }
}
