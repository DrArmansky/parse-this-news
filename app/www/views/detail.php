<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/header.php';
if (isset($templateData['NEWS_DATA'])) { ?>
    <article>
        <h1><?= $templateData['NEWS_DATA']['TITLE'] ?></h1>
        <img src="<?= $templateData['NEWS_DATA']['IMAGE'] ?>" alt="News picture" width="500" height="500">
        <p><?= $templateData['NEWS_DATA']['TEXT'] ?></p>
    </article>
<?php }
require_once $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>