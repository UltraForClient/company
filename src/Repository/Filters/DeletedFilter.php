<?php declare(strict_types=1);

namespace App\Repository\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DeletedFilter extends SQLFilter
{

    public function addFilterConstraint(ClassMetadata $targetEntity, $alias): string
    {
        if($targetEntity->hasField('deletedAt')) {
            return $alias . '.deleted_at IS NULL';
        }
        return '';
    }
}