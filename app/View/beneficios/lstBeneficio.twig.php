{% extends 'partials/body.twig.php' %}
{% block title %}Energy People | Backend Test | Dashboard{% endblock %}
{% block body %}

<!-- Main Content -->
<main class="content">
    <div class="header-list-page">
		<h1 class="title">Beneficios</h1>
		<a href="{{URL_BASE}}/beneficios/novo" class="btn-action">Adicionar beneficio</a>
    </div>
    <table class="data-grid">
		<thead>
			<tr class="data-row">
				<th class="data-grid-th">
					<span class="data-grid-cell-content">Nome</span>
				</th>
				<th class="data-grid-th">
					<span class="data-grid-cell-content">Código</span>
				</th>
				<th class="data-grid-th">
					<span class="data-grid-cell-content">Ações</span>
				</th>
			</tr>
		</thead>
		<tbody>
			{% if beneficios %}
			{% for beneficio in beneficios %}
			<tr class="data-row">
				<td class="data-grid-td">
					<span class="data-grid-cell-content">{{beneficio.beneficio_nome}}</span>
				</td>
				<td class="data-grid-td">
					<span class="data-grid-cell-content">{{beneficio.beneficio_codigo}}</span>
				</td>
				<td class="data-grid-td">
					<div class="actions">
						<div class="action edit">
							<a href="{{URL_BASE}}/beneficios/editar/{{beneficio.beneficio_id}}"><span>Editar</span></a>
						</div>
						<div class="action delete">
							<a href="{{URL_BASE}}/beneficios/delete/{{beneficio.beneficio_id}}"><span>Deletar</span></a>
						</div>
					</div>
				</td>
			</tr>
			{% endfor %}
			{% else %}
			<tr class="data-row">
				<td class="data-grid-td" colspan="3">
					Nenhum registro encontrado!
				</td>
			</tr>
			{% endif %}
		</tbody>
    </table>
</main>
<!-- Main Content -->

{% endblock %}
