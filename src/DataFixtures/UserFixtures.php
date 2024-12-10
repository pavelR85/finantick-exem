<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Agent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Agents
        $adminAgent = new Agent();
        $adminAgent->setUsername('admin');
        $adminAgent->setPassword($this->passwordHasher->hashPassword($adminAgent, 'adminpass'));
        $adminAgent->setRole('ROLE_ADMIN');
        $manager->persist($adminAgent);

        $agent1 = new Agent();
        $agent1->setUsername('agent1');
        $agent1->setPassword($this->passwordHasher->hashPassword($agent1, 'pass1234'));
        $agent1->setRole('ROLE_REP');
        $agent1->setSupervisor($adminAgent);
        $manager->persist($agent1);

        $agent2 = new Agent();
        $agent2->setUsername('agent2');
        $agent2->setPassword($this->passwordHasher->hashPassword($agent2, 'pass1234'));
        $agent2->setRole('ROLE_REP');
        $agent2->setSupervisor($adminAgent);
        $manager->persist($agent2);

        $agent3 = new Agent();
        $agent3->setUsername('agent3');
        $agent3->setPassword($this->passwordHasher->hashPassword($agent3, 'pass1234'));
        $agent3->setRole('ROLE_REP');
        $agent3->setSupervisor($agent1);
        $manager->persist($agent3);

        // Create Users
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername("user{$i}");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'pass1234'));
            $user->setCurrency('USD');
            $user->setTotalPnl(0);
            $user->setEquity(1000);
            $user->setAgent($agent1); // Assign to agent1
            $manager->persist($user);
        }

        for ($i = 6; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername("user{$i}");
            $user->setPassword($this->passwordHasher->hashPassword($user, 'pass1234'));
            $user->setCurrency('EUR');
            $user->setTotalPnl(0);
            $user->setEquity(1000);
            $user->setAgent($agent2); // Assign to agent2
            $manager->persist($user);
        }

        $manager->flush();
    }
}
