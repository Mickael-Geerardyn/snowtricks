<?php

namespace App\Repository;

use App\Entity\Figure;
use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 *
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Image::class);
	}

	public function save(Image $entity, bool $flush = false): void
	{
		$this->getEntityManager()->persist($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	public function remove(Image $entity, bool $flush = false): void
	{
		$this->getEntityManager()->remove($entity);

		if ($flush) {
			$this->getEntityManager()->flush();
		}
	}

	/**
	 * @param Figure $figure
	 *
	 * @return Image[] Returns an array of Image objects
	 */
	public function findImagesByFigure(Figure $figure): array
	{
		return $this->createQueryBuilder("image")
					->andWhere("image.figure = :figure")
					->setParameter("figure", $figure)
					->orderBy("image.created_at", "DESC")
					->getQuery()
					->getResult();
	}
}
