<?php
$tve_color = isset($_POST['colour']) && strpos($_POST['colour'], 'tve_') !== false ? $_POST['colour'] : 'tve_red';
?>
<div class="thrv_wrapper thrv_lead_generation tve_clearfix <?php echo $tve_color ?>" data-tve-style="1">
    <div class="thrv_lead_generation_code" style="display: none;"></div>
    <div class="thrv_lead_generation_container tve_clearfix">
        <div class="tve_lead_generated_inputs_container tve_clearfix">
            <div class="tve_lead_fields_overlay"></div>
            <div class=" tve_lg_input_container ">
                <input type="text" data-placeholder="" value="" name="name"/>
            </div>
            <div class="tve_lg_input_container">
                <input type="text" data-placeholder="" value="" name="email"/>
            </div>
            <div class="tve_lg_input_container tve_submit_container">
                <button type="Submit">Sign Up</button>
            </div>
        </div>
    </div>
</div>
