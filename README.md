Basketbola Portāls — NBA × LBS

Modern Laravel app for exploring and comparing basketball data across NBA and LBS (Latvijas Basketbola Savienība). It includes rich UIs (Tailwind), fast server-side aggregations, and an interactive cross-league player comparison explorer with dual tables, pagination, global search, and a slide-over “maximize” view.

Features

Automated DB updates using cronjobs to save api data to DB.

Dual-home experience (NBA Hub + LBS Hub) with animated hero, quick nav, and latest news.

News grid with hover reveals and responsive image handling.

LBS Player Compare (in-league): filter by seasons, leagues/sub-leagues, client-side sort, and side-by-side cards.

Cross-League Compare (NBA ↔ LBS):

Two paginated tables (NBA/LBS) rendered side-by-side.

Global (server-side) search across all pages.

Pick up to 5 players from each table and compare as mixed sets.

Slide-over panel maximize per table (click table or “Maximize”), non-destructive restore on close.

Mobile-first drawer UX.

Hero image auto-fit: works for any image aspect ratio (cover + gradient + safe top spacing).

Animations: reveal-on-scroll, subtle tilt, and A11Y-respecting reduced-motion behavior.

🧱 Tech Stack

Backend: Laravel 10+, PHP 8.1+

Database: MySQl

Frontend: Blade, Tailwind CSS, vanilla JS

Data:

nba_players, nba_player_game_logs (NBA) etc.

players, teams, leagues, player_game_stats, games (LBS) etc.

--------------------------------------------------------------------------------------------------------------------------------------------------
KOMANDAS LAI PALAISTU PROJEKTU

composer install

cp .env.example .env
//samainat ko jums vajag samainit (DB vārdu)

php artisan key:generate

php artisan db:seed

php artisan db:seed --class=AdminUserSeeder
//admin lietotāja izveide 
// PAROLE: password
// EPASTS: admin@example.com

php artisan storage:link

//šo laikam pagaidām nevajag bet neko sliktu nedara

npm install

php artisan serve

Runājot par nba datu iegūšanu ir tā
vajadzīgs api keys un URL

kā arī CMD palaistas komandas
php artisan queue:work
un šo nba datubāzes pildīšanu var palaist admin paneļa galvenajā skatā ar pogu "atjaunināt"

pašlaik cik testēts iespējams ar pirmo reizi uzmetīs timeout erroru tajā gadījumā ir jāspeiž poga velreiz.
ŠIS ERROR IR TIKAI SAGLABĀJOT DATUS PIRMO REIZI.

