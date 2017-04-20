<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 19.4.2017 г.
 * Time: 12:57 ч.
 */

namespace FlorilFlowersBundle\Service\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    private $targetDir;

    /**
     * FileUploader constructor.
     * @param $targetDir
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid());
        $file->move($this->targetDir,$fileName);

        return $fileName;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }
}