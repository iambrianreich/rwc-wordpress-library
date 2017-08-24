<?php

$temperature = $data->getMain()->getTemperature();
?>
<div class="openweathermap micro">
    <table class="details">
        <thead>
            <tr>
                <td colspan="2">Current Weather for <?php echo esc_html( $zipcode ); ?></td>
            </tr>
        </thead>
        <tr>
            <th>Temperature</th>
            <td class="temperature"><?php echo esc_html( $temperature); ?>&#8457;</td>
        </tr>
        <tr>
            <th>Conditions</th>
            <td>
                <?php echo $data->getWeather()->getDescription(); ?>
                <span class="icon wi wi-<?php echo esc_attr( $data->getIconClass() ); ?>"></span>
            </td>
        </tr>
        <?php $rain = $data->getRain(); if( ! empty( $rain ) ) : ?>
            <tr>
                <th>Rain (3hr)</th>
                <td><?php echo esc_html( $rain ); ?></td>
            </tr>
        <?php endif; ?>
        <?php $snow = $data->getSnow(); if( ! empty( $snow ) ) : ?>
            <tr>
                <th>Snow (3hr)</th>
                <td><?php echo esc_html( $snow ); ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <th>Sunrise</th>
            <td data-timestamp="<?php echo esc_attr( $data->getSunrise() ); ?>">
                <?php echo date_i18n( 'g:i A', $data->getSunrise() ); ?>
            </td>
        </tr>
        <tr>
            <th>Sunset</th>
            <td data-timestamp="<?php echo esc_attr( $data->getSunset() ); ?>">
                <?php echo date_i18n( 'g:i A', $data->getSunset() ); ?>
            </td>
        </tr>
        <tr>
            <th>Wind</th>
            <td>
                <span class="speed"><?php echo esc_html( $data->getWind()->getSpeed() ); ?></span> /
                <span class="direction"><?php echo esc_html( $data->getWind()->getDirection() ); ?>
            </td>
        </tr>
        <tr>
            <th>Cloudiness (%)</th>
            <td><?php echo esc_html( $data->getClouds() ); ?></td>
        </tr>
        <tr>
            <th>Humidity (%)</th>
            <td><?php echo esc_html( $data->getMain()->getHumidity() ); ?></td>
        </tr>
        <tr>
            <th>Minimum Temperature</th>
            <td><?php echo esc_html( $data->getMain()->getTemperatureMinimum() ); ?></td>
        </tr>
        <tr>
            <th>Maximum Temperature</th>
            <td><?php echo esc_html( $data->getMain()->getTemperatureMaximum() ); ?></td>
        </tr>
    </table>
</div>
