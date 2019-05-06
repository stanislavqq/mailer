<template>

    <div>
        <div class="page-distribution-add">
            <div class="content-head">
                <div class="title-wrap">
                    <h2 class="title">Настройки</h2>
                </div>
                <div class="content-actions">

                </div>
            </div>
            <div class="content-body">
                <el-form :rules="rules" :model="form" ref="form">
                    <el-form-item label="Mail Driver">
                        <el-input placeholder="" value="smtp" disabled=""></el-input>
                    </el-form-item>

                    <el-form-item label="Профиль" prop="selectProfile">
                        <el-select v-model="form.driver_id" placeholder="Укажите профиль">
                            <el-option
                                    v-for="item in profiles"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id"
                                    :aria-selected="item.id == form.driver_id"
                            >
                            </el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item label="Логин" prop="login">
                        <el-input v-model="form.login" placeholder="Email"></el-input>
                    </el-form-item>

                    <el-form-item label="Пароль" prop="password">
                        <el-input v-model="form.password" show-password></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="primary" @click="submitForm('form')">Сохранить</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MailerSettings",
        data() {
            return {
                profiles: this.$store.state.smtpProfiles,
                form: {
                    login: this.$store.state.smtpUserProfile.login ? this.$store.state.smtpUserProfile.login : '',
                    password: this.$store.state.smtpUserProfile.password ? this.$store.state.smtpUserProfile.password : '',
                    driver_id: this.$store.state.smtpUserProfile.driver_id ? this.$store.state.smtpUserProfile.driver_id : '',
                },

                rules: {
                    driver_id: [
                        {required: true, message: 'Пожалуйста, выберите профиль', trigger: 'blur'},
                    ],
                    login: [
                        {required: true, message: 'Пожалуйста, укажите логин', trigger: 'blur'},
                    ],
                    password: [
                        {required: true, message: 'Пожалуйста, укажите пароль', trigger: 'blur'},
                    ]
                }
            }
        },
        methods: {
            submitForm(formName) {
                let self = this;
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.$store.dispatch("SAVE_MAILER_DRIVER_USER", self.form)
                            .then(response => {
                                self.$store.dispatch("SET_MAILER_DRIVERS");

                                self.$notify({
                                    type: 'success',
                                    message: 'Настройки сохранены'
                                });

                                self.$router.push({name: 'mailerTemplates'});
                            });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
        }
    }
</script>

<style scoped>

</style>