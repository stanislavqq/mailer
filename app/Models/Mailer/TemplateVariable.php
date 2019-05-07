<?php

namespace App\Models\Mailer;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class TemplateVariable
 * Local model without database connection
 * @package App\Models\Mailer
 */
class TemplateVariable implements Arrayable
{
    private static $keyIterator = 0;

    public $id;
    public $name;
    public $column;

    /**
     * TemplateVariable constructor.
     * @param string $name
     * @param string $column
     */
    public function __construct(string $name, string $column)
    {
        $this->id = self::$keyIterator++;
        $this->name = $name;
        $this->column = $column;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'column' => $this->column
        ];
    }
}