<?php if (isset($templateData['NEWS_DATA'])): ?>
    <article>
        <h1><?= $templateData['NEWS_DATA']['TITLE'] ?></h1>
        <?php if (!empty($templateData['NEWS_DATA']['IMAGE'])) : ?>
            <img src="<?= $templateData['NEWS_DATA']['IMAGE'] ?>" alt="News picture" width="500" height="500">
        <?php endif ?>
        <p><?= $templateData['NEWS_DATA']['TEXT'] ?></p>
    </article>
<?php endif; ?>