<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Nettoyer les tables
        Comment::truncate();
        Task::truncate();
        Project::truncate();
        User::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('🚀 SEEDING BASE DE DONNÉES');
        $this->command->info('========================================');
        $this->command->info('');

        // 1. Création de l'administrateur
        $admin = User::create([
            'name' => 'Administrateur',
            'email' => 'admin@taskmanager.com',
            'password' => Hash::make('password'), // IMPORTANT: utiliser Hash::make()
            'role' => 'admin',
        ]);
        $this->command->info('✅ Admin créé: admin@taskmanager.com / password');

        // 2. Création des utilisateurs normaux
        $users = [];
        $userEmails = ['user1@taskmanager.com', 'user2@taskmanager.com', 'user3@taskmanager.com', 'user4@taskmanager.com', 'user5@taskmanager.com'];
        $userNames = ['Sophie Martin', 'Thomas Bernard', 'Emma Petit', 'Lucas Robert', 'Julie Richard'];
        
        for ($i = 0; $i < 5; $i++) {
            $users[] = User::create([
                'name' => $userNames[$i],
                'email' => $userEmails[$i],
                'password' => Hash::make('password'), // IMPORTANT: utiliser Hash::make()
                'role' => 'user',
            ]);
        }
        $this->command->info('✅ 5 utilisateurs créés');

        // 3. Création des projets (comme avant)
        $projects = [];
        
        // Projets admin
        for ($i = 1; $i <= 3; $i++) {
            $projects[] = Project::create([
                'title' => "Projet Admin $i",
                'description' => "Description du projet admin $i",
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'status' => 'active',
                'created_by' => $admin->id
            ]);
        }
        
        // Projets utilisateurs
        foreach ($users as $user) {
            for ($i = 1; $i <= 2; $i++) {
                $projects[] = Project::create([
                    'title' => "Projet de {$user->name} - $i",
                    'description' => "Description du projet de {$user->name}",
                    'start_date' => now(),
                    'end_date' => now()->addDays(20),
                    'status' => ['planning', 'active', 'on_hold'][array_rand(['planning', 'active', 'on_hold'])],
                    'created_by' => $user->id
                ]);
            }
        }
        
        // 4. Création des tâches
        foreach ($projects as $project) {
            for ($i = 1; $i <= 3; $i++) {
                $assignedUser = $users[array_rand($users)];
                Task::create([
                    'project_id' => $project->id,
                    'assigned_to' => $assignedUser->id,
                    'title' => "Tâche $i du projet {$project->title}",
                    'description' => "Description de la tâche $i",
                    'priority' => ['low', 'medium', 'high', 'urgent'][array_rand(['low', 'medium', 'high', 'urgent'])],
                    'status' => ['pending', 'in_progress', 'review', 'completed'][array_rand(['pending', 'in_progress', 'review', 'completed'])],
                    'deadline' => now()->addDays(rand(1, 15))
                ]);
            }
        }
        
        // 5. Création des commentaires
        $tasks = Task::all();
        foreach ($tasks as $task) {
            for ($c = 1; $c <= 2; $c++) {
                Comment::create([
                    'commentable_type' => Task::class,
                    'commentable_id' => $task->id,
                    'user_id' => $users[array_rand($users)]->id,
                    'content' => "Commentaire $c sur cette tâche"
                ]);
            }
        }
        
        $this->command->info('✅ ' . count($projects) . ' projets créés');
        $this->command->info('✅ ' . Task::count() . ' tâches créées');
        $this->command->info('✅ ' . Comment::count() . ' commentaires créés');
        
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✨ SEED TERMINÉ AVEC SUCCÈS !');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('🔑 COMPTES DE CONNEXION:');
        $this->command->info('   📧 Admin: admin@taskmanager.com');
        $this->command->info('   📧 Users: user1@taskmanager.com à user5@taskmanager.com');
        $this->command->info('   🔐 Mot de passe: password');
        $this->command->info('');
    }
}