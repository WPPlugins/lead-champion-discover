Il progetto è gestito mediante "git-svn" per tenere sincronizzato
il repository git locale e il repository svn remoto di Wordpress.

Segue l'elenco dei comandi usati per creare il repository mixed
e al fondo i comandi per gestire le modifiche.

---------------------------------------------------------------
# Visualizzo tutte le revision del progetto in Wordpress e prendo la più vecchia
svn log https://plugins.svn.wordpress.org/lead-champion-discover

---------------------------------------------------------------
# Si sceglie la revision di creazione del progetto e si clona la struttura delle cartelle svn vuota
git svn clone --no-minimize-url -s -r1499299 https://plugins.svn.wordpress.org/lead-champion-discover

---------------------------------------------------------------
# Entrando nella directory di checkout del progetto si esegue la fetch di tutte le revisioni.
# Siccome il repository svn di Wordpress è gigantesco è necessario usare l'opzione --log-window-size 10000
# che prende in considerazione solo la parte più recente del repository, altrimenti bisognerebbe aspettare ore!
cd lead-champion-discover

git svn fetch --log-window-size 10000

git svn rebase

git branch -a

---------------------------------------------------------------
# Dopo avere caricato in git il branch relativo ai sorgenti sotto trunk, aggiungiamo un branch per salvare i file
# che si trovano sotto assets perché è fuori dalla gerarchia delle normali directory di svn

git config --add svn-remote.assets.url https://plugins.svn.wordpress.org/lead-champion-discover/assets

git config --add svn-remote.assets.fetch :refs/remotes/assets

git svn fetch -r HEAD assets

git checkout remotes/assets

git checkout -b assets

---------------------------------------------------------------
# Una volta completati i due branch si può passare dal trunk ai contenuti di assets nel seguente modo:
git checkout assets
git checkout master

---------------------------------------------------------------
# Esempi d'uso
git checkout master
# modifiche locali ...
git add .

git commit -m 'xxx'

# mando le modifiche a git
git push

# mando le modifica ad svn
git svn dcommit

# per creare un tag in git
git tag v1.1.1
git push --tags

# per creare l'analogo tag in svn andrebbe fatto
git svn tag 1.1.1
# siccome "git svn tag" ha problemi di autenticazione
# appena dopo aver creato il tag git, creare il tag svn con il comando "svn cp"
svn cp https://plugins.svn.wordpress.org/lead-champion-discover/trunk https://plugins.svn.wordpress.org/lead-champion-discover/tags/1.1.1

---------------------------------------------------------------
# per resettare un eventuale problema e buttare le commit locali - attenzione che serve il carattere '^' dopo HEAD
git reset --hard HEAD^
