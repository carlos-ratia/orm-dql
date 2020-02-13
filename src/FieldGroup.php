<?php
declare(strict_types=1);


namespace Cratia\ORM\DQL;


use Cratia\ORM\DQL\Interfaces\IField;

/**
 * Class FieldGroup
 * @package Cratia\ORM\DQL
 */
class FieldGroup
{
    /**
     * @var IField[]
     */
    private $fields = [];


    /**
     * FieldGroup constructor.
     */
    public function __construct()
    {
        $this->fields = [];
    }

}