<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

	public function getPaginatedComments( $figure, int $page = 1, $maxResult = 10): Paginator
	{
		$query = $this->createQueryBuilder("comments")
			->leftJoin('comments.figure', 'figure')
			->andWhere("figure.slug = :figureSlug")
			->setParameter(":figureSlug", $figure->getSlug())
			->orderBy("comments.created_at", "DESC")
			->getQuery();

		$paginator = new Paginator($query);

		$paginator->getQuery()
			->setFirstResult($maxResult * ($page - 1))
			->setMaxResults($maxResult);

		return $paginator;
	}
}
