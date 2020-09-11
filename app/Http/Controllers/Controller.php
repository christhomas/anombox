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
            if($user['username'] !== $username) continue;
            if($user['password'] !== sha1($password)) continue;

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

        // CHECK FILE EXISTS
        if (!$file) {
            error_log("http/400: file was missing");
            abort(400, "File was missing");
        }

        $data = [
            'src_name'  => $file->getClientOriginalName(),
            'dst_name'  => '',
            'size'      => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'type'      => $file->getMimeType(),
            'article'   => $request->post('article'),
            'section'   => $request->post('section'),
            'language'  => trim($request->post('language')),
        ];

        $filename = "a{$data['article']}";
        if(!empty($data['section'])) $filename = "{$filename}_s{$data['section']}";
        $filename = "{$filename}_{$data['language']}";

        $suffix = bin2hex(random_bytes(4));
        $extension = $data['extension'];
        $filename = "$filename.$suffix.$extension";

        $data['dst_name'] = $filename;

        error_log("incoming upload: ".json_encode($data));

        // CHECK NAME OF FILE MATCHES CRITERIA
//        $filename = $file->getClientOriginalName();
//        $regex = config('video.regexPattern');
//        if($regex && !preg_match('/'.$regex.'/', $filename)){
//            abort(400, "File did not match requested pattern for name");
//        }

        // CHECK FILE HAS A MINIMUM SIZE
        $minimumFileSize = config('video.minimumFileSize');
        if ($data['size'] < $minimumFileSize) {
            error_log("http/400: file was too small");
            abort(400, "File was too small");
        }

        // CHECK FILE HAS A MAXIMUM SIZE
        $maximumFileSize = config('video.maximumFileSize');
        if($maximumFileSize > 0 && $data['size'] > $maximumFileSize){
            error_log("http/400: file was too large");
            abort(400, "File was too large");
        }

        // CHECK BASIC MIME TYPE CHECKS
        if (!preg_match('/video\/*/', $data['type'])){
            error_log("http/400: file was not a movie");
            abort(400, "File was not a movie");
        }

        $file = $file->move("/www/storage/app", $filename);

        return new \Illuminate\Http\JsonResponse([
            "filename" => $file->getRealPath()
        ]);
    }

    public function list(Request $request)
    {
        $path = "/www/storage/app";
        $list = array_map(function($item) use ($path){
            return [
                "name"  => trim(str_replace($path, "", $item), "/"),
                "size"  => filesize($item),
                "date"  => fileatime($item),
            ];
        }, glob("$path/*.*"));

        usort($list, function($a, $b){
            return $a['date'] < $b['date'];
        });

        return new \Illuminate\Http\JsonResponse($list);
    }

    public function delete(string $filename)
    {
        $filename = urldecode($filename);
        $fullpath = "/www/storage/app/$filename";

        if(is_file($fullpath)){
            if(strpos($fullpath, "/www/storage/app") === 0){
                $filename = basename($fullpath);
                $dirname = dirname($fullpath);
                rename($fullpath, "$dirname/deleted/$filename");
            }
        }

        return new \Illuminate\Http\JsonResponse(["is_deleted" => !is_file($fullpath)]);
    }

    public function download(string $filename)
    {
        $filename = urldecode($filename);
        $fullpath = "/www/storage/app/$filename";

        if(is_file($fullpath)){
            error_log("downloading file: $fullpath");

            $size = filesize($fullpath);
            $mimeType = mime_content_type($fullpath);

            // Output headers.
            header("Cache-Control: private");
            header("Content-Type: $mimeType");
            header("Content-Length: $size");
            header("Content-Disposition: attachment; filename=$filename");

            // Output file.
            readfile ($fullpath);
            exit();
        }else{
            error_log("filename does not exist '$filename'");

            abort(500, "File was not found");
        }
    }
}

