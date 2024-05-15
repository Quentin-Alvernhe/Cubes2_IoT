<?php
date_default_timezone_set('Europe/Paris');

/**
 * Crée un objet PDO connecté à la base de données
 * @return PDO Retourne l'objet PDO permettant de se connecter
 */
function CreatePDO()
{
  require_once("./config.php");
  $dbh = new PDO('mysql:host=localhost;dbname=meteo;port=8888', $user, $mdp);
  return $dbh;
}

/**
* Envoie une requête SQL pour créer une ligne
* @param PDO $dbh Connection entre la bdd et le serveur
* @param mixed $capteur 
* @return void
*/
function CreateSonde($capteur)
{
  $today = date("d/m/Y H:i:s");
  $dbh = createPDO();
  $stmt = $dbh->prepare("INSERT INTO `sondes` (`addresse_mac`, `description_sonde`, `creation_date`, `derniere_co`) VALUES (:addresse_mac, NULL, :creation_date, :derniere_co);");
  $stmt->bindParam(':addresse_mac', $capteur["addresse_mac"], PDO::PARAM_STR);
  $stmt->bindParam(':creation_date', $today, PDO::PARAM_STR);
  $stmt->bindParam(':derniere_co', $today, PDO::PARAM_STR);
  $stmt->execute();
}

/**
 * Récupére la liste des sondes qui existent dans la base de données
 * @return array Tableau contenant la liste des sondes
 */
function ReadSonde()
{
  $dbh = createPDO();
  $stmt = $dbh->prepare("SELECT * FROM `sondes`");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

//UPDATE Sonde
function UpdateSonde($capteur)
{
  // TODO
}

function UpdateSondeDate($capteur)
{
  $today = date("d/m/Y H:i:s");
  $dbh = createPDO();
  $stmt = $dbh->prepare("UPDATE `sondes` SET `derniere_co` = :derniere_co WHERE `addresse_mac` = :addresse_mac");
  $stmt->bindParam(':addresse_mac', $capteur["addresse_mac"], PDO::PARAM_STR);
  $stmt->bindParam(':derniere_co', $today, PDO::PARAM_STR);
  $stmt->execute();
}

/**
 * Supprime la sonde indiqué en parametre de la fonction
 * @param mixed $capteur Nom de la sonde à supprimer
 */
function DeleteSonde($capteur)
{
  $dbh = createPDO();
  $stmt = $dbh->prepare("DELETE FROM `sondes` WHERE `addresse_mac` = :addresse_mac");
  $stmt->bindParam(':addresse_mac', $capteur["addresse_mac"], PDO::PARAM_STR);
  $stmt->execute();
}



/**
 * Ajouter une mesure à la base de données
 * @param mixed $capteur Données à insérer dans la base de données
 */
function CreateMesures($capteur)
{
  $dbh = createPDO();
  $today = date("d/m/Y H:i:s");
  
  $stmt = $dbh->prepare("INSERT INTO `mesures` (`id`, `sonde_addresse`, `temperature`, `pression`, `humidite`, `creation_date`) VALUES (DEFAULT, (SELECT `addresse_mac` FROM `sondes` WHERE `addresse_mac` = :addresse_mac), :temperature, :pression, :humidite, :creation_date);");
  $stmt->bindParam(':addresse_mac', $capteur['addresse_mac'], PDO::PARAM_STR);
  $stmt->bindParam(':temperature', $capteur['valeurs']['temperature'], PDO::PARAM_INT);
  $stmt->bindParam(':pression', $capteur['valeurs']['pression'], PDO::PARAM_INT);
  $stmt->bindParam(':humidite', $capteur['valeurs']['humidite'], PDO::PARAM_INT);
  $stmt->bindParam(':creation_date', $today, PDO::PARAM_STR);
  $stmt->execute();

  UpdateSondeDate($capteur);
}
/**
 * Lit les mesures de la sonde passé en paramètre
 * @return array Tableau contenant les mesures de la sonde
 */
function ReadMesures($capteur)
{
  $dbh = createPDO();
  $stmt = $dbh->prepare("SELECT `sonde_addresse`,`temperature`, `pression`, `humidite`, `mesures`.`creation_date` FROM `sondes` INNER JOIN `mesures` WHERE `sonde_addresse` = `addresse_mac` AND `sonde_addresse` = :sonde_addresse");
  $stmt->bindParam(':sonde_addresse', $capteur['addresse_mac'], PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}
/**
 * Lit les 5 dernières mesures de la sonde passé en paramètre
 * @return array Tableau contenant les 5 dernières mesures de la sonde
 */
function ReadLastFiveMesures($capteur)
{
  $dbh = createPDO();
  $stmt = $dbh->prepare("SELECT `sonde_addresse`,`temperature`, `pression`, `humidite`, `mesures`.`creation_date` FROM `sondes` INNER JOIN `mesures` WHERE `sonde_addresse` = `addresse_mac` AND `sonde_addresse` = :sonde_addresse ORDER BY `id` DESC LIMIT 5");
  $stmt->bindParam(':sonde_addresse', $capteur['addresse_mac'], PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}
/**
 * Lit toutes les mesures enregistrées dans la base de données
 * @return array Tableau contenant toutes les mesures
 */
function ReadAllMesures()
{
  $dbh = createPDO();
  $stmt = $dbh->prepare("SELECT `sonde_addresse`,`temperature`, `pression`, `humidite`, `creation_date` FROM mesures");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}





// MAIN
if (isset($_POST["capteur"])) {
  $capteurExist = false;
  $result = ReadSonde();
  $capteur = json_decode($_POST["capteur"]);

  foreach ($result as $key => $value) {
    if ($capteur['addresse_mac'] == $value['addresse_mac']) {
      $capteurExist = true;
    }
  }
  if (!$capteurExist) {
    CreateSonde($capteur);
    CreateMesures($capteur);
  }
}

if (isset($_POST['action'])) {
  $data = $_POST['data'];
  switch ($_POST['action']) {
    case 'createSondes':
      CreateSonde($data);
    break;
    case 'readSondes':
    return ReadSonde();
    case 'updateSondes':
      UpdateSonde($data);
    break;  
    case 'deleteSondes':
      DeleteSonde($data);
    break;
    case 'createMesures':
      CreateMesures($data);
    break;
    case 'readMesures':
    return ReadMesures($data);
    case 'readFiveMesures':
    return ReadLastFiveMesures($data);
    case 'readAllMesures':
    return ReadAllMesures();
  }
}
                
?>