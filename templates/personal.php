<?php
/** @var array $_ */
script('jsondashboard', 'settings');
style('jsondashboard', 'settings');
style('jsondashboard', 'dashboard');

$icons = [
    'dashboard' => $l->t('Dashboard'),
    'home' => $l->t('Haus'),
    'thermometer' => $l->t('Temperatur / Heizung'),
    'bolt' => $l->t('Energie'),
    'battery' => $l->t('Batterie'),
    'server' => $l->t('Server'),
    'database' => $l->t('Datenbank'),
    'wifi' => $l->t('Netzwerk / WLAN'),
    'cloud' => $l->t('Cloud'),
    'water' => $l->t('Wasser'),
    'fire' => $l->t('Feuer'),
    'ship' => $l->t('Boot'),
    'clock' => $l->t('Uhrzeit'),
    'chart' => $l->t('Diagramm'),
    'speed' => $l->t('Geschwindigkeit'),
    'light' => $l->t('Licht'),
    'settings' => $l->t('Einstellungen'),
];
?>
<div id="jd-settings" class="section">
    <h2><?php p($l->t('JSON Dashboard')); ?></h2>
    <p><?php p($l->t('Bis zu acht persönliche JSON-Widgets. Aktivierte Einträge erscheinen nach dem Speichern in der Dashboard-Auswahl.')); ?></p>

    <div id="jd-rows">
        <?php foreach ($_['widgets'] as $index => $widget) { ?>
            <?php $slot = $index + 1; ?>
            <fieldset class="jd-row" data-slot="<?php p($slot); ?>">
                <legend><?php p($l->t('Widget %s', [$slot])); ?></legend>

                <label class="jd-enabled-label">
                    <input class="jd-enabled" type="checkbox" <?php if ($widget['enabled']) { p('checked'); } ?>>
                    <?php p($l->t('Aktiv')); ?>
                </label>

                <label>
                    <?php p($l->t('Titel')); ?>
                    <input class="jd-title" type="text" value="<?php p($widget['title']); ?>">
                </label>

                <label>
                    <?php p($l->t('JSON-URL')); ?>
                    <input class="jd-url" type="url" value="<?php p($widget['url']); ?>">
                </label>

                <label>
                    <?php p($l->t('JSON-Hauptschlüssel')); ?>
                    <input class="jd-key" type="text" value="<?php p($widget['key']); ?>">
                </label>

                <fieldset class="jd-icon-picker">
                    <legend><?php p($l->t('Icon')); ?></legend>
                    <div class="jd-icon-options">
                        <?php foreach ($icons as $value => $label) { ?>
                            <label class="jd-icon-option" title="<?php p($label); ?>">
                                <input class="jd-icon" type="radio" name="jd-icon-<?php p($slot); ?>" value="<?php p($value); ?>" <?php if ($widget['icon'] === $value) { p('checked'); } ?>>
                                <span class="jd-icon-preview icon-jsondashboard-<?php p($value); ?>" aria-hidden="true"></span>
                                <span class="jd-icon-name"><?php p($label); ?></span>
                            </label>
                        <?php } ?>
                    </div>
                </fieldset>
            </fieldset>
        <?php } ?>
    </div>

    <button id="jd-save" type="button" class="primary"><?php p($l->t('Speichern')); ?></button>
    <span id="jd-message" role="status" aria-live="polite"></span>
</div>
