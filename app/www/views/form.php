<?php if (isset($templateData)): ?>
    <form id="parseSettings" action="#" style="border: 1px solid black; padding: 10px">
        <?php foreach ($templateData['FIELDS'] as $field): ?>

            <?php if ($field['TYPE'] === 'string'): ?>
                <p>
                    <label>
                        <?= $field['LANG_NAME'] ?>
                        <input type="text" name="<?= $field['NAME'] ?>">
                    </label>
                </p>
            <?php elseif ($field['TYPE'] === 'int'): ?>
                <p>
                    <label>
                        <?= $field['LANG_NAME'] ?>
                        <input type="number" name="<?= $field['NAME'] ?>">
                    </label>
                </p>
            <?php endif; ?>

        <?php endforeach; ?>

        <button type="submit" id="rbc-default">Установить для RBC</button>
        <button type="submit">Сохранить</button>
    </form>
    <br>
    <table class="source-table">
        <tr>
            <th>Ресурс</th>
            <th>Статус</th>
            <th>Действие</th>
        </tr>
    </table>
    <script>
        let parser = new NewsParser();
        parser.init(<?= $templateData['ROUTES'] ?>);
    </script>
<?php endif; ?>