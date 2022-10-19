<template>
	<div class="container">
		<div class="post">
			<div class="postinfotop">
				<h2>Запись к нотариусу</h2>
				<p id="responseText"></p>
			</div>
			<form action="#" class="form newtopic" @submit.prevent="send">
				<div class="accsection">
					<div class="topwrap">
						<div class="userinfo pull-left">
						</div>
						<div class="posttext pull-left">
							<div class="col-lg-6 col-md-6 mt-2">
								<input v-model="form.first_name" type="text" placeholder="Имя" class="form-control" :class="{ 'is-invalid': form.errors.has('first_name') }" name="first_name">
								<has-error :form="form" field="first_name"></has-error>
							</div>
							<div class="col-lg-6 col-md-6 mt-2">
								<input v-model="form.last_name" type="text" placeholder="Фамилия" class="form-control" :class="{ 'is-invalid': form.errors.has('last_name') }" name="last_name">
								<has-error :form="form" field="last_name"></has-error>
							</div>
							<div class="col-lg-6 col-md-6 mt-2">
								<input v-model="form.email" type="text" placeholder="Адрес эл. почты" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" name="email">
								<has-error :form="form" field="email"></has-error>
							</div>
							<div class="col-lg-6 col-md-6 mt-2">
								<datepicker v-model="form.date" :language="ru" placeholder="Выбрать дату приёма" class="form-control"></datepicker>
								<has-error :form="form" field="date"></has-error>
							</div>
							<div class="col-lg-6 col-md-6 mt-2">
								<select v-model="form.document_type" class="form-control" name="document_type">
									<option value="">Выбрать тип документа</option>
									<option v-for="document_type in document_types" v-bind:value="document_type.id">{{ document_type.name }}</option>
								</select>
								<has-error :form="form" field="document_type"></has-error>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="mt-2">
					<button type="submit" class="btn btn-primary">Записаться</button>
				</div>
			</form>
		</div>
		<div v-if="notary_records[0]" class="post">
			<div class="postinfotop mt-4">
				<h2>Ранее сделанные записи под этим id</h2>
			</div>
			<div class="col-lg-6 col-md-6 mt-2">
				<table class="table table-bordered table-responsive">
					<tr>
						<td>Адрес эл.почты</td>
						<td>Имя</td>
						<td>Фамилия</td>
						<td>Тип документа</td>
						<td>Дата приёма</td>
					</tr>
					<tr v-for="notary_record in notary_records">
						<td>{{ notary_record.email }}</td>
						<td>{{ notary_record.first_name }}</td>
						<td>{{ notary_record.last_name }}</td>
						<td>{{ notary_record.name }}</td>
						<td>{{ notary_record.date | formatDate }}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</template>
<script>
	import {Form} from 'vform';
	import {HasError, AlertError} from 'vform/src/components/bootstrap5';
	import Datepicker from 'vuejs-datepicker';
	import {ru} from 'vuejs-datepicker/dist/locale';
	import moment from 'moment'

	window.Form = Form;
	Vue.component(HasError.name, HasError);
	Vue.component(AlertError.name, AlertError);

	Vue.filter('formatDate', function(value) {
		if (value) {
			return moment(String(value)).format('DD.MM.YYYY');
		}
	});

	export default {
		data () {
			return {
				form: new Form({
					first_name: '',
					last_name: '',
					email: '',
					date: '',
					document_type: '',
				}),
				ru: ru,
				notary_records: null
			}
		},
		methods: {
			send () {
				this.form.post('/processnotary')
					.then(( response ) => {
						var attr = document.getElementById('responseText');
						attr.innerHTML = response.data.message;
						this.form.reset();
					})
			},
		},
		components: {
			Datepicker
		},

		props: {
			id: Number,
			name: String,
			first_name: String,
			last_name: String,
			email: String,
			document_type_id: Number,
			date: Number,
			document_types: {
				type: Array,
				default: []
			},
			notary_records: {
				type: Array,
				default: []
			}
		}
	}
</script>