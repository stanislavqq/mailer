<template>
    <div class="page-distribution-add">
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Редактирование</h2>
            </div>
            <div class="content-actions">
                <router-link :to="{name:'Distribution'}" class="btn btn-danger"><i class="fa fa-close"></i> Отмена
                </router-link>
                <button class="btn btn-success" @click="update()"><i class="fa fa-check"></i> Сохранить</button>
            </div>
        </div>
        <div class="content-body">
            <el-form>
                <el-form-item>
                    <el-input type="hidden" name="id" value="formEdit.id"></el-input>
                </el-form-item>
                <el-form-item label="Активность">
                    <el-switch
                            v-model="formEdit.active"
                            active-color="#13ce66"
                            inactive-color="#ff4949"
                    >
                    </el-switch>
                </el-form-item>

                <el-form-item label="Название">
                    <el-input v-model="formEdit.name" placeholder="Название"></el-input>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input v-model="formEdit.description" placeholder="Описание"></el-input>
                </el-form-item>

                <el-form-item label="Тема письма">
                    <el-input v-model="formEdit.subject" placeholder="Тема письма"></el-input>
                </el-form-item>

                <el-form-item label="Email отправителя">
                    <el-input v-model="formEdit.email_from" placeholder="Email отправителя"></el-input>
                </el-form-item>
                <el-form-item label="Имя отправителя">
                    <el-input v-model="formEdit.name_from" placeholder="Email отправителя"></el-input>
                </el-form-item>
                <el-form-item label="Список контактов">
                    <el-select v-model="formEdit.contacts_list_id" filterable placeholder="Список контактов">
                        <!--<el-autocomplete-->
                        <!--v-model="formAdd.template"-->
                        <!--:fetch-suggestions="queryTemplateSearchAsync"-->
                        <!--placeholder="Шаблон"-->
                        <!--@select="handleSelect"-->
                        <!--&gt;</el-autocomplete>-->
                        <el-option
                                :checked="formEdit.contacts_list_id == item.id"
                                v-for="item in contactsOptions"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Шаблон для рассылки">
                    <el-select v-model="formEdit.template_id" filterable placeholder="Шаблон для рассылки">
                        <!--<el-autocomplete-->
                        <!--v-model="formAdd.template"-->
                        <!--:fetch-suggestions="queryTemplateSearchAsync"-->
                        <!--placeholder="Шаблон"-->
                        <!--@select="handleSelect"-->
                        <!--&gt;</el-autocomplete>-->
                        <el-option
                                v-for="item in templatesList"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                    <!--<el-autocomplete-->
                    <!--v-model="templatesList"-->
                    <!--:fetch-suggestions="queryTemplateSearchAsync"-->
                    <!--placeholder="Шаблон"-->
                    <!--@select="handleSelect"-->
                    <!--&gt;</el-autocomplete>-->
                </el-form-item>

                <el-form-item label="Отправить в определенное время">
                    <el-switch
                            v-model="formEdit.send_by_date"
                            active-color="#13ce66"
                            inactive-color="#ff4949">
                    </el-switch>
                </el-form-item>

                <el-form-item label="Дата и время отправки" v-if="formEdit.send_by_date">
                    <el-date-picker
                            v-model="formEdit.send_date"
                            :pickerOptions="{firstDayOfWeek: 1}"
                            type="datetime"
                            placeholder="Укажите дату и время">
                    </el-date-picker>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';

    export default {
        name: "DistributionEdit",
        data() {
            return {
                templateLinks: [],
                timeout: null,
                formEdit: {
                    id: 0,
                    active: 0,
                    name: '',
                    description: '',
                    subject: '',
                    email_from: '',
                    name_from: '',
                    contacts_list_id: null,
                    template_id: null,
                    send_date: null,
                    send_by_date: false
                }
            }
        },
        computed: {
            ...mapGetters({
                contactsOptions: "GET_CONTACTS_LISTS"
            }),
            ...mapGetters({
                templatesList: "GET_TEMPLATES_DATA"
            }),
        },
        methods: {
            createFilter(queryString) {
                return (link) => {
                    return (link.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },
            update() {
                let data = {};
                let self = this;

                self.$store.dispatch('UPDATE_DISTRIBUTION', this.formEdit)
                    .then(response => {
                        let res = this.$store.dispatch('SET_DISTRIBUTIONS', self.$route.params.id);

                        if (res) {
                            self.$message({
                                type: 'success',
                                message: 'Успешно обновлено.'
                            });
                            self.$router.push({name: 'Distribution'});
                        }
                    })
                    .catch(error => {
                        let errorData = JSON.parse(JSON.stringify(error));

                        for (let key in errorData.response.data.errors) {
                            let rowErrors = errorData.response.data.errors[key];

                            for (let i = 0; i < rowErrors.length; i++) {
                                let row = rowErrors[i];

                                this.$notify.error({
                                    title: 'Ошибка',
                                    message: row
                                });

                            }
                        }
                    });
            }
        },
        created() {
            let distributionId = this.$route.params.id;
            let self = this;

            let distributions = this.$store.getters.GET_DISTRIBUTIONS;
            let distribution = distributions.filter(item => {
                return item.id == distributionId;
            }).shift();

            if (distribution) {
                self.formEdit = {
                    id: distribution.id,
                    active: !!distribution.active,
                    name: distribution.name,
                    description: distribution.description,
                    subject: distribution.subject,
                    email_from: distribution.from_email,
                    name_from: distribution.from_name,
                    contacts_list: distribution.contact_list[0],
                    template: distribution.template[0],
                    contacts_list_id: distribution.contact_list.length ? distribution.contacts_list_id : '',
                    template_id: distribution.template.length ? distribution.template_id : '',
                    send_date: distribution.send_date,
                    send_by_date: !!distribution.send_by_date
                }
            }
        }
    }
</script>

<style scoped>

</style>