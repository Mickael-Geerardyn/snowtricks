<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Groupe;
use App\Entity\Image;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\GroupeRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
	private Figure $figure;
	private User $user;
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

	public function __construct()
	{
		$this->objectUsersArray = array();
		$this->objectsGroupeArray = array();
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
			$this->user = new User();
			$this->user->setName($user["name"]);
			$this->user->setEmail($user["email"]);
			$this->user->setPassword($user["password"]);
			$this->user->setCreatedAt();
			$this->user->setIsVerified(true);

			$this->objectUsersArray[] = $this->user;

			$manager->persist($this->user);
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

			self::loadMessages($manager, $selectedUserObject);
			self::loadImage($i, $manager, $selectedUserObject);

			$manager->persist($this->figure);
		}
	}

	public function loadMessages(ObjectManager $manager, $selectedUserObject): void
	{
		$maxNumbers = mt_rand(1, 10);

		for ($i = 0; $i < $maxNumbers; $i++)
		{
			$message = new Message();

			$message->setContent("Contenu du message numéro ${i}");
			$message->setCreatedAt();
			$message->setFigure($this->figure);
			$message->setUser($selectedUserObject);

			$manager->persist($message);
		}
	}

	public function loadImage(int $i, ObjectManager $manager, $selectedUserObject): void
	{
		$image = new Image();
		$image->setPath("picture-${i}.jpg");
		$image->setCreatedAt();
		$image->setFigure($this->figure);
		$image->setUser($selectedUserObject);

		$manager->persist($image);
	}
}
