
<script type="text/javascript">
    let Form = window.opener.document.getElementById('order_form');
    Form.querySelector('input[name="receive_addr"]').value      = '{{$var_jiyok}}';
    Form.querySelector('input[name="receive_shop"]').value      = '{{$var_corp}}';
    Form.querySelector('input[name="receive_name"]').value      = '{{$var_name}}';
    Form.querySelector('input[name="receive_tel"]').value       = '{{$var_tel}}';
    Form.querySelector('input[name="receive_shop_id"]').value   = '{{$var_sid}}';
    window.close();
</script>