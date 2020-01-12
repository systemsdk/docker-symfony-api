<?php
declare(strict_types = 1);
/**
 * /src/Entity/Interfaces/UserInterface.php
 */

namespace App\Entity\Interfaces;

/**
 * Interface UserInterface
 *
 * @package App\Entity\Interfaces
 */
interface UserInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return string
     */
    public function getEmail(): string;
}
