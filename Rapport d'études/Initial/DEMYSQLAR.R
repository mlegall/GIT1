## /!\ Ne pas oublier de déconnecter en faisant tourner la dernière ligne 


## 1 - Mise en place des packages nécessaires
install.packages("RMySQL")
library("RMySQL")

## 2 - Connecting to MySQL
## Once the RMySQL library is installed create a database connection object.
## ICI remplir les champs : type de serveur, user, password, la base à charger, et le serveur hote
mydb = dbConnect(MySQL(), user='root', password='', dbname='rc2009', host='localhost')

## 3 - Visualisation de l'importation de données
## Listing Tables and Fields, i.e. tables dans la BDD de chargée
dbListTables(mydb)
## Variables de la table choisie, A REMPLIR
dbListFields(mydb, 'fd_logemtzc_2013')

## 4 - Importation des données nécessaires
## Ecrire dans objet R1 la requête en SQL des éléments à sélectionner, tout ou partie
R2 <- "SELECT * FROM rc2009.fd_logemtzc_2009 where commune='35278' "
## Récupération des éléments décrits dans la requête Ri
B2 <- dbGetQuery(mydb, R2)

## 4 bis - Création d'une autre sous-base : copier/coller bloc n°4 et ajouter +1 à Ri & Bi

##DECONNEXION !!
dbDisconnect (mydb)