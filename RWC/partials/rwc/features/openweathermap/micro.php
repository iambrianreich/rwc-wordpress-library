<?php

$temperature = $data->getMain()->getTemperature();
?>
<div class="openweathermap micro">
    <span class="temperature"><?php echo esc_html( $temperature); ?>&#8457;</span>
    <span class="icon wi wi-<?php echo esc_attr( $data->getIconClass() ); ?>"></span>
</div>
