<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tuits
 *
 * @ORM\Table(name="tuits", uniqueConstraints={@ORM\UniqueConstraint(name="ID", columns={"ID"})})
 * @ORM\Entity
 */
class Tuits
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
