<template>
    <div>
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Шаблон #{{ $route.params.id }}</h2>
            </div>
            <div class="content-actions">
                <button class="btn btn-success" @click="submit()"><i class="fa fa-check"></i> Сохранить</button>
            </div>
        </div>
        <div class="content-body">
            <el-form ref="form" :model="editForm" v-loading="loading">
                <el-form-item label="Наименование">
                    <el-input v-model="editForm.name"></el-input>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input v-model="editForm.description"></el-input>
                </el-form-item>
                <template-editor :edit-form="editForm"></template-editor>
                <mailer-attachment :fileList="editForm.fileList" :attachments="editForm.mailer_attachments"></mailer-attachment>
            </el-form>
        </div>
    </div>
</template>

<script>
    import {VueEditor} from "vue2-editor"
    import TemplateEditor from "../plagins/TemplateEditor";
    import MailerAttachment from "../plagins/MailerAttachment"

    export default {
        name: "TemplateEdit",
        data() {
            return {
                activeName: 'main-editor',
                loading: false,
                editForm: {
                    id: 0,
                    name: '',
                    description: '',
                    body: '',
                    mailer_attachment: []
                }
            }
        },
        components: {VueEditor, TemplateEditor, MailerAttachment},
        methods: {
            submit() {
                let self = this;

                axios.post('mailer/template/update', this.editForm)
                    .then(response => {
                        this.$store.dispatch("SET_TEMPLATES")
                            .then(() => {
                                self.$message({
                                    message: 'Успешно сохранено',
                                    type: 'success'
                                });
                                self.$router.push({name: 'mailerTemplates'});
                            })
                    });
            },
            handleClick(tab, event) {
                console.log(tab, event);
            },
        },
        created() {
            let self = this;
            self.loading = true;

            axios.post('mailer/template/get_item', {id: self.$route.params.id})
                .then(response => self.editForm = response.data)
                .finally(() => self.loading = false);
        }
    }
</script>

<style scoped type="SCSS">
    h3.title {
        font-size: 20px;
        margin-bottom: 15px;
    }
</style>