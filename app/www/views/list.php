<?php if (isset($templateData['NEWS_LIST'])): ?>
    <div class="news-list">
        <?php
        foreach ($templateData['NEWS_LIST'] as $news) { ?>
            <article>
                <a href="<?= $news['LINK'] ?>"><h2><?= $news['TITLE'] ?></h2></a>
                <p><?= $news['TEXT'] ?></p>
            </article>
        <?php } ?>
    </div>
<?php endif; ?>