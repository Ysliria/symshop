<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected SluggerInterface $slugger;
    protected UserPasswordHasherInterface $passwordHasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;

        $hash = $this->passwordHasher->hashPassword($admin, 'test');

        $admin->setEmail('admin@gmail.com')
            ->setPassword($hash)
            ->setFullName('Admin')
            ->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($admin);

        $users = [];

        for ($u = 0; $u < 5; $u++) {
            $user = new User;

            $hash = $this->passwordHasher->hashPassword($user, 'test');

            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);

            $users[] = $user;

            $manager->persist($user);
            
        }

        for ($c = 0; $c < 3; $c++) {
            $category = new Category;

            $category->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
        
            $manager->persist($category);

            for ($p = 0, $pmax = mt_rand(15, 20); $p < $pmax; $p++) {
                $product = new Product;
            
                $product->setName($faker->productName)
                    ->setPrice($faker->price(4000, 2000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true));
    
                $manager->persist($product);
            }
        }

        for ($p = 0; $p < mt_rand(20, 40); $p++) {
            $purchase = new Purchase;

            $purchase->setFullName($faker->name())
                ->setAddress($faker->streetAddress())
                ->setPostalCode($faker->postcode())
                ->setCity($faker->city())
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchasedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months')))
            ;

            if($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);
        }
        

        $manager->flush();
    }
}
