<div class="col-lg-12" style="display:none;">
    <form id="pagoseguropayment" method="post" action="<?php echo $url; ?>">
        <input type="hidden" name="key" value="<?php echo $accountId; ?>" />
        <input type="hidden" name="txnid" value="<?php echo $orderReference; ?>" />
        <input type="hidden" name="amount" value="<?php echo $total; ?>" />
        <input type="hidden" name="productinfo" value="<?php echo $product; ?>" />
        <input type="hidden" name="firstname" value="<?php echo $customerFullName; ?>" />
        <input type="hidden" name="email" value="<?php echo $customerEmail; ?>" />
        <input type="hidden" name="hash" value="<?php echo $signature; ?>" />
        <input type="hidden" name="url_response" value='<?php echo $urlResponse; ?>'/>
        <input type="hidden" name="udf1" value="/payment/process" />
        <input type="submit" name="Submit" type="hidden" value="Enviar">
    </form>

    <p style="text-align: center; font-size: 20px; color: #041E3D; font-family: Arial; font-weight: 600; margin: 20px;">
        <?php echo __('PagoSeguro'); ?>
    </p>
</div>
<script>
document.getElementById("pagoseguropayment").submit();
</script>