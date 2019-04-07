<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Libro2
 *
 * @ORM\Table(name="libro2")
 * @ORM\Entity
 */
class Libro2
{
    /**
     * @var int
     *
     * @ORM\Column(name="LOTE", type="integer", nullable=false)
     */
    private $lote;

    /**
     * @var int
     *
     * @ORM\Column(name="NUM", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $num;

    /**
     * @var string
     *
     * @ORM\Column(name="TUIT", type="text", length=65535, nullable=false)
     */
    private $tuit;

    /**
     * @var string
     *
     * @ORM\Column(name="HASHTAG", type="string", length=45, nullable=false, options={"fixed"=true})
     */
    private $hashtag;


}
