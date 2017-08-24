<?php

$temperature = $data->getMain()->getTemperature();
?>
<div class="openweathermap micro">
    <span class="temperature"><?php echo esc_html( $temperature); ?>&#8457;</span>
    <span class="icon wi wi-<?php echo esc_attr( $data->getIconClass() ); ?>"></span>
    <div class="details">
        <dl>
            <dt>Sunrise</dt>
            <dd data-timestamp="<?php echo esc_attr( $data->getSunrise() ); ?>">
                <?php echo date_i18n( 'g:i A', $data->getSunrise() ); ?>
            </dd>
            <dt>Sunset</dt>
            <dd data-timestamp="<?php echo esc_attr( $data->getSunset() ); ?>">
                <?php echo date_i18n( 'g:i A', $data->getSunset() ); ?>
            </dd>

            <dt>Wind</dt>
            <dd>
                <span class="speed"><?php echo esc_html( $data->getWind()->getSpeed() ); ?></span> /
                <span class="direction"><?php echo esc_html( $data->getWind()->getDirection() ); ?>
            </dd>
            
            <?php $rain = $data->getRain(); if( ! empty( $rain ) ) : ?>
                <dt>Rain (3hr)</dt>
                <dd><?php echo esc_html( $rain ); ?></dd>
            <?php endif; ?>

            <?php $snow = $data->getSnow(); if( ! empty( $snow ) ) : ?>
                <dt>Snow  (3hr)</dt>
                <dd><?php echo esc_html( $snow ); ?></dd>
            <?php endif; ?>
        </dl>
    </div>
</div>
