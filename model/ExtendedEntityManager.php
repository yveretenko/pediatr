<?php

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;

class ExtendedEntityManager extends EntityManagerDecorator
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function save($entity)
    {
        $errors=[];

        if (method_exists($entity, 'validate'))
            $errors=$entity->validate();

        if (!count($errors))
        {
            try
            {
                $this->persist($entity);
                $this->flush();
            }
            catch (UniqueConstraintViolationException $e)
            {
                $errors[] = method_exists($entity, 'getAlreadyExistsError') ? $entity->getAlreadyExistsError() : 'Such '.strtolower(rtrim(basename(str_replace('\\', '/', get_class($entity))), 's')).' already exists';
            }
            catch (\Exception $e)
            {
                $errors[]=$e->getMessage();
            }
        }

        return $errors;
    }
}