<style>
.container {
    margin-left: auto;
    margin-right: auto;
    padding-left: 15px;
    padding-right: 15px;
}

.panel-heading {
    min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
}

dl {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-block-start: 1em;
    margin-block-end: 1em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
}

dl dd {
    flex: 0 0 55%;
    background: #f1f1f1;
    padding: .625rem;
    margin: .125rem;
}

dl dt {
    flex: 0 0 35%;
    background: #f1f1f1;
    padding: .625rem;
    margin: .125rem;
}
</style>
<section id="pagoseguro_hook_payment_detail" class="container">
    <div class="panel-heading">
        <dl>
            <dt>Total</dt>
            <dd>
                <?php echo $amount.' '.__('TaxInclude', 'pagoseguro'); ?>
            </dd>
        </dl>
        <?php esc_html_e('PaymentDetailsMessage', 'pagoseguro'); ?>
    </div>
</section>