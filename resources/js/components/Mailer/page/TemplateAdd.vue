<template>
    <div>
        <div class="content-head">
            <div class="title-wrap">
                <h2 class="title">Создать новый шаблон</h2>
            </div>
            <div class="content-actions">
                <button class="btn btn-success" @click="submit()"><i class="fa fa-check"></i> Сохранить</button>
            </div>
        </div>
        <div class="content-body">
            <el-form ref="form" :model="addForm">
                <el-form-item label="Наименование">
                    <el-input v-model="addForm.name"></el-input>
                </el-form-item>

                <el-form-item label="Описание">
                    <el-input v-model="addForm.description"></el-input>
                </el-form-item>

                <template-editor :edit-form="addForm"></template-editor>
            </el-form>
        </div>
    </div>
</template>

<script>

    import {VueEditor} from "vue2-editor"
    import TemplateEditor from '../plagins/TemplateEditor';

    export default {
        name: "TemplateAdd",
        data() {
            return {
                addForm: {
                    name: '',
                    description: '',
                    template: '',
                    body: ''
                }
            }
        },
        components: {VueEditor, TemplateEditor},
        methods: {
            submit() {
                let self = this;

                axios.post('mailer/template/save', this.addForm)
                    .then(response => {
                        self.$store.dispatch("SET_TEMPLATES")
                            .then(() => {
                                self.$message({
                                    type: 'success',
                                    message: 'Успешно сохранено'
                                });
                                self.$router.push({name: 'mailerTemplates'})
                            });
                    });
            },
            handleClick(tab, event) {
                console.log(tab, event);
            }
        }
    }
</script>

<style scoped lang="scss">
    .template-editor {
        background-color: #fff;
    }

    .code-ed {
        height: 200px;
    }
</style>