<?php

use yii\web\View;

$this->registerCss(<<<'CSS'
    .string { color: green; }
    .number { color: darkorange; }
    .boolean { color: blue; }
    .null { color: magenta; }
    .key { color: red; }
    .white-space-normal { white-space: normal; }
CSS
);

$this->registerJs(<<<'JS'
    function syntaxHighlight(json) {
        json = json.replace(/&/g, '&').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            var cls = 'number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'key';
                } else {
                    cls = 'string';
                }
            } else if (/true|false/.test(match)) {
                cls = 'boolean';
            } else if (/null/.test(match)) {
                cls = 'null';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    }

    $('.hiart-link').on('click', function (event) {
        event.preventDefault();

        var id = $(this).data('id');
        var result = $('.hiart-wrapper[data-id=' + id +']');
        result.find('.result').html('Sending request...');
        result.show();
        $.ajax({
            type: 'POST',
            url: $(this).attr('href'),
            beforeSend: function(xhr) {
              xhr.setRequestHeader('X-CSRF-Token', $('meta[name=csrf-token]').attr('content'))
            },
            success: function (data, status, xhr) {
                result.find('.result').html($('<pre>').html(syntaxHighlight(JSON.stringify( data.result, undefined, 2))));
                result.find('.time').html(data.time);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                result.find('.time').html('');
                result.find('.result').html('<span style="color: #c00">Error: ' + errorThrown + ' - ' + textStatus + '</span><br />' + jqXHR.responseText);
            },
            dataType: 'json'
        });
        return false;
    });
JS
, View::POS_READY);
?>

<h1>HiArt Queries</h1>

<table class="table table-condensed table-bordered table-striped table-hover" style="table-layout: fixed">
    <thead>
        <tr>
            <th style="width: 10%">Time</th>
            <th style="width: 80%">Url / Query</th>
            <th style="width: 10%">Run Query</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($timings as $timing) : ?>
            <tr>
                <td style="width: 10%"><?= $timing->getDuration() ?></td>
                <td style="width: 75%" class="white-space-normal">
                    <b><?= $timing->getMethod() ?> <?= $timing->getUrlEncoded() ?></b><br/>
                    <p style="word-wrap: break-word;"><?= $timing->getBodyEncoded() ?></p>
                    <ul class="list-unstyled">
                    <?php foreach ($timing->getHeaders() as $header): ?>
                        <li><?= $header ?></li>
                    <?php endforeach ?>
                    </ul>
                    <?= $timing->getTrace() ?>
                </div></td>
                <td style="width: 15%" class="white-space-normal">
                    <?= $timing->getRunLink() ?><br/>
                    <?= $timing->getNewTabLink() ?><br/>
                    <?= $timing->getCopyAsCurlLink() ?>
                </td>
            </tr>
            <tr style="display: none" class="hiart-wrapper" data-id="<?= $timing->getLogId() ?>">
                <td class="time"></td><td colspan="3" class="result"></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
