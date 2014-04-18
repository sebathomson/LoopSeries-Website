<?php
namespace LoopAnime\UsersBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LoopAnime\UsersBundle\Entity\Users;

class LoadEvents implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = new Users();

        $user1->setAvatar("");
        $user1->setBirthdate(new \DateTime("07-02-1990"));
        $user1->setCreateTime(new \DateTime("now"));
        $user1->setCountry("Portugal");
        $user1->setEmail("user1@test.com");
        $user1->setUsername("user1");
        $user1->setLang("EN");
        $user1->setNewsletter("0");
        $user1->setPassword("user1");
        $user1->setStatus("1");
        $manager->persist($user1);

        $user2 = new Users();

        $user2->setAvatar("");
        $user2->setBirthdate(new \DateTime("07-02-1990"));
        $user2->setCreateTime(new \DateTime("now"));
        $user2->setCountry("Portugal");
        $user2->setEmail("user2@test.com");
        $user2->setUsername("user2");
        $user2->setLang("EN");
        $user2->setNewsletter("0");
        $user2->setPassword("user2");
        $user2->setStatus("1");
        $manager->persist($user2);

        $user3 = new Users();

        $user3->setAvatar("");
        $user3->setBirthdate(new \DateTime("07-02-1990"));
        $user3->setCreateTime(new \DateTime("now"));
        $user3->setCountry("Portugal");
        $user3->setEmail("user3@test.com");
        $user3->setUsername("user3");
        $user3->setLang("EN");
        $user3->setNewsletter("0");
        $user3->setPassword("user3");
        $user3->setStatus("1");
        $manager->persist($user3);

        $manager->flush();

    }
}