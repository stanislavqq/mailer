<?php
/**
 * Created by PhpStorm.
 * User: Stas
 * Date: 25.04.2019
 * Time: 18:29
 */

namespace App\Models\Mailer;


use Illuminate\Http\UploadedFile;
use phpDocumentor\Reflection\File;
use Psr\Http\Message\UploadedFileInterface;

class MailerFile implements UploadedFileInterface
{
    const ATTACH_DIRECTORY = 'mailer_attachments';

    protected $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function save() {

    }
}