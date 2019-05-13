<template>
    <div>
        <div class="page-distribution-add">
            <div class="content-head">
                <div class="title-wrap">
                    <h2 class="title">Выполнить рассылку</h2>
                </div>
                <div class="content-actions">
                    <router-link :to="{name:'Distribution'}" class="btn btn-info"><i class="fa fa-arrow-left"></i> Назад
                    </router-link>
                    <button class="btn btn-success" @click="save()"><i class="fa fa-check"></i> Сохранить</button>
                </div>
            </div>
            <div class="content-body">
                <el-row>
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <span>Отправить тестовое письмо</span>
                        </div>
                        <el-form>
                            <el-form-item label="Шаблон">
                                <el-select v-model="formTest.template_id">
                                    <el-option
                                            v-for="option in templates"
                                            :key="option.id"
                                            :label="option.name"
                                            :value="option.id"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="Ваш Email">
                                <el-input v-model="formTest.email" :value="user.user_email" placeholder="Email"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button @click="testSend()">Отправить</el-button>
                            </el-form-item>
                        </el-form>
                    </el-card>
                </el-row>
                <el-row>
                    <el-card class="box-card">
                        <div slot="header" class="clearfix">
                            <span>Начать</span>
                        </div>
                        <el-progress v-model="progressbar" :percentage="progressbar"></el-progress>

                        <el-button @click="test" type="primary">Начать рассылку</el-button>
                    </el-card>
                </el-row>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';

    export default {
        name: "DistributionSend",
        data() {
            return {
                formTest: {
                    email: '',
                    template_id: null,
                },
                progressbar: 0,
                progressStatus: ''
            }
        },
        computed: {
            ...mapGetters({
                conatctsLists: "GET_CONTACTS_LISTS"
            }),
            ...mapGetters({
                templates: "GET_TEMPLATES_DATA"
            }),
            ...mapGetters({
                user: "GET_CURRENT_USER"
            }),
        },
        methods: {
            testSend() {
                axios.post('/mailer/distribution/test_send', {
                    email: this.formTest.email,
                    template_id: this.formTest.template_id
                })
                    .then(response => {

                    });
            },
        },
        mounted() {
            console.log(this.user);
        }
    }
</script>

<style scoped>

</style>
