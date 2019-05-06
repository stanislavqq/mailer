<template>
    <div>
        <div class="modal-body">
            <form id="form_send_email" enctype="multipart/form-data" onsubmit="sendEmail()" name="form_send_email"
                  type="post">
                <input type="hidden" id="email_client_id" value="">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="recipientMail">E-mail получателя<span class="required">*</span></label>
                            <input type="email" v-model="formData.email" class="form-control" name="recipientMail"
                                   id="recipientMail"
                                   placeholder="Введите e-mail получателя" required>
                        </div>
                    </div>
                </div>
                <el-tabs v-model="activeTab" @tab-click="tabClickHandle">

                    <el-tab-pane label="Произвольное письмо" name="default">

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="subjectMail">Заголовок письма<span class="required">*</span></label>
                                    <input type="text" v-model="formData.subject" class="form-control"
                                           name="subjectMail" id="subjectMail"
                                           placeholder="Введите заголовок письма" required
                                           maxlength="50"
                                           minlength="3">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <div class="well well-sm well-primary">
                                        <label class="tags">Сообщение<span class="required">*</span></label>
                                        <label class="textarea textarea-expandable" style="width: 100%">
                                    <textarea rows="3" v-model="formData.message" class="custom-scroll" id="messageMail"
                                              style="width: 100%"></textarea>
                                            <span class="col-xs-12 text-right">
										<span id="maxContentPost">{{$maxContentPost}}</span><span
                                                    class="maxContentPost">/{{$maxContentPost}}</span>
									</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group grouppy">
                                    Максимальное количество - 10<br>
                                    Максимальный размер 10 MB - (.csv, .txt, .pdf, .doc, .docx, .xls, .xlsx, .ppt,
                                    .pptx, .rar,
                                    .zip, .jpeg, .png, .tiff, .jpg)<br><br>
                                    <input type="file" name="files[]" multiple id="email_files"
                                           @change="inputFilesChangeHandle()"
                                           accept=".csv, .txt, .pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .rar, .zip, .jpeg, .png, .tiff, .jpg"/>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning" id="email_sending" style="display: none" role="alert">Отправка
                            письма...<i
                                    class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></div>
                        <div id="email_send_result" style="display: none" role="alert"></div>


                    </el-tab-pane>
                    <el-tab-pane label="Шаблон" name="withTemplate">

                        <el-autocomplete
                                v-model="selectTemplate"
                                :fetch-suggestions="querySearchAsync"
                                placeholder="Please input"
                                @select="templateSelectHandle"
                        ></el-autocomplete>

                    </el-tab-pane>
                </el-tabs>
            </form>
        </div>

        <div class="modal-footer">
            <div id="modal_buttons">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Отмена
                </button>
                <button type="submit" class="btn btn-success" id="email_send_modal_success" form="form_send_email">
                    Отправить
                </button>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ClientModalEmail",
        data() {
            return {
                formData: {
                    email: '',
                    subject: '',
                    message: '',
                    files: []
                },
                selectTemplate: ''
            }
        },
        methods: {
            inputFilesChangeHandle() {

            },
            tabClickHandle(tab) {
                console.log(tab);
            },
            templateSelectHandle(item) {
                console.log('templateSelectHandle', item);
            }
        },
        created() {

        }
    }
</script>

<style scoped>
    textarea#messageMail {
        max-width: 100%;
    }

    .badge.email-send:hover {
        background-color: #2d7482 !important;
    }

    .badge.email-send {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #64b9c9 !important;
        width: 24px;
        height: 24px;
        display: block;
        border-radius: 50%;
        font-size: 12px;
        line-height: 15px;
        text-align: center;
        -webkit-transition: all ease 300ms;
        transition: all ease 300ms;
        padding: 5px 5px;
    }

    #email_send_modal .close {
        opacity: 0.6;
    }
</style>