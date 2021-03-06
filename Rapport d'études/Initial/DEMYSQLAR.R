## /!\ Ne pas oublier de d�connecter en faisant tourner la derni�re ligne 


## 1 - Mise en place des packages n�cessaires
install.packages("RMySQL")
library("RMySQL")

## 2 - Connecting to MySQL
## Once the RMySQL library is installed create a database connection object.
## ICI remplir les champs : type de serveur, user, password, la base � charger, et le serveur hote
mydb = dbConnect(MySQL(), user='root', password='', dbname='rc2009', host='localhost')

## 3 - Visualisation de l'importation de donn�es
## Listing Tables and Fields, i.e. tables dans la BDD de charg�e
dbListTables(mydb)
## Variables de la table choisie, A REMPLIR
dbListFields(mydb, 'fd_logemtzc_2013')

## 4 - Importation des donn�es n�cessaires
## Ecrire dans objet R1 la requ�te en SQL des �l�ments � s�lectionner, tout ou partie
R2 <- "SELECT * FROM rc2009.fd_logemtzc_2009 where commune='35278' "
## R�cup�ration des �l�ments d�crits dans la requ�te Ri
B2 <- dbGetQuery(mydb, R2)

## 4 bis - Cr�ation d'une autre sous-base : copier/coller bloc n�4 et ajouter +1 � Ri & Bi

##DECONNEXION !!
dbDisconnect (mydb)