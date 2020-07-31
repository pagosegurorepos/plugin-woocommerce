<?php

echo
'<style>
.panel-heading {
    padding: 10px 15px;
    border-bottom: 1px solid transparent;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
    color: #333;
    border-color: #041E3D;
    background-color:grey;
}
.panel-heading h3,
.panel-heading a {
    color: white;
}
</style>
<div class="panel-heading">
    <img style="max-width:75px; display: inline-block;vertical-align: middle;" src="'.$icon.'">
    <h3 style="display: contents;">'.__('Configuration', 'pagoseguro').' PAGO SEGURO</h3>  
    <a href="'.admin_url().'/admin.php?page=wc-settings&amp;tab=checkout" aria-label="Return to payments">⤴</a>
</div>
<div>
    <table class="form-table">'
    .parent::generate_settings_html(parent::get_form_fields(), false).
    '</table>
</div>';