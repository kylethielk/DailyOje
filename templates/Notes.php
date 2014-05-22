<ul class="notes">
    <?php foreach ($notes as $note): ?>

        <?php if ($ajaxRequest  === true): ?>
            <li onclick="OJE.Note.loadNote('<?php echo $note->nid; ?>');">

        <?php else: ?>
            <li onclick="OJE.changeUrl('<?php echo $note->editUrl(); ?>');">
        <?php endif; ?>

        <a href="javascript:void(0);">
            <span class="note-title"><?php echo $note->title; ?></span>
            <br/>
            <span class="grey-date"><?php echo $note->prettyDate(); ?></span>
        </a>
        </li>
    <?php endforeach; ?>
</ul>