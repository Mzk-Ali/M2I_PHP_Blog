    <article class="col-md-6">
        <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
            <div class="col p-4 d-flex flex-column position-static">
                <h3 class="mb-0"><?php echo $arrDetArticle->getTitle(); ?></h3>
                <div class="mb-1 text-body-secondary"><?php echo $arrDetArticle->getDateFormat(); ?> (<?php echo $arrDetArticle->getAuthor(); ?>)</div>
                <p class="mb-auto"><?php echo $arrDetArticle->getSummary(); ?> </p>
                <div>
                    <a href="" class="icon-link gap-1 icon-link-hover">Lire la suite</a>
                    <?php if(isset($_SESSION['id'] ) && $_SESSION['id'] == $arrDetArticle->getCreator()){
                        echo '<a alt="Modifier un article" href="index.php?ctrl=article&action=edit_article&articleId='.$arrDetArticle->getId().'" class="border rounded">Modifier</a>';
                        echo '<a alt="Supprimer un article" href="index.php?ctrl=article&action=delete_article&articleId='.$arrDetArticle->getId().'" class="border rounded">Supprimer</a>';
                     } ?>
                </div>
            </div>
            <div class="col-auto d-none d-lg-block">
                <img class="bd-placeholder-img" width="200" height="250" alt="<?php echo $arrDetArticle->getTitle(); ?>" src="assets/images/<?php echo $arrDetArticle->getImg(); ?>">
            </div>
        </div>
    </article>    