<?php

namespace App\Models\Mailer;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    public $variables;
    protected $table = 'mailer_templates';

    protected $fillable = [
        'name', 'description', 'body'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->variables = self::getVariables();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mailer_attachments() {
        return $this->hasMany(MailerAttachment::class);
    }

    /**
     * @return Collection
     */
    public static function getVariables(): Collection
    {
        return new Collection([
            new TemplateVariable('user_name', 'name'),
            new TemplateVariable('user_phone', 'phone'),
            new TemplateVariable('user_email', 'email'),
        ]);
    }

    /**
     * @param array $values
     * @return bool|mixed
     */
    public function setVariables(array $values)
    {
        $message = $this->body;

        if (count($values)) {
            foreach($values as $key => $value) {
                $needle = "#" . strtoupper($key) . "#";
                $message = str_replace($needle, $value, $message);
            }

            return $message;
        }

        return false;
    }

    public function prepareBody(ContactList $contactList)
    {

        $message = iconv('ISO-8859-1', 'UTF-8', $this->body);
        dd($message);

        $variables = [];

        foreach ($this->variables as $variable) {
            $variables[$variable->name] = $contactList->{$variable->column};
        }

        if (count($variables)) {
            foreach ($variables as $key => $variable) {
                if (is_null($variable)) {
                    $variable = "NONE";
                }

                $needle = "#" . strtoupper($key) . "#";
                $message = str_replace($needle, $variable, $message);
            }
        }

        $this->message = $message;
        return $this->message;
    }

}
