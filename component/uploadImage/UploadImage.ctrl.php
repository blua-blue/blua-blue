<?php
/* Generated by neoan3-cli */

namespace Neoan3\Components;

use Neoan3\Apps\Stateless;
use Neoan3\Frame\Neoan;
use Neoan3\Model\ImageModel;

class UploadImage extends Neoan {

    function postUploadImage($obj){
        $jwt = Stateless::restrict();
        //TODO: quota
        $id = ImageModel::saveFromBase64($obj['image'],$jwt['jti']);
        return ['uploadId'=>$id];
    }
    function getUploadImage($image){
        $jwt = Stateless::restrict();
        return ImageModel::byId($image['id']);
    }

}
