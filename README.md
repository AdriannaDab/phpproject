Serwis ogłoszeniowy - instalacja
1. Struktura bazy danych w postaci pliku skryptu SQL dostępna w pliku src/data/serwis-ogl.sql. 
2. Zaimportować tą bazę danych na serwer.
3. Należy wypakować katalogi /src i /web oraz pliki composer.json i composer.phar do folderu /ścieżka/do/projektu.
4. W terminalu wpisać komendy:
  cd /ścieżka/do/projektu
  php composer.phar install
4. Przejść do pliku ścieżka/do/projektu/web/index.php i zmienić dane 'databasename', 'user' i 'password' podając własne dane dostępowe do bazy danych: 
  'db.options' => array( 'driver' => 'pdo_mysql', 'host' => 'localhost', 'dbname' => 'databasename', 'user' => 'user', 'password' => 'password', 'charset' => 'utf8', ).
5. Przejść do pliku /ścieżka/do/projektu/web/.htaccess podać ścieżkę do katalogu web:
   Options -MultiViews RewriteEngine On RewriteBase {TWOJA_ŚCIEŻKA}/web/ RewriteCond %{REQUEST_FILENAME} !-f RewriteRule ^ index.php [QSA,L]
6. Nadać prawa plikowi:
   chmod 777 web/media/
7. Dane do logowania w aplikacji (należy zmienić je podczas pierwszego logowania):
   administrator
   login: admin
   hasło: password
   moderator
   login: moderator
   hasło: password
   user
   login: user
   hasło: password
8. Dokumentacja projektu dostępna jest w folderze src/docs
