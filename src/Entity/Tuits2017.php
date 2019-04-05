<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tuits2017
 *
 * @ORM\Table(name="tuits_2017")
 * @ORM\Entity
 */
class Tuits2017
{
    /**
     * @var string
     *
     * @ORM\Column(name="ID", type="decimal", precision=25, scale=0, nullable=false)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="NUM", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $num;


}
