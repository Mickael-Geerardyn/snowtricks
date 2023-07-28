<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Groupe;
use App\Entity\Image;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\GroupeRepository;
use App\Service\SluggerService;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
	private Figure $figure;
	private array $users =
		[
			["email" => "contact@mickael-geerardyn.com", "name" => "Mickaël", "password" => "password"],
			["email" => "kelly.l@gmail.com", "name" => "Kelly", "password" => "password"],
			["email" => "freddy.g@gmail.com", "name" => "Freddy", "password" => "password"]
		];
	private array $objectUsersArray;

	private array $objectsGroupeArray;

	private array $groupeTypes =
		["grabs", "rotations", "flips", "rotations désaxées", "slides", "one foot tricks", "Old school"];
	private SluggerService $sluggerService;

	private UserPasswordHasherInterface $userPasswordHasher;

	public function __construct(SluggerService $sluggerService, UserPasswordHasherInterface $userPasswordHasher)
	{
		$this->objectUsersArray = array();
		$this->objectsGroupeArray = array();
		$this->sluggerService = $sluggerService;
		$this->userPasswordHasher = $userPasswordHasher;
	}

    public function load(ObjectManager $manager): void
    {

		self::loadUser($manager);
		self::loadGroupe($manager);
		self::loadFigure($manager);

        $manager->flush();
    }

	public function loadUser(ObjectManager $manager): void
	{
		foreach($this->users as $user)
		{
			$user1 = new User();
			$user1->setName($user["name"]);
			$user1->setEmail($user["email"]);
			$user1->setPassword($this->userPasswordHasher->hashPassword($user1, $user["password"]));
			$user1->setCreatedAt(new DateTimeImmutable());
			$user1->setIsVerified(true);

			$this->objectUsersArray[] = $user1;

			$manager->persist($user1);
		}
	}

	public function loadGroupe(ObjectManager $manager): void
	{
		foreach($this->groupeTypes as $type)
		{
			$groupe = new Groupe();
			$groupe->setName($type);
			$groupe->setCreatedAt();
			$this->objectsGroupeArray[] = $groupe;

			$manager->persist($groupe);
		}
	}

	public function loadFigure(ObjectManager $manager): void
	{
		$lengthUserArray = count($this->objectUsersArray) -1;
		$lengthGroupeArray = count($this->objectsGroupeArray) -1;

		for ($i = 0; $i < 10; $i++)
		{

			$selectedUserObject = $this->objectUsersArray[mt_rand(0, $lengthUserArray)];
			$selectedGroupeObject = $this->objectsGroupeArray[mt_rand(0, $lengthGroupeArray)];

			$this->figure = new Figure();

			$this->figure->setName("Nom de la figure numéro ${i}");
			$this->figure->setDescription("Description de la figure numéro ${i}");
			$this->figure->setCreatedAt();
			$this->figure->setGroupe($selectedGroupeObject);
			$this->figure->setUser($selectedUserObject);
			$this->figure->setSlug($this->sluggerService->makeSlug($this->figure->getName()));

			self::loadMessages($manager, $selectedUserObject);
			self::loadImage($i, $manager, $selectedUserObject);

			$manager->persist($this->figure);
		}
	}

	public function loadMessages(ObjectManager $manager, User $selectedUserObject): void
	{
		$maxNumbers = mt_rand(1, 40);

		for ($i = 0; $i < $maxNumbers; $i++)
		{
			$message = new Message();

			$message->setContent("Contenu du message numéro ${i}");
			$message->setFigure($this->figure);
			$message->setUser($selectedUserObject);

			$manager->persist($message);
		}
	}

	public function loadImage(int $i, ObjectManager $manager, User $selectedUserObject): void
	{
		$image = new Image();
		$image->setPath("picture-${i}.jpg");
		$image->setFigure($this->figure);
		$image->setUser($selectedUserObject);
		$image->setBanner(true);

		$manager->persist($image);
	}
}
