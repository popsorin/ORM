<?php

namespace ReallyOrm\Test\Entity;

use ReallyOrm\Entity\AbstractEntity;

class QuizProperties extends AbstractEntity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $questions;

    /**
     * @var string
     */
    private $answers;

    /**
     * @var int
     */
    private $grade;
}