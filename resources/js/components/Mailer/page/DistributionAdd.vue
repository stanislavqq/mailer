<template>
    <div class="page-distribution-add">
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Создать рассылку</h2>
            </div>
            <div class="content-actions">
                <router-link :to="{name:'Distribution'}" class="btn btn-danger"><i class="fa fa-close"></i> Отмена
                </router-link>
                <el-button type="success" :loading="btnSaveLoading" @click="save()"><i class="fa fa-check"></i>
                    Сохранить
                </el-button>
            </div>
        </div>
        <div class="content-body">
            <el-form>
                <el-form-item label="Активность">
                    <el-switch
                            v-model="formAdd.active"
                            active-color="#13ce66"
                            inactive-color="#ff4949"
                    >
                    </el-switch>
                </el-form-item>

                <el-form-item label="Название">
                    <el-input v-model="formAdd.name" placeholder="Название"></el-input>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input v-model="formAdd.description" placeholder="Описание"></el-input>
                </el-form-item>

                <el-form-item label="Тема письма">
                    <el-input v-model="formAdd.subject" placeholder="Тема письма"></el-input>
                </el-form-item>

                <el-form-item label="Email отправителя">
                    <el-input v-model="formAdd.email_from" placeholder="Email отправителя"></el-input>
                </el-form-item>
                <el-form-item label="Имя отправителя">
                    <el-input v-model="formAdd.name_from" placeholder="Email отправителя"></el-input>
                </el-form-item>
                <el-form-item label="Список контактов">
                    <el-select v-model="formAdd.contacts_list_id" filterable placeholder="Список контактов">
                        <!--<el-autocomplete-->
                        <!--v-model="formAdd.template"-->
                        <!--:fetch-suggestions="queryTemplateSearchAsync"-->
                        <!--placeholder="Шаблон"-->
                        <!--@select="handleSelect"-->
                        <!--&gt;</el-autocomplete>-->
                        <el-option
                                v-for="item in contactsOptions"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="Шаблон для рассылки">
                    <el-select v-model="formAdd.template_id" filterable placeholder="Шаблон для рассылки">
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
                            v-model="sendByDate"
                            active-color="#13ce66"
                            inactive-color="#ff4949">
                    </el-switch>
                </el-form-item>

                <el-form-item label="Дата и время отправки" v-if="sendByDate">
                    <el-date-picker
                            v-model="formAdd.send_date"
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
        name: "DistributionAdd",
        data() {
            return {
                sendByDate: false,
                templateLinks: [],
                timeout: null,
                btnSaveLoading: false,
                formAdd: {
                    active: 0,
                    name: '',
                    description: '',
                    subject: '',
                    email_from: '',
                    name_from: '',
                    contacts_list_id: null,
                    template_id: null,
                    send_date: null
                }
            }
        },
        computed: {
            ...mapGetters({
                contactsOptions: "GET_CONTACTS_LISTS"
            }),
            ...mapGetters({
                templatesList: "GET_TEMPLATES_DATA"
            })
        },
        methods: {
            save() {
                let self = this;
                self.btnSaveLoading = true;

                axios.post('mailer/distribution/save', this.formAdd)
                    .then(response => {
                        console.log('then', response);
                        self.$store.dispatch("SET_DISTRIBUTIONS");
                        this.$router.push({name: 'Distribution'});
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


                    })
                    .finally(() => {
                        self.btnSaveLoading = false;
                    });
            },

            createFilter(queryString) {
                return (link) => {
                    return (link.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },

            handleSelect(item) {
                console.log(item);
            }
        },
        created() {
        },
        mounted() {
            //this.templateLinks = this.loadAll();
        }
    }
</script>

<style scoped>

</style>