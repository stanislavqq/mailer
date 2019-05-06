<template>
    <div class="page-distribution">
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Рассылки</h2>
            </div>
            <div class="content-actions">
                <router-link :to="{name: 'DistributionAdd'}" class="btn btn-success"><i class="fa fa-plus"></i> Создать
                </router-link>
            </div>
        </div>
        <div class="content-body">
            <el-table
                    :data="distributions">
                <el-table-column
                        prop="active"
                        label="Активность"
                        width="150">
                    <template slot-scope="scope">
                        <el-switch :value="scope.row.active == true" @change="updateActiveSwitch(scope.row.id)">
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column
                        prop="name"
                        label="Наименование"
                        width="180">

                </el-table-column>

                <el-table-column
                        prop="description"
                        label="Описание"
                        width="180">

                </el-table-column>

                <el-table-column
                        prop="subject"
                        label="Тема"
                        width="100">

                </el-table-column>

                <el-table-column
                        prop="template[0].name"
                        label="Шаблон"
                        width="150">

                </el-table-column>

                <el-table-column
                        prop="contact_list[0].name"
                        label="Список контактов"
                        width="200">

                </el-table-column>

                <el-table-column
                        label="Действия"
                        fixed="right"
                        width="180">
                    <template slot-scope="scope">
                        <el-button @click="distributionSendSolve(scope.row)"
                                   type="success" size="mini"
                                   title="Выполнить рассылку"
                                   :disabled="sendingProcess"
                                   icon="el-icon-message" circle></el-button>

                        <el-button @click="testSend(scope.row)"
                                   type="info" size="mini"
                                   title="Выполнить тестовую рассылку"
                                   :disabled="sendingProcess"
                                   icon="el-icon-message" circle></el-button>

                        <el-button @click="editItem(scope.row.id)"
                                   title="Редактировать"
                                   type="primary" size="mini" icon="el-icon-edit"
                                   :disabled="sendingProcess"
                                   circle></el-button>
                        <el-button @click="removeItem(scope.row.id)"
                                   title="Удалить"
                                   type="danger" size="mini" icon="el-icon-delete"
                                   :disabled="sendingProcess"
                                   circle></el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="sending" v-if="sendingProcess && sendingData">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <span>Отправка</span>
                        <el-button style="float: right; padding: 3px 0" type="text" @click="cancelSend">Отмена
                        </el-button>
                    </div>
                    <div class="name">{{ sendingData.name }}</div>
                    <el-progress v-model="progressbar" :percentage="progressbar"></el-progress>
                    <div class="result"></div>
                </el-card>
            </div>
        </div>
        <el-dialog
                title="Подтвердите действие"
                :visible.sync="centerDialogVisible"
                width="30%"
                center>
            <span>Выполнить рассылку?</span>
            <span slot="footer" class="dialog-footer">
    <el-button @click="centerDialogVisible = false">Отмена</el-button>
    <el-button type="primary" @click="distributionSend">Продолжить</el-button>
  </span>
        </el-dialog>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import axios from 'axios';

    const CancelToken = axios.CancelToken;

    export default {
        name: "Distribution",
        data() {
            return {
                sendingProcess: false,
                sendingData: {},
                allowedSend: false,
                centerDialogVisible: false,
                progressbar: 0,
                requestSending: null,
                sendingCancel: null,
            }
        },
        computed: {
            ...mapGetters({
                distributions: "GET_DISTRIBUTIONS"
            }),
            ...mapGetters({
                contactsLists: "GET_CONTACTS_LISTS"
            }),
            ...mapGetters({
                templates: "GET_TEMPLATES_DATA"
            }),
        },
        methods: {
            setProgress(value) {
                console.log('%', parseInt(value));
                this.progressbar = parseInt(value);
            },
            updateActiveSwitch(id) {
                console.log(id);
            },

            cancelSend() {

                this.sendingProcess = false;
                this.sendingData = {};
                this.cancel();
                // this.$message({
                //     type: 'info',
                //     message: 'Операция отменена пользователем'
                // });
            },
            distributionSendSolve(row) {
                this.sendingData = row;
                this.allowedSend = true;
                this.centerDialogVisible = true;
            },
            testSend(row) {
                this.$prompt('Пожалуйста, введите ваш e-mail, для отправки тестового письма', 'Отправить тестовое письмо', {
                    confirmButtonText: 'Отправить',
                    cancelButtonText: 'Отмена',
                    inputPattern: /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/,
                    inputErrorMessage: 'Некорректный email'
                }).then(({ value }) => {
                    axios.post('/mailer/distribution/test_send', {
                        email: value,
                        template_id: row.template_id
                    })
                        .then(response => {
                            let data = response.data;
                            if (data.status === 'ok') {
                                this.$message({
                                    type: 'success',
                                    message: 'Сообщение отправлено'
                                })
                            }
                        })
                        .catch(error => {
                            let errorData = JSON.parse(JSON.stringify(error));
                            console.log('test send error request', errorData);
                        });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: 'Действие отменено'
                    });
                });

            },
            distributionSend() {
                this.centerDialogVisible = false;

                if (!this.allowedSend) {

                    return false;
                }

                this.sendingProcess = true;
                let self = this;
                console.log(this.source);
                this.requestSending = axios.post('mailer/distribution/send', {
                    cancelToken: new CancelToken(function executor(c) {
                        // An executor function receives a cancel function as a parameter
                        self.cancel = c;
                    }),
                    id: self.sendingData.id,
                    contacts_list_id: self.sendingData.contacts_list_id,
                    template_id: self.sendingData.template_id,
                    action: 'start',
                    sending_status: 'start'
                }).then(response => {
                    console.log(response);
                }).catch(error => {
                    if (axios.isCancel(error)) {
                        console.log('Request canceled', error.message);
                    } else {
                        // handle error
                        let errorObject = JSON.parse(JSON.stringify(error));
                        let errorResponse = errorObject.response;
                        let errorData = errorResponse.data;

                        let statusCode = errorResponse.status;
                        let message = errorData.message;

                        if (statusCode === 500) {
                            self.$message({
                                'type': 'error',
                                'message': message
                            });
                        }
                    }
                }).finally(() => {
                    self.sendingProcess = false;
                });
            },
            editItem(id) {
                this.$router.push({name: 'DistributionEdit', params: {id: id}});
            },
            removeItem(id) {
                this.$store.dispatch("UNSET_DISTRIBUTION", {id: id});
            }
        }, filters: {},
        created() {
            console.log(this.distributions);
        },
        mounted() {
            let self = this;

            console.log(this.distributions);
            console.log('listen', 'mailer:MailerProcessing' + configSocketChannel + '.' + user.id);
            io.on('mailer:MailerProcessing' + configSocketChannel + '.' + user.id, data => {
                let counData = data.data.data;

                let allCount = counData.all_count;
                let current = counData.current;

                console.log(allCount);

                self.setProgress(Math.round((100 / allCount) * current));
            });
        }
    }
</script>

<style scoped>

</style>