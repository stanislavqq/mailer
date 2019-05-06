<template>
    <div>
        <div class="variables">
            <h3 class="title">
                Доступные переменные
            </h3>
            <div class="t-list">
                <span class="t-variable" v-for="tVariable in templateVariables" :class="'variable-' + tVariable.id"
                      @click="variableClickHandle">#{{ tVariable.name | uppercase }}#</span>
            </div>
        </div>
        <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="Редактор" name="main-editor">
                <el-form-item>
                    <div class="template-editor">
                        <vue-editor v-model.lazzy="editForm.body"
                                    :editorToolbar="customToolbar"></vue-editor>
                    </div>
                </el-form-item>
            </el-tab-pane>
            <el-tab-pane label="Code" name="code-editor">
                <el-form-item>
                    <el-input type="textarea" class="code-ed" v-model.lazzy="editForm.body"></el-input>
                </el-form-item>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    import {Quill, VueEditor} from "vue2-editor"
    import {mapGetters} from "vuex";

    export default {
        name: "template-editor",
        components: {VueEditor},
        computed: {
            ...mapGetters({
                templateVariables: "GET_TEMPLATE_VARIABLES"
            }),
        },
        props: {
            editForm: {
                type: Object,
                required: true
            },
        },
        filters: {
            uppercase(value) {
                if (!value) return;
                return value.toUpperCase();
            }
        },
        data() {
            return {
                customToolbar: [
                    [{'font': []}],
                    [{'header': [false, 1, 2, 3, 4, 5, 6,]}],
                    ['bold', 'italic', 'underline'],
                    [{'align': ''}, {'align': 'center'}, {'align': 'right'}, {'align': 'justify'}],
                    [{'list': 'ordered'}, {'list': 'bullet'}],
                    [{'color': []}, {'background': []}],
                    [{"code-block": []}],
                    ['link', 'image'],
                    ['clean'],
                ],
                activeName: 'main-editor',
            }
        },
        methods: {
            handleClick(tab, event) {
                console.log(tab, event);
            },
            variableClickHandle(e) {
                let item = e.target;

                let doc = document
                    , text = doc.getElementsByClassName(item.className)[0]
                    , range, selection
                ;

                if (doc.body.createTextRange) {
                    range = document.body.createTextRange();
                    range.moveToElementText(text);
                    range.select();
                } else if (window.getSelection) {
                    selection = window.getSelection();
                    range = document.createRange();
                    range.selectNodeContents(text);
                    selection.removeAllRanges();
                    selection.addRange(range);
                }

                try {
                    // Теперь, когда мы выбрали текст ссылки, выполним команду копирования
                    let successful = document.execCommand('copy');
                    let msg = successful ? 'successful' : 'unsuccessful';
                    console.log('Copy email command was ' + msg);
                } catch (err) {
                    console.log('Oops, unable to copy');
                }
            },
            submitUpload() {

            }

        },
        created() {
            // console.log(this.$props.id);
            // this.uploadHeaders["x-template-id"] = this.formEdit.id;
        }
    }
</script>

<style scoped type="scss">
    .t-variable {
        padding: 5px 10px;
        color: #409eff;
        cursor: pointer;
        transition: all ease 300ms;
        &:hover {
            color: #333;
        }
    }

    .t-list {
        border: 1px solid #bbb;
        border-radius: 3px;
        display: inline-block;
        padding: 10px;
    }

    .el-upload .el-upload__input {
        display: none;
    }
</style>