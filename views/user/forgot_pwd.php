<?php
    /* Affichage des erreurs */
    if (count($arrErrors) > 0){
        echo "<div class='alert alert-danger'>";
        foreach ($arrErrors as $strError){
            echo "<p class='mb-0'>".$strError."</p>";
        }
        echo "</div>";
    }
?>
<p class="alert alert-info">Merci de saisir un mail, si celui-ci existe nous vous enverrons un lien pour le réinitialiser</p>
<form method="post">
    <p>
        <label for="mail">Mail</label>
        <input name="mail" value="<?php echo $strMail; ?>" id="mail" class="form-control 
            <?php if(isset($arrErrors['mail'])){ echo 'is-invalid'; } ?>" type="text" >
    </p>
    <p>
        <input class="form-control btn btn-primary" type="submit" >
    </p>
</form>