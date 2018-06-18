<?php declare(strict_types=1);

namespace App\Generate;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class Username
{
    private $em;
    private $slugify;

    public function __construct(EntityManagerInterface $em, Slugify $slugify)
    {
        $this->em = $em;
        $this->slugify = $slugify;
    }

    public function createUsername(string $name, string $surname): string
    {
        $username = $this->slugify->slugify($name) . '.' . $this->slugify->slugify($surname);
        if ($this->isSetUsername($username)) {
            $counter = 1;
            do {
                $username .= $counter;
                $counter++;
            } while ($this->isSetUsername($username));
        }
        return $username;
    }

    private function isSetUsername(string $username): bool
    {
        $allUser = $this->em->getRepository(User::class)->findAllUsername();
        return in_array($username, $allUser);
    }
}