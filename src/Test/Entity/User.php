<?php


namespace ReallyOrm\Test\Entity;


use ReallyOrm\Entity\AbstractEntity;


class User extends AbstractEntity
{
    /**
     * @var string
     * @MappedOn name
     */
    private $name;

    /**
     * @var string
     * @MappedOn email
     */
    private $email;

    public function setName(string $string)
    {
        $this->name = $string;
    }

    public function setEmail(string $string)
    {
        $this->email = $string;
    }

}