<?php

namespace App\Models\Mailer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MailerAttachment extends Model
{
    protected $file;

    const MAILER_ATTACH_DIR = 'mailer_attachmens';
    const UPLOAD_DIR = DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . self::MAILER_ATTACH_DIR;

    protected $fillable = [
        'name', 'type', 'id', 'message_id', 'path', 'size'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function template()
    {
        return $this->hasMany(Template::class);
    }

    public function saveFile(UploadedFile $file, int $templateId)
    {
        $uploadsDirectory = self::UPLOAD_DIR;
        $filesPath = public_path($uploadsDirectory);
        $fileName = $file->getClientOriginalName();
        $newFileName = md5($fileName) . '_' . $templateId . '.' . $file->getClientOriginalExtension();

        $saveAttachments[] = self::create([
            'name' => $fileName,
            'type' => $file->getClientOriginalExtension(),
            'template_id' => $templateId,
            'file' => $newFileName,
            'path' => $uploadsDirectory . DIRECTORY_SEPARATOR . $newFileName,
            'size' => $file->getClientSize(),
        ]);

        $file->move($filesPath, $newFileName);

        return $saveAttachments;
    }
}
