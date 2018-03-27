<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Model\AccessLog;

class Controller extends BaseController
{
  /**
  * For Generate custom response from api.
  *
  * @return \Illuminate\Http\Response
  */
  public function generate_response(&$params, $code, $message, $is_error)
  {
    if($is_error){
      $result['isError'] = true;
      $result['errorCode'] = $code;
      $result['message'] = $message;
      $result['data'] = $params;
    }else{
      $result['isError'] = false;
      $result['errorCode'] = $code;
      $result['message'] = $message;
      $result['data'] = $params;
    }

    return $result;
  }

  public function uploadFile($path, $base64,$last_image = ''){
        /* Upload function */
        $data = base64_decode($base64);
        $extension = $this->getImageMimeType($data);
        $filename = sha1(time()).".".$extension;
        $destinationPath = $path . $filename;

        /** Explode Image Full Path into name only */
        $temp_image = explode('/',$last_image);
        $last_image = $temp_image[count($temp_image)-1];

        if(file_put_contents($destinationPath, $data)){
            if($last_image!='' && $last_image != 'default_advertisement.png' && $last_image != 'default_place.png' && $last_image != 'default_img.png'){
                $path = str_replace('/','\\',$path);
                @unlink($path.$last_image);
            }
            return $filename;
        }else{
            return false;
        };
  }

  /**
  * Get Type File from base_64.
  *
  * @return \Illuminate\Http\Response
  */

  public function getImageMimeType($imagedata)
  {
      $imagemimetypes = array(
      "jpeg" => "FFD8",
      "png" => "89504E470D0A1A0A",
      "gif" => "474946",
      "bmp" => "424D",
      "tiff" => "4949",
      "tiff" => "4D4D"
      );

      foreach ($imagemimetypes as $mime => $hexbytes)
      {
          $bytes = $this->getBytesFromHexString($hexbytes);
          if (substr($imagedata, 0, strlen($bytes)) == $bytes)
          return $mime;
      }
      return NULL;
  }

  /**
  * Parse Type base_64 file.
  *
  * @return \Illuminate\Http\Response
  */

      public function getBytesFromHexString($hexdata)
      {
          for($count = 0; $count < strlen($hexdata); $count+=2)
          $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));
          return implode($bytes);
      }

    public function create_access_log($params)
    {
        $result = AccessLog::create($params);

        return $result->id;
    }

    public function update_access_log($access_log_id, $arr_result)
    {
        $access_log = AccessLog::find($access_log_id);

        $access_log->result = json_encode($arr_result);

        $access_log->save();
    }

  /**
  * Access public path on lumen.
  *
  * @return \Illuminate\Http\Response
  */
  function public_path($path = null)
  {
      return rtrim(app()->basePath('public/' . $path), '/');
  }

  /**
  * Get All Table Name
  *
  * @return \Illuminate\Http\Response
  */
  function get_table()
  {
      $table[] = 'advertisements';
      $table[] = 'cities';
      $table[] = 'events';
      $table[] = 'feedbacks';
      $table[] = 'info_payments';
      $table[] = 'messages';
      $table[] = 'migrations';
      $table[] = 'packages';
      $table[] = 'package_cities';
      $table[] = 'package_details';
      $table[] = 'pictures';
      $table[] = 'plans';
      $table[] = 'plan_details';
      $table[] = 'provinces';
      $table[] = 'roles';
      $table[] = 'special_deals';
      $table[] = 'tip_tricks';
      $table[] = 'tourism_places';
      $table[] = 'users';

      $result = '';
      $i      = 0;
      foreach($table as $t){
          $result .= (count($table)-1)==$i ? $t : $t.',';
          $i++;
      }

      return $result;
  }
}
