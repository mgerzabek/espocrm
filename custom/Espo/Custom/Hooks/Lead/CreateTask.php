<?php

namespace Espo\Custom\Hooks\Lead;

use Espo\Core\Hook\Hook\AfterSave;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Repository\Option\SaveOptions;

class CreateTask implements AfterSave
{
    public function __construct(private EntityManager $entityManager) {}

    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        if ($entity->isNew()) {
            $task = $this->entityManager->getNewEntity('Task');
            $task->set([
                'name' => 'Follow-up: Contact ' . $entity->get('name'),
                'assignedUserId' => $entity->get('assignedUserId'),
                'parentType' => 'Lead',
                'parentId' => $entity->get('id'),
                'status' => 'Not Started',
                'dateStart' => (new \DateTime())->format('Y-m-d H:i:s'),
                'dateEnd' => (new \DateTime('+7 days'))->format('Y-m-d H:i:s'),
                'description' => $entity->get('description'),
            ]);
            $this->entityManager->saveEntity($task);
        }
    }
}