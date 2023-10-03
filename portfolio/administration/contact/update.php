<?php
require_once('../../includes/connexion-bdd.php');

$result = $conn->query("SELECT * FROM contact");
$result = $result->fetch();

// Définir les variables
$accroche = $result["accroche"] ;
$accroche_err = "";
$input_accroche = "";

// Vérifier la valeur id dans le post pour la mise à jour
if(isset($_POST["id"]) && !empty($_POST["id"])){
    $id = $_POST["id"];

    // Validation de l'accroche
    $input_accroche = trim($_POST["accroche"]);
    if(empty($input_accroche)){
        $accroche_err = "Veuillez entrer une accroche.";
    } 
    else{
        $accroche = $input_accroche; // mettre à jour la variable $accroche avec la valeur entrée
    }

    // Vérifier les erreurs avant modification
    if(empty($accroche_err)){

        // Préparation de la requête SQL pour la mise à jour de l'enregistrement avec l'ID spécifié
        $sql = "UPDATE contact SET accroche = :accroche WHERE id=:id";
        $stmt = $conn->prepare($sql);

        // Bind des paramètres pour la mise à jour
        $stmt->bindParam(":accroche", $accroche, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT); // liaison du paramètre pour l'ID

        // Exécution de la requête pour la mise à jour
        if($stmt->execute()){
            // Enregistrement modifié, retourner à la page d'accueil
            header("location: contact_admin.php");
            exit();
        } else{
            echo "Oops! une erreur est survenue.";
        }
    } 

} // fin du bloc "if(isset($_POST["id"]) && !empty($_POST["id"]))"

// Si un paramètre id existe dans l'URL
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);

    // Préparation de la requête SQL pour la sélection de l'enregistrement avec l'ID spécifié
    $sql = "SELECT * FROM contact WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Bind des paramètres pour la sélection
    $stmt->bindParam(1, $id);

    // Exécution de la requête de sélection
    if($stmt->execute()){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result){
            // Récupère l'enregistrement
            $accroche = $result["accroche"];
        } 

        else{
            // Retourne à la page d'erreur si aucun enregistrement n'a été trouvé
            header("location: error.php");
            exit();
        }

    } 

    else{
        echo "Oops! une erreur est survenue.";
    }

    // Fermeture du statement et de la connexion
    $stmt = null;
    $conn = null;

} // fin du bloc "if(isset($_GET["id"]) && !empty(trim($_GET["id"])))"

else{
    // Si le paramètre id n'est pas spécifié dans l'URL
    header("location: error.php");
    exit();
}

// HTML pour le formulaire de modification

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'enregistremnt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        .wrapper{
            width: 700px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Mise à jour de l'enregistremnt</h2>
                    <p>Modifier les champs et enregistrer</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Accroche</label>
                            <input type="text" name="accroche" class="form-control <?php echo (!empty($accroche_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $accroche; ?>">
                            <span class="invalid-feedback"><?php echo $accroche_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Enregistrer">
                        <a href="index.php" class="btn btn-secondary ml-2">Annuler</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>