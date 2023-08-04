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
	private array $tricks = ["La manière de rider" => "Tout d'abord, il faut savoir qu'il y a deux positions sur sa planche: regular ou goofy. Un rider regular aura son pied gauche devant et un rider goofy aura son pied droit devant. Après un certain moment, les planchistes sont capables de descendre dans les deux positions. Un rider regular qui descend en position goofy, dira qu'il descend « switch ». Généralement, une manœuvre sera considéré comme plus difficile quand elle est effectué « switch ».",
		"Les grabs" => "Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper. » Il existe plusieurs types de grabs selon la position de la saisie et la main choisie pour l'effectuer,",
		"Les rotations" => "On désigne par le mot « rotation » uniquement des rotations horizontales ; les rotations verticales sont des flips. Le principe est d'effectuer une rotation horizontale pendant le saut, puis d'atterrir en position switch ou normal. La nomenclature se base sur le nombre de degrés de rotation effectués",
		"Les flips" => "Un flip est une rotation verticale. On distingue les front flips, rotations en avant, et les back flips, rotations en arrière. Il est possible de faire plusieurs flips à la suite, et d'ajouter un grab à la rotation.",
		"Les rotations désaxées" => "Une rotation désaxée est une rotation initialement horizontale mais lancée avec un mouvement des épaules particulier qui désaxe la rotation. Il existe différents types de rotations désaxées (corkscrew ou cork, rodeo, misty, etc.) en fonction de la manière dont est lancé le buste. Certaines de ces rotations, bien qu'initialement horizontales, font passer la tête en bas.",
		"Les slides" => "Un slide consiste à glisser sur une barre de slide. Le slide se fait soit avec la planche dans l'axe de la barre, soit perpendiculaire, soit plus ou moins désaxé.",
		"Les one foot tricks" => "Figures réalisée avec un pied décroché de la fixation, afin de tendre la jambe correspondante pour mettre en évidence le fait que le pied n'est pas fixé. Ce type de figure est extrêmement dangereuse pour les ligaments du genou en cas de mauvaise réception.",
		"Old school" => "Le terme old school désigne un style de freestyle caractérisée par en ensemble de figure et une manière de réaliser des figures passée de mode, qui fait penser au freestyle des années 1980 - début 1990 (par opposition à new school)",
		"Backside Air" => "On commence tout simplement avec LE trick. Les mauvaises langues prétendent qu’un backside air suffit à reconnaître ceux qui savent snowboarder. Si c’est vrai, alors Nicolas Müller est le meilleur snowboardeur du monde. Personne ne sait s’étirer aussi joliment, ne demeure aussi zen, n’est aussi provocant dans la jouissance.",
		"Switch Backside Rodeo 720" => "Si l’univers du snowboard a jamais disposé d’un scientifique, alors c’était David Benedek. Personne mieux que lui n’a su comment monter un kicker pour qu’un trick marche bien. En musique, on qualifie cela d’expérimental. Ce n’est pas un hasard si c’est précisément lui qui a eu l’idée de faire un switch BS rodeo.",
		"BS 540 Seatbelt" => "Hitsch aurait tout aussi bien pu faire de la danse classique mais il s’est décidé pour la neige. Peut-être tout simplement parce qu’en Engadine, les montagnes sont plus séduisantes que les gymnases. Quoi qu’il en soit, quiconque arrive à attraper aussi tranquillement l’arrière de la planche avec la main avant pendant un BS 5 dans un half-pipe sans s’ouvrir les lèvres sur le coping devrait occuper une chaire à Cambridge sur les prodiges de la coordination.",
		"FS 720 Japan" => "Si, dans le monde du snowboard, il y avait aujourd’hui encore quelque chose de comparable au rock’n’roll, Ben Ferguson en serait le Jim Morrison, haut la main. Son riding est radical, sans compromis et beau à voir. Bien entendu, rien ne se fait à moins de 200 km/h, pas même les FS 7 Japan dans le pipe.",
		"Skate Skills" => "Scott «MacGyver» Stevens n’aurait en fait pas besoin de forfait de remontée. Scott n’aurait même pas besoin d’aller à la montagne. Scott a juste à sortir de chez lui, respirer un bon coup et démarrer. Après trois jours de tournage, son rôle serait plus long et plus créatif que pour ceux qui ont dû passer 20 heures en avion, 10 heures en voiture, 5 heures en Ski-Doo et 2 heures en hélicoptère pour leur séquence."];

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
		$imageNumber = 0;

		foreach($this->tricks as $name => $description)
		{

			$selectedUserObject = $this->objectUsersArray[mt_rand(0, $lengthUserArray)];
			$selectedGroupeObject = $this->objectsGroupeArray[mt_rand(0, $lengthGroupeArray)];

			$this->figure = new Figure();

			$this->figure->setName($name);
			$this->figure->setDescription($description);
			$this->figure->setCreatedAt();
			$this->figure->setGroupe($selectedGroupeObject);
			$this->figure->setUser($selectedUserObject);
			$this->figure->setSlug($this->sluggerService->makeSlug($this->figure->getName()));

			self::loadMessages($manager, $selectedUserObject);
			self::loadImage($imageNumber, $manager, $selectedUserObject);

			$imageNumber++;
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

	public function loadImage(int $imageNumber,ObjectManager $manager, User $selectedUserObject): void
	{
			$image = new Image();
			$image->setPath("picture-${imageNumber}.jpg");
			$image->setFigure($this->figure);
			$image->setUser($selectedUserObject);
			$image->setBanner(true);

			$manager->persist($image);
	}
}
