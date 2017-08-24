<?php

$temperature = $data->getMain()->getTemperature();
?>
<div class="openweathermap micro horizontal">
    <table class="details">
        <thead>
            <tr>
                <th class="title" colspan="4">Weather for <?php echo esc_html( $zipcode ); ?></th>
            <tr>
                <th>Temp</th>
                <th>Conditions</th>
                <th>Wind</th>
                <th>Humidity</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="temperature"><?php echo esc_html( $temperature); ?>&#8457;</td>
                <td>
                    <?php echo $data->getWeather()->getDescription(); ?>
                    <span class="icon wi wi-<?php echo esc_attr( $data->getIconClass() ); ?>"></span>
                </td>
                <td>
                    <span class="speed"><?php echo esc_html( $data->getWind()->getSpeed() ); ?></span> /
                    <span class="direction"><?php echo esc_html( $data->getWind()->getDirection() ); ?>
                </td>
                <td><?php echo esc_html( $data->getMain()->getHumidity() ); ?></td>
            </tr>
        </tbody>
    </table>
</div>
