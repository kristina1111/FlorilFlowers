<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 8.4.2017 г.
 * Time: 23:29 ч.
 */

namespace FlorilFlowersBundle\Doctrine;


use FlorilFlowersBundle\Entity\User\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class HashPasswordListener implements EventSubscriber
{
    /**
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getSubscribedEvents()
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof User){
            return;
        }

        $this->encodePassword($entity);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof User || $entity->getPlainPassword()===null){
            return;
        }
//        dump($entity);exit;

        $this->encodePassword($entity);
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    /**
     * @param User $entity
     */
    private function encodePassword(User $entity)
    {
        $encodedPass = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());

        $entity->setPassword($encodedPass);
    }
}