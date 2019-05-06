<template>
    <div class="mailer-attachment">

        <el-form-item>
            <el-upload
                    class="attach-upload"
                    action="/mailer/template/upload"
                    ref="upload"
                    :on-change="handleUploadChange"
                    :auto-upload="false"
                    :headers="uploadHeaders"
                    :multiple="true"
                    :data="uploadData"
                    :on-progress="handleProgress"
                    :on-remove="handleRemove"
                    :file-list="fileList">
                <el-button size="small" type="primary" icon="el-icon-upload2"></el-button>
                <div slot="tip" class="el-upload__tip">jpg/png files with a size less than 500kb</div>
            </el-upload>
        </el-form-item>
    </div>
</template>

<script>
    export default {
        name: "MailerAttachment",
        data() {
            return {
                uploadHeaders: {
                    "X-CSRF-Token": document.querySelector("meta[name='csrf-token']").getAttribute("content"),
                },
                uploadData: {},
            }
        },
        props: ['attachments', 'templateId', 'onSubmit', 'fileList'],
        methods: {
            handleUploadChange(file, fileList) {
                this.fileList = fileList.slice(-3);
            },
            handleProgress(event, file, filelist) {
                console.log(event, file, filelist);
            },

            handleRemove(file, filelist) {
                console.log(file);
                axios.post('/mailer/template/attach/remove', {
                    id: file.id
                })
                    .then(response => {
                        console.log(response);
                        this.$notyfy({
                            title: 'Success',
                            message: 'Файл удален',
                            type: 'success'
                        });
                    })
                    .catch(error => {

                    });
            }
        },
        mounted() {
            console.log(this.fileList);
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

    input[type="file"].el-upload__input {
        display: none;
    }
</style>