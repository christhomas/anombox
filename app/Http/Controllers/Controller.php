<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function cors()
    {
        return new \Illuminate\Http\JsonResponse([
            'version' => 'v1',
            'date' => new \DateTime(),
        ]);
    }

    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        foreach(config('auth.users') as $user){
            if($user->username !== $username) continue;
            if($user->password !== sha1($password)) continue;

            if(empty(session_id())){
                session_start();
            }

            $_SESSION['token']      = sha1(json_encode($_SESSION));
            $_SESSION['expires_at'] = time()+3600;

            return new JsonResponse([
                "token"         => $_SESSION['token'],
                "expires_at"    => $_SESSION['expires_at'],
            ]);
        }

        if(!empty(session_id())){
            session_destroy();
        }

        abort(401);
    }

    public function upload(Request $request)
    {
        $file = $request->file('video');
        $article = $request->post('article');
        $section = $request->post('section');
        $language = $request->post('language');

        // CHECK FILE EXISTS
        if (!$file) {
            abort(400, "File was missing");
        }

        // CHECK NAME OF FILE MATCHES CRITERIA
//        $filename = $file->getClientOriginalName();
//        $regex = config('video.regexPattern');
//        if($regex && !preg_match('/'.$regex.'/', $filename)){
//            abort(400, "File did not match requested pattern for name");
//        }

        // CHECK FILE HAS A MINIMUM SIZE
        $minimumFileSize = config('video.minimumFileSize');
        if ($file->getSize() < $minimumFileSize) {
            abort(400, "File was too small");
        }

        // CHECK FILE HAS A MAXIMUM SIZE
        $maximumFileSize = config('video.maximumFileSize');
        if($maximumFileSize > 0 && $file->getSize() > $maximumFileSize){
            abort(400, "File was too large");
        }

        // CHECK BASIC MIME TYPE CHECKS
        if (!preg_match('/video\/*/', $file->getMimeType())){
            abort(400, "File was not a movie");
        }

        $filename = "a{$article}";
        if(!empty($section)) $filename = "{$filename}_s{$section}";
        $filename = "{$filename}_$language";

        $suffix = bin2hex(random_bytes(4));
        $extension = $file->getClientOriginalExtension();
//        $filename = explode(".", $filename);
//        array_pop($filename);
//        array_push($filename,$suffix,$extension);
//        $filename = implode(".", $filename);
        $filename = "$filename.$suffix.$extension";

        $file = $file->move("/www/storage/app", $filename);

        return new \Illuminate\Http\JsonResponse([
            "filename" => $file->getRealPath()
        ]);
    }

    public function list(Request $request)
    {
        return new \Illuminate\Http\JsonResponse(["get" => "/list"]);
    }
}

