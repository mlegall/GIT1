<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//FR"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<head>
<title>
		OBJET : Resultats des projections
</title>
<style>
body{ background-color: pink;}
	#container{
   float:right;
   height:px;
   width:1200px; 
   marginright : 100px;
}
	container2{
   float:right;
   height:auto;
   width:auto; 
}
 TAB{
   float:left; 
   height: auto;
   width: auto; }
input.button, textarea{background-color: pink;}
</style>
	
<link rel="stylesheet" href="style.css" />
<-- Logo -->
<h1>
<img alt="logo MID" src="logo_audiar_RVB.png" align="right" height="150" width="130">

<-- Titre -->
<b><i>MODULE DE PROJECTION</b></i></img>

<-- Formulaire ICF -->
<h2>
	<form action="PROJ_2905A.php" method="post">
	<p><b>ICF</b>
		<input type="float" name="ICF" /> <!-- La valeur à entrer est au format décimal -->
		<input type="submit" value="OK" class="button"/> <!-- Bouton d'envoi -->
	</p>
	</form>
</h2>
</head>
<body>	
<?php

ini_set('memory_limit', '10000M'); <!-- Mémoire max. utilisable par le programme -->
ini_set('max_execution_time', '60'); <!-- Durée maximale autorisée pour les calculs -->
		
header('Content-type: text/html; charset=iso-8859-1');

#1 CONNEXION
#echo '<br/><br/>CONNEXION';
##1.0 Identifiants
	
$hostname = "localhost"; /* Localisation du serveur */
$username = "root"; /* Nom d'utilisateur MySql */
$password = ""; /* Mdp */
$con = mysqli_connect($hostname, $username, $password, 'projrm') or die(mysqli_error()); /* Connexion */
	
if (mysqli_connect_errno()) {
	printf("Échec de la connexion : %s\n", mysqli_connect_error());
	exit();
	} /* Message d'erreur si problème de connexion */
else echo ("<br/>Connexion ok<br/>");
		
### 	CREATION DES TABLES ANNEE
		
$A=30; %-- Etendue de la projection en années --%
$T0= 2013; %-- Annee intiale --%
$CREA = FALSE; %-- TRUE si les tables ne sont pas encore existantes --%
if($CREA ){
	while($A > -1){
		$Ti = $T0 + $A;
		$DROP_TB ="DROP TABLE IF EXISTS Projrm.T$Ti ";
		mysqli_query($con,$DROP_TB) or die ("Error in query :  $DROP_TB ".mysqli_error($con ));
		$CREA_TB ="CREATE TABLE Projrm.T$Ti (Id INT PRIMARY KEY NOT NULL, Nummi varchar(40), Aged INT NOT NULL, Dipl varchar(2) NOT NULL, Sexe varchar(2) NOT NULL, AleaM float, EtatM_0101 INT, EtatM_3112 INT, AleaF float, EtatF INT)";
		mysqli_query($con,$CREA_TB) or die ("Error in query :  $CREA_TB ".mysqli_error($con ));
		$A--;
		echo "</br> CREA_TB_$Ti</br>";
		}
	}
		
###	CREA MORT
$CREAM = FALSE;
if($CREAM ){
	$DROP_TMF ="DROP TABLE IF EXISTS Projrm.fec";
	mysqli_query($con ,$DROP_TMF ) or die ("Error in query :  $DROP_TMF ".mysqli_error($con ));
			
	$CREA_TM ="CREATE TABLE projrm.morta (code varchar(5) NOT NULL, valeur float )";
	#mysqli_query($con ,$CREA_TM ) or die ("Error in query :  $CREA_TM ".mysqli_error($con ));
			
	$CREA_TF ="CREATE TABLE projrm.fec  (Age int PRIMARY KEY NOT NULL,Poids_rel float not null,  ICF float not null , Taux_fec float not null);";
	mysqli_query($con ,$CREA_TF ) or die ("Error in query :  $CREA_TM ".mysqli_error($con ));
	}
			
### 	INSERTION INITIALE
$INSERTION = FALSE;
if( $INSERTION ){
	$ERASE = "TRUNCATE TABLE T2013";
	mysqli_query($con ,$ERASE ) or die ("ERROR IN QUERY".$ERASE );
		
	$INSERT = "INSERT INTO T2013 (Id, Nummi, Aged, Dipl, Sexe, EtatM_0101) SELECT id, nummi, aged, dipl_15, sexe, 1 FROM rc2013.individus WHERE Iris_2013 like '35080%' and indic_inp > 0";
	mysqli_query($con ,$INSERT) or die ("Error in query :  $INSERT "."</br>".mysqli_error($con));
		
	$DIPL = "UPDATE t2013 SET dipl = 'Z' where aged < 30;";
	mysqli_query($con ,$DIPL) or die ("Error in query :  $DIPL "."</br>".mysqli_error($con));
		
	echo "Insertion 2013 OK</br>";
	}	
	
######################  UPLOAD DES FICHIERS NECESSAIRES #########################
## 	Les importations de fichiers hypotheses ont été fait à la main	       ##
#################################################################################

###	MISE A JOUR ICF
if(empty($_POST ['ICF'])){ # Si aucune valeur en entrée du formulaire
	$ICF = 1.8; #	Valeur ICF par default
	} 
else {
	$ICF = $_POST ['ICF']; # Maj de la valeur ICF
	$UPD_ICF ="UPDATE fec SET ICF = $ICF";
	mysqli_query($con ,$UPD_ICF);
			
	$UPD_TF ="UPDATE fec SET Taux_fec = Poids_rel *  ICF"; # Les éléments de calendrier sont recadrés sur le nouvel ICF
	mysqli_query($con , $UPD_TF);
		
	echo "Mise a jour icf =  $ICF .</br>"; # Message de validation de l'hypothèse
	}

###	Phase Test 
	
	$DELETE_N = TRUE;
	if($DELETE_N )
		{
		$VIDE_NAIS ="DELETE  FROM t2013 WHERE id < 10000;";
		mysqli_query($con ,$VIDE_NAIS );
		}
		
		$truncate = TRUE;
		if($truncate )
		{
		echo "TRUNCATE_";
		$Ti= $T0 + $A;
			
		while($Ti > $T0)
			{
			$ERASE = "TRUNCATE TABLE T$Ti ";
			mysqli_query($con ,$ERASE ) or die ("ERROR IN QUERY".$ERASE );
			
			#echo "_$Ti ";
			$Ti--;}
			echo "</br>";}
					
##   ALEA ET ETAT POUR CHAQUE ANNEE ##

$i = 0; # Indice pour l'année
$Id_kid = 0; # Initialisation des indentifiants enfants

while($i < 30 ){ # Pour les 29 premières années
	$Ti = $T0 + $i; #Redefinir l'annee de la boucle 
	$n =	0; # Reinitialisation de n a chaque boucle

###	ALEA MORTALITE

	if($Ti < 2043){	
		$ALEAM = "UPDATE t$Ti SET aleaM = rand() WHERE  EtatM_0101= 1"; # Generer un nb alea pour les personnes vivantes
		mysqli_query($con , $ALEAM ) or die ("ERROR IN ALEAM" .mysqli_error($con ));
			
		$ALEAF = "UPDATE t$Ti SET aleaF = rand() WHERE  EtatM_0101= 1 AND sexe = 2 and aged < 51 and aged > 14";
		mysqli_query($con , $ALEAF ) or die ("ERROR IN ALEAM" .mysqli_error($con )); # Generer un nb alea pour les femmes en age de procréer
		
		$SELECT = "SELECT * FROM projrm.t$Ti ";
		$SEL = mysqli_query($con ,$SELECT ) or die ("ERROR IN selection" .mysqli_error($con ));
		
		if(!$SEL ){
			echo "LOOSE 1";
			}
		else{
			while($row = mysqli_fetch_array($SEL ))
			{
			$id =	$row ["Id"];
			$S =	$row ["Sexe"];
			$D =	$row ["Dipl"];
			$A =	$row ["Aged"];
			$M =	$row ["EtatM_0101"];
			$RAND =	$row ["AleaM"];
			$F=     $row["AleaF"];
			$N=     $row["Nummi"];

			if(is_null($F )){
				$FEC = "UPDATE t$Ti SET EtatF = 0 WHERE Id = $id ";
				mysqli_query($con ,$FEC );
				}
			else{
				$TF = "SELECT Taux_fec FROM fec WHERE age = $A ";
				$SEL_TF = mysqli_query($con ,$TF ) or die ("ERROR IN TF" .mysqli_error($con ));
				$row =	mysqli_fetch_array($SEL_TF );
				$TF =	$row["Taux_fec"];
				
				if($F > $TF ){
					$NO_NAISS = " UPDATE t$Ti SET EtatF = 0 WHERE id = $id ";
					mysqli_query($con ,$NO_NAISS ) or die ("ERROR IN $NO_NAISS ".mysqli_error($con ));
					}	
				else{
					$NAISS =" UPDATE t$Ti SET EtatF = 1 WHERE id = $id ";
					mysqli_query($con ,$NAISS ) or die ("ERROR IN $NAISS".mysqli_error($con ));
				
					$INSERT_NN =" INSERT INTO t$Ti (nummi, id, aged, sexe, Dipl, EtatM_3112, aleaM) VALUES ($N , $Id_kid , -1, 1+rand(),'Z',1,1)	";
					mysqli_query($con ,$INSERT_NN ) or die ("ERROR IN $INSERT_NN".mysqli_error($con ));
					$n++ ;
					$Id_kid++ ; #echo $Id_kid ;
				     }
			     }
//							
			if($M = 0){
				$DEAD ="UPDATE t$Ti SET EtatM_3112 = 0 WHERE Id  = $Id ";
				mysqli_query($con ,$DEAD );
				}
			else{
				$CODE =	"$A$S$D";
				#echo "</br>$CODE";
			
				$VAL= "SELECT valeur FROM morta WHERE code = '".$CODE."'";
				$SEL_VAL = mysqli_query($con ,$VAL ) or die ("ERROR IN VAL" .mysqli_error($con ));
				$row = mysqli_fetch_array($SEL_VAL);
				$SEUIL = $row ["valeur"]/10;
				
				if($RAND > $SEUIL ){
					$SURVIE = " UPDATE t$Ti SET EtatM_3112 = 1 WHERE id = $id ";
					mysqli_query($con ,$SURVIE ) or die ("ERROR IN $SURVIE".mysqli_error($con ));
					}
				else{
				$MORT ="UPDATE t$Ti SET EtatM_3112 = 0 WHERE id = $id ";
				mysqli_query($con, $MORT) or die ("ERROR in $MORT".mysqli_query($MORT));
				}
			     }	
		}}
### TRANSFERT N+1
$T_plusun = $Ti + 1;

if($T_plusun < 2044){
	$INSERT = "INSERT INTO t$T_plusun (Id, Nummi, Aged, Dipl, Sexe, EtatM_0101) SELECT id, nummi, aged, dipl, sexe, EtatM_3112 FROM t$Ti ";
	mysqli_query($con ,$INSERT ) or die ("Error in query :  $INSERT "."</br>".mysqli_error($con));
			
	$UPD_AGED = "UPDATE T$T_plusun SET aged = aged + 1;";
	mysqli_query($con,$UPD_AGED);
			
	$UPD_AGED100 = "UPDATE T$T_plusun SET aged = 100 where aged > 100;";
	mysqli_query($con,$UPD_AGED100);
	}	
$i++;}

}


//
###	TOTAL POP / ANNEE POUR GRAPH -> JSON
	$t=2013;
	while($t<2044)
	{$res1 = "SELECT count(id) AS POP FROM  t$t where EtatM_0101 = 1";
					$result = mysqli_query($con,$res1);
					while($row=mysqli_fetch_array($result))
					{
					$evol_pop[]=$row["POP"]*1;
					$t++;
					}}
	#print_r($evol_pop);
	$A =json_encode($evol_pop);
	#echo "</br>".$A;
	
	
###	DETAILS AGE x SEXE / ANNEE + INIT (2013) POUR PYRA -> JSON
	if(empty($_POST['Range']))
		{
		$AN_PYRA = 2013;}
		else{
		$AN_PYRA = $_POST['Range'];}
		$SERIE_F="SELECT FLOOR(Aged / 5) * 5 AS Tranche_Age, count(id) AS Serie_F FROM t$AN_PYRA WHERE aged > -1 AND sexe = 2 AND etatM_0101=1 group by Tranche_age";
		$result_SERIE_F = mysqli_query($con,$SERIE_F);
					while($row=mysqli_fetch_array($result_SERIE_F))
					{
					$SF[]=$row["Serie_F"]*1;
					}
					
	#print_r($SF);
	$SF =json_encode($SF);
	
	#	2013	
	$SERIE_F13="SELECT FLOOR(Aged / 5) * 5 AS Tranche_Age, count(id) AS Serie_F13 FROM t2013 WHERE aged > -1 AND sexe = 2 AND etatM_0101=1 group by Tranche_age";
	$result_SERIE_F13 = mysqli_query($con,$SERIE_F13);
					while($row=mysqli_fetch_array($result_SERIE_F13))
					{
					$SF13[]=$row["Serie_F13"]*1;
					}
			
	#print_r($SF);
	$SF13 =json_encode($SF13);
		
	#	H
	$SERIE_M="SELECT FLOOR(Aged / 5) * 5 AS Tranche_Age, count(id) AS Serie_M FROM t$AN_PYRA WHERE aged > -1 AND sexe = 1 AND etatM_0101=1 group by Tranche_age";
	$result_SERIE_M = mysqli_query($con,$SERIE_M);
					while($row=mysqli_fetch_array($result_SERIE_M))
					{
					$SM[]=$row["Serie_M"]*-1;
					}
	#print_r($SM);
	$SM =json_encode($SM);		
	
	#	2013
	$SERIE_M13="SELECT FLOOR(Aged / 5) * 5 AS Tranche_Age, count(id) AS Serie_M13 FROM t2013 WHERE aged > -1 AND sexe = 1 AND etatM_0101=1 group by Tranche_age";
	$result_SERIE_M13 = mysqli_query($con,$SERIE_M13);
					while($row=mysqli_fetch_array($result_SERIE_M13))
					{
					$SM13[]=$row["Serie_M13"]*-1;
					}
	#print_r($SM);
	$SM13 =json_encode($SM13);	
	
###	AGE
	$age=0;	
	while($age<101)
					{
					$aged[]=$age;
					$age=$age+5;
					}
	#print_r($aged);
	$aged2 =json_encode($aged);			
?>


<div id="TAB">
 <table BORDER="1" >
	<tr BGCOLOR="#FF6347">
		<td>1.1.N</td>
		<td>Effectif</td>
		<td>Nb de menages</td>
		<td>Nb de pers/men</td>
		<td>Nb de naissances</td>
		<td>Âge moyen</td>
	</tr>
    <?php 
        for ($t = 2013; $t <= 2043; $t++) { 
    ?>
       <tr>
			<td	BGCOLOR="#FF6347">	<?php echo $t; ?>		</td>
		   	<td align="right"><?php 
				{$res1 = "SELECT count(id) AS POP FROM  t$t where EtatM_0101 = 1";
				$result = mysqli_query($con,$res1);
				$row=mysqli_fetch_array($result);
				$count=$row["POP"];
				echo "$count</br>";
				} ?>					</td>
			<td align="right"><?php 
				{$res1 = "SELECT count(NB) AS NBM FROM (SELECT count(nummi) AS NB from t$t AS B where etatm_0101 = 1 group by nummi) AS A";
				$result = mysqli_query($con,$res1);
				$row=mysqli_fetch_array($result);
				$count=$row["NBM"];
				echo "$count</br>";
				} ?>					</td>
			<td align="right"><?php 	
				{$res1 = "SELECT avg(NB) AS TMM FROM (SELECT count(nummi) AS NB from t$t AS B where etatm_0101 = 1 group by nummi) AS A";
				$result = mysqli_query($con,$res1);
				$row=mysqli_fetch_array($result);
				$count=$row["TMM"];
				echo "$count</br>";
				}?>						</td>
			<td align="right"><?php 
				{$res1 = "SELECT count(id) from t$t where etatF = 1";
				$result = mysqli_query($con,$res1);
				$row=mysqli_fetch_array($result);
				$count=$row["count(id)"];
				echo "$count</br>";
				} ?>					</td>
			<td align="right"><?php 
				{$res1 = "SELECT avg(aged) AS POP FROM  t$t where EtatM_0101 = 1";
				$result = mysqli_query($con,$res1);
				$row=mysqli_fetch_array($result);
				$POP=$row["POP"];
				echo "$POP </br>";
				} ?>					</td>

		</tr>
     <?php   
        } 
     ?> 
 </table>
 </div>
 
 
<div align="center"  style="min-width: auto; height: auto; margin: 0 auto">
	<form action="#" method="post" name='AN_PYRA' oninput="result.value=Range.value" >
		<fieldset>Année <output for="out" name="result"></output></br>
			2013<input type="range" name="Range"  step="1"  
			min="2013" max="2043" default='2013' title="Année d'observation.value" value="" />2043
			<span class="range__value"></span>
			<input type="submit" class="button" value="OK" align="right" bgcolor='pink'/>
		</fieldset>
	</form>
</div>




<div id="container" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto">
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>	
<script type="text/javascript">

// Data gathered from http://populationpyramid.net/germany/2015/

// Age categories
var categories = <?php echo $aged2; ?>;
$(document).ready(function () {
    Highcharts.chart('container', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Pyramide des âges par sexe dans Rennes Métropole'
        },
        subtitle: {
            text: 'Source: <a href="http://populationpyramid.net/germany/2015/">*</a>'
        },
        xAxis: [{
            categories: categories,
            reversed: false,
            labels: {
                step: 5
            }
        }],
        yAxis: {
            title: {
                text: null
            },
            labels: {
                formatter: function () {
                    return Math.abs(this.value) ;
                }
            }
        },

        plotOptions: {
             series: {
            stacking: false,
            groupPadding : 0,
            pointPadding : 0,
            pointWidth : 10
            }
        },

        tooltip: {
            formatter: function () {
                return '<b>' + this.series.name + ', age ' + this.point.category + '</b><br/>' +
                    'Population: ' + Highcharts.numberFormat(Math.abs(this.point.y), 0);
            }
        },
        
        series: [
        {stack: false,
            name: '2013',
            data: <?php echo $SM13; ?>,
            color : '#FF6347'
        }, {stack: false,
        name: ' '
            data: <?php echo $SF13; ?>,
            color : '#FF6347'
        },
        {

       
			stack: false,
            name: '<?php echo $AN_PYRA;?>',
            
            data: <?php echo $SM; ?>,
            color: 'pink'
        }, {stack: false,
            name: ' ',
            data: <?php echo $SF; ?>,
            color: 'pink'
        }]
    });
});
</script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<div id="AA" style="min-width: 150px; height: 400px; margin: 0 " >

 
<script type="text/javascript">


Highcharts.chart('container2', {
    chart: {
        type: 'area'
    },
    title: {
        text: 'Projection de population Rennes Métropole'
    },
    subtitle: {
        text: 'Source: <a href="http://thebulletin.metapress.com/content/c4120650912x74k7/fulltext.pdf">' +
            '*</a>'
    },
    xAxis: {
        allowDecimals: false,
        labels: {
            formatter: function () {
                return this.value ; // clean, unformatted number for year
            }
        }
    },
    yAxis: {
        title: {
            text: 'Habitants (milliers)'
        },
        labels: {
            formatter: function () {
                return this.value / 1000 + 'k';
            }
        }
    },
    tooltip: {
        pointFormat: '{series.name} comptera <b>{point.y:,.0f}</b><br/>habitants en {point.x}',
        color : 'green',
        symbol : 'cross',
        shadow : false
    },
    
    plotOptions: {
        area: {
            pointStart: 2013,
            marker: {
                enabled: false,
                symbol: 'cross',
                radius: 1,
                states: {
                    hover: {
                        enabled: true
                    }
                }
            }
        }
    },
    series: [{
        name: 'Rennes Métropole',
        data:<?php echo $A; ?>,
        color:'pink'
    }]
});
</script>
</div>

</html>