{% extends 'partials/body.twig.php' %}
{% block title %}Energy People | Backend Test | Dashboard{% endblock %}
{% block body %}

<script type="text/javascript">
    $(document).ready(function () {
        $('#validate').validate({
            rules: {
                beneficio_nome: {
                    required: true,
                    maxlength: 40
                },
                beneficio_codigo: {
                    required: true,
                    maxlength: 20
                },
                beneficio_operadora: {
                    required: true,
                    maxlength: 45
                },
                beneficio_tipo: {
                    required: true,
                    maxlength: 45
                }
            }
        });

        // mascara dinheiro
        $('.m-dinheiro').maskMoney({
            prefix: 'R$ ',
            allowNegative: true,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        // mascara de data
        $('.m-data').mask('99/99/9999');
    });
</script>
<!-- Main Content -->
<main class="content">
    <h1 class="title new-item">{{ (beneficio.beneficio_id is defined) ? 'Editar' : 'Novo' }} beneficio</h1>
	{% set routerInsert = URL_BASE ~ '/beneficios/insert' %}
	{% set routerUpdate = URL_BASE ~ '/beneficios/update/' ~ beneficio.beneficio_id %}
	<form method="post" id="validate" action="{{ (beneficio.beneficio_id is defined) ? routerUpdate : routerInsert }}">
		<div class="input-field">
			<label for="beneficio-name" class="label">Beneficio nome</label>
			<input type="text" name="beneficio_nome" id="beneficio_nome" class="input-text" maxlength="40" value="{{beneficio.beneficio_nome}}" required />
		</div>
		<div class="input-field">
			<label for="beneficio-code" class="label">Beneficio código</label>
			<input type="text" name="beneficio_codigo" id="beneficio_codigo" class="input-text" maxlength="20" value="{{beneficio.beneficio_codigo}}" required />
		</div>
		<div class="input-field">
			<label for="beneficio-code" class="label">Operadora</label>
			<input type="text" name="beneficio_operadora" id="beneficio_operadora" class="input-text" maxlength="20" value="{{beneficio.beneficio_operadora}}" required />
		</div>
		<div class="input-field">
			<label for="beneficio-code" class="label">Tipo de beneficio</label>
			<input type="text" name="beneficio_tipo" id="beneficio_tipo" class="input-text" maxlength="20" value="{{beneficio.beneficio_tipo}}" required />
		</div>
		<div class="input-field">
			<label for="beneficio-code" class="label">Valor de beneficio</label>
			<input type="text" name="beneficio_valor" id="beneficio_valor" class="input-text m-dinheiro" maxlength="20" value="{{beneficio.beneficio_valor|number_format(2, ',', '.')}}" required />
		</div>
		<div class="input-field">
			<label for="beneficio-code" class="label">Data de vencimento do contrato</label>
			<input type="text" name="beneficio_dt_vencimento" id="beneficio_dt_vencimento" class="input-text m-data" maxlength="20" value="{{beneficio.beneficio_dt_vencimento|date('d/m/Y')}}" required />
		</div>
		<div class="actions-form">
			<a href="{{URL_BASE}}" class="action back">Voltar</a>
			<input class="btn-submit btn-action" type="submit" value="Salvar" />
		</div>
	</form>
</main>
<!-- Main Content -->

{% endblock %}
