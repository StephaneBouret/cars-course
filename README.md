# On clone le dépot !
git clone https://github.com/StephaneBouret/cars-course.git

# On se déplace dans le dossier
cd cars-course

# On installe les dépendances !
composer install puis,
npm install

# On créé la base de données
php bin/console doctrine:database:create

# On exécute les migrations
php bin/console doctrine:migrations:migrate

# On exécute la fixture
php bin/console doctrine:fixtures:load --no-interaction

# On lance le serveur
php bin/console server:run
